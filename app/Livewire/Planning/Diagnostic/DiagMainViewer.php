<?php

namespace App\Livewire\Planning\Diagnostic;

use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Services\Planning\DiagnosticMarkdownBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DiagMainViewer extends Component
{
    use WithPagination;

    #[Url]
    public ?int $selectedId = null;

    // Filtros + paginación para "Resumen por área de formación" — anidados pestudio→grado→pensum
    public string $progressSearch = '';

    public ?int $progressPestudioId = null;

    public ?int $progressGradoId = null;

    public ?int $progressPensumId = null;

    public ?int $resumenGradoId = null;

    public ?int $resumenPestudioId = null;

    public ?int $resumenAreaId = null;

    public int $paginate = 10;

    public string $activeTab = 'general';

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        // #[Url] hidrata selectedId desde la query string antes del mount:
        // no forzar null para no borrar el parámetro de la URL.
        if ($this->selectedId) {
            $this->dispatch('diag-main-selected', id: $this->selectedId);
        }
    }

    public function updatedSelectedId(): void
    {
        $this->progressSearch = '';
        $this->progressGradoId = null;
        $this->progressPensumId = null;
        $this->resumenPestudioId = null;
        $this->resumenGradoId = null;
        $this->resumenAreaId = null;
        $this->activeTab = 'general';
        $this->resetPage('pensumProgressPage');
        $this->resetPage('gradoProgressPage');
        $this->resetPage('areaProgressPage');
        $this->dispatch('diag-main-selected', id: $this->selectedId);
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['general', 'areas', 'grados', 'areas_conocimiento'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function updatedResumenAreaId(): void
    {
        $this->resetPage('areaProgressPage');
    }

    public function triggerCreate(): void
    {
        $this->dispatch('open-diag-create');
    }

    public function downloadMarkdownReport()
    {
        $report = $this->buildMarkdownPayload();
        $markdown = app(DiagnosticMarkdownBuilder::class)->build($report);
        $slug = Str::slug($report['selected']['name'] ?? 'catalogo') ?: 'catalogo';

        return response()->streamDownload(
            static function () use ($markdown): void {
                echo $markdown;
            },
            'diagnostico-'.$slug.'-'.now()->format('Ymd_His').'.md',
            ['Content-Type' => 'text/markdown; charset=UTF-8']
        );
    }

    private function buildMarkdownPayload(): array
    {
        $instruments = DiagMain::with(['lapso', 'pestudio', 'referent'])
            ->withCount(['questions', 'sessions'])
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'active', 'lapso_id', 'pestudio_id', 'referent_id']);
        $catalog = $this->buildCatalogPayload($instruments);
        $selected = $this->selectedId ? $instruments->firstWhere('id', $this->selectedId) : null;

        $instrumentData = $selected ? $this->buildInstrumentPayload($selected) : [
            'selected' => null,
            'kpis' => [],
            'summary' => [],
            'pensums' => [],
            'grades' => [],
            'knowledge_areas' => [],
            'session_fallback' => false,
            'knowledge_area_overlap' => false,
        ];

        $questionFilters = $this->questionBankFilters();
        $activationSearch = trim((string) request()->query('diagActivationSearch', ''));
        $questionBank = $selected ? $this->buildQuestionBankPayload($questionFilters) : [
            'filters' => $questionFilters,
            'summary' => [],
            'questions' => [],
            'selected_pensum' => null,
        ];
        $activation = $selected ? $this->buildActivationPayload($activationSearch) : [
            'search' => $activationSearch,
            'pensums' => [],
        ];

        $route = route('app.planning.diagnostico.index');
        if ($selected) {
            $route .= '?'.http_build_query(['selectedId' => $selected->id]);
        }

        return [
            'generated_at' => now(),
            'source_url' => $route,
            'selected' => $instrumentData['selected'],
            'catalog' => $catalog,
            'kpis' => $instrumentData['kpis'],
            'summary' => $instrumentData['summary'],
            'pensums' => $instrumentData['pensums'],
            'grades' => $instrumentData['grades'],
            'knowledge_areas' => $instrumentData['knowledge_areas'],
            'question_bank' => $questionBank,
            'activation' => $activation,
            'session_fallback' => $instrumentData['session_fallback'],
            'knowledge_area_overlap' => $instrumentData['knowledge_area_overlap'],
            'catalog_comparison' => (bool) $selected,
            'alerts' => [],
            'filters' => [
                'selected_id' => $selected?->id,
                'selected_name' => $selected?->name,
                'progress_search' => $this->progressSearch,
                'progress_pestudio' => $this->filterName(Pestudio::class, $this->progressPestudioId),
                'progress_grado' => $this->filterName(Grado::class, $this->progressGradoId),
                'progress_pensum' => $this->filterName(Pensum::class, $this->progressPensumId),
                'summary_pestudio' => $this->filterName(Pestudio::class, $this->resumenPestudioId),
                'summary_grado' => $this->filterName(Grado::class, $this->resumenGradoId),
                'summary_area' => $this->filterName(AreaConocimiento::class, $this->resumenAreaId),
                'question_bank_scope' => $this->questionBankScopeLabel($questionFilters),
                'question_bank_load' => $this->questionBankLoadLabel($questionFilters),
                'question_search' => $questionFilters['search'],
                'question_show_inactive' => $questionFilters['show_inactive'] ? 'Sí' : 'No',
                'activation_search' => $activationSearch,
            ],
        ];
    }

    private function buildCatalogPayload($instruments): array
    {
        $questionPensums = DiagQuestion::query()
            ->whereNotNull('diag_main_id')
            ->get(['diag_main_id', 'pensum_id'])
            ->groupBy('diag_main_id')
            ->map(fn ($rows) => $rows->pluck('pensum_id')->filter()->unique()->values());

        $answersByInstrument = DB::table('diag_answers as answers')
            ->join('diag_questions as questions', 'questions.id', '=', 'answers.question_id')
            ->whereNotNull('answers.completado_at')
            ->selectRaw('questions.diag_main_id, COUNT(*) as total')
            ->groupBy('questions.diag_main_id')
            ->pluck('total', 'diag_main_id');

        $completedSessionsByInstrument = DB::table('diag_sessions')
            ->whereNotNull('diag_main_id')->whereNotNull('completado_at')
            ->selectRaw('diag_main_id, COUNT(*) as total')
            ->groupBy('diag_main_id')->pluck('total', 'diag_main_id');

        $precisionByInstrument = DB::table('diag_answers as answers')
            ->join('diag_questions as questions', 'questions.id', '=', 'answers.question_id')
            ->leftJoin('diag_options as options', 'options.id', '=', 'answers.option_id')
            ->where('questions.activo', 1)
            ->where('questions.tipo_pregunta', 'multiple')
            ->whereNotNull('answers.completado_at')
            ->whereNotNull('answers.option_id')
            ->selectRaw('questions.diag_main_id, COUNT(*) as total, SUM(CASE WHEN options.valor = 1 THEN 1 ELSE 0 END) as correct')
            ->groupBy('questions.diag_main_id')
            ->get()
            ->keyBy('diag_main_id');

        return $instruments->map(function (DiagMain $instrument) use ($questionPensums, $answersByInstrument, $completedSessionsByInstrument, $precisionByInstrument) {
            $precision = $precisionByInstrument->get($instrument->id);

            return [
                'id' => $instrument->id,
                'name' => $instrument->name,
                'description' => $instrument->description,
                'active' => (bool) $instrument->active,
                'lapso' => $instrument->lapso?->name,
                'pestudio' => $instrument->pestudio?->full_name,
                'referent' => $instrument->referent ? trim($instrument->referent->code.' — '.$instrument->referent->name, ' —') : null,
                'questions' => (int) $instrument->questions_count,
                'pensums_with_questions' => $questionPensums->get($instrument->id, collect())->count(),
                'sessions' => (int) $instrument->sessions_count,
                'completed_sessions' => (int) ($completedSessionsByInstrument[$instrument->id] ?? 0),
                'answers' => (int) ($answersByInstrument[$instrument->id] ?? 0),
                'precision' => $precision && (int) $precision->total > 0
                    ? round(100 * (int) $precision->correct / (int) $precision->total, 1)
                    : null,
            ];
        })->all();
    }

    private function buildInstrumentPayload(DiagMain $selected): array
    {
        $selected->loadMissing(['lapso', 'pestudio', 'referent']);
        $questionPensumIds = DiagQuestion::where('diag_main_id', $selected->id)
            ->pluck('pensum_id')->unique()->filter()->values();

        $answers = DiagAnswer::whereHas('question', fn ($query) => $query->where('diag_main_id', $selected->id))
            ->whereNotNull('completado_at');
        $answerCount = (clone $answers)->count();
        $answeredQuestionIds = (clone $answers)->pluck('question_id')->unique()->filter()->values();
        $pensumsWithAnswers = $answeredQuestionIds->isNotEmpty()
            ? DiagQuestion::whereIn('id', $answeredQuestionIds)->pluck('pensum_id')->filter()->unique()->count()
            : 0;

        $directSessionCount = DiagSession::where('diag_main_id', $selected->id)->count();
        $sessionFallback = $directSessionCount === 0 && $questionPensumIds->isNotEmpty();
        $sessions = $sessionFallback
            ? DiagSession::whereIn('pensum_id', $questionPensumIds)
            : DiagSession::where('diag_main_id', $selected->id);
        $sessionsCount = (clone $sessions)->count();
        $completedSessions = (clone $sessions)->whereNotNull('completado_at')->count();
        $students = (clone $sessions)->whereNotNull('estudiant_id')->distinct('estudiant_id')->count('estudiant_id');

        $precisionBase = DiagAnswer::whereHas('question', fn ($query) => $query->where('diag_main_id', $selected->id)->where('activo', 1)->where('tipo_pregunta', 'multiple'))
            ->whereNotNull('completado_at')->whereNotNull('option_id');
        $precisionTotal = (clone $precisionBase)->count();
        $precisionCorrect = (clone $precisionBase)->whereHas('selectedOption', fn ($query) => $query->where('valor', 1))->count();
        $completionRate = $sessionsCount > 0 ? round(100 * $completedSessions / $sessionsCount, 1) : null;
        $abandonRate = $completionRate !== null ? round(100 - $completionRate, 1) : null;
        $precision = $precisionTotal > 0 ? round(100 * $precisionCorrect / $precisionTotal, 1) : null;

        $pensums = Pensum::whereIn('id', $questionPensumIds)->with(['pestudio', 'grado', 'asignatura'])->get();
        $progress = $this->buildPensumProgress($selected, $pensums);
        $filteredProgress = collect($progress);
        if ($this->progressSearch !== '') {
            $needle = mb_strtolower($this->progressSearch);
            $filteredProgress = $filteredProgress->filter(fn ($row) => str_contains(mb_strtolower($row['fullname']), $needle)
                || str_contains(mb_strtolower($row['asignatura']), $needle)
                || str_contains(mb_strtolower($row['asignatura_code']), $needle));
        }
        if ($this->progressPestudioId) {
            $filteredProgress = $filteredProgress->where('pestudio_id', (int) $this->progressPestudioId);
        }
        if ($this->progressGradoId) {
            $filteredProgress = $filteredProgress->where('grado_id', (int) $this->progressGradoId);
        }
        if ($this->progressPensumId) {
            $filteredProgress = $filteredProgress->where('pensum_id', (int) $this->progressPensumId);
        }

        $grades = $this->buildGradeProgress($progress);
        if ($this->resumenPestudioId) {
            $grades = array_values(array_filter($grades, fn ($row) => $row['pestudio_id'] === (int) $this->resumenPestudioId));
        }
        if ($this->resumenGradoId) {
            $grades = array_values(array_filter($grades, fn ($row) => $row['grado_id'] === (int) $this->resumenGradoId));
        }

        [$knowledgeAreas, $hasOverlap] = $this->buildKnowledgeAreaProgress($progress);
        if ($this->resumenAreaId) {
            $knowledgeAreas = array_values(array_filter($knowledgeAreas, fn ($row) => in_array((int) $this->resumenAreaId, $row['area_ids'] ?? [$row['area_id']], true)));
        }

        $recentSessions = (clone $sessions)->with(['estudiant', 'pensum.asignatura', 'pensum.grado'])
            ->orderByDesc('iniciado_at')->limit(5)->get()
            ->map(fn (DiagSession $session) => [
                'student' => $session->estudiant?->full_name ?? trim(($session->estudiant?->name ?? '').' '.($session->estudiant?->lastname ?? '')) ?: '—',
                'area' => $session->pensum?->full_name ?? '—',
                'progress' => $session->progreso,
                'started_at' => $session->iniciado_at ? Carbon::parse($session->iniciado_at)->format('d/m/Y H:i') : '—',
            ])->all();

        $displayPestudio = $selected->pestudio;
        $derived = [];
        $pestudioSource = 'Campo directo del instrumento.';
        if (! $displayPestudio && $questionPensumIds->isNotEmpty()) {
            $pestudioIds = $pensums->pluck('pestudio_id')->filter()->unique()->values();
            if ($pestudioIds->count() === 1) {
                $displayPestudio = Pestudio::find($pestudioIds->first());
                $pestudioSource = 'Derivado de las preguntas: un único plan asociado.';
                $derived[] = 'plan de estudio';
            } elseif ($pestudioIds->count() > 1) {
                $pestudioSource = 'Las preguntas corresponden a varios planes; la vista indica “Múltiples planes”.';
            }
        }

        $selectedPayload = [
            'id' => $selected->id,
            'name' => $selected->name,
            'description' => $selected->description,
            'active' => (bool) $selected->active,
            'lapso' => $selected->lapso?->name,
            'pestudio' => $displayPestudio?->full_name ?? ($selected->pestudio_id && ! $displayPestudio ? 'Múltiples planes' : null),
            'referent' => $selected->referent ? trim($selected->referent->code.' — '.$selected->referent->name, ' —') : null,
            'lapso_source' => $selected->lapso ? 'Campo directo del instrumento.' : 'No disponible; la vista muestra —.',
            'pestudio_source' => $pestudioSource,
            'referent_source' => $selected->referent ? 'Campo directo del instrumento.' : 'No disponible; la vista muestra —.',
            'derived' => $derived,
        ];

        return [
            'selected' => $selectedPayload,
            'kpis' => [
                'questions' => (int) $selected->questions()->count(),
                'pensums_with_answers' => $pensumsWithAnswers,
                'questions_with_answers' => $answeredQuestionIds->count(),
                'answers' => $answerCount,
                'students' => $students,
                'sessions' => $sessionsCount,
                'completed_sessions' => $completedSessions,
                'completion_rate' => $completionRate,
                'abandon_rate' => $abandonRate,
                'precision' => $precision,
                'precision_correct' => $precisionCorrect,
                'precision_total' => $precisionTotal,
                'sessions_scope' => $sessionFallback
                    ? 'Fallback por pensum de preguntas; puede incluir sesiones de otros instrumentos.'
                    : 'Sesiones con diag_main_id igual al instrumento.',
            ],
            'summary' => [
                'recent_sessions' => $recentSessions,
                'questions_by_type' => DiagQuestion::where('diag_main_id', $selected->id)
                    ->selectRaw('tipo_pregunta as label, COUNT(*) as count')->groupBy('tipo_pregunta')->get()->toArray(),
                'questions_by_difficulty' => DiagQuestion::where('diag_main_id', $selected->id)
                    ->selectRaw('difficulty as label, COUNT(*) as count')->groupBy('difficulty')->get()->toArray(),
            ],
            'pensums' => $filteredProgress->sortByDesc('completion_rate')->values()->all(),
            'grades' => $grades,
            'knowledge_areas' => $knowledgeAreas,
            'session_fallback' => $sessionFallback,
            'knowledge_area_overlap' => $hasOverlap,
        ];
    }

    private function buildPensumProgress(DiagMain $selected, $pensums): array
    {
        $pensumIds = $pensums->pluck('id');
        if ($pensumIds->isEmpty()) {
            return [];
        }

        $activeQuestions = DiagQuestion::where('diag_main_id', $selected->id)->where('activo', 1)
            ->whereIn('pensum_id', $pensumIds)->selectRaw('pensum_id, COUNT(*) as total')->groupBy('pensum_id')->pluck('total', 'pensum_id');
        $sessionStats = DB::table('diag_sessions')->whereIn('pensum_id', $pensumIds)
            ->selectRaw('pensum_id, COUNT(*) as total, SUM(CASE WHEN completado_at IS NOT NULL THEN 1 ELSE 0 END) as completed')
            ->groupBy('pensum_id')->get()->keyBy('pensum_id');
        $fallbackSessionStats = DB::table('diag_sessions')->where('diag_main_id', $selected->id)->whereIn('pensum_id', $pensumIds)
            ->selectRaw('pensum_id, COUNT(*) as total')->groupBy('pensum_id')->get()->keyBy('pensum_id');
        $answerStats = DB::table('diag_answers as answers')
            ->join('diag_questions as questions', 'questions.id', '=', 'answers.question_id')
            ->leftJoin('diag_options as options', 'options.id', '=', 'answers.option_id')
            ->where('questions.diag_main_id', $selected->id)
            ->where('questions.tipo_pregunta', 'multiple')
            ->whereIn('questions.pensum_id', $pensumIds)
            ->whereNotNull('answers.completado_at')->whereNotNull('answers.option_id')
            ->selectRaw('questions.pensum_id, COUNT(*) as total, SUM(CASE WHEN options.valor = 1 THEN 1 ELSE 0 END) as correct')
            ->groupBy('questions.pensum_id')->get()->keyBy('pensum_id');

        return $pensums->map(function (Pensum $pensum) use ($activeQuestions, $sessionStats, $fallbackSessionStats, $answerStats) {
            $sessionStat = $sessionStats->get($pensum->id);
            $totalSessions = (int) ($sessionStat->total ?? 0);
            if ($totalSessions === 0) {
                $totalSessions = (int) ($fallbackSessionStats->get($pensum->id)->total ?? 0);
            }
            $completedSessions = (int) ($sessionStat->completed ?? 0);
            $answerStat = $answerStats->get($pensum->id);
            $answerTotal = (int) ($answerStat->total ?? 0);
            $answerCorrect = (int) ($answerStat->correct ?? 0);
            $assignment = $pensum->asignatura?->name ?? '—';

            return [
                'pensum_id' => (int) $pensum->id,
                'fullname' => $pensum->full_name ?? ($pensum->grado?->name.' — '.$assignment),
                'pestudio_id' => (int) $pensum->pestudio_id,
                'pestudio' => $pensum->pestudio?->full_name ?? '—',
                'grado_id' => (int) $pensum->grado_id,
                'grado' => $pensum->grado?->name ?? '—',
                'asignatura' => $assignment,
                'asignatura_code' => $pensum->asignatura?->code ?? '',
                'active_questions' => (int) ($activeQuestions[$pensum->id] ?? 0),
                'sessions' => $totalSessions,
                'completed_sessions' => $completedSessions,
                'completion_rate' => $totalSessions > 0 ? round(100 * $completedSessions / $totalSessions, 1) : 0,
                'precision' => $answerTotal > 0 ? round(100 * $answerCorrect / $answerTotal, 1) : null,
                'answers' => $answerTotal,
                'correct_answers' => $answerCorrect,
                'active_for_diagnostic' => (bool) $pensum->status_active_diagnostic,
            ];
        })->all();
    }

    private function buildGradeProgress(array $pensumRows): array
    {
        $gradeIds = collect($pensumRows)->pluck('grado_id')->unique()->filter();
        $grades = Grado::whereIn('id', $gradeIds)->where('status_active', 'true')->get(['id', 'name', 'pestudio_id'])->keyBy('id');

        return collect($pensumRows)->filter(fn ($row) => $grades->has($row['grado_id']))
            ->groupBy('grado_id')->map(function ($items, $gradeId) use ($grades) {
                $sessions = $items->sum('sessions');
                $completed = $items->sum('completed_sessions');
                $answers = $items->sum('answers');
                $correct = $items->sum('correct_answers');
                $grade = $grades->get($gradeId);

                return [
                    'grado_id' => (int) $gradeId,
                    'pestudio_id' => (int) $grade->pestudio_id,
                    'name' => $grade->name,
                    'questions' => $items->sum('active_questions'),
                    'sessions' => $sessions,
                    'completed_sessions' => $completed,
                    'completion_rate' => $sessions > 0 ? round(100 * $completed / $sessions, 1) : 0,
                    'precision' => $answers > 0 ? round(100 * $correct / $answers, 1) : null,
                    'answers' => $answers,
                    'correct_answers' => $correct,
                ];
            })->sortBy('name')->values()->all();
    }

    private function buildKnowledgeAreaProgress(array $pensumRows): array
    {
        if ($pensumRows === []) {
            return [[], false];
        }

        $pensumIds = collect($pensumRows)->pluck('pensum_id');
        $links = CampoConocimiento::whereIn('pensum_id', $pensumIds)->get(['pensum_id', 'area_conocimiento_id'])->groupBy('pensum_id');
        $areaIds = $links->flatten()->pluck('area_conocimiento_id')->filter()->unique();
        $areas = AreaConocimiento::whereIn('id', $areaIds)->get(['id', 'name', 'code'])->keyBy('id');
        $buckets = [];
        $hasOverlap = false;

        foreach ($pensumRows as $pensum) {
            $ids = ($links->get($pensum['pensum_id']) ?? collect())->pluck('area_conocimiento_id')->filter()->unique()->values();
            if ($ids->count() > 1) {
                $hasOverlap = true;
            }
            if ($ids->isEmpty()) {
                $ids = collect([0]);
            }
            foreach ($ids as $areaId) {
                $areaId = (int) $areaId;
                $area = $areas->get($areaId);
                // El catálogo tiene filas repetidas por code+name (mismo área en varios planes);
                // se consolidan para no contar dos veces el mismo pensum.
                $key = $area
                    ? mb_strtolower(trim((string) $area->code)).'|'.mb_strtolower(trim((string) $area->name))
                    : 'sin-area';
                if (! isset($buckets[$key])) {
                    $buckets[$key] = [
                        'area_id' => $areaId,
                        'area_ids' => [],
                        'name' => $area ? trim(($area->code ? $area->code.' — ' : '').$area->name) : 'Sin área asignada',
                        'pensum_ids' => [],
                        'questions' => 0,
                        'sessions' => 0,
                        'completed_sessions' => 0,
                        'answers' => 0,
                        'correct_answers' => 0,
                    ];
                }
                $buckets[$key]['area_ids'][$areaId] = true;
                if (isset($buckets[$key]['pensum_ids'][$pensum['pensum_id']])) {
                    // El pensum ya sumó en esta área consolidada.
                    continue;
                }
                $buckets[$key]['pensum_ids'][$pensum['pensum_id']] = true;
                $buckets[$key]['questions'] += $pensum['active_questions'];
                $buckets[$key]['sessions'] += $pensum['sessions'];
                $buckets[$key]['completed_sessions'] += $pensum['completed_sessions'];
                $buckets[$key]['answers'] += $pensum['answers'];
                $buckets[$key]['correct_answers'] += $pensum['correct_answers'];
            }
        }

        $rows = collect($buckets)->map(function ($bucket) {
            $sessions = $bucket['sessions'];
            $answers = $bucket['answers'];
            return [
                'area_id' => $bucket['area_id'],
                'area_ids' => array_keys($bucket['area_ids']),
                'name' => $bucket['name'],
                'pensums' => count($bucket['pensum_ids']),
                'questions' => $bucket['questions'],
                'sessions' => $sessions,
                'completed_sessions' => $bucket['completed_sessions'],
                'completion_rate' => $sessions > 0 ? round(100 * $bucket['completed_sessions'] / $sessions, 1) : 0,
                'precision' => $answers > 0 ? round(100 * $bucket['correct_answers'] / $answers, 1) : null,
                'answers' => $answers,
                'correct_answers' => $bucket['correct_answers'],
            ];
        })->sortBy('name')->values()->all();

        return [$rows, $hasOverlap];
    }

    private function questionBankFilters(): array
    {
        $showInactive = filter_var(request()->query('diagQuestionShowInactive', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return [
            'pestudio_id' => $this->queryId('diagQuestionPestudio'),
            'grado_id' => $this->queryId('diagQuestionGrado'),
            'pevaluacion_id' => $this->queryId('diagQuestionPevaluacion'),
            'grupo_id' => $this->queryId('diagQuestionGroup'),
            'pensum_id' => $this->queryId('diagQuestionPensum'),
            'search' => trim((string) request()->query('diagQuestionSearch', '')),
            'show_inactive' => $showInactive ?? true,
        ];
    }

    private function questionBankScopeLabel(array $filters): string
    {
        return collect([
            $filters['pestudio_id'] ? 'Plan #'.$filters['pestudio_id'] : null,
            $filters['grado_id'] ? 'Grado #'.$filters['grado_id'] : null,
            $filters['pensum_id'] ? 'Pensum #'.$filters['pensum_id'] : null,
        ])->filter()->implode(' · ') ?: 'Todos';
    }

    private function questionBankLoadLabel(array $filters): string
    {
        return collect([
            $filters['pevaluacion_id'] ? 'Carga #'.$filters['pevaluacion_id'] : null,
            $filters['grupo_id'] ? 'Grupo #'.$filters['grupo_id'] : null,
        ])->filter()->implode(' · ') ?: 'Todos';
    }

    private function queryId(string $key): ?int
    {
        $value = request()->query($key);
        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    private function filterName(string $model, ?int $id): string
    {
        if (! $id) {
            return 'Todos';
        }

        $record = $model::find($id);
        return $record?->full_name ?? $record?->name ?? ('ID '.$id);
    }

    private function buildQuestionBankPayload(array $filters): array
    {
        $questionPensumIds = DiagQuestion::query()->select('pensum_id')->distinct()->pluck('pensum_id')->filter();
        $pensumQuery = Pensum::with(['asignatura', 'grado', 'pestudio'])->whereIn('id', $questionPensumIds);

        if ($filters['pestudio_id']) {
            $pensumQuery->where('pestudio_id', $filters['pestudio_id']);
        }
        if ($filters['grado_id']) {
            $pensumQuery->where('grado_id', $filters['grado_id']);
        }
        if ($filters['pevaluacion_id']) {
            $pensumId = Pevaluacion::find($filters['pevaluacion_id'])?->pensum_id;
            $pensumQuery->where('id', $pensumId ?? 0);
        }
        if ($filters['grupo_id']) {
            $pensumIds = Pevaluacion::where('grupo_estable_id', $filters['grupo_id'])->pluck('pensum_id')->unique();
            $pensumQuery->whereIn('id', $pensumIds);
        }

        $pensumOptions = $pensumQuery->orderBy('pestudio_id')->orderBy('grado_id')->get();
        $selectedPensum = $filters['pensum_id']
            ? Pensum::with(['asignatura', 'grado', 'pestudio'])->find($filters['pensum_id'])
            : null;
        $summary = [];
        $questions = [];

        if (! $selectedPensum) {
            $ids = $pensumOptions->pluck('id');
            $questionCounts = DiagQuestion::whereIn('pensum_id', $ids)
                ->selectRaw('pensum_id, COUNT(*) as total, SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as active_total')
                ->groupBy('pensum_id')->get()->keyBy('pensum_id');
            $loadCounts = Pevaluacion::whereIn('pensum_id', $ids)
                ->selectRaw('pensum_id, COUNT(*) as total, COUNT(DISTINCT grupo_estable_id) as groups_total')
                ->groupBy('pensum_id')->get()->keyBy('pensum_id');

            foreach ($pensumOptions as $pensum) {
                $qCount = $questionCounts->get($pensum->id);
                $loadCount = $loadCounts->get($pensum->id);
                $summary[] = [
                    'pensum' => '#'.$pensum->id.' '.($pensum->asignatura?->name ?? '—'),
                    'pestudio' => $pensum->pestudio?->full_name ?? '—',
                    'grado' => $pensum->grado?->name ?? '—',
                    'questions' => (int) ($qCount->total ?? 0),
                    'active_questions' => (int) ($qCount->active_total ?? 0),
                    'pevaluacions' => (int) ($loadCount->total ?? 0),
                    'groups' => (int) ($loadCount->groups_total ?? 0),
                ];
            }
            usort($summary, fn ($left, $right) => $right['questions'] <=> $left['questions']);
        } else {
            $questionQuery = DiagQuestion::with(['competency', 'indicator', 'options', 'diagMain'])
                ->where('pensum_id', $selectedPensum->id)
                ->when(! $filters['show_inactive'], fn ($query) => $query->where('activo', true))
                ->when($filters['search'] !== '', fn ($query) => $query->where('pregunta', 'like', '%'.$filters['search'].'%'))
                ->orderBy('orden')->orderBy('id');
            $questionModels = $questionQuery->get();

            $loadsQuery = Pevaluacion::with(['grupoEstable', 'seccion.grado', 'pensum.grado', 'profesor', 'lapso'])
                ->where('pensum_id', $selectedPensum->id)
                ->when($filters['pevaluacion_id'], fn ($query) => $query->where('id', $filters['pevaluacion_id']))
                ->when($filters['grupo_id'], fn ($query) => $query->where('grupo_estable_id', $filters['grupo_id']))
                ->orderBy('lapso_id')->orderBy('seccion_id');
            $loadsByGroup = $loadsQuery->get()->groupBy(fn (Pevaluacion $load) => $load->grupo_estable_id ?? 0);
            if ($loadsByGroup->isEmpty()) {
                $loadsByGroup->put(0, collect());
            }

            foreach ($loadsByGroup as $groupId => $loads) {
                $group = $loads->first()?->grupoEstable;
                $groupLabel = $group ? trim($group->code.' — '.$group->name, ' —') : 'Sin grupo estable';
                $loadLabels = $loads->map(function (Pevaluacion $load) {
                    $section = $load->seccion
                        ? (($load->seccion->grado?->name ?? '—').'/'.$load->seccion->name)
                        : ($load->pensum?->grado?->name ?? '—');
                    $teacher = trim(($load->profesor?->lastname ?? '').' '.($load->profesor?->name ?? '')) ?: '—';
                    return $section.' · '.$teacher.' · '.($load->lapso?->name ?? '—');
                })->implode('; ');
                foreach ($questionModels as $question) {
                    $options = $question->options->map(fn ($option) => [
                        'text' => $option->opcion,
                        'correct' => (int) $option->valor === 1,
                    ])->all();
                    $questions[] = [
                        'group' => $groupLabel,
                        'loads' => $loadLabels ?: 'Sin cargas asociadas',
                        'diagnostic' => $question->diagMain?->name,
                        'question' => $question->pregunta,
                        'type' => $question->tipo_pregunta,
                        'difficulty' => $question->difficulty,
                        'weighing' => $question->weighing,
                        'order' => $question->orden,
                        'active' => (bool) $question->activo,
                        'competency' => $question->competency?->name,
                        'indicator' => $question->indicator
                            ? trim(($question->indicator->code ? $question->indicator->code.' — ' : '').$question->indicator->description, ' —')
                            : null,
                        'options' => $options,
                    ];
                }
            }
        }

        $scopeIds = $selectedPensum ? collect([$selectedPensum->id]) : $pensumOptions->pluck('id');
        $qualityQuery = DiagQuestion::whereIn('pensum_id', $scopeIds);
        if ($selectedPensum) {
            $qualityQuery->when(! $filters['show_inactive'], fn ($query) => $query->where('activo', true))
                ->when($filters['search'] !== '', fn ($query) => $query->where('pregunta', 'like', '%'.$filters['search'].'%'));
        }

        return [
            'filters' => $filters,
            'selected_pensum' => $selectedPensum
                ? '#'.$selectedPensum->id.' '.($selectedPensum->asignatura?->name ?? '—').' — '.($selectedPensum->grado?->name ?? '—')
                : null,
            'summary' => $summary,
            'questions' => $questions,
            'inactive_questions' => (clone $qualityQuery)->where('activo', false)->count(),
            'questions_without_options' => (clone $qualityQuery)->whereDoesntHave('options')->count(),
            'questions_without_correct_option' => (clone $qualityQuery)->where('tipo_pregunta', 'multiple')
                ->whereDoesntHave('options', fn ($query) => $query->where('valor', 1))->count(),
        ];
    }

    private function buildActivationPayload(string $search): array
    {
        $pensums = Pensum::query()
            ->where('pensums.status_active', true)
            ->where(function ($query) {
                $query->whereHas('pevaluacions')->orWhereHas('diagQuestions');
            })
            ->whereHas('grado', fn ($query) => $query->where('status_active', 'true'))
            ->whereHas('pestudio', fn ($query) => $query->where('status_active', 'true'))
            ->with(['asignatura', 'grado', 'pestudio'])
            ->withCount([
                'diagQuestions',
                'diagQuestions as active_questions_count' => fn ($query) => $query->where('activo', true),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where(function ($subQuery) use ($term) {
                    $subQuery->whereHas('asignatura', fn ($assignment) => $assignment->where('name', 'like', $term)->orWhere('code', 'like', $term))
                        ->orWhereHas('grado', fn ($grade) => $grade->where('name', 'like', $term));
                });
            })
            ->orderBy('grado_id')->orderBy('asignatura_id')->get();

        return [
            'search' => $search,
            'pensums' => $pensums->map(fn (Pensum $pensum) => [
                'pestudio' => $pensum->pestudio?->full_name ?? '—',
                'grado' => $pensum->grado?->name ?? '—',
                'asignatura' => trim(($pensum->asignatura?->code ? $pensum->asignatura->code.' — ' : '').($pensum->asignatura?->name ?? '—')),
                'questions' => (int) $pensum->diag_questions_count,
                'active_questions' => (int) $pensum->active_questions_count,
                'active_for_diagnostic' => (bool) $pensum->status_active_diagnostic,
            ])->all(),
        ];
    }

    public function updatedProgressSearch(): void
    {
        $this->resetPage('pensumProgressPage');
    }

    public function updatedProgressPestudioId(): void
    {
        $this->progressGradoId = null;
        $this->progressPensumId = null;
        $this->resetPage('pensumProgressPage');
    }

    public function updatedProgressGradoId(): void
    {
        $this->progressPensumId = null;
        $this->resetPage('pensumProgressPage');
    }

    public function updatedProgressPensumId(): void
    {
        $this->resetPage('pensumProgressPage');
    }

    public function updatedResumenGradoId(): void
    {
        // Anidamiento: un grado sólo es válido si hay un pestudio seleccionado.
        if (! $this->resumenPestudioId) {
            $this->resumenGradoId = null;
        }
        $this->resetPage('gradoProgressPage');
    }

    public function updatedResumenPestudioId(): void
    {
        $this->resumenGradoId = null;
        $this->resetPage('gradoProgressPage');
    }

    public function updatedPaginate(): void
    {
        $this->resetPage('pensumProgressPage');
    }

    public function render()
    {
        $diagMains = DiagMain::with(['lapso', 'pestudio', 'referent'])
            ->orderByRaw('active DESC')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'active', 'lapso_id', 'pestudio_id', 'referent_id']);

        $selected = null;
        if ($this->selectedId) {
            $selected = DiagMain::with(['lapso', 'pestudio', 'referent'])
                ->withCount(['questions', 'sessions'])
                ->find($this->selectedId);
        }

        // Stats y display derivados — corrige cifras cuando diag_main_id no está en sesiones
        $completedSessions = null;
        $studentsEvaluated = null;
        $displayPestudio = null;
        $displayLapso = null;
        $displayReferent = null;
        $questionsCount = null;
        $sessionsCount = null;
        $precision = null;
        $precisionCorrect = 0;
        $precisionTotal = 0;
        $pensumProgress = collect();
        $recentSessions = collect();
        $questionsByType = collect();
        $questionsByDifficulty = collect();
        $progressPestudios = collect();
        $progressGrados = collect();
        $progressPensums = collect();
        $pestudiosForGradoResumen = collect();
        $gradosForResumen = collect();
        $gradosAnidados = collect();
        $gradoProgress = collect();
        $areasConocimientoOptions = collect();
        $areaProgress = collect();

        // Nuevos indicadores solicitados (pensums/respuestas) + completitud/abandono s2526
        $pensumsWithAnswersCount = null;
        $questionsWithAnswersCount = null;
        $totalAnswersCount = null;
        $completionRate = null;
        $abandonRate = null;

        if ($selected) {
            $questionsCount = $selected->questions_count;

            // Pensums vinculados a las preguntas de este diagnóstico
            $questionPensumIds = DiagQuestion::where('diag_main_id', $selected->id)->pluck('pensum_id')->unique()->filter()->values();

            // Cálculo de pensums con al menos un answer / preguntas con respuestas / total answers
            $answerScoped = DiagAnswer::whereHas('question', fn ($q) => $q->where('diag_main_id', $selected->id))->whereNotNull('completado_at');
            $totalAnswersCount = (clone $answerScoped)->count();
            $answerQuestionIds = (clone $answerScoped)->pluck('question_id')->unique()->filter()->values();
            $questionsWithAnswersCount = $answerQuestionIds->count();
            $pensumsWithAnswersCount = $answerQuestionIds->isNotEmpty()
                ? DiagQuestion::whereIn('id', $answerQuestionIds)->pluck('pensum_id')->unique()->filter()->count()
                : 0;

            // Sesiones: primero por diag_main_id, si da 0 usar pensum de las preguntas (datos reales)
            $sessionsCount = DiagSession::where('diag_main_id', $selected->id)->count();
            $sessionQuery = $sessionsCount > 0
                ? DiagSession::where('diag_main_id', $selected->id)
                : DiagSession::whereIn('pensum_id', $questionPensumIds);

            if ($questionPensumIds->isNotEmpty() && $sessionsCount === 0) {
                $sessionsCount = (clone $sessionQuery)->count();
            }

            $completedSessions = (clone $sessionQuery)->whereNotNull('completado_at')->count();
            $studentsEvaluated = (clone $sessionQuery)->whereNotNull('estudiant_id')->distinct('estudiant_id')->count('estudiant_id');

            // Precisión global del diagnóstico (como en s2526: correctas / total contestadas)
            $answersBase = DiagAnswer::whereHas('question', function ($q) use ($selected) {
                $q->where('diag_main_id', $selected->id)->where('activo', 1)->where('tipo_pregunta', 'multiple');
            })->whereNotNull('completado_at')->whereNotNull('option_id');

            $precisionTotal = (clone $answersBase)->count();
            $precisionCorrect = (clone $answersBase)->whereHas('selectedOption', fn ($q) => $q->where('valor', 1))->count();
            $precision = $precisionTotal > 0 ? round((100 * $precisionCorrect) / $precisionTotal, 1) : null;

            // % Completitud y Tasa Abandono (s2526: getGeneralStats / analytics.blade.php:70)
            $completionRate = $sessionsCount > 0 ? round((100 * $completedSessions) / $sessionsCount, 1) : null;
            $abandonRate = $sessionsCount > 0 ? round(100 - $completionRate, 1) : null;

            // Pensum progress — inspirado en s2526 getPensumProgress (con precisión por área)
            if ($questionPensumIds->isNotEmpty()) {
                $pensumProgress = Pensum::whereIn('id', $questionPensumIds)->with(['asignatura', 'grado'])->get()->map(function (Pensum $pensum) use ($selected) {
                    $pid = $pensum->id;
                    $totalQ = DiagQuestion::where('pensum_id', $pid)->where('diag_main_id', $selected->id)->where('activo', 1)->count();
                    $totalS = DiagSession::where('pensum_id', $pid)->count();
                    if ($totalS === 0) {
                        $totalS = DiagSession::where('diag_main_id', $selected->id)->where('pensum_id', $pid)->count();
                    }
                    $completedS = DiagSession::where('pensum_id', $pid)->whereNotNull('completado_at')->count();
                    $completion = $totalS > 0 ? round((100 * $completedS) / $totalS, 1) : 0;

                    $ansBase = DiagAnswer::whereHas('question', fn ($q) => $q->where('pensum_id', $pid)->where('diag_main_id', $selected->id)->where('tipo_pregunta', 'multiple'))->whereNotNull('completado_at')->whereNotNull('option_id');
                    $totalAns = (clone $ansBase)->count();
                    $correctAns = (clone $ansBase)->whereHas('selectedOption', fn ($q) => $q->where('valor', 1))->count();
                    $prec = $totalAns > 0 ? round((100 * $correctAns) / $totalAns, 1) : null;

                    return (object) [
                        'pensum' => $pensum,
                        'fullname' => $pensum->full_name ?? ($pensum->grado?->name.' - '.$pensum->asignatura?->name),
                        'total_questions' => $totalQ,
                        'total_sessions' => $totalS,
                        'completed_sessions' => $completedS,
                        'completion_percentage' => $completion,
                        'precision' => $prec,
                        'total_answered' => $totalAns,
                        'correct_answers' => $correctAns,
                    ];
                })->sortByDesc('completion_percentage')->values();
            }

            // Filtros + paginación para el resumen (search / grado / pensum) — como s2526
            $progressPestudios = collect();
            $progressGrados = collect();
            $progressPensums = collect();
            if ($questionPensumIds->isNotEmpty()) {
                $progressPestudios = Pestudio::whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('pestudio_id'))->orderBy('code')->get(['id', 'code', 'name']);
                if ($this->progressPestudioId) {
                    $progressGrados = Grado::where('pestudio_id', $this->progressPestudioId)->whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('grado_id'))->where('status_active', 'true')->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
                    $progressPensums = Pensum::whereIn('id', $questionPensumIds)->where('pestudio_id', $this->progressPestudioId)->with(['asignatura', 'grado'])->orderBy('grado_id')->get(['id', 'grado_id', 'pestudio_id', 'asignatura_id']);
                    if ($this->progressGradoId) {
                        $progressPensums = $progressPensums->where('grado_id', $this->progressGradoId)->values();
                    }
                } else {
                    $progressGrados = Grado::whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('grado_id'))->where('status_active', 'true')->orderBy('pestudio_id')->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
                    $progressPensums = Pensum::whereIn('id', $questionPensumIds)->with(['asignatura', 'grado'])->orderBy('pestudio_id')->orderBy('grado_id')->get(['id', 'grado_id', 'pestudio_id', 'asignatura_id']);
                    if ($this->progressGradoId) {
                        $progressPensums = $progressPensums->where('grado_id', $this->progressGradoId)->values();
                    }
                }
            }

            // Aplicar filtros al collection ya ordenado y paginar
            $allProgress = $pensumProgress;
            $filteredProgress = $pensumProgress;
            if ($this->progressSearch !== '') {
                $s = mb_strtolower($this->progressSearch);
                $filteredProgress = $filteredProgress->filter(fn ($pp) => str_contains(mb_strtolower($pp->fullname ?? ''), $s) || str_contains(mb_strtolower($pp->pensum->asignatura?->name ?? ''), $s) || str_contains(mb_strtolower($pp->pensum->asignatura?->code ?? ''), $s));
            }
            if ($this->progressPestudioId) {
                $filteredProgress = $filteredProgress->filter(fn ($pp) => (int) $pp->pensum->pestudio_id === (int) $this->progressPestudioId);
            }
            if ($this->progressGradoId) {
                $filteredProgress = $filteredProgress->filter(fn ($pp) => (int) $pp->pensum->grado_id === (int) $this->progressGradoId);
            }
            if ($this->progressPensumId) {
                $filteredProgress = $filteredProgress->filter(fn ($pp) => (int) $pp->pensum->id === (int) $this->progressPensumId);
            }
            $perPage = max(1, (int) $this->paginate);
            $currentPage = LengthAwarePaginator::resolveCurrentPage('pensumProgressPage');
            $total = $filteredProgress->count();
            $items = $filteredProgress->forPage($currentPage, $perPage)->values();
            $pensumProgress = new LengthAwarePaginator($items, $total, $perPage, $currentPage, ['path' => request()->url(), 'pageName' => 'pensumProgressPage']);

            // Grado resumen — agregado por gradoId con select pestudio+grado específico
            // Anidamiento pestudio → grado: el grado sólo aplica si hay un pestudio elegido.
            $pestudiosForGradoResumen = Pestudio::whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('pestudio_id'))->orderBy('code')->get(['id', 'code', 'name']);
            if (! $this->resumenPestudioId) {
                $this->resumenGradoId = null;
            }
            $gradosForResumenQuery = Grado::whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('grado_id'))->where('status_active', 'true');
            if ($this->resumenPestudioId) {
                $gradosForResumenQuery->where('pestudio_id', (int) $this->resumenPestudioId);
            }
            $gradosForResumen = $gradosForResumenQuery->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
            // Todos los grados activos del alcance (independiente del filtro de pestudio).
            $activeGradoIds = Grado::whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('grado_id'))->where('status_active', 'true')->pluck('id')->map(fn ($id) => (int) $id)->all();
            // Opciones del select de grado: anidadas bajo el pestudio elegido, sólo grados activos.
            $gradosAnidados = $this->resumenPestudioId
                ? $gradosForResumen->values()
                : collect();
            // Si el grado elegido no pertenece al pestudio elegido, se descarta.
            if ($this->resumenGradoId && ! $gradosAnidados->contains('id', (int) $this->resumenGradoId)) {
                $this->resumenGradoId = null;
            }
            $gradoProgress = collect();
            if ($questionPensumIds->isNotEmpty()) {
                // Base independiente de los filtros de la pestaña "Por área": esta pestaña
                // se rige sólo por sus propios filtros resumenPestudioId / resumenGradoId.
                $baseForGrado = $allProgress;
                $gradoProgress = $baseForGrado->groupBy(fn ($pp) => $pp->pensum->grado_id)->map(function ($items) {
                    $grado = $items->first()->pensum->grado;
                    $totalQ = $items->sum('total_questions');
                    $totalS = $items->sum('total_sessions');
                    $completedS = $items->sum('completed_sessions');
                    $completion = $totalS > 0 ? round((100 * $completedS) / $totalS, 1) : 0;
                    $totalAns = $items->sum('total_answered');
                    $correctAns = $items->sum('correct_answers');
                    $prec = $totalAns > 0 ? round((100 * $correctAns) / $totalAns, 1) : null;

                    return (object) [
                        'grado' => $grado,
                        'fullname' => $grado?->name ?? '—',
                        'total_questions' => $totalQ,
                        'total_sessions' => $totalS,
                        'completed_sessions' => $completedS,
                        'completion_percentage' => $completion,
                        'precision' => $prec,
                        'total_answered' => $totalAns,
                        'correct_answers' => $correctAns,
                    ];
                })->values()->sortBy('fullname')->values();
                // Sólo grados activos, coherente con las opciones del select anidado.
                $gradoProgress = $gradoProgress->filter(fn ($gp) => $gp->grado && in_array((int) $gp->grado->id, $activeGradoIds, true))->values();
                if ($this->resumenPestudioId) {
                    $gradoProgress = $gradoProgress->filter(fn ($gp) => $gp->grado && (int) $gp->grado->pestudio_id === (int) $this->resumenPestudioId)->values();
                }
                if ($this->resumenGradoId) {
                    $gradoProgress = $gradoProgress->where('grado.id', (int) $this->resumenGradoId)->values();
                }
                // Paginación por defecto 10 filas por página para Resumen por Grado
                $gradoPerPage = 10;
                $gradoCurrentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('gradoProgressPage');
                $gradoTotal = $gradoProgress->count();
                $gradoItems = $gradoProgress->forPage($gradoCurrentPage, $gradoPerPage)->values();
                $gradoProgress = new \Illuminate\Pagination\LengthAwarePaginator($gradoItems, $gradoTotal, $gradoPerPage, $gradoCurrentPage, ['path' => request()->url(), 'pageName' => 'gradoProgressPage']);
            }

            // Resumen por Área de Conocimiento — agrega la base por pensum
            // ($allProgress) según campo_conocimientos.pensum_id. Un pensum
            // puede pertenecer a 0..N áreas: los sin adscripción caen en la
            // fila "Sin área asignada" y los compartidos suman en cada área
            // (los totales del tab pueden solaparse, igual que las preguntas
            // compartidas entre grados no existen pero entre áreas sí).
            if ($questionPensumIds->isNotEmpty()) {
                $areaIds = CampoConocimiento::whereIn('pensum_id', $questionPensumIds)
                    ->distinct()->pluck('area_conocimiento_id')
                    ->filter()->map(fn ($id) => (int) $id)->values();
                $areasConocimientoOptions = AreaConocimiento::whereIn('id', $areaIds)
                    ->with(['pestudio', 'leader.profile'])
                    ->orderBy('code')->orderBy('name')
                    ->get(['id', 'name', 'code', 'pestudio_id', 'leader_id']);

                // El área elegida debe seguir perteneciendo al alcance del diagnóstico.
                if ($this->resumenAreaId && ! $areasConocimientoOptions->contains('id', (int) $this->resumenAreaId)) {
                    $this->resumenAreaId = null;
                }

                $pensumAreaMap = CampoConocimiento::whereIn('pensum_id', $questionPensumIds)
                    ->get(['pensum_id', 'area_conocimiento_id'])
                    ->groupBy('pensum_id');
                $areasById = $areasConocimientoOptions->keyBy('id');

                $areaRows = [];
                foreach ($allProgress as $pp) {
                    $pid = $pp->pensum->id;
                    $aids = isset($pensumAreaMap[$pid])
                        ? $pensumAreaMap[$pid]->pluck('area_conocimiento_id')->map(fn ($id) => (int) $id)->unique()->values()->all()
                        : [];
                    if (empty($aids)) {
                        $aids = [0];
                    }
                    foreach ($aids as $aid) {
                        $area = $areasById->get($aid);
                        // El catálogo repite áreas con el mismo code+name (una fila por plan);
                        // se consolidan para no duplicar los totales del mismo pensum.
                        $key = $area
                            ? mb_strtolower(trim((string) $area->code)).'|'.mb_strtolower(trim((string) $area->name))
                            : 'sin-area';
                        if (! isset($areaRows[$key])) {
                            $areaRows[$key] = [
                                'area' => $area,
                                'area_ids' => [],
                                'pensum_ids' => [],
                                'total_questions' => 0,
                                'total_sessions' => 0,
                                'completed_sessions' => 0,
                                'total_answered' => 0,
                                'correct_answers' => 0,
                            ];
                        }
                        $areaRows[$key]['area_ids'][(int) $aid] = true;
                        if (isset($areaRows[$key]['pensum_ids'][$pid])) {
                            // El pensum ya sumó en esta área consolidada.
                            continue;
                        }
                        $areaRows[$key]['pensum_ids'][$pid] = true;
                        $areaRows[$key]['total_questions'] += $pp->total_questions;
                        $areaRows[$key]['total_sessions'] += $pp->total_sessions;
                        $areaRows[$key]['completed_sessions'] += $pp->completed_sessions;
                        $areaRows[$key]['total_answered'] += $pp->total_answered;
                        $areaRows[$key]['correct_answers'] += $pp->correct_answers;
                    }
                }

                $areaProgress = collect($areaRows)->map(function ($row) {
                    $totalS = $row['total_sessions'];
                    $completion = $totalS > 0 ? round((100 * $row['completed_sessions']) / $totalS, 1) : 0;
                    $totalAns = $row['total_answered'];
                    $prec = $totalAns > 0 ? round((100 * $row['correct_answers']) / $totalAns, 1) : null;

                    return (object) [
                        'area' => $row['area'],
                        'area_ids' => array_keys($row['area_ids']),
                        'fullname' => $row['area'] ? (($row['area']->code ? $row['area']->code.' — ' : '').$row['area']->name) : 'Sin área asignada',
                        'pensums_count' => count($row['pensum_ids']),
                        'total_questions' => $row['total_questions'],
                        'total_sessions' => $totalS,
                        'completed_sessions' => $row['completed_sessions'],
                        'completion_percentage' => $completion,
                        'precision' => $prec,
                        'total_answered' => $totalAns,
                        'correct_answers' => $row['correct_answers'],
                    ];
                })->values()->sortBy('fullname')->values();

                if ($this->resumenAreaId) {
                    $areaProgress = $areaProgress->filter(fn ($ap) => in_array((int) $this->resumenAreaId, $ap->area_ids, true))->values();
                }

                $areaPerPage = 10;
                $areaCurrentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('areaProgressPage');
                $areaTotal = $areaProgress->count();
                $areaItems = $areaProgress->forPage($areaCurrentPage, $areaPerPage)->values();
                $areaProgress = new \Illuminate\Pagination\LengthAwarePaginator($areaItems, $areaTotal, $areaPerPage, $areaCurrentPage, ['path' => request()->url(), 'pageName' => 'areaProgressPage']);
            }

            // Sesiones recientes y distribución por tipo/dificultad (como en s2526 dashboard)
            $recentSessions = (clone $sessionQuery)->with(['estudiant', 'pensum'])->orderByDesc('iniciado_at')->limit(5)->get();
            $questionsByType = DiagQuestion::where('diag_main_id', $selected->id)->select('tipo_pregunta as type', DB::raw('count(*) as count'))->groupBy('tipo_pregunta')->get();
            $questionsByDifficulty = DiagQuestion::where('diag_main_id', $selected->id)->select('difficulty', DB::raw('count(*) as count'))->groupBy('difficulty')->get();

            // Display: si el campo directo es null, derivar de las preguntas
            $displayLapso = $selected->lapso;
            $displayPestudio = $selected->pestudio;
            $displayReferent = $selected->referent;

            if (! $displayPestudio && $questionPensumIds->isNotEmpty()) {
                $pestudioIds = Pensum::whereIn('id', $questionPensumIds)->pluck('pestudio_id')->unique()->filter()->values();
                if ($pestudioIds->count() === 1) {
                    $displayPestudio = Pestudio::find($pestudioIds->first());
                } elseif ($pestudioIds->count() > 1) {
                    $displayPestudio = null;
                }
            }
        }

        return view('livewire.planning.diagnostic.diag-main-viewer', [
            'diagMains' => $diagMains,
            'selected' => $selected,
            'completedSessions' => $completedSessions,
            'studentsEvaluated' => $studentsEvaluated,
            'displayPestudio' => $displayPestudio ?? null,
            'displayLapso' => $displayLapso ?? null,
            'displayReferent' => $displayReferent ?? null,
            'questionsCount' => $questionsCount,
            'sessionsCount' => $sessionsCount,
            'pensumsWithAnswersCount' => $pensumsWithAnswersCount,
            'questionsWithAnswersCount' => $questionsWithAnswersCount,
            'totalAnswersCount' => $totalAnswersCount,
            'completionRate' => $completionRate,
            'abandonRate' => $abandonRate,
            'precision' => $precision,
            'precisionCorrect' => $precisionCorrect,
            'precisionTotal' => $precisionTotal,
            'pensumProgress' => $pensumProgress,
            'gradoProgress' => $gradoProgress ?? collect(),
            'gradosForResumen' => $gradosForResumen ?? collect(),
            'gradosAnidados' => $gradosAnidados ?? collect(),
            'areasConocimientoOptions' => $areasConocimientoOptions ?? collect(),
            'areaProgress' => $areaProgress ?? collect(),
            'pestudiosForGradoResumen' => $pestudiosForGradoResumen ?? collect(),
            'recentSessions' => $recentSessions,
            'questionsByType' => $questionsByType,
            'questionsByDifficulty' => $questionsByDifficulty,
            'progressPestudios' => $progressPestudios,
            'progressGrados' => $progressGrados,
            'progressPensums' => $progressPensums,
        ]);
    }
}
