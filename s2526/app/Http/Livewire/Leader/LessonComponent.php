<?php

namespace App\Http\Livewire\Leader;

use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\AreaConocimiento;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LessonComponent extends Component
{
    // use LessonTrait;

    public $lapso,$profesor,$user;

    public Lesson $lesson;
    public $area_conocimientos,$pevaluacion,$pevaluacion_id,$observations;
    public $list_pevaluacions,$list_evaluacions;
    public $modeIndex,$modeEdit,$modeShow,$modeShowImage;
    public $list_comment;

    protected $rules = [
        'lesson.observations' => 'required|string',
    ];

    protected $listeners = [ 'showSwal','alertConfirm','alertQuestion','remove' ];

    public function mount()
    {
        $this->lesson = New Lesson;
        $this->list_comment = Lesson::COLUMN_COMMENTS;

        $this->user = User::findOrFail(Auth::id());

        $this->area_conocimientos = AreaConocimiento::where('leader_id',$this->user->id)->get();

        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->modeShow = false;
    }

    public function render()
    {
        // $lessons = Lesson::all();
        $lessons = Lesson::getForLeaderId($this->user->id);
        return view('livewire.leader.lesson-component',compact('lessons'));
    }

    public function edit($id)
    {
        $lesson = Lesson::find($id);
        if ($lesson) {
            $pevaluacion =  $lesson->pevaluacion;
            $this->lesson = $lesson;
            $this->pevaluacion = $pevaluacion;

            $this->modeIndex = false;
            $this->modeEdit = true;
            $this->modeShow = false;
            $this->modeShowImage = false;
        }
    }

    public function showImagen($id)
    {
        $lesson = Lesson::find($id);
        if ($lesson) {
            $pevaluacion =  $lesson->pevaluacion;
            $this->lesson = $lesson;
            $this->pevaluacion = $pevaluacion;

            $this->modeIndex = false;
            $this->modeEdit = false;
            $this->modeShow = false;
            $this->modeShowImage = true;
        }
    }

    public function saveLesson()
    {
        // $this->lesson->observations = Auth::user()->username . ': '.$this->lesson->observations;
        $this->lesson->modified_by = Auth::id();

        $this->validate();

        $this->lesson->finished = ($this->lesson->status) ? Carbon::now()->format('Y-m-d') : null;
        
        $this->lesson->save();

        $this->lesson = New Lesson;
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Actualización realizada exitosamente';
		$this->showSwal($title,$html);

        $this->close();
        $this->resetValidation();

        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->modeShow = false;
        $this->modeShowImage = false;
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->modeShow = false;
        $this->modeShowImage = false;
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
