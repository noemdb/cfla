<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;

class AnswerComponent extends Component
{
    public $competition,$question,$timeRemaining;

    public function mount($id) // ID de la competición
    {
        $this->updateQuestion($id);
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.answer-component');
    }

    public function updateQuestion($id) // ID de la competición
    {   
        $this->competition = DebateCompetition::findOrFail($id);     
        $this->question = DebateQuestion::ActiveCompetitionId($id); //dd($this->question);
        $this->timeRemaining = ($this->question) ? $this->question->timeRemaining : null ;
    }

    public function updateTimetimeRemaining()
    {
        $this->question = ($this->question) ? DebateQuestion::find($this->question->id) : null; //dd($this->question);
        $this->timeRemaining = ($this->question) ? $this->question->timeRemaining : null;
        if ($this->timeRemaining <= 0) {
            $this->dispatch('update-question-answer',id: $this->question->id);
        }
    }
}
