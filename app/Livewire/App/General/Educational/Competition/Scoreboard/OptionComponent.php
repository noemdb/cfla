<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Attributes\On;
use Livewire\Component;

class OptionComponent extends Component
{
    public $literal, $debate, $grado, $seccions, $colors, $competition, $options, $question, $timeRemaining;
    public int $competition_id = 0;

    public function mount($id)
    {
        $this->competition_id = $id;
        $this->literal = ['A','B','C','D','E','F'];
        $this->colors  = ['primary','secondary','positive','negative','warning','info'];
        $this->refresh();
    }

    #[On('echo:competition.{competition_id},.question.activated')]
    #[On('echo:competition.{competition_id},.debate.activated')]
    #[On('echo:competition.{competition_id},.scoreboard.updated')]
    public function refresh(): void
    {
        $this->competition = DebateCompetition::findOrFail($this->competition_id);
        $this->options     = DebateOption::ActiveCompetitionId($this->competition_id);
        $this->question    = DebateQuestion::ActiveCompetitionId($this->competition_id);
        $this->debate      = Debate::ActiveCompetitionId($this->competition_id);

        $this->grado    = $this->debate ? $this->debate->grado   : collect();
        $this->seccions = $this->debate ? $this->debate->seccions : collect();
        $this->timeRemaining = $this->question ? $this->question->timeRemaining : null;
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.option-component');
    }
}
