<?php

namespace App\Http\Livewire\Profesor\SocialActions\Itinerary;

use Livewire\Component;

class IndexComponent extends Component
{

    protected $listeners = [ 'updateItinerary','showSwal','alertConfirm','alertQuestion','remove' ];

    public function updateItinerary()
    {
        // $this->close();
    }

    public function render()
    {
        return view('livewire.profesor.social-actions.itinerary.index-component');
    }
}
