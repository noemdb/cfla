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
        private int $repairAttempts = 2,
        private ?\Closure $onAttempt = null,
        private bool $halfGroupPriority = false,
        private int $halfGroupBonus = 20,
        private bool $sharedTeacherPriority = false,
        private int $sharedTeacherBonus = 15,
    ) {}

    public function solve(): SolverOutcome
    {
        $started = microtime(true);
        $deadline = $started + max(1, $this->budgetSeconds);

        $attempts = [];
        $best = null;

        foreach ($this->attemptConfigs() as $config) {
            $this->runAttempt($config, $deadline, $attempts, $best);

            // Cobertura total: no hay nada más que ganar (early stop).
            if ($best !== null && $best->isComplete()) {
                return new SolverOutcome($best, $attempts);
            }
        }

        // Fase de reparación (TT-CFP-11): reintenta priorizando las lecciones
        // que quedaron sin asignar, para que el backtracking reubique a las
        // "bloqueantes" y les libere espacio.
        if ($best !== null && ! $best->isComplete()) {
            $priority = array_map('intval', $best->result->unassigned);

            for ($i = 0; $i < max(0, $this->repairAttempts) && $priority !== []; $i++) {
                if ($deadline - microtime(true) <= 0) {
                    break;
                }

                $config = new SolverAttemptConfig(
                    'S7r'.$i,
                    SolverAttemptConfig::ORDER_REPAIR,
                    5000 + $i,
                    $this->attemptSeconds,
                    $priority,
                );

                $this->runAttempt($config, $deadline, $attempts, $best);

                if ($best->isComplete()) {
                    break;
                }
            }
        }

        return new SolverOutcome($best ?? $this->emptyOutcome(), $attempts);
    }

    /**
     * Ejecuta un intento respetando el deadline global y actualiza el keep-best.
     *
     * @param  list<AttemptResult>  $attempts
     */
    private function runAttempt(
        SolverAttemptConfig $config,
        float $deadline,
        array &$attempts,
        ?AttemptResult &$best,
    ): void {
        $remaining = $deadline - microtime(true);

        if ($remaining <= 0) {
            return;
        }

        $solver = new TimetableSolver(
            $this->lessons,
            $this->availablePeriodsByTeacher,
            $this->roomsByType,
            $this->periodMeta,
            max(1, min($config->timeLimitSeconds, (int) ceil($remaining))),
            $this->maxSubjectsPerPeriod,
            $config,
            $this->halfGroupPriority,
            $this->halfGroupBonus,
            $this->sharedTeacherPriority,
            $this->sharedTeacherBonus,
        );

        $result = $solver->solve();
        $attempt = new AttemptResult(
            $config->id,
            $result,
            $this->assignedBlocks($result->assignment),
            $solver->qualityScore($result->assignment),
        );

        $attempts[] = $attempt;

        if ($this->onAttempt !== null) {
            ($this->onAttempt)($attempt);
        }

        if ($best === null || $this->isBetter($attempt, $best)) {
            $best = $attempt;
        }
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
        ];

        // HG-03: cuando la prioridad de medio-grupo está activa, se antepone un
        // intento que las coloca primero y las agrupa por sección.
        if ($this->halfGroupPriority) {
            $configs[] = new SolverAttemptConfig('S1h', SolverAttemptConfig::ORDER_HALF_GROUP_FIRST, 0, $this->attemptSeconds);
        }

        // ST-01: cuando la prioridad de docente compartido está activa, se
        // antepone un intento que las coloca primero y las agrupa por docente.
        if ($this->sharedTeacherPriority) {
            $configs[] = new SolverAttemptConfig('S1s', SolverAttemptConfig::ORDER_SHARED_TEACHER_FIRST, 0, $this->attemptSeconds);
        }

        $configs[] = new SolverAttemptConfig('S2', SolverAttemptConfig::ORDER_SCARCITY, 0, $this->attemptSeconds);
        $configs[] = new SolverAttemptConfig('S3', SolverAttemptConfig::ORDER_BLOCKS_DESC, 0, $this->attemptSeconds);

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
