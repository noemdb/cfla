<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitTurn;

use App\Models\app\Assistcontrol\AssitDay;
use App\Models\app\Assistcontrol\AssitTurn;
use Livewire\Component;

class UpdateComponent extends Component
{

    //'assit_day_id','name','number'
    public $list_comment_assit_turn;
    public $assit_day_id,$assit_turn_id,$name,$number;
    public $assit_day,$assit_turn;
    public $editModeAssitTurn=false;

    public function mount($id)
    {
        $this->assit_day_id = $id;
        $assit_turn = AssitTurn::find($id);
        if ($assit_turn) {
            $this->assit_turn = $assit_turn;
            $this->assit_turn_id = $assit_turn->id;
            $this->assit_day_id = $assit_turn->assit_day_id;
            $this->name = $assit_turn->name;
            $this->number = $assit_turn->number;
        }

        $this->list_comment_assit_turn = AssitTurn::COLUMN_COMMENTS;
    }
    public function render()
    {
        return view('livewire.administracion.assist-control.assit-turn.update-component');
    }

    public function editModeAssitTurnOn()
    {
        $this->editModeAssitTurn = true;
    }
    public function editModeAssitTurnOff()
    {
        $this->editModeAssitTurn = false;
    }

    public function save()
    {
        $assit_turn = AssitTurn::find($this->assit_turn_id);

        if ($assit_turn) {

            $this->validate();

            $arr = [
                'name' => $this->name,
                'number' => $this->number
            ];

            $assit_turn->update($arr);
            $this->editModeAssitTurnOff();
            $this->emit('updateNameAssitTurn');
            session()->flash('operp_ok', 'Guardado!!!.');
        }

    }

    protected $rules = [
        'name' => 'required|string',
        'number' => 'required|integer|max:7'
    ];

}
