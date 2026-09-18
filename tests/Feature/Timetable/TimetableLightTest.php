<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Planning\Timetable\TimetableLight;
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
 * Asistente «light»: solo dos pasos (elegir calendario + grilla drag&drop).
 */
class TimetableLightTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{0: User, 1: TimetableCalendar, 2: Seccion, 3: TimetableLesson, 4: TimetablePeriod, 5: TimetablePeriod}
     */
    private function makeContext(): array
    {
        $user = User::factory()->create(['is_planner' => true]);
        $profesor = Profesor::create(['user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'L', 'ci_profesor' => '9401', 'status_active' => 'true']);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $pev = Pevaluacion::factory()->create(['profesor_id' => $profesor->id, 'seccion_id' => $seccion->id, 'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'name' => 'Horario Light', 'status' => 'active']);
        $shift = $this->makeShift();
        $p1 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false]);
        $p2 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false]);

        $lesson = TimetableLesson::factory()->create(['calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id, 'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0]);
        TimetableSlot::factory()->create(['calendar_id' => $calendar->id, 'lesson_id' => $lesson->id, 'period_id' => $p1->id, 'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id]);

        return [$user, $calendar, $seccion, $lesson, $p1, $p2];
    }

    public function test_light_starts_on_calendar_selection(): void
    {
        [$user, $calendar] = $this->makeContext();

        $component = Livewire::actingAs($user)->test(TimetableLight::class);

        $this->assertSame(1, $component->get('lightStep'));
        $component->assertSee('Horario · modo rápido');
        $component->assertSee($calendar->name);
    }

    public function test_light_groups_calendars_active_and_archived(): void
    {
        [$user, $calendar] = $this->makeContext();

        $archived = TimetableCalendar::factory()->create([
            'lapso_id' => $calendar->lapso_id,
            'pestudio_id' => $calendar->pestudio_id,
            'name' => 'Horario Archivado',
            'status' => 'archived',
        ]);

        $component = Livewire::actingAs($user)->test(TimetableLight::class);

        $component->assertSee('Activos');
        $component->assertSee('Archivados');
        $component->assertSee($calendar->name);
        $component->assertSee('Horario Archivado');
    }

    public function test_choose_calendar_opens_grid_and_persists_moves(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2] = $this->makeContext();

        $component = Livewire::actingAs($user)->test(TimetableLight::class);

        $component->call('chooseCalendar', $calendar->id);

        $this->assertSame(2, $component->get('lightStep'));
        $this->assertSame($calendar->id, (int) $component->get('calendarId'));
        $this->assertSame((int) $seccion->id, (int) $component->get('activeSeccionId'));
        $component->assertSee('Cambiar calendario');

        // Dropdown de formatos/reportes.
        $component->assertSee('Formatos');
        $component->assertSee('PDF todos P.Estudios');

        // La grilla debe ser arrastrable (drag & drop).
        $component->assertSee('x-on:drop.prevent', false);
        $component->assertSee('x-on:dragstart', false);
        $component->assertSee('draggable="true"', false);
        $component->assertSee('id="light-slot-'.$lesson->id.'-'.$p1->id.'"', false);

        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p2->id);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
        ]);
        $this->assertDatabaseMissing('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p1->id,
        ]);
    }

    public function test_light_route_is_accessible_for_planner(): void
    {
        [$user] = $this->makeContext();

        $this->actingAs($user)
            ->get('/app/planning/timetable/light')
            ->assertOk()
            ->assertSee('Horario · modo rápido');
    }

    public function test_light_can_add_a_lesson_from_the_grid(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        // La celda vacía debe ofrecer el botón de agregar.
        $component->assertSee('openAddPreviewLessonModal('.$p2->id.')', false);

        $component->call('openAddPreviewLessonModal', $p2->id);

        $this->assertTrue($component->get('showAddPreviewLessonModal'));
        $component->assertSee('Agregar lección al período');

        $component->call('addPreviewLesson', $lesson->id);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
        ]);
    }

    public function test_light_can_toggle_section_lock(): void
    {
        [$user, $calendar, $seccion] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->assertSee('Bloquear el horario de esta sección');

        $component->call('toggleSectionTimetableLock', $seccion->id);

        $this->assertTrue((bool) $seccion->fresh()->timetable_locked);
        $component->assertSee('Desbloquear el horario de esta sección');
    }

    public function test_light_shows_pdf_button_and_pdf_renders_persisted_slots(): void
    {
        [$user, $calendar, $seccion] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->assertSee('Generar PDF');
        $component->assertSee('source=persisted', false);

        $this->actingAs($user)
            ->get(route('app.planning.timetable.pdf.preview', [
                'calendar' => $calendar->id,
                'seccion' => $seccion->id,
                'source' => 'persisted',
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_light_opens_teacher_schedule_dialog(): void
    {
        [$user, $calendar] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->assertSee('Horario docente');

        $component->call('openTeacherScheduleDialog');

        $this->assertTrue($component->get('showTeacherScheduleDialog'));
        $component->assertSee('Horario por profesor');
        $component->assertSee('Ana');

        $component->call('closeTeacherScheduleDialog');
        $this->assertFalse($component->get('showTeacherScheduleDialog'));
    }

    public function test_light_highlights_teacher(): void
    {
        [$user, $calendar, $seccion, $lesson] = $this->makeContext();
        $profesorId = (int) \App\Models\app\Academy\Pevaluacion::query()->find($lesson->pevaluacion_id)->profesor_id;

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('toggleHighlightProfesor', $profesorId);
        $this->assertSame($profesorId, (int) $component->get('highlightProfesorId'));
        $component->assertSee('Quitar resaltado');

        $component->call('toggleHighlightProfesor', $profesorId);
        $this->assertNull($component->get('highlightProfesorId'));
    }

    public function test_light_syncs_preview_payload_after_move(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2] = $this->makeContext();

        // Payload obsoleto que apunta al período de origen.
        $calendar->update(['preview_payload' => [
            'assignment' => [$lesson->id => [['period_id' => $p1->id]]],
            'assignment_source' => 'stale',
        ]]);

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p2->id);

        $calendar->refresh();
        $payload = $calendar->preview_payload;

        $this->assertSame('light_slots', $payload['assignment_source'] ?? null);
        $periodIds = array_column($payload['assignment'][$lesson->id] ?? [], 'period_id');
        $this->assertContains($p2->id, $periodIds);
        $this->assertNotContains($p1->id, $periodIds);
    }

    public function test_light_collisions_panel_lists_cross_studio_collisions(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1] = $this->makeContext();
        $profesorId = (int) Pevaluacion::query()->find($lesson->pevaluacion_id)->profesor_id;

        // Otro P.Estudio activo del mismo lapso con el mismo docente en el mismo día/hora.
        $pestB = Pestudio::factory()->create(['status_active' => 'true']);
        $gradoB = Grado::factory()->create(['pestudio_id' => $pestB->id, 'status_active' => 'true']);
        $secB = Seccion::factory()->create(['grado_id' => $gradoB->id, 'status_active' => 'true']);
        $calB = TimetableCalendar::factory()->create(['lapso_id' => $calendar->lapso_id, 'pestudio_id' => $pestB->id, 'status' => 'active']);
        $shiftB = $this->makeShift();
        $pB = TimetablePeriod::factory()->create([
            'calendar_id' => $calB->id, 'shift_id' => $shiftB->id, 'day_of_week' => 1, 'order_in_day' => 1,
            'start_time' => $p1->start_time, 'end_time' => $p1->end_time, 'is_break' => false,
        ]);

        $asigB = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $pensumB = Pensum::factory()->create(['pestudio_id' => $pestB->id, 'grado_id' => $gradoB->id, 'asignatura_id' => $asigB->id]);
        $pevB = Pevaluacion::factory()->create(['profesor_id' => $profesorId, 'seccion_id' => $secB->id, 'pensum_id' => $pensumB->id, 'lapso_id' => $calendar->lapso_id]);
        $lessonB = TimetableLesson::factory()->create(['calendar_id' => $calB->id, 'pevaluacion_id' => $pevB->id, 'shift_id' => $shiftB->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0]);
        TimetableSlot::factory()->create(['calendar_id' => $calB->id, 'lesson_id' => $lessonB->id, 'period_id' => $pB->id, 'profesor_id' => $profesorId, 'seccion_id' => $secB->id]);

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->assertSee('Colisiones (1)');
        $component->assertSee('Ir a la celda');
        $component->assertSee('Resaltar docente');
    }

    public function test_light_shows_saved_indicator_and_updates_after_edit(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->assertSee('Guardado');
        $this->assertNull($component->get('lastSavedAt'));

        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p2->id);

        $savedAt = $component->get('lastSavedAt');
        $this->assertNotNull($savedAt);
        $component->assertSee('Guardado · '.$savedAt);
    }

    public function test_light_locked_section_shows_unlock_button_next_to_selector(): void
    {
        [$user, $calendar, $seccion] = $this->makeContext();
        $seccion->update(['timetable_locked' => true]);

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        // El botón de desbloqueo vive junto al selector; ya no hay banner aparte.
        $component->assertSee('Desbloquear el horario de esta sección');
        $component->assertDontSee('El horario de esta sección está bloqueado');

        $component->call('toggleSectionTimetableLock', $seccion->id);

        $this->assertFalse((bool) $seccion->fresh()->timetable_locked);
    }

    public function test_change_calendar_returns_to_selection(): void
    {
        [$user, $calendar] = $this->makeContext();

        $component = Livewire::actingAs($user)->test(TimetableLight::class);

        $component->call('chooseCalendar', $calendar->id);
        $component->call('changeCalendar');

        $this->assertSame(1, $component->get('lightStep'));
        $this->assertNull($component->get('calendarId'));
    }
}
