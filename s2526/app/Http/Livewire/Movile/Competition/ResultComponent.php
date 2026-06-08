<?php

namespace App\Http\Livewire\Movile\Competition;

use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Pescolar\Peducativo;
use Carbon\Carbon;
use Livewire\Component;

class ResultComponent extends Component
{
    public $competition,$date;

    public function mount()
    {
        $this->updateDateScoreBoard();
    }  

    public function render()
    {
        return view('livewire.movile.competition.result-component');
    }

    public function updateDateScoreBoard()
    {
        $this->date = Carbon::now();
        $this->competition = DebateCompetition::active()->where('id', 1)->first();
    }
}
