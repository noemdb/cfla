<?php

namespace App\Http\Livewire\Movile\Profesor\Activity;

use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Achievement;
use App\Models\app\Profesor\Activity;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListComponent extends Component
{
    // use LessonTrait;
    // use WithFileUploads;
    use WithPagination; 
    protected $paginationTheme = 'bootstrap';

    use ActivityValidateTrait;

    public Activity $activity;
    public Achievement $achievement;    

    public $image,$items;

    public $pevaluacions,$pevaluacion,$pevaluacion_id,$activity_id;

    public $modeIndex,$modeEdit,$modeCreatorAchievement;

    public $lapso,$profesor,$user,$enable_edit;

    public $list_pevaluacions,$list_evaluacions,$list_comment;

    public function setEditActivity($id)
    {
        $this->modeIndex = false;
        $this->modeEdit = true;
        $this->activity = Activity::findOrFail($id);
        $this->activity_id = $id;
    }

    public function saveActivity()
    {
        $this->validate([
            'activity.pevaluacion_id'=>'required|integer',
            'activity.finicial'=>'required|date',
            'activity.ffinal'=>'required|date',
            'activity.topic'=>'required|string',
            'activity.thematic'=>'required|string',
            'activity.references'=>'required|string',
            'activity.teaching'=>'nullable|string',
            'activity.learning'=>'nullable|string',
            'activity.observations'=>'required|string',
            'activity.description'=>'nullable|string',
        ]);

        $this->activity->description = ($this->activity->description === '') ? null : $this->activity->description ;
        $this->activity->observations = ($this->activity->observations === '') ? null : $this->activity->observations ;
        $this->activity->references = ($this->activity->references === '') ? null : $this->activity->references ;
        
        $this->activity->save();

        $this->resetModel();
        $this->activity_id = null;
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwal($title,$html);

        $this->closeActivity();
        $this->modeIndex = true;
    }

    protected $listeners = [ 'updateListActivity','showSwal','alertConfirm','alertQuestion','remove' ];
    public function updateListActivity()
    {
        $this->mount();
        $this->resetPage();
    }

    public function updatedPevaluacionId($value)
    {
        $this->closeActivity();
        $this->modeIndex = true;
        $this->resetPage();
    }

    public function mount()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->image = null;

        $this->activity = New Activity;
        $this->achievement = New Achievement;

        $this->list_comment = Activity::COLUMN_COMMENTS;

        $user = User::findOrFail(Auth::id());
        $profesor =  $user->profesor;

        $this->user = $user;
        $this->profesor = $profesor;
        $this->pevaluacions = ($profesor) ? $profesor->pevaluacions : null; 

        $this->lapso = Lapso::current();
        $this->enable_edit = $this->lapso->status_preclosing;
        $this->items = collect();
        $this->list_evaluacions = collect();
        $this->list_pevaluacions = $profesor->list_pevaluacions($this->lapso->id);
    }    

    public function render()
    {
        $activities = Activity::query()
        ->select('activities.*')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
        ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
        ->where('profesors.id', $this->profesor->id)
        ->where('pevaluacions.lapso_id', $this->lapso->id)
        ->groupBy('activities.id')
        ->orderBy('activities.created_at','desc');

        $activities = ($this->pevaluacion_id) ? $activities->where('pevaluacions.id',$this->pevaluacion_id) : $activities->where('activities.pevaluacion_id',null) ;

        $activities = $activities->paginate(5);

        return view('livewire.movile.profesor.activity.list-component', compact('activities'));
    }

    public function deleteActivity($id,$method=null)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        $this->resetModel();
        $this->activity_id = null;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'Registro fue eliminado exitosamente';
        $this->showSwal($title,$html);
    }

    public function alertQuestionActivity($id,$method)
    {
        $activity = Activity::findOrFail($id);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => 'Estas seguro que desea eliminar esta actividad? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir.',
            'id'=>$id,
            'method'=>$method,
            'toast'=>true,
            'position'=>'top-end',
        ]);
    }
    
    public function alertQuestionDefault($id,$method)
    {
        $activity = Activity::findOrFail($id);
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

    public function resetModel()
    {
        $this->activity = New Activity;
        $this->activity->finicial = null;
        $this->activity->ffinal = null;
        $this->activity->topic = null;
        $this->activity->thematic = null;
        $this->activity->references = null;
        $this->activity->teaching = null;
        $this->activity->learning = null;
        $this->activity->observations = null;
        $this->activity->description = null;

        $this->achievement = New Achievement;
        $this->achievement->name = null;
        $this->achievement->weighting = null;
    }

    public function closeActivity()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->modeCreatorAchievement = false;
    }

}
