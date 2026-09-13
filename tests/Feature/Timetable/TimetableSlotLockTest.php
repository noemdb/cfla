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
 * Toggle de bloqueo por slot en el preview (timetable_slots.locked): si el
 * bloque está bloqueado, no se puede mover ni retirar.
 */
class TimetableSlotLockTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_toggle_slot_lock_persists_and_blocks_move_remove(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1501', 'status_active' => 'true',
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
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        // Slot persistido (para verificar la persistencia del lock).
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
            'period_id' => $periodA->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [(string) $lesson->id => [['period_id' => $periodA->id]]],
                'unassigned' => [],
            ])
            ->call('togglePreviewSlotLock', $lesson->id, $periodA->id);

        // Preview marcado como bloqueado.
        $assignment = $component->get('preview.assignment');
        $this->assertTrue((bool) $assignment[(string) $lesson->id][0]['locked']);
        // Persistido en la BD.
        $this->assertTrue((bool) TimetableSlot::query()
            ->where('lesson_id', $lesson->id)
            ->where('period_id', $periodA->id)
            ->value('locked'));

        // Mover y retirar el bloque bloqueado debe quedar bloqueado.
        $component->call('movePreviewLesson', $lesson->id, $periodA->id, $periodB->id);
        $assignment = $component->get('preview.assignment');
        $this->assertSame(
            [$periodA->id],
            array_column($assignment[(string) $lesson->id], 'period_id'),
            'el bloque bloqueado no debe poder moverse',
        );

        $component->call('removePreviewLesson', $lesson->id, $periodA->id);
        $assignment = $component->get('preview.assignment');
        $this->assertSame(
            [$periodA->id],
            array_column($assignment[(string) $lesson->id], 'period_id'),
            'el bloque bloqueado no debe poder retirarse',
        );
    }
}
