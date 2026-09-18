<?php

namespace Tests\Feature\Timetable;

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
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use App\Services\Timetable\TimetableViewService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * El docente canónico de un slot es el de su Pevaluación; la columna
 * denormalizada `timetable_slots.profesor_id` puede quedar desfasada. La grilla,
 * los reportes por docente y el comando de reparación deben respetar esa regla.
 */
class TimetableTeacherDriftRepairTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{0: TimetableCalendar, 1: TimetableSlot, 2: Profesor, 3: Profesor, 4: TimetableLesson}
     */
    private function makeDriftedFixture(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pevProfesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Moises', 'lastname' => 'Ordoñez',
            'ci_profesor' => '17611704', 'status_active' => 'true',
        ]);
        $slotProfesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Maria', 'lastname' => 'D Lima',
            'ci_profesor' => '17500001', 'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $pevProfesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active']);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        $slot = TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
            'period_id' => $period->id, 'profesor_id' => $slotProfesor->id,
            'seccion_id' => $seccion->id,
        ]);

        return [$calendar, $slot, $pevProfesor, $slotProfesor, $lesson];
    }

    private function countSlotsInSchedules(array $schedules): int
    {
        $count = 0;
        foreach ($schedules as $schedule) {
            foreach ($schedule['grid'] as $row) {
                foreach ($row as $cell) {
                    $count += $cell->count();
                }
            }
        }

        return $count;
    }

    public function test_teacher_reports_resolve_profesor_from_pevaluacion_not_slot_column(): void
    {
        [$calendar, $slot, $pevProfesor, $slotProfesor, $lesson] = $this->makeDriftedFixture();
        $service = app(TimetableViewService::class);

        $teacherIds = $service->teacherIdsForCalendar($calendar);

        $this->assertContains((int) $pevProfesor->id, $teacherIds->all(), 'el docente de la Pevaluación debe aparecer');
        $this->assertNotContains((int) $slotProfesor->id, $teacherIds->all(), 'el profesor desfasado del slot no debe aparecer');

        $schedules = $service->teacherShiftSchedules($calendar, (int) $pevProfesor->id);
        $this->assertSame(1, $this->countSlotsInSchedules($schedules), 'el horario del docente de la Pevaluación incluye el slot');

        $empty = $service->teacherShiftSchedules($calendar, (int) $slotProfesor->id);
        $this->assertSame(0, $this->countSlotsInSchedules($empty), 'el profesor desfasado no recibe el slot');

        $this->assertSame((int) $slotProfesor->id, (int) $slot->fresh()->profesor_id, 'la consulta no altera la columna denormalizada');
    }

    public function test_repair_command_fixes_drifted_slots(): void
    {
        [$calendar, $slot, $pevProfesor] = $this->makeDriftedFixture();

        $this->artisan('timetable:repair-slot-teacher-drift', ['--calendar' => $calendar->id, '--force' => true])
            ->assertSuccessful();

        $this->assertSame((int) $pevProfesor->id, (int) $slot->fresh()->profesor_id);
    }

    public function test_repair_command_dry_run_does_not_change_data(): void
    {
        [$calendar, $slot, $pevProfesor, $slotProfesor] = $this->makeDriftedFixture();

        $this->artisan('timetable:repair-slot-teacher-drift', ['--calendar' => $calendar->id, '--dry-run' => true])
            ->assertSuccessful();

        $this->assertSame((int) $slotProfesor->id, (int) $slot->fresh()->profesor_id);
    }
}
