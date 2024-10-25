<?php

namespace App\Livewire\App\Enrollment;

use Livewire\Component;

class MainComponent extends Component
{

    public $modalAssistent, $simpleModal, $modalSearch, $modalStart, $modalEmpty;
    public $ci;

    public function render()
    {
        return view('livewire.app.enrollment.main-component');
    }

    public function setStart()
    {
        $this->modalSearch = true;
        $this->modalStart = false;
        $this->modalAssistent = false;
        $this->modalEmpty = false;
    }

    public function search()
    {
        $url = env('APP_URL_SAEFL','.').'/general/enrollments/index/'.$this->ci;

        $this->dispatch('redireccionar', $url);

        $this->modalSearch = false;
        $this->modalStart = false;
        $this->modalAssistent = false;
        $this->modalEmpty = false;
    }
}
