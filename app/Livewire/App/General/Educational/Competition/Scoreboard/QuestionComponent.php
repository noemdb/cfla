<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Attributes\On;
use Livewire\Component;

class QuestionComponent extends Component
{
    public $competition, $question, $competition_id;

    public function mount($id)
    {
        $this->competition_id = $id;
        $this->refresh();
    }

    #[On('echo:competition.{competition_id},.question.activated')]
    #[On('echo:competition.{competition_id},.debate.activated')]
    public function refresh(): void
    {
        $this->competition = DebateCompetition::findOrFail($this->competition_id);
        $this->question = DebateQuestion::ActiveCompetitionId($this->competition_id);
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.question-component');
    }
}
