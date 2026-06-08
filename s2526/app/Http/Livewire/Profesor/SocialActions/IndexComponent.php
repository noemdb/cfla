<?php

namespace App\Http\Livewire\Profesor\SocialActions;

use Livewire\Component;

class IndexComponent extends Component
{
    //public $modeCommunityAction,$modeCommunityHour,$modeCommunityCalendar,$modeItinerary;
    // protected $listeners = [ 'updateCommunityAction'];
    public $tabActivity,$tabAsistent,$tabCalendar,$tabItinerary,$tabEstudiant;

    //protected $listeners = [ 'updateCommunityHour' ];

    public function mount()
    {
        $this->tabActivity = true;
        $this->tabAsistent = false;
        $this->tabCalendar = false;
        $this->tabItinerary = false;
        $this->tabEstudiant = false;
    }

    public function render()
    {
        return view('livewire.profesor.social-actions.index-component');
    }

    public function updateCommunityAction()
    {
        $this->tabActivity = true;
        $this->tabAsistent = false;
        $this->tabCalendar = false;
        $this->tabItinerary = false;
        $this->tabEstudiant = false;
        $this->emit('updateCommunityAction');
    }

    public function updateCommunityHour()
    {
        $this->tabActivity = false;
        $this->tabAsistent = true;
        $this->tabCalendar = false;
        $this->tabItinerary = false;
        $this->tabEstudiant = false;
        $this->emit('updateCommunityHour');
    }

    public function updateCalendar()
    {
        $this->tabActivity = false;
        $this->tabAsistent = false;
        $this->tabCalendar = true;
        $this->tabItinerary = false;
        $this->tabEstudiant = false;
        $this->emit('updateCalendar');
    }

    public function updateItinerary()
    {
        $this->tabActivity = false;
        $this->tabAsistent = false;
        $this->tabCalendar = false;
        $this->tabEstudiant = false;
        $this->tabItinerary = true;
        $this->emit('updateItinerary');
    }

    public function updateEstudiant()
    {
        $this->tabActivity = false;
        $this->tabAsistent = false;
        $this->tabCalendar = false;
        $this->tabItinerary = false;
        $this->tabEstudiant = true;
        $this->emit('updateEsudiant');
    }
}
