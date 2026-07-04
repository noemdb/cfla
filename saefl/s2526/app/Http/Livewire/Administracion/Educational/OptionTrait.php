<?php

namespace App\Http\Livewire\Administracion\Educational;

trait OptionTrait
{
    protected $rules = [
        'option.text' => 'required|string',
        'option.observation' => 'nullable|string',
        'option.status_option_correct' => 'nullable|boolean',
        'option.attachment' => 'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'option.text' => $this->list_comment['text'],
            'option.observation' => $this->list_comment['observation'],
            'option.status_option_correct' => $this->list_comment['status_option_correct'],
            'option.attachment' => $this->list_comment['attachment'],
        ];
    }
}

?>