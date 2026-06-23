<?php

namespace App\Http\Livewire\Profesor\Debate;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Educational\DebateCompetition;

trait CreateTrait
{    
    public function setCreate()
    {
        $this->close();
        $this->resetModel();
        $this->modeCreator = true;
    }

    public function setCreateDebate($id)
    {
        $this->close();
        $this->resetModelDebate();
        $competition = DebateCompetition::findOrFail($id);
        $this->competition_id = $competition->id;
        $this->debate_id = null;
        $this->debate->question_max = 4;
        $this->debate->status_active = true;
        $this->modeCreatorDebate = true;
    }

    public function setCreateGroup($id)
    {
        $this->close();
        $this->resetModelGroup();
        $competition = DebateCompetition::findOrFail($id);
        $this->competition_id = $competition->id;
        $this->group_id = null;
        $this->modeCreatorGroup = true;
    }

    public function setCreateQuestion($id)
    {
        $this->close();
        $this->resetModelQuestion();
        $debate = Debate::findOrFail($id);
        $this->debate_id = $debate->id;
        $this->modeCreatorQuestion = true;
    }

    public function setCreateOption($id)
    {
        $this->close();
        $this->resetModelOption();
        $question = DebateQuestion::findOrFail($id);
        $this->question_id = $question->id;
        $this->modeCreatorOption = true;
    }
}