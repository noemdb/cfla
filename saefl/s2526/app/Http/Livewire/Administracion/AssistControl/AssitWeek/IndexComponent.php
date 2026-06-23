<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitWeek;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\app\Assistcontrol\AssitWeek;
use Livewire\Component;

class IndexComponent extends Component
{
    //'name','assit_schedule_id','number_week'
    public $name,$number_week;
    public $assit_week,$assit_schedule_id,$assit_week_id;
    public $editMode=false;
    public $list_comment_assit_week;

    public function mount($id)
    {
        $this->setAssitWeek( $id);
        $this->list_comment_assit_week = AssitWeek::COLUMN_COMMENTS;
    }

    public function render()
    {
        $this->setAssitWeek( $this->assit_week_id);
        return view('livewire.administracion.assist-control.assit-week.index-component');
    }

    public function setAssitWeek($id)
    {
        $assit_week = AssitWeek::find($id);
        if ($assit_week) {
            $this->assit_week = $assit_week;
            $this->assit_week_id = $assit_week->id;
            $this->assit_schedule_id = $id;
            $this->name = $assit_week->name;
            $this->number_week = $assit_week->number_week;
        }
    }
}
