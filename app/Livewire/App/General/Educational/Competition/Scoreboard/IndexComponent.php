<?php

namespace App\Livewire\App\General\Educational\Competition\Scoreboard;

use App\Models\app\Academy\Grado;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;

class IndexComponent extends Component
{
    public $token,$competition,$competition_id,$debate,$debate_id,$questions,$question,$question_id,$options,$answer,$grado,$seccions;
    public $literal,$colors,$pollingInterval,$timeRemaining;

    public function mount($token)
    {
        $this->token = $token;
        $this->competition = DebateCompetition::where('token',$token)->where('status_active',true)->first(); //dd($this->competition);
        $this->competition_id = ($this->competition) ? $this->competition->id : null ;
        $this->setDebateActive();
        $this->getQuestions();
        $this->setQuestionActive();
        $this->setOptionsActive();
        $this->grado = ($this->debate) ? $this->debate->grado : collect(); //dd($this->seccions);
        $this->seccions = ($this->debate) ? $this->debate->seccions : collect(); //dd($this->seccions);

        $this->literal = ['A','B','C','D','E','F']; 
        $this->colors = ['primary','secondary','positive','negative','warning','info'];
        $this->pollingInterval = 100; 
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.scoreboard.index-component');
    }

    public function setDebateActive()
    {
        $this->debate = ($this->competition) ? Debate::where('competition_id',$this->competition->id)->where('status_active',true)->first() : null;
        $this->debate_id = ($this->debate) ? $this->debate->id : null ;
    }

    public function getQuestions()
    {
        $this->questions = ($this->debate) ? $this->debate->questions : collect();
    }

    public function setQuestionActive()
    {
        $this->question = ($this->debate) ? DebateQuestion::where('debate_id',$this->debate->id)->where('status_active',true)->first() : null;
        $this->question_id = ($this->question) ? $this->question->id : null ;
        $this->timeRemaining = ($this->question) ? $this->question->timeRemaining : null ;
    }

    public function setOptionsActive()
    {
        $this->options = ($this->question) ? $this->question->options : collect();
    }

    public function updateTimetimeRemaining()
    {
        $this->question = ($this->question) ? DebateQuestion::find($this->question_id) : null;
    }
}

