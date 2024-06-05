<?php

namespace App\Livewire\App\General\Educational\Competition\Dashboard;

use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;

class AnswerComponent extends Component
{
    public $question,$grado,$seccions,$question_id;

    public function mount($question_id)
    { 
        $question = DebateQuestion::find($question_id);
        $this->question_id = ($question) ? $question->id : null ;
        $this->grado = ($question) ? $question->grado : null ;
        $this->seccions = ($question) ? $question->seccions : null ;
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.dashboard.answer-component');
    }
}
