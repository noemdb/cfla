<?php

namespace App\Http\Livewire\Profesor\Debate;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Pescolar\Seccion;

trait EditTrait
{    
    public function setEdit($id)
    {
        $this->close();
        $this->resetModel();
        $this->competition = DebateCompetition::findOrFail($id);
        $this->competition_id = $id;
        $this->modeCreator = true;
    }
    public function setEditDebate($id)
    {
        $this->close();
        $this->resetModelDebate();
        $this->debate = Debate::findOrFail($id);
        $this->debate_id = $id;
        $this->competition_id = $this->debate->competition_id;
        $this->modeCreatorDebate = true;
        $this->list_seccion = Seccion::list_seccion_grado($this->debate->grado_id) ;
    }
    public function setEditGroup($id)
    {
        $this->close();
        $this->resetModelGroup();
        $this->group = Debate::findOrFail($id);
        $this->group_id = $id;
        $this->competition_id = $this->group->competition_id;
        $this->modeCreatorGroup = true;
    }
    public function setEditQuestion($id)
    {
        $this->close();
        $this->resetModelQuestion();
        $this->question = DebateQuestion::findOrFail($id);
        $this->question_id = $id;
        $this->debate_id = $this->question->debate_id;
        $this->modeCreatorQuestion = true;
    }
    public function setEditOption($id)
    {
        $this->close();
        $this->resetModelOption();
        $this->option = DebateOption::findOrFail($id);
        $this->option_id = $id;
        $this->question_id = $this->option->question_id;
        $this->modeCreatorOption = true;
    }
}