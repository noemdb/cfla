<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;

class OptionComponent extends Component
{

    public $literal, $debate, $grado, $seccions, $colors, $competition, $options, $question, $timeRemaining;

    public function mount($id) // ID de la competición
    {
        $this->updateOptions($id);

        $this->literal = ['A', 'B', 'C', 'D', 'E', 'F'];
        $this->colors = ['primary', 'secondary', 'positive', 'negative', 'warning', 'info'];
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.option-component');
    }

    public function updateOptions($id) // ID de la competición
    {
        $this->competition = DebateCompetition::findOrFail($id);
        $this->question = DebateQuestion::ActiveCompetitionId($id);
        $this->options = ($this->question) ? $this->question->options : collect();
        $this->debate = Debate::ActiveCompetitionId($id);

        $this->grado = ($this->debate) ? $this->debate->grado : collect();
        $this->seccions = ($this->debate) ? $this->debate->seccions : collect();
    }
    public function updateTimetimeRemaining()
    {
        $this->timeRemaining = ($this->question) ? $this->question->timeRemaining : null;
    }
}
