<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class ResultComponent extends Component
{
    public $competition,$debate,$grado,$seccions;
    public $question;

    #[On('update-question-answer')] 
    public function updateQuestionsList($id) //ID Question
    {
        $this->question = DebateQuestion::findOrfail($id);
        $this->competition = DebateCompetition::findOrfail($this->competition->id);
    }

    public function mount($id) // ID de la competición
    {
        $this->competition = DebateCompetition::where('id',$id)->where('status_active',true)->first();
        $this->debate = ($this->competition) ? Debate::where('competition_id',$this->competition->id)->where('status_active',true)->first() : null;
        $this->grado = ($this->debate) ? $this->debate->grado : collect(); //dd($this->seccions);
        $this->seccions = ($this->debate) ? $this->debate->seccions : collect(); //dd($this->seccions);
    }


    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.result-component');
    }
}
