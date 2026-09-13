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
