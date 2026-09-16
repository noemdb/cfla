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
 * Al agregar una lección desde el preview (botón «+»), el Paso 3 debe quedar
 * consistente: la pevaluación seleccionada y su configuración de lesson.
 */
class TimetableAddPreviewLessonRegistersStep3Test extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_add_preview_lesson_registers_it_in_step3(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9601', 'status_active' => 'true',
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
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [],
                'unassigned' => [$lesson->id],
            ])
            ->call('openAddPreviewLessonModal', $period->id)
            ->call('addPreviewLesson', $lesson->id);

        $assignment = $component->get('preview.assignment');
        $this->assertSame([$period->id], array_column($assignment[(string) $lesson->id], 'period_id'));

        $selected = $component->get('selectedPevs');
        $this->assertArrayHasKey($pev->id, $selected);

        // El paso 3 refleja el bloque realmente colocado (1 teórico), no la
        // demanda previa de la lesson.
        $lessons = $component->get('lessons');
        $this->assertSame(1, (int) ($lessons[$pev->id]['weekly_blocks_t'] ?? 0));
        $this->assertSame(0, (int) ($lessons[$pev->id]['weekly_blocks_p'] ?? 0));
        $this->assertSame((int) $shift->id, (int) ($lessons[$pev->id]['shift_id'] ?? 0));

        // La lesson persistida también queda sincronizada.
        $this->assertSame(1, (int) $lesson->fresh()->weekly_blocks_t);
        $this->assertSame(0, (int) $lesson->fresh()->weekly_blocks_p);
    }

    public function test_add_preview_lesson_persists_slot_to_database(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9603', 'status_active' => 'true',
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
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', ['assignment' => [], 'unassigned' => [$lesson->id]])
            ->call('openAddPreviewLessonModal', $period->id)
            ->call('addPreviewLesson', $lesson->id);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
        ]);
    }

    public function test_add_preview_lesson_counts_practical_block_in_step3(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9602', 'status_active' => 'true',
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
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', ['assignment' => [], 'unassigned' => [$lesson->id]])
            ->call('openAddPreviewLessonModal', $period->id)
            ->set('addPreviewLessonType', 'practice')
            ->call('addPreviewLesson', $lesson->id);

        // El bloque se marca práctico y el paso 3 incrementa P, no T.
        $this->assertTrue((bool) data_get(
            $component->get('preview.assignment'),
            (string) $lesson->id.'.0.is_practical',
        ));

        $lessons = $component->get('lessons');
        $this->assertSame(0, (int) ($lessons[$pev->id]['weekly_blocks_t'] ?? 0));
        $this->assertSame(1, (int) ($lessons[$pev->id]['weekly_blocks_p'] ?? 0));

        $this->assertSame(1, (int) $lesson->fresh()->weekly_blocks_p);
        $this->assertSame(0, (int) $lesson->fresh()->weekly_blocks_t);
    }

    public function test_add_preview_lesson_persists_even_when_teacher_has_collision(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Carmin', 'lastname' => 'Cortez',
            'ci_profesor' => '9604', 'status_active' => 'true',
        ]);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $existingPev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionA->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        $existingLesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $existingPev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        \App\Models\app\Timetable\TimetableSlot::create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $existingLesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccionA->id,
            'is_half_group' => false,
            'locked' => true,
            'is_manual_override' => true,
        ]);

        $newPev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionB->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        $newLesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $newPev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', ['assignment' => [], 'unassigned' => [$newLesson->id]])
            ->call('openAddPreviewLessonModal', $period->id)
            ->call('addPreviewLesson', $newLesson->id);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $newLesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
        ]);
    }
}
