<?php

namespace App\Http\Livewire\Profesor\SocialActions\CommunityHour;

use App\Models\app\Estudiant;
use App\Models\app\SocialAction\CommunityAction;
use App\Models\app\SocialAction\CommunityHour;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //use ValidateTrait;

    public $community_hour;
    public $lapso,$profesor,$estudiants,$user,$grado_id,$community_action,$community_action_id,$hours;
    public $asistent,$duration;
    public $asistents,$paginates;

    public $modeIndex,$modeCreate,$modeEdit,$modeShow;
    public $list_comment;
    public $list_community_actions,$list_status,$list_type;

    public $posts;
 
    protected $rules = [
        'posts.*.title' => 'required|string|min:6',
        'posts.*.body' => 'required|string|max:500',
        'asistents.*.id' => 'required|integer',
        'asistents.*.duration' => 'required|integer',
    ];

    protected $listeners = [ 'updateCommunityHour','showSwalCommunityHour','alertConfirmCommunityHour','alertQuestionCommunityHour','removeCommunityHour' ];

    public function mount()
    {        
        $this->community_action_id = null;
        $this->list_comment = CommunityHour::COLUMN_COMMENTS;
        $this->list_community_actions = CommunityAction::list_community_actions();

        $user = User::findOrFail(Auth::id());
        $this->profesor = $user->profesor ;        

        $this->community_hour = collect();
        $this->asistents = collect();
        $this->estudiants = collect();

        $this->close();
    }

    public function render()
    {
        $community_hours = CommunityHour::query()
            ->select('community_hours.*')
            ->join('community_actions', 'community_actions.id', '=', 'community_hours.community_action_id')
            ->leftjoin('estudiants', 'estudiants.id', '=', 'community_hours.estudiant_id')
            ->where('community_actions.user_id',Auth::id())
            ->where('community_actions.id',$this->community_action_id)
            ->orderBy('estudiants.ci_estudiant','asc')
            ;     

        $community_hours = $community_hours->paginate();

        return view('livewire.profesor.social-actions.community-hour.index-component',compact(
            'community_hours',
        ));
    }

    public function updatedCommunityActionId($value)
    {
        $this->asistents = collect();
        $this->estudiants = collect();
        $this->community_hour = collect();
        $this->duration = null;

        $this->getAsistents($value);

        $this->resetValidation();
    }

    public function getAsistents($id)
    {
        $community_action = CommunityAction::find($id);
        $this->asistents = collect();
        $this->estudiants = collect();
        if ($community_action) {                
            $estudiants = $community_action->estudiants; 
            $this->duration =  $community_action->duration;
            $this->estudiants = $estudiants;
            foreach ($estudiants as $estudiant) {
                // $duration = ($community_action->type == "group") ? $this->duration : null;
                $community_hour = CommunityHour::where('estudiant_id',$estudiant->id)->where('community_action_id',$community_action->id)->first();
                $duration = ($community_hour) ? $community_hour->duration : null;
                $data = [
                    'estudiant_id'=>$estudiant->id,
                    'duration'=> $duration,
                ];
                $this->asistents->push($data);
            }
        }
    }

    public function save()
    { 
        if ($this->asistents->isNotEmpty()) {
            $asistents = $this->asistents;
            $community_hour = [];
            foreach ($asistents as $index => $asistent) {
                if ($asistent['estudiant_id'] && $asistent['duration'] && $this->community_action_id) {
                    $arr = [
                        'user_id'=>Auth::id(),
                        'estudiant_id'=>$asistent['estudiant_id'],
                        'community_action_id'=>$this->community_action_id,
                        'date'=>Carbon::now()->format('Y-m-d'),
                        'duration'=>$asistent['duration'],
                    ];
                    $community_hour = CommunityHour::where('estudiant_id',$asistent['estudiant_id'])->where('community_action_id',$this->community_action_id)->first();
                    if ($community_hour) {
                        $community_hour->fill($arr);
                        $community_hour->save();
                    } else {
                        $community_hour[] = CommunityHour::create($arr);
                    }
                }
            }
        }

        $this->asistent = [];
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwalCommunityHour($title,$html);

        $this->close();

        // $this->emit('updateListLesson');
        // $this->emitUp('updateCommunityHour');
    }

    public function updateCommunityHour()
    {
        $this->mount();
    }    

    public function create()
    {
        $this->community_hour = collect();
        $id = $this->community_action_id;
        $this->getAsistents($id);
        $this->modeIndex = false;
        $this->modeCreate = true;
        $this->modeEdit = false;
        $this->modeShow = false;
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeCreate = false;
        $this->modeEdit = false;
        $this->modeShow = false;
    }

    public function showSwalCommunityHour($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
        ]);
    }

    public function alertConfirmCommunityHour($id)
    {
        $community_action = CommunityHour::findOrFail($id);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',  
            'message' => 'Estas seguro?', 
            'text' => 'Sí se elimina este registro, no lo podrá recuperar',
            'id'=>$id,
            'method'=>'removeCommunityHour',
            'toast'=>true,
            'position'=>'top-end',
        ]);
    }    
    
    public function alertQuestionCommunityHour($id,$method)
    {
        $community_action = CommunityHour::findOrFail($id);
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

    public function removeCommunityHour($id)
    {
        $community_action = CommunityHour::findOrFail($id);
        $community_action->delete();

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Se eliminó exitosamente';
		$this->showSwalCommunityHour($title,$html);
        // $this->emitUp('reRenderIndex');

        $id = $this->community_action_id;
        $this->getAsistents($id);
    }
}
