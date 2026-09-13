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
 * El botón «×» del preview debe retirar SOLO el bloque (celda) actual, no todos
 * los bloques de la asignatura.
 */
class TimetableRemovePreviewSlotTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_remove_preview_slot_removes_only_current_block(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9101', 'status_active' => 'true',
        ]);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id]);
        $shift = $this->makeShift();
        $first = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $second = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lesson->id => [
                        ['period_id' => $first->id],
                        ['period_id' => $second->id],
                    ],
                ],
                'unassigned' => [],
            ])
            ->call('removePreviewLesson', $lesson->id, $first->id);

        // Solo se retira el primer bloque; la lección sigue asignada.
        $assignment = $component->get('preview.assignment');
        $this->assertSame([$second->id], array_column($assignment[(string) $lesson->id], 'period_id'));
        $this->assertSame([], $component->get('preview.unassigned'));

        // Al retirar el último bloque, la lección queda sin asignar.
        $component->call('removePreviewLesson', $lesson->id, $second->id);

        $this->assertSame([], $component->get('preview.assignment'));
        $this->assertSame([$lesson->id], $component->get('preview.unassigned'));
    }
}
