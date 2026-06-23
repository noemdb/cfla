<?php

namespace App\Http\Livewire\Movile\Profesor\Incident;
// /home/nuser/code/s2223/app/Http/Livewire/Movile/Profesor/Incident/IncidentTrait.php

trait IncidentTrait
{
    protected $rules = [
        'incident.estudiant_id'=>'required|integer',
        'incident.user_id'=>'required|integer',
        'incident.profesor_id'=>'required|integer',
        'incident.reason_id'=>'required|integer',
        'incident.type'=>'required|string',
        'incident.description'=>'required|string',
        'incident.observations'=>'nullable|string',
        'incident.taken_actions'=>'required|string',
        'incident.status_aggression'=>'nullable|boolean',
        'incident.status_announcement'=>'nullable|boolean',
        'incident.date_announcement'=>'nullable',
        'incident.hour_announcement'=>'nullable',
        'incident.status_pedagogical'=>'nullable|boolean',
        'incident.status_close'=>'nullable|boolean',
        'incident.close_observations'=>'required_with:incident.status_close',
    ];

    protected function validationAttributes()
    {
        return [
            'incident.user_id' => $this->list_comment['user_id'],
            'incident.estudiant_id' => $this->list_comment['estudiant_id'],
            'incident.profesor_id' => $this->list_comment['profesor_id'],
            'incident.reason_id' => $this->list_comment['reason_id'],
            'incident.type' => $this->list_comment['type'],
            'incident.description' => $this->list_comment['description'],
            'incident.observations' => $this->list_comment['observations'],
            'incident.taken_actions' => $this->list_comment['taken_actions'],
            'incident.status_aggression' => $this->list_comment['status_aggression'],
            'incident.status_announcement' => $this->list_comment['status_announcement'],
            'incident.date_announcement' => $this->list_comment['date_announcement'],
            'incident.hour_announcement' => $this->list_comment['hour_announcement'],
            'incident.status_pedagogical' => $this->list_comment['status_pedagogical'],
            'incident.status_close' => $this->list_comment['status_close'],
            'incident.close_observations' => $this->list_comment['close_observations'],
        ];
    }
}

?>
