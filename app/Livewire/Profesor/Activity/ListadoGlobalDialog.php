<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Profesor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ListadoGlobalDialog extends Component
{
    /** @var bool Controla la visibilidad del modal */
    public $showModal = false;

    // ─── FILTROS ───────────────────────────────────────────────

    /** @var string Búsqueda por texto libre */
    public $search = '';

    /** @var string|null Filtro por lapso */
    public $lapso_id = '';

    // ─── ORDENAMIENTO ──────────────────────────────────────────

    /** @var string Campo de ordenamiento */
    public $sortField = 'activities.finicial';

    /** @var string Dirección del ordenamiento */
    public $sortDir = 'asc';

    // ─── PAGINACIÓN MANUAL ─────────────────────────────────────

    public $perPage = 20;
    public $page = 1;
    public $total = 0;
    public $lastPage = 1;
    public $from = 0;
    public $to = 0;

    // ─── DATOS ─────────────────────────────────────────────────

    public $activities = [];
    public $lapsos = [];

    // ─── MÉTODOS PRINCIPALES ───────────────────────────────────

    #[On('openListadoGlobal')]
    public function open()
    {
        $this->resetFilters();
        $this->loadLapsos();
        $this->page = 1;
        $this->loadActivities();
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
    }

    // ─── RESET ─────────────────────────────────────────────────

    public function resetFilters()
    {
        $this->search = '';
        $this->lapso_id = '';
        $this->sortField = 'activities.finicial';
        $this->sortDir = 'asc';
        $this->page = 1;
    }

    // ─── FILTROS ───────────────────────────────────────────────

    public function updatedSearch()
    {
        $this->page = 1;
        $this->loadActivities();
    }

    public function updatedLapsoId()
    {
        $this->page = 1;
        $this->loadActivities();
    }

    // ─── ORDENAMIENTO ──────────────────────────────────────────

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
        $this->page = 1;
        $this->loadActivities();
    }

    // ─── PAGINACIÓN ────────────────────────────────────────────

    public function gotoPage($page)
    {
        $this->page = max(1, min((int) $page, $this->lastPage));
        $this->loadActivities();
    }

    // ─── CARGAR DATOS ──────────────────────────────────────────

    protected function getProfesorId()
    {
        $profesor = Profesor::where('user_id', Auth::id())->first();
        return $profesor?->id;
    }

    public function loadLapsos()
    {
        $profesorId = $this->getProfesorId();
        if (!$profesorId) {
            $this->lapsos = [];
            return;
        }

        $this->lapsos = DB::table('lapsos')
            ->join('pevaluacions', 'pevaluacions.lapso_id', '=', 'lapsos.id')
            ->where('pevaluacions.profesor_id', $profesorId)
            ->distinct()
            ->orderBy('lapsos.name')
            ->pluck('lapsos.name', 'lapsos.id')
            ->toArray();
    }

    public function loadActivities()
    {
        $profesorId = $this->getProfesorId();
        if (!$profesorId) {
            $this->activities = [];
            $this->total = 0;
            $this->lastPage = 1;
            return;
        }

        $query = DB::table('activities')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->join('asignaturas', 'pensums.asignatura_id', '=', 'asignaturas.id')
            ->join('grados', 'pensums.grado_id', '=', 'grados.id')
            ->join('seccions', 'pevaluacions.seccion_id', '=', 'seccions.id')
            ->join('lapsos', 'pevaluacions.lapso_id', '=', 'lapsos.id')
            ->where('pevaluacions.profesor_id', $profesorId)
            ->select([
                'activities.id',
                'activities.pevaluacion_id',
                'activities.finicial',
                'activities.ffinal',
                'activities.topic',
                'activities.thematic',
                'activities.references',
                'activities.teaching',
                'activities.learning',
                'activities.description',
                'activities.observations',
                'activities.comments',
                'activities.status',
                'asignaturas.name as asignatura_name',
                'grados.name as grado_name',
                'seccions.name as seccion_name',
                'lapsos.name as lapso_name',
            ]);

        // ── Filtro de búsqueda ──
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('activities.topic', 'like', $searchTerm)
                  ->orWhere('activities.thematic', 'like', $searchTerm)
                  ->orWhere('activities.teaching', 'like', $searchTerm)
                  ->orWhere('activities.learning', 'like', $searchTerm)
                  ->orWhere('activities.description', 'like', $searchTerm)
                  ->orWhere('asignaturas.name', 'like', $searchTerm);
            });
        }

        // ── Filtro por lapso ──
        if ($this->lapso_id) {
            $query->where('pevaluacions.lapso_id', $this->lapso_id);
        }

        // ── Total y paginación manual ──
        $total = $query->count();
        $this->total = $total;
        $this->lastPage = max(1, (int) ceil($total / $this->perPage));
        $this->page = min($this->page, $this->lastPage);

        $this->from = ($this->page - 1) * $this->perPage + 1;
        $this->from = min($this->from, $total);
        $this->to = min($this->from + $this->perPage - 1, $total);

        // ── Ordenamiento ──
        $query->orderBy($this->sortField, $this->sortDir);

        // ── Obtener datos ──
        $items = $query
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();

        $this->activities = $items;
    }

    // ─── RENDER ────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.profesor.activity.listado-global-dialog');
    }
}
