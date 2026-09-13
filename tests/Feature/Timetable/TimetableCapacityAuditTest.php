<?php

namespace Tests\Feature\Timetable;

use App\Jobs\Timetable\GenerateTimetableJob;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use App\Models\User;
use App\Services\Timetable\TimetableCapacityAuditService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §10 (F1a/F1b) — Auditoría de capacidad:
 * separa infactibilidad real (C-1) de "no encontrado" por heurística.
 */
class TimetableCapacityAuditTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function calendarWithPeriods(int $days, int $perDay): array
    {
        $calendar = TimetableCalendar::factory()->create();
        $shift = $this->makeShift();
        $periods = [];

        for ($day = 1; $day <= $days; $day++) {
            for ($order = 1; $order <= $perDay; $order++) {
                $periods[] = TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id,
                    'shift_id' => $shift->id,
                    'day_of_week' => $day,
                    'order_in_day' => $order,
                    'is_break' => false,
                ]);
            }
        }

        return [$calendar, $shift, $periods];
    }

    private function makeProfesor(int $n): Profesor
    {
        $user = User::factory()->create();

        return Profesor::create([
            'user_id' => $user->id,
            'name' => "Prof{$n}",
            'lastname' => 'Test',
            'ci_profesor' => (string) (5000 + $n),
            'status_active' => 'true',
        ]);
    }

    private function makeSeccion(?Pestudio $pestudio = null, ?Grado $grado = null): Seccion
    {
        $pestudio ??= Pestudio::factory()->create();
        $grado ??= Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);

        return Seccion::factory()->create(['grado_id' => $grado->id]);
    }

    private function makeLesson(
        TimetableCalendar $calendar,
        TimetableShift $shift,
        Profesor $profesor,
        Seccion $seccion,
        int $blocks,
        bool $halfGroup = false,
    ): TimetableLesson {
        $grado = $seccion->grado;
        $pestudioId = $grado?->pestudio_id;
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudioId,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $lapso->id,
        ]);

        return TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => $blocks,
            'weekly_blocks_p' => 0,
            'is_half_group' => $halfGroup,
        ]);
    }

    private function audit(TimetableCalendar $calendar): \App\Services\Timetable\CapacityAuditReport
    {
        return app(TimetableCapacityAuditService::class)->audit($calendar);
    }

    public function test_section_within_capacity_has_no_overflow(): void
    {
        [$calendar, $shift] = $this->calendarWithPeriods(2, 3); // 6 períodos
        $seccion = $this->makeSeccion();
        $this->makeLesson($calendar, $shift, $this->makeProfesor(1), $seccion, 3);

        $report = $this->audit($calendar);

        $this->assertFalse($report->hasOverflow());
        $this->assertSame(0.0, $report->overflowBlocksSections());
        $this->assertSame([], $report->overCapacityLessonIds());
    }

    public function test_section_exceeding_periods_reports_overflow(): void
    {
        [$calendar, $shift] = $this->calendarWithPeriods(2, 3); // 6 períodos
        $seccion = $this->makeSeccion();
        $profesor = $this->makeProfesor(1);
        $lessonA = $this->makeLesson($calendar, $shift, $profesor, $seccion, 4);
        $lessonB = $this->makeLesson($calendar, $shift, $profesor, $seccion, 4);

        $report = $this->audit($calendar);

        $this->assertTrue($report->hasOverflow());
        $this->assertSame(2.0, $report->overflowBlocksSections()); // 8 - 6
        $this->assertCount(1, $report->overflowSections());
        $this->assertEqualsCanonicalizing(
            [$lessonA->id, $lessonB->id],
            $report->overCapacityLessonIds(),
        );
    }

    public function test_teacher_overflow_across_sections_is_reported_independently(): void
    {
        [$calendar, $shift] = $this->calendarWithPeriods(2, 3); // 6 períodos
        $profesor = $this->makeProfesor(1);
        $lessonA = $this->makeLesson($calendar, $shift, $profesor, $this->makeSeccion(), 4);
        $lessonB = $this->makeLesson($calendar, $shift, $profesor, $this->makeSeccion(), 4);

        $report = $this->audit($calendar);

        // Cada sección cabe (4 ≤ 6), pero el docente no (8 > 6).
        $this->assertSame(0.0, $report->overflowBlocksSections());
        $this->assertSame(2.0, $report->overflowBlocksTeachers());
        $this->assertCount(1, $report->overflowTeachers());
        $this->assertEqualsCanonicalizing(
            [$lessonA->id, $lessonB->id],
            $report->overCapacityLessonIds(),
        );
    }

    public function test_half_group_weighs_half(): void
    {
        [$calendar, $shift] = $this->calendarWithPeriods(1, 3); // 3 períodos
        $seccion = $this->makeSeccion();
        // 4 medios grupos × 2 bloques: 4×2×0.5 = 4 > 3 → overflow 1.
        $this->makeLesson($calendar, $shift, $this->makeProfesor(1), $seccion, 2, true);
        $this->makeLesson($calendar, $shift, $this->makeProfesor(2), $seccion, 2, true);
        $this->makeLesson($calendar, $shift, $this->makeProfesor(3), $seccion, 2, true);
        $this->makeLesson($calendar, $shift, $this->makeProfesor(4), $seccion, 2, true);

        $report = $this->audit($calendar);

        $this->assertSame(1.0, $report->overflowBlocksSections());
    }

    public function test_command_json_reports_overflow(): void
    {
        [$calendar, $shift] = $this->calendarWithPeriods(2, 3);
        $seccion = $this->makeSeccion();
        $profesor = $this->makeProfesor(1);
        $this->makeLesson($calendar, $shift, $profesor, $seccion, 4);
        $this->makeLesson($calendar, $shift, $profesor, $seccion, 4);

        $this->artisan('timetable:audit-capacity', ['--calendar' => $calendar->id])
            ->assertSuccessful();
    }

    public function test_dry_run_preview_classifies_capacity_exceeded(): void
    {
        [$calendar, $shift] = $this->calendarWithPeriods(2, 3); // 6 períodos
        $seccion = $this->makeSeccion();
        $profesor = $this->makeProfesor(1);
        $this->makeLesson($calendar, $shift, $profesor, $seccion, 4);
        $this->makeLesson($calendar, $shift, $profesor, $seccion, 4);

        GenerateTimetableJob::dispatchSync($calendar->id, dryRun: true);

        $payload = $calendar->fresh()->preview_payload;
        $this->assertSame([], $payload['unassigned_reasons']['not_found']);
        $this->assertNotEmpty($payload['unassigned_reasons']['capacity_exceeded']);
        $this->assertGreaterThan(0, $payload['unassigned_reasons']['capacity_summary']['overflow_blocks_sections']);
    }

    public function test_grid_asymmetry_between_days_is_flagged(): void
    {
        $calendar = TimetableCalendar::factory()->create();
        $shift = $this->makeShift();

        foreach ([1 => 2, 2 => 3] as $day => $count) {
            for ($order = 1; $order <= $count; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id,
                    'shift_id' => $shift->id,
                    'day_of_week' => $day,
                    'order_in_day' => $order,
                    'is_break' => false,
                ]);
            }
        }

        $this->makeLesson($calendar, $shift, $this->makeProfesor(1), $this->makeSeccion(), 1);

        $report = $this->audit($calendar);

        $this->assertTrue($report->isGridAsymmetric());
        $this->assertContains($shift->id, $report->asymmetricShifts);
        $this->assertFalse($report->hasIncompleteSetup());
    }

    public function test_used_shift_without_periods_is_incomplete_setup(): void
    {
        $calendar = TimetableCalendar::factory()->create();
        $shift = $this->makeShift();

        // Lección en un turno sin períodos definidos.
        $this->makeLesson($calendar, $shift, $this->makeProfesor(1), $this->makeSeccion(), 1);

        $report = $this->audit($calendar);

        $this->assertTrue($report->hasIncompleteSetup());
        $this->assertContains($shift->id, $report->incompleteShifts);
    }

    public function test_inactive_grades_are_excluded_from_capacity_audit(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $calendar = TimetableCalendar::factory()->create(['pestudio_id' => $pestudio->id]);
        $shift = $this->makeShift();

        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 6; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        // Grado activo: 1 bloque (cabe).
        $gradoActivo = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionActiva = Seccion::factory()->create(['grado_id' => $gradoActivo->id, 'status_active' => 'true']);
        $this->makeLesson($calendar, $shift, $this->makeProfesor(1), $seccionActiva, 1);

        // Grado inactivo: 40 bloques (excederían si se contaran).
        $gradoInactivo = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'false']);
        $seccionInactiva = Seccion::factory()->create(['grado_id' => $gradoInactivo->id, 'status_active' => 'true']);
        for ($i = 0; $i < 10; $i++) {
            $this->makeLesson($calendar, $shift, $this->makeProfesor(10 + $i), $seccionInactiva, 4);
        }

        $report = $this->audit($calendar);

        $this->assertSame(0.0, $report->overflowBlocksSections());
        $this->assertArrayHasKey($seccionActiva->id, $report->sections);
        $this->assertArrayNotHasKey($seccionInactiva->id, $report->sections);
    }
}
