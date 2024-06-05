<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use Livewire\Component;

class DebateComponent extends Component
{
    public $competition,$debate;

    public function mount($id) // ID de la competición
    {
        $this->updateDebate($id);
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.debate-component');
    }

    public function updateDebate($id) // ID de la competición
    {   
        $this->competition = DebateCompetition::findOrFail($id);     
        $this->debate = Debate::where('competition_id',$id)->where('status_active',true)->first();
    }
}
