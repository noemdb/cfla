<?php

namespace App\Http\Livewire\Profesor\Incident\Action;
// incident_id,description,observations,

trait ValidationTrait
{
    protected $rules = [
        'incident_action.incident_id' => 'required',
        'incident_action.corrective_id' => 'required',
    ];

    protected function validationAttributes()
    {
        return [
            'incident_action.incident_id' => $this->list_comment['incident_id'],
            'incident_action.corrective_id' => $this->list_comment['corrective_id'],
        ];
    }
}

?>
