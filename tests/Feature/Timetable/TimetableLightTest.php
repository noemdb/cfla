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
 * Asistente «light»: dos pasos (elegir calendario + espacio de trabajo) con dos
 * tabs sobre la sección activa: «Grilla» (drag&drop) y «Clases» (Paso 3).
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
        // Loader global del módulo light (mismo patrón que /app/planning/diagnostico).
        $component->assertSee('timetable-light-global-loading');
        $component->assertSee('timetable-light-global-bar');
        $component->assertSee('Sincronizando…');
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
        $component->assertSee('Cambiar P.Estudio / calendario');

        // Dropdown de formatos/reportes.
        $component->assertSee('Formatos');
        $component->assertSee('Exportar P.Estudios');

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

        // El badge vive dentro del slot de la lección afectada (no en la celda).
        $this->assertMatchesRegularExpression(
            '/id="light-slot-'.$lesson->id.'-'.$p1->id.'"[^>]*data-collision="1"/',
            $component->html(),
        );
    }

    public function test_half_block_collision_only_counts_the_overlapping_half(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1] = $this->makeContext();
        $profesorId = (int) Pevaluacion::query()->find($lesson->pevaluacion_id)->profesor_id;

        TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('lesson_id', $lesson->id)
            ->update(['half' => 0]);

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
        $slotB = TimetableSlot::factory()->create([
            'calendar_id' => $calB->id, 'lesson_id' => $lessonB->id, 'period_id' => $pB->id,
            'profesor_id' => $profesorId, 'seccion_id' => $secB->id, 'half' => 1,
        ]);

        // Mitades opuestas: no hay solapamiento real del docente.
        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);
        $component->assertSee('Colisiones (0)');

        // La misma mitad sí colisiona.
        $slotB->update(['half' => 0]);
        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);
        $component->assertSee('Colisiones (1)');
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

    public function test_light_tab_defaults_to_grid_and_normalizes_invalid_values(): void
    {
        [$user, $calendar] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $this->assertSame('grid', $component->get('lightTab'));

        // setLightTab normaliza cualquier valor no soportado a grid.
        $component->call('setLightTab', 'bogus');
        $this->assertSame('grid', $component->get('lightTab'));

        // updatedLightTab normaliza entradas tipo ?tab=...
        $component->set('lightTab', 'desconocido');
        $this->assertSame('grid', $component->get('lightTab'));

        $component->call('setLightTab', 'classes');
        $this->assertSame('classes', $component->get('lightTab'));

        $component->call('setLightTab', 'grid');
        $this->assertSame('grid', $component->get('lightTab'));
    }

    public function test_light_tab_url_param_selects_classes_and_normalizes_invalid(): void
    {
        [$user, $calendar] = $this->makeContext();

        $classes = Livewire::withQueryParams(['tab' => 'classes'])
            ->actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);
        $this->assertSame('classes', $classes->get('lightTab'));

        $invalid = Livewire::withQueryParams(['tab' => 'nope'])
            ->actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);
        $this->assertSame('grid', $invalid->get('lightTab'));
    }

    public function test_light_classes_tab_renders_management_table_without_grid(): void
    {
        [$user, $calendar] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('setLightTab', 'classes');

        $this->assertSame('classes', $component->get('lightTab'));
        $component->assertSee('Gestión de clases');
        $component->assertSee('Guardar clases');
        $component->assertSee('Asignatura · Sección');
        $component->assertSee('Capacidad por turno');

        // El tab Clases no renderiza la grilla drag & drop.
        $component->assertDontSee('x-on:drop.prevent', false);
    }

    public function test_light_classes_tab_shows_persisted_grid_summary_instead_of_dry_run(): void
    {
        [$user, $calendar] = $this->makeContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('setLightTab', 'classes');

        // El modo rápido edita `timetable_slots` directamente: no hay dry-run;
        // el resumen muestra la grilla guardada de la sección activa y debe
        // reflejar los números reales (id de lección ↔ payload persistido).
        $component->assertDontSee('Dry-run');
        $component->assertSee('Grilla: 1 celdas ocupadas');
        $component->assertSee('1/1 clases ubicadas');
        $component->assertSee('todas las clases ubicadas');
        $component->assertSee('Grilla guardada');
    }

    public function test_light_classes_tab_prompts_when_section_has_no_lessons(): void
    {
        [$user, $calendar, $seccion, $lesson] = $this->makeContext();
        $profesorId = (int) Pevaluacion::query()->findOrFail($lesson->pevaluacion_id)->profesor_id;

        // Sección con Pevaluación pero sin lección configurada.
        $seccionZ = Seccion::factory()->create([
            'grado_id' => $seccion->grado_id,
            'name' => 'Z',
            'status_active' => 'true',
        ]);
        $this->makeExtraPevaluacion($seccionZ, (int) $calendar->lapso_id, $profesorId, 'Música sin clases');

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->set('activeSeccionId', $seccionZ->id);
        $component->call('setLightTab', 'classes');

        // Sin clases guardadas: ni Dry-run ni métricas vacías; una guía útil.
        $component->assertDontSee('Dry-run');
        $component->assertDontSee('clases ubicadas');
        $component->assertSee('aún no tiene clases guardadas');
    }

    public function test_light_selector_options_come_from_all_pevaluaciones_of_lapso(): void
    {
        [$user, $calendar, $seccion, $lesson] = $this->makeContext();
        $profesorId = (int) Pevaluacion::query()->findOrFail($lesson->pevaluacion_id)->profesor_id;

        // Sección del mismo P.Estudio/Grado con Pevaluación, pero sin lesson.
        $seccionZ = Seccion::factory()->create([
            'grado_id' => $seccion->grado_id,
            'name' => 'Z',
            'status_active' => 'true',
        ]);
        $this->makeExtraPevaluacion($seccionZ, (int) $calendar->lapso_id, $profesorId, 'Música sin clases');

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        // La sección sin lessons aparece igual en el selector común: las
        // opciones vienen de todas las Pevaluaciones del P.Estudio del lapso.
        $component->assertSee('Sección Z');
    }

    public function test_light_classes_tab_lists_pevaluaciones_without_lessons(): void
    {
        [$user, $calendar, $seccion, $lesson] = $this->makeContext();
        $profesorId = (int) Pevaluacion::query()->findOrFail($lesson->pevaluacion_id)->profesor_id;
        $pevWithoutLesson = $this->makeExtraPevaluacion(
            $seccion,
            (int) $calendar->lapso_id,
            $profesorId,
            'Física sin lesson',
        );

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);
        $component->call('setLightTab', 'classes');

        // La pevaluación sin lesson aparece igual en la tabla de la sección activa.
        $component->assertSee('Física sin lesson');
        $component->assertSee('lesson-row-'.$pevWithoutLesson->id, false);
        $this->assertDatabaseMissing('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pevWithoutLesson->id,
        ]);
    }

    public function test_light_save_lessons_from_classes_returns_to_grid_and_persists(): void
    {
        [$user, $calendar, $seccion, $lesson] = $this->makeContext();
        $profesorId = (int) Pevaluacion::query()->findOrFail($lesson->pevaluacion_id)->profesor_id;
        $pevWithoutLesson = $this->makeExtraPevaluacion(
            $seccion,
            (int) $calendar->lapso_id,
            $profesorId,
            'Química por guardar',
        );

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('setLightTab', 'classes');
        $component->call('addLesson', $pevWithoutLesson->id);

        // En memoria: todavía no toca la base de datos.
        $this->assertDatabaseMissing('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pevWithoutLesson->id,
        ]);

        $component->call('saveLessons');

        // Guardar desde Clases vuelve a la grilla...
        $this->assertSame('grid', $component->get('lightTab'));
        $this->assertSame(2, $component->get('lightStep'));

        // ...y persiste la lesson nueva.
        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pevWithoutLesson->id,
        ]);

        $calendar->refresh();
        $this->assertSame('light_slots', $calendar->preview_payload['assignment_source'] ?? null);
    }

    /**
     * Crea una Pevaluación adicional reutilizando la sección/lapso dados.
     */
    private function makeExtraPevaluacion(Seccion $seccion, int $lapsoId, int $profesorId, string $asignaturaName): Pevaluacion
    {
        $asignatura = Asignatura::factory()->create([
            'name' => $asignaturaName,
            'hour_t_week' => 1,
            'hour_p_week' => 0,
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $seccion->grado->pestudio_id,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $asignatura->id,
        ]);

        return Pevaluacion::factory()->create([
            'profesor_id' => $profesorId,
            'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $lapsoId,
        ]);
    }
}
