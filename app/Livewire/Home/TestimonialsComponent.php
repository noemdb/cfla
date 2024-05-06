<?php

namespace App\Livewire\Home;

use App\Models\app\Academy\Interrogation\InterviewAnswer;
use Livewire\Component;

class TestimonialsComponent extends Component
{
    public $testimonials,$users;

    public function mount()
    {
        $this->testimonials = InterviewAnswer::testimonials(4); //dd($this->testimonials);
    }

    public function render()
    {
        return view('livewire.home.testimonials-component');
    }
}
