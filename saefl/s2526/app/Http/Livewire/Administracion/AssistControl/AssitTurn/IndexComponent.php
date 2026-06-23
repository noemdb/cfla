<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitTurn;

use App\Models\app\Assistcontrol\AssitDay;
use App\Models\app\Assistcontrol\AssitTurn;
use Livewire\Component;

class IndexComponent extends Component
{
    //'assit_day_id','name','number'
    public $list_comment_assit_number;
    public $assit_day_id,$name,$number;
    public $assit_day,$assit_turns;
    public $editModeAssitTurn=false;

    public function mount($id)
    {
        $this->assit_day_id = $id;
        $assit_day = AssitDay::find($id);
        if ($assit_day) {
            $this->assit_day = $assit_day;
            $this->assit_turns = AssitTurn::where('assit_day_id',$assit_day->id)->get();
        }

        $this->list_comment_assit_number = AssitTurn::COLUMN_COMMENTS;
    }

        public function render()
    {
        return view('livewire.administracion.assist-control.assit-turn.index-component');
    }
}
