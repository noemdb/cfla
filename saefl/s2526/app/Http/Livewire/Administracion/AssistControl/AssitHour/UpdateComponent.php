<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitHour;

use App\Models\app\Assistcontrol\AssitHour;
use Livewire\Component;

class UpdateComponent extends Component
{

    //'assit_hour_id','h','m','type'
    public $list_comment_assit_hour;
    public $assit_hour_id,$h,$m,$type;
    public $assit_hour,$assit_hours;
    public $editModeAssitHour=false;

    public function mount($id)
    {
        $this->assit_day_id = $id;
        $assit_hour = AssitHour::find($id);
        if ($assit_hour) {
            $this->assit_hour = $assit_hour;
            $this->assit_hour_id = $assit_hour->id;
            $this->h = $assit_hour->h;
            $this->m = $assit_hour->m;
            $this->type = $assit_hour->type;
        }

        $this->list_comment_assit_hour = AssitHour::COLUMN_COMMENTS;
    }


    public function render()
    {
        return view('livewire.administracion.assist-control.assit-hour.update-component');
    }

    public function editModeAssitHourOn()
    {
        $this->editModeAssitHour = true;
    }
    public function editModeAssitHourOff()
    {
        $this->editModeAssitHour = false;
    }

    public function save()
    {
        $assit_hour = AssitHour::find($this->assit_hour_id);

        if ($assit_hour) {

            $this->validate();

            $arr = [
                'h' => $this->h,
                'm' => $this->m,
                'type' => $this->type,
            ];

            $assit_hour->update($arr);
            $this->editModeAssitHourOff();
            $this->emit('updateNameAssitHour');
            session()->flash('operp_ok', 'Guardado!!!.');
        }

    }

    protected $rules = [
        'h' => 'required|integer|max:23',
        'm' => 'required|integer|max:60',
        'type' => 'required|integer|max:1',
    ];
}
