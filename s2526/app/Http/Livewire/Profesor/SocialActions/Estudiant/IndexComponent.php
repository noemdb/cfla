<?php

namespace App\Http\Livewire\Profesor\SocialActions\Estudiant;

use Livewire\Component;

class IndexComponent extends Component
{
    public $estudiants;
    protected $listeners = [ 'updateEsudiant'];

    public function updateEsudiant()
    {
        // $this->close();
    }

    public function render()
    {
        // $id = auth()->user()->id;
        $profesor = auth()->user()->profesor;
        $this->estudiants = $profesor->guia_estudiants;
        return view('livewire.profesor.social-actions.estudiant.index-component');
    }
}
