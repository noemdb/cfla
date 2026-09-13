<?php

namespace App\Services\Timetable\Solver;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §4 — Resultado de un intento del solver,
 * con métricas para comparar intentos y quedarse con el mejor.
 */
final class AttemptResult
{
    public function __construct(
        public readonly string $id,
        public readonly SolverResult $result,
        public readonly int $assignedBlocks,
        public readonly int $qualityScore,
    ) {}

    public function unassignedCount(): int
    {
        return count($this->result->unassigned);
    }

    public function isComplete(): bool
    {
        return $this->result->isComplete();
    }
}
