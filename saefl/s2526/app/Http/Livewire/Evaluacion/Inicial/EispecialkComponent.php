<?php

namespace App\Http\Livewire\Evaluacion\Inicial;

use Livewire\Component;
use App\Models\app\Inicial\Eispecialk;

class EispecialkComponent extends Component
{
    public $eispecialks;
    public $selectedEispecialkId;
    public $showForm = false;
    public $profesor_id;
    public $grado_id;
    public $seccion_id;
    public $observacion;

    protected $rules = [
        'observacion' => 'required|string|min:5',
    ];

    public function mount($profesor_id, $grado_id)
    {
        $this->profesor_id = $profesor_id;
        $this->grado_id = $grado_id;
    }

    public function showForm($id)
    {
        $eispecialk = Eispecialk::findOrFail($id);
        $this->selectedEispecialkId = $id;
        $this->observacion = $eispecialk->observacion;
    }

    public function cancelForm()
    {
        $this->reset(['selectedEispecialkId', 'observacion']);
    }

    public function saveObservacion()
    {
        $this->validate();

        $eispecialk = Eispecialk::findOrFail($this->selectedEispecialkId);
        $eispecialk->observacion = $this->observacion;

        if ($eispecialk->save()) {
            session()->flash('message', 'Observación registrada correctamente.');
            $this->cancelForm();
        } else {
            session()->flash('error', 'Error al guardar la observación.');
        }

        $this->showSwal('¡Excelente, buen trabajo!', 'Registro realizado exitosamente');
    }

    public function render()
    {
        $query = Eispecialk::query();

        if ($this->grado_id) {
            $query->where('grado_id', $this->grado_id);
        }

        if ($this->profesor_id) {
            $query->where('profesor_id', $this->profesor_id);
        }

        $this->eispecialks = $query->get();

        return view('livewire.evaluacion.inicial.eispecialk-component');
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
