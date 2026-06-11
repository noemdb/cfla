<?php

namespace App\Livewire\Forms\Planning;

use App\Models\app\Academy\Pensum;
use Livewire\Form;

class PensumForm extends Form
{
    // ─── FORM FIELDS ─────────────────────────────────────────────

    public $pestudio_id;
    public $grado_id;
    public $asignatura_id;
    public $status_component = 'false';
    public $status_active = true;
    public $status_active_diagnostic = false;
    public $observations;

    // Editing state
    public $isEditing = false;
    public $pensum_id;

    // ─── VALIDATION ──────────────────────────────────────────────

    protected function rules()
    {
        return [
            'pestudio_id' => 'required|integer|exists:pestudios,id',
            'grado_id' => 'required|integer|exists:grados,id',
            'asignatura_id' => 'required|integer|exists:asignaturas,id',
            'status_component' => 'required|in:true,false',
            'status_active' => 'required|boolean',
            'status_active_diagnostic' => 'required|boolean',
            'observations' => 'nullable|string|max:255',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'pestudio_id' => 'plan de estudio',
            'grado_id' => 'grado',
            'asignatura_id' => 'asignatura',
            'status_component' => 'componentes de formación',
            'status_active' => 'estado activo',
            'status_active_diagnostic' => 'diagnóstico activo',
            'observations' => 'observaciones',
        ];
    }

    // ─── LOADERS ─────────────────────────────────────────────────

    /**
     * Load form data from an existing Pensum for editing
     */
    public function loadFromPensum(Pensum $pensum): void
    {
        $this->pensum_id = $pensum->id;
        $this->isEditing = true;
        $this->pestudio_id = $pensum->pestudio_id;
        $this->grado_id = $pensum->grado_id;
        $this->asignatura_id = $pensum->asignatura_id;
        $this->status_component = $pensum->status_component;
        $this->status_active = $pensum->status_active;
        $this->status_active_diagnostic = $pensum->status_active_diagnostic;
        $this->observations = $pensum->observations;
    }

    // ─── RESET ───────────────────────────────────────────────────

    /**
     * Reset all form fields to defaults
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->isEditing = false;
        $this->pensum_id = null;
        $this->status_component = 'false';
        $this->status_active = true;
        $this->status_active_diagnostic = false;
    }

    // ─── DATA ────────────────────────────────────────────────────

    /**
     * Get the data array for create/update with normalized values
     */
    public function getData(): array
    {
        $val = $this->status_component;
        $normalizedComponent = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';

        return [
            'pestudio_id' => $this->pestudio_id,
            'grado_id' => $this->grado_id,
            'asignatura_id' => $this->asignatura_id,
            'status_component' => $normalizedComponent,
            'status_active' => filter_var($this->status_active, FILTER_VALIDATE_BOOLEAN),
            'status_active_diagnostic' => filter_var($this->status_active_diagnostic, FILTER_VALIDATE_BOOLEAN),
            'observations' => $this->observations ?: null,
        ];
    }

    // ─── SAVE ────────────────────────────────────────────────────

    /**
     * Validate uniqueness and save the pensum
     */
    public function save(): Pensum
    {
        // Validate uniqueness compound
        $exists = Pensum::where('pestudio_id', $this->pestudio_id)
            ->where('grado_id', $this->grado_id)
            ->where('asignatura_id', $this->asignatura_id);

        if ($this->isEditing) {
            $exists->where('id', '!=', $this->pensum_id);
        }

        throw_if(
            $exists->exists(),
            \RuntimeException::class,
            'Ya existe un pensum con ese plan de estudio, grado y asignatura.'
        );

        $data = $this->getData();

        if ($this->isEditing) {
            $pensum = Pensum::findOrFail($this->pensum_id);
            $pensum->update($data);
            return $pensum;
        }

        return Pensum::create($data);
    }
}
