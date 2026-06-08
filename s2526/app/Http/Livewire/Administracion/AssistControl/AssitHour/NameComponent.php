<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitHour;

use App\Models\app\Assistcontrol\AssitHour;
use Livewire\Component;

class NameComponent extends Component
{
    public $assit_hour_id,$assit_hour;
    protected $listeners = ['updateNameAssitHour'];

    public function mount($id)
    {
        $this->assit_hour = AssitHour::find($id);
    }

    public function updateNameAssitHour()
    {
        $this->assit_hour = ($this->assit_hour) ? AssitHour::find($this->assit_hour->id) : null;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-hour.name-component');
    }
}
