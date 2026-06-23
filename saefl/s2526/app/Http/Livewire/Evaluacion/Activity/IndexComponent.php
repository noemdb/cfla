<?php

namespace App\Http\Livewire\Evaluacion\Activity;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';

    // public $pevaluacions;
    public $activity, $objetivo, $comments, $status, $pevaluacion, $observations, $pestudios;
    public $modeCreator, $modeEdit, $modeIndex, $modeObservation, $modeEditObservations, $statusAtivities;
    public $user_id, $manager_id, $grado_id, $seccion_id, $lapso_id, $profesor_id;
    public $search, $paginate = 10, $name;
    public $list_grado, $list_seccion;
    public $pestudio_id, $list_pestudio, $list_lapso, $list_profesor;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingName()
    {
        $this->resetPage();
    }
    public function updatingPestudioId()
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
    public function updatingLapsoId()
    {
        $this->resetPage();
    }
    public function updatingPaginate()
    {
        $this->resetPage();
    }

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id) ? Seccion::list_seccion_grado($this->grado_id) : array();
    }

    public function updatedPestudioId($value)
    {
        $this->list_grado = ($value) ? Grado::list_pestudio_grado($value) : array();
        $this->list_profesor = ($value) ? Profesor::list_profesors_pestudio($value) : array();
    }

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $this->user_id = $user->id;
        $this->manager_id = $user->id;

        $this->close();
        $this->modeIndex = true;

        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_pestudio = Pestudio::getPestudios($user->id)->SortBy('id')->pluck('name', 'id'); //dd($this->list_pestudio);
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $this->list_seccion = array();
        $this->list_profesor = array();
        $this->paginate = 10;
    }

    public function render()
    {
        $name = $this->name;
        $search = $this->search;
        $user_id = $this->user_id;

        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
            ->leftjoin('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('seccions', function ($join) {
                $join->on('seccions.id', '=', 'pevaluacions.seccion_id')
                    ->where('seccions.status_active', 'true');
            })

            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where(
                function ($query) use ($user_id) {
                    $query->orWhere('peducativos.manager_id', $user_id)
                        ->orWhere('peducativos.assistant_id', $user_id)
                        ->orWhere('peducativos.deputy_id', $user_id)
                    ;
                }
            )
            ->where('peducativos.status_active', 'true')
            ->where('pestudios.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->groupBy('pevaluacions.id');
        $pevaluacions = ($this->profesor_id) ? $pevaluacions->where('profesors.id', $this->profesor_id) : $pevaluacions;
        $pevaluacions = ($this->seccion_id) ? $pevaluacions->where('seccions.id', $this->seccion_id) : $pevaluacions;
        $pevaluacions = ($this->grado_id) ? $pevaluacions->where('grados.id', $this->grado_id) : $pevaluacions;
        $pevaluacions = ($this->lapso_id) ? $pevaluacions->where('lapsos.id', $this->lapso_id) : $pevaluacions;
        $pevaluacions = ($this->pestudio_id) ? $pevaluacions->where('pestudios.id', $this->pestudio_id) : $pevaluacions;
        $pevaluacions = ($this->statusAtivities == 'NO') ? $pevaluacions->whereNull('activities.id') : $pevaluacions;
        $pevaluacions = ($this->statusAtivities == 'SI') ? $pevaluacions->whereNotNull('activities.id') : $pevaluacions;

        $pevaluacions = $pevaluacions->paginate($this->paginate);

        return view('livewire.evaluacion.activity.index-component', [
            'pevaluacions' => $pevaluacions,
        ]);
    }

    public function close()
    {
        $this->modeCreator = false;
        $this->modeEdit = false;
        $this->modeIndex = false;
        $this->modeObservation = false;
    }

    public function createObservation($id)
    {
        $this->pevaluacion = Pevaluacion::findOrFail($id); //dd($this->pevaluacion);
        $this->observations = ($this->pevaluacion) ? $this->pevaluacion->observations : null;
        $this->close();
        $this->modeObservation = true;
    }

    public function saveObservation()
    {
        $this->pevaluacion->observations = $this->observations;
        $this->pevaluacion->save();
        $this->pevaluacion = null;
        $this->close();
        $this->modeIndex = true;
    }

    public function createComent($id)
    {
        $this->activity = Activity::findOrFail($id);
        $this->comments = $this->activity->comments;
        $this->close();
        $this->modeCreator = true;
    }

    public function saveComent()
    {
        $this->activity->comments = $this->comments;
        $this->activity->status = $this->status;
        $this->activity->save();
        $this->activity = null;
        $this->close();
        $this->modeIndex = true;
    }

    public function editObservation($id)
    {
        $this->pevaluacion = Pevaluacion::findOrFail($id); //dd($this->pevaluacion);
        $this->observations = $this->pevaluacion->observations;
        $this->close();
        $this->modeObservation = true;
    }
}
