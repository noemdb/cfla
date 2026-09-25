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

        // Al visitar el listado, las `activity_created` pendientes se dan
        // por vistas (el badge baja sin clics manuales).
        if (auth()->id()) {
            app(\App\Services\NotificationService::class)->markTypeAsRead(auth()->id(), 'activity_created');
        }

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
        if ($this->statusActivities === 'SI') {
            $query->whereHas('pevaluacion.activities');
        } elseif ($this->statusActivities === 'NO') {
            $query->whereDoesntHave('pevaluacion.activities');
        } elseif ($this->statusActivities === 'SI_LE') {
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
            $query->where(fn ($q) => $q->where('status', 0)->orWhereNull('status'));
        } elseif ($this->filterStatus === 'approved') {
            $query->where('status', 1);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                    ->orWhere('thematic', 'like', "%{$this->search}%");
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
                type: 'pevaluacion_observation_updated',
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
}
