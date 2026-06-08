<?php

namespace App\Http\Livewire\Profesor\Debate;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateGroup;

trait ResetTrait
{    
    public function resetModel()
    {
        $this->competition = New DebateCompetition();
        $this->competition->user_id = null;
        $this->competition->name = null;
        $this->competition->token = null;
        $this->competition->description = null;
        $this->competition->motive = null;
        $this->competition->date = null;
        $this->competition->status_active = null;
    }

    public function resetModelDebate()
    {
        $this->debate = New Debate();
        $this->debate->competition_id = null;
        $this->debate->token = null;
        $this->debate->grado_id = null;
        $this->debate->seccion_id = null;
        $this->debate->name = null;
        $this->debate->description = null;
        $this->debate->question_max = null;
        $this->debate->status_active = null;
        $this->debate->winner_section_id = null;
        $this->debate->attachment = null;
    }

    public function resetModelGroup()
    {
        $this->group = New DebateGroup();
        $this->group->competition_id = null;
        $this->group->name = null;
        $this->group->description = null;
        $this->group->attachment = null;
    }

    public function resetModelQuestion()
    {
        $this->question = New DebateQuestion();
        $this->question->debate_id = null;
        $this->question->category = null;
        $this->question->text = null;
        $this->question->time = null;
        $this->question->weighting = null;
        $this->question->observation = null;
        $this->question->option_max = null;
        $this->question->status_active = null;
        $this->question->attachment = null;
        $this->question->time_elapsed = null;
        $this->question->status_answer = null;
        $this->question->status_under_review = null;
    }
    public function resetModelOption()
    {
        $this->option = New DebateOption();
        $this->option->question_id = null;
        $this->option->text = null;
        $this->option->observation = null;
        $this->option->status_option_correct = null;
        $this->option->attachment = null;
    }
}