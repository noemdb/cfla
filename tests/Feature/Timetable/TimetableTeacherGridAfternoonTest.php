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
 * El grid de un docente debe incluir las lessons del turno tarde aunque el
 * mismo order_in_day tenga un recreo del turno mañana (quirk del grid).
 */
class TimetableTeacherGridAfternoonTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_teacher_grid_includes_afternoon_slot_when_same_order_has_morning_break(): void
    {
        $user = User::factory()->create();
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1201', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);

        $morning = $this->makeShift('M', 'Mañana', '07:00:00', '12:30:00');
        $afternoon = $this->makeShift('T', 'Tarde', '13:00:00', '15:00:00');

        // Turno mañana: order 2 es recreo (se crea primero → id menor).
        TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $morning->id,
            'day_of_week' => 1, 'order_in_day' => 2, 'is_break' => true,
        ]);
        TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $morning->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        // Turno tarde: order 2 es clase (13:35).
        $afternoonPeriod = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $afternoon->id,
            'day_of_week' => 1, 'order_in_day' => 2, 'is_break' => false,
        ]);

        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $afternoon->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
            'period_id' => $afternoonPeriod->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
        ]);

        $grid = app(TimetableViewService::class)->gridForTeacher($calendar, $profesor->id);

        $cell = $grid->get(2)?->get(1) ?? collect();
        $this->assertSame(
            [$afternoonPeriod->id],
            $cell->pluck('period_id')->map(fn ($id): int => (int) $id)->all(),
            'el slot del turno tarde (order 2, día 1) debe aparecer en el grid del docente',
        );
    }
}
