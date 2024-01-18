<?php

namespace App\Livewire\App\Enrollment;

use Livewire\Component;

class IndexComponent extends Component
{
    public $ci;
    public $step=0,$limit=6;
    public $modalRepresentant;

    public function render()
    {
        return view('livewire.app.enrollment.index-component');
    }

    public function search()
    {
        //dd($this->ci);
        $this->step=1;
        $this->modalRepresentant=true;
    }
}
