<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitSchedules;

use App\Models\app\Assistcontrol\AssitSchedule;
use Illuminate\Http\Request;
use Livewire\Component;

class NavContentComponent extends Component
{
    public $assit_schedules,$assit_schedule,$assit_schedule_id;
    public $editModeAssitSchedule=null,$createModeAssitSchedule=null;
    public $list_comment_assit_schedule;

    protected $listeners = ['saveScheduleContent'];

    public function mount(Request $request)
    {
        $this->list_comment_assit_schedule = AssitSchedule::COLUMN_COMMENTS;
    }

    public function render()
    {
        $this->assit_schedules = AssitSchedule::all();
        return view('livewire.administracion.assist-control.assit-schedules.nav-content-component');
    }
    public function saveScheduleContent($id=null)
    {
        $this->assit_schedule_id = $id;
        $this->render();
    }
}
