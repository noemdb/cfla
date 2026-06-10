<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Achievement;
use Livewire\Form;

class AchievementForm extends Form
{
    public ?int $id = null;
    public string $name = '';
    public ?int $weighting = null;
    public bool $quantitative = false;
    public ?int $activity_id = null;

    /**
     * Reglas de validación.
     */
    public function rules()
    {
        return [
            'name' => 'required|min:6',
            'weighting' => 'nullable|numeric|min:0|max:20',
            'quantitative' => 'nullable|boolean',
        ];
    }

    /**
     * Atributos personalizados para errores de validación.
     */
    public function validationAttributes()
    {
        $comments = $this->getComponent()->list_comment ?? [];

        return [
            'name' => $comments['name'] ?? 'Nombre del indicador',
            'weighting' => $comments['weighting'] ?? 'Ponderación',
            'quantitative' => $comments['status_quantitative_weighting'] ?? 'Indicador ponderado',
        ];
    }

    /**
     * Toggle del switch de ponderación cuantitativa.
     */
    public function toggleWeighting(): void
    {
        $this->quantitative = ! $this->quantitative;
        if (! $this->quantitative) {
            $this->weighting = null;
        }
    }

    /**
     * Poblar el formulario desde un modelo Achievement existente.
     */
    public function fillFromModel(Achievement $achievement): static
    {
        $this->id = $achievement->id;
        $this->name = $achievement->name;
        $this->weighting = $achievement->weighting;
        $this->quantitative = (bool) $achievement->status_quantitative_weighting;
        $this->activity_id = $achievement->activity_id;

        return $this;
    }

    /**
     * Aplicar los valores del formulario a un modelo Achievement.
     */
    public function applyToModel(?Achievement $achievement = null): Achievement
    {
        if (! $achievement) {
            $achievement = new Achievement();
        }

        $achievement->name = $this->name;
        $achievement->status_quantitative_weighting = $this->quantitative;
        $achievement->weighting = $this->quantitative ? $this->weighting : null;

        if ($this->activity_id) {
            $achievement->activity_id = $this->activity_id;
        }

        return $achievement;
    }

    /**
     * Resetear el formulario a sus valores iniciales.
     */
    public function resetForm(): void
    {
        $this->reset();
    }
}
