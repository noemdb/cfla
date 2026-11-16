<?php

namespace App\Livewire\Planning\DidacticPlans;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\DidacticPlan;
use App\Models\app\Academy\Lapso;
use App\Services\Planning\DidacticPlanBuilder;
use App\Services\Planning\DidacticPlanPeriodResolver;
use App\Services\Planning\DidacticPlanScorer;
use App\Services\Planning\DidacticPlanService;
use App\Services\Planning\FilteredActivitiesQuery;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class Wizard extends Component
{
    private const FILTER_KEYS = [
        'search', 'pestudio_id', 'grado_id', 'seccion_id', 'lapso_id',
        'profesor_id', 'status_activities', 'filter_status', 'filter_observations',
        'date_from', 'date_to',
    ];

    public bool $open = false;

    public int $step = 1;

    /** @var array<string, mixed> */
    public array $filters = [];

    /** @var array<int, string> */
    public array $filterChips = [];

    /**
     * @var array<int, array{
     *     id:int,label:string,short_label:string,grado_id:int,grado_name:string,
     *     grado_order:int,pestudio_id:int,pestudio_name:string,lapso_id:?int,activity_count:int
     * }>
     */
    public array $sectionOptions = [];

    /** @var array<int, int> */
    public array $seccionIds = [];

    public ?int $activitySectionId = null;

    public ?int $lapsoId = null;

    public ?string $rangeStart = null;

    public ?string $rangeEnd = null;

    public string $rangeOrigin = '';

    public string $derivedRangeStart = '';

    public string $derivedRangeEnd = '';

    /** @var array<int, array{value:string,label:string,start:string,end:string,days:int}> */
    public array $weekOptions = [];

    public ?string $selectedWeek = '';

    public ?string $periodStart = null;

    public ?string $periodEnd = null;

    public string $axis = 'Alimentación, cultura e identidad';

    public string $projectTitle = '';

    public string $objective = '';

    /** @var array<int, array<string, mixed>> */
    public array $activities = [];

    /** @var array<int, int> */
    public array $selectedIds = [];

    /** @var array<int, array<string, mixed>> */
    public array $sessions = [];

    /** @var array<int, string> */
    public array $warnings = [];

    public string $stepError = '';

    /**
     * Resultado por sección: plan_id, version, status (draft/approved/error).
     *
     * @var array<int, array{seccion_id:int,label:string,plan_id:?int,version:?int,status:string,error:?string,errors:array<int,string>}>
     */
    public array $planResults = [];

    public bool $approved = false;

    public int $filteredCount = 0;

    public int $selectedCount = 0;

    public string $universeHash = '';

    public string $filterHash = '';

    public string $notes = '';

    public string $pdfNotes = '';

    /** @var \Illuminate\Support\Collection<int, \App\Models\app\Academy\Pevaluacion>|null */
    private ?Collection $filteredPevaluacionsCache = null;

    public function mount(): void
    {
        $this->axis = 'Alimentación, cultura e identidad';
    }

    /**
     * Abre el wizard con la instantánea de filtros de la página.
     *
     * @param  array<string, mixed>  $filters
     */
    #[On('open-didactic-plan-wizard')]
    public function open(array $filters = []): void
    {
        abort_unless((bool) config('planning.didactic_plans_enabled'), 404);

        Gate::authorize('create', DidacticPlan::class);

        $this->resetState();
        $this->filteredPevaluacionsCache = null;
        $this->filters = $this->sanitizeFilters($filters);
        $this->filterChips = $this->buildFilterChips($this->filters);

        $this->buildSectionOptions();

        $this->lapsoId = ! empty($this->filters['lapso_id'])
            ? (int) $this->filters['lapso_id']
            : Lapso::current()?->id;

        $this->seccionIds = $this->defaultSectionIds();
        $this->activitySectionId = $this->seccionIds[0] ?? null;

        $this->open = true;
        $this->step = 1;

        if ($this->sectionOptions === []) {
            $this->stepError = 'No hay actividades con los filtros activos. Ajusta los filtros de la página e inténtalo de nuevo.';

            return;
        }

        $this->recalculateContext();
    }

    /**
     * El usuario marca/desmarca secciones en el paso 1.
     */
    public function updatedSeccionIds(): void
    {
        $this->applySectionSelection();
    }

    public function selectAllSections(): void
    {
        $this->seccionIds = collect($this->sectionOptions)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        $this->applySectionSelection();
    }

    public function clearSections(): void
    {
        $this->seccionIds = [];
        $this->applySectionSelection();
    }

    public function focusSection(int $seccionId): void
    {
        $this->activitySectionId = $seccionId;
    }

    public function updatedSelectedWeek(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        foreach ($this->weekOptions as $week) {
            if ($week['value'] === $value) {
                $this->periodStart = $week['start'];
                $this->periodEnd = $week['end'];
                $this->stepError = '';

                break;
            }
        }

        $this->rebuildActivityRows();
    }

    public function updatedPeriodStart(): void
    {
        $this->applyCustomPeriod();
    }

    public function updatedPeriodEnd(): void
    {
        $this->applyCustomPeriod();
    }

    public function updatedRangeStart(): void
    {
        $this->applyRangeFromInputs();
    }

    public function updatedRangeEnd(): void
    {
        $this->applyRangeFromInputs();
    }

    /**
     * Restaura el periodo derivado de los filtros y la primera semana.
     */
    public function resetDerivedPeriod(): void
    {
        if ($this->derivedRangeStart === '' || $this->derivedRangeEnd === '') {
            return;
        }

        $this->rangeStart = $this->derivedRangeStart;
        $this->rangeEnd = $this->derivedRangeEnd;
        $this->periodStart = '';
        $this->periodEnd = '';
        $this->selectedWeek = '';
        $this->stepError = '';

        $this->applyRangeFromInputs(false);
    }

    public function rangeCustomized(): bool
    {
        return $this->derivedRangeStart !== ''
            && ($this->rangeStart !== $this->derivedRangeStart
                || $this->rangeEnd !== $this->derivedRangeEnd);
    }

    public function goToActivities(): void
    {
        $this->stepError = '';

        if ($this->seccionIds === []) {
            $this->stepError = 'Selecciona al menos una sección para el plan.';

            return;
        }

        if ($this->periodStart === null || $this->periodStart === ''
            || $this->periodEnd === null || $this->periodEnd === '') {
            $this->stepError = 'No fue posible derivar una semana con los filtros activos. Ajusta los filtros.';

            return;
        }

        $errors = $this->selectedPeriodErrors();

        if ($errors !== []) {
            $this->stepError = implode(' ', $errors);

            return;
        }

        if ($this->activitySectionId === null) {
            $this->activitySectionId = $this->seccionIds[0];
        }

        $this->step = 2;
    }

    public function toggleActivity(int $id): void
    {
        $visibleIds = array_column($this->visibleActivities(), 'id');

        if (! in_array($id, $visibleIds, true)) {
            return;
        }

        if (in_array($id, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));

            return;
        }

        $this->selectedIds[] = $id;
    }

    /**
     * Marca/desmarca todas las actividades de la sección activa.
     */
    public function toggleSection(int $seccionId): void
    {
        $ids = array_values(array_map(
            fn (array $row): int => (int) $row['id'],
            array_filter(
                $this->activities,
                fn (array $row): bool => (int) $row['seccion_id'] === $seccionId
                    && (bool) $row['within_period']
            )
        ));

        if ($ids === []) {
            return;
        }

        $selectedInSection = array_intersect($ids, $this->selectedIds);

        if (count($selectedInSection) === count($ids)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, $ids));

            return;
        }

        $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $ids)));
    }

    public function selectComplete(): void
    {
        $ids = array_values(array_map(
            fn (array $row): int => (int) $row['id'],
            array_filter($this->visibleActivities(), fn (array $row): bool => (bool) $row['complete'] && (bool) $row['within_period'])
        ));

        $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $ids)));
    }

    public function selectAffinity(): void
    {
        $ids = array_column($this->visibleActivities(), 'id');

        if ($ids === []) {
            return;
        }

        $models = Activity::query()
            ->with('achievements')
            ->whereIn('id', $ids)
            ->get();

        $selected = app(DidacticPlanScorer::class)
            ->preselectForAxis($models, $this->periodStart, $this->periodEnd);

        $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $selected)));
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->sessions = [];
        $this->selectedCount = 0;
    }

    public function manualPlan(): void
    {
        $seccionId = $this->activitySectionId ?? ($this->seccionIds[0] ?? null);

        if ($seccionId === null) {
            $this->stepError = 'Selecciona al menos una sección para el plan manual.';

            return;
        }

        $this->sessions = [
            $this->emptySession((int) $seccionId, $this->periodStart),
        ];
        $this->selectedIds = [];
        $this->selectedCount = 0;
        $this->warnings = ['Plan manual: completa los momentos y la evaluación en el paso 3.'];
        $this->activitySectionId = (int) $seccionId;
        $this->step = 3;
    }

    public function buildSessions(): void
    {
        $this->stepError = '';
        $eligibleIds = array_column($this->periodActivities(), 'id');
        $this->selectedIds = array_values(array_intersect(
            array_map('intval', $this->selectedIds),
            $eligibleIds
        ));

        if ($this->selectedIds === []) {
            $this->stepError = 'Selecciona una actividad dentro del periodo, o crea un plan manual.';

            return;
        }

        $sectionByActivity = [];

        foreach ($this->activities as $row) {
            $sectionByActivity[(int) $row['id']] = (int) $row['seccion_id'];
        }

        $models = Activity::query()
            ->with(['achievements', 'pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado'])
            ->whereIn('id', $this->selectedIds)
            ->get()
            ->keyBy('id');

        $builder = app(DidacticPlanBuilder::class);
        $sessions = [];
        $warnings = [];

        foreach (array_column($this->activities, 'id') as $id) {
            if (! in_array((int) $id, $this->selectedIds, true) || ! isset($models[$id])) {
                continue;
            }

            /** @var Activity $model */
            $model = $models[$id];
            $built = $builder->buildSession($model, $this->periodStart, $this->periodEnd);

            foreach ((array) ($built['warnings'] ?? []) as $warning) {
                $warnings[] = "Actividad {$id}: {$warning}";
            }

            $ods = array_values((array) ($built['ods'] ?? []));

            $sessions[] = [
                'uid' => uniqid('s', true),
                'seccion_id' => $sectionByActivity[(int) $id] ?? (int) $model->pevaluacion?->seccion_id,
                'date' => $built['date'] ?? $this->periodStart,
                'day_index' => $built['day_index'] ?? 1,
                'title' => $built['title'] ?: 'Sesión sin título',
                'thematic' => $built['thematic'] ?? '',
                'objective' => $built['objective'] ?? '',
                'start' => $built['start'] ?? '',
                'development' => $built['development'] ?? '',
                'close' => $built['close'] ?? '',
                'resources' => $built['resources'] ?? '',
                'evaluation_instrument' => $built['evaluation_instrument'] ?? '',
                'evaluation_description' => $built['evaluation_description'] ?? '',
                'observations' => $built['observations'] ?? '',
                'ods' => $ods,
                'ods_text' => implode(', ', $ods),
                'is_non_teaching' => false,
                'source_activity_id' => (int) $model->id,
                'source_pevaluacion_id' => (int) $model->pevaluacion_id,
                'source_field' => $built['source_field'] ?? 'teaching',
                'source_excerpt' => $built['source_excerpt'] ?? '',
                'source_checksum' => $built['source_checksum'] ?? null,
                'confidence' => $built['confidence'] ?? null,
                'indicators' => array_map(fn (array $indicator): array => [
                    'name' => $indicator['name'],
                    'weighting' => $indicator['weighting'],
                    'is_quantitative' => $indicator['is_quantitative'],
                    'origin' => $indicator['origin'],
                    'source_achievement_id' => $indicator['source_achievement_id'],
                ], (array) ($built['indicators'] ?? [])),
            ];
        }

        $sectionOrder = array_flip(array_map('intval', $this->seccionIds));

        usort($sessions, function (array $a, array $b) use ($sectionOrder): int {
            return [
                $sectionOrder[$a['seccion_id']] ?? 999,
                $a['date'],
                $a['title'],
            ] <=> [
                $sectionOrder[$b['seccion_id']] ?? 999,
                $b['date'],
                $b['title'],
            ];
        });

        $this->sessions = $sessions;
        $this->selectedCount = count($this->selectedIds);
        $this->warnings = $this->sessionWarnings($sessions, $warnings);
        $this->step = 3;
    }

    public function addSession(?int $seccionId = null): void
    {
        $seccionId = $seccionId ?? $this->activitySectionId ?? ($this->seccionIds[0] ?? null);

        if ($seccionId === null) {
            $this->stepError = 'Selecciona una sección antes de agregar sesiones.';

            return;
        }

        $this->sessions[] = $this->emptySession((int) $seccionId, $this->periodStart);
    }

    public function removeSession(int $index): void
    {
        unset($this->sessions[$index]);
        $this->sessions = array_values($this->sessions);
    }

    public function duplicateSession(int $index): void
    {
        if (! isset($this->sessions[$index])) {
            return;
        }

        $copy = $this->sessions[$index];
        $copy['uid'] = uniqid('s', true);
        $copy['source_activity_id'] = null;
        $copy['source_excerpt'] = '';
        $copy['source_checksum'] = null;

        array_splice($this->sessions, $index + 1, 0, [$copy]);
    }

    public function addIndicator(int $sessionIndex): void
    {
        $this->sessions[$sessionIndex]['indicators'][] = [
            'name' => '',
            'weighting' => null,
            'is_quantitative' => false,
            'origin' => 'manual',
            'source_achievement_id' => null,
        ];
    }

    public function removeIndicator(int $sessionIndex, int $indicatorIndex): void
    {
        unset($this->sessions[$sessionIndex]['indicators'][$indicatorIndex]);
        $this->sessions[$sessionIndex]['indicators'] = array_values(
            $this->sessions[$sessionIndex]['indicators']
        );
    }

    public function goToReview(): void
    {
        $this->stepError = '';

        if ($this->sessions === []) {
            $this->stepError = 'Agrega al menos una sesión de clase.';

            return;
        }

        foreach ($this->sessions as $row) {
            if (trim((string) ($row['title'] ?? '')) === '' || empty($row['date'])) {
                $this->stepError = 'Cada sesión necesita fecha y título antes de continuar.';

                return;
            }
        }

        $this->step = 4;
    }

    public function back(): void
    {
        $this->stepError = '';
        $this->step = max(1, $this->step - 1);
    }

    public function saveDraft(): void
    {
        $this->persist(false);
    }

    public function approve(): void
    {
        $this->persist(true);
    }

    /**
     * Aprueba solo los borradores ya guardados en el paso 5.
     */
    public function approveDrafts(): void
    {
        abort_unless((bool) config('planning.didactic_plans_enabled'), 404);
        Gate::authorize('create', DidacticPlan::class);

        $service = app(DidacticPlanService::class);
        $hasErrors = false;

        foreach ($this->planResults as $index => $result) {
            if ($result['status'] !== DidacticPlan::STATUS_DRAFT || empty($result['plan_id'])) {
                continue;
            }

            try {
                $plan = DidacticPlan::query()->findOrFail($result['plan_id']);
                Gate::authorize('approve', $plan);
                $plan = $service->approve($plan, Auth::user());

                $this->planResults[$index]['status'] = $plan->status;
                $this->planResults[$index]['version'] = (int) $plan->version;
                $this->planResults[$index]['error'] = null;
                $this->planResults[$index]['errors'] = [];
            } catch (ValidationException $exception) {
                $hasErrors = true;
                $messages = collect($exception->errors())->flatten()->map(
                    fn ($message): string => (string) $message
                )->values()->all();

                $this->planResults[$index]['status'] = DidacticPlan::STATUS_DRAFT;
                $this->planResults[$index]['error'] = $this->summarizePlanErrors($messages);
                $this->planResults[$index]['errors'] = $messages;
            }
        }

        $this->approved = ! $hasErrors
            && $this->planResults !== []
            && collect($this->planResults)->every(
                fn (array $result): bool => $result['status'] === DidacticPlan::STATUS_APPROVED
            );

        if ($hasErrors) {
            $this->stepError = 'Algunas secciones no pudieron aprobarse. Revisa el detalle.';
        }
    }

    public function close(): void
    {
        $this->resetState();
    }

    /**
     * Agrega una justificación estándar para los elementos que bloquean
     * la aprobación (indicadores y actividad evaluativa).
     */
    public function justifyMissingItems(): void
    {
        $justification = 'Se justifica la ausencia de indicadores de logro y de actividad evaluativa en las sesiones que lo requieren, porque serán completados durante el desarrollo del proyecto.';

        if (! str_contains((string) $this->notes, $justification)) {
            $this->notes = trim(trim((string) $this->notes)."\n\n".$justification);
        }

        $this->stepError = '';
    }

    /**
     * Justifica los faltantes, vuelve a guardar y reintenta la aprobación.
     */
    public function justifyAndRetry(): void
    {
        $this->justifyMissingItems();
        $this->persist(true);
    }

    /**
     * @return array<int, array{label:string,ok:bool,help:string}>
     */
    public function checklistForSection(int $seccionId): array
    {
        $sessions = array_values(array_filter(
            $this->sessions,
            fn (array $row): bool => (int) ($row['seccion_id'] ?? 0) === $seccionId
        ));

        $justified = trim($this->notes) !== '';

        $allDates = $sessions !== [] && collect($sessions)->every(
            fn (array $row): bool => ! empty($row['date']) && trim((string) ($row['title'] ?? '')) !== ''
        );

        $allMoments = $sessions !== [] && collect($sessions)->every(
            fn (array $row): bool => trim((string) ($row['start'] ?? '')) !== ''
                && trim((string) ($row['development'] ?? '')) !== ''
                && trim((string) ($row['close'] ?? '')) !== ''
        );

        $allEvaluation = $sessions !== [] && collect($sessions)->every(
            fn (array $row): bool => trim((string) ($row['evaluation_description'] ?? '')) !== ''
        );

        $allIndicators = $sessions !== [] && collect($sessions)->every(
            fn (array $row): bool => collect($row['indicators'] ?? [])->contains(
                fn (array $indicator): bool => trim((string) ($indicator['name'] ?? '')) !== ''
            )
        );

        $hasOds = collect($sessions)->contains(
            fn (array $row): bool => trim((string) ($row['ods_text'] ?? '')) !== ''
        );

        return [
            ['label' => 'Al menos una sesión de clase', 'ok' => $sessions !== [], 'help' => 'La secuencia debe tener al menos un momento de clase.'],
            ['label' => 'Fecha y título en cada sesión', 'ok' => $allDates, 'help' => 'Cada sesión necesita fecha y tema.'],
            ['label' => 'Inicio, desarrollo y cierre', 'ok' => $allMoments || $justified, 'help' => 'Completa los tres momentos o justifica la ausencia en las notas.'],
            ['label' => 'Actividad evaluativa', 'ok' => $allEvaluation || $justified, 'help' => 'Describe el instrumento o justifica la ausencia.'],
            ['label' => 'Indicadores de logro', 'ok' => $allIndicators || $justified, 'help' => 'Agrega al menos un indicador por sesión o justifica.'],
            ['label' => 'ODS', 'ok' => $hasOds || $justified, 'help' => 'Agrega el ODS del eje o justifica la ausencia.'],
        ];
    }

    public function allChecklistOkForSection(int $seccionId): bool
    {
        return collect($this->checklistForSection($seccionId))
            ->every(fn (array $item): bool => $item['ok']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function visibleActivities(): array
    {
        return array_values(array_filter(
            $this->periodActivities(),
            fn (array $row): bool => $this->activitySectionId === null
                || (int) $row['seccion_id'] === (int) $this->activitySectionId
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function periodActivities(): array
    {
        $selectedSections = array_map('intval', $this->seccionIds);

        return array_values(array_filter(
            $this->activities,
            fn (array $row): bool => (bool) $row['within_period']
                && in_array((int) $row['seccion_id'], $selectedSections, true)
        ));
    }

    /**
     * @return array<int, array{id:int,label:string,short_label:string,total:int,selected:int}>
     */
    public function sectionTabs(): array
    {
        $selected = array_map('intval', $this->seccionIds);
        $order = array_flip($selected);

        return collect($this->sectionOptions)
            ->filter(fn (array $option): bool => in_array((int) $option['id'], $selected, true))
            ->map(function (array $option): array {
                $rows = array_values(array_filter(
                    $this->activities,
                    fn (array $row): bool => (int) $row['seccion_id'] === (int) $option['id']
                        && (bool) $row['within_period']
                ));

                return [
                    'id' => (int) $option['id'],
                    'label' => $option['label'],
                    'short_label' => $option['short_label'],
                    'total' => count($rows),
                    'selected' => count(array_intersect(array_column($rows, 'id'), $this->selectedIds)),
                ];
            })
            ->sortBy(fn (array $tab): int => $order[$tab['id']] ?? 999)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{seccion_id:int,label:string,sessions:array<int, array{index:int,session:array<string,mixed>}>}>
     */
    public function sessionGroups(): array
    {
        $order = array_flip(array_map('intval', $this->seccionIds));

        $rows = collect($this->sessions)
            ->map(fn (array $session, int $index): array => ['index' => $index, 'session' => $session])
            ->values();

        return $rows
            ->groupBy(fn (array $row): int => (int) ($row['session']['seccion_id'] ?? 0))
            ->map(fn (Collection $group, $seccionId): array => [
                'seccion_id' => (int) $seccionId,
                'label' => $this->sectionLabel((int) $seccionId),
                'sessions' => $group->values()->all(),
            ])
            ->sortBy(fn (array $group): int => $order[$group['seccion_id']] ?? 999)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{seccion_id:int,label:string,sessions_count:int,checklist:array<int,array<string,mixed>>,all_ok:bool}>
     */
    public function sectionReviews(): array
    {
        return collect($this->seccionIds)
            ->map(function (int $seccionId): array {
                $checklist = $this->checklistForSection($seccionId);
                $sessionsCount = count(array_filter(
                    $this->sessions,
                    fn (array $row): bool => (int) ($row['seccion_id'] ?? 0) === (int) $seccionId
                ));

                return [
                    'seccion_id' => (int) $seccionId,
                    'label' => $this->sectionLabel((int) $seccionId),
                    'sessions_count' => $sessionsCount,
                    'checklist' => $checklist,
                    'all_ok' => collect($checklist)->every(fn (array $item): bool => $item['ok']),
                ];
            })
            ->values()
            ->all();
    }

    public function hasDraftResults(): bool
    {
        return collect($this->planResults)->contains(
            fn (array $result): bool => $result['status'] === DidacticPlan::STATUS_DRAFT
        );
    }

    public function hasErrorResults(): bool
    {
        return collect($this->planResults)->contains(
            fn (array $result): bool => $result['status'] === 'error'
        );
    }

    public function render()
    {
        return view('livewire.planning.didactic-plans.wizard', [
            'visibleActivities' => $this->visibleActivities(),
            'periodActivityCount' => count($this->periodActivities()),
            'sectionTabs' => $this->sectionTabs(),
            'sessionGroups' => $this->sessionGroups(),
            'sectionReviews' => $this->sectionReviews(),
            'hasDraftResults' => $this->hasDraftResults(),
            'hasErrorResults' => $this->hasErrorResults(),
        ]);
    }

    private function persist(bool $approve): void
    {
        abort_unless((bool) config('planning.didactic_plans_enabled'), 404);

        Gate::authorize('create', DidacticPlan::class);

        $this->stepError = '';

        if ($this->seccionIds === []) {
            $this->stepError = 'Selecciona al menos una sección antes de guardar.';

            return;
        }

        if ($this->sessions === []) {
            $this->stepError = 'Agrega al menos una sesión antes de guardar.';

            return;
        }

        $periodErrors = $this->selectedPeriodErrors();

        if ($periodErrors !== []) {
            $this->stepError = implode(' ', $periodErrors);

            return;
        }

        $service = app(DidacticPlanService::class);
        $user = Auth::user();
        $results = [];
        $hasErrors = false;

        foreach ($this->sectionIdsWithSessions() as $seccionId) {
            $context = $this->sectionContext($seccionId);

            if ($context === null) {
                $hasErrors = true;
                $results[] = [
                    'seccion_id' => $seccionId,
                    'label' => 'Sección #'.$seccionId,
                    'plan_id' => null,
                    'version' => null,
                    'status' => 'error',
                    'error' => 'La sección ya no está disponible en el universo filtrado.',
                    'errors' => ['La sección ya no está disponible en el universo filtrado.'],
                ];

                continue;
            }

            $rows = array_values(array_filter(
                $this->sessions,
                fn (array $row): bool => (int) ($row['seccion_id'] ?? 0) === $seccionId
            ));

            if ($rows === []) {
                continue;
            }

            $plan = null;

            try {
                $plan = $service->saveDraft(
                    $this->buildPayloadForSection($context, $rows),
                    $user
                );

                if ($approve) {
                    Gate::authorize('approve', $plan);
                    $plan = $service->approve($plan, $user);
                }

                $results[] = [
                    'seccion_id' => $seccionId,
                    'label' => $context['label'],
                    'plan_id' => (int) $plan->id,
                    'version' => (int) $plan->version,
                    'status' => $plan->status,
                    'error' => null,
                    'errors' => [],
                ];
            } catch (ValidationException $exception) {
                $hasErrors = true;
                $messages = collect($exception->errors())->flatten()->map(
                    fn ($message): string => (string) $message
                )->values()->all();

                // El borrador pudo guardarse antes de que fallara la aprobación.
                $draft = $plan !== null ? $plan->fresh() : null;

                $results[] = [
                    'seccion_id' => $seccionId,
                    'label' => $context['label'],
                    'plan_id' => $draft !== null ? (int) $draft->id : null,
                    'version' => $draft !== null ? (int) $draft->version : null,
                    'status' => $draft !== null ? DidacticPlan::STATUS_DRAFT : 'error',
                    'error' => $this->summarizePlanErrors($messages),
                    'errors' => $messages,
                ];
            }
        }

        if ($results === []) {
            $this->stepError = 'No hay sesiones asociadas a las secciones seleccionadas.';

            return;
        }

        $this->planResults = $results;
        $this->approved = $approve && ! $hasErrors && collect($results)->every(
            fn (array $result): bool => $result['status'] === DidacticPlan::STATUS_APPROVED
        );

        if ($hasErrors) {
            $this->stepError = 'Algunas secciones no pudieron procesarse. Revisa el detalle en el resumen.';
        }

        $this->step = 5;
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    private function buildPayloadForSection(array $context, array $rows): array
    {
        $seccionId = (int) $context['id'];

        $sectionActivities = array_values(array_filter(
            $this->activities,
            fn (array $row): bool => (int) $row['seccion_id'] === $seccionId
        ));

        $selectedIds = array_values(array_intersect(
            array_column($sectionActivities, 'id'),
            $this->selectedIds
        ));

        $sessions = [];
        $sources = [];
        $planOds = [];

        foreach ($rows as $index => $row) {
            $ods = $this->splitOds((string) ($row['ods_text'] ?? ''));
            $planOds = array_merge($planOds, $ods);

            $indicators = [];

            foreach ((array) ($row['indicators'] ?? []) as $order => $indicator) {
                $name = trim((string) ($indicator['name'] ?? ''));

                if ($name === '') {
                    continue;
                }

                $indicators[] = [
                    'name' => $name,
                    'weighting' => $indicator['weighting'] ?? null,
                    'is_quantitative' => (bool) ($indicator['is_quantitative'] ?? false),
                    'origin' => $indicator['origin'] ?? 'manual',
                    'source_achievement_id' => $indicator['source_achievement_id'] ?? null,
                    'sort_order' => $order,
                ];
            }

            $date = (string) ($row['date'] ?? '');

            $sessions[] = [
                'date' => $date !== '' ? $date : $this->periodStart,
                'day_index' => $this->dayIndex($date),
                'sort_order' => $index,
                'title' => trim((string) ($row['title'] ?? 'Sesión sin título')),
                'thematic' => $row['thematic'] ?? null,
                'objective' => $row['objective'] ?? null,
                'start' => $row['start'] ?? null,
                'development' => $row['development'] ?? null,
                'close' => $row['close'] ?? null,
                'resources' => $row['resources'] ?? null,
                'evaluation_instrument' => $row['evaluation_instrument'] ?? null,
                'evaluation_description' => $row['evaluation_description'] ?? null,
                'observations' => $row['observations'] ?? null,
                'ods' => $ods,
                'is_non_teaching' => (bool) ($row['is_non_teaching'] ?? false),
                'source_activity_id' => $row['source_activity_id'] ?? null,
                'source_field' => $row['source_field'] ?? null,
                'source_excerpt' => $row['source_excerpt'] ?? null,
                'source_checksum' => $row['source_checksum'] ?? null,
                'confidence' => $row['confidence'] ?? null,
                'indicators' => $indicators,
            ];

            if (! empty($row['source_activity_id'])) {
                $sources[(int) $row['source_activity_id']] = [
                    'activity_id' => (int) $row['source_activity_id'],
                    'pevaluacion_id' => $row['source_pevaluacion_id'] ?? null,
                    'activity_checksum' => $row['source_checksum'] ?? null,
                    'fields_used' => ['topic', 'teaching', 'description', 'observations'],
                    'excerpt' => mb_substr((string) ($row['source_excerpt'] ?? ''), 0, 500),
                    'included' => true,
                ];
            }
        }

        return [
            'pestudio_id' => (int) $context['pestudio_id'],
            'grado_id' => (int) $context['grado_id'],
            'seccion_id' => $seccionId,
            'lapso_id' => $context['lapso_id'] ?? $this->lapsoId,
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'axis' => $this->axis,
            'project_title' => $this->projectTitle,
            'objective' => $this->objective,
            'ods' => array_values(array_unique($planOds)),
            'notes' => $this->notes,
            'pdf_notes' => $this->pdfNotes,
            'filter_snapshot' => $this->filters,
            'filter_hash' => $this->filterHash,
            'universe_hash' => app(FilteredActivitiesQuery::class)
                ->universeHash(array_column($sectionActivities, 'id')),
            'filtered_count' => count($sectionActivities),
            'selected_count' => count($selectedIds),
            'sessions' => $sessions,
            'sources' => array_values($sources),
        ];
    }

    private function applySectionSelection(): void
    {
        $this->seccionIds = array_values(array_unique(array_map('intval', $this->seccionIds)));
        $this->seccionIds = array_values(array_filter(
            $this->seccionIds,
            fn (int $id): bool => $this->sectionContext($id) !== null
        ));

        $this->selectedIds = [];
        $this->selectedCount = 0;
        $this->sessions = [];
        $this->warnings = [];

        if ($this->activitySectionId === null
            || ! in_array((int) $this->activitySectionId, $this->seccionIds, true)) {
            $this->activitySectionId = $this->seccionIds[0] ?? null;
        }

        $this->recalculateContext();
    }

    private function recalculateContext(): void
    {
        $this->resetContext();

        if ($this->seccionIds === []) {
            return;
        }

        $activities = $this->sectionActivities();
        $lapso = $this->lapsoId !== null ? Lapso::query()->find($this->lapsoId) : null;
        $range = app(DidacticPlanPeriodResolver::class)->resolveRange($activities, $lapso);

        if ($range['start'] === null || $range['end'] === null) {
            $this->stepError = 'El filtro no define un periodo con fechas válidas. Ajusta los filtros.';

            return;
        }

        $this->derivedRangeStart = $range['start'];
        $this->derivedRangeEnd = $range['end'];
        $this->rangeStart = $range['start'];
        $this->rangeEnd = $range['end'];
        $this->rangeOrigin = (string) $range['origin'];

        if ($this->buildWeekOptions(false, false) !== []) {
            return;
        }

        $this->filterHash = hash('sha256', json_encode($this->normalizeForHash($this->filters)));

        $this->activities = $this->buildActivityRows($activities);
        $this->filteredCount = count($this->activities);
        $this->universeHash = app(FilteredActivitiesQuery::class)
            ->universeHash(array_column($this->activities, 'id'));

        $this->activitySectionId = $this->resolveActivitySection();
    }

    /**
     * El periodo elegido debe ser una semana y estar dentro del rango aplicado.
     *
     * @return array<int, string>
     */
    private function selectedPeriodErrors(): array
    {
        $resolver = app(DidacticPlanPeriodResolver::class);
        $errors = $resolver->validateWeek($this->periodStart, $this->periodEnd);

        if ($errors !== []) {
            return $errors;
        }

        $start = $resolver->normalizeDate($this->periodStart);
        $end = $resolver->normalizeDate($this->periodEnd);

        if ($this->rangeStart === null || $this->rangeStart === ''
            || $this->rangeEnd === null || $this->rangeEnd === ''
            || $start === null || $end === null
            || $start < $this->rangeStart || $end > $this->rangeEnd) {
            return ['El periodo seleccionado debe estar dentro del rango definido.'];
        }

        return [];
    }

    /**
     * Valida el periodo escrito a mano y sincroniza la semana sugerida.
     */
    private function applyCustomPeriod(): void
    {
        $errors = $this->selectedPeriodErrors();
        $this->stepError = implode(' ', $errors);

        if ($errors !== []) {
            return;
        }

        $match = collect($this->weekOptions)->first(
            fn (array $week): bool => $week['start'] === $this->periodStart
                && $week['end'] === $this->periodEnd
        );

        $this->selectedWeek = $match['value'] ?? null;

        $this->rebuildActivityRows();
    }

    /**
     * Aplica el rango editado manualmente y reconstruye las semanas.
     *
     * @return array<int, string>
     */
    private function applyRangeFromInputs(bool $preserveWeek = true): array
    {
        $resolver = app(DidacticPlanPeriodResolver::class);
        $start = $resolver->normalizeDate($this->rangeStart);
        $end = $resolver->normalizeDate($this->rangeEnd);

        if ($start === null || $end === null) {
            $this->stepError = 'Indica una fecha inicial y una fecha final válidas para el periodo.';

            return ['invalid_range'];
        }

        if ($start > $end) {
            $this->stepError = 'La fecha inicial del periodo no puede ser posterior a la fecha final.';

            return ['invalid_range'];
        }

        $days = (int) Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;

        if ($days > 366) {
            $this->stepError = 'El periodo no puede superar 366 días.';

            return ['invalid_range'];
        }

        $this->rangeStart = $start;
        $this->rangeEnd = $end;

        return $this->buildWeekOptions($preserveWeek);
    }

    /**
     * Construye las semanas del rango aplicado y conserva la semana actual
     * cuando sigue siendo válida.
     *
     * @return array<int, string>
     */
    private function buildWeekOptions(bool $preserveWeek = true, bool $rebuildActivities = true): array
    {
        $resolver = app(DidacticPlanPeriodResolver::class);

        $this->weekOptions = collect($resolver->weeks($this->rangeStart, $this->rangeEnd))
            ->map(function (array $week, int $index): array {
                $start = Carbon::parse($week['period_start']);
                $end = Carbon::parse($week['period_end']);

                return [
                    'value' => $week['period_start'].'|'.$week['period_end'],
                    'label' => 'Semana '.($index + 1).': '.$start->format('d/m/Y').' – '.$end->format('d/m/Y'),
                    'start' => $week['period_start'],
                    'end' => $week['period_end'],
                    'days' => (int) $week['days'],
                ];
            })
            ->values()
            ->all();

        if ($this->weekOptions === []) {
            $this->selectedWeek = null;
            $this->periodStart = '';
            $this->periodEnd = '';
            $this->stepError = 'No fue posible construir una semana con el periodo seleccionado.';

            return ['empty_period'];
        }

        $keepPeriod = $preserveWeek
            && $this->periodStart !== null && $this->periodStart !== ''
            && $this->periodEnd !== null && $this->periodEnd !== ''
            && $this->periodStart >= $this->rangeStart
            && $this->periodEnd <= $this->rangeEnd;

        if ($keepPeriod) {
            $match = collect($this->weekOptions)->first(
                fn (array $week): bool => $week['start'] === $this->periodStart
                    && $week['end'] === $this->periodEnd
            );

            $this->selectedWeek = $match['value'] ?? null;
        } else {
            $this->selectedWeek = $this->weekOptions[0]['value'];
            $this->periodStart = $this->weekOptions[0]['start'];
            $this->periodEnd = $this->weekOptions[0]['end'];
        }

        $this->stepError = '';

        if ($rebuildActivities) {
            $this->rebuildActivityRows();
        }

        return [];
    }

    private function resolveActivitySection(): ?int
    {
        $selected = array_map('intval', $this->seccionIds);

        $withActivities = collect($this->sectionOptions)
            ->filter(fn (array $option): bool => in_array((int) $option['id'], $selected, true)
                && (int) $option['activity_count'] > 0)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        if ($this->activitySectionId !== null && in_array((int) $this->activitySectionId, $withActivities, true)) {
            return (int) $this->activitySectionId;
        }

        return $withActivities[0] ?? ($selected[0] ?? null);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Activity>
     */
    private function sectionActivities(): Collection
    {
        if ($this->seccionIds === []) {
            return collect();
        }

        $allowed = $this->filteredPevaluacions()
            ->whereIn('seccion_id', array_map('intval', $this->seccionIds))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($allowed === []) {
            return collect();
        }

        return Activity::query()
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.seccion.grado', 'achievements'])
            ->whereIn('pevaluacion_id', $allowed)
            ->orderBy('finicial')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Activity>  $activities
     * @return array<int, array<string, mixed>>
     */
    private function buildActivityRows(Collection $activities): array
    {
        $scorer = app(DidacticPlanScorer::class);
        $resolver = app(DidacticPlanPeriodResolver::class);
        $sections = collect($this->sectionOptions)->keyBy('id');
        $rows = [];

        foreach ($activities as $activity) {
            $score = $scorer->score($activity, $this->periodStart, $this->periodEnd);
            $date = $resolver->normalizeDate($activity->finicial ?? null);
            $seccionId = (int) ($activity->pevaluacion?->seccion_id ?? 0);
            $section = $sections->get($seccionId);

            $rows[] = [
                'id' => (int) $activity->id,
                'seccion_id' => $seccionId,
                'seccion_label' => $section['short_label'] ?? 'Sección #'.$seccionId,
                'date' => $date,
                'end' => $resolver->normalizeDate($activity->ffinal ?? null),
                'title' => (string) ($activity->topic ?: 'Sin tema'),
                'subject' => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
                'professor' => trim(sprintf(
                    '%s %s',
                    $activity->pevaluacion?->profesor?->name ?? '',
                    $activity->pevaluacion?->profesor?->lastname ?? ''
                )),
                'pevaluacion_id' => (int) $activity->pevaluacion_id,
                'score' => $score['score'],
                'level' => $score['level'],
                'reasons' => $score['reasons'],
                'complete' => $score['complete'],
                'partial' => $score['partial'],
                'has_evaluation' => trim((string) $activity->description) !== '',
                'has_ods' => str_contains(mb_strtolower((string) $activity->observations), 'ods'),
                'has_indicators' => $score['indicator_count'] > 0,
                'indicator_count' => $score['indicator_count'],
                'within_period' => $score['within_week'],
            ];
        }

        usort($rows, fn (array $a, array $b): int => [$a['seccion_id'], $a['date'] ?? '9999-12-31', $a['id']]
            <=> [$b['seccion_id'], $b['date'] ?? '9999-12-31', $b['id']]);

        return $rows;
    }

    private function rebuildActivityRows(): void
    {
        if ($this->seccionIds === []) {
            return;
        }

        $this->activities = $this->buildActivityRows($this->sectionActivities());
        $periodActivityIds = array_column($this->periodActivities(), 'id');
        $this->selectedIds = array_values(array_intersect(
            array_map('intval', $this->selectedIds),
            $periodActivityIds
        ));
    }

    private function buildSectionOptions(): void
    {
        $pevaluacions = $this->filteredPevaluacions();

        $this->sectionOptions = $pevaluacions
            ->groupBy('seccion_id')
            ->map(function (Collection $group): array {
                /** @var \App\Models\app\Academy\Pevaluacion $first */
                $first = $group->first();
                $grado = $first->seccion?->grado;

                return [
                    'id' => (int) $first->seccion_id,
                    'label' => trim(sprintf(
                        '%s %s · %s',
                        $grado?->name ?? 'Grado',
                        $first->seccion?->name ?? '',
                        $first->pensum?->pestudio?->name ?? 'Plan'
                    )),
                    'short_label' => trim(sprintf(
                        '%s %s',
                        $grado?->name ?? 'Grado',
                        $first->seccion?->name ?? ''
                    )),
                    'grado_id' => (int) ($first->seccion?->grado_id ?? 0),
                    'grado_name' => (string) ($grado?->name ?? 'Grado'),
                    'grado_order' => (int) ($grado?->order ?? 0),
                    'pestudio_id' => (int) ($first->pensum?->pestudio_id ?? 0),
                    'pestudio_name' => (string) ($first->pensum?->pestudio?->name ?? 'Plan'),
                    'lapso_id' => $first->lapso_id !== null ? (int) $first->lapso_id : null,
                    'activity_count' => (int) $group->sum(fn ($pevaluacion): int => (int) ($pevaluacion->activities_count ?? 0)),
                ];
            })
            ->sortBy([
                ['pestudio_name', 'asc'],
                ['grado_order', 'asc'],
                ['short_label', 'asc'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, int>
     */
    private function defaultSectionIds(): array
    {
        $withActivities = collect($this->sectionOptions)
            ->filter(fn (array $option): bool => (int) $option['activity_count'] > 0)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        if (! empty($this->filters['pestudio_id'])) {
            return $withActivities;
        }

        if (count($this->sectionOptions) === 1) {
            return [(int) $this->sectionOptions[0]['id']];
        }

        return [];
    }

    /**
     * Pevaluaciones del universo filtrado cuya sección pertenece al mismo
     * plan de estudio y grado que su pensum.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\app\Academy\Pevaluacion>
     */
    private function filteredPevaluacions(): Collection
    {
        if ($this->filteredPevaluacionsCache !== null) {
            return $this->filteredPevaluacionsCache;
        }

        return $this->filteredPevaluacionsCache = app(FilteredActivitiesQuery::class)
            ->pevaluacions($this->filters)
            ->get()
            ->filter(function ($pevaluacion): bool {
                $grado = $pevaluacion->seccion?->grado;
                $pensum = $pevaluacion->pensum;

                return $grado !== null
                    && $pensum !== null
                    && (int) $pensum->pestudio_id === (int) $grado->pestudio_id
                    && (int) $pensum->grado_id === (int) $grado->id;
            })
            ->values();
    }

    /**
     * @return array<int, int>
     */
    private function sectionIdsWithSessions(): array
    {
        $withSessions = array_values(array_unique(array_filter(array_map(
            fn (array $row): int => (int) ($row['seccion_id'] ?? 0),
            $this->sessions
        ))));

        return array_values(array_filter(
            array_map('intval', $this->seccionIds),
            fn (int $id): bool => in_array($id, $withSessions, true)
        ));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function sectionContext(int $seccionId): ?array
    {
        foreach ($this->sectionOptions as $option) {
            if ((int) $option['id'] === $seccionId) {
                return $option;
            }
        }

        return null;
    }

    private function sectionLabel(int $seccionId): string
    {
        return $this->sectionContext($seccionId)['label'] ?? 'Sección #'.$seccionId;
    }

    /**
     * Resume mensajes repetidos de validación para no mostrar párrafos
     * gigantes en el paso 5.
     *
     * @param  array<int, string>  $messages
     */
    private function summarizePlanErrors(array $messages): string
    {
        $counters = [
            'sin_indicadores' => 0,
            'sin_evaluacion' => 0,
            'sin_momentos' => 0,
            'sin_fecha' => 0,
            'fuera_periodo' => 0,
            'sin_titulo' => 0,
            'sin_ods' => 0,
            'sin_sesiones' => 0,
        ];
        $others = [];

        foreach ($messages as $message) {
            $clean = trim((string) preg_replace('/^Sesión\s+\d+:\s*/u', '', trim((string) $message)));

            if ($clean === '') {
                continue;
            }

            $haystack = mb_strtolower($clean);

            if (str_contains($haystack, 'al menos un indicador')) {
                $counters['sin_indicadores']++;
            } elseif (str_contains($haystack, 'actividad evaluativa')) {
                $counters['sin_evaluacion']++;
            } elseif (str_contains($haystack, 'completa los momentos')) {
                $counters['sin_momentos']++;
            } elseif (str_contains($haystack, 'falta la fecha')) {
                $counters['sin_fecha']++;
            } elseif (str_contains($haystack, 'fuera del periodo')) {
                $counters['fuera_periodo']++;
            } elseif (str_contains($haystack, 'falta el título')) {
                $counters['sin_titulo']++;
            } elseif (str_contains($haystack, 'ningún ods')) {
                $counters['sin_ods']++;
            } elseif (str_contains($haystack, 'al menos una sesión')) {
                $counters['sin_sesiones']++;
            } else {
                $others[$clean] = ($others[$clean] ?? 0) + 1;
            }
        }

        $labels = [
            'sin_indicadores' => 'sin indicadores',
            'sin_evaluacion' => 'sin actividad evaluativa',
            'sin_momentos' => 'sin momentos completos',
            'sin_fecha' => 'sin fecha',
            'fuera_periodo' => 'con fecha fuera del periodo',
            'sin_titulo' => 'sin título',
            'sin_sesiones' => 'sin sesiones',
        ];

        $parts = [];

        foreach ($labels as $key => $label) {
            if ($counters[$key] > 0) {
                $parts[] = $counters[$key].($counters[$key] === 1 ? ' sesión ' : ' sesiones ').$label;
            }
        }

        if ($counters['sin_ods'] > 0) {
            $parts[] = 'el plan no menciona ODS';
        }

        $otherParts = [];

        foreach ($others as $message => $count) {
            $otherParts[] = $count > 1 ? "{$count} × {$message}" : $message;
        }

        if (count($otherParts) > 2) {
            $remaining = count($otherParts) - 2;
            $otherParts = array_slice($otherParts, 0, 2);
            $otherParts[] = '+'.$remaining.' más';
        }

        $parts = array_merge($parts, $otherParts);

        if ($parts === []) {
            return '';
        }

        return ucfirst(implode(' · ', $parts)).'.';
    }

    /**
     * @param  array<int, array<string, mixed>>  $sessions
     * @param  array<int, string>  $warnings
     * @return array<int, string>
     */
    private function sessionWarnings(array $sessions, array $warnings): array
    {
        $bySectionDate = [];

        foreach ($sessions as $session) {
            $key = ($session['seccion_id'] ?? 0).'|'.($session['date'] ?? 'sin-fecha');
            $bySectionDate[$key][] = $session['title'] ?? '';
        }

        foreach ($bySectionDate as $key => $titles) {
            if (count($titles) > 1) {
                [$seccionId, $date] = explode('|', (string) $key, 2);
                $warnings[] = 'Hay '.count($titles).' sesiones el '.$date.' en '.$this->sectionLabel((int) $seccionId).': revisa si deben fusionarse.';
            }
        }

        $withSessions = array_map(
            fn (array $row): int => (int) ($row['seccion_id'] ?? 0),
            $sessions
        );

        $sectionsWithoutSessions = collect($this->seccionIds)
            ->reject(fn (int $seccionId): bool => in_array((int) $seccionId, $withSessions, true))
            ->map(fn (int $seccionId): string => $this->sectionLabel((int) $seccionId))
            ->values()
            ->all();

        if (count($sectionsWithoutSessions) === 1) {
            $warnings[] = 'La sección '.$sectionsWithoutSessions[0].' no tiene sesiones; no se generará plan para esa sección.';
        } elseif (count($sectionsWithoutSessions) > 1) {
            $visible = array_slice($sectionsWithoutSessions, 0, 5);
            $remaining = count($sectionsWithoutSessions) - count($visible);

            $warnings[] = 'Hay '.count($sectionsWithoutSessions).' secciones sin sesiones; no se generará plan para ellas: '
                .implode(', ', $visible)
                .($remaining > 0 ? ' y '.$remaining.' más' : '')
                .'.';
        }

        return array_values(array_unique($warnings));
    }

    /**
     * @return array<string, mixed>
     */
    private function emptySession(int $seccionId, string $date): array
    {
        return [
            'uid' => uniqid('s', true),
            'seccion_id' => $seccionId,
            'date' => $date,
            'day_index' => $this->dayIndex($date),
            'title' => '',
            'thematic' => '',
            'objective' => '',
            'start' => '',
            'development' => '',
            'close' => '',
            'resources' => '',
            'evaluation_instrument' => '',
            'evaluation_description' => '',
            'observations' => '',
            'ods' => [],
            'ods_text' => '',
            'is_non_teaching' => false,
            'source_activity_id' => null,
            'source_pevaluacion_id' => null,
            'source_field' => null,
            'source_excerpt' => '',
            'source_checksum' => null,
            'confidence' => null,
            'indicators' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function sanitizeFilters(array $filters): array
    {
        $clean = [];

        foreach (self::FILTER_KEYS as $key) {
            if (array_key_exists($key, $filters) && $filters[$key] !== '' && $filters[$key] !== null) {
                $clean[$key] = $filters[$key];
            }
        }

        return $clean;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    private function buildFilterChips(array $filters): array
    {
        $labels = [
            'search' => 'Buscar',
            'pestudio_id' => 'Plan de estudio',
            'grado_id' => 'Grado',
            'seccion_id' => 'Sección',
            'lapso_id' => 'Momento',
            'profesor_id' => 'Profesor',
            'status_activities' => 'Actividades',
            'filter_status' => 'Estado',
            'filter_observations' => 'Observaciones',
            'date_from' => 'Desde',
            'date_to' => 'Hasta',
        ];

        $chips = [];

        foreach ($filters as $key => $value) {
            if ($value === false || $value === null || $value === '') {
                continue;
            }

            $chips[] = ($labels[$key] ?? $key).': '.(\is_bool($value) ? ($value ? 'Sí' : 'No') : (string) $value);
        }

        return $chips;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function normalizeForHash(array $filters): array
    {
        $normalized = [];

        foreach ($filters as $key => $value) {
            if (\is_bool($value)) {
                $normalized[$key] = $value;
            } elseif (\is_array($value)) {
                $normalized[$key] = array_values(array_map('strval', $value));
            } elseif ($value !== null && $value !== '') {
                $normalized[$key] = (string) $value;
            }
        }

        ksort($normalized);

        return $normalized;
    }

    private function dayIndex(string $date): int
    {
        if ($date === '') {
            return 1;
        }

        try {
            return Carbon::parse($date)->dayOfWeekIso;
        } catch (\Throwable) {
            return 1;
        }
    }

    /**
     * @return array<int, string>
     */
    private function splitOds(string $text): array
    {
        $parts = preg_split('/[,;|]+/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn (string $value): bool => $value !== ''));
    }

    private function resetContext(): void
    {
        $this->activities = [];
        $this->selectedIds = [];
        $this->sessions = [];
        $this->warnings = [];
        $this->weekOptions = [];
        $this->selectedWeek = '';
        $this->periodStart = '';
        $this->periodEnd = '';
        $this->rangeStart = '';
        $this->rangeEnd = '';
        $this->rangeOrigin = '';
        $this->derivedRangeStart = '';
        $this->derivedRangeEnd = '';
        $this->filteredCount = 0;
        $this->selectedCount = 0;
        $this->universeHash = '';
        $this->filterHash = '';
        $this->stepError = '';
    }

    private function resetState(): void
    {
        $this->reset([
            'open', 'step', 'filters', 'filterChips', 'sectionOptions', 'seccionIds',
            'activitySectionId', 'lapsoId', 'rangeStart', 'rangeEnd', 'rangeOrigin',
            'derivedRangeStart', 'derivedRangeEnd',
            'weekOptions', 'selectedWeek', 'periodStart', 'periodEnd', 'axis',
            'projectTitle', 'objective', 'activities', 'selectedIds', 'sessions',
            'warnings', 'stepError', 'planResults', 'approved', 'filteredCount',
            'selectedCount', 'universeHash', 'filterHash', 'notes', 'pdfNotes',
        ]);

        $this->axis = 'Alimentación, cultura e identidad';
        $this->selectedCount = 0;
    }
}
