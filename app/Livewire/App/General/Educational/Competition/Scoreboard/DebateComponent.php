<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use Livewire\Attributes\On;
use Livewire\Component;

class DebateComponent extends Component
{
    public $competition, $debate, $competition_id;

    public function mount($id)
    {
        $this->competition_id = $id;
        $this->refresh();
    }

    #[On('echo:competition.{competition_id},.debate.activated')]
    public function refresh(): void
    {
        $this->competition = DebateCompetition::findOrFail($this->competition_id);
        $this->debate = Debate::where('competition_id', $this->competition_id)
            ->where('status_active', true)->first();
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.debate-component');
    }
}
