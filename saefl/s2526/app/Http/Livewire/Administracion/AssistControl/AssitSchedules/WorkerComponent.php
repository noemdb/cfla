<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitSchedules;

use App\User;
use Livewire\Component;

class WorkerComponent extends Component
{
    public $workers,$user,$user_id,$work_id,$worker_order;
    public $editWorkerModeAssitSchedules;

    protected $rules = [
        'assit_schedule_id' => 'required|integer'
    ];
    public function render()
    {
        $this->workers = User::getWorkers();
        return view('livewire.administracion.assist-control.assit-schedules.worker-component');
    }
}
