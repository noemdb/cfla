<?php

namespace App\Livewire\App\General\Educational\Competition\Dashboard;

use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class OptionComponent extends Component
{
    public $question,$options,$option,$active_id,$literal,$colors;

    #[On('question-active')] 
    public function updateOptionList($id)
    {
        $this->question = DebateQuestion::find($id);
        $this->options = DebateOption::where('question_id',$id)->inRandomOrder()->get();
        $this->active_id = null;
    }

    public function mount($question_id)
    {
        $this->updateOptionList($question_id);
        $this->literal = ['A','B','C','D','E','F'];     
        $this->colors = ['primary','secondary','positive','negative','warning','info'];     
    }
    

//     <x-badge.circle icon="home" />
// <x-badge.circle primary icon="pencil" />
// <x-badge.circle secondary icon="clipboard-list"  />
// <x-badge.circle positive icon="check" />
// <x-badge.circle negative icon="x" />
// <x-badge.circle warning icon="exclamation" />
// <x-badge.circle info icon="information-circle" />
// <x-badge.circle dark icon="ban" />
// <x-badge.circle secondary label="A"  />
// <x-badge.circle positive label="B" />
// <x-badge.circle negative label="C" />
// <x-badge.circle primary label="+" />

    public function render()
    {
        return view('livewire.app.general.educational.competition.dashboard.option-component');
    }

    public function active($id)
    {
        $question = DebateQuestion::find($id);
        $this->active_id = ($question) ? $question->id : null ;
        $this->dispatch('question-active',id: $id);
    }

    public function loadActive()
    {
        $this->active_id = ($this->options->isNotEmpty()) ? $this->options->first()->id : null;
    }
}
