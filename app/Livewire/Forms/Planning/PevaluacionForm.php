<?php

namespace App\Livewire\Forms\Planning;

use App\Models\app\Academy\Pevaluacion;
use Livewire\Form;

class PevaluacionForm extends Form
{
    // ─── FORM FIELDS ─────────────────────────────────────────────

    public $pestudio_id;
    public $grado_id;
    public $seccion_id;
    public $lapso_id;
    public $pensum_id;
    public $profesor_id;
    public $grupo_estable_id;
    public $escala_id;
    public $nota_type = 'ACUMULATIVA';
    public $status_note_report = true;
    public $status_official = true;
    public $status_baremo;
    public $objetivo;
    public $description;
    public $observations;
    public $category;

    // Editing state
    public $isEditing = false;
    public $pevaluacion_id;

    protected function rules()
    {
        return [
            'pestudio_id' => 'required|integer|exists:pestudios,id',
            'grado_id' => 'required|integer|exists:grados,id',
            'seccion_id' => 'required|integer|exists:seccions,id',
            'lapso_id' => 'required|integer|exists:lapsos,id',
            'pensum_id' => 'required|integer|exists:pensums,id',
            'profesor_id' => 'required|integer|exists:profesors,id',
            'grupo_estable_id' => 'nullable|integer|exists:grupo_estables,id',
            'escala_id' => 'nullable|integer|exists:escalas,id',
            'nota_type' => 'required|in:ACUMULATIVA,PROMEDIADA',
            'status_note_report' => 'required|boolean',
            'status_official' => 'required|boolean',
            'status_baremo' => 'nullable|string|max:255',
            'objetivo' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:500',
            'observations' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:255',
        ];
    }

    /**
     * Load form data from an existing Pevaluacion for editing
     */
    public function loadFromPevaluacion(Pevaluacion $pevaluacion): void
    {
        $this->pevaluacion_id = $pevaluacion->id;
        $this->isEditing = true;
        $this->pestudio_id = $pevaluacion->pensum->pestudio_id;
        $this->grado_id = $pevaluacion->seccion->grado_id;
        $this->seccion_id = $pevaluacion->seccion_id;
        $this->lapso_id = $pevaluacion->lapso_id;
        $this->pensum_id = $pevaluacion->pensum_id;
        $this->profesor_id = $pevaluacion->profesor_id;
        $this->grupo_estable_id = $pevaluacion->grupo_estable_id;
        $this->escala_id = $pevaluacion->escala_id;
        $this->nota_type = $pevaluacion->nota_type ?? 'ACUMULATIVA';
        $this->status_note_report = $pevaluacion->status_note_report ?? true;
        $this->status_official = $pevaluacion->status_official ?? true;
        $this->status_baremo = $pevaluacion->status_baremo;
        $this->objetivo = $pevaluacion->objetivo;
        $this->description = $pevaluacion->description;
        $this->observations = $pevaluacion->observations;
        $this->category = $pevaluacion->category;
    }

    /**
     * Reset all form fields to defaults
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->isEditing = false;
        $this->pevaluacion_id = null;
        $this->status_note_report = true;
        $this->status_official = true;
        $this->nota_type = 'ACUMULATIVA';
    }

    /**
     * Get the data array for create/update
     */
    public function getData(): array
    {
        return [
            'profesor_id' => $this->profesor_id,
            'pensum_id' => $this->pensum_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'grupo_estable_id' => $this->grupo_estable_id ?: null,
            'escala_id' => $this->escala_id ?: null,
            'nota_type' => $this->nota_type,
            'status_note_report' => filter_var($this->status_note_report, FILTER_VALIDATE_BOOLEAN),
            'status_official' => filter_var($this->status_official, FILTER_VALIDATE_BOOLEAN),
            'status_baremo' => $this->status_baremo ?: null,
            'objetivo' => $this->objetivo ?: null,
            'description' => $this->description ?: null,
            'observations' => $this->observations ?: null,
            'category' => $this->category ?: null,
        ];
    }
}
