<?php

namespace App\Http\Livewire\Bienestar\IncidentAgreement;
// incident_id,description,observations,

trait IncidentAgreementTrait
{
    protected $rules = [
        'incident_agreement.description'=>'required|string',
        'incident_agreement.observations'=>'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'incident_agreement.description' => $this->list_comment['description'],
            'incident_agreement.observations' => $this->list_comment['observations'],
        ];
    }
}

?>