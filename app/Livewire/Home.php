<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public $model,$count;
    public function render()
    {
        return view('livewire.home');
    }
 
    public function increment()
    {
        $this->count++;
    }
 
    public function decrement()
    {
        $this->count--;
    }
}
