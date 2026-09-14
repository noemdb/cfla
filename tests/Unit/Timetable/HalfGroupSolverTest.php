<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\Solver\LessonToSchedule;
use App\Services\Timetable\Solver\SolverAttemptConfig;
use App\Services\Timetable\Solver\SlotCandidate;
use App\Services\Timetable\Solver\TimetableSolver;
use App\Services\Timetable\Solver\TimetableSolverOrchestrator;
use PHPUnit\Framework\TestCase;

/**
 * PLAN-TIMETABLE-HALFGROUP-001 — Prioridad y agrupación de medio-grupos:
 * HG-01 (orden), HG-02 (bonus por celda), HG-03 (estrategia) y HG-05 (métricas).
 */
class HalfGroupSolverTest extends TestCase
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
            for ($order = 1; $order <= 3; $order++) {
                $this->periodIds[] = $id;
                $this->periodMeta[$id] = ['day' => $day, 'order' => $order];
                $id++;
            }
        }
    }

    private function halfGroup(int $id, int $profesor, int $seccion, int $blocksT = 1): LessonToSchedule
    {
        return new LessonToSchedule(
            lessonId: $id,
            seccionId: $seccion,
            profesorId: $profesor,
            shiftId: 10,
            blocksT: $blocksT,
            blocksP: 0,
            isHalfGroup: true,
        );
    }

    private function whole(int $id, int $profesor, int $seccion, int $blocksT = 1): LessonToSchedule
    {
        return new LessonToSchedule(
            lessonId: $id,
            seccionId: $seccion,
            profesorId: $profesor,
            shiftId: 10,
            blocksT: $blocksT,
            blocksP: 0,
        );
    }

    /**
     * @param  LessonToSchedule[]  $lessons
     * @param  array<int, list<int>>  $available
     */
    private function solver(array $lessons, array $available, int $maxSubjects = 2): TimetableSolver
    {
        return new TimetableSolver(
            $lessons,
            $available,
            ['aula' => [1]],
            $this->periodMeta,
            30,
            $maxSubjects,
            new SolverAttemptConfig('HG', SolverAttemptConfig::ORDER_HALF_GROUP_FIRST),
            true,
            20,
        );
    }

    public function test_half_groups_of_same_section_share_a_period(): void
    {
        // Dos mitades de la MISMA sección y una sola celda: deben compartir.
        $lessons = [
            $this->halfGroup(1, 101, 500),
            $this->halfGroup(2, 102, 500),
        ];

        $result = $this->solver($lessons, [101 => [1], 102 => [1]])->solve();

        $this->assertTrue($result->isComplete());
        $this->assertSame(1, $result->assignment[1][0]->periodId);
        $this->assertSame(1, $result->assignment[2][0]->periodId);
    }

    public function test_half_group_is_scheduled_before_whole_group_under_scarcity(): void
    {
        // Mismo docente y una sola celda disponible: la lección explorada
        // primero se queda con el período. La prioridad debe favorecer al
        // medio-grupo frente al grupo completo.
        $halfGroup = $this->halfGroup(1, 101, 500);
        $wholeGroup = $this->whole(2, 101, 501);
        $available = [101 => [1]];

        $result = $this->solver([$wholeGroup, $halfGroup], $available)->solve();

        $this->assertArrayHasKey(1, $result->assignment, 'el medio-grupo debe quedarse con la única celda');
        $this->assertContains(2, $result->unassigned, 'el grupo completo queda sin asignar');
    }

    public function test_grouped_cell_is_preferred_over_empty_cell(): void
    {
        // La mitad A ya está fijada en el período 1; la mitad B tiene los
        // períodos 1 (con A) y 2 (vacío). El bonus debe llevarla al 1.
        $fixed = new LessonToSchedule(
            lessonId: 1,
            seccionId: 500,
            profesorId: 101,
            shiftId: 10,
            blocksT: 1,
            blocksP: 0,
            isHalfGroup: true,
            preassignedSlots: [new SlotCandidate(1, null, false)],
        );
        $free = $this->halfGroup(2, 102, 500);

        $result = $this->solver([$fixed, $free], [101 => [1], 102 => [1, 2]])->solve();

        $this->assertTrue($result->isComplete());
        $this->assertSame(1, $result->assignment[2][0]->periodId, 'debe agruparse en el período de su par');
    }

    public function test_max_subjects_per_period_is_respected(): void
    {
        // Tope 2: un tercer medio-grupo de la misma sección no cabe en la celda.
        $lessons = [
            $this->halfGroup(1, 101, 500),
            $this->halfGroup(2, 102, 500),
            $this->halfGroup(3, 103, 500),
        ];

        $result = $this->solver($lessons, [101 => [1], 102 => [1], 103 => [1]], maxSubjects: 2)->solve();

        $this->assertFalse($result->isComplete());
        $this->assertNotEmpty($result->unassigned);
    }

    public function test_priority_can_be_disabled(): void
    {
        // Sin prioridad, el orden por defecto no sesga el medio-grupo y el
        // resultado sigue siendo válido.
        $lessons = [
            $this->halfGroup(1, 101, 500),
            $this->whole(2, 102, 501),
        ];

        $solver = new TimetableSolver(
            $lessons,
            [101 => [1], 102 => [1]],
            ['aula' => [1]],
            $this->periodMeta,
            30,
            2,
            new SolverAttemptConfig('base', SolverAttemptConfig::ORDER_CONSTRAINT),
            false,
            0,
        );

        $result = $solver->solve();
        $this->assertCount(2, $result->assignment);
    }

    public function test_orchestrator_metrics_report_grouped_half_groups(): void
    {
        $lessons = [
            $this->halfGroup(1, 101, 500),
            $this->halfGroup(2, 102, 500),
        ];

        $outcome = (new TimetableSolverOrchestrator(
            lessons: $lessons,
            availablePeriodsByTeacher: [101 => [1], 102 => [1]],
            roomsByType: ['aula' => [1]],
            periodMeta: $this->periodMeta,
            budgetSeconds: 30,
            maxSubjectsPerPeriod: 2,
            restarts: 0,
            attemptSeconds: 8,
            halfGroupPriority: true,
            halfGroupBonus: 20,
        ))->solve();

        $metrics = $outcome->halfGroupMetrics($lessons);

        $this->assertSame(2, $metrics['half_group_lessons']);
        $this->assertSame(1, $metrics['half_group_grouped_periods']);
        $this->assertSame(0, $metrics['half_group_isolated']);
        $this->assertSame(0, $metrics['half_group_unassigned']);
    }
}
