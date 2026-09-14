<?php

namespace App\Services\Timetable\Solver;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §4 — Resultado consolidado del orquestador:
 * la mejor solución de la cadena de intentos, más el detalle de intentos.
 */
final class SolverOutcome
{
    /**
     * @param  AttemptResult[]  $attempts
     */
    public function __construct(
        public readonly AttemptResult $best,
        public readonly array $attempts,
    ) {}

    public function toSolverResult(): SolverResult
    {
        return $this->best->result;
    }

    /**
     * HG-05 — Métricas de agrupación de medio-grupos de la mejor solución.
     *
     * @param  LessonToSchedule[]  $lessons
     * @return array{half_group_lessons:int, half_group_grouped_periods:int, half_group_isolated:int, half_group_unassigned:int}
     */
    public function halfGroupMetrics(array $lessons): array
    {
        $byId = [];
        foreach ($lessons as $lesson) {
            $byId[$lesson->lessonId] = $lesson;
        }

        $assignment = $this->best->result->assignment;
        $halfGroupLessons = 0;
        $cells = [];
        $assignedHalfGroupIds = [];

        foreach ($assignment as $lessonId => $slots) {
            $lesson = $byId[(int) $lessonId] ?? null;
            if (! $lesson || ! $lesson->isHalfGroup) {
                continue;
            }

            $halfGroupLessons++;
            $assignedHalfGroupIds[$lesson->lessonId] = true;

            foreach ($slots as $slot) {
                $key = $slot->periodId.':'.$lesson->seccionId;
                $cells[$key] = ($cells[$key] ?? 0) + 1;
            }
        }

        // Las lecciones de medio-grupo no asignadas también cuentan en el total.
        foreach ($lessons as $lesson) {
            if ($lesson->isHalfGroup && ! isset($assignedHalfGroupIds[$lesson->lessonId])) {
                $halfGroupLessons++;
            }
        }

        $groupedPeriods = 0;
        $isolated = 0;
        foreach ($cells as $count) {
            if ($count >= 2) {
                $groupedPeriods++;
            } else {
                $isolated++;
            }
        }

        $halfGroupUnassigned = $halfGroupLessons - count($assignedHalfGroupIds);

        return [
            'half_group_lessons' => $halfGroupLessons,
            'half_group_grouped_periods' => $groupedPeriods,
            'half_group_isolated' => $isolated,
            'half_group_unassigned' => max(0, $halfGroupUnassigned),
        ];
    }

    /**
     * Resumen por intento (para logs/observabilidad §9).
     *
     * @return list<array{id: string, assigned: int, unassigned: int, score: int, timed_out: bool, elapsed_ms: int}>
     */
    public function attemptSummary(): array
    {
        return array_map(fn (AttemptResult $attempt): array => [
            'id' => $attempt->id,
            'assigned' => $attempt->assignedBlocks,
            'unassigned' => $attempt->unassignedCount(),
            'score' => $attempt->qualityScore,
            'timed_out' => $attempt->result->timedOut,
            'elapsed_ms' => (int) round($attempt->result->elapsedSeconds * 1000),
        ], $this->attempts);
    }
}
