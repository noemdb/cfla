<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * El horario del profesor debe incluir la carga de TODAS las secciones
 * (unión de slots del preview y slots persistidos).
 */
class TimetableTeacherScheduleGridTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function makePev(Seccion $seccion, Profesor $profesor, Lapso $lapso, Pestudio $pestudio, int $n): Pevaluacion
    {
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2 + $n, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $asignatura->id,
        ]);

        return Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $lapso->id,
        ]);
    }

    public function test_teacher_schedule_includes_all_sections(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9801', 'status_active' => 'true',
        ]);
        $pevA = $this->makePev($seccionA, $profesor, $lapso, $pestudio, 0);
        $pevB = $this->makePev($seccionB, $profesor, $lapso, $pestudio, 1);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();
        $periodA = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $periodB = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevA->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevB->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        // El preview solo contiene la sección A; la B tiene slot persistido.
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonB->id,
            'period_id' => $periodB->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccionB->id,
        ]);

        $wizard = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('teacherScheduleProfesorId', $profesor->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $periodA->id]],
                ],
            ]);

        $ref = new \ReflectionMethod(TimetableWizard::class, 'teacherScheduleGrid');
        $ref->setAccessible(true);
        $grid = $ref->invoke($wizard->instance());

        $lessonIds = [];
        foreach ($grid as $row) {
            foreach ($row['cells'] as $cells) {
                foreach ($cells as $cell) {
                    $lessonIds[] = (int) $cell['lesson_id'];
                }
            }
        }

        $this->assertContains((int) $lessonA->id, $lessonIds, 'la lección de la sección A (preview) debe aparecer');
        $this->assertContains((int) $lessonB->id, $lessonIds, 'la lección de la sección B (slot persistido) debe aparecer');
    }
}
