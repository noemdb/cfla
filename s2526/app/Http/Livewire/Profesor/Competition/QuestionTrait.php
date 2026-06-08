<?php

namespace App\Http\Livewire\Profesor\Competition;

trait QuestionTrait
{
    protected $rules = [
        'question.debate_id' => 'required|integer',
        'question.pensum_id' => 'required|integer',
        'question.category' => 'required|string',
        'question.text' => 'required|string',
        'question.time' => 'required|integer',
        'question.weighting' => 'required|integer',
        'question.observation' => 'nullable|string',
        'question.status_active' => 'nullable|boolean',
        'question.attachment' => 'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'question.debate_id' => $this->list_comment['debate_id'],
            'question.pensum_id' => $this->list_comment['pensum_id'],
            'question.category' => $this->list_comment['category'],
            'question.text' => $this->list_comment['text'],
            'question.time' => $this->list_comment['time'],
            'question.weighting' => $this->list_comment['weighting'],
            'question.observation' => $this->list_comment['observation'],
            'question.status_active' => $this->list_comment['status_active'],
            'question.attachment' => $this->list_comment['attachment'],
        ];
    }
}

?>