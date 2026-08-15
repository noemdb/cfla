<?php

namespace App\Services\Timetable\Solver;

/**
 * SPEC-TIMETABLE-001 §6.1 — Resultado de solve().
 */
final class SolverResult
{
    /**
     * @param  array<int, list<SlotCandidate>>  $assignment  lessonId => slots
     * @param  array<int, int>  $unassigned  lessonIds sin asignar
     * @param  bool  $timedOut  true si se cortó por tiempo (ADR-TT-009)
     */
    public function __construct(
        public readonly array $assignment,
        public readonly array $unassigned,
        public readonly bool $timedOut = false,
        public readonly float $elapsedSeconds = 0.0,
    ) {}

    public function isComplete(): bool
    {
        return $this->unassigned === [];
    }
}
