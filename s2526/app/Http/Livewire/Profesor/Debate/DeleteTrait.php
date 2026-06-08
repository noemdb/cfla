<?php

namespace App\Http\Livewire\Profesor\Debate;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateGroup;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;

trait DeleteTrait
{    
    public function delete($id)
    {
        $competition = DebateCompetition::findOrFail($id);
        $competition->deleteOptions();      
        $competition->deleteQuestions();      
        $competition->deleteDebates();
        $competition->delete();
        $this->resetModel();
        $this->competition_id = null;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'El registro fué eliminado exitosamente';
        $this->showSwal($title,$html);
    }
    public function deleteDebate($id)
    {
        Debate::findOrFail($id)->delete();
        $this->resetModelDebate();
        // $this->competition_id = null;
        $this->debate_id = null;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'El registro fué eliminado exitosamente';
        $this->showSwal($title,$html);
    }
    public function deleteGroup($id)
    {
        DebateGroup::findOrFail($id)->delete();
        $this->resetModelGroup();
        // $this->competition_id = null;
        $this->group_id = null;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'El registro fué eliminado exitosamente';
        $this->showSwal($title,$html);
    }
    public function deleteQuestion($id)
    {
        DebateQuestion::findOrFail($id)->delete();
        $this->resetModelQuestion();
        // $this->competition_id = null;
        $this->debate_id = null;
        $this->question_id = null;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'El registro fué eliminado exitosamente';
        $this->showSwal($title,$html);
    }
    public function deleteOption($id)
    {
        DebateOption::findOrFail($id)->delete();
        $this->resetModelOption();
        // $this->competition_id = null;
        $this->debate_id = null;
        $this->question_id = null;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'El registro fué eliminado exitosamente';
        $this->showSwal($title,$html);
    }
}