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
 * El drag-and-drop del Paso 5 debe persistir el traslado en `timetable_slots`:
 * antes solo actualizaba el preview y al recargar la página se veía distinto.
 */
class TimetableMovePreviewLessonPersistTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_move_preview_lesson_persists_new_period_in_database(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create(['user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'L', 'ci_profesor' => '9801', 'status_active' => 'true']);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $pev = Pevaluacion::factory()->create(['profesor_id' => $profesor->id, 'seccion_id' => $seccion->id, 'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active']);
        $shift = $this->makeShift();
        $p1 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false]);
        $p2 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 1, 'order_in_day' => 2, 'is_break' => false]);
        $p3 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false]);

        $lesson = TimetableLesson::factory()->create(['calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id, 'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0]);

        TimetableSlot::factory()->create(['calendar_id' => $calendar->id, 'lesson_id' => $lesson->id, 'period_id' => $p1->id, 'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id]);
        TimetableSlot::factory()->create(['calendar_id' => $calendar->id, 'lesson_id' => $lesson->id, 'period_id' => $p2->id, 'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class, ['calendarId' => $calendar->id]);

        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p3->id);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p3->id,
        ]);

        $this->assertDatabaseMissing('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p1->id,
        ]);

        // El otro bloque de la lección permanece intacto.
        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
        ]);
    }
}
