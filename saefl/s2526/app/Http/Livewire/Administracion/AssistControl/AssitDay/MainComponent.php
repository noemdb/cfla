<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitDay;

use App\Models\app\Assistcontrol\AssitDay;
use App\Models\app\Assistcontrol\AssitWeek;
use Livewire\Component;

class MainComponent extends Component
{
    // 'assit_week_id','name','number_day'
    public $list_comment;
    public $assit_day_id,$assit_week_id,$name,$number_day;
    public $assit_week;

    protected $listeners = ['mainSaveDay'];

    public function mount($id)
    {
        $assit_week = AssitWeek::find($id);
        if ($assit_week) {
            $this->assit_week_id = $assit_week->id;
            $this->assit_week = $assit_week;
            $this->assit_days = $assit_week->assit_days;
        }
        $this->list_comment_assit_week = AssitDay::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-day.main-component');
    }

    public function mainSaveDay($id=null)
    {
        // $this->emit('saveDayNavBar', $id);
        // $this->emit('saveDayContent', $id);
    }
}
