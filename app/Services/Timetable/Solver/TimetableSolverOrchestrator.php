<?php

namespace App\Services\Timetable\Solver;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §4/§5 — Orquestador del solver.
 *
 * Ejecuta una cadena de intentos (fallback) sobre el mismo problema, cada uno
 * con distinto orden de lecciones/semilla, y conserva la mejor solución por
 * cobertura y, en empate, por score soft. La recursividad está acotada por un
 * presupuesto global y un deadline por intento (nunca explota sin control).
 *
 * Sólo mejora el residual factible: no puede ubicar bloques que exceden la
 * capacidad real (eso lo reporta TimetableCapacityAuditService).
 */
final class TimetableSolverOrchestrator
{
    /**
     * @param  LessonToSchedule[]  $lessons
     * @param  array<int, list<int>>  $availablePeriodsByTeacher
     * @param  array<string, list<int>>  $roomsByType
     * @param  array<int, array{day: int, order: int}>  $periodMeta
     */
    public function __construct(
        private array $lessons,
        private array $availablePeriodsByTeacher,
        private array $roomsByType,
        private array $periodMeta = [],
        private int $budgetSeconds = 30,
        private int $maxSubjectsPerPeriod = 2,
        private int $restarts = 6,
        private int $attemptSeconds = 8,
    ) {}

    public function solve(): SolverOutcome
    {
        $started = microtime(true);
        $deadline = $started + max(1, $this->budgetSeconds);

        $attempts = [];
        $best = null;

        foreach ($this->attemptConfigs() as $config) {
            $remaining = $deadline - microtime(true);

            if ($remaining <= 0) {
                break;
            }

            $solver = new TimetableSolver(
                $this->lessons,
                $this->availablePeriodsByTeacher,
                $this->roomsByType,
                $this->periodMeta,
                max(1, min($config->timeLimitSeconds, (int) ceil($remaining))),
                $this->maxSubjectsPerPeriod,
                $config,
            );

            $result = $solver->solve();
            $attempt = new AttemptResult(
                $config->id,
                $result,
                $this->assignedBlocks($result->assignment),
                $solver->qualityScore($result->assignment),
            );

            $attempts[] = $attempt;

            if ($best === null || $this->isBetter($attempt, $best)) {
                $best = $attempt;
            }

            // Cobertura total con el mejor score posible de la cadena: no hay
            // nada más que ganar (early stop).
            if ($best->isComplete()) {
                break;
            }
        }

        return new SolverOutcome($best ?? $this->emptyOutcome(), $attempts);
    }

    /**
     * Cadena de estrategias: S1 conserva el orden original (con una porción
     * mayor del presupuesto); S2/S3 exploran otros órdenes; S4 reinicia con
     * semillas deterministas.
     *
     * @return list<SolverAttemptConfig>
     */
    private function attemptConfigs(): array
    {
        $firstAttemptSeconds = max($this->attemptSeconds, intdiv(max(1, $this->budgetSeconds), 2));

        $configs = [
            new SolverAttemptConfig('S1', SolverAttemptConfig::ORDER_CONSTRAINT, 0, $firstAttemptSeconds),
            new SolverAttemptConfig('S2', SolverAttemptConfig::ORDER_SCARCITY, 0, $this->attemptSeconds),
            new SolverAttemptConfig('S3', SolverAttemptConfig::ORDER_BLOCKS_DESC, 0, $this->attemptSeconds),
        ];

        for ($i = 0; $i < max(0, $this->restarts); $i++) {
            $configs[] = new SolverAttemptConfig('S4r'.$i, SolverAttemptConfig::ORDER_RANDOM, 1000 + $i, $this->attemptSeconds);
        }

        return $configs;
    }

    /**
     * @param  array<int, list<SlotCandidate>>  $assignment
     */
    private function assignedBlocks(array $assignment): int
    {
        return (int) array_sum(array_map('count', $assignment));
    }

    private function isBetter(AttemptResult $candidate, AttemptResult $current): bool
    {
        if ($candidate->assignedBlocks !== $current->assignedBlocks) {
            return $candidate->assignedBlocks > $current->assignedBlocks;
        }

        return $candidate->qualityScore > $current->qualityScore;
    }

    private function emptyOutcome(): AttemptResult
    {
        $lessonIds = array_map(fn (LessonToSchedule $lesson): int => $lesson->lessonId, $this->lessons);

        return new AttemptResult(
            'empty',
            new SolverResult([], $lessonIds),
            0,
            0,
        );
    }
}
