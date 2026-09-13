<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\Solver\LessonToSchedule;
use App\Services\Timetable\Solver\SchedulingContext;
use App\Services\Timetable\Solver\TimetableSolver;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-TIMETABLE-SHARED-TEACHER-001 §6/§10 — Regla de docente compartido:
 * solo se permite si AMBAS lessons lo autorizan y nunca una tercera.
 */
class SharedTeacherSolverTest extends TestCase
{
    private array $periods = [];

    private array $periodMeta = [];

    protected function setUp(): void
    {
        parent::setUp();

        $id = 1;
        $this->periods = [];
        $this->periodMeta = [];
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                $this->periods[$id] = ['day' => $day, 'order' => $order, 'shift' => 10];
                $this->periodMeta[$id] = ['day' => $day, 'order' => $order];
                $id++;
            }
        }
    }

    private function lesson(int $id, int $profesor, bool $shared, int $blocksT = 1): LessonToSchedule
    {
        return new LessonToSchedule(
            lessonId: $id,
            seccionId: 100 + $id,
            profesorId: $profesor,
            shiftId: 10,
            blocksT: $blocksT,
            blocksP: 0,
            allowSharedTeacher: $shared,
        );
    }

    public function test_two_shared_lessons_can_share_the_same_period(): void
    {
        // Un solo período disponible para el docente.
        $available = [101 => [1]];
        $lessons = [
            $this->lesson(1, 101, true),
            $this->lesson(2, 101, true),
        ];

        $solver = new TimetableSolver($lessons, $available, ['aula' => [1]], $this->periodMeta, 30, 2);
        $result = $solver->solve();

        $this->assertTrue($result->isComplete(), 'dos lessons de docente compartido pueden coincidir');
        $this->assertSame(1, $result->assignment[1][0]->periodId);
        $this->assertSame(1, $result->assignment[2][0]->periodId);
    }

    public function test_shared_plus_non_shared_is_rejected(): void
    {
        $available = [101 => [1]];
        $lessons = [
            $this->lesson(1, 101, true),
            $this->lesson(2, 101, false),
        ];

        $solver = new TimetableSolver($lessons, $available, ['aula' => [1]], $this->periodMeta, 30, 2);
        $result = $solver->solve();

        $this->assertCount(1, $result->assignment, 'una autorizada y otra no: no pueden coincidir');
    }

    public function test_third_shared_lesson_is_rejected(): void
    {
        $available = [101 => [1]];
        $lessons = [
            $this->lesson(1, 101, true),
            $this->lesson(2, 101, true),
            $this->lesson(3, 101, true),
        ];

        $solver = new TimetableSolver($lessons, $available, ['aula' => [1]], $this->periodMeta, 30, 2);
        $result = $solver->solve();

        $this->assertLessThan(3, count($result->assignment), 'una tercera lesson del mismo docente es rechazada');
    }

    public function test_context_allows_shared_pair_and_rejects_third(): void
    {
        $ctx = new SchedulingContext(2);

        $this->assertTrue($ctx->isFree(1, 101, 100, null, null, false, 1, true));
        $ctx->occupy(1, 101, 100, null, null, false, 1, true);

        $this->assertTrue($ctx->isFree(1, 101, 200, null, null, false, 2, true));
        $ctx->occupy(1, 101, 200, null, null, false, 2, true);

        $this->assertFalse($ctx->isFree(1, 101, 300, null, null, false, 3, true), 'tercera ocupación rechazada');
    }
}
