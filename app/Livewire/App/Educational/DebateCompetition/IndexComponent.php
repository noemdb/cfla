<?php

namespace App\Livewire\App\Educational\DebateCompetition;

use App\Models\app\Educational\DebateCompetition;
use Livewire\Component;

class IndexComponent extends Component
{
    public $competition,$token;

    public function mount($token)
    {
        $this->competition = DebateCompetition::where('token',$token)->first(); 
    }

    public function render()
    {
        return view('livewire.app.educational.debate-competition.index-component');
    }
}
