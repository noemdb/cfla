<?php

namespace App\Http\Livewire\Movile\Profesor\Activity;

use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Achievement;
use App\Models\app\Profesor\Activity;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateComponent extends Component
{
    use ActivityValidateTrait;

    public Activity $activity;
    public Achievement $achievement;
    public $image,$items;
    public $pevaluacions,$pevaluacion,$pevaluacion_id;
    public $modeIndex,$modeEdit;
    public $lapso,$profesor,$user;
    public $list_pevaluacions,$list_evaluacions,$list_comment;

    protected $listeners = [ 'showSwal','alertConfirm','alertQuestion','remove' ];

    public function updatedActivityPevaluacionId($value)
    {
        // $this->modeIndex = true;
        $this->resetErrorBag();
        // $this->resetPage();
        // $this->resetErrorBag();
    }

    // public function updated()
    // {
    //     $this->resetErrorBag();
    // }

    public function mount()
    {
        $this->activity = New Activity;
        $this->achievement = New Achievement;

        $this->list_comment = Activity::COLUMN_COMMENTS;

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
        return view('livewire.movile.profesor.activity.create-component');
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
            'activity.observations'=>'required|string',
            'activity.teaching'=>'nullable|string',
            'activity.learning'=>'nullable|string',
            'activity.description'=>'nullable|string',
        ]);        

        $this->activity->description = ($this->activity->description === '') ? null : $this->activity->description ;
        $this->activity->observations = ($this->activity->observations === '') ? null : $this->activity->observations ;
        $this->activity->references = ($this->activity->references === '') ? null : $this->activity->references ;
        
        $this->activity->save();

        $this->resetModel();
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwal($title,$html);

        $this->emit('updateListActivity');
        $this->emitUp('setModeDefault');
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
