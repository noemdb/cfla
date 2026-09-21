<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Seccion;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

/**
 * Wizard para importar actividades de otra sección del MISMO grado hacia el
 * área de formación (Pevaluacion) destino.
 *
 * Paso 1: elegir la sección origen (hermanas del grado del destino).
 * Paso 2: seleccionar una a una las actividades a importar.
 * Paso 3: vista previa y guardado (clona actividades + indicadores).
 */
class ActivityImportWizard extends Component
{
    use WireUiActions;

    public bool $showModal = false;

    /** 1 = sección · 2 = actividades · 3 = vista previa */
    public int $step = 1;

    public ?int $targetPevaluacionId = null;

    public ?int $sourceSeccionId = null;

    /** @var list<array{id:int,name:string,activities_count:int}> */
    public array $sections = [];

    /** @var list<array<string,mixed>> */
    public array $sourceActivities = [];

    /** @var list<int> */
    public array $selectedActivityIds = [];

    /** @var array<string,mixed> */
    public array $preview = [];

    #[On('openActivityImportWizard')]
    public function open($pevaluacionId): void
    {
        $pevaluacion = Pevaluacion::query()
            ->with(['pensum.asignatura', 'pensum.grado', 'seccion', 'lapso', 'grupoEstable'])
            ->find((int) $pevaluacionId);

        if (! $pevaluacion) {
            $this->notification()->error('Área no encontrada', 'No se pudo abrir el importador de actividades.');

            return;
        }

        $this->resetState();
        $this->targetPevaluacionId = (int) $pevaluacion->id;
        $this->loadSections();
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetState();
    }

    public function selectSection($seccionId): void
    {
        $seccionId = (int) $seccionId;
        $source = $this->sourcePevaluacion($seccionId);

        if (! $source) {
            $this->notification()->warning(
                'Sin carga académica',
                'No hay una Pevaluación de esta asignatura en la sección seleccionada.',
            );

            return;
        }

        $activities = $source->activities()
            ->withCount('achievements')
            ->orderBy('finicial')
            ->orderBy('id')
            ->get();

        if ($activities->isEmpty()) {
            $this->notification()->warning(
                'Sin actividades',
                'La sección seleccionada no tiene actividades para importar.',
            );

            return;
        }

        $this->sourceSeccionId = $seccionId;
        $this->sourceActivities = $activities->map(fn (Activity $activity): array => [
            'id' => (int) $activity->id,
            'topic' => (string) ($activity->topic ?: 'Sin tema generador'),
            'thematic' => (string) ($activity->thematic ?: ''),
            'finicial' => $activity->finicial ? (string) $activity->finicial : null,
            'ffinal' => $activity->ffinal ? (string) $activity->ffinal : null,
            'achievements_count' => (int) $activity->achievements_count,
            'status' => (bool) $activity->status,
        ])->all();

        // Se preseleccionan todas: el docente desmarca lo que no quiera.
        $this->selectedActivityIds = array_map(
            fn (array $activity): int => (int) $activity['id'],
            $this->sourceActivities,
        );
        $this->step = 2;
    }

    public function toggleAll(): void
    {
        $this->selectedActivityIds = count($this->selectedActivityIds) === count($this->sourceActivities)
            ? []
            : array_map(fn (array $activity): int => (int) $activity['id'], $this->sourceActivities);
    }

    public function goToPreview(): void
    {
        $selected = collect($this->sourceActivities)
            ->whereIn('id', array_map('intval', $this->selectedActivityIds))
            ->values();

        if ($selected->isEmpty()) {
            $this->notification()->warning('Sin selección', 'Selecciona al menos una actividad para importar.');

            return;
        }

        $target = $this->targetPevaluacion();
        $sourceSeccion = $this->sourceSeccionId ? Seccion::find($this->sourceSeccionId) : null;

        $this->preview = [
            'source_section' => $sourceSeccion?->name ?? '—',
            'activities' => $selected->all(),
            'activities_count' => $selected->count(),
            'achievements_count' => (int) $selected->sum('achievements_count'),
        ];
        $this->step = 3;
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function save(): void
    {
        $target = $this->targetPevaluacion();

        if (! $target) {
            $this->notification()->error('Área no encontrada', 'No se pudo guardar la importación.');

            return;
        }

        $activities = Activity::query()
            ->with('achievements')
            ->whereIn('id', array_map('intval', $this->selectedActivityIds))
            ->get();

        if ($activities->isEmpty()) {
            $this->notification()->warning('Sin selección', 'Selecciona al menos una actividad para importar.');

            return;
        }

        $createdActivities = 0;
        $createdAchievements = 0;

        DB::transaction(function () use ($target, $activities, &$createdActivities, &$createdAchievements): void {
            foreach ($activities as $activity) {
                $copy = $activity->replicate();
                $copy->pevaluacion_id = (int) $target->id;
                $copy->comments = null;
                $copy->save();
                $createdActivities++;

                foreach ($activity->achievements as $achievement) {
                    $achievementCopy = $achievement->replicate();
                    $achievementCopy->activity_id = (int) $copy->id;
                    $achievementCopy->save();
                    $createdAchievements++;
                }
            }
        });

        $sectionName = $target->seccion?->name ?? '—';
        $this->showModal = false;
        $this->resetState();

        $this->notification()->success(
            'Actividades importadas',
            "{$createdActivities} actividad(es) y {$createdAchievements} indicador(es) importados a la sección {$sectionName}.",
        );

        // Refresca el listado (conteos de actividades/indicadores).
        $this->dispatch('activity-imported');
    }

    private function resetState(): void
    {
        $this->step = 1;
        $this->sourceSeccionId = null;
        $this->sections = [];
        $this->sourceActivities = [];
        $this->selectedActivityIds = [];
        $this->preview = [];
    }

    private function targetPevaluacion(): ?Pevaluacion
    {
        if (! $this->targetPevaluacionId) {
            return null;
        }

        return Pevaluacion::query()
            ->with(['pensum.asignatura', 'pensum.grado', 'seccion', 'lapso', 'grupoEstable'])
            ->find($this->targetPevaluacionId);
    }

    /**
     * Pevaluación origen: misma asignatura (pensum), mismo lapso y misma
     * sección hermana. El grupo estable (componente de formación) forma parte
     * de la clave de la Pevaluación: si el destino es un componente se importa
     * del MISMO componente en la sección hermana; si el destino es de sección
     * completa (sin componente) solo se consideran Pevaluaciones sin grupo.
     */
    private function sourcePevaluacion(int $seccionId): ?Pevaluacion
    {
        $target = $this->targetPevaluacion();

        if (! $target) {
            return null;
        }

        return Pevaluacion::query()
            ->where('lapso_id', $target->lapso_id)
            ->where('pensum_id', $target->pensum_id)
            ->where('seccion_id', $seccionId)
            ->when(
                $target->grupo_estable_id,
                fn ($query) => $query->where('grupo_estable_id', $target->grupo_estable_id),
                fn ($query) => $query->whereNull('grupo_estable_id'),
            )
            ->withCount('activities')
            ->orderByDesc('activities_count')
            ->first();
    }

    private function loadSections(): void
    {
        $target = $this->targetPevaluacion();
        $gradoId = $target?->pensum?->grado_id;

        if (! $target || ! $gradoId) {
            $this->sections = [];

            return;
        }

        $this->sections = Seccion::query()
            ->where('grado_id', $gradoId)
            ->where('status_active', 'true')
            ->whereKeyNot($target->seccion_id)
            ->orderBy('name')
            ->get()
            ->map(function (Seccion $seccion): array {
                $source = $this->sourcePevaluacion((int) $seccion->id);

                return [
                    'id' => (int) $seccion->id,
                    'name' => (string) $seccion->name,
                    'activities_count' => (int) ($source?->activities_count ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    public function render()
    {
        $target = $this->targetPevaluacion();

        return view('livewire.profesor.activity.activity-import-wizard', [
            'target' => $target ? [
                'asignatura' => $target->pensum?->asignatura?->name ?? '—',
                'grado' => $target->pensum?->grado?->name ?? '—',
                'seccion' => $target->seccion?->name ?? '—',
                'lapso' => $target->lapso?->name ?? '—',
                'grupo_estable' => $target->grupoEstable?->name,
            ] : null,
        ]);
    }
}
