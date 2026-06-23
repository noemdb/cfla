<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitHour;

use App\Models\app\Assistcontrol\AssitHour;
use App\Models\app\Assistcontrol\AssitTurn;
use Livewire\Component;

class IndexComponent extends Component
{

    //'assit_turn_id','h','m','type'
    public $list_comment_assit_hour;
    public $assit_turn_id,$h,$m,$type;
    public $assit_turn,$assit_hours;
    public $editModeAssitTurn=false;

    public function mount($id)
    {
        $this->assit_day_id = $id;
        $assit_turn = AssitTurn::find($id);
        if ($assit_turn) {
            $this->assit_turn = $assit_turn;
            $this->assit_hours = AssitHour::where('assit_turn_id',$assit_turn->id)->get();
        }
        $this->list_comment_assit_hour = AssitHour::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-hour.index-component');
    }
}
