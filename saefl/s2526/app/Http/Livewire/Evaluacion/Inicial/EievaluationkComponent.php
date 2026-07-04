<?php

namespace App\Http\Livewire\Evaluacion\Inicial;

use Livewire\Component;
use App\Models\app\Inicial\Eievaluationk;

class EievaluationkComponent extends Component
{
    public $eievaluationks;
    public $selectedEievaluationkId;
    public $showForm = false;
    public $profesor_id;
    public $grado_id;
    public $lapso_id;
    public $recomendacion;

    protected $rules = [
        'recomendacion' => 'required|string|min:5',
    ];

    public function mount($profesor_id = null, $grado_id = null, $lapso_id = null)
    {
        $this->profesor_id = $profesor_id;
        $this->grado_id = $grado_id;
        $this->lapso_id = $lapso_id;
    }

    public function showForm($id)
    {
        $eievaluationk = Eievaluationk::findOrFail($id);
        $this->selectedEievaluationkId = $id;
        $this->recomendacion = $eievaluationk->recomendacion;
    }

    public function cancelForm()
    {
        $this->reset(['selectedEievaluationkId', 'recomendacion']);
    }

    public function saveRecomendacion()
    {
        $this->validate();

        $eievaluationk = Eievaluationk::findOrFail($this->selectedEievaluationkId);
        $eievaluationk->recomendacion = $this->recomendacion;

        if ($eievaluationk->save()) {
            session()->flash('message', 'Recomendación registrada correctamente.');
            $this->cancelForm();
        } else {
            session()->flash('error', 'Error al guardar la Recomendación.');
        }

        $this->showSwal('¡Excelente, buen trabajo!', 'Registro realizado exitosamente');
    }

    public function render()
    {
        $query = Eievaluationk::query();

        if ($this->profesor_id) {
            $query->where('profesor_id', $this->profesor_id);
        }

        if ($this->grado_id) {
            $query->where('grado_id', $this->grado_id);
        }

        if ($this->lapso_id) {
            $query->where('lapso_id', $this->lapso_id);
        }

        $this->eievaluationks = $query->get();

        return view('livewire.evaluacion.inicial.eievaluationk-component');
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
