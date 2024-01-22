<?php

namespace App\Livewire\Home;

use App\Models\app\Blog\Post;
use Livewire\Component;

class HighlightedComponent extends Component
{
    public $enrollment;
    public function mount($enrollment = null)
    {
        $this->enrollment = $enrollment;
    }

    public function render()
    {
        return view('livewire.home.highlighted-component');
    }
}
