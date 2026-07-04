<?php

namespace App\Http\Livewire\General\Educational\Competition\Debate;

trait DebateTrait
{
    protected $rules = [
        'question.category' => 'required|string',
        'question.text' => 'required|string',
        'question.time' => 'required|integer',
        'question.weighting' => 'required|integer',
        'question.observation' => 'nullable|string',
        // 'question.option_max' => 'nullable|integer',
        'question.status_active' => 'nullable|boolean',
        'option.text' => 'required|string',
        'option.observation' => 'nullable|string',
        'option.status_option_correct' => 'nullable|boolean',
    ];

    protected function validationAttributes()
    {
        return [
            'question.category' => $this->list_comment['category'],
            'question.text' => $this->list_comment['text'],
            'question.time' => $this->list_comment['time'],
            'question.weighting' => $this->list_comment['weighting'],
            'question.observation' => $this->list_comment['observation'],
            // 'question.option_max' => $this->list_comment['option_max'],
            'question.status_active' => $this->list_comment['status_active'],
            'option.text' => $this->list_comment_option['text'],
            'option.observation' => $this->list_comment_option['observation'],
            'option.status_option_correct' => $this->list_comment_option['status_option_correct'],
        ];
    }
}

?>