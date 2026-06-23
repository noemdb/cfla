<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitDay;

use App\Models\app\Assistcontrol\AssitDay;
use Livewire\Component;

class NameComponent extends Component
{

    public $assit_day_id,$assit_day;
    protected $listeners = ['updateNameAssitDay'];

    public function mount($id)
    {
        $this->assit_day = AssitDay::find($id);
    }

    public function updateNameAssitDay()
    {
        $this->assit_day = ($this->assit_day) ? AssitDay::find($this->assit_day->id) : null;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-day.name-component');
    }
}
