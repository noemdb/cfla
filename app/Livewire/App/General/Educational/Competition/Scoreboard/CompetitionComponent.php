<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\DebateCompetition;
use Livewire\Component;

class CompetitionComponent extends Component
{
    public $token, $competition;

    public function mount($id)
    {        
        // $this->competition = DebateCompetition::where('id',$id)->where('status_active',true)->first();
        $this->competition = DebateCompetition::where('id',$id)->first();
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.competition-component');
    }

    public function updateCompetition($id) // ID de la competición
    {   
        $this->competition = DebateCompetition::findOrFail($id);
    }

}
