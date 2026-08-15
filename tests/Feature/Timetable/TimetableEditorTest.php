<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableEditor;
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
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §7 — Editor manual con validación de reglas duras.
 */
class TimetableEditorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_editor_renders_grid_for_active_calendar(): void
    {
        $fixture = $this->editorFixture();

        $user = User::factory()->create(['is_coordinacion' => true]);

        Livewire::actingAs($user)
            ->test(TimetableEditor::class, ['calendarId' => $fixture['calendar']->id])
            ->assertOk();
    }

    public function test_move_slot_validates_conflict_and_blocks(): void
    {
        $fixture = $this->editorFixture();
        $user = User::factory()->create(['is_coordinacion' => true]);

        $slot = $fixture['slot'];
        $newPeriod = $fixture['periods'][3];

        // El docente del slot ocupa el nuevo período con otro slot.
        $blockingSlot = TimetableSlot::factory()->create([
            'calendar_id' => $fixture['calendar']->id,
            'lesson_id' => $fixture['lessonB']->id,
            'period_id' => $newPeriod->id,
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $fixture['seccionB']->id,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableEditor::class, ['calendarId' => $fixture['calendar']->id])
            ->call('moveSlot', $slot->id, $newPeriod->id)
            ->assertNotSet('conflictMessage', null);

        // El slot no se movió.
        $this->assertSame($fixture['periods'][0]->id, $slot->fresh()->period_id);
        $this->assertNotSame($newPeriod->id, $slot->fresh()->period_id);
        $this->assertNotSame($blockingSlot->period_id, $slot->period_id);
    }

    public function test_move_slot_to_free_period_succeeds(): void
    {
        $fixture = $this->editorFixture();
        $user = User::factory()->create(['is_coordinacion' => true]);

        $freePeriod = $fixture['periods'][4];

        Livewire::actingAs($user)
            ->test(TimetableEditor::class, ['calendarId' => $fixture['calendar']->id])
            ->call('moveSlot', $fixture['slot']->id, $freePeriod->id)
            ->assertSet('conflictMessage', null);

        $this->assertSame($freePeriod->id, $fixture['slot']->fresh()->period_id);
        $this->assertTrue((bool) $fixture['slot']->fresh()->is_manual_override);
    }

    public function test_locked_slot_cannot_move(): void
    {
        $fixture = $this->editorFixture();
        $user = User::factory()->create(['is_coordinacion' => true]);

        $fixture['slot']->update(['locked' => true]);
        $freePeriod = $fixture['periods'][4];

        Livewire::actingAs($user)
            ->test(TimetableEditor::class, ['calendarId' => $fixture['calendar']->id])
            ->call('moveSlot', $fixture['slot']->id, $freePeriod->id)
            ->assertSet('conflictMessage', 'Este bloque está fijado (locked) y no se puede mover.');

        $this->assertSame($fixture['periods'][0]->id, $fixture['slot']->fresh()->period_id);
    }

    public function test_remove_slot_deletes_it(): void
    {
        $fixture = $this->editorFixture();
        $user = User::factory()->create(['is_coordinacion' => true]);

        Livewire::actingAs($user)
            ->test(TimetableEditor::class, ['calendarId' => $fixture['calendar']->id])
            ->call('removeSlot', $fixture['slot']->id);

        $this->assertDatabaseMissing('timetable_slots', ['id' => $fixture['slot']->id]);
    }

    // ─── Fixtures ──────────────────────────────────────────────

    private function editorFixture(): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '3001', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id]);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id]);

        $asignaturaA = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);
        $asignaturaB = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);

        $pensumA = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignaturaA->id]);
        $pensumB = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignaturaB->id]);

        $lapso = Lapso::factory()->create();
        $pevA = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionA->id,
            'pensum_id' => $pensumA->id, 'lapso_id' => $lapso->id,
        ]);
        $pevB = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionB->id,
            'pensum_id' => $pensumB->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = TimetableShift::factory()->create();

        $periods = [];
        for ($i = 0; $i < 6; $i++) {
            $periods[] = TimetablePeriod::factory()->create([
                'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                'day_of_week' => 1, 'order_in_day' => $i + 1, 'is_break' => false,
            ]);
        }

        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevA->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevB->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        $slot = TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonA->id,
            'period_id' => $periods[0]->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccionA->id,
        ]);

        return compact('calendar', 'shift', 'periods', 'lessonA', 'lessonB', 'profesor', 'seccionA', 'seccionB', 'slot');
    }
}
