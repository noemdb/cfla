<?php

namespace App\Http\Livewire\Bienestar\Description;

trait ValidateTrait
{
    protected $rules = [
        'incident_description.name'=>'required|string',
        'incident_description.ambit'=>'required|string',
        'incident_description.reason_id'=>'required|integer'
    ];

    protected function validationAttributes()
    {
        return [
            'incident_description.name' => $this->list_comment['name'],
            'incident_description.ambit' => $this->list_comment['ambit'],
            'incident_description.reason_id' => $this->list_comment['reason_id']
        ];
    }
}

?>
