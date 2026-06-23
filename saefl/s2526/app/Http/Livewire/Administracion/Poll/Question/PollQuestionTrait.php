<?php

namespace App\Http\Livewire\Administracion\Poll\Question;
// 'poll_main_id','text','description','observations','body'

trait PollQuestionTrait
{
    protected $rules = [
        'poll_question.poll_main_id' => 'required|integer',
        'poll_question.text' => 'required|string|min:6',
        'poll_question.description' => 'required|string|max:500',
        'poll_question.observations' => 'nullable|string|max:500',
        'poll_question.body' => 'nullable|string',
        'poll_question.status_grid' => 'required|string',
    ];

    protected function validationAttributes()
    {
        return [
            'poll_question.poll_main_id' => $this->list_comment['poll_main_id'],
            'poll_question.text' => $this->list_comment['text'],
            'poll_question.description' => $this->list_comment['description'],
            'poll_question.observations' => $this->list_comment['observations'],
            'poll_question.body' => $this->list_comment['body'],
            'poll_question.status_grid' => $this->list_comment['status_grid']
        ];
    }
}

?>
