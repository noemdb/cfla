<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\Solver\LessonToSchedule;
use App\Services\Timetable\Solver\SolverAttemptConfig;
use App\Services\Timetable\Solver\TimetableSolver;
use PHPUnit\Framework\TestCase;

/**
 * ADR-TT-017 — Solver con mitad de bloque: dos mitades secuenciales comparten
 * un período y un bloque completo excluye cualquier mitad.
 */
class HalfBlockSolverTest extends TestCase
{
    /** @var array<int, array{day: int, order: int}> */
    private array $periodMeta = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->periodMeta = [];
        $id = 1;
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                $this->periodMeta[$id] = ['day' => $day, 'order' => $order];
                $id++;
            }
        }
    }

    private function halfBlock(int $id, int $profesor, int $seccion, int $blocksT = 1): LessonToSchedule
    {
        return new LessonToSchedule(
            lessonId: $id,
            seccionId: $seccion,
            profesorId: $profesor,
            shiftId: 10,
            blocksT: $blocksT,
            blocksP: 0,
            halfBlock: true,
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
            new SolverAttemptConfig('HB', SolverAttemptConfig::ORDER_HALF_BLOCK_FIRST),
            false,
            0,
            false,
            0,
            true,
            15,
        );
    }

    public function test_two_half_blocks_fill_both_halves_of_one_period(): void
    {
        // Dos mitades de la MISMA sección y una sola celda: deben ocupar la
        // mitad superior y la inferior del mismo período.
        $lessons = [
            $this->halfBlock(1, 101, 500),
            $this->halfBlock(2, 102, 500),
        ];

        $result = $this->solver($lessons, [1 => [1], 2 => [1]])->solve();

        $this->assertTrue($result->isComplete());
        $this->assertSame(1, $result->assignment[1][0]->periodId);
        $this->assertSame(1, $result->assignment[2][0]->periodId);

        $halves = [$result->assignment[1][0]->half, $result->assignment[2][0]->half];
        sort($halves);
        $this->assertSame([0, 1], $halves, 'deben ocupar mitades distintas');
    }

    public function test_full_block_and_half_block_cannot_share_a_period(): void
    {
        $full = $this->whole(1, 101, 500);
        $half = $this->halfBlock(2, 102, 500);

        $result = $this->solver([$full, $half], [1 => [1], 2 => [1]])->solve();

        $this->assertFalse($result->isComplete());
        $this->assertCount(1, $result->unassigned);
    }

    public function test_half_block_only_occupies_one_half(): void
    {
        $result = $this->solver([$this->halfBlock(1, 101, 500)], [1 => [1]])->solve();

        $this->assertTrue($result->isComplete());
        $this->assertNotNull($result->assignment[1][0]->half);
    }

    public function test_teacher_reuses_period_across_halves_for_different_sections(): void
    {
        // Mismo docente, secciones distintas, una sola celda: pueden compartir
        // el período en mitades opuestas (secuencial).
        $lessons = [
            $this->halfBlock(1, 101, 500),
            $this->halfBlock(2, 101, 501),
        ];

        $result = $this->solver($lessons, [1 => [1], 2 => [1]])->solve();

        $this->assertTrue($result->isComplete());
        $this->assertNotSame($result->assignment[1][0]->half, $result->assignment[2][0]->half);
    }
}
