<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.answer-component

use App\Models\app\Educational\DebateAnswer;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class AnswerComponent extends Component
{
    public $question,$answer,$grado,$seccions,$question_id;
    public $timeRemaining,$timerActive,$pollingInterval,$timeElapsed;
    public $interval;

    #[On('answer-update')] 
    public function updateQuestion($id)
    {
        $this->question = DebateQuestion::findOrFail($id);
        $this->question_id = $this->question->id;
        $this->timeElapsed = $this->question->time_elapsed;
        $this->timeRemaining = $this->question->time - $this->timeElapsed;
        $this->timerActive = false;
        $this->answer = DebateAnswer::where('question_id',$id)->first();
    }   

    public function mount($question_id)
    { 
        $this->updateQuestion($question_id);

        $this->grado = ($this->question) ? $this->question->grado : null ;
        $this->seccions = ($this->question) ? $this->question->seccions->where('status_inscription_affects','true') : null ;

        $this->timerActive = false;
        $this->pollingInterval = 100;

        $this->answer = DebateAnswer::where('question_id',$question_id)->first();
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.moderator.answer-component');
    }

    public function start()
    {
        $this->timerActive = true;
        $this->timeElapsed = $this->question->time_elapsed;
        $this->dispatch('startTimer', active: $this->timerActive); 
    }

    public function decrementCount()
    {
        if ($this->timeRemaining > 0) {
            $this->timeRemaining--;
            $this->timeElapsed++;
            $this->question->time_elapsed = $this->timeElapsed;
            $this->question->save();
        } else {
            $this->stopPolling();
        }
    }

    public function stopPolling()
    {
        $this->timerActive = false;
    }

}