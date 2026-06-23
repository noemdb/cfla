<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitWeek;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\app\Assistcontrol\AssitWeek;
use Illuminate\Http\Request;
use Livewire\Component;

class NavBarComponent extends Component
{
    public $assit_weeks;
    public $assit_week_id,$assit_schedules,$assit_schedule,$assit_schedule_id;
    public $editModeAssitSchedule=null,$createModeAssitSchedule=null;
    public $list_comment_assit_week;

    protected $listeners = ['saveWeekNavBar'];

    public function mount($id)
    {
        $this->setAssitWeek($id);
        $this->list_comment_assit_week = AssitWeek::COLUMN_COMMENTS;
    }

    public function render()
    {
        $this->setAssitWeek($this->assit_schedule_id);
        return view('livewire.administracion.assist-control.assit-week.nav-bar-component');
    }

    public function saveWeekNavBar($id=null)
    {
        $assit_week = AssitWeek::find($id);
        if ($assit_week) {
            $this->assit_week_id = $id;
            $this->render();
        }
    }

    public function setAssitWeek($id)
    {
        $assit_schedule = AssitSchedule::find($id);
        if ($assit_schedule) {
            $this->assit_schedule_id = $id;
            $this->assit_weeks = AssitWeek::where('assit_schedule_id',$this->assit_schedule_id)->get();
        }
    }
}
