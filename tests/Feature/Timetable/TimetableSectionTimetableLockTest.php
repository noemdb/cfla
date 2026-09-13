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
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Toggle de bloqueo del horario de una sección: si `timetable_locked` es true,
 * no se puede modificar el horario de esa sección.
 */
class TimetableSectionTimetableLockTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function makeLesson(
        Seccion $seccion,
        Profesor $profesor,
        Lapso $lapso,
        Pestudio $pestudio,
        TimetableCalendar $calendar,
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

    public function test_toggle_section_timetable_lock_persists(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('toggleSectionTimetableLock', $seccion->id);

        $this->assertTrue((bool) $seccion->fresh()->timetable_locked);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('toggleSectionTimetableLock', $seccion->id);

        $this->assertFalse((bool) $seccion->fresh()->timetable_locked);
    }

    public function test_move_is_blocked_when_section_locked(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1401', 'status_active' => 'true',
        ]);
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id]);
        $shift = $this->makeShift();
        $periodA = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $periodB = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = $this->makeLesson($seccion, $profesor, $lapso, $pestudio, $calendar);

        $seccion->update(['timetable_locked' => true]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [(string) $lesson->id => [['period_id' => $periodA->id]]],
                'unassigned' => [],
            ])
            ->call('movePreviewLesson', $lesson->id, $periodA->id, $periodB->id);

        $assignment = $component->get('preview.assignment');
        $this->assertSame(
            [$periodA->id],
            array_column($assignment[(string) $lesson->id], 'period_id'),
            'el movimiento debe bloquearse si el horario de la sección está bloqueado',
        );
    }
}
