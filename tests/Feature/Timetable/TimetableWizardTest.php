<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Livewire\Planning\Timetable\TimetableWizard as PlanningTimetableWizard;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §5 / §18 — Flujo del wizard de horario.
 */
class TimetableWizardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_coordinacion_can_access_wizard_page(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $this->actingAs($user)
            ->get('/app/coordinacion/timetable')
            ->assertOk()
            ->assertSee('Horario Escolar');
    }

    public function test_planner_can_access_wizard_page_in_planning_module(): void
    {
        $user = User::factory()->create(['is_planner' => true]);

        $this->actingAs($user)
            ->get('/app/planning/timetable')
            ->assertOk()
            ->assertSee('Horario Escolar');
    }

    public function test_planner_wizard_component_uses_planning_layout(): void
    {
        $user = User::factory()->create(['is_planner' => true]);

        Livewire::actingAs($user)
            ->test(PlanningTimetableWizard::class)
            ->assertOk()
            ->assertSet('currentStep', 1);
    }

    public function test_steps_two_to_five_are_blocked_without_calendar(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->assertSee('id="tt-tab-2"', false)
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('disabled', false)
            ->call('goToStep', 2)
            ->assertSet('currentStep', 1)
            ->call('goToStep', 5)
            ->assertSet('currentStep', 1);
    }

    public function test_header_refresh_clears_calendar_selection(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $calendar = TimetableCalendar::factory()->create();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 3)
            ->set('lessons', [['pev_id' => 1]])
            ->set('preview', ['assignment' => []])
            ->set('calendarId', null)
            ->assertSet('calendarId', null)
            ->assertSet('currentStep', 1)
            ->assertSet('lessons', [])
            ->assertSet('preview', null);
    }

    public function test_header_refresh_restarts_step3_selection_state(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $calendar = TimetableCalendar::factory()->create();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 3)
            ->set('selectedPevs', [21 => true, 57 => true])
            ->set('lessons', [21 => ['pev_id' => 21]])
            ->set('activePestudioId', 4)
            ->set('activeGradoId', 5)
            ->set('activeSeccionId', 21)
            ->set('step3Search', 'Inglés')
            ->set('bulkShiftId', 2)
            ->call('refreshWizard')
            ->assertSet('selectedPevs', [])
            ->assertSet('selectionResetToken', 1)
            ->assertSet('lessons', [])
            ->assertSet('activePestudioId', null)
            ->assertSet('activeGradoId', null)
            ->assertSet('activeSeccionId', null)
            ->assertSet('step3Search', '')
            ->assertSet('bulkShiftId', null)
            ->assertSet('currentStep', 1)
            ->assertSet('calendarId', $calendar->id);
    }

    public function test_header_refresh_rehydrates_component_and_calendar_list(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $calendar = TimetableCalendar::factory()->create(['name' => 'Calendario disponible']);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->set('calendarName', 'Valor obsoleto')
            ->set('strategy', TimetableCalendar::STRATEGY_LEGACY)
            ->call('refreshWizard')
            ->assertSet('calendarId', $calendar->id)
            ->assertSet('currentStep', 1)
            ->assertSet('calendarName', 'Calendario disponible')
            ->assertSet('strategy', TimetableCalendar::DEFAULT_STRATEGY)
            ->assertSee('Calendario disponible');
    }

    public function test_calendar_selector_change_clears_all_step3_checkboxes(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $firstCalendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $secondCalendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $shift = $this->shift();

        TimetableLesson::query()->create([
            'calendar_id' => $secondCalendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $firstCalendar->id)
            ->set('selectedPevs', [$fixture['pev']->id => true])
            ->set('lessons', [$fixture['pev']->id => ['pev_id' => $fixture['pev']->id]])
            ->set('calendarId', $secondCalendar->id)
            ->assertSet('selectedPevs', [])
            ->assertSet('lessons', []);
    }

    public function test_step3_reset_button_clears_all_checkboxes_without_deleting_saved_lessons(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $shift = $this->shift();

        TimetableLesson::query()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('selectedPevs', [$fixture['pev']->id => true])
            ->set('lessons', [$fixture['pev']->id => ['pev_id' => $fixture['pev']->id]])
            ->call('resetLessonCheckboxes')
            ->assertSet('selectedPevs', [])
            ->assertSet('lessons', [])
            ->assertSet('selectionResetToken', 1)
            ->assertDispatched('wireui:notification');

        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
        ]);
    }

    public function test_step5_conflict_detail_is_limited_to_selected_step3_lessons(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $first = $this->pevaluacionFixture($lapso->id);
        $second = $this->pevaluacionFixture($lapso->id);
        $firstLesson = TimetableLesson::query()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $first['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);
        $secondLesson = TimetableLesson::query()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $second['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('selectedPevs', [$first['pev']->id => true])
            ->set('preview', [
                'assignment' => [
                    (string) $firstLesson->id => [],
                    (string) $secondLesson->id => [],
                ],
            ]);

        $readiness = $component->instance()->publicationReadiness();

        $this->assertCount(1, $readiness['display_hard_conflicts']);
        $this->assertSame($firstLesson->id, $readiness['display_hard_conflicts'][0]['lesson_id']);
    }

    public function test_step1_creates_calendar_with_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();

        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('pestudioId', $pestudio->id)
            ->set('calendarName', 'Horario Test')
            ->call('createCalendar')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 1)
            ->assertSet('calendarId', TimetableCalendar::query()->where('lapso_id', $lapso->id)->first()->id);

        $this->assertDatabaseHas('timetable_calendars', [
            'lapso_id' => $lapso->id,
            'max_subjects_per_period' => 2,
            'strategy' => TimetableCalendar::STRATEGY_OPTIMIZED,
        ]);
    }

    public function test_calendar_strategy_can_be_created_and_updated(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('pestudioId', $pestudio->id)
            ->set('calendarName', 'Horario Legacy')
            ->set('strategy', TimetableCalendar::STRATEGY_LEGACY)
            ->call('createCalendar')
            ->assertHasNoErrors();

        $calendar = TimetableCalendar::query()->where('name', 'Horario Legacy')->firstOrFail();
        $this->assertSame(TimetableCalendar::STRATEGY_LEGACY, $calendar->strategy);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('openEditCalendarForm')
            ->set('strategy', TimetableCalendar::STRATEGY_OPTIMIZED)
            ->call('updateCalendar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('timetable_calendars', [
            'id' => $calendar->id,
            'strategy' => TimetableCalendar::STRATEGY_OPTIMIZED,
        ]);
    }

    public function test_calendar_subjects_per_period_can_be_updated(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'max_subjects_per_period' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('openEditCalendarForm')
            ->set('maxSubjectsPerPeriod', 3)
            ->call('updateCalendar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('timetable_calendars', [
            'id' => $calendar->id,
            'max_subjects_per_period' => 3,
        ]);
    }

    public function test_multiple_drafts_allowed_per_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('pestudioId', $pestudio->id)
            ->set('calendarName', 'Alternativa B')
            ->call('createCalendar')
            ->assertHasNoErrors()
            ->assertSet('calendarId', TimetableCalendar::query()->where('lapso_id', $lapso->id)->orderByDesc('id')->first()->id);

        $this->assertSame(2, TimetableCalendar::query()->where('lapso_id', $lapso->id)->count());

        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create();
        $lapso2 = Lapso::factory()->create();
        $src = TimetableCalendar::factory()->create(['lapso_id' => $lapso2->id, 'pestudio_id' => $pestudio->id]);
        $shift = $this->shift('M', 'Mañana');
        TimetablePeriod::factory()->create(['calendar_id' => $src->id, 'shift_id' => $shift->id, 'start_time' => '08:00:00', 'end_time' => '09:00:00']);
        $fixture = $this->pevaluacionFixture($lapso2->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $src->id, 'pevaluacion_id' => $fixture['pev']->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0, 'locked' => true,
        ]);
        $period = TimetablePeriod::where('calendar_id', $src->id)->first();
        TimetableSlot::create([
            'calendar_id' => $src->id, 'lesson_id' => $lesson->id, 'period_id' => $period->id,
            'profesor_id' => $fixture['profesor']->id, 'seccion_id' => $fixture['pev']->seccion_id,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $src->id)
            ->call('duplicateCalendar', $src->id)
            ->assertHasNoErrors();

        $this->assertSame(2, TimetableCalendar::query()->where('lapso_id', $lapso2->id)->count());
        $copy = TimetableCalendar::where('lapso_id', $lapso2->id)->orderByDesc('id')->first();
        $this->assertSame($src->pestudio_id, $copy->pestudio_id);
        $this->assertSame(TimetablePeriod::where('calendar_id', $src->id)->count(), TimetablePeriod::where('calendar_id', $copy->id)->count());
        $this->assertSame(TimetableLesson::where('calendar_id', $src->id)->count(), TimetableLesson::where('calendar_id', $copy->id)->count());
    }

    public function test_calendar_name_must_be_unique_within_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'name' => 'Horario Test']);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('calendarName', 'Horario Test')
            ->call('createCalendar');

        $this->assertSame(1, TimetableCalendar::query()->where('lapso_id', $lapso->id)->count());
    }

    public function test_step1_creates_shift_and_periods(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->pevaluacionFixture($lapso->id);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('shiftCode', 'M')
            ->set('shiftName', 'Mañana')
            ->call('createShift')
            ->assertSet('shiftId', TimetableShift::query()->where('code', 'M')->value('id'))
            ->assertHasNoErrors();

        $shift = TimetableShift::query()->where('code', 'M')->first();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('shiftId', $shift->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 2);

        $this->assertGreaterThan(0, TimetablePeriod::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_period_preview_exposes_complete_period_details(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->pevaluacionFixture($lapso->id);
        $shift = $this->shift();

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 1)
            ->set('shiftId', $shift->id)
            ->call('generatePeriods')
            ->assertHasNoErrors();

        $periods = $component->get('periods');
        $this->assertNotEmpty($periods);
        foreach (['pestudio', 'order', 'start', 'end', 'is_break', 'label'] as $key) {
            $this->assertArrayHasKey($key, $periods[0]);
        }
        $component
            ->assertSee('Detalle de períodos')
            ->assertSee('Duración')
            ->assertSee('Descripción');
    }

    public function test_period_preview_requires_a_selected_shift(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('generatePeriods')
            ->assertHasErrors(['shiftId' => 'gt'])
            ->assertSee('Debes seleccionar un turno antes de generar los períodos.');
    }

    public function test_step1_creates_periods_for_multiple_shifts(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->pevaluacionFixture($lapso->id);
        $shiftM = $this->shift('M', 'Mañana');
        $shiftT = $this->shift('T', 'Tarde', '13:00:00', '18:15:00');

        $wizard = Livewire::actingAs($user)->test(TimetableWizard::class);

        // Turno mañana: genera los bloques del pestudio para ese turno.
        $wizard->set('calendarId', $calendar->id)
            ->set('shiftId', $shiftM->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors();
        $countM = TimetablePeriod::query()->where('calendar_id', $calendar->id)->count();

        // Turno tarde: el guard por-turno no bloquea; suma los bloques de su turno.
        $wizard->set('shiftId', $shiftT->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors();
        $countTotal = TimetablePeriod::query()->where('calendar_id', $calendar->id)->count();

        $this->assertGreaterThan(0, $countM);
        $this->assertGreaterThan($countM, $countTotal);
    }

    public function test_step3_rehydrates_saved_lesson_metadata_from_calendar(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $shift = $this->shift('M', 'Mañana');

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 3,
            'weekly_blocks_p' => 2,
            'room_type_required' => 'laboratorio',
            'priority' => 5,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->assertSet('selectedPevs', [])
            ->set('selectedPevs', [$fixture['pev']->id])
            ->call('loadLessons')
            ->assertSet('lessons.'.$fixture['pev']->id.'.room_type_required', 'laboratorio')
            ->assertSet('lessons.'.$fixture['pev']->id.'.priority', 5);
    }

    public function test_select_all_preserves_derived_weekly_blocks(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $fixture['pev']->pensum->asignatura->update(['hour_t_week' => 3, 'hour_p_week' => 2]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('step3ViewMode', 'flat')
            ->set('currentStep', 3)
            ->call('toggleSelectAll')
            ->assertSet('lessons.'.$fixture['pev']->id.'.weekly_blocks_t', 3)
            ->assertSet('lessons.'.$fixture['pev']->id.'.weekly_blocks_p', 2)
            ->assertSee('value="3"', false)
            ->assertSee('value="2"', false);
    }

    public function test_reloading_selection_preserves_unsaved_weekly_blocks(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $fixture = $this->pevaluacionFixture($lapso->id);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('step3ViewMode', 'flat')
            ->set('selectedPevs', [$fixture['pev']->id])
            ->call('loadLessons')
            ->set('lessons.'.$fixture['pev']->id.'.weekly_blocks_t', 7)
            ->set('lessons.'.$fixture['pev']->id.'.weekly_blocks_p', 8)
            ->call('loadLessons')
            ->assertSet('lessons.'.$fixture['pev']->id.'.weekly_blocks_t', 7)
            ->assertSet('lessons.'.$fixture['pev']->id.'.weekly_blocks_p', 8);
    }

    public function test_reloading_selection_does_not_replace_current_blocks_with_saved_values(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $shift = TimetableShift::factory()->create(['code' => 'TEST-BLOCKS']);

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 0,
            'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('selectedPevs', [$fixture['pev']->id])
            ->set('lessons', [
                $fixture['pev']->id => [
                    'pev_id' => $fixture['pev']->id,
                    'weekly_blocks_t' => 3,
                    'weekly_blocks_p' => 2,
                ],
            ])
            ->call('loadLessons')
            ->assertSet('lessons.'.$fixture['pev']->id.'.weekly_blocks_t', 3)
            ->assertSet('lessons.'.$fixture['pev']->id.'.weekly_blocks_p', 2);
    }

    public function test_step2_registers_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('roomCode', 'LAB-01')
            ->set('roomName', 'Laboratorio de Química')
            ->set('roomCapacity', 24)
            ->set('roomType', 'laboratorio')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('timetable_rooms', ['code' => 'LAB-01', 'type' => 'laboratorio']);
    }

    public function test_register_room_with_reactivo_seccion_cascade(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['name' => 'Plan 1', 'status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id)
            ->set('roomCode', 'A-201')
            ->set('roomName', 'Aula 201')
            ->set('roomCapacity', 30)
            ->set('roomType', 'aula')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $room = TimetableRoom::query()->where('code', 'A-201')->first();
        $this->assertNotNull($room);
        $this->assertSame($seccion->id, $room->seccion_id);
    }

    public function test_step2_shows_only_rooms_of_current_pestudio_and_unlinked_rooms(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();

        $pestudioA = Pestudio::factory()->create(['name' => 'Plan A', 'status_active' => 'true']);
        $gradoA = Grado::factory()->create(['pestudio_id' => $pestudioA->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $gradoA->id, 'name' => 'A', 'status_active' => 'true']);

        $pestudioB = Pestudio::factory()->create(['name' => 'Plan B', 'status_active' => 'true']);
        $gradoB = Grado::factory()->create(['pestudio_id' => $pestudioB->id, 'name' => '2do Grado', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $gradoB->id, 'name' => 'B', 'status_active' => 'true']);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudioA->id]);

        TimetableRoom::query()->create(['code' => 'A-101', 'name' => 'Aula plan A', 'capacity' => 30, 'type' => 'aula', 'seccion_id' => $seccionA->id, 'status_active' => true]);
        TimetableRoom::query()->create(['code' => 'B-101', 'name' => 'Aula plan B', 'capacity' => 30, 'type' => 'aula', 'seccion_id' => $seccionB->id, 'status_active' => true]);
        TimetableRoom::query()->create(['code' => 'GEN-01', 'name' => 'Aula general', 'capacity' => 40, 'type' => 'aula', 'seccion_id' => null, 'status_active' => true]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('currentStep', 2)
            ->call('selectCalendar', $calendar->id)
            ->assertSet('currentStep', 2)
            ->assertSee('Aula plan A')
            ->assertSee('Aula general')
            ->assertDontSee('Aula plan B');
    }

    public function test_room_cascade_resets_lower_levels(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id);

        // Al cambiar el pestudio, se resetean grado y sección (hook automático).
        $component->set('roomPestudioId', $pestudio->id + 1)
            ->assertSet('roomGradoId', null)
            ->assertSet('roomSeccionId', null);

        // Al cambiar el grado, se resetea la sección.
        $component->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id)
            ->set('roomGradoId', $grado->id + 1)
            ->assertSet('roomSeccionId', null);
    }

    public function test_register_room_without_seccion_is_optional(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomCode', 'A-301')
            ->set('roomName', 'Aula 301')
            ->set('roomCapacity', 30)
            ->set('roomType', 'aula')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $room = TimetableRoom::query()->where('code', 'A-301')->first();
        $this->assertNotNull($room);
        $this->assertNull($room->seccion_id);
    }

    public function test_register_room_allows_multiple_rooms_per_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccion->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('roomPestudioId', $pestudio->id)
            ->set('roomGradoId', $grado->id)
            ->set('roomSeccionId', $seccion->id)
            ->set('roomCode', 'A-402')
            ->set('roomName', 'Aula 402')
            ->set('roomCapacity', 30)
            ->set('roomType', 'aula')
            ->call('saveRoom')
            ->assertHasNoErrors();

        $rooms = TimetableRoom::query()->where('seccion_id', $seccion->id)->orderBy('code')->get();
        $this->assertCount(2, $rooms);
        $this->assertSame(['A-401', 'A-402'], $rooms->pluck('code')->all());
    }

    public function test_update_room_can_link_seccion_already_used_by_other_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $roomA = TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccionA->id]);
        $roomB = TimetableRoom::factory()->create(['code' => 'A-402', 'seccion_id' => null]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('editRoom', $roomB->id)
            ->set('editRoomSeccionId', (string) $seccionA->id)
            ->call('updateRoom')
            ->assertHasNoErrors();

        $this->assertSame($seccionA->id, $roomA->refresh()->seccion_id);
        $this->assertSame($seccionA->id, $roomB->refresh()->seccion_id);
    }

    public function test_room_seccion_warning_lists_existing_rooms(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccion->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('roomSeccionId', $seccion->id)
            ->assertSet('roomSectionLinkedRooms', fn ($rooms) => $rooms->count() === 1 && $rooms->first()->code === 'A-401');
    }

    public function test_edit_room_warning_excludes_room_being_edited(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $roomA = TimetableRoom::factory()->create(['code' => 'A-401', 'seccion_id' => $seccion->id]);
        $roomB = TimetableRoom::factory()->create(['code' => 'A-402', 'seccion_id' => $seccion->id]);

        // Al editar A-401, el aviso solo lista la otra aula (A-402).
        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('editRoom', $roomA->id)
            ->assertSet('editRoomSectionLinkedRooms', fn ($rooms) => $rooms->count() === 1 && $rooms->first()->code === 'A-402');
    }

    public function test_update_room_changes_seccion_link(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $room = TimetableRoom::factory()->create(['code' => 'A-501', 'seccion_id' => $seccionA->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->call('editRoom', $room->id)
            ->assertSet('editRoomSeccionId', (string) $seccionA->id)
            ->set('editRoomSeccionId', null)
            ->call('updateRoom')
            ->assertHasNoErrors();

        $room->refresh();
        $this->assertNull($room->seccion_id);
    }

    public function test_bulk_create_rooms_creates_one_per_active_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertCount(1, $component->get('pendingRooms'));

        // El staging no persiste: la sección aún no tiene aula.
        $this->assertDatabaseMissing('timetable_rooms', ['seccion_id' => $seccion->id]);

        // Guardar todas → persiste.
        $component->call('saveAllRooms');

        $room = TimetableRoom::query()->where('seccion_id', $seccion->id)->first();
        $this->assertNotNull($room);
        $this->assertSame('Aula 1er Grado A', $room->name);
        $this->assertSame('aula', $room->type);
        $this->assertSame($seccion->amount_student, $room->capacity);
    }

    public function test_bulk_create_rooms_skips_seccion_with_existing_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        TimetableRoom::create([
            'code' => 'AULA-001', 'name' => 'Aula 1er Grado A',
            'type' => 'aula', 'seccion_id' => $seccion->id, 'status_active' => true,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertSame(1, TimetableRoom::query()->where('seccion_id', $seccion->id)->count());
    }

    public function test_bulk_create_rooms_ignores_inactive_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'false']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertSame(0, TimetableRoom::query()->where('seccion_id', $seccion->id)->count());
    }

    public function test_edit_room_updates_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $room = TimetableRoom::create([
            'code' => 'A-101', 'name' => 'Aula 101', 'capacity' => 30,
            'type' => 'aula', 'status_active' => true,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('editRoom', $room->id)
            ->assertSet('roomEditOpen', true)
            ->assertSet('editRoomCode', 'A-101')
            ->assertSet('editRoomName', 'Aula 101')
            ->set('editRoomName', 'Aula 101 (nueva)')
            ->set('editRoomCapacity', 40)
            ->call('updateRoom');

        $this->assertSame('Aula 101 (nueva)', $room->fresh()->name);
        $this->assertSame(40, $room->fresh()->capacity);
        $this->assertFalse($component->get('roomEditOpen'));
    }

    public function test_edit_room_rejects_duplicate_code(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        TimetableRoom::create(['code' => 'A-101', 'name' => 'Aula 101', 'type' => 'aula', 'status_active' => true]);
        $room = TimetableRoom::create(['code' => 'A-102', 'name' => 'Aula 102', 'type' => 'aula', 'status_active' => true]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('editRoom', $room->id)
            ->set('editRoomCode', 'A-101')
            ->call('updateRoom')
            ->assertHasErrors('editRoomCode');

        $this->assertSame('A-102', $room->fresh()->code);
    }

    public function test_rooms_grouped_by_peducativo(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $peducativo = Peducativo::factory()->create(['name' => 'Proyecto A']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $peducativo->id, 'status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $groups = $component->get('roomsByPeducativo');
        $matching = collect($groups)->first(fn ($g) => $g['name'] === 'Proyecto A');

        $this->assertNotNull($matching);
        $this->assertContains($seccion->id, array_column($matching['rooms'], 'seccion_id'));
    }

    public function test_save_all_rooms_persists_and_clears_pending(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $this->assertCount(1, $component->get('pendingRooms'));

        $component->call('saveAllRooms');

        $this->assertCount(0, $component->get('pendingRooms'));
        $this->assertDatabaseHas('timetable_rooms', ['seccion_id' => $seccion->id]);
    }

    public function test_remove_pending_room(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('bulkCreateRooms');

        $pendingId = $component->get('pendingRooms')[0]['id'];

        $component->call('removePendingRoom', $pendingId);

        $this->assertCount(0, $component->get('pendingRooms'));
        $this->assertDatabaseMissing('timetable_rooms', ['seccion_id' => $seccion->id]);
    }

    public function test_delete_all_rooms(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        TimetableRoom::create(['code' => 'A-101', 'name' => 'Aula 101', 'type' => 'aula', 'status_active' => true]);
        TimetableRoom::create(['code' => 'A-102', 'name' => 'Aula 102', 'type' => 'aula', 'status_active' => true]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('deleteAllRooms');

        $this->assertSame(0, TimetableRoom::query()->count());
        $this->assertCount(0, $component->get('rooms'));
    }

    public function test_step3_pevaluaciones_grouped_by_pestudio_grado_seccion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $peducativo = Peducativo::factory()->create(['name' => 'Proyecto A']);
        $pestudio = Pestudio::factory()->create(['peducativo_id' => $peducativo->id, 'name' => 'Plan 1', 'status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $profesor = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '30001', 'status_active' => 'true',
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('currentStep', 3)
            ->set('calendarId', $calendar->id)
            ->assertSet('activePestudioId', $pestudio->id)
            ->assertSet('activeGradoId', $grado->id)
            ->assertSet('activeSeccionId', $seccion->id)
            ->assertSee('Plan 1')
            ->assertSee('1er Grado')
            ->assertSee('Sección A')
            ->assertSee($asignatura->name);
    }

    public function test_step3_excludes_pevaluaciones_of_inactive_grado(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $pestudio = Pestudio::factory()->create(['name' => 'Plan 1', 'status_active' => 'true']);

        // Grado activo con asignatura "Matemáticas".
        $gradoActive = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '1er Grado', 'status_active' => 'true']);
        $seccionActive = Seccion::factory()->create(['grado_id' => $gradoActive->id, 'name' => 'A', 'status_active' => 'true']);
        $asigActive = Asignatura::factory()->create(['name' => 'Matemáticas', 'hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensumActive = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $gradoActive->id, 'asignatura_id' => $asigActive->id]);
        $profesorA = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '40001', 'status_active' => 'true',
        ]);
        Pevaluacion::factory()->create([
            'profesor_id' => $profesorA->id, 'seccion_id' => $seccionActive->id,
            'pensum_id' => $pensumActive->id, 'lapso_id' => $lapso->id,
        ]);

        // Grado inactivo con asignatura "Química" → NO debe aparecer.
        $gradoInactive = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'name' => '2do Grado', 'status_active' => 'false']);
        $seccionInactive = Seccion::factory()->create(['grado_id' => $gradoInactive->id, 'name' => 'B', 'status_active' => 'true']);
        $asigInactive = Asignatura::factory()->create(['name' => 'Química', 'hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensumInactive = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $gradoInactive->id, 'asignatura_id' => $asigInactive->id]);
        $profesorB = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Beto', 'lastname' => 'Pérez',
            'ci_profesor' => '40002', 'status_active' => 'true',
        ]);
        Pevaluacion::factory()->create([
            'profesor_id' => $profesorB->id, 'seccion_id' => $seccionInactive->id,
            'pensum_id' => $pensumInactive->id, 'lapso_id' => $lapso->id,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('currentStep', 3)
            ->set('calendarId', $calendar->id)
            ->assertSee('Matemáticas')
            ->assertDontSee('Química');
    }

    public function test_step3_derives_blocks_from_asignatura_hours(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $shift = $this->shift();

        $fixture = $this->pevaluacionFixture($lapso->id);
        $pev = $fixture['pev'];

        $wizard = Livewire::actingAs($user)->test(TimetableWizard::class);
        $wizard->set('calendarId', $calendar->id);
        $wizard->set('selectedPevs', [$pev->id]);
        $wizard->call('loadLessons');

        $lessons = $wizard->get('lessons');
        $this->assertCount(1, $lessons);
        $lesson = $lessons[$pev->id];
        $this->assertSame(3, $lesson['weekly_blocks_t']); // 3 h semanales → 3 bloques de 60'
        $this->assertSame(2, $lesson['weekly_blocks_p']); // 2 h semanales → 2 bloques
    }

    public function test_step3_recalculates_blocks_without_changing_asignatura_hours(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $pev = $fixture['pev'];
        $asignatura = $pev->load('pensum.asignatura')->pensum->asignatura;

        $wizard = Livewire::actingAs($user)->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('selectedPevs', [$pev->id])
            ->call('loadLessons')
            ->set('lessons.'.$pev->id.'.weekly_blocks_t', 7)
            ->set('lessons.'.$pev->id.'.weekly_blocks_p', 8)
            ->call('recalculateLessonBlocks')
            ->assertSet('lessons.'.$pev->id.'.weekly_blocks_t', 3)
            ->assertSet('lessons.'.$pev->id.'.weekly_blocks_p', 2)
            ->assertDispatched('wireui:notification');

        $this->assertSame(3, (int) $asignatura->fresh()->hour_t_week);
        $this->assertSame(2, (int) $asignatura->fresh()->hour_p_week);
    }

    public function test_step3_rehydrates_zero_saved_blocks_from_current_asignatura_hours(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $pev = $fixture['pev'];
        $shift = $this->shift();

        TimetableLesson::create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 0,
            'weekly_blocks_p' => 0,
            'locked' => false,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('selectedPevs', [$pev->id])
            ->call('loadLessons')
            ->assertSet('lessons.'.$pev->id.'.weekly_blocks_t', 3)
            ->assertSet('lessons.'.$pev->id.'.weekly_blocks_p', 2);
    }

    public function test_step3_saves_lessons(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $fixture = $this->pevaluacionFixture($lapso->id);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 3)
            ->set('selectedPevs', [$fixture['pev']->id])
            ->call('loadLessons')
            ->set('lessons.'.$fixture['pev']->id.'.is_half_group', true)
            ->call('saveLessons')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 4);

        $this->assertSame(1, TimetableLesson::query()->where('calendar_id', $calendar->id)->count());
        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'is_half_group' => 1,
        ]);
    }

    public function test_step3_save_preserves_unchecked_existing_lessons(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $selected = $this->pevaluacionFixture($lapso->id)['pev'];
        $unchecked = $this->pevaluacionFixture($lapso->id)['pev'];

        foreach ([$selected, $unchecked] as $pev) {
            TimetableLesson::create([
                'calendar_id' => $calendar->id,
                'pevaluacion_id' => $pev->id,
                'shift_id' => $shift->id,
                'weekly_blocks_t' => 1,
                'weekly_blocks_p' => 0,
                'locked' => false,
            ]);
        }

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 3)
            ->set('selectedPevs', [$selected->id])
            ->call('loadLessons')
            ->call('saveLessons')
            ->assertSet('currentStep', 4);

        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $selected->id,
        ]);
        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $unchecked->id,
        ]);
    }

    public function test_step3_save_with_no_checked_lessons_preserves_calendar_lessons(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $pev = $this->pevaluacionFixture($lapso->id)['pev'];

        TimetableLesson::create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
            'locked' => false,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 3)
            ->set('selectedPevs', [])
            ->call('loadLessons')
            ->call('saveLessons')
            ->assertSet('currentStep', 4);

        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
        ]);
    }

    public function test_step3_rejects_partial_lesson_payload_without_name(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('lessons', [
                $fixture['pev']->id => [
                    'pev_id' => $fixture['pev']->id,
                    'weekly_blocks_t' => 0,
                    'weekly_blocks_p' => 0,
                ],
            ])
            ->call('saveLessons')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 1)
            ->assertDispatched('wireui:notification');

        $this->assertDatabaseMissing('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
        ]);
    }

    public function test_orphan_fix_action_is_safe_when_no_orphans_exist(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('confirmRemoveOrphanLessons')
            ->assertDispatched('wireui:notification');

        $this->assertSame([], TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->whereDoesntHave('pevaluacion')
            ->pluck('id')
            ->all());
    }

    public function test_step3_syncs_current_profesor_from_academic_load(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $replacementUser = User::factory()->create();
        $replacement = \App\Models\app\Academy\Profesor::create([
            'user_id' => $replacementUser->id,
            'name' => 'Luis',
            'lastname' => 'Actualizado',
            'ci_profesor' => '2999',
            'status_active' => 'true',
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 3)
            ->set('selectedPevs', [$fixture['pev']->id])
            ->call('loadLessons');

        $fixture['pev']->update(['profesor_id' => $replacement->id]);

        $component
            ->call('syncAcademicLoad')
            ->assertSet('lessons.'.$fixture['pev']->id.'.profesor_id', $replacement->id)
            ->assertSee('Sincronizar carga académica');
    }

    public function test_step3_replicates_lesson_configuration_to_sibling_section(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $sourcePev = $fixture['pev'];
        $sourceSection = $sourcePev->seccion;
        $targetSection = Seccion::factory()->create(['grado_id' => $sourceSection->grado_id]);
        $targetPev = \App\Models\app\Academy\Pevaluacion::factory()->create([
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $targetSection->id,
            'pensum_id' => $sourcePev->pensum_id,
            'lapso_id' => $lapso->id,
        ]);
        $shift = $this->shift();

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $sourcePev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 3,
            'weekly_blocks_p' => 2,
            'room_type_required' => 'laboratorio',
            'is_half_group' => true,
            'priority' => 4,
            'locked' => true,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 3)
            ->set('activeSeccionId', $sourceSection->id)
            ->set('replicateToSeccionId', $targetSection->id)
            ->set('selectedPevs', [$sourcePev->id => true])
            ->call('loadLessons')
            ->set('lessons.'.$sourcePev->id.'.weekly_blocks_t', 7)
            ->set('lessons.'.$sourcePev->id.'.weekly_blocks_p', 3)
            ->set('lessons.'.$sourcePev->id.'.room_type_required', 'taller')
            ->set('lessons.'.$sourcePev->id.'.is_half_group', false)
            ->call('replicateLessonsToSection')
            ->assertSet('replicateToSeccionId', null);

        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $targetPev->id,
            'weekly_blocks_t' => 7,
            'weekly_blocks_p' => 3,
            'room_type_required' => 'taller',
            'is_half_group' => 0,
            'priority' => 4,
            'locked' => 1,
        ]);
    }

    public function test_bulk_room_type_assigns_and_notifies(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $shift = $this->shift();

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('lessons', [
                $fixture['pev']->id => [
                    'pev_id' => $fixture['pev']->id,
                    'name' => 'Inglés · A',
                    'shift_id' => $shift->id,
                    'weekly_blocks_t' => 2,
                    'weekly_blocks_p' => 1,
                    'room_type_required' => null,
                    'priority' => 0,
                    'locked' => false,
                ],
            ])
            ->set('bulkRoomType', 'laboratorio')
            ->call('bulkAssignRoomType')
            ->assertSet('lessons.'.$fixture['pev']->id.'.room_type_required', 'laboratorio');

        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'room_type_required' => 'laboratorio',
        ]);
    }

    public function test_import_lessons_from_csv(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $shiftM = $this->shift('M', 'Mañana');
        $fixture = $this->pevaluacionFixture($lapso->id);

        $csv = "pevaluacion_id,turno,bloques_t,bloques_p,aula,prioridad\n";
        $csv .= "{$fixture['pev']->id},M,3,1,,\n";
        $file = UploadedFile::fake()->createWithContent('lecciones.csv', $csv);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons')
            ->assertSet('importMessage', '1 lección(es) importada(s).')
            ->assertCount('lessons', 1);

        $lessons = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons')
            ->get('lessons');

        $lesson = $lessons[$fixture['pev']->id];
        $this->assertSame($shiftM->id, $lesson['shift_id']);
        $this->assertSame(3, $lesson['weekly_blocks_t']);
        $this->assertSame(1, $lesson['weekly_blocks_p']);
    }

    public function test_import_rejects_duplicate_rows(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->shift('M', 'Mañana');
        $fixture = $this->pevaluacionFixture($lapso->id);

        $csv = "pevaluacion_id,turno\n";
        $csv .= "{$fixture['pev']->id},M\n";
        $csv .= "{$fixture['pev']->id},M\n";
        $file = UploadedFile::fake()->createWithContent('lecciones.csv', $csv);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons');

        $component->assertCount('lessons', 1);
        $this->assertNotEmpty($component->get('importErrors'));
        $this->assertStringContainsString('duplicada', implode(' ', $component->get('importErrors')));
    }

    public function test_import_rejects_invalid_turno(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $fixture = $this->pevaluacionFixture($lapso->id);

        $csv = "pevaluacion_id,turno\n";
        $csv .= "{$fixture['pev']->id},Z\n";
        $file = UploadedFile::fake()->createWithContent('lecciones.csv', $csv);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('importFile', $file)
            ->call('importLessons');

        $this->assertEmpty($component->get('lessons'));
        $this->assertStringContainsString('turno inválido', implode(' ', $component->get('importErrors')));
    }

    public function test_step5_runs_dry_run_and_shows_preview(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();

        // Períodos para el solver.
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        $fixture = $this->pevaluacionFixture($lapso->id);

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->call('runDryRun')
            ->assertSet('generationState', 'preview_ready');

        $preview = TimetableCalendar::find($calendar->id)->preview_payload;
        $this->assertNotNull($preview);
        $this->assertTrue($preview['dry_run']);
    }

    public function test_step5_can_update_draft_with_current_preview(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $preview = [
            'dry_run' => true,
            'assignment' => ['12' => [['period_id' => 4, 'room_id' => null]]],
            'unassigned' => [],
            'manual_override' => true,
        ];

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->set('generationState', 'preview_ready')
            ->set('preview', $preview)
            ->call('updateDraftPreview')
            ->assertDispatched('wireui:notification');

        $this->assertSame($preview, $calendar->fresh()->preview_payload);
        $this->assertSame(TimetableCalendar::STATUS_DRAFT, $calendar->fresh()->status);
    }

    public function test_step5_explains_unassigned_lesson_with_actionable_diagnostics(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift('X', 'Sin períodos');
        $fixture = $this->pevaluacionFixture($lapso->id);

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2,
            'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->call('runDryRun')
            ->assertSee('Conflictos accionables')
            ->assertSee('El turno seleccionado no tiene períodos disponibles')
            ->assertSee('Genera y guarda los períodos del turno');
    }

    public function test_step5_shows_section_preview_tabs_and_grid(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();

        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        $fixture = $this->pevaluacionFixture($lapso->id);
        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('goToStep', 5)
            ->call('runDryRun')
            ->assertSet('generationState', 'preview_ready')
            // Pestañas pestudio → grado → sección + grilla del horario.
            ->assertSee('Horario previsualizado', false)
            ->assertSee('Sección ', false)
            ->assertSee('Asignadas', false)
            ->assertSee('Vacío', false)
            ->assertSee('draggable="true"', false)
            ->assertSee('movePreviewLesson', false);
    }

    public function test_step5_reports_empty_cells_and_incomplete_lessons_for_active_section(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();

        $firstPeriod = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 2,
            'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2,
            'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->set('activeSeccionId', $fixture['pev']->seccion_id)
            ->set('generationState', 'preview_ready')
            ->set('preview', [
                'assignment' => [
                    (string) $lesson->id => [['period_id' => $firstPeriod->id]],
                ],
                'unassigned' => [],
            ]);

        $summary = $component->instance()->previewSectionGapSummary($fixture['pev']->seccion_id);

        $this->assertSame(1, $summary['empty_cells']);
        $this->assertSame(1, $summary['incomplete_lessons'][0]['missing']);
        $component
            ->assertSee('Cobertura de la sección')
            ->assertSee('1 celda(s) vacía(s)')
            ->assertSee('Lessons incompletas');
    }

    public function test_section_preview_keeps_multiple_assignments_in_one_cell(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
            'is_half_group' => true,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lesson->id => [
                        ['period_id' => $period->id],
                        ['period_id' => $period->id],
                    ],
                ],
            ]);

        $method = new \ReflectionMethod($component->instance(), 'previewSectionGrid');
        $method->setAccessible(true);
        $grid = $method->invoke($component->instance(), $fixture['pev']->seccion_id);

        $this->assertCount(2, $grid[$shift->id][1][1]);
        $this->assertTrue($grid[$shift->id][1][1][0]['is_half_group']);
    }

    public function test_step5_can_move_preview_lesson_to_another_period(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 2,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->set('generationState', 'preview_ready')
            ->set('preview', [
                'assignment' => [
                    (string) $lesson->id => [
                        ['period_id' => $source->id, 'room_id' => null, 'is_practical' => false],
                    ],
                ],
            ])
            ->call('movePreviewLesson', $lesson->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lesson->id).'.0.period_id', $target->id)
            ->assertSet('preview.manual_override', true);
    }

    public function test_step5_does_not_duplicate_lesson_when_moved_to_its_existing_period(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2,
            'weekly_blocks_p' => 0,
            'is_half_group' => false,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lesson->id => [
                        ['period_id' => $source->id],
                        ['period_id' => $target->id],
                    ],
                ],
            ])
            ->call('movePreviewLesson', $lesson->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lesson->id), [
                ['period_id' => $source->id],
                ['period_id' => $target->id],
            ])
            ->assertDispatched('wireui:notification');
    }

    public function test_step5_can_add_unassigned_lesson_from_empty_cell(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->set('generationState', 'preview_ready')
            ->set('preview', [
                'assignment' => [],
                'unassigned' => [$lesson->id],
            ])
            ->call('openAddPreviewLessonModal', $period->id)
            ->assertSet('showAddPreviewLessonModal', true)
            ->assertSee('Agregar lección al período')
            ->call('addPreviewLesson', $lesson->id)
            ->assertSet('preview.assignment.'.((string) $lesson->id).'.0.period_id', $period->id)
            ->assertSet('preview.unassigned', [])
            ->assertSet('showAddPreviewLessonModal', false)
            ->assertDispatched('wireui:notification');
    }

    public function test_step5_add_lesson_modal_prioritizes_same_shift_and_searches(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shiftM = $this->shift('M', 'Mañana');
        $shiftT = $this->shift('T', 'Tarde', '13:00:00', '15:00:00');
        $periodM = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shiftM->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $fxM = $this->pevaluacionFixture($lapso->id);
        $fxT = $this->pevaluacionFixture($lapso->id);
        $fxM['pev']->pensum->asignatura->update(['name' => 'Zoologia Avanzada']);
        $fxT['pev']->pensum->asignatura->update(['name' => 'Botanica Elemental']);

        $lessonM = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $fxM['pev']->id,
            'shift_id' => $shiftM->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        $lessonT = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $fxT['pev']->id,
            'shift_id' => $shiftT->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        $c = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('currentStep', 5)
            ->set('generationState', 'preview_ready')
            ->set('preview', ['assignment' => [], 'unassigned' => [$lessonT->id, $lessonM->id]])
            ->call('openAddPreviewLessonModal', $periodM->id)
            ->assertSet('addPreviewLessonSearch', '')
            ->assertSet('addPreviewLessonSource', 'grade')
            ->set('addPreviewLessonSource', 'missing');

        // Prioriza el mismo turno del período destino (M).
        $ordered = $c->instance()->availablePreviewLessons()->pluck('id')->all();
        $this->assertSame($lessonM->id, $ordered[0]);
        $this->assertContains($lessonT->id, $ordered);

        // Búsqueda por asignatura.
        $c->set('addPreviewLessonSearch', 'zoologia');
        $this->assertSame([$lessonM->id], $c->instance()->availablePreviewLessons()->pluck('id')->all());

        $c->set('addPreviewLessonSearch', 'zzz-no-existe');
        $this->assertCount(0, $c->instance()->availablePreviewLessons());

        $c->set('activeSeccionId', $fxM['pev']->seccion_id)
            ->set('addPreviewLessonSource', 'grade')
            ->set('addPreviewLessonSearch', '');
        $sectionLessons = $c->instance()->availablePreviewLessons();
        $this->assertTrue($sectionLessons->every(
            fn ($lesson) => (int) $lesson->pevaluacion?->seccion_id === (int) $fxM['pev']->seccion_id
        ));
        $this->assertFalse($sectionLessons->contains(
            fn ($lesson) => (int) $lesson->pevaluacion?->seccion_id === (int) $fxT['pev']->seccion_id
        ));
    }

    public function test_step5_swaps_lessons_when_target_period_has_same_section_lesson(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $first = $this->pevaluacionFixture($lapso->id);
        $second = $this->pevaluacionFixture($lapso->id);
        $second['pev']->update(['seccion_id' => $first['pev']->seccion_id]);
        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $first['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $second['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        $this->assertSame(
            $lessonA->pevaluacion->seccion_id,
            $lessonB->fresh()->pevaluacion->seccion_id,
        );

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $source->id]],
                    (string) $lessonB->id => [['period_id' => $target->id]],
                ],
            ])
            ->call('movePreviewLesson', $lessonA->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonA->id).'.0.period_id', $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonB->id).'.0.period_id', $source->id);
    }

    public function test_step5_allows_cross_shift_swap_with_warning(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $morning = $this->shift();
        $afternoon = $this->shift('T', 'Tarde');
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $morning->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $afternoon->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $first = $this->pevaluacionFixture($lapso->id);
        $second = $this->pevaluacionFixture($lapso->id);
        $second['pev']->update(['seccion_id' => $first['pev']->seccion_id]);
        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $first['pev']->id,
            'shift_id' => $morning->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $second['pev']->id,
            'shift_id' => $afternoon->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $source->id]],
                    (string) $lessonB->id => [['period_id' => $target->id]],
                ],
            ])
            ->call('movePreviewLesson', $lessonA->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonA->id).'.0.period_id', $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonB->id).'.0.period_id', $source->id)
            ->assertDispatched('wireui:notification');
    }

    public function test_step5_allows_direct_cross_shift_move_with_warning(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $morning = $this->shift();
        $afternoon = $this->shift('T', 'Tarde');
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $afternoon->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $morning->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $afternoon->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lesson->id => [['period_id' => $source->id]],
                ],
            ])
            ->call('movePreviewLesson', $lesson->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lesson->id).'.0.period_id', $target->id)
            ->assertSet('preview.manual_override', true)
            ->assertDispatched('wireui:notification');
    }

    public function test_step5_allows_adding_lesson_to_other_shift_with_warning(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $morning = $this->shift();
        $afternoon = $this->shift('T', 'Tarde');
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $afternoon->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $morning->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('addPreviewLessonPeriodId', $period->id)
            ->set('preview', [
                'assignment' => [],
                'unassigned' => [$lesson->id],
            ])
            ->call('addPreviewLesson', $lesson->id)
            ->assertSet('preview.assignment.'.((string) $lesson->id).'.0.period_id', $period->id)
            ->assertSet('preview.unassigned', [])
            ->assertSet('showAddPreviewLessonModal', false)
            ->assertDispatched('wireui:notification');
    }

    public function test_step5_removes_preview_lesson_and_marks_it_unassigned(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $first = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $second = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
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
            ->call('removePreviewLesson', $lesson->id)
            ->assertSet('preview.assignment', [])
            ->assertSet('preview.unassigned', [$lesson->id])
            ->assertSet('preview.manual_override', true)
            ->assertDispatched('wireui:notification');
    }

    public function test_step5_confirms_before_removing_preview_lesson(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lesson->id => [['period_id' => $period->id]],
                ],
            ])
            ->call('confirmRemovePreviewLesson', $lesson->id)
            ->assertSet('preview.assignment.'.((string) $lesson->id).'.0.period_id', $period->id)
            ->assertNotSet('preview.manual_override', true);
    }

    public function test_step5_reports_slot_parity_between_sections_of_same_grade(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $firstPeriod = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $secondPeriod = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $first = $this->pevaluacionFixture($lapso->id);
        $second = $this->pevaluacionFixture($lapso->id);
        $second['pev']->seccion->update(['grado_id' => $first['pev']->seccion->grado_id]);
        $inactiveSection = \App\Models\app\Academy\Seccion::factory()->create([
            'grado_id' => $first['pev']->seccion->grado_id,
            'status_active' => 'false',
        ]);
        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $first['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $second['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('activeSeccionId', $first['pev']->seccion_id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [
                        ['period_id' => $firstPeriod->id],
                        ['period_id' => $secondPeriod->id],
                    ],
                    (string) $lessonB->id => [
                        ['period_id' => $firstPeriod->id],
                    ],
                ],
            ]);

        $parity = $component->instance()->sectionSlotParity();

        $this->assertNotNull($parity);
        $this->assertSame(1, $parity['delta']);
        $this->assertFalse($parity['balanced']);
        $this->assertCount(2, $parity['subjects']);
        $this->assertTrue(collect($parity['subjects'])
            ->pluck('sections')
            ->flatten()
            ->contains(2));
        $this->assertNotContains($inactiveSection->id, array_keys($parity['subjects'][0]['sections']));
    }

    public function test_step5_does_not_replace_section_slots_when_preserved_teacher_collides(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        $preserved = $this->pevaluacionFixture($lapso->id);
        $candidate = $this->pevaluacionFixture($lapso->id);
        $candidate['pev']->update(['profesor_id' => $preserved['profesor']->id]);
        $preservedLesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $preserved['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);
        $candidateLesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $candidate['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);
        TimetableSlot::create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $preservedLesson->id,
            'period_id' => $period->id,
            'profesor_id' => $preserved['profesor']->id,
            'seccion_id' => $preserved['pev']->seccion_id,
            'is_half_group' => false,
            'locked' => true,
            'is_manual_override' => true,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('activeSeccionId', $candidate['pev']->seccion_id)
            ->set('preview', [
                'assignment' => [
                    (string) $candidateLesson->id => [
                        ['period_id' => $period->id],
                    ],
                ],
            ])
            ->call('persistCurrentSectionSlots');

        $this->assertDatabaseHas('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $preservedLesson->id,
            'period_id' => $period->id,
        ]);
        $this->assertDatabaseMissing('timetable_slots', [
            'calendar_id' => $calendar->id,
            'lesson_id' => $candidateLesson->id,
        ]);
    }

    public function test_step5_allows_swap_when_both_lessons_have_same_teacher(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $first = $this->pevaluacionFixture($lapso->id);
        $second = $this->pevaluacionFixture($lapso->id);
        $second['pev']->update([
            'seccion_id' => $first['pev']->seccion_id,
            'profesor_id' => $first['profesor']->id,
        ]);
        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $first['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $second['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $source->id]],
                    (string) $lessonB->id => [['period_id' => $target->id]],
                ],
            ])
            ->call('movePreviewLesson', $lessonA->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonA->id).'.0.period_id', $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonB->id).'.0.period_id', $source->id);
    }

    public function test_step5_swaps_when_only_one_same_section_lesson_allows_half_group(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $first = $this->pevaluacionFixture($lapso->id);
        $second = $this->pevaluacionFixture($lapso->id);
        $second['pev']->update(['seccion_id' => $first['pev']->seccion_id]);
        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $first['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            'is_half_group' => true,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $second['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            'is_half_group' => false,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $source->id]],
                    (string) $lessonB->id => [['period_id' => $target->id]],
                ],
            ])
            ->call('movePreviewLesson', $lessonA->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonA->id).'.0.period_id', $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonB->id).'.0.period_id', $source->id);
    }

    public function test_step5_shares_same_section_cell_only_when_both_lessons_allow_half_group(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->shift();
        $source = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $target = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $first = $this->pevaluacionFixture($lapso->id);
        $second = $this->pevaluacionFixture($lapso->id);
        $second['pev']->update(['seccion_id' => $first['pev']->seccion_id]);
        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $first['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            'is_half_group' => true,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $second['pev']->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            'is_half_group' => true,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $source->id]],
                    (string) $lessonB->id => [['period_id' => $target->id]],
                ],
            ])
            ->call('movePreviewLesson', $lessonA->id, $source->id, $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonA->id).'.0.period_id', $target->id)
            ->assertSet('preview.assignment.'.((string) $lessonB->id).'.0.period_id', $target->id);
    }

    public function test_legacy_section_preview_does_not_restore_excluded_locked_slots(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'strategy' => TimetableCalendar::STRATEGY_LEGACY,
            'max_subjects_per_period' => 1,
        ]);
        $shift = $this->shift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        $fixture = $this->pevaluacionFixture($lapso->id);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $fixture['pev']->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);
        TimetableSlot::create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $fixture['pev']->seccion_id,
            'locked' => true,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('preview', [
                'strategy' => TimetableCalendar::STRATEGY_LEGACY,
                'assignment' => [],
                'unassigned' => [$lesson->id],
            ]);

        $method = new \ReflectionMethod($component->instance(), 'previewSectionGrid');
        $method->setAccessible(true);
        $grid = $method->invoke($component->instance(), $fixture['pev']->seccion_id);

        $this->assertSame([], $grid);
    }

    public function test_step1_shows_selected_calendar_detail(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create();
        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
            'name' => 'Horario Detalle Test',
        ]);
        $shift = $this->shift('M', 'Mañana');
        TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'start_time' => '07:00:00',
            'end_time' => '08:00:00', 'is_break' => false,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->assertSee('Horario Detalle Test', false)
            // Panel de detalle con la información del calendario seleccionado.
            ->assertSee('Plan de estudio', false)
            ->assertSee('Bloques de horario del plan de estudio', false)
            ->assertSee('Lunes', false)
            ->assertSee('07:00–08:00', false)
            ->assertSee('Clase', false)
            ->assertSee('Slots asignados', false)
            ->assertSee('Conflictos', false)
            ->assertSee('Editar', false)
            // La lista de todos los calendarios ya no se renderiza en el paso 1.
            ->assertDontSee('Calendarios del lapso', false);
    }

    // ─── Fixtures ──────────────────────────────────────────────

    /**
     * Turno reutilizable (catálogo compartido con code único): firstOrCreate
     * para tolerar turnos 'M'/'T' preexistentes en la BD de tests.
     */
    private function shift($code = 'M', $name = 'Mañana', $start = '07:00:00', $end = '12:15:00'): TimetableShift
    {
        return TimetableShift::query()->firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'start_time' => $start, 'end_time' => $end],
        );
    }

    private function pevaluacionFixture($lapsoId): array
    {
        $user = User::factory()->create();
        $profesor = \App\Models\app\Academy\Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '2001', 'status_active' => 'true',
        ]);
        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create();
        $grado = \App\Models\app\Academy\Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = \App\Models\app\Academy\Seccion::factory()->create(['grado_id' => $grado->id]);
        $asignatura = \App\Models\app\Academy\Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 2]);
        $pensum = \App\Models\app\Academy\Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = \App\Models\app\Academy\Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapsoId,
        ]);

        return compact('profesor', 'pev');
    }
}
