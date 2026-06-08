<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitWeek;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\app\Assistcontrol\AssitWeek;
use Livewire\Component;

class MainComponent extends Component
{
    //'name','assit_schedule_id','number_week'
    public $list_comment;
    public $name,$number_week;
    public $assit_schedule,$assit_schedule_id,$assit_weeks;

    protected $listeners = ['mainSaveWeek'];

    public function mount($id)
    {
        $this->assit_schedule_id = $id;
        $assit_schedule = AssitSchedule::find($id);
        if ($assit_schedule) {
            $this->assit_schedule = $assit_schedule;
            $this->assit_weeks = AssitWeek::where('assit_schedule_id',$assit_schedule->id)->get();
        }

        $this->list_comment_assit_week = AssitWeek::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-week.main-component');
    }

    public function mainSaveWeek($id=null)
    {
        $this->emit('saveWeekNavBar', $id);
        $this->emit('saveWeekContent', $id);
    }
}
