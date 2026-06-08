<?php

namespace App\Http\Livewire\Administracion\Educational;

trait DebateCompetitionTrait
{
    protected $rules = [
        'debate_competition.name' => 'required|string',
        'debate_competition.token' => 'required|string',
        'debate_competition.description' => 'required|string',
        'debate_competition.motive' => 'required|string',
        'debate_competition.date' => 'required|date',
        'debate_competition.status_active' => 'nullable|boolean',
        'debate_competition.attachment' => 'nullable|string'
    ];

    protected function validationAttributes()
    {
        return [
            'debate_competition.name' => $this->list_comment['name'],
            'debate_competition.token' => $this->list_comment['token'],
            'debate_competition.description' => $this->list_comment['description'],
            'debate_competition.motive' => $this->list_comment['motive'],
            'debate_competition.date' => $this->list_comment['date'],
            'debate_competition.status_active' => $this->list_comment['status_active'],
            'debate_competition.attachment' => $this->list_comment['attachment'],
        ];
    }
}

?>