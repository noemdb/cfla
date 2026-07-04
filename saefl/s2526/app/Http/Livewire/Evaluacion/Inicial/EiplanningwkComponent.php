<?php

namespace App\Http\Livewire\Evaluacion\Inicial;

use App\Models\app\Inicial\Eiplanningwk;
use Livewire\Component;

class EiplanningwkComponent extends Component
{
    public $eiplanningwks;
    public $selectedEiplanningwkId;
    public $showForm = false;
    public $profesor_id;
    public $grado_id;
    public $seccion_id;
    public $observacion;

    // Nuevas propiedades para el modal de estrategias
    public $showStrategiesModal = false;
    public $selectedEiplanningwk = null;

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
        $eiplanningwk = Eiplanningwk::findOrFail($id);
        $this->selectedEiplanningwkId = $id;
        $this->observacion = $eiplanningwk->observacion;
    }

    public function cancelForm()
    {
        $this->reset(['selectedEiplanningwkId', 'observacion']);
    }

    public function saveObservacion()
    {
        $this->validate([
            'observacion' => 'required|string|min:5',
        ]);

        $eiplanningwk = Eiplanningwk::findOrFail($this->selectedEiplanningwkId);

        $eiplanningwk->observacion = $this->observacion;

        $saved = $eiplanningwk->save();

        if ($saved) {
            session()->flash('message', 'Observación registrada correctamente.');
            $this->cancelForm();
        } else {
            session()->flash('error', 'Error al guardar la observación.');
        }

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Registro realizado exitosamente';
        $this->showSwal($title,$html);
    }

    // Métodos para el modal de estrategias
    public function viewStrategies($id)
    {
        $this->selectedEiplanningwk = Eiplanningwk::with(['grado', 'seccion', 'profesor'])->find($id);
        $this->showStrategiesModal = true;
    }

    public function closeStrategiesModal()
    {
        $this->showStrategiesModal = false;
        $this->selectedEiplanningwk = null;
    }

    public function render()
    {
        $eiplanningwks = Eiplanningwk::query();
        $eiplanningwks = ($this->grado_id) ? $eiplanningwks->where('grado_id',$this->grado_id) : $eiplanningwks ;
        $eiplanningwks = ($this->profesor_id) ? $eiplanningwks->where('profesor_id',$this->profesor_id) : $eiplanningwks ;
        $this->eiplanningwks = $eiplanningwks->get();

        return view('livewire.evaluacion.inicial.eiplanningwk-component');
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
        ]);
    }
}