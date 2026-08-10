<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class PevaluacionList extends Component
{
    use WithPagination;

    public $lapsoId = null;

    #[Url]
    public $pestudio_id = null;

    #[Url]
    public $grado_id = null;

    #[Url]
    public $seccion_id = null;

    #[Url]
    public $status_activities = null;

    #[Url]
    public $filter_status = null;

    public $filter_observations = false;

    public $sort = 'pevaluacions.created_at';
    public $direction = 'desc';

    protected $profesor;

    public function mount()
    {
        $profesorModel = Profesor::where('user_id', Auth::user()->id)->first();
        $this->profesor = $profesorModel;

        if (!$this->lapsoId) {
            $this->lapsoId = Lapso::current()?->id;
        }
    }

    public function updatingLapsoId()
    {
        $this->resetPage();
    }

    public function updatingPestudioId()
    {
        $this->resetPage();
        $this->grado_id = null;
        $this->seccion_id = null;
    }

    public function updatingGradoId()
    {
        $this->resetPage();
        $this->seccion_id = null;
    }

    public function updatingStatusActivities()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterObservations()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['pestudio_id', 'grado_id', 'seccion_id', 'status_activities', 'filter_status', 'filter_observations']);
        $this->resetPage();
    }

    public function render()
    {
        $profesor = $this->profesor ?? Profesor::where('user_id', Auth::user()->id)->first();

        // ── Pevaluacions query ──
        $allowedSorts = [
            'asignaturas.name', 'grados.name', 'lapsos.name',
            'lapsos.finicial', 'pevaluacions.created_at',
        ];
        $sort = in_array($this->sort, $allowedSorts) ? $this->sort : 'pevaluacions.created_at';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        $pevaluacionsQuery = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('asignaturas', 'pensums.asignatura_id', '=', 'asignaturas.id')
            ->join('grados', 'pensums.grado_id', '=', 'grados.id')
            ->join('lapsos', 'pevaluacions.lapso_id', '=', 'lapsos.id')
            ->where('pevaluacions.profesor_id', $profesor->id)
            ->where('pestudios.planning_module', true)
            ->where('pestudios.status_active', 'true');

        if ($this->pestudio_id) {
            $pevaluacionsQuery->where('pensums.pestudio_id', $this->pestudio_id);
        }
        if ($this->grado_id) {
            $pevaluacionsQuery->where('pensums.grado_id', $this->grado_id);
        }
        if ($this->seccion_id) {
            $pevaluacionsQuery->where('pevaluacions.seccion_id', $this->seccion_id);
        }
        if ($this->lapsoId) {
            $pevaluacionsQuery->where('pevaluacions.lapso_id', $this->lapsoId);
        }

        $pevaluacionsQuery->withCount([
            'activities',
            'activities as activities_lessons_count' => fn ($q) => $q->whereHas('lmsPublication'),
        ]);

        if ($this->status_activities === 'SI') {
            $pevaluacionsQuery->having('activities_count', '>', 0);
        } elseif ($this->status_activities === 'NO') {
            $pevaluacionsQuery->having('activities_count', '=', 0);
        } elseif ($this->status_activities === 'SI_LE') {
            $pevaluacionsQuery->having('activities_lessons_count', '>', 0);
        } elseif ($this->status_activities === 'NO_LE') {
            $pevaluacionsQuery->having('activities_lessons_count', '=', 0);
        }

        if ($this->filter_status === 'pending') {
            $pevaluacionsQuery->whereHas('activities', fn ($q) => $q->where('status', 0));
        } elseif ($this->filter_status === 'approved') {
            $pevaluacionsQuery->has('activities')
                ->whereDoesntHave('activities', fn ($q) => $q->where('status', 0));
        }

        if ($this->filter_observations) {
            $pevaluacionsQuery->whereNotNull('pevaluacions.observations')
                ->where('pevaluacions.observations', '!=', '');
        }

        $pevaluacions = $pevaluacionsQuery->with([
            'activities.achievements', 'pensum.asignatura',
            'pensum.grado.pestudio', 'seccion', 'lapso', 'grupoEstable',
        ])->orderBy($sort, $direction)->paginate(10);

        // ── Filter lists ──
        $list_pestudio = Pestudio::where('planning_module', true)
            ->where('status_active', 'true')
            ->whereHas('pensums.pevaluacions', function ($q) use ($profesor) {
                $q->where('profesor_id', $profesor->id);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        $grados = Grado::whereHas('pensums.pevaluacions', function ($q) use ($profesor) {
            $q->where('profesor_id', $profesor->id);
        })->when($this->pestudio_id, function ($q) {
            $q->where('pestudio_id', $this->pestudio_id);
        })->get();
        $list_grado = $grados->pluck('name', 'id');

        $list_seccion = $this->grado_id
            ? Seccion::where('grado_id', $this->grado_id)->pluck('name', 'id')
            : collect();

        $lapsos = Lapso::orderBy('name', 'asc')->get();
        $lapso_active = Lapso::find($this->lapsoId) ?? Lapso::current();

        return view('livewire.profesor.activity.pevaluacion-list', [
            'pevaluacions' => $pevaluacions,
            'list_pestudio' => $list_pestudio,
            'list_grado' => $list_grado,
            'list_seccion' => $list_seccion,
            'lapsos' => $lapsos,
            'lapso_active' => $lapso_active,
        ]);
    }
}
