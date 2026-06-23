<?php

namespace App\Http\Livewire\Leader;

use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ActivityComponent extends Component
{
    public $search,$name;
    public $list_grado,$list_seccion;
    public $pestudio_id,$list_lapso;

    public $pevaluacions;
    public $area_conocimientos,$area_active;
    public $user_id,$grado_id,$seccion_id,$lapso_id;
    public $activity,$comments,$status;

    public $modeCreator,$modeComments,$modeIndex,$modeObservation,$modeCommentsObservations;

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id) ? Seccion::list_seccion_grado($this->grado_id) : Array() ;
    }

    public function mount()
    {
        $this->modeIndex = true;
        $this->user_id = Auth::id();
        $user = User::findOrFail($this->user_id);        
        $this->area_conocimientos = AreaConocimiento::where('leader_id',$user->id)->get();
        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $this->area_active = "CONOCIMIENTO";
    }

    public function render()
    {
        $name = $this->name;
        $search = $this->search;

        $pevaluacions =
        Pevaluacion::select('pevaluacions.*')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
        ->join('grados', 'grados.id', '=', 'seccions.grado_id')
        ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

        ->where('area_conocimientos.leader_id',$this->user_id)
        ->wherenull('pensums.deleted_at')
        ->wherenull('pevaluacions.deleted_at')
        ->groupBy('pevaluacions.id')

        ->where('asignaturas.name', 'like', '%'.$search.'%')
        ->where( function($query) use ($name) {
            $query->where('profesors.name', 'like', "%".$name."%")
            ->orWhere('profesors.lastname', 'like', "%".$name."%")
            ->orWhere('profesors.ci_profesor','like','%'.$name.'%');
        });


        $pevaluacions = ($this->grado_id) ? $pevaluacions->where('grados.id',$this->grado_id) : $pevaluacions ;
        $pevaluacions = ($this->seccion_id) ? $pevaluacions->where('seccions.id',$this->seccion_id) : $pevaluacions ;
        $pevaluacions = ($this->lapso_id) ? $pevaluacions->where('lapsos.id',$this->lapso_id) : $pevaluacions ;

        // dd($this->grado_id);

        $this->pevaluacions = $pevaluacions->get();

        return view('livewire.leader.activity-component');
    }

    public function createComent($id)
    {
        $this->activity = Activity::findOrFail($id); 
        $this->comments = $this->activity->comments;
        $this->status = $this->activity->status;
        $this->close();
        $this->modeComments = true;       
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

    public function close()
    {
        $this->modeCreator = false;
        $this->modeComments = false;
        $this->modeIndex = false;
        $this->modeObservation = false;
    }
}
