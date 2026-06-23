<?php

namespace App\Http\Livewire\Administracion\Poll;

trait PollMainTrait
{
    protected $rules = [
        'poll_main.poll_group_id' => 'required|integer',
        'poll_main.autoridad_id' => 'required|integer',
        'poll_main.name' => 'required|string|min:6',
        'poll_main.description' => 'required|string|max:500',
        'poll_main.observations' => 'nullable|string|max:500',
        'poll_main.image' => 'nullable',
        'poll_main.date_start' => 'required|date',
        'poll_main.date_end' => 'required|date',
        'poll_main.time_start' => 'required|string',
        'poll_main.time_end' => 'required|string',
        'poll_main.ci_list' => 'nullable|string',
        'poll_main.status_test' => 'nullable',
        'poll_main.status_estudiant' => 'required',
        'poll_main.status_exclude_last' => 'required|string',
        'poll_main.status_representant' => 'required',
    ];

    protected function validationAttributes()
    {
        return [
            'poll_main.poll_group_id' => $this->list_comment['poll_group_id'],
            'poll_main.autoridad_id' => $this->list_comment['autoridad_id'],
            'poll_main.name' => $this->list_comment['name'],
            'poll_main.description' => $this->list_comment['description'],
            'poll_main.observations' => $this->list_comment['observations'],
            'poll_main.image' => $this->list_comment['image'],
            'poll_main.date_start' => $this->list_comment['date_start'],
            'poll_main.time_start' => $this->list_comment['time_start'],
            'poll_main.date_end' => $this->list_comment['date_end'],
            'poll_main.time_end' => $this->list_comment['time_end'],
            'poll_main.ci_list' => $this->list_comment['ci_list'],
            'poll_main.status_test' => $this->list_comment['status_test'],
            'poll_main.status_estudiant' => $this->list_comment['status_estudiant'],
            'poll_main.status_exclude_last' => $this->list_comment['status_exclude_last'],
            'poll_main.status_representant' => $this->list_comment['status_representant'],
        ];
    }
}

?>
