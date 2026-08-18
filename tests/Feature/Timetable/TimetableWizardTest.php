<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Livewire\Planning\Timetable\TimetableWizard as PlanningTimetableWizard;
use App\Models\app\Academy\Lapso;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    public function test_step1_creates_calendar_with_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('calendarName', 'Horario Test')
            ->call('createCalendar')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 1)
            ->assertSet('calendarId', TimetableCalendar::query()->where('lapso_id', $lapso->id)->first()->id);
    }

    public function test_multiple_drafts_allowed_per_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->set('calendarName', 'Alternativa B')
            ->call('createCalendar')
            ->assertHasNoErrors()
            ->assertSet('calendarId', TimetableCalendar::query()->where('lapso_id', $lapso->id)->orderByDesc('id')->first()->id);

        $this->assertSame(2, TimetableCalendar::query()->where('lapso_id', $lapso->id)->count());
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

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('shiftCode', 'M')
            ->set('shiftName', 'Mañana')
            ->call('createShift')
            ->assertSet('shiftId', TimetableShift::query()->orderByDesc('id')->first()->id)
            ->assertHasNoErrors();

        $shift = TimetableShift::query()->orderByDesc('id')->first();

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('shiftId', $shift->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 2);

        $this->assertSame(30, TimetablePeriod::query()->where('calendar_id', $calendar->id)->count());
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

    public function test_step3_derives_blocks_from_asignatura_hours(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'period_minutes' => 60]);
        $shift = TimetableShift::factory()->create();

        $fixture = $this->pevaluacionFixture($lapso->id);
        $pev = $fixture['pev'];

        $wizard = Livewire::actingAs($user)->test(TimetableWizard::class);
        $wizard->set('calendarId', $calendar->id);
        $wizard->set('selectedPevs', [$pev->id]);
        $wizard->call('loadLessons');

        $lessons = $wizard->get('lessons');
        $this->assertCount(1, $lessons);
        $this->assertSame(3, $lessons[0]['weekly_blocks_t']); // 3 h semanales → 3 bloques de 60'
        $this->assertSame(2, $lessons[0]['weekly_blocks_p']); // 2 h semanales → 2 bloques
    }

    public function test_step3_saves_lessons(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = TimetableShift::factory()->create();
        $fixture = $this->pevaluacionFixture($lapso->id);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('selectedPevs', [$fixture['pev']->id])
            ->call('loadLessons')
            ->call('saveLessons')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 4);

        $this->assertSame(1, TimetableLesson::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_step5_runs_dry_run_and_shows_preview(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = TimetableShift::factory()->create();

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
            ->call('runDryRun')
            ->assertSet('generationState', 'preview_ready');

        $preview = TimetableCalendar::find($calendar->id)->preview_payload;
        $this->assertNotNull($preview);
        $this->assertTrue($preview['dry_run']);
    }

    // ─── Fixtures ──────────────────────────────────────────────

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
