<?php

namespace App\Livewire\App\General\Educational\Competition\Dashboard;

use App\Models\app\Educational\Debate;
use Livewire\Component;
use Livewire\Attributes\On;

class DebateComponent extends Component
{

    public $competition_id,$debates,$active_id;

    #[On('grado-active')] 
    public function updateDebatesList($id)
    {
        $this->debates = Debate::query();
        $this->debates = ($this->competition_id) ? Debate::where('competition_id',$this->competition_id) : $this->debates;
        $this->debates = ($id) ? $this->debates->where('grado_id',$id) : $this->debates ;
        $this->debates = $this->debates->get();
        $this->active_id = null;      
    }
    
    public function mount($competition_id)
    {
        $this->debates = Debate::where('competition_id',$competition_id)->get();
        $this->competition_id = $competition_id;
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.dashboard.debate-component');
    }

    public function active($id)
    {
        $debate = Debate::find($id); //dd($debate);
        $this->active_id = ($debate) ? $debate->id : null ;
        $this->dispatch('detabe-active',id: $id);
    }

    public function loadActive()
    {
        $this->active_id = ($this->debates->isNotEmpty()) ? $this->debates->first()->id : null ; //dd($this->active_id);
        // $this->dispatch('debate-active',id: $this->active_id);
    }
}
