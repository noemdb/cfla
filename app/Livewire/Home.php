<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public $model;
    public function render()
    {
        return view('livewire.home');
    }
}
