<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;

class QuestionComponent extends Component
{
    public $competition,$question;

    public function mount($id) // ID de la competición
    {
        $this->updateQuestion($id);
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.question-component');
    }

    public function updateQuestion($id) // ID de la competición
    {   
        $this->competition = DebateCompetition::findOrFail($id);     
        $this->question = DebateQuestion::ActiveCompetitionId($id);
    }
}
