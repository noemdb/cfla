<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\Solver\LessonToSchedule;
use App\Services\Timetable\Solver\TimetableSolverOrchestrator;
use PHPUnit\Framework\TestCase;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §10 — Orquestador con fallback y
 * recursividad controlada (PHP puro, sin Eloquent).
 */
class TimetableSolverOrchestratorTest extends TestCase
{
    /** @var array<int, array{day: int, order: int}> */
    private array $periodMeta = [];

    /** @var list<int> */
    private array $periodIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->periodMeta = [];
        $this->periodIds = [];
        $id = 1;
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 6; $order++) {
                $this->periodIds[] = $id;
                $this->periodMeta[$id] = ['day' => $day, 'order' => $order];
                $id++;
            }
        }
    }

    private function lesson(int $id, int $profesor, int $blocksT = 3, int $blocksP = 0): LessonToSchedule
    {
        return new LessonToSchedule(
            lessonId: $id,
            seccionId: 100 + $id,
            profesorId: $profesor,
            shiftId: 10,
            blocksT: $blocksT,
            blocksP: $blocksP,
        );
    }

    /**
     * @param  LessonToSchedule[]  $lessons
     * @param  array<int, list<int>>|null  $available  profesorId => períodos
     */
    private function orchestrator(array $lessons, int $restarts = 6, ?array $available = null): TimetableSolverOrchestrator
    {
        if ($available === null) {
            $available = [];
            foreach (array_unique(array_map(fn (LessonToSchedule $l): int => $l->profesorId, $lessons)) as $profesorId) {
                $available[$profesorId] = $this->periodIds;
            }
        }

        return new TimetableSolverOrchestrator(
            lessons: $lessons,
            availablePeriodsByTeacher: $available,
            roomsByType: ['aula' => [1]],
            periodMeta: $this->periodMeta,
            budgetSeconds: 30,
            maxSubjectsPerPeriod: 2,
            restarts: $restarts,
            attemptSeconds: 8,
        );
    }

    public function test_feasible_dataset_is_complete_and_stops_early(): void
    {
        $lessons = [
            $this->lesson(1, 101, 3),
            $this->lesson(2, 102, 3),
        ];

        $outcome = $this->orchestrator($lessons)->solve();

        $this->assertTrue($outcome->best->isComplete());
        $this->assertCount(1, $outcome->attempts, 'debe parar en el primer intento completo');
        $this->assertSame([], $outcome->toSolverResult()->unassigned);
    }

    public function test_infeasible_dataset_runs_fallback_chain_and_keeps_best(): void
    {
        // 2 lecciones de 5 bloques con el mismo docente y solo 6 períodos:
        // imposible ubicar 10 bloques en 6 → siempre queda algo sin asignar.
        $lessons = [
            $this->lesson(1, 101, 5),
            $this->lesson(2, 101, 5),
        ];
        $available = [101 => array_slice($this->periodIds, 0, 6)];

        $outcome = $this->orchestrator($lessons, restarts: 6, available: $available)->solve();

        $this->assertFalse($outcome->best->isComplete());
        $this->assertNotEmpty($outcome->best->result->unassigned);
        $this->assertGreaterThan(0, $outcome->best->assignedBlocks);
        // Cadena completa: S1 + S2 + S3 + 6 restarts.
        $this->assertCount(9, $outcome->attempts);
        $this->assertSame($outcome->best->result, $outcome->toSolverResult());
    }

    public function test_no_attempt_violates_hard_constraints(): void
    {
        $lessons = [
            $this->lesson(1, 101, 4),
            $this->lesson(2, 101, 4),
        ];
        $available = [101 => array_slice($this->periodIds, 0, 6)];

        $outcome = $this->orchestrator($lessons, available: $available)->solve();

        $this->assertGreaterThan(1, count($outcome->attempts));

        foreach ($outcome->attempts as $attempt) {
            $teacherPeriods = [];
            foreach ($attempt->result->assignment as $slots) {
                foreach ($slots as $slot) {
                    $this->assertArrayNotHasKey(
                        $slot->periodId,
                        $teacherPeriods,
                        'el docente no puede tener dos bloques en el mismo período',
                    );
                    $teacherPeriods[$slot->periodId] = true;
                }
            }
        }
    }

    public function test_random_restarts_are_reproducible_by_seed(): void
    {
        $lessons = [
            $this->lesson(1, 101, 4),
            $this->lesson(2, 101, 4),
        ];

        $first = $this->orchestrator($lessons)->solve();
        $second = $this->orchestrator($lessons)->solve();

        $periodsOf = fn ($outcome): array => collect($outcome->best->result->assignment)
            ->map(fn ($slots) => collect($slots)->map(fn ($slot) => $slot->periodId)->sort()->values()->all())
            ->all();

        $this->assertSame($periodsOf($first), $periodsOf($second));
    }

    public function test_attempt_summary_reports_each_attempt(): void
    {
        $lessons = [
            $this->lesson(1, 101, 5),
            $this->lesson(2, 101, 5),
        ];
        $available = [101 => array_slice($this->periodIds, 0, 6)];

        $summary = $this->orchestrator($lessons, restarts: 2, available: $available)->solve()->attemptSummary();

        $this->assertSame(['S1', 'S2', 'S3', 'S4r0', 'S4r1'], array_column($summary, 'id'));
    }
}
