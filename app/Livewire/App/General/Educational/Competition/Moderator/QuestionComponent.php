<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.question-component

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class QuestionComponent extends Component
{
    public $debate,$debate_id,$questions,$question,$active_id,$category,$list_category;

    #[On('detabe-active')] 
    public function updateQuestionsList($id)
    {
        $this->debate = Debate::findOrFail($id);
        $this->questions = DebateQuestion::where('debate_id',$id); //dd($id,$this->category,$this->questions);    
        $this->questions = ($this->category) ? $this->questions->where('category',$this->category) : $this->questions ;    
        $this->questions = $this->questions->inRandomOrder()->get(); //dd($id,$this->category,$this->questions);        
        $this->loadActive();       
        $this->updatedListCategory($id);
        $this->dispatch('question-active',id: $this->active_id);
    }

    #[On('update-score')] 
    public function updateScore()
    {
        $this->questions = DebateQuestion::where('debate_id',$this->debate->id)->where('category',$this->category)->inRandomOrder()->get();
    }

    #[On('question-online')] 
    public function updateQuestionOnline($id)
    {
        $this->question = DebateQuestion::findOrFail($id);
        $this->category = ($this->question) ? $this->question->category : null; 
    }

    public function mount($debate_id)
    {
        $this->updateQuestionsList($debate_id); 
        $this->loadActive();        
    }

    public function loadActive()
    {
        $question =  DebateQuestion::where('status_active',true)->first();
        $this->question = ($question) ? $question : null;
        $this->active_id = ($question) ? $question->id : null;
        $this->category = ($question) ? $question->category : null;        
    }
    
    public function render()
    {
        return view('livewire.app.general.educational.competition.moderator.question-component');
    }

    public function active($id)
    {
        $question = DebateQuestion::findOrFail($id);
        $this->question = $question;
        $this->active_id = $question->id ;
        $this->dispatch('question-active',id: $id);
        $this->updatedListCategory($this->debate->id);
    }

    public function updatedCategory($category)
    {
        $this->questions = DebateQuestion::where('debate_id',$this->debate->id)->where('category','like','%'.$category.'%')->inRandomOrder()->get();
    }

    public function updatedListCategory($debate_id)
    {
        $this->list_category =  DebateQuestion::CATEGORY;
        $pestudio = $this->debate->pestudio;
        $filteredArray = [];
        foreach ($this->list_category as $key => $value) {
            if (strpos($key, '['.$pestudio->code_oficial.']') !== false) {
                $filteredArray[$key] = $value;
            }
        }
        $this->list_category = $filteredArray;
    }

    public function setOnline($id)
    {
        $this->debate = Debate::setActive($id);
        // $this->active_id = $this->debate->id ;
        $this->dispatch('debate-online',id: $id);
    }

    public function setOffline($id)
    {
        $this->debate = Debate::setDesActive($id);
        // $this->active_id = null;
        $this->dispatch('debate-online',id: $id);
    }

    public function setOnlineQuestion($id)
    {
        $this->question = DebateQuestion::setActive($id);
        $this->active_id = $id;
    }

    public function setOfflineQuestion($id)
    {
        $this->question = DebateQuestion::setDesActive($id);
        $this->active_id = null;
    }

}