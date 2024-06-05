<?php

namespace App\Livewire\App\General\Educational\Competition\Dashboard;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class QuestionComponent extends Component
{
    public $debate,$questions,$active_id;

    #[On('detabe-active')] 
    public function updateQuestionsList($id)
    {
        $this->debate = Debate::find($id);
        $this->questions = DebateQuestion::where('debate_id',$id)->inRandomOrder()->get(); //dd($id,$this->questions);
        $this->dispatch('question-active',id: $this->active_id);
        $this->active_id = null;
        // $this->loadActive();
        //dd($this->questions);
    }

    public function mount($debate_id)
    {
        $this->updateQuestionsList($debate_id);
        // $this->loadActive();        
    }
    
    public function render()
    {
        return view('livewire.app.general.educational.competition.dashboard.question-component');
    }

    public function active($id)
    {
        $question = DebateQuestion::find($id);
        $this->active_id = ($question) ? $question->id : null ;
        $this->dispatch('question-active',id: $id);
    }

    public function loadActive()
    {
        $this->active_id = ($this->questions->isNotEmpty()) ? $this->questions->first()->id : null ;
        $this->dispatch('question-active',id: $this->active_id);
    }
}
