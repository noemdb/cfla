<?php

namespace App\Http\Livewire\Administracion\Poll\Option;
// 'poll_question_id','text','description','observations','body'

trait PollOptionTrait
{
    protected $rules = [
        'poll_option.poll_question_id' => 'required|integer',
        'poll_option.text' => 'required|string|min:6',
        'poll_option.description' => 'required|string|max:500',
        'poll_option.observations' => 'nullable|string|max:500',
        'poll_option.body' => 'nullable|string',
        'poll_option.image' => 'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'poll_option.poll_question_id' => $this->list_comment['poll_question_id'],
            'poll_option.text' => $this->list_comment['text'],
            'poll_option.description' => $this->list_comment['description'],
            'poll_option.observations' => $this->list_comment['observations'],
            'poll_option.body' => $this->list_comment['body'],
            'poll_option.image' => $this->list_comment['image'],
        ];
    }
}

?>
