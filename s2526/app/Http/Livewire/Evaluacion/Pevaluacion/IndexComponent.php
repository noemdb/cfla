<?php

namespace App\Http\Livewire\Evaluacion\Pevaluacion;

use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Escala;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';
    public $search = '',$paginate = 10, $name;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingName() { $this->resetPage(); }
    public function updatingPestudioId() { $this->resetPage(); }
    public function updatingGradoId() { $this->resetPage(); }
    public function updatingSeccionId() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    public $activity,$evaluacions,$objetivo,$comments,$status,$observations,$select_id,$profesor_name;
    public $pestudio_id, $list_pestudio;

    public $modeIndex,$modeAssign,$modeEdit;

    public $manager_id,$pevaluacion_id,$profesor_id,$grado_id,$seccion_id,$lapso_id,$pensum_id,$grupo_estable_id,$description;
    public $list_grado,$list_seccion,$list_lapso,$list_profesors,$list_profesor,$list_pensum,$list_grupo_estable,$tipo_list,$baremo_apply_list;
    public $user_id,$pestudios,$escala_list;
    public $list_comment;

    public function updatedGradoId($value)
    {
        if ($value) {
            $grado = Grado::find($value);
            if ($grado) {
                $this->grado_id = $grado->id;            
                $this->list_seccion = Seccion::list_seccion_grado($this->grado_id); //dd($this->list_seccion);
                $this->list_pensum  = Pensum::list_pestudio_pensum_manage($this->grado_id,$this->manager_id);         
            }
        } else {
            $this->grado_id = null;
            $this->list_seccion = collect();
        }
    }

    public function updatedPestudioId($value)
    {
        $this->list_grado = ($value) ? Grado::list_pestudio_grado($value) : Array() ;
        $this->list_profesor = ($value) ? Profesor::list_profesors_pestudio($value) : Array() ;
    }

    public function updatedProfesorName($value)
    {
        $this->setProfesorLists($value);
    }

    public function setProfesorLists($value = null)
    {
        $profesors = Profesor::orderby('profesors.lastname','asc')
        ->select('profesors.id')
        ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
        ->orderby('profesors.lastname','asc')
        ->where('profesors.status_active','true');        
        $profesors = ($value) ? $profesors->where('profesors.name','like','%'.$value.'%')->orWhere('profesors.lastname','like','%'.$value.'%') : $profesors ;
        $this->list_profesors = $profesors->pluck('profesor_fullname', 'id');
    }

    function mount()
    {
        $user = User::find(Auth::id());
        $this->user_id = $user->id;
        $this->manager_id = $user->id;
        $this->pestudios = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->get();

        //$this->list_grado = Grado::list_pestudio_grado_manage($this->manager_id);
        $this->list_grado = Pestudio::list_pestudio_grado_manage($this->manager_id); //dd($this->list_grado);
        $this->list_seccion = collect();
        $this->list_pensum    = Pensum::list_pestudio_pensum_manage($this->grado_id,$this->manager_id);
        // $this->list_pestudio = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->pluck('name','id');
        $this->list_pestudio = Peducativo::list_pestudios($user->id); //dd($this->list_pestudio);
        $this->setProfesorLists();
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $this->list_grupo_estable = GrupoEstable::list_grupo_estable_full();
        $this->tipo_list = Pevaluacion::pevalaucion_tipo_list();
        $this->baremo_apply_list = Baremo::baremo_apply_list();
        $this->escala_list = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $this->list_comment = Pevaluacion::COLUMN_COMMENTS;

        $this->list_profesor = Array();

        $this->evaluacions = collect();

        $this->close();

    }

    public function save()
    { 
        $validate = Pevaluacion::where('lapso_id',$this->lapso_id)->where('seccion_id',$this->seccion_id)->where('pensum_id',$this->pensum_id)->first(); //dd($validate);
        
        if ($this->modeAssign && $validate ) {  
            $this->addError('unique', 'Esta asignatura ya cuenta con un plan de evaluación');
        } else {

            $data = $this->validate([
                'grado_id'=>'required|integer',
                'profesor_id'=>'required|integer',
                'lapso_id'=>'required|integer',
                'seccion_id'=>'required|integer',
                'pensum_id'=>'required|integer',
                'description'=>'nullable|string',
                'grupo_estable_id'=>'nullable|integer',
            ]);
    
            $pevaluacion = ($this->pevaluacion_id) ? $pevaluacion = Pevaluacion::findOrFail($this->pevaluacion_id) : $pevaluacion = New Pevaluacion ; //dd($pevaluacion);
    
            $pevaluacion->fill($data);
            $pevaluacion->save();
            
            $title = '¡Excelente, buen trabajo! ';
            $html = 'Registro realizado exitosamente';
            $this->showSwal($title,$html);    
            $this->close();
        }
        
        
    }

    public function render()
    {
        $name = $this->name;
        $search = $this->search;
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
        ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('grados', 'grados.id', '=', 'pensums.grado_id')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
        ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')        
        ->where('peducativos.manager_id',$this->manager_id)

        // ->where('asignaturas.name', 'like', '%'.$search.'%')
        // ->where( function($query) use ($name) {
        //     $query->where('profesors.name', 'like', "%".$name."%")
        //     ->orWhere('profesors.lastname', 'like', "%".$name."%")
        //     ->orWhere('profesors.ci_profesor','like','%'.$name.'%');
        // })
        ;

        $pevaluacions = ($this->seccion_id) ? $pevaluacions->where('seccions.id',$this->seccion_id) : $pevaluacions ;
        $pevaluacions = ($this->grado_id) ? $pevaluacions->where('grados.id',$this->grado_id) : $pevaluacions ;
        $pevaluacions = ($this->lapso_id) ? $pevaluacions->where('lapsos.id',$this->lapso_id) : $pevaluacions ;
        $pevaluacions = ($this->pestudio_id) ? $pevaluacions->where('pestudios.id',$this->pestudio_id) : $pevaluacions ;
        $pevaluacions = ($this->profesor_id) ? $pevaluacions->where('profesors.id',$this->profesor_id) : $pevaluacions ;
        
        $pevaluacions = $pevaluacions->paginate($this->paginate); //dd($pevaluacions);

        return view('livewire.evaluacion.pevaluacion.index-component',[
            'pevaluacions'=>$pevaluacions
        ]);
    }

    public function setAssign()
    {
        $this->close();
        $this->modeAssign = true;
        $this->profesor_name = null; 
        $this->setProfesorLists();      
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        $this->pevaluacion_id = $pevaluacion->id;
        $grado = $pevaluacion->grado;
        $this->grado_id = $grado->id;            
        $this->list_seccion = Seccion::list_seccion_grado($this->grado_id);

        $this->profesor_id = $pevaluacion->profesor_id;
        $this->lapso_id = $pevaluacion->lapso_id;
        $this->seccion_id = $pevaluacion->seccion_id;
        $this->pensum_id = $pevaluacion->pensum_id;
        $this->grupo_estable_id = $pevaluacion->grupo_estable_id;
        $this->description = $pevaluacion->description;

        $this->modeIndex = true;
        $this->modeAssign = false;
        $this->modeEdit = true;
        $this->profesor_name = null;
        $this->setProfesorLists();
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeAssign = false;
        $this->modeEdit = false;
        $this->search = null;
        $this->seccion_id = null;
        $this->grado_id = null;
        $this->lapso_id = null;
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
        ]);
    }

    public function delete($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        if ($pevaluacion) {
            $pevaluacion->delete();
            $this->close();
            $this->pevaluacion_id = null;
            $title = '¡Excelente, buen trabajo! ';
            $html = 'Registro fue eliminado exitosamente';
            $this->showSwal($title,$html);
        }
    }
}
