<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitSchedules;

use App\Models\app\Assistcontrol\AssitSchedule;
use Illuminate\Http\Request;
use Livewire\Component;

class IndexComponent extends Component
{
    public $list_comment;
    public $name,$number_turn,$description,$observations,$frecuency,$status;
    public $assit_schedule,$assit_schedule_id;

    public function mount($id)
    {
        $this->setAssitSchedule($id);
        $this->list_comment = AssitSchedule::COLUMN_COMMENTS;
    }

    public function render()
    {
        $this->setAssitSchedule( $this->assit_schedule_id);
        return view('livewire.administracion.assist-control.assit-schedules.index-component');
    }

    public function setAssitSchedule($id)
    {
        $assit_schedule = AssitSchedule::find($id);
        if ($assit_schedule) {
            $this->assit_schedule_id = $id;
            $this->assit_schedule = $assit_schedule;
            $this->id = $assit_schedule->id;
            $this->name = $assit_schedule->name;
            $this->number_turn = $assit_schedule->number_turn;
            $this->description = $assit_schedule->description;
            $this->observations = $assit_schedule->observations;
            $this->frecuency = $assit_schedule->frecuency;
            $this->status = $assit_schedule->status;
        }
    }
}
