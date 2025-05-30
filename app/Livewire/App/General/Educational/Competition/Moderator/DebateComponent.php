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
    public $competition_id,$debates,$debate,$active_id,$grado,$grado_id,$question;

    #[On('grado-active')] 
    public function updateDebatesList($id)
    {
        $this->grado = Grado::findOrFail($id);
        $this->grado_id = $this->grado->id;
        $this->loadDebate($this->competition_id);
        $this->loadActive();
    }

    #[On('debate-online')] 
    public function updateDebateOnline($id)
    {
        // $this->debate = Debate::findOrFail($id);
        $this->debate = Debate::find($id) ?? new Debate();
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
        // $this->debates = $this->debates->sortByDesc('status_active');
        $this->debates = $this->debates->sort();
        return view('livewire.app.general.educational.competition.moderator.debate-component');
    }

    public function active($id)
    {
        // $this->debate = Debate::findOrFail($id);
        $this->debate = Debate::find($id) ?? new Debate();
        $this->active_id = ($this->debate) ? $this->debate->id : null;        
        $this->dispatch('detabe-active',id: $id);
    }

    public function activeOnline($id)
    {
        $this->active($id);
        $this->setOnline($id);
        $this->loadDebate($this->competition_id);
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

    public function loadDebate($competition_id)
    {
        $this->debates = Debate::query(); 
        $this->debates = ($competition_id) ? Debate::where('competition_id',$competition_id)->where('grado_id',$this->grado->id) : $this->debates;
        $this->debates = $this->debates->get(); 
    }
    
}
