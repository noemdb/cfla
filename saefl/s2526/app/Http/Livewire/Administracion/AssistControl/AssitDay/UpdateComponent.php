<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitDay;

use App\Models\app\Assistcontrol\AssitDay;
use App\Models\app\Assistcontrol\AssitSchedule;
use Livewire\Component;

class UpdateComponent extends Component
{

    //'name','number_day'
    public $list_comment_assit_day;
    public $assit_day_id,$name,$number_day;
    public $assit_schedule,$assit_schedule_id,$assit_days;
    public $editModeAssitDay=false;

    public function mount($id)
    {
        $assit_day = AssitDay::find($id);
        if ($assit_day) {
            $this->assit_day_id = $assit_day->id;
            $this->name = $assit_day->name;
            $this->number_day = $assit_day->number_day;
        }

        $this->list_comment_assit_day = AssitDay::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-day.update-component');
    }

    public function editModeAssitDayOn()
    {
        $this->editModeAssitDay = true;
    }
    public function editModeAssitDayOff()
    {
        $this->editModeAssitDay = false;
    }

    public function save()
    {
        $assit_day = AssitDay::find($this->assit_day_id);

        if ($assit_day) {

            $this->validate();

            $arr = [
                'name' => $this->name,
                'number_day' => $this->number_day
            ];

            $assit_day->update($arr);
            $this->editModeAssitDayOff();
            $this->emit('updateNameAssitDay');
            session()->flash('operp_ok', 'Guardado!!!.');
        }

    }

    protected $rules = [
        'name' => 'required|string',
        'number_day' => 'required|integer|max:7'
    ];
}
