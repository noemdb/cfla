<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitTurn;

use App\Models\app\Assistcontrol\AssitTurn;
use Livewire\Component;

class NameComponent extends Component
{

    public $assit_turn_id,$assit_turn;
    protected $listeners = ['updateNameAssitTurn'];

    public function mount($id)
    {
        $this->assit_turn = AssitTurn::find($id);
    }

    public function updateNameAssitTurn()
    {
        $this->assit_turn = ($this->assit_turn) ? AssitTurn::find($this->assit_turn->id) : null;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-turn.name-component');
    }
}
