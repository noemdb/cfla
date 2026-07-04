<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitSchedules;

use App\Models\app\Assistcontrol\AssitSchedule;
use Illuminate\Http\Request;
use Livewire\Component;

class NavBarComponent extends Component
{
    //'name','assit_schedule_id','number_week'
    public $assit_week_id,$name,$assit_schedule_id,$number_week;
    public $assit_schedules,$assit_schedule;
    public $list_comment_assit_week;

    protected $listeners = ['saveScheduleNavBar'];

    public function mount(Request $request)
    {
        $this->list_comment_assit_schedule = AssitSchedule::COLUMN_COMMENTS;
    }

    public function render()
    {
        $this->assit_schedules = AssitSchedule::all();
        return view('livewire.administracion.assist-control.assit-schedules.nav-bar-component');
    }

    public function saveScheduleNavBar($id=null)
    {
        $this->assit_schedule_id = $id;
        $this->render();
    }
}
