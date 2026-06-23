<?php

namespace App\Http\Livewire\Movile\Profesor\Learning;

use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LessonListComponent extends Component
{
    use LessonTrait;
    use WithFileUploads;
    use WithPagination;
 
    protected $paginationTheme = 'bootstrap';

    public Lesson $lesson;

    public $image,$items;

    public $pevaluacions,$pevaluacion,$pevaluacion_id;

    public $modeIndex,$modeEdit;

    public $lapso,$profesor,$user;

    public $list_pevaluacions,$list_evaluacions;

    protected $listeners = [ 'updateListLesson','showSwal','alertConfirm','alertQuestion','remove' ];

    public function mount()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->image = null;

        $this->lesson = New Lesson;

        $this->list_comment = Lesson::COLUMN_COMMENTS;

        $user = User::findOrFail(Auth::id());
        $profesor =  $user->profesor;

        $this->user = $user;
        $this->profesor = $profesor;
        $this->pevaluacions = ($profesor) ? $profesor->pevaluacions : null; 

        $this->lapso = Lapso::current();
        $this->items = collect();
        $this->list_evaluacions = collect();
        $this->list_pevaluacions = $profesor->list_pevaluacions($this->lapso->id);
    }

    public function updateListLesson()
    {
        $this->mount();
    }

    public function render()
    {
        $lessons = Lesson::query()
        ->select('lessons.*')
        ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->where('profesors.id', $this->profesor->id)
        ->orderBy('lessons.planned','desc');

        $lessons = ($this->pevaluacion_id) ? $lessons->where('pevaluacions.id',$this->pevaluacion_id) : $lessons ;

        $lessons = $lessons->paginate(5);

        return view('livewire.movile.profesor.learning.lesson-list-component', compact('lessons'));
    }

    public function updatedPevaluacionId($value)
    {
        if ($value) {
            $pevaluacion = Pevaluacion::find($value);
            $this->pevaluacion_id = ($pevaluacion) ? $pevaluacion->id : null;            
        } else {
            $this->pevaluacion_id = null;
        }
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
    }

    public function edit($id)
    {
        $this->lesson = Lesson::find($id);
        if ($this->lesson) {
            $this->pevaluacion = $this->lesson->pevaluacion;
            if ($this->pevaluacion) {
                $this->pevaluacion_id = $this->pevaluacion->id;
                $this->modeIndex = false;
                $this->modeEdit = true;
                $this->items = $this->pevaluacion->getLessonsForProfesor($this->profesor->id);
                $this->list_evaluacions = $this->profesor->list_evaluacions($this->pevaluacion->id);
                $this->resetValidation();
            }            
        }
    }

    public function saveLesson()
    {
        $this->validate();

        $this->uploadImage();

        $this->lesson->modified_by = Auth::id();
        $this->lesson->save();

        $this->lesson = New Lesson;
        $this->pevaluacion_id = null;
        $this->items = collect();
        $this->list_evaluacions = collect();
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Actualización realizada exitosamente';
		$this->showSwal($title,$html);

        $this->image = null;

        $this->close();
        $this->resetValidation();
    }

    public function uploadImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:1024',
        ]);
        $this->lesson->evidence = ($this->image) ? $this->image->store('images','lessons') : $this->lesson->evidence;
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

    public function alertConfirm($id)
    {
        $lesson = Lesson::findOrFail($id); //dd($lesson);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',  
            'message' => 'Estas seguro?', 
            'text' => 'Sí se elimina este registro, no lo podrá recuperar',
            'id'=>$id,
            'method'=>'remove',
            'toast'=>true,
            'position'=>'top-end',
        ]);
    }    
    
    public function alertQuestion($id,$method)
    {
        $lesson = Lesson::findOrFail($id);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => 'Estas seguro ? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir.',
            'id'=>$id,
            'method'=>$method,
            'toast'=>true,
            'position'=>'top-end',
        ]);
    }

    public function remove($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Se eliminó exitosamente';
		$this->showSwal($title,$html);
        // $this->emitUp('reRenderIndex');
    }
    
}
