<?php

namespace Tests\Feature\Timetable;

use App\Jobs\Timetable\NotifySubstituteJob;
use App\Livewire\Coordinacion\Timetable\TimetableSubstitutes;
use App\Livewire\Profesor\Timetable\SubstituteInbox;
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
use App\Services\Timetable\SubstituteService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §7 / §18 (v1.2) — Ausencias y suplentes.
 */
class TimetableSubstitutesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_coordinacion_can_register_absence(): void
    {
        $fixture = $this->fixture();
        $user = User::factory()->create(['is_coordinacion' => true]);

        Livewire::actingAs($user)
            ->test(TimetableSubstitutes::class)
            ->set('calendarId', $fixture['calendar']->id)
            ->set('absentProfesorId', $fixture['profesorA']->id)
            ->set('dateStart', '2026-09-14')
            ->set('dateEnd', '2026-09-18')
            ->call('registerAbsence')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('timetable_absences', [
            'calendar_id' => $fixture['calendar']->id,
            'profesor_id' => $fixture['profesorA']->id,
            'date_start' => '2026-09-14',
            'date_end' => '2026-09-18',
        ]);
    }

    public function test_affected_slots_match_weekday_range(): void
    {
        $fixture = $this->fixture();
        $absence = $fixture['service']->registerAbsence(
            calendarId: $fixture['calendar']->id,
            profesorId: $fixture['profesorA']->id,
            dateStart: '2026-09-14', // lunes
            dateEnd: '2026-09-14',   // solo lunes
        );

        $affected = $fixture['service']->affectedSlots($absence);
        $periods = $affected->map(fn ($s) => (int) $s->period->day_of_week)->unique();

        $this->assertNotEmpty($affected);
        $this->assertTrue($periods->every(fn ($day) => $day === 1), 'solo días lunes dentro del rango');
    }

    public function test_assign_substitute_creates_pending_and_queues_notification(): void
    {
        $fixture = $this->fixture();
        $absence = $fixture['service']->registerAbsence(
            calendarId: $fixture['calendar']->id,
            profesorId: $fixture['profesorA']->id,
            dateStart: '2026-09-14',
            dateEnd: '2026-09-14',
        );

        $slot = $fixture['service']->affectedSlots($absence)->first();

        $assignment = $fixture['service']->assignSubstitute($absence, $slot, $fixture['profesorB']->id);

        $this->assertDatabaseHas('timetable_substitute_assignments', [
            'id' => $assignment->id,
            'absence_id' => $absence->id,
            'slot_id' => $slot->id,
            'substitute_profesor_id' => $fixture['profesorB']->id,
            'status' => 'pending',
        ]);

        NotifySubstituteJob::dispatchSync($assignment->id);

        $this->assertDatabaseHas('notifications', [
            'type' => \App\Notifications\SubstituteAssignedNotification::class,
            'notifiable_id' => $fixture['profesorB']->user_id,
        ]);
    }

    public function test_profesor_confirms_and_declines_own_substitute(): void
    {
        $fixture = $this->fixture();
        $absence = $fixture['service']->registerAbsence(
            calendarId: $fixture['calendar']->id,
            profesorId: $fixture['profesorA']->id,
            dateStart: '2026-09-14',
            dateEnd: '2026-09-14',
        );
        $slot = $fixture['service']->affectedSlots($absence)->first();
        $assignment = $fixture['service']->assignSubstitute($absence, $slot, $fixture['profesorB']->id);

        $userB = User::find($fixture['profesorB']->user_id);

        Livewire::actingAs($userB)
            ->test(SubstituteInbox::class)
            ->call('confirmAssignment', $assignment->id);

        $this->assertDatabaseHas('timetable_substitute_assignments', [
            'id' => $assignment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_profesor_cannot_confirm_others_substitute(): void
    {
        $fixture = $this->fixture();
        $absence = $fixture['service']->registerAbsence(
            calendarId: $fixture['calendar']->id,
            profesorId: $fixture['profesorA']->id,
            dateStart: '2026-09-14',
            dateEnd: '2026-09-14',
        );
        $slot = $fixture['service']->affectedSlots($absence)->first();
        $assignment = $fixture['service']->assignSubstitute($absence, $slot, $fixture['profesorB']->id);

        // El profesor A (ausente) NO puede confirmar la suplencia de B.
        $userA = User::find($fixture['profesorA']->user_id);

        Livewire::actingAs($userA)
            ->test(SubstituteInbox::class)
            ->call('confirmAssignment', $assignment->id);

        $this->assertDatabaseHas('timetable_substitute_assignments', [
            'id' => $assignment->id,
            'status' => 'pending',
        ]);
    }

    // ─── Fixtures ──────────────────────────────────────────────

    private function fixture(): array
    {
        $userA = User::factory()->create();
        $profesorA = Profesor::create([
            'user_id' => $userA->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '3001', 'status_active' => 'true',
        ]);
        $userB = User::factory()->create();
        $profesorB = Profesor::create([
            'user_id' => $userB->id, 'name' => 'Beto', 'lastname' => 'Pérez',
            'ci_profesor' => '3002', 'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id]);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesorA->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = TimetableShift::factory()->create();

        // Períodos: lunes (day 1) y martes (day 2) con 2 bloques cada uno.
        $periods = [];
        foreach ([1, 2] as $day) {
            foreach ([1, 2] as $order) {
                $periods[] = TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        // Slot del profesor A en lunes bloque 1.
        $slot = TimetableSlot::create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $periods[0]->id,
            'profesor_id' => $profesorA->id,
            'seccion_id' => $seccion->id,
            'room_id' => null,
            'is_manual_override' => false,
            'locked' => false,
        ]);

        return [
            'calendar' => $calendar,
            'shift' => $shift,
            'lesson' => $lesson,
            'slot' => $slot,
            'periods' => $periods,
            'profesorA' => $profesorA,
            'profesorB' => $profesorB,
            'pev' => $pev,
            'seccion' => $seccion,
            'service' => app(SubstituteService::class),
        ];
    }
}
