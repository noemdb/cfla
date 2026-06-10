<?php

namespace App\Livewire\Profesor\Competition;

use App\Models\app\Educational\DebateQuestion;
use Livewire\Form;

class QuestionForm extends Form
{
    public ?int $debate_id = null;
    public string $category = '';
    public string $text = '';
    public int $time = 30;
    public int $weighting = 1;
    public ?string $observation = null;
    public bool $status_active = true;

    /**
     * Rules for form validation.
     */
    public function rules()
    {
        return [
            'debate_id' => 'required|integer',
            'category'  => 'required|string',
            'text'      => 'required|string',
            'time'      => 'required|integer|min:0',
            'weighting' => 'required|integer|min:0',
            'observation' => 'nullable|string',
            'status_active' => 'nullable|boolean',
        ];
    }

    /**
     * Custom validation attribute names (Spanish).
     */
    public function validationAttributes()
    {
        return [
            'debate_id' => 'debate',
            'category' => 'categoría',
            'text' => 'texto de la pregunta',
            'time' => 'tiempo (segundos)',
            'weighting' => 'ponderación',
            'observation' => 'observación',
            'status_active' => 'estado activo',
        ];
    }

    /**
     * Fill the form from an existing DebateQuestion model.
     */
    public function fillFromModel(DebateQuestion $question): static
    {
        $this->debate_id = $question->debate_id;
        $this->category = $question->category;
        $this->text = $question->text;
        $this->time = $question->time ?? 30;
        $this->weighting = $question->weighting ?? 1;
        $this->observation = $question->observation;
        $this->status_active = $question->status_active ?? true;

        return $this;
    }

    /**
     * Apply form values to a DebateQuestion model (in-memory, not saved).
     */
    public function applyToModel(DebateQuestion $question, ?int $pensumId = null): DebateQuestion
    {
        $question->debate_id = $this->debate_id;

        if ($pensumId) {
            $question->pensum_id = $pensumId;
        }

        $question->category = $this->category;
        $question->text = $this->text;
        $question->time = $this->time;
        $question->weighting = $this->weighting;
        $question->observation = ($this->observation === '') ? null : $this->observation;
        $question->status_active = $this->status_active;

        return $question;
    }
}
