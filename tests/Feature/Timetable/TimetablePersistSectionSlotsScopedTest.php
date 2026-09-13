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
 * "Guardar sección" solo debe persistir slots de la sección activa; las
 * asignaciones de otras secciones deben quedar intactas.
 */
class TimetablePersistSectionSlotsScopedTest extends TestCase
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
            'shift_id' => $shiftId = 1, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
    }

    public function test_persist_current_section_does_not_touch_other_sections(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1101', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();

        $lessonA = $this->makeLesson($seccionA, $profesor, $lapso, $pestudio, $calendar, 0);
        $lessonB = $this->makeLesson($seccionB, $profesor, $lapso, $pestudio, $calendar, 1);

        $periodA = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $periodNew = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $periodB = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 3, 'order_in_day' => 1, 'is_break' => false,
        ]);

        // Slot existente de la sección B (no debe cambiar).
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonB->id,
            'period_id' => $periodB->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccionB->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('activeSeccionId', $seccionA->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $periodNew->id]],
                ],
            ])
            ->call('persistCurrentSectionSlots');

        // La sección B conserva su slot intacto.
        $slotB = TimetableSlot::query()
            ->where('lesson_id', $lessonB->id)
            ->first();
        $this->assertNotNull($slotB);
        $this->assertSame($periodB->id, (int) $slotB->period_id, 'el slot de la sección B no debe modificarse');

        // La sección A quedó en el nuevo período.
        $slotA = TimetableSlot::query()
            ->where('lesson_id', $lessonA->id)
            ->first();
        $this->assertSame($periodNew->id, (int) $slotA->period_id);
    }
}
