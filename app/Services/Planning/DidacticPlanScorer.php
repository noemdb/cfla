<?php

namespace App\Services\Planning;

use App\Models\app\Academy\Activity;

/**
 * Puntúa la aptitud de una actividad para el plan de clases.
 *
 * La puntuación ordena y explica; no preselecciona por sí sola. La acción
 * "Seleccionar las afines al eje" exige además tema afín, fecha dentro de la
 * semana y un máximo de 10.
 */
class DidacticPlanScorer
{
    public const THEME_KEYWORDS = [
        'alimentación', 'alimentacion', 'alimentaria', 'nutrición', 'nutricion',
        'saludable', 'lonchera', 'comida', 'comidas', 'plato', 'platos',
        'gastronomía', 'gastronomia', 'cultura', 'cultural', 'identidad',
        'tradición', 'tradicion', 'tradiciones', 'arepa', 'hallaca', 'comida típica',
    ];

    public function __construct(private readonly DidacticPlanPeriodResolver $periodResolver) {}

    /**
     * @return array{
     *     score: int,
     *     level: string,
     *     reasons: array<int, string>,
     *     complete: bool,
     *     partial: bool,
     *     theme_affinity: bool,
     *     within_week: bool,
     *     indicator_count: int
     * }
     */
    public function score(Activity $activity, ?string $weekStart = null, ?string $weekEnd = null): array
    {
        $parsed = $activity->parseTeachingSections();
        $complete = $parsed['missing'] === [] && $parsed['sections'] !== [];
        $partial = ! $complete && $parsed['sections'] !== [];

        $hasEvaluation = trim((string) $activity->description) !== '';
        $hasOds = $this->extractOds((string) $activity->observations) !== [];
        $hasObjective = trim((string) $activity->learning) !== '';
        $indicatorCount = $this->indicatorCount($activity);
        $hasIndicators = $indicatorCount > 0;
        $themeAffinity = $this->hasThemeAffinity($activity);
        $withinWeek = $weekStart !== null && $weekEnd !== null
            && $this->periodResolver->activitiesInWeek([$activity], $weekStart, $weekEnd)->isNotEmpty();

        $score = 0;
        $reasons = [];

        if ($complete) {
            $score += 30;
            $reasons[] = 'Tiene inicio, desarrollo y cierre.';
        }
        if ($hasEvaluation) {
            $score += 20;
            $reasons[] = 'Tiene actividad evaluativa.';
        }
        if ($hasOds) {
            $score += 15;
            $reasons[] = 'Menciona ODS.';
        }
        if ($hasObjective) {
            $score += 10;
            $reasons[] = 'Tiene objetivo/aprendizaje.';
        }
        if ($hasIndicators) {
            $score += 10;
            $reasons[] = "Tiene {$indicatorCount} indicador(es).";
        }
        if ($themeAffinity) {
            $score += 10;
            $reasons[] = 'Tema afín: alimentación/cultura.';
        }
        if ($withinWeek) {
            $score += 5;
            $reasons[] = 'Fecha dentro de la semana.';
        }
        if ($partial) {
            $score -= 10;
            $reasons[] = 'Falta '.implode(', ', $parsed['missing']).'.';
        }

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'level' => $this->level($score),
            'reasons' => $reasons,
            'complete' => $complete,
            'partial' => $partial,
            'theme_affinity' => $themeAffinity,
            'within_week' => $withinWeek,
            'indicator_count' => $indicatorCount,
        ];
    }

    /**
     * Preselecciona solo actividades afines, dentro de la semana y con score ≥ 70.
     *
     * @param  iterable<int, Activity>  $activities
     * @return array<int, int> IDs ordenados por prioridad, máximo 10.
     */
    public function preselectForAxis(iterable $activities, string $weekStart, string $weekEnd, int $limit = 10): array
    {
        $candidates = [];

        foreach ($activities as $activity) {
            $result = $this->score($activity, $weekStart, $weekEnd);

            if ($result['score'] < 70 || ! $result['theme_affinity'] || ! $result['within_week']) {
                continue;
            }

            $date = $this->periodResolver->normalizeDate($activity->finicial ?? null) ?? '9999-12-31';

            $candidates[] = [
                'id' => (int) $activity->id,
                'score' => $result['score'],
                'date' => $date,
            ];
        }

        usort($candidates, function (array $a, array $b): int {
            return [$b['score'], $a['date'], $a['id']] <=> [$a['score'], $b['date'], $b['id']];
        });

        return array_slice(array_column($candidates, 'id'), 0, max(0, $limit));
    }

    public function level(int $score): string
    {
        return match (true) {
            $score >= 70 => 'completa',
            $score >= 40 => 'apta',
            $score >= 20 => 'parcial',
            default => 'debil',
        };
    }

    public function hasThemeAffinity(Activity $activity): bool
    {
        $text = mb_strtolower(trim(implode(' ', [
            (string) $activity->topic,
            (string) $activity->thematic,
        ])));

        if ($text === '') {
            return false;
        }

        foreach (self::THEME_KEYWORDS as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string> Códigos ODS detectados, sin duplicados.
     */
    public function extractOds(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        preg_match_all('/\bODS\s*[-:]?\s*(\d{1,2})\b/iu', $text, $matches);

        $codes = array_map(static fn ($code) => (string) (int) $code, $matches[1] ?? []);

        return array_values(array_unique($codes));
    }

    private function indicatorCount(Activity $activity): int
    {
        if ($activity->relationLoaded('achievements')) {
            return $activity->achievements->count();
        }

        if (isset($activity->achievements_count)) {
            return (int) $activity->achievements_count;
        }

        if (! $activity->exists) {
            return 0;
        }

        return $activity->achievements()->count();
    }
}
