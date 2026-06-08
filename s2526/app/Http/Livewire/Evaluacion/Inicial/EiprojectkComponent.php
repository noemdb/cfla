<?php

namespace App\Http\Livewire\Evaluacion\Inicial;

use App\Models\app\Inicial\Eiprojectk;
use Livewire\Component;

class EiprojectkComponent extends Component
{
    public $eiprojectks;
    public $selectedEiprojectkId;
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
        $eiprojectk = Eiprojectk::findOrFail($id);
        $this->selectedEiprojectkId = $id;
        $this->observacion = $eiprojectk->observacion;
    }

    public function cancelForm()
    {
        $this->reset(['selectedEiprojectkId', 'observacion']);
    }

    public function saveObservacion()
    {
        $this->validate();

        $eiprojectk = Eiprojectk::findOrFail($this->selectedEiprojectkId);
        $eiprojectk->observacion = $this->observacion;

        $saved = $eiprojectk->save();

        if ($saved) {
            session()->flash('message', 'Observación registrada correctamente.');
            $this->cancelForm();
        } else {
            session()->flash('error', 'Error al guardar la observación.');
        }

        $this->showSwal('¡Excelente, buen trabajo!', 'Registro realizado exitosamente');
    }

    public function render()
    {
        $query = Eiprojectk::query();

        if ($this->grado_id) {
            $query->where('grado_id', $this->grado_id);
        }

        if ($this->profesor_id) {
            $query->where('profesor_id', $this->profesor_id);
        }

        $this->eiprojectks = $query->get();

        return view('livewire.evaluacion.inicial.eiprojectk-component');
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
