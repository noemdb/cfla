<?php

namespace Tests\Unit\Timetable;

use App\Services\Timetable\Solver\LessonToSchedule;
use App\Services\Timetable\Solver\SlotCandidate;
use App\Services\Timetable\Solver\TimetableSolver;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-TIMETABLE-001 §18 — Unit tests del solver (sin Eloquent, solo DTOs).
 */
class TimetableSolverTest extends TestCase
{
    /** 5 días × 6 períodos (turno mañana, sin recreos) = 30 períodos. */
    private array $periods;

    private array $periodMeta;

    /** lessonId => profesorId (para verificar doble-booking por docente). */
    private array $lessonProfesor = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->periods = [];
        $this->periodMeta = [];
        $id = 1;
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 6; $order++) {
                $this->periods[$id] = ['day' => $day, 'order' => $order, 'shift' => 10];
                $this->periodMeta[$id] = ['day' => $day, 'order' => $order];
                $id++;
            }
        }
    }

    /** Solver con un solo docente y aulas genéricas (sin tipo requerido). */
    private function solver(array $lessons, int $timeLimit = 30): TimetableSolver
    {
        $profesorIds = array_unique(array_map(fn (LessonToSchedule $l) => $l->profesorId, $lessons));
        $available = [];
        foreach ($profesorIds as $pid) {
            $available[$pid] = array_keys($this->periods);
        }

        return new TimetableSolver($lessons, $available, ['aula' => [1]], $this->periodMeta, $timeLimit);
    }

    private function lesson(
        int $id,
        int $profesor,
        int $blocksT = 3,
        int $blocksP = 0,
        ?string $roomType = null,
        int $priority = 0,
        bool $locked = false,
        array $lockedPeriods = [],
    ): LessonToSchedule {
        return new LessonToSchedule(
            lessonId: $id,
            seccionId: 100 + $id,
            profesorId: $profesor,
            shiftId: 10,
            blocksT: $blocksT,
            blocksP: $blocksP,
            roomTypeRequired: $roomType,
            priority: $priority,
            locked: $locked,
            lockedPeriodIds: $lockedPeriods,
        );
    }

    public function test_feasible_small_dataset_solves_without_conflicts(): void
    {
        // 2 docentes, 2 lecciones de 3 bloques c/u → factible en 5×6.
        $lessons = [
            $this->lesson(1, 101, 3),
            $this->lesson(2, 102, 3),
        ];
        $this->lessonProfesor = [1 => 101, 2 => 102];

        $result = $this->solver($lessons)->solve();

        $this->assertTrue($result->isComplete());
        $this->assertCount(2, $result->assignment);
        foreach ($result->assignment as $lessonId => $slots) {
            $this->assertCount(3, $slots, "lección {$lessonId} debe tener 3 bloques");
        }

        // Sin doble-booking de docente por período (docentes distintos pueden
        // compartir período: solo se veta "periodId:profesorId").
        $byPeriodTeacher = [];
        foreach ($result->assignment as $lessonId => $slots) {
            foreach ($slots as $slot) {
                $key = $slot->periodId.':'.$this->lessonProfesor[$lessonId];
                $this->assertArrayNotHasKey($key, $byPeriodTeacher, "período {$key} repetido");
                $byPeriodTeacher[$key] = true;
            }
        }
    }

    public function test_infeasible_dataset_reports_unassigned_without_double_booking(): void
    {
        // Un solo docente debe dictar 40 bloques en 30 períodos → infactible.
        $lessons = [
            $this->lesson(1, 101, 20),
            $this->lesson(2, 101, 20),
        ];
        $this->lessonProfesor = [1 => 101, 2 => 101];

        $result = $this->solver($lessons)->solve();

        $this->assertFalse($result->isComplete());
        $this->assertNotEmpty($result->unassigned);

        // Verifica que lo asignado no tenga doble-booking (período por docente).
        $byPeriodTeacher = [];
        foreach ($result->assignment as $lessonId => $slots) {
            foreach ($slots as $slot) {
                $key = $slot->periodId.':'.$this->lessonProfesor[$lessonId];
                $this->assertArrayNotHasKey($key, $byPeriodTeacher);
                $byPeriodTeacher[$key] = true;
            }
        }
    }

    public function test_blocks_t_and_blocks_p_are_respected(): void
    {
        $lessons = [$this->lesson(1, 101, blocksT: 3, blocksP: 2)];

        $result = $this->solver($lessons)->solve();

        $this->assertTrue($result->isComplete());
        $slots = $result->assignment[1];
        $practical = array_filter($slots, fn (SlotCandidate $s) => $s->isPractical);
        $theoretical = array_filter($slots, fn (SlotCandidate $s) => ! $s->isPractical);
        $this->assertCount(2, $practical);
        $this->assertCount(3, $theoretical);
    }

    public function test_practical_blocks_require_matching_room_type(): void
    {
        // Lección que exige laboratorio; el solver solo ofrece aula común (room 1).
        // Sin laboratorios en roomsByType, los bloques prácticos no caben.
        $lessons = [$this->lesson(1, 101, blocksT: 3, blocksP: 1, roomType: 'laboratorio')];

        $solver = new TimetableSolver(
            $lessons,
            [101 => array_keys($this->periods)],
            ['aula' => [1], 'laboratorio' => []],
            $this->periodMeta,
            30,
        );

        $result = $solver->solve();

        $this->assertFalse($result->isComplete());
        $this->assertContains(1, $result->unassigned);
    }

    public function test_locked_lessons_are_reserved_first_and_not_reassigned(): void
    {
        $lockedLesson = $this->lesson(1, 101, blocksT: 2, locked: true, lockedPeriods: [1, 2]);
        $freeLesson = $this->lesson(2, 102, blocksT: 3);
        $this->lessonProfesor = [1 => 101, 2 => 102];

        $result = $this->solver([$freeLesson, $lockedLesson])->solve();

        $this->assertTrue($result->isComplete());
        $lockedSlots = $result->assignment[1];
        $this->assertCount(2, $lockedSlots);
        $lockedPeriods = array_map(fn (SlotCandidate $s) => $s->periodId, $lockedSlots);
        $this->assertSame([1, 2], $lockedPeriods);

        // La lección libre NO puede pisar los períodos 1 y 2 del docente 101.
        $byPeriodTeacher = [];
        foreach ($result->assignment as $lessonId => $slots) {
            foreach ($slots as $slot) {
                $key = $slot->periodId.':'.$this->lessonProfesor[$lessonId];
                $this->assertArrayNotHasKey($key, $byPeriodTeacher);
                $byPeriodTeacher[$key] = true;
            }
        }
    }

    public function test_shift_mismatch_is_respected(): void
    {
        // Docente 101 solo disponible en períodos del turno mañana (1-30),
        // pero la lección pide turno tarde (otro shift). availablePeriodsByTeacher
        // ya lo filtra → dominio vacío → no asignada.
        $lessons = [$this->lesson(1, 101, 3)];

        $solver = new TimetableSolver(
            $lessons,
            [101 => []], // sin períodos disponibles (filtro shift/ausencia)
            [],
            $this->periodMeta,
            30,
        );

        $result = $solver->solve();

        $this->assertFalse($result->isComplete());
        $this->assertContains(1, $result->unassigned);
    }

    public function test_timeout_preserves_partial_solution(): void
    {
        // Dataset grande + timeout de 0.01s: debe conservar lo asignado y
        // marcar el resto como no asignadas (ADR-TT-009), no devolver vacío.
        $lessons = [];
        for ($i = 1; $i <= 8; $i++) {
            $lessons[] = $this->lesson($i, 1000 + $i, blocksT: 4);
            $this->lessonProfesor[$i] = 1000 + $i;
        }

        $result = $this->solver($lessons, timeLimit: 0)->solve();

        // timeout=0 → corta de inmediato sin lanzar; conserva lo asignado.
        $this->assertTrue($result->timedOut);

        // Si quedó algo asignado, verifica que no haya doble-booking.
        $byPeriodTeacher = [];
        foreach ($result->assignment as $lessonId => $slots) {
            foreach ($slots as $slot) {
                $key = $slot->periodId.':'.$this->lessonProfesor[$lessonId];
                $this->assertArrayNotHasKey($key, $byPeriodTeacher);
                $byPeriodTeacher[$key] = true;
            }
        }
    }

    public function test_null_rooms_do_not_collide_between_sections(): void
    {
        // Dos secciones (sin aula dedicada, roomId null) del MISMO docente
        // no pueden chocar; pero dos secciones de DOCENTES distintos en el
        // mismo período sin aula deben poder convivir (ADR-TT-008).
        $lessons = [
            $this->lesson(1, 101, 1),
            $this->lesson(2, 102, 1),
        ];

        // Los períodos 1 y 2 serán ocupados por ambos sin aula: no debe
        // colisionar por la clave "periodId:" vacía.
        $result = $this->solver($lessons)->solve();

        $this->assertTrue($result->isComplete());
    }

    public function test_lesson_with_more_blocks_than_base_pool_is_assignable(): void
    {
        // Una lección de 18 bloques T supera el tope base del pool (14).
        // El pool debe crecer adaptativamente (ADR-TT-011) y asignarla.
        $lessons = [$this->lesson(1, 101, blocksT: 18)];
        $this->lessonProfesor = [1 => 101];

        $result = $this->solver($lessons)->solve();

        $this->assertTrue($result->isComplete(), 'la lección de 18 bloques debe asignarse');
        $this->assertCount(18, $result->assignment[1]);
        $this->assertSame([], $result->unassigned);
    }

    public function test_lesson_with_25_blocks_assigns_without_exploding(): void
    {
        // 25 bloques T → pool adaptativo tope absoluto (26). C(26,25)=26
        // combinaciones: el corte temprano mantiene el CSP acotado.
        $lessons = [$this->lesson(1, 101, blocksT: 25)];
        $this->lessonProfesor = [1 => 101];

        $result = $this->solver($lessons)->solve();

        $this->assertTrue($result->isComplete());
        $this->assertCount(25, $result->assignment[1]);
        $this->assertLessThan(30, $result->elapsedSeconds);
    }
}
