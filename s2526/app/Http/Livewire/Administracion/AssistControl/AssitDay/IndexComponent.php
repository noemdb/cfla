<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitDay;

use App\Models\app\Assistcontrol\AssitDay;
use App\Models\app\Assistcontrol\AssitSchedule;
use Livewire\Component;

class IndexComponent extends Component
{
//'assit_week_id','name','number_day'
    public $list_comment;
    public $name,$number_day;
    public $assit_schedule,$assit_schedule_id,$assit_day;
    public $editMode=false;

    public function mount($id)
    {
        $assit_day = AssitDay::find($id);
        if ($assit_day) {
            $this->assit_day_id = $id;
            $this->assit_day = $assit_day;
        }

        $this->list_comment_assit_day = AssitDay::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-day.index-component');
    }
}
