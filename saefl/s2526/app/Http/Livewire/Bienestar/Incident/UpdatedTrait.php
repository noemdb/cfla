<?php

namespace App\Http\Livewire\Bienestar\Incident;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentDescription;
use App\Models\app\Incident\IncidentReason;

trait UpdatedTrait
{
    public function updatedIncidentStatusValoration ($value)
    {        
        $status_valoration =  $this->getStatusValoration($value);
        $this->incident->reason_id = null;
        $this->incident->type = null;
        $this->incident->description = null;

        $reason_id = $this->incident->reason_id;
        $ambit = $this->incident->type;

        $this->list_reason = IncidentReason::list_reason_category($status_valoration,$ambit);
        $this->list_description = IncidentDescription::list_description_tab($status_valoration,$ambit,$reason_id);
        $this->resetErrorBag('incident.status_valoration');
    }

    public function updatedIncidentType ($ambit) 
    {
        $status_valoration =  $this->getStatusValoration($this->incident->status_valoration);
        $this->incident->reason_id = null;
        $this->incident->description = null;
        $reason_id = $this->incident->reason_id;

        $this->list_reason = IncidentReason::list_reason_category($status_valoration,$ambit);
        $this->list_description = IncidentDescription::list_description_tab($status_valoration,$ambit,$reason_id);
        $this->resetErrorBag('incident.type');
    }

    public function updatedIncidentReasonId ($reason_id)
    {
        $ambit = $this->incident->type;
        $status_valoration =  $this->getStatusValoration($this->incident->status_valoration);

        $this->incident->description = null;

        $this->list_reason = IncidentReason::list_reason_category($status_valoration,$ambit);
        $this->list_description = IncidentDescription::list_description_tab($status_valoration,$ambit,$reason_id);
        $this->resetErrorBag('incident.reason_id');
    }

    public function updatedIncidentDescription ($descriptions)
    {
        $incident_reason = IncidentReason::select('incident_reasons.*','incident_descriptions.ambit')
        ->join('incident_descriptions', 'incident_reasons.id', '=', 'incident_descriptions.reason_id')
        ->where('incident_descriptions.name',$descriptions)
        ->first(); //dd($descriptions,$incident_reason);

        $status_valoration = ($incident_reason) ? $this->getStatusValoration($incident_reason->status_valoration) : null;
        $ambit = ($incident_reason) ? $incident_reason->ambit : null ;
        $this->list_reason = IncidentReason::list_reason_category($status_valoration,$ambit);

        $this->incident->reason_id = ($incident_reason) ? $incident_reason->id : null;

        $this->incident->type = ($incident_reason) ? $incident_reason->ambit : null;

        $this->incident->status_valoration = ($incident_reason) ? $incident_reason->status_valoration : null;

        $this->resetErrorBag('incident.description');
    }

    public function getStatusValoration ($value)
    {
        switch ($value) {
            case '1': $status_valoration = true; break;
            case '0': $status_valoration = false; break;
            default : $status_valoration = null; break;
        }
        return $status_valoration;
    }


}

?>
