<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.question-component

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class QuestionComponent extends Component
{
    public $debate,$debate_id,$questions,$question,$active_id,$category,$list_category,$list_weighting,$weighting;

    #[On('detabe-active')] 
    public function updateQuestionsList($id)
    {
        $this->debate = Debate::findOrFail($id);
        $this->questions = DebateQuestion::where('debate_id',$id);
        $this->questions = ($this->category) ? $this->questions->where('category',$this->category) : $this->questions ;    
        $this->questions = $this->questions->inRandomOrder()->get();
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
        $this->list_weighting = DebateQuestion::list_weighting();   //dd($this->list_weighting);  
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
        $this->questions = $this->questions->sortByDesc('status_active');
        return view('livewire.app.general.educational.competition.moderator.question-component');
    }

    public function active($id)
    {
        $question = DebateQuestion::findOrFail($id);
        $this->debate = $question->debate;
        $this->question = $question;
        $this->active_id = $question->id ;
        $this->dispatch('question-active',id: $id);
        $this->updatedListCategory($this->debate->id);
        // $this->updatedWeighting();
    }

    public function updatedCategory($category)
    {
        $this->questions =  DebateQuestion::where('debate_id',$this->debate->id)->where('category','like','%'.$category.'%');
        $this->questions = ($this->weighting) ? $this->questions->where('weighting',$this->weighting) : $this->questions;
        $this->questions = $this->questions->inRandomOrder()->get();
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

    public function updatedWeighting()
    {
        $this->updatedCategory($this->category);
    }

    public function setOnline($id)
    {
        $this->debate = Debate::setActive($id);
        $this->question = DebateQuestion::find($this->active_id);
        $this->updatedCategory($this->category);
        $this->dispatch('debate-online',id: $id);
        $id = ($this->question) ? $this->question->id : null ;
        $this->dispatch('question-active',id: $id);
    }

    public function setOffline($id)
    {
        $this->debate = Debate::setDesActive($id);
        $this->question = DebateQuestion::find($this->active_id);
        $this->updatedCategory($this->category);
        $this->dispatch('debate-online',id: $id);
        $id = ($this->question) ? $this->question->id : null ;
        $this->dispatch('question-active',id: $id);
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

    public function activeOnline($id)
    {
        // $this->question = DebateQuestion::find($id);
        $this->question = DebateQuestion::setActive($id);
        $this->questions =  DebateQuestion::where('debate_id',$this->question->debate_id)->get();
    }

}