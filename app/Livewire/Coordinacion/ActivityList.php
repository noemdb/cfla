<?php

namespace App\Livewire\Coordinacion;

use App\Livewire\Concerns\InteractsWithLmsLessons;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Notifications\PevaluacionObservationNotification;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class ActivityList extends Component
{
    use Concerns\HasCoordinacionScope, InteractsWithLmsLessons, WireUiActions, WithPagination;

    // ─── Filters ───────────────────────────────────────────────────
    public string $search = '';

    public $lapsoId = '';

    public $pestudioId = '';

    public $profesorId = '';

    public $gradoId = '';

    public $seccionId = '';

    public $statusActivities = '';

    public $filterObservations = false;

    public $filterStatus = '';

    public $paginate = 15;

    protected $paginationTheme = 'tailwind';

    // ─── Select lists ──────────────────────────────────────────────
    public $listPestudio;

    public $listGrado = [];

    public $listSeccion = [];

    public $listProfesores = [];

    public $listLapso;

    public $lapsos;

    // ─── Edición de observaciones ──────────────────────────────────
    public ?int $editingPevId = null;

    public string $observations = '';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
        $service = $this->getCoordinacionService();

        $this->listPestudio = Pestudio::whereIn('id', $service->getPestudioIds())
            ->where('status_active', 'true')
            ->orderBy('order')
            ->get()
            ->pluck('name', 'id');

        $this->listGrado = Grado::where('status_active', 'true')
            ->whereHas('pensums', fn ($q) => $q->whereIn('pestudio_id', $service->getPestudioIds()))
            ->orderBy('order')
            ->get()
            ->pluck('name', 'id');

        $this->listProfesores = Profesor::where('status_active', 'true')
            ->whereHas('pevaluacions.pensum', fn ($q) => $q->whereIn('pestudio_id', $service->getPestudioIds()))
            ->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->listLapso = Lapso::orderBy('finicial', 'desc')
            ->pluck('name', 'id');

        $this->lapsos = Lapso::orderBy('finicial')
            ->orderBy('id')
            ->get();

        // Default to current lapso
        $lapsoCurrent = Lapso::current();
        $this->lapsoId = $lapsoCurrent?->id ?? '';
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Activity::with([
            'lmsPublication',
            'lmsSections',
            'pevaluacion' => fn ($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
        ]);

        $query = $service->scopeActivities($query);

        // Solo actividades cuyo pensum esté activo.
        $query->whereHas('pevaluacion.pensum', fn ($q) => $q->where('pensums.status_active', true));

        // Lapso filter
        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('lapso_id', $this->lapsoId));
        }

        // Pestudio filter
        if ($this->pestudioId) {
            $query->whereHas('pevaluacion.pensum', fn ($q) => $q->where('pestudio_id', $this->pestudioId));
        }

        // Profesor filter
        if ($this->profesorId) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('profesor_id', $this->profesorId));
        }

        // Grado filter
        if ($this->gradoId) {
            $query->whereHas('pevaluacion.seccion', fn ($q) => $q->where('grado_id', $this->gradoId));
        }

        // Seccion filter
        if ($this->seccionId) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('seccion_id', $this->seccionId));
        }

        // Status activities filter
        if ($this->statusActivities === 'SI_LE') {
            $query->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                ->whereNotNull('lms_activity_publications.id');
        } elseif ($this->statusActivities === 'NO_LE') {
            $query->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                ->whereNull('lms_activity_publications.id');
        }

        // Observations filter
        if ($this->filterObservations) {
            $query->whereHas('pevaluacion', fn ($q) => $q->whereNotNull('observations')->where('observations', '<>', ''));
        }

        // Status filter
        if ($this->filterStatus === 'pending') {
            $query->where(fn ($q) => $q->where('activities.status', 0)->orWhereNull('activities.status'));
        } elseif ($this->filterStatus === 'approved') {
            $query->where('activities.status', 1);
        }

        // Search
        if ($this->search) {
            $term = trim((string) $this->search);
            $query->where(function ($q) use ($term) {
                $q->where('topic', 'like', "%{$term}%")
                    ->orWhere('thematic', 'like', "%{$term}%")
                    ->orWhereHas('pevaluacion.profesor', fn ($q) => $q
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('lastname', 'like', "%{$term}%"))
                    ->orWhereHas('pevaluacion.pensum.asignatura', fn ($q) => $q
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('code', 'like', "%{$term}%"))
                    ->orWhereHas('pevaluacion.seccion', fn ($q) => $q->where('name', 'like', "%{$term}%"));
            });
        }

        $activities = $query->orderBy('activities.created_at', 'desc')
            ->paginate($this->paginate);

        return view('livewire.coordinacion.activity-list', [
            'activities' => $activities,
            'lapsos' => $this->lapsos,
        ])->layout('coordinacion.layouts.app');
    }

    // ─── Cascading selects ────────────────────────────────────────

    public function updatedPestudioId($value)
    {
        $this->gradoId = null;
        $this->seccionId = null;

        if ($value) {
            $this->listGrado = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('order')
                ->get()
                ->pluck('name', 'id');
        } else {
            $this->listGrado = Grado::where('status_active', 'true')
                ->whereHas('pensums', fn ($q) => $q->whereIn('pestudio_id', $this->getCoordinacionService()->getPestudioIds()))
                ->orderBy('order')
                ->get()
                ->pluck('name', 'id');
        }

        $this->resetPage();
    }

    public function updatedGradoId($value)
    {
        $this->seccionId = null;

        if ($value) {
            $this->listSeccion = Seccion::where('grado_id', $value)
                ->where('status_active', true)
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id');
        } else {
            $this->listSeccion = [];
        }

        $this->resetPage();
    }

    // ─── Edición de observaciones ────────────────────────────────

    public function editObservations(int $pevId): void
    {
        $pev = Pevaluacion::findOrFail($pevId);
        if (! $this->getCoordinacionService()->pevaluacionIsInScope($pevId)) {
            abort(403);
        }
        $this->editingPevId = $pevId;
        $this->observations = $pev->observations ?? '';
    }

    public function cancelEdit(): void
    {
        $this->editingPevId = null;
        $this->observations = '';
    }

    public function saveObservations(): void
    {
        $this->validate(['observations' => 'nullable|string|max:2000']);

        $pev = Pevaluacion::findOrFail($this->editingPevId);
        if (! $this->getCoordinacionService()->pevaluacionIsInScope($pev->id)) {
            abort(403);
        }

        $originalObservations = $pev->observations;
        $newValue = $this->observations ?: null;
        $isNew = filled(trim((string) $newValue));
        $wasChanged = trim((string) $originalObservations) !== trim((string) $newValue);

        $pev->update(['observations' => $newValue]);
        $action = filled(trim((string) $originalObservations)) ? 'actualizó' : 'registró';

        if ($wasChanged && $isNew) {
            $this->notifyPlanners($pev->fresh(), $action);
        }

        $this->editingPevId = null;
        $this->observations = '';

        $this->dispatch('observations-saved');
        $this->notification()->success(
            title: 'Observaciones guardadas',
            description: 'Las observaciones se han actualizado correctamente.'
        );
    }

    private function notifyPlanners(Pevaluacion $pev, string $action): void
    {
        $planners = User::where('is_planner', true)->where('is_active', 'enable')->get();

        // Anti-spam: si planificación ya tiene sin leer un aviso de esta
        // pevaluacion (registro o actualización) no se repite al guardar
        // varias veces seguidas la misma observación.
        $type = $action === 'registró'
            ? 'pevaluacion_observation_registered'
            : 'pevaluacion_observation_updated';

        $subject = ['pevaluacion_id' => (int) $pev->id];

        $planners = app(NotificationService::class)->filterByDedupe(
            $planners,
            $type,
            $subject
        );

        if ($planners->isEmpty()) {
            return;
        }

        $pev->loadMissing(['pensum.pestudio', 'pensum.asignatura', 'profesor', 'seccion.grado', 'lapso']);

        $pestudioName = $pev->pensum?->pestudio?->name;
        $asignaturaName = $pev->pensum?->asignatura?->name;
        $seccionName = $pev->seccion?->name;
        $gradoName = $pev->seccion?->grado?->name;
        $profesorName = trim(($pev->profesor?->lastname ?? '').' '.($pev->profesor?->name ?? ''));
        $lapsoName = $pev->lapso?->name;
        $preview = \Illuminate\Support\Str::limit(trim((string) $pev->observations), 80);

        $context = collect([$pestudioName, $asignaturaName, $gradoName ? $gradoName.' · '.$seccionName : $seccionName, $profesorName])->filter()->implode(' · ');
        $message = 'Coordinación '.$action.' la observación'.($context ? ' en '.$context : '').($preview ? ': "'.$preview.'"' : '.');
        if ($lapsoName) {
            $message .= ' ('.$lapsoName.')';
        }

        app(NotificationService::class)->notifyUsers(
            $planners,
            new PevaluacionObservationNotification(
                type: $type,
                message: $message,
                url: route('app.planning.pevaluacions.index'),
                pevaluacionId: (int) $pev->id,
                pestudioName: $pestudioName,
                asignaturaName: $asignaturaName,
                seccionName: $seccionName,
                profesorName: $profesorName ?: null,
                lapsoName: $lapsoName,
                observationPreview: $preview ?: null,
                action: $action,
            ),
            // Huella de idempotencia: esta observación + esta acción. Dos
            // guardados seguidos del mismo tipo los cubre el anti-spam; un
            // reintento del mismo guardado, la BD.
            ['pevaluacion_id' => (int) $pev->id, 'action' => $action]
        );
    }

    public function selectLapso($id): void
    {
        $this->lapsoId = $id;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLapsoId()
    {
        $this->resetPage();
    }

    public function updatingPestudioId()
    {
        $this->resetPage();
    }

    public function updatingProfesorId()
    {
        $this->resetPage();
    }

    public function updatingGradoId()
    {
        $this->resetPage();
    }

    public function updatingSeccionId()
    {
        $this->resetPage();
    }

    public function updatingStatusActivities()
    {
        $this->resetPage();
    }

    public function updatingFilterObservations()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedPaginate()
    {
        $this->resetPage();
    }

    /**
     * Restaura filtros persistidos en localStorage en un único round-trip.
     */
    public function restoreFilters(array $filters): void
    {
        $this->search = trim((string) ($filters['search'] ?? ''));
        $this->statusActivities = in_array($filters['statusActivities'] ?? '', ['SI_LE', 'NO_LE'], true)
            ? $filters['statusActivities']
            : '';
        $this->filterStatus = in_array($filters['filterStatus'] ?? '', ['pending', 'approved'], true)
            ? $filters['filterStatus']
            : '';
        $this->filterObservations = (bool) ($filters['filterObservations'] ?? false);
        $this->paginate = in_array((int) ($filters['paginate'] ?? 15), [15, 25, 50, 100], true)
            ? (int) $filters['paginate']
            : 15;

        $lapso = Lapso::find($filters['lapsoId'] ?? null);
        $this->lapsoId = $lapso?->id ?? Lapso::current()?->id ?? '';

        $this->pestudioId = $filters['pestudioId'] ?? '';
        $this->listGrado = $this->buildGradoOptions($this->pestudioId);

        $this->gradoId = $filters['gradoId'] ?? '';
        $this->listSeccion = $this->gradoId
            ? Seccion::where('grado_id', $this->gradoId)
                ->where('status_active', true)
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id')
            : [];

        $this->seccionId = $filters['seccionId'] ?? '';
        $this->profesorId = $filters['profesorId'] ?? '';

        $this->resetPage();
    }

    /**
     * Limpia todos los filtros y vuelve al lapso actual.
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search', 'pestudioId', 'profesorId', 'gradoId', 'seccionId',
            'statusActivities', 'filterObservations', 'filterStatus',
        ]);

        $this->paginate = 15;
        $this->lapsoId = Lapso::current()?->id ?? '';
        $this->listGrado = $this->buildGradoOptions();
        $this->listSeccion = [];
        $this->resetPage();
    }

    private function buildGradoOptions(?string $pestudioId = null)
    {
        return Grado::where('status_active', 'true')
            ->when($pestudioId, fn ($q) => $q->where('pestudio_id', $pestudioId))
            ->whereHas('pensums', fn ($q) => $q->whereIn('pestudio_id', $this->getCoordinacionService()->getPestudioIds()))
            ->orderBy('order')
            ->get()
            ->pluck('name', 'id');
    }
}
