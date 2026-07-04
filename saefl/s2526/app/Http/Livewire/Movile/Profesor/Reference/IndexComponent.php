<?php

namespace App\Http\Livewire\Movile\Profesor\Reference;

use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public Evaluacion $evaluacion;
    use ReferenceTrait;

    public $user_id;
    public $profesor,$lapsos,$lapso_active,$evaluacion_id,$estudiants,$pevaluacion,$pevaluacion_id,$pevaluacions;
    public $modeMain,$modeLoad;
    public $estudiant,$estudiant_id,$list_estudiants;
    public $index;
    public $list_pevaluacions,$list_comment;    
    public $nota;

    protected $listeners = [ 'showSwal','alertConfirm','alertQuestion' ];

    public function mount($pevaluacions)
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $fecha = Carbon::Now();
        $this->evaluacion = New Evaluacion;
        
        $this->index = 0;
        $this->pevaluacion = null;
        $this->pevaluacion_id = null;
        $this->pevaluacions = collect();
        $this->list_pevaluacions = collect();
        // $this->evaluacions = collect();

        if ($pevaluacions->isNotEmpty()) {
            $this->pevaluacions = $pevaluacions;
            $this->list_pevaluacions = $pevaluacions->pluck('grado_asignatura_name','id'); //dd($this->list_pevaluacions);
        }        
        
        $this->list_comment = Evaluacion::COLUMN_COMMENTS;

        $this->modeMain = true;
        $this->modeLoad = false;
        $this->profesor = $user->profesor;
        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::WhereDate('finicial','<=', $fecha)->WhereDate('ffinal','>=', $fecha)->first();
        // $this->lapso_active = Lapso::first();

    }

    public function render()
    {
        return view('livewire.movile.profesor.reference.index-component');
    }

    public function updatedEvaluacionPevaluacionId($value)
    {
        $pevaluacion = ($value) ? Pevaluacion::find($value): null ;
        $this->pevaluacion = ($pevaluacion) ? $pevaluacion: null ;
        $this->pevaluacion_id = ($pevaluacion) ? $pevaluacion->id: null ;
        
        $this->resetValidation();
    }

    public function saveReferent()
    {
        $this->validate();

        $this->evaluacion->escala_id = $this->pevaluacion->escala->id; //dd($this->pevaluacion);

        $this->evaluacion->save();

        $this->evaluacion = New Evaluacion;
        
        $this->pevaluacion = Pevaluacion::find($this->pevaluacion_id); //dd($this->pevaluacion);

        $this->evaluacion->pevaluacion_id = $this->pevaluacion->id; 
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwal($title,$html);

        //dd($this->pevaluacion);

        // $this->emit('updateListLesson');
        // $this->emitUp('setModeDefault');
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
            'toast'=>true,
            'position'=>'top-end',
        ]);
    }

    public function delete($id)
    {
        $evaluacion = Evaluacion::find($id);
        if ($evaluacion) {
            $evaluacion->delete();
            $title = '¡Excelente, buen trabajo! ';
            $html = 'Registro eliminado exitosamente';
            $this->showSwal($title,$html);
            $this->pevaluacion = Pevaluacion::find($this->pevaluacion_id);
        }
        
    }
}
