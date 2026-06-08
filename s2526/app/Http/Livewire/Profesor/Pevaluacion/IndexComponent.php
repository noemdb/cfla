<?php

namespace App\Http\Livewire\Profesor\Pevaluacion;

use Livewire\Component;

class IndexComponent extends Component
{
    public $achievements;
    public $modeCreator;

    public function mount()
    {
        $this->achievements = collect();
        $this->modeCreator = false;
    }

    public function render()
    {
        return view('livewire.profesor.pevaluacion.index-component');
    }

    public function setCreate()
    {
        $this->modeCreator = true;
    }

    public function close()
    {
        $this->modeCreator = false;
    }
}
