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

    /** @var string Almacena el campo completo concatenado (INICIO + DESARROLLO + CIERRE) */
    public ?string $teaching = null;

    /** @var string Sección INICIO de la enseñanza globalizada */
    public ?string $teachingStart = null;

    /** @var string Sección DESARROLLO de la enseñanza globalizada */
    public ?string $teachingContent = null;

    /** @var string Sección CIERRE de la enseñanza globalizada */
    public ?string $teachingEnd = null;

    public ?string $learning = null;

    public ?string $observations = null;

    public ?string $description = null;

    public ?int $pevaluacion_id = null;

    /**
     * Concatena los tres segmentos en el campo teaching completo.
     */
    public function composeTeaching(): ?string
    {
        $parts = [];
        if ($this->teachingStart !== null && trim($this->teachingStart) !== '') {
            $parts[] = 'INICIO: '.trim($this->teachingStart);
        }
        if ($this->teachingContent !== null && trim($this->teachingContent) !== '') {
            $parts[] = 'DESARROLLO: '.trim($this->teachingContent);
        }
        if ($this->teachingEnd !== null && trim($this->teachingEnd) !== '') {
            $parts[] = 'CIERRE: '.trim($this->teachingEnd);
        }

        return ! empty($parts) ? implode(' ', $parts) : null;
    }

    public function buildTeaching(): void
    {
        $this->teaching = $this->composeTeaching();
    }

    /**
     * Descompone el teaching completo en los tres segmentos.
     *
     * Solo reconoce los marcadores que produce {@see composeTeaching()}
     * ("INICIO:", "DESARROLLO:", "CIERRE:"), en ese orden, y toma la primera
     * aparición de cada uno a partir del marcador anterior. Así el contenido
     * no se corrompe cuando el texto pedagógico menciona palabras sueltas como
     * "el inicio de la jornada", "durante el desarrollo" o "como cierre".
     */
    protected function parseTeaching(): void
    {
        $this->teachingStart = null;
        $this->teachingContent = null;
        $this->teachingEnd = null;

        $text = (string) $this->teaching;
        if (trim($text) === '') {
            return;
        }

        $labels = [
            'teachingStart' => 'INICIO',
            'teachingContent' => 'DESARROLLO',
            'teachingEnd' => 'CIERRE',
        ];

        $found = [];
        $cursor = 0;
        foreach ($labels as $property => $label) {
            $pattern = '/\b'.preg_quote($label, '/').'\b\s*:\s*/u';
            if (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $cursor)) {
                $found[$property] = [
                    'marker_start' => $matches[0][1],
                    'content_start' => $matches[0][1] + strlen($matches[0][0]),
                ];
                $cursor = $found[$property]['content_start'];
            }
        }

        $order = array_keys($labels);
        foreach ($order as $index => $property) {
            if (! isset($found[$property])) {
                continue;
            }

            $start = $found[$property]['content_start'];
            $end = strlen($text);
            foreach ($order as $nextIndex => $nextProperty) {
                if ($nextIndex > $index && isset($found[$nextProperty])) {
                    $end = $found[$nextProperty]['marker_start'];
                    break;
                }
            }

            $this->{$property} = trim(substr($text, $start, $end - $start));
        }
    }

    public function rules()
    {
        return [
            'finicial' => 'required|date',
            'ffinal' => 'required|date',
            'topic' => 'required|string',
            'thematic' => 'required|string',
            'references' => 'required|string',
            'teachingStart' => 'required|string',
            'teachingContent' => 'required|string',
            'teachingEnd' => 'required|string',
            'teaching' => 'nullable|string|max:30000',
            'learning' => 'nullable|string',
            'observations' => 'required|string',
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'teaching.max' => 'La enseñanza no debe superar los 30000 caracteres. Sintetiza el contenido (inicio, desarrollo y cierre).',
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
            'teachingStart' => $comments['teaching'] ?? 'INICIO - Enseñanza / Actividad Globalizada',
            'teachingContent' => $comments['teaching'] ?? 'DESARROLLO - Enseñanza / Actividad Globalizada',
            'teachingEnd' => $comments['teaching'] ?? 'CIERRE - Enseñanza / Actividad Globalizada',
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
        $this->parseTeaching();
        $this->learning = $activity->learning;
        $this->observations = $activity->observations;
        $this->description = $activity->description;
        $this->pevaluacion_id = $activity->pevaluacion_id;

        return $this;
    }

    /**
     * Poblar el formulario desde un array de datos (usado para copiar desde s2526).
     */
    public function fillFromArray(array $data): static
    {
        $this->finicial = $data['finicial'] ?? null;
        $this->ffinal = $data['ffinal'] ?? null;
        $this->topic = $data['topic'] ?? null;
        $this->thematic = $data['thematic'] ?? null;
        $this->references = $data['references'] ?? null;
        $this->teaching = $data['teaching'] ?? null;
        $this->parseTeaching();
        $this->learning = $data['learning'] ?? null;
        $this->observations = $data['observations'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->pevaluacion_id = $data['pevaluacion_id'] ?? null;

        return $this;
    }

    /**
     * Escribir los valores del formulario a un modelo Activity.
     */
    public function applyToModel(Activity $activity): Activity
    {
        // Construir el teaching completo desde los tres segmentos
        $this->buildTeaching();

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
