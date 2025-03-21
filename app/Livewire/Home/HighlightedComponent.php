<?php

namespace App\Livewire\Home;

use App\Models\app\Blog\Post;
use Livewire\Component;

class HighlightedComponent extends Component
{

    protected $listeners = ['hideVideo'];
    public bool $showVideo = true; // Estado inicial: mostrar video
    public function hideVideo()
    {
        $this->showVideo = false; // Ocultar video al finalizar
    }

    public $isVideoLoaded = false;
    public function videoLoaded()
    {
        $this->isVideoLoaded = true;
    }

    public function render()
    {
        return view('livewire.home.highlighted-component');
    }
}
