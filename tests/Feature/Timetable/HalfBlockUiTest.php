<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
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
 * ADR-TT-017 — Fase 5: UI de la mitad de bloque.
 *
 * Cubre el toggle «Mitad» del Paso 3 (recálculo de demanda), su persistencia y
 * la asignación de mitades explícitas en la grilla del asistente light
 * (drag&drop + modal de agregar), además del render de celdas partidas.
 */
class HalfBlockUiTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{user: User, calendar: TimetableCalendar, seccion: Seccion, pev: Pevaluacion}
     */
    private function wizardContext(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'Mitad',
            'ci_profesor' => '9477', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $lapso = Lapso::factory()->create();

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
            'period_minutes' => 60,
            'status' => 'draft',
        ]);
        $this->makeShift();

        // 3 h teóricas + 2 h prácticas: con períodos de 60' son 3/2 unidades de
        // bloque completo y 6/4 medias franjas.
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 2]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        return compact('user', 'calendar', 'seccion', 'pev');
    }

    public function test_step3_toggle_half_block_recalculates_demand(): void
    {
        $fx = $this->wizardContext();
        $pevId = $fx['pev']->id;

        Livewire::actingAs($fx['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fx['calendar']->id)
            ->set('currentStep', 3)
            ->set('selectedPevs', [$pevId])
            ->call('loadLessons')
            ->assertSet("lessons.$pevId.half_block", false)
            ->assertSet("lessons.$pevId.weekly_blocks_t", 3)
            ->assertSet("lessons.$pevId.weekly_blocks_p", 2)
            ->call('setLessonHalfBlock', $pevId)
            ->assertSet("lessons.$pevId.half_block", true)
            ->assertSet("lessons.$pevId.weekly_blocks_t", 6)
            ->assertSet("lessons.$pevId.weekly_blocks_p", 4)
            ->call('setLessonHalfBlock', $pevId, false)
            ->assertSet("lessons.$pevId.half_block", false)
            ->assertSet("lessons.$pevId.weekly_blocks_t", 3)
            ->assertSet("lessons.$pevId.weekly_blocks_p", 2);
    }

    public function test_step3_table_exposes_half_block_control(): void
    {
        $fx = $this->wizardContext();
        $pevId = $fx['pev']->id;

        $component = Livewire::actingAs($fx['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fx['calendar']->id)
            ->set('currentStep', 3)
            ->set('activeSeccionId', $fx['seccion']->id)
            ->set('selectedPevs', [$pevId])
            ->call('loadLessons');

        $component->assertSee('Mitad');
        $component->assertSee('setLessonHalfBlock('.$pevId.')', false);

        // Tras activar, el checkbox queda marcado (checked) en el re-render.
        $component->call('setLessonHalfBlock', $pevId);
        $component->assertSee('setLessonHalfBlock('.$pevId.')', false);

        // La estimación cuenta las 10 medias franjas: la lección necesita 10
        // períodos (una mitad por período como máximo), así que 10–10 celdas.
        $component->assertSee('10 medias franjas (mitad ½)');
        $component->assertSee('Estimación: 10–10 celdas');
    }

    public function test_save_lessons_persists_half_block_and_demands(): void
    {
        $fx = $this->wizardContext();
        $pevId = $fx['pev']->id;

        Livewire::actingAs($fx['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fx['calendar']->id)
            ->set('currentStep', 3)
            ->set('selectedPevs', [$pevId])
            ->call('loadLessons')
            ->call('setLessonHalfBlock', $pevId)
            ->call('saveLessons')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $fx['calendar']->id,
            'pevaluacion_id' => $pevId,
            'half_block' => 1,
            'weekly_blocks_t' => 6,
            'weekly_blocks_p' => 4,
        ]);
    }

    /**
     * El light edita `timetable_slots` directamente: mover a una mitad concreta
     * debe persistir esa mitad.
     */
    public function test_light_move_assigns_the_requested_half(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2, $profesor] = $this->lightContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p2->id, 1);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
            'half' => 1,
        ]);
        $this->assertDatabaseMissing('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p1->id,
        ]);
    }

    public function test_light_move_rejects_an_occupied_half_and_allows_the_opposite_one(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2, $profesor] = $this->lightContext();

        $otraAsignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $otroPensum = Pensum::factory()->create([
            'pestudio_id' => $seccion->grado->pestudio_id,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $otraAsignatura->id,
        ]);
        $otraPev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $otroPensum->id,
            'lapso_id' => $calendar->lapso_id,
        ]);
        $otraLeccion = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $otraPev->id,
            'shift_id' => $lesson->shift_id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
            'half_block' => true,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $otraLeccion->id,
            'period_id' => $p2->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'half' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        // La mitad superior ya está ocupada: no se mueve.
        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p2->id, 0);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p1->id,
        ]);
        $this->assertDatabaseMissing('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
        ]);

        // La mitad inferior está libre: conviven secuencialmente sin intercambio.
        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p2->id, 1);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
            'half' => 1,
        ]);
        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $otraLeccion->id,
            'period_id' => $p2->id,
            'half' => 0,
        ]);
    }

    public function test_light_drop_on_occupied_half_swaps_the_order_in_the_same_cell(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2, $profesor] = $this->lightContext();

        TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('lesson_id', $lesson->id)
            ->update(['half' => 0, 'is_half_group' => true]);

        $otraAsignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $otroPensum = Pensum::factory()->create([
            'pestudio_id' => $seccion->grado->pestudio_id,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $otraAsignatura->id,
        ]);
        $otraPev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $otroPensum->id,
            'lapso_id' => $calendar->lapso_id,
        ]);
        $otraLeccion = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $otraPev->id,
            'shift_id' => $lesson->shift_id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
            'half_block' => true,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $otraLeccion->id,
            'period_id' => $p1->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'half' => 1,
            'is_half_group' => true,
        ]);

        // Soltar sobre la mitad ocupada intercambia las mitades.
        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);
        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p1->id, 1);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p1->id,
            'half' => 1,
        ]);
        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $otraLeccion->id,
            'period_id' => $p1->id,
            'half' => 0,
        ]);

        // Dato heredado con ambas en la misma mitad: la otra se mueve a la
        // mitad opuesta y la arrastrada conserva la mitad elegida.
        TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereIn('lesson_id', [$lesson->id, $otraLeccion->id])
            ->update(['half' => 0, 'allow_shared_teacher' => true, 'is_half_group' => true]);

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);
        $component->call('movePreviewLesson', $lesson->id, $p1->id, $p1->id, 0);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p1->id,
            'half' => 0,
        ]);
        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $otraLeccion->id,
            'period_id' => $p1->id,
            'half' => 1,
        ]);
    }

    public function test_light_add_lesson_with_explicit_half(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2, $profesor] = $this->lightContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('openAddPreviewLessonModal', $p2->id);
        $component->call('addPreviewLesson', $lesson->id, 0);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
            'half' => 0,
        ]);
    }

    /**
     * Al agregar desde el modal una lección `half_block` sin indicar mitad, debe
     * caer en la primera mitad libre (superior) en vez de un bloque completo.
     */
    public function test_light_add_half_block_lesson_defaults_to_free_half(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2, $profesor] = $this->lightContext();

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->call('openAddPreviewLessonModal', $p2->id);
        $component->call('addPreviewLesson', $lesson->id);

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $p2->id,
            'half' => 0,
        ]);
    }

    /**
     * Una celda con media franja debe renderizar el sub-bloque y la clave «½»,
     * además de las zonas de drop por mitad.
     */
    public function test_light_grid_renders_split_cells_for_halves(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2, $profesor] = $this->lightContext();

        TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('lesson_id', $lesson->id)
            ->update(['half' => 0]);

        $component = Livewire::actingAs($user)
            ->test(TimetableLight::class, ['calendar' => $calendar->id]);

        $component->assertSee('½');
        // Zona de drop de la mitad inferior (0) y superior (1) del período.
        $component->assertSee(', '.$p1->id.', 0)', false);
        $component->assertSee(', '.$p1->id.', 1)', false);
        // Botón para alternar la mitad de la misma lección en el mismo período.
        $component->assertSee('movePreviewLesson('.$lesson->id.', '.$p1->id.', '.$p1->id.', 1)', false);
        $component->assertSee('Cambiar a la mitad inferior');
    }

    /**
     * @return array{0: User, 1: TimetableCalendar, 2: Seccion, 3: TimetableLesson, 4: TimetablePeriod, 5: TimetablePeriod, 6: Profesor}
     */
    private function lightContext(): array
    {
        $user = User::factory()->create(['is_planner' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'Mitad',
            'ci_profesor' => '9478', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'H', 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
            'name' => 'Horario Half Block UI', 'status' => 'active',
        ]);
        $shift = $this->makeShift();
        $p1 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false]);
        $p2 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false]);

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
            'half_block' => true,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
            'period_id' => $p1->id, 'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
        ]);

        return [$user, $calendar, $seccion, $lesson, $p1, $p2, $profesor];
    }
}
