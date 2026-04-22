<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\DebateCompetition;
use Livewire\Attributes\On;
use Livewire\Component;

class CompetitionComponent extends Component
{
    public $token, $competition, $competition_id;

    public function mount($id)
    {
        $this->competition_id = $id;
        $this->competition = DebateCompetition::where('id', $id)->first();
    }

    #[On('echo:competition.{competition_id},.debate.activated')]
    #[On('echo:competition.{competition_id},.question.activated')]
    public function refresh(): void
    {
        $this->competition = DebateCompetition::find($this->competition_id);
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.competition-component');
    }
}
