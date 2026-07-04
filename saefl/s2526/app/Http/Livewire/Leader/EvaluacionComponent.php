<?php

namespace App\Http\Livewire\Leader;

use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EvaluacionComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $evaluacion,$evaluacion_id,$fecha,$description;
    public $user_id;
    public $modeIndex,$modeEdit,$modeShow;

    public $search,$name;
    public $list_grado,$list_seccion;
    public $grado_id,$seccion_id,$lapso_id,$pestudio_id,$list_lapso,$area_active;

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id) ? Seccion::list_seccion_grado($this->grado_id) : Array() ;
    }

    public function mount()
    {
        $user = User::find(Auth::id());
        $this->user_id = $user->id; 
        
        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $this->area_active = "CONOCIMIENTO";

        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->modeShow = false;
    }

    public function render()
    {
        $name = $this->name;
        $search = $this->search;

        $evaluacions =
        Evaluacion::select('evaluacions.*')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
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
        ->wherenull('evaluacions.deleted_at')
        ->wherenull('pevaluacions.deleted_at')
        ->groupBy('evaluacions.id')

        ->where('asignaturas.name', 'like', '%'.$search.'%')
        ->where( function($query) use ($name) {
            $query->where('profesors.name', 'like', "%".$name."%")
            ->orWhere('profesors.lastname', 'like', "%".$name."%")
            ->orWhere('profesors.ci_profesor','like','%'.$name.'%');
        });


        $evaluacions = ($this->grado_id) ? $evaluacions->where('grados.id',$this->grado_id) : $evaluacions ;
        $evaluacions = ($this->seccion_id) ? $evaluacions->where('seccions.id',$this->seccion_id) : $evaluacions ;
        $evaluacions = ($this->lapso_id) ? $evaluacions->where('lapsos.id',$this->lapso_id) : $evaluacions ;

        $evaluacions = $evaluacions->paginate(10);

        return view('livewire.leader.evaluacion-component', [
            'evaluacions' => $evaluacions,
        ]);
    }

    public function edit($id)
    {
        $evaluacion = Evaluacion::find($id);
        if ($evaluacion) {
            $this->evaluacion = $evaluacion;
            $this->evaluacion_id = $evaluacion->id;
            $this->fecha = $evaluacion->fecha;
            $this->description = $evaluacion->description;
            $this->modeIndex = false;
            $this->modeEdit = true;
            $this->modeShow = false;
        }
    }

    public function save()
    {
        $this->evaluacion->fecha = $this->fecha;
        $this->evaluacion->description = $this->description;
        $this->evaluacion->save();
        $this->close();
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Actualización realizada exitosamente';
		$this->showSwal($title,$html);
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->evaluacion = false;
        $this->evaluacion_id = false;
        $this->modeEdit = false;
        $this->modeShow = false;
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
            // 'toast'=>true,
            // 'position'=>'top-end',
        ]);
    }

}
