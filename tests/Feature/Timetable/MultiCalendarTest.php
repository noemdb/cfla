<?php

namespace Tests\Feature\Timetable;

use App\Jobs\Timetable\GenerateTimetableJob;
use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Models\app\Academy\Lapso;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * PLAN-TIMETABLE-002 §6 — Varios horarios por lapso con máximo UNO activo.
 * Cubre: multi-borrador, democión en persist, índice DB, dryRun sin tocar al
 * activo, delete solo draft, activate() y resolución de lectores.
 */
class MultiCalendarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_wizard_lists_calendars_of_lapso(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id, 'name' => 'Plan A']);
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'name' => 'Plan B']);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->assertSet('calendarId', $active->id)
            ->assertCount('calendars', 2);

        $calendars = Livewire::actingAs($user)->test(TimetableWizard::class)->set('lapsoId', $lapso->id)->get('calendars');
        $this->assertSame([$active->id, $draft->id], array_column($calendars, 'id'));
    }

    public function test_switch_calendar_changes_context(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $wizard = Livewire::actingAs($user)->test(TimetableWizard::class);
        $wizard->set('lapsoId', $lapso->id)->assertSet('calendarId', $active->id);

        $wizard->set('calendarId', $draft->id)
            ->assertSet('calendarId', $draft->id)
            ->assertSet('generationState', null);
    }

    public function test_persist_demotes_previous_active(): void
    {
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $this->seedSolverFixture($draft);

        GenerateTimetableJob::dispatchSync($draft->id, dryRun: false);

        $this->assertSame(TimetableCalendar::STATUS_ARCHIVED, $active->fresh()->status);
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $draft->fresh()->status);
        $this->assertSame(1, $draft->fresh()->version);
        $this->assertSame(2, TimetableSlot::query()->where('calendar_id', $draft->id)->count());
    }

    public function test_dry_run_keeps_active_calendar_intact(): void
    {
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        $this->seedSolverFixture($draft);

        GenerateTimetableJob::dispatchSync($draft->id, dryRun: true);

        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $active->fresh()->status);
        $this->assertSame(TimetableCalendar::STATUS_DRAFT, $draft->fresh()->status);
        $this->assertNotNull($draft->fresh()->preview_payload);
    }

    public function test_db_forbids_two_active_per_lapso(): void
    {
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $other = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        try {
            $other->update(['status' => TimetableCalendar::STATUS_ACTIVE]);
            $this->fail('El índice único uq_active_lapso debió rechazar el segundo activo.');
        } catch (QueryException $e) {
            $this->assertStringContainsString('uq_active_lapso', $e->getMessage());
        }

        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $active->fresh()->status);
        $this->assertSame(TimetableCalendar::STATUS_DRAFT, $other->fresh()->status);
    }

    public function test_activate_promotes_draft_and_archives_previous_active(): void
    {
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->makeSlotFor($draft);

        $draft->activate();

        $this->assertSame(TimetableCalendar::STATUS_ARCHIVED, $active->fresh()->status);
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $draft->fresh()->status);
    }

    public function test_delete_only_drafts(): void
    {
        $lapso = Lapso::factory()->create();
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $archived = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'status' => TimetableCalendar::STATUS_ARCHIVED,
        ]);

        $this->assertTrue($draft->deleteDraft());
        $this->assertFalse($active->deleteDraft());
        $this->assertFalse($archived->deleteDraft());

        $this->assertDatabaseMissing('timetable_calendars', ['id' => $draft->id]);
        $this->assertDatabaseHas('timetable_calendars', ['id' => $active->id]);
        $this->assertDatabaseHas('timetable_calendars', ['id' => $archived->id]);
    }

    public function test_wizard_rejects_activating_draft_without_slots(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->call('activateCalendar', $draft->id);

        $this->assertSame(TimetableCalendar::STATUS_DRAFT, $draft->fresh()->status);
    }

    public function test_wizard_activate_draft_with_slots(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->makeSlotFor($draft);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('lapsoId', $lapso->id)
            ->call('activateCalendar', $draft->id);

        $this->assertSame(TimetableCalendar::STATUS_ARCHIVED, $active->fresh()->status);
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $draft->fresh()->status);
    }

    public function test_readers_resolve_active_of_current_lapso(): void
    {
        $lapso = Lapso::factory()->create([
            'finicial' => now()->subMonth(),
            'ffinal' => now()->addMonth(),
        ]);
        TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id, 'name' => 'Activo vigente']);
        TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'name' => 'Archivado',
            'status' => TimetableCalendar::STATUS_ARCHIVED,
        ]);
        TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'name' => 'Otro borrador']);

        $resolved = TimetableCalendar::activeForCurrentLapso();
        $this->assertNotNull($resolved);
        $this->assertSame('Activo vigente', $resolved->name);
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $resolved->status);
    }

    public function test_readers_fallback_to_latest_active_without_current_lapso(): void
    {
        // Aisla el escenario: sin lapso vigente en la BD.
        Lapso::query()->delete();

        $oldLapso = Lapso::factory()->create([
            'finicial' => now()->subYears(2),
            'ffinal' => now()->subYears(2)->addMonth(),
        ]);
        TimetableCalendar::factory()->active()->create(['lapso_id' => $oldLapso->id, 'name' => 'Activo histórico']);

        $resolved = TimetableCalendar::activeForCurrentLapso();
        $this->assertNotNull($resolved);
        $this->assertSame('Activo histórico', $resolved->name);
    }

    // ─── Fixtures ──────────────────────────────────────────────

    private function seedSolverFixture(TimetableCalendar $calendar): void
    {
        $shift = TimetableShift::factory()->create();

        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        [$profesor, $pev] = $this->pevaluacionFixture($calendar->lapso_id);

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
    }

    /**
     * Slot válido para un calendario (lección + período + slot con FK reales).
     */
    private function makeSlotFor(TimetableCalendar $calendar): void
    {
        $shift = TimetableShift::factory()->create();
        [$profesor, $pev] = $this->pevaluacionFixture($calendar->lapso_id);

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $pev->seccion_id,
        ]);
    }

    private function pevaluacionFixture($lapsoId): array
    {
        $user = User::factory()->create();
        $profesor = \App\Models\app\Academy\Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '3001', 'status_active' => 'true',
        ]);
        $pestudio = \App\Models\app\Academy\Pestudio::factory()->create();
        $grado = \App\Models\app\Academy\Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = \App\Models\app\Academy\Seccion::factory()->create(['grado_id' => $grado->id]);
        $asignatura = \App\Models\app\Academy\Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = \App\Models\app\Academy\Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = \App\Models\app\Academy\Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapsoId,
        ]);

        return [$profesor, $pev];
    }
}
