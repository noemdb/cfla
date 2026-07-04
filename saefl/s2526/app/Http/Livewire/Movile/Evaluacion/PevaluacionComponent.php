<?php

namespace App\Http\Livewire\Movile\Evaluacion;

use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PevaluacionComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-5';

    public $pevaluacion,$evaluacions,$status_execution;
    public $modeIndex,$modeEdit;
    public $pevaluacion_id,$lapso_id,$profesor_id;
    public $list_comment,$list_lapsos,$list_profesor;

    protected $listeners = [ 'showSwal','alertConfirm','alertQuestion' ];

    public function mount()
    {
        $user = User::find(Auth::id());
        $this->list_comment = Pevaluacion::COLUMN_COMMENTS;
        $this->list_lapsos = Lapso::pluck('name','id');
        $this->list_profesor = $user->list_evaluacion_profesors();
        $lapso_current = Lapso::current();
        $this->lapso_id = ($lapso_current) ? $lapso_current->id : null ;

        $this->modeIndex = true;
        $this->modeEdit = false;
    }

    public function render()
    {
        $user = User::find(Auth::id());
        $pevaluacions = $user->getPevaluacions($this->lapso_id,$this->profesor_id,5);
        return view('livewire.movile.evaluacion.pevaluacion-component',compact('pevaluacions'));
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::find($id);
        $this->pevaluacion = ($pevaluacion) ? $pevaluacion : null;
        $this->pevaluacion_id = ($pevaluacion) ? $pevaluacion->id : null;
        $this->evaluacions = ($pevaluacion) ? $pevaluacion->evaluacions : null;

        $this->modeIndex = false;
        $this->modeEdit = true;
    }

    public function mark($evaluacion_id)
    {
        $evaluacion = Evaluacion::find($evaluacion_id);
        $evaluacion->status_execution = true;
        $evaluacion->save();

        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->pevaluacion = null;
        $this->pevaluacion_id = null;
        $this->evaluacions = null;

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwal($title,$html);
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->pevaluacion = null;
        $this->pevaluacion_id = null;
        $this->evaluacions = null;
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
    
}
