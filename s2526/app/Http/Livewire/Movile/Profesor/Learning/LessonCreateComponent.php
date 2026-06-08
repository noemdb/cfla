<?php

namespace App\Http\Livewire\Movile\Profesor\Learning;

use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class LessonCreateComponent extends Component
{
    use LessonTrait;
    use WithFileUploads;

    public Lesson $lesson;
    public $pevaluacions,$pevaluacion_id;
    public $list_pevaluacions,$list_evaluacions;
    public $image;

    protected $listeners = [ 'showSwal','alertConfirm','alertQuestion' ];

    public $lapso,$profesor,$user,$items;

    public function mount()
    {
        $this->lesson = New Lesson;
        $this->image = null;
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

    public function render()
    {
        return view('livewire.movile.profesor.learning.lesson-create-component');
    }

    public function saveLesson()
    {
        $this->validate();

        $this->uploadImage();

        $this->lesson->author_id = Auth::id();
        $this->lesson->finished = ($this->lesson->status) ? Carbon::now()->format('Y-m-d') : null;
        $this->lesson->save();

        $this->lesson = New Lesson;
        $this->pevaluacion_id = null;
        $this->items = collect();
        $this->list_evaluacions = collect();
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwal($title,$html);

        $this->image = null;

        $this->emit('updateListLesson');
        $this->emitUp('setModeDefault');
    }

    public function uploadImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:1024', // 1MB Max
        ]);
        $this->lesson->evidence = ($this->image) ? $this->image->store('images','lessons') : $this->lesson->evidence;
    }

    public function updatedPevaluacionId($value)
    {
        $this->lesson = new Lesson;
        if ($value) {
            $pevaluacion = Pevaluacion::find($value);
            $this->items = $pevaluacion->getLessonsForProfesor($this->profesor->id); //dd($this->list_evaluacions);
            $this->list_evaluacions = $this->profesor->list_evaluacions($value); //dd($this->list_evaluacions);
            $this->lesson->order = $this->items->count() + 1; //dd($this->lesson->order);
        } else {
            $this->items = collect();
            $this->list_evaluacions = collect();
        }
        $this->resetValidation();
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
