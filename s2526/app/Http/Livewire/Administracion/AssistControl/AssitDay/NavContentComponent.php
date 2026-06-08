<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitDay;

use App\Models\app\Assistcontrol\AssitDay;
use App\Models\app\Assistcontrol\AssitWeek;
use Livewire\Component;

class NavContentComponent extends Component
{
    // 'assit_week_id','name','number_day'
    public $list_comment_assit_day;
    public $assit_day_id,$assit_week_id,$name,$number_day,$assit_days;
    public $assit_week;

    public function mount($id)
    {
        $this->setAssitDay($id);
        $this->list_comment_assit_day = AssitDay::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-day.nav-content-component');
    }

    public function setAssitDay($id)
    {
        $assit_week = AssitWeek::find($id);
        if ($assit_week) {
            $this->assit_week_id = $assit_week->id;
            $this->assit_week = $assit_week;
            $this->assit_days = $assit_week->assit_days;
        }
    }
}
