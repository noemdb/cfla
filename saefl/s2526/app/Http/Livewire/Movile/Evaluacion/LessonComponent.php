<?php

namespace App\Http\Livewire\Movile\Evaluacion;

use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LessonComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $lesson,$lesson_id,$observations;
    public $pevaluacion_id,$lapso_id,$profesor_id;
    public $list_comment,$list_comment_pevaluacion;
    public $list_pevaluacions,$list_lapsos,$list_profesor;
    public $modeIndex,$modeEdit;

    protected $listeners = [ 'showSwal','alertConfirm','alertQuestion' ];

    public function mount()
    {
        $user = User::find(Auth::id());
        $this->list_comment = Lesson::COLUMN_COMMENTS;
        
        $this->list_profesor = $user->list_evaluacion_profesors();
        $this->list_lapsos = Lapso::pluck('name','id');

        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->lesson = null;
        $this->lesson_id = null;
    }

    public function render()
    {
        $user = User::find(Auth::id());

        $this->list_pevaluacions = $user->list_evaluacion_pevaluacions($this->lapso_id,$this->profesor_id);

        $lessons = 
        Lesson::query()
        ->select('lessons.*','pevaluacions.id as pevaluacion_id')
        ->join('evaluacions', 'evaluacions.id', '=', 'lessons.evaluacion_id')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
        ->join('peducativos', 'peducativos.id', '=', 'area_conocimientos.peducativo_id')
        ->join('users', 'users.id', '=', 'peducativos.manager_id')
        ->join('lapsos', 'lapsos.id', '=', 'pevaluacions.lapso_id')

        ->where('users.id',$user->id)

        ->groupby('lessons.id');

        $lessons = ($this->pevaluacion_id) ? $lessons->where('pevaluacions.id',$this->pevaluacion_id) : $lessons ;

        $lessons = ($this->lapso_id) ? $lessons->where('lapsos.id',$this->lapso_id) : $lessons ;

        $lessons = ($this->profesor_id) ? $lessons->where('pevaluacions.profesor_id',$this->profesor_id) : $lessons ;

        $lessons = $lessons->paginate(5);

        return view('livewire.movile.evaluacion.lesson-component', compact('lessons'));
    }

    public function updatedLapsoId()
    {
        $this->pevaluacion_id = null;
    }

    public function updatedProfesorId()
    {
        $this->pevaluacion_id = null;
    }

    public function edit($id)
    {
        $lesson = Lesson::find($id);
        $this->lesson = ($lesson) ? $lesson : null;
        $this->lesson_id = ($lesson) ? $lesson->id : null;
        $this->observations = ($lesson) ? $lesson->observations : null;

        $this->modeIndex = false;
        $this->modeEdit = true;
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->lesson = null;
        $this->lesson_id = null;
        $this->observations = null;
    }

    public function save()
    {
        $lesson = Lesson::find($this->lesson_id);
        $lesson->observations = $this->observations;
        $lesson->save();

        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->lesson = null;
        $this->lesson_id = null;

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwal($title,$html);
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
