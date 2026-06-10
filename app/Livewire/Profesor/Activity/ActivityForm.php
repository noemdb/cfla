<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Activity;
use Livewire\Form;

class ActivityForm extends Form
{
    public ?string $finicial = null;
    public ?string $ffinal = null;
    public ?string $topic = null;
    public ?string $thematic = null;
    public ?string $references = null;
    public ?string $teaching = null;
    public ?string $learning = null;
    public ?string $observations = null;
    public ?string $description = null;
    public ?int $pevaluacion_id = null;

    public function rules()
    {
        return [
            'finicial' => 'required|date',
            'ffinal' => 'required|date',
            'topic' => 'required|string',
            'thematic' => 'required|string',
            'references' => 'required|string',
            'teaching' => 'nullable|string',
            'learning' => 'nullable|string',
            'observations' => 'required|string',
            'description' => 'nullable|string',
        ];
    }

    public function validationAttributes()
    {
        $comments = $this->getComponent()->list_comment ?? [];

        return [
            'finicial' => $comments['finicial'] ?? 'Fecha Inicial',
            'ffinal' => $comments['ffinal'] ?? 'Fecha Final',
            'topic' => $comments['topic'] ?? 'Tema',
            'thematic' => $comments['thematic'] ?? 'Tejido Temático',
            'references' => $comments['references'] ?? 'Referentes',
            'teaching' => $comments['teaching'] ?? 'Enseñanza',
            'learning' => $comments['learning'] ?? 'Aprendizaje',
            'observations' => $comments['observations'] ?? 'Observaciones',
            'description' => $comments['description'] ?? 'Actividad Evaluativa',
        ];
    }

    /**
     * Poblar el formulario desde un modelo Activity existente.
     */
    public function fillFromModel(Activity $activity): static
    {
        $this->finicial = $activity->finicial;
        $this->ffinal = $activity->ffinal;
        $this->topic = $activity->topic;
        $this->thematic = $activity->thematic;
        $this->references = $activity->references;
        $this->teaching = $activity->teaching;
        $this->learning = $activity->learning;
        $this->observations = $activity->observations;
        $this->description = $activity->description;
        $this->pevaluacion_id = $activity->pevaluacion_id;

        return $this;
    }

    /**
     * Escribir los valores del formulario a un modelo Activity.
     */
    public function applyToModel(Activity $activity): Activity
    {
        $activity->finicial = $this->finicial;
        $activity->ffinal = $this->ffinal;
        $activity->topic = $this->topic;
        $activity->thematic = $this->thematic;
        $activity->references = $this->references;
        $activity->teaching = ($this->teaching === '') ? null : $this->teaching;
        $activity->learning = ($this->learning === '') ? null : $this->learning;
        $activity->observations = $this->observations;
        $activity->description = ($this->description === '') ? null : $this->description;

        if ($this->pevaluacion_id) {
            $activity->pevaluacion_id = $this->pevaluacion_id;
        }

        return $activity;
    }
}
