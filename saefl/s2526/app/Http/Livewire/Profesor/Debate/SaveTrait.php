<?php

namespace App\Http\Livewire\Profesor\Debate;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition as Competition;
use App\Models\app\Educational\DebateGroup;

trait SaveTrait
{    
    public function save()
    {  
        $this->competition->user_id = $this->user_id;
        $this->competition->token  = (empty($this->competition->token)) ? Competition::genTokenSm(8) : $this->competition->token ;

        $this->validate([
            'competition.user_id'=>'required|integer',
            'competition.name'=>'required|string',
            'competition.token'=>'required|string',
            'competition.description'=>'required|string',
            'competition.motive'=>'nullable|string',
            'competition.date'=>'required|date',
            'competition.status_active'=>'nullable|boolean',
            'competition.attachment'=>'nullable|string',
            'competition.context'=>'nullable|string',
            'cant_group'=>'nullable|integer',
        ]);
        $this->competition->save();

        if ($this->modeCreator) {
            for ($i=1; $i <= $this->cant_group; $i++) { 
                $arr = [
                    'competition_id'=>$this->competition->id,
                    'name'=>'Grupo '.$i,
                    'description'=>null,
                    'attachment'=>null,
                ];
                DebateGroup::create($arr);
            }
        }
        
        $this->competition_id = $this->competition->id; 
        $this->resetModel();

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado/actualizado exitosamente';
		$this->showSwal($title,$html);

        $this->close();
    }

    public function saveDebate()
    {  
        $this->debate->token  = (empty($this->debate->token)) ? Debate::genTokenSm(8) : $this->debate->token ;
        $this->debate->competition_id = $this->competition_id;
        $this->validate([
            'debate.competition_id' => 'required|integer',
            'debate.token' => 'required|string',
            'debate.grado_id' => 'required|integer',
            'debate.seccion_id' => 'required|integer',
            'debate.name' => 'required|string',
            'debate.description' => 'required|string',
            'debate.status_active' => 'nullable|boolean',
            'debate.winner_section_id' => 'nullable|integer',
            'debate.attachment' => 'nullable|string',
            'debate.question_max' => 'required|integer',
            'debate.context'=>'nullable|string',
        ]);
        
        $this->debate->save();

        $this->resetModelDebate();
        $this->debate_id = null;
        // $this->competition_id = null;
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado/actualizado exitosamente';
		$this->showSwal($title,$html);

        $this->close();
    }

    public function saveGroup()
    {  
        $this->group->competition_id = $this->competition_id; //dd($this->group);
        $this->validate([
            'group.competition_id' => 'required|integer',
            'group.name' => 'required|string',
            'group.description' => 'nullable|string',
            'group.attachment' => 'nullable|string',
        ]);
        
        $this->group->save();

        $this->resetModelGroup();
        $this->group_id = null;
        // $this->competition_id = null;
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado/actualizado exitosamente';
		$this->showSwal($title,$html);

        $this->close();
    }


    public function saveQuestion()
    {  
        $this->question->debate_id = $this->debate_id; //dd($this->question);
        $this->validate([
            'question.debate_id' => 'required|integer',
            'question.category' => 'required|string',
            'question.text' => 'required|string',
            'question.time' => 'required|integer',
            'question.weighting' => 'required|integer',
            'question.observation' => 'nullable|string',
            'question.status_active' => 'nullable|boolean',
            'question.attachment' => 'nullable|string',
            'question.context'=>'nullable|string',
        ]);
        
        $this->question->save();

        $this->resetModelQuestion();
        // $this->debate_id = null;
        // $this->competition_id = null;
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado/actualizado exitosamente';
		$this->showSwal($title,$html);

        $this->close();
    }


    public function saveOption()
    {  
        $this->option->question_id = $this->question_id; //dd($this->option);
        $this->validate([
            'option.text' => 'required|string',
            'option.observation' => 'nullable|string',
            'option.status_option_correct' => 'nullable|boolean',
            'option.attachment' => 'nullable|string',
            'option.context'=>'nullable|string',
        ]);
        
        $this->option->save();

        $this->resetModelOption();
        // $this->question_id = null;
        // $this->debate_id = null;
        // $this->competition_id = null;
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Registro realizado/actualizado exitosamente';
		$this->showSwal($title,$html);

        $this->close();
    }
}