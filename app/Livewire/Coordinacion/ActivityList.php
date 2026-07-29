<?php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Profesor;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class ActivityList extends Component
{
    use WithPagination, WireUiActions, Concerns\HasCoordinacionScope;

    // ─── Filters ───────────────────────────────────────────────────
    public string $search = '';
    public $lapsoId = '';
    public $pestudioId = '';
    public $profesorId = '';
    public $gradoId = '';
    public $seccionId = '';
    public $statusActivities = '';
    public $filterObservations = false;
    public $filterRevision = false;
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
            ->whereHas('pensums', fn($q) => $q->whereIn('pestudio_id', $service->getPestudioIds()))
            ->orderBy('order')
            ->get()
            ->pluck('name', 'id');

        $this->listProfesores = Profesor::where('status_active', 'true')
            ->whereHas('pevaluacions.pensum', fn($q) => $q->whereIn('pestudio_id', $service->getPestudioIds()))
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
            'pevaluacion' => fn($q) => $q->with([
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
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }

        // Pestudio filter
        if ($this->pestudioId) {
            $query->whereHas('pevaluacion.pensum', fn($q) => $q->where('pestudio_id', $this->pestudioId));
        }

        // Profesor filter
        if ($this->profesorId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('profesor_id', $this->profesorId));
        }

        // Grado filter
        if ($this->gradoId) {
            $query->whereHas('pevaluacion.seccion', fn($q) => $q->where('grado_id', $this->gradoId));
        }

        // Seccion filter
        if ($this->seccionId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('seccion_id', $this->seccionId));
        }

        // Status activities filter
        if ($this->statusActivities === 'SI') {
            $query->whereHas('pevaluacion.activities');
        } elseif ($this->statusActivities === 'NO') {
            $query->whereDoesntHave('pevaluacion.activities');
        }

        // Observations filter
        if ($this->filterObservations) {
            $query->whereHas('pevaluacion', fn($q) => $q->whereNotNull('observations')->where('observations', '<>', ''));
        }

        // Revision filter
        if ($this->filterRevision) {
            $query->where(fn($q) => $q->where('status', 0)->orWhereNull('status'));
        }

        // Status filter
        if ($this->filterStatus === 'pending') {
            $query->where(fn($q) => $q->where('status', 0)->orWhereNull('status'));
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
                ->whereHas('pensums', fn($q) => $q->whereIn('pestudio_id', $this->getCoordinacionService()->getPestudioIds()))
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
        if (!$this->getCoordinacionService()->pevaluacionIsInScope($pevId)) {
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
        if (!$this->getCoordinacionService()->pevaluacionIsInScope($pev->id)) {
            abort(403);
        }

        $pev->update(['observations' => $this->observations ?: null]);
        $this->editingPevId = null;
        $this->observations = '';

        $this->dispatch('observations-saved');
        $this->notification()->success(
            title: 'Observaciones guardadas',
            description: 'Las observaciones se han actualizado correctamente.'
        );
    }

    public function selectLapso($id): void
    {
        $this->lapsoId = $id;
        $this->resetPage();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPestudioId() { $this->resetPage(); }
    public function updatingProfesorId() { $this->resetPage(); }
    public function updatingGradoId() { $this->resetPage(); }
    public function updatingSeccionId() { $this->resetPage(); }
    public function updatingStatusActivities() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
