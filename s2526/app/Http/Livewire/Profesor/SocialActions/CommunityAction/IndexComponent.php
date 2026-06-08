<?php

namespace App\Http\Livewire\Profesor\SocialActions\CommunityAction;

use App\Models\app\SocialAction\CommunityAction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

use App\Models\app\Pescolar\Lapso;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    use WithFileUploads;

    use ValidateTrait;

    public CommunityAction $community_action;
    public $lapso,$profesor,$user,$grado_id;

    // public $area_conocimientos,$pevaluacion,$pevaluacion_id,$observations;
    // public $list_pevaluacions,$list_evaluacions;
    public $modeIndex,$modeCreate,$modeEdit,$modeShow,$modeShowImage;
    public $list_comment;
    public $list_status,$list_type;
    public $image,$allGrados,$lapsoActual;

    protected $listeners = [ 'updateCommunityAction','showSwal','alertConfirm','alertQuestion','remove' ];

    public function updateCommunityAction()
    {
        $this->mount();
    }

    public function mount()
    {
        $this->image = null;

        $this->community_action = New CommunityAction;
        $this->list_comment = CommunityAction::COLUMN_COMMENTS;
        $this->list_status  = CommunityAction::getStatus() ;
        $this->list_type  = CommunityAction::getTypes() ;
        $user = User::findOrFail(Auth::id());
        $this->profesor = $user->profesor ;
        $this->getAllGradosTutor();
        
        $grado = $this->profesor->getGradoForTutorLapso() ;
        $this->grado_id = ($grado) ? $grado->id : null ;

        $this->close();
        $this->modeShowImage = false;
        $this->lapsoActual = Lapso::current();
    }

    public function getAllGradosTutor()
    {
        if (!$this->profesor) {
            return collect();
        }
        $this->allGrados = $this->profesor->getAllGradosAsTutor($this->lapso?->id ?? null);
    }

    public function render()
    {
        $community_actions = CommunityAction::where('user_id',Auth::id())->get();
        return view('livewire.profesor.social-actions.community-action.index-component',compact(
                'community_actions',
            )
        );
    }

    public function create()
    {
        $this->image = null;
        $this->community_action = New CommunityAction;
        $this->modeIndex = false;
        $this->modeCreate = true;
        $this->modeEdit = false;
        $this->modeShow = false;
        $this->modeShowImage = false;
    }

    public function edit($id)
    {
        $community_action = CommunityAction::find($id);
        if ($community_action) {
            $this->community_action = $community_action;
            $this->modeIndex = false;
            $this->modeCreate = false;
            $this->modeEdit = true;
            $this->modeShow = false;
            $this->modeShowImage = false;
        }
    }

    public function updatedLapso()
    {
        $grado = $this->profesor->getGradoForTutorLapso();
        $this->grado_id = $grado ? $grado->id : null;
    }

    public function save()
    {        

        // Validar que grado_id esté presente
        if (!$this->grado_id) {
            $this->showSwal(
                '⚠️ Atención', 
                'Debes tener un grado asignado como tutor/guía para registrar esta acción.', 
                'warning'
            );
            return;
        }

        $this->validate(); 

        $this->community_action->user_id = Auth::id();
        $this->community_action->grado_id = $this->grado_id;
        $this->community_action->image = $this->uploadImage();
        $this->community_action->save();

        $this->community_action = New CommunityAction;
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado exitosamente';
		$this->showSwal($title,$html);

        $this->image = null;
        $this->close(); //dd($this->community_action);

        // $this->emit('updateCommunityHour');
        // $this->emitUp('setModeDefault');
    }

    public function showImagen($id)
    {
        $community_action = CommunityAction::find($id);
        if ($community_action) {
            $this->community_action = $community_action;
            $this->modeIndex = false;
            $this->modeCreate = false;
            $this->modeEdit = false;
            $this->modeShow = false;
            $this->modeShowImage = true;
        }
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeCreate = false;
        $this->modeEdit = false;
        $this->modeShow = false;
        $this->modeShowImage = false;
    }

    public function uploadImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:1024', // 1MB Max
        ]);
        return ($this->image) ? $this->image->store('images','social_accions') : $this->community_action->image;
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

    public function alertConfirm($id)
    {
        $community_action = CommunityAction::findOrFail($id);
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
        $community_action = CommunityAction::findOrFail($id);
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
        $community_action = CommunityAction::findOrFail($id);
        $community_action->delete();

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Se eliminó exitosamente';
		$this->showSwal($title,$html);
        // $this->emitUp('reRenderIndex');
    }
}
