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
 * Al retirar por completo una lección del preview (botón «×»), el Paso 3 debe
 * des-seleccionarla; al retirar solo un bloque, se mantiene seleccionada.
 */
class TimetableRemovePreviewLessonUnregistersStep3Test extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function fixture(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9901', 'status_active' => 'true',
        ]);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();
        $period1 = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $period2 = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        return compact('user', 'calendar', 'pev', 'lesson', 'period1', 'period2');
    }

    public function test_removing_last_block_unregisters_from_step3(): void
    {
        $f = $this->fixture();

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('preview', [
                'assignment' => [
                    (string) $f['lesson']->id => [
                        ['period_id' => $f['period1']->id],
                        ['period_id' => $f['period2']->id],
                    ],
                ],
                'unassigned' => [],
            ])
            ->set('selectedPevs', [$f['pev']->id => true])
            ->set('lessons', [$f['pev']->id => ['pev_id' => $f['pev']->id, 'weekly_blocks_t' => 2]])
            ->call('removePreviewLesson', $f['lesson']->id, $f['period1']->id);

        // Retiro parcial: sigue seleccionada en el Paso 3.
        $this->assertArrayHasKey($f['pev']->id, $component->get('selectedPevs'));
        $this->assertArrayHasKey($f['pev']->id, $component->get('lessons'));

        // El Paso 3 decrementa al bloque realmente restante (1) y la lesson
        // persistida queda sincronizada.
        $lessons = $component->get('lessons');
        $this->assertSame(1, (int) ($lessons[$f['pev']->id]['weekly_blocks_t'] ?? 0));
        $this->assertSame(1, (int) $f['lesson']->fresh()->weekly_blocks_t);

        $component->call('removePreviewLesson', $f['lesson']->id, $f['period2']->id);

        // Retiro completo: des-seleccionada del Paso 3 y demanda en cero.
        $this->assertArrayNotHasKey($f['pev']->id, $component->get('selectedPevs'));
        $this->assertArrayNotHasKey($f['pev']->id, $component->get('lessons'));
        $this->assertSame(0, (int) $f['lesson']->fresh()->weekly_blocks_t);
    }

    public function test_removing_block_deletes_persisted_slot_from_database(): void
    {
        $f = $this->fixture();
        \App\Models\app\Timetable\TimetableSlot::create([
            'calendar_id' => $f['calendar']->id,
            'lesson_id' => $f['lesson']->id,
            'period_id' => $f['period1']->id,
            'profesor_id' => $f['pev']->profesor_id,
            'seccion_id' => $f['pev']->seccion_id,
            'is_half_group' => false,
            'locked' => true,
            'is_manual_override' => true,
        ]);

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('preview', [
                'assignment' => [
                    (string) $f['lesson']->id => [
                        ['period_id' => $f['period1']->id],
                        ['period_id' => $f['period2']->id],
                    ],
                ],
                'unassigned' => [],
            ])
            ->call('removePreviewLesson', $f['lesson']->id, $f['period1']->id);

        // El bloque retirado se elimina también de la base de datos para que no
        // reaparezca al recargar el preview desde los slots persistidos.
        $this->assertDatabaseMissing('timetable_slots', [
            'calendar_id' => $f['calendar']->id,
            'lesson_id' => $f['lesson']->id,
            'period_id' => $f['period1']->id,
        ]);
    }
}
