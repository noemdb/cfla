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
 * El drag-and-drop del paso 5 debe bloquear el movimiento cuando la lección
 * colisiona con un slot preservado de otra sección (mismo docente).
 */
class TimetableMovePreviewLessonBlockingTest extends TestCase
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
        ]);
    }

    public function test_move_is_blocked_by_other_section_slot(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1301', 'status_active' => 'true',
        ]);

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

        $lessonA = $this->makeLesson($seccionA, $profesor, $lapso, $pestudio, $calendar, 0);
        $lessonB = $this->makeLesson($seccionB, $profesor, $lapso, $pestudio, $calendar, 1);

        // Slot preservado de la sección B en el período destino (mismo docente).
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonB->id,
            'period_id' => $periodB->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccionB->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $periodA->id]],
                ],
                'unassigned' => [],
            ])
            ->call('movePreviewLesson', $lessonA->id, $periodA->id, $periodB->id);

        // El movimiento debe quedar bloqueado: la lección A sigue en su período.
        $assignment = $component->get('preview.assignment');
        $this->assertSame(
            [$periodA->id],
            array_column($assignment[(string) $lessonA->id], 'period_id'),
            'el movimiento debe bloquearse por colisión con otra sección',
        );
    }
}
