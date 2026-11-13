<?php

namespace App\Services\Planning;

use App\Models\app\Academy\Activity;
use Carbon\Carbon;

/**
 * Mapea una actividad de planificación a una sesión didáctica editable.
 *
 * El resultado es una propuesta determinista: el usuario siempre puede
 * corregir título, momentos, evaluación, ODS e indicadores.
 */
class DidacticPlanBuilder
{
    private const INSTRUMENT_PATTERNS = [
        'lista de cotejo' => 'Lista de cotejo',
        'escala de estimación' => 'Escala de estimación',
        'escala de estimacion' => 'Escala de estimación',
        'rúbrica' => 'Rúbrica',
        'rubrica' => 'Rúbrica',
        'registro anecdótico' => 'Registro anecdótico',
        'registro anecdotic' => 'Registro anecdótico',
        'observación directa' => 'Observación directa',
        'observacion directa' => 'Observación directa',
        'prueba escrita' => 'Prueba escrita',
        'examen' => 'Prueba escrita',
        'autoevaluación' => 'Autoevaluación',
        'autoevaluacion' => 'Autoevaluación',
        'coevaluación' => 'Coevaluación',
        'coevaluacion' => 'Coevaluación',
    ];

    public function __construct(
        private readonly DidacticPlanPeriodResolver $periodResolver,
        private readonly DidacticPlanScorer $scorer,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildSession(Activity $activity, ?string $weekStart = null, ?string $weekEnd = null): array
    {
        $parsed = $activity->parseTeachingSections();
        $sections = $parsed['sections'];
        $warnings = [];

        $date = $this->periodResolver->normalizeDate($activity->finicial ?? null)
            ?? $this->periodResolver->normalizeDate($activity->ffinal ?? null);

        if ($date === null) {
            $warnings[] = 'La actividad no tiene una fecha válida; asigna una fecha dentro de la semana.';
        } elseif ($weekStart !== null && $weekEnd !== null
            && ! $this->periodResolver->isWithinWeek($date, $weekStart, $weekEnd)) {
            $warnings[] = 'La fecha de la actividad está fuera de la semana elegida.';
        }

        if ($parsed['missing'] !== []) {
            $warnings[] = 'Secuencia parcial: falta '.implode(', ', $parsed['missing']).'.';
        }

        $ods = $this->scorer->extractOds((string) $activity->observations);
        if ($ods === []) {
            $warnings[] = 'La actividad no menciona ODS; puedes agregarlos manualmente.';
        }

        $indicators = $this->buildIndicators($activity);

        if ($indicators === []) {
            $warnings[] = 'La actividad no tiene indicadores; agrega al menos uno o justifica la ausencia.';
        }

        $instrument = $this->extractInstrument((string) $activity->description);

        $evaluationDescription = trim((string) $activity->description);

        $payload = [
            'date' => $date,
            'day_index' => $date !== null ? Carbon::parse($date)->dayOfWeekIso : null,
            'title' => $this->limit((string) $activity->topic, 180),
            'thematic' => $this->limit((string) $activity->thematic, 180),
            'objective' => trim((string) $activity->learning) ?: null,
            'start' => $sections['INICIO'] ?? null,
            'development' => $sections['DESARROLLO'] ?? ($sections === [] ? trim((string) $activity->teaching) : null),
            'close' => $sections['CIERRE'] ?? null,
            'resources' => trim((string) $activity->references) ?: null,
            'evaluation_instrument' => $instrument,
            'evaluation_description' => $evaluationDescription !== '' ? $evaluationDescription : null,
            'observations' => trim((string) $activity->observations) ?: null,
            'ods' => $ods,
            'is_non_teaching' => false,
            'source_activity_id' => $activity->id ? (int) $activity->id : null,
            'source_field' => 'teaching',
            'source_excerpt' => trim((string) $activity->teaching) ?: null,
            'source_checksum' => $this->checksum($activity),
            'confidence' => round($this->scorer->score($activity, $weekStart, $weekEnd)['score'] / 100, 2),
            'indicators' => $indicators,
            'warnings' => $warnings,
        ];

        return $payload;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildIndicators(Activity $activity): array
    {
        $achievements = $activity->relationLoaded('achievements')
            ? $activity->achievements
            : ($activity->exists ? $activity->achievements()->get() : collect());

        $indicators = [];

        foreach ($achievements as $index => $achievement) {
            $name = trim((string) $achievement->name);

            if ($name === '') {
                continue;
            }

            $indicators[] = [
                'name' => $this->limit($name, 191),
                'weighting' => $achievement->weighting !== null ? (float) $achievement->weighting : null,
                'is_quantitative' => (bool) $achievement->status_quantitative_weighting,
                'origin' => 'activity',
                'source_achievement_id' => $achievement->id ? (int) $achievement->id : null,
                'sort_order' => $index,
            ];
        }

        return $indicators;
    }

    public function extractInstrument(string $description): ?string
    {
        $text = mb_strtolower($description);

        if (trim($text) === '') {
            return null;
        }

        foreach (self::INSTRUMENT_PATTERNS as $needle => $label) {
            if (str_contains($text, $needle)) {
                return $label;
            }
        }

        return null;
    }

    public function checksum(Activity $activity): string
    {
        return hash('sha256', implode('|', [
            (string) $activity->topic,
            (string) $activity->thematic,
            (string) $activity->teaching,
            (string) $activity->learning,
            (string) $activity->description,
            (string) $activity->observations,
            (string) $activity->references,
        ]));
    }

    private function limit(string $value, int $length): string
    {
        $value = trim($value);

        return mb_strlen($value) > $length ? mb_substr($value, 0, $length) : $value;
    }
}
