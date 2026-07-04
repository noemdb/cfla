<?php

namespace App\Http\Livewire\Evaluacion\Inicial;

use App\Models\app\Inicial\Eiplanningbwk;
use Livewire\Component;

class EiplanningbwkComponent extends Component
{
    public $eiplanningbwks;
    public $selectedEiplanningbwkId;
    public $showForm = false;
    public $profesor_id;
    public $grado_id;
    public $seccion_id;
    public $observacion;
    public $showStrategiesModal = false;

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
        $eiplanningbwk = Eiplanningbwk::findOrFail($id);
        $this->selectedEiplanningbwkId = $id;
        $this->observacion = $eiplanningbwk->observacion;
    }

    public function cancelForm()
    {
        $this->reset(['selectedEiplanningbwkId', 'observacion']);
    }

    public function saveObservacion()
    {
        $this->validate();

        $eiplanningbwk = Eiplanningbwk::findOrFail($this->selectedEiplanningbwkId);
        $eiplanningbwk->observacion = $this->observacion;

        $saved = $eiplanningbwk->save();

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
        $query = Eiplanningbwk::query();

        if ($this->grado_id) {
            $query->where('grado_id', $this->grado_id);
        }

        if ($this->profesor_id) {
            $query->where('profesor_id', $this->profesor_id);
        }

        $this->eiplanningbwks = $query->get();

        return view('livewire.evaluacion.inicial.eiplanningbwk-component');
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

    // Métodos para el modal de estrategias
    public function viewStrategies($id)
    {
        $this->selectedEiplanningwk = Eiplanningbwk::with(['grado', 'seccion', 'profesor'])->find($id);
        $this->showStrategiesModal = true;
    }

    public function closeStrategiesModal()
    {
        $this->showStrategiesModal = false;
        $this->selectedEiplanningwk = null;
    }
}
