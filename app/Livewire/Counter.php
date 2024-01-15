<?php

namespace App\Livewire;

use Livewire\Component;

use WireUi\Traits\Actions;

class Counter extends Component
{    
    use Actions;

    public $count = 0;
 
    public function increment()
    {
        $this->count++; 

        $this->notification()->success(
            $title = 'Profile saved',
            $description = 'Your profile was successfully saved'
        );

    }

    public function decrement()
    {
        $this->count--;

        $this->dialog()->success(
            $title = 'Profile saved',
            $description = 'Your profile was successfully saved'
        );
        
    }
 
    public function render()
    {
        return view('livewire.counter');
    }
}
