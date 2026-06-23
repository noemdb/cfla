<?php

namespace App\Http\Livewire\Profesor\Incident\Main;
// /home/nuser/code/s2223/app/Http/Livewire/Bienestar/Incident/IncidentTrait.php

trait IncidentTrait
{
    protected $rules = [
        'incident.user_id'=>'required|integer',
        'incident.estudiant_id'=>'required|integer',
        'incident.profesor_id'=>'required|integer',
        'incident.duty_id'=>'required|integer',
        'incident.fault_id'=>'required|integer',
        'incident.description'=>'required|string',
        'incident.observations'=>'nullable|string',
        'incident.taken_actions'=>'nullable|string',
        'incident.status_pedagogical'=>'nullable|boolean',
        'incident.status_reiterative'=>'nullable|boolean',
        'incident.status_announcement'=>'nullable|boolean',
        'incident.date_announcement'=>'required_with:incident.status_announcement',
        'incident.hour_announcement'=>'required_with:incident.date_announcement',
        'incident_action.incident_id' => 'nullable',
        'incident_action.corrective_id' => 'nullable',
        'incident_action.status_selected' => 'nullable',
        // 'user.posts.*.title',
    ];

    protected function validationAttributes()
    {
        return [
            'incident.estudiant_id' => $this->list_comment['estudiant_id'],
            'incident.profesor_id' => $this->list_comment['profesor_id'],
            'incident.duty_id' => $this->list_comment['duty_id'],
            'incident.fault_id' => $this->list_comment['fault_id'],
            'incident.description' => $this->list_comment['description'],
            'incident.observations' => $this->list_comment['observations'],
            'incident.taken_actions' => $this->list_comment['taken_actions'],
            'incident.status_pedagogical' => $this->list_comment['status_pedagogical'],
            'incident.status_reiterative' => $this->list_comment['status_reiterative'],
            'incident.status_announcement' => $this->list_comment['status_announcement'],
            'incident.date_announcement' => $this->list_comment['date_announcement'],
            'incident.hour_announcement' => $this->list_comment['hour_announcement'],
            'incident.status_pedagogical' => $this->list_comment['status_pedagogical'],
            'incident_action.incident_id' => $this->list_comment['incident_id'],
            'incident_action.corrective_id' => $this->list_comment['corrective_id'],
            'incident_action.status_selected' => $this->list_comment['status_selected'],
            // 'incident.status_close' => $this->list_comment['status_close'],
            // 'incident.close_observations' => $this->list_comment['close_observations'],
        ];
    }
}

?>
