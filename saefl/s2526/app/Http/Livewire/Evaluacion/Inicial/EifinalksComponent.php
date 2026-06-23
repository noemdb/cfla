<?php

namespace App\Http\Livewire\Evaluacion\Inicial;

use App\Models\app\Inicial\Eifinalk;
use Livewire\Component;

class EifinalksComponent extends Component
{
    public $eifinalks;
    public $selectedEifinalkId;
    public $showForm = false;

    public $profesor_id;
    public $seccion_id;
    public $lapso_id;

    public $recommendations;

    protected $rules = [
        'recommendations' => 'required|string|min:5',
    ];

    public function mount($profesor_id = null, $seccion_id = null, $lapso_id = null)
    {
        $this->profesor_id = $profesor_id;
        $this->seccion_id = $seccion_id;
        $this->lapso_id = $lapso_id;
    }

    public function showForm($id)
    {
        $eifinalk = Eifinalk::findOrFail($id);
        $this->selectedEifinalkId = $id;
        $this->recommendations = $eifinalk->recommendations;
        $this->showForm = true;
    }

    public function cancelForm()
    {
        $this->reset(['selectedEifinalkId', 'recommendations', 'showForm']);
    }

    public function saveRecommendations()
    {
        $this->validate();

        $eifinalk = Eifinalk::findOrFail($this->selectedEifinalkId);
        $eifinalk->recommendations = $this->recommendations;

        $this->showSwal('¡Excelente!', 'Registro realizado exitosamente');
    }

    public function render()
    {
        $query = Eifinalk::query();

        if ($this->profesor_id) {
            $query->byProfesor($this->profesor_id);
        }

        if ($this->lapso_id && $this->seccion_id) {
            $query->byLapsoYSeccion($this->lapso_id, $this->seccion_id);
        }

        $this->eifinalks = $query->get(); dd();
        
        return view('livewire.evaluacion.inicial.eifinalk-component');
    }

    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer' => 6000,
            'icon' => $icon,
        ]);
    }
}
