<?php

namespace App\Http\Livewire\Administracion\Educational;

trait DebateTrait
{
    protected $rules = [
        'debate.token' => 'required|string',
        'debate.grado_id' => 'required|integer',
        'debate.seccion_id' => 'nullable|integer',
        'debate.name' => 'required|string',
        'debate.description' => 'required|string',
        'debate.status_active' => 'nullable|boolean',
        'debate.winner_section_id' => 'nullable|integer',
        'debate.attachment' => 'nullable|string',
        'debate.question_max' => 'required|integer',
        'debate.pevaluacion_id' => 'nullable|integer',
    ];

    protected function validationAttributes()
    {
        return [
            'debate.grado_id' => $this->list_comment['grado_id'],
            'debate.seccion_id' => $this->list_comment['seccion_id'],
            'debate.name' => $this->list_comment['name'],
            'debate.description' => $this->list_comment['description'],
            'debate.status_active' => $this->list_comment['status_active'],
            'debate.winner_section_id' => $this->list_comment['winner_section_id'],
            'debate.attachment' => $this->list_comment['attachment'],
            'debate.question_max' => $this->list_comment['question_max'],
            'debate.pevaluacion_id' => $this->list_comment['pevaluacion_id'],
        ];
    }
}

?>