<?php

namespace App\Http\Livewire\Bienestar\Description;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentDescription;
use App\Models\app\Incident\IncidentReason;

trait UpdatedTrait
{
    public function updatedIncidentDescriptionAmbit ($ambit) 
    {
        $this->list_reason = IncidentReason::list_reason_category(null,$ambit);
        $this->incident_description->reason_id = null;
        $this->resetErrorBag('incident_description.ambit');
    }

}

?>
