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
 * El botón × (retirar del preview) debe borrar el slot de la BD y refrescar la
 * rejilla. Un bloque bloqueado, en cambio, se mantiene (toast «Bloque bloqueado»).
 */
class TimetableRemovePreviewLessonPersistTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{0: User, 1: TimetableCalendar, 2: Seccion, 3: TimetableLesson, 4: TimetablePeriod}
     */
    private function makeContext(bool $locked): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create(['user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'L', 'ci_profesor' => '9601', 'status_active' => 'true']);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $pev = Pevaluacion::factory()->create(['profesor_id' => $profesor->id, 'seccion_id' => $seccion->id, 'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active']);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false]);
        $lesson = TimetableLesson::factory()->create(['calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id, 'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0]);

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id, 'period_id' => $period->id,
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id, 'locked' => $locked,
        ]);

        return [$user, $calendar, $seccion, $lesson, $period];
    }

    public function test_remove_unlocked_preview_lesson_updates_database_and_grid(): void
    {
        [$user, $calendar, $seccion, $lesson, $period] = $this->makeContext(false);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class, ['calendarId' => $calendar->id])
            ->set('activeSeccionId', $seccion->id)
            ->set('currentStep', 5);

        $component->call('removePreviewLesson', $lesson->id, $period->id);

        $this->assertDatabaseMissing('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
        ]);

        $this->assertNotContains($lesson->id, $component->get('preview')['assignment'] ?? []);
    }

    public function test_remove_locked_preview_lesson_keeps_slot(): void
    {
        [$user, $calendar, $seccion, $lesson, $period] = $this->makeContext(true);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class, ['calendarId' => $calendar->id])
            ->set('activeSeccionId', $seccion->id)
            ->set('currentStep', 5);

        $component->call('removePreviewLesson', $lesson->id, $period->id);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
        ]);
    }
}
