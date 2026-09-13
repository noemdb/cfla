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
 * En el grid del docente, una celda con dos lessons de docente compartido
 * (mismo período) debe mostrar AMBAS asignaturas.
 */
class TimetableTeacherGridSharedTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function makeLesson(
        Seccion $seccion,
        Profesor $profesor,
        Lapso $lapso,
        Pestudio $pestudio,
        TimetableCalendar $calendar,
        int $n,
    ): TimetableLesson {
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $seccion->grado_id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        return TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => 1, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
            'allow_shared_teacher' => true,
        ]);
    }

    public function test_teacher_grid_shows_both_shared_subjects_in_same_cell(): void
    {
        $user = User::factory()->create();
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1601', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id]);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $lessonA = $this->makeLesson($seccionA, $profesor, $lapso, $pestudio, $calendar, 0);
        $lessonB = $this->makeLesson($seccionB, $profesor, $lapso, $pestudio, $calendar, 1);

        // Ambos slots en el MISMO período (docente compartido).
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonA->id,
            'period_id' => $period->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccionA->id, 'allow_shared_teacher' => true,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonB->id,
            'period_id' => $period->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccionB->id, 'allow_shared_teacher' => true,
        ]);

        $grid = app(TimetableViewService::class)->gridForTeacher($calendar, $profesor->id);
        $cell = $grid->get(1)?->get(1) ?? collect();

        $this->assertCount(2, $cell, 'la celda debe contener ambas lessons del docente compartido');
        $this->assertSame(
            [$lessonA->id, $lessonB->id],
            $cell->pluck('lesson_id')->map(fn ($id): int => (int) $id)->sort()->values()->all(),
        );
    }
}
