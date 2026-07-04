<?php

namespace App\Http\Livewire\Profesor\Incident\Main;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentCorrective;
use App\Models\app\Incident\IncidentDuty;
use App\Models\app\Incident\IncidentFault;

trait UpdatedTrait
{
    public function updatedIncidentDutyId ($value)
    {
        if ($value) {
            $this->list_fault = IncidentFault::list_fault($value);
            $this->resetErrorBag('incident.duty_id');
            $this->list_corrective = collect();
        } else {
            $this->list_corrective = collect();            
        }
        $this->incident->fault_id = null;
    }

    public function updatedIncidentFaultId ($value)
    {
        if ($value) {
            $incident_fault = IncidentFault::find($value);
            $this->incident->duty_id = ($incident_fault) ? $incident_fault->duty_id : null ;
            $this->list_corrective = IncidentCorrective::list_corrective($value);
        } else {
            $this->list_corrective = collect();
        }    
        $this->resetErrorBag('incident.fault_id');
    }
}

?>
