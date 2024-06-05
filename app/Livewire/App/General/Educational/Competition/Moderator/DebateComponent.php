<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.debate-component

use App\Models\app\Academy\Grado;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class DebateComponent extends Component
{
    public $competition_id,$debates,$debate,$active_id,$grado_id,$question;

    #[On('grado-active')] 
    public function updateDebatesList($id)
    {
        $grado = Grado::findOrFail($id);
        $this->grado_id = $grado->id; 
        $this->debates = Debate::query(); 
        $this->debates = ($this->competition_id) ? Debate::where('competition_id',$this->competition_id)->where('grado_id',$grado->id) : $this->debates;
        $this->debates = $this->debates->get();
        $this->loadActive();
    }

    #[On('debate-online')] 
    public function updateDebateOnline($id)
    {
        $this->debate = Debate::findOrFail($id);
    }

    public function loadActive()
    {
        $debate =  Debate::where('status_active',true)->first();
        $this->debate = ($debate) ? $debate : null;
        $this->active_id = ($debate) ? $debate->id : null;       
    }
    
    public function mount($competition_id)
    {
        $this->debates = Debate::where('competition_id',$competition_id)->where('grado_id',$this->grado_id)->get();
        $this->competition_id = $competition_id;
        $this->loadActive();
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.moderator.debate-component');
    }

    public function active($id)
    {
        $this->debate = Debate::findOrFail($id);
        $this->active_id = $this->debate->id ;
        $this->dispatch('detabe-active',id: $id);
    }

    public function setOnline($id)
    {
        $this->debate = Debate::setActive($id);
        $this->active_id = $this->debate->id ;
    }

    public function setOffline($id)
    {
        $this->debate = Debate::setDesActive($id);
        $this->active_id = null;
    }
    
}
