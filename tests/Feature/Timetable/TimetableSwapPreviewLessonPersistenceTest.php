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
 * El intercambio de lecciones del paso 5 (drag a una celda ocupada) debe
 * persistir en la base de datos las posiciones de ambas lecciones.
 */
class TimetableSwapPreviewLessonPersistenceTest extends TestCase
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

    public function test_swap_preview_lessons_persists_both_lessons(): void
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

        $lessonA = $this->makeLesson($seccion, $profesor, $lapso, $pestudio, $calendar);
        $lessonB = $this->makeLesson($seccion, $profesor, $lapso, $pestudio, $calendar);

        // Slots persistidos previos (estado inicial: A en periodA, B en periodB).
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonA->id,
            'period_id' => $periodA->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonB->id,
            'period_id' => $periodB->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $periodA->id]],
                    (string) $lessonB->id => [['period_id' => $periodB->id]],
                ],
                'unassigned' => [],
            ])
            ->call('movePreviewLesson', $lessonA->id, $periodA->id, $periodB->id);

        // Preview: las lecciones intercambiaron sus períodos.
        $assignment = $component->get('preview.assignment');
        $this->assertSame([$periodB->id], array_column($assignment[(string) $lessonA->id], 'period_id'));
        $this->assertSame([$periodA->id], array_column($assignment[(string) $lessonB->id], 'period_id'));

        // Base de datos: el intercambio quedó persistido.
        $this->assertSame(
            [$periodB->id],
            TimetableSlot::where('lesson_id', $lessonA->id)->pluck('period_id')->all(),
            'la lección A debe persistirse en el período destino',
        );
        $this->assertSame(
            [$periodA->id],
            TimetableSlot::where('lesson_id', $lessonB->id)->pluck('period_id')->all(),
            'la lección B debe persistirse en el período de origen',
        );
    }
}
