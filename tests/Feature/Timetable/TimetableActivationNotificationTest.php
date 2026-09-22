<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard as CoordinacionWizard;
use App\Livewire\Planning\Timetable\TimetableWizard as PlanningWizard;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use App\Notifications\TimetableCalendarActivatedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Notificación a is_planner al activar un calendario desde
 * /app/planning/timetable (no desde coordinación).
 */
class TimetableActivationNotificationTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_activar_calendario_en_planning_notifica_a_planners(): void
    {
        Notification::fake();
        Event::fake();

        $planner = User::factory()->create(['is_planner' => true]);
        $otherPlanner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'name' => 'Plan Test']);
        $this->makeSlotFor($draft);

        Livewire::actingAs($planner)
            ->test(PlanningWizard::class)
            ->set('lapsoId', $lapso->id)
            ->call('activateCalendar', $draft->id);

        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $draft->fresh()->status);

        Notification::assertSentTo(
            $otherPlanner,
            TimetableCalendarActivatedNotification::class,
            fn ($n) => $n->calendarId === $draft->id && $n->calendarName === 'Plan Test'
        );
        Notification::assertSentTo($planner, TimetableCalendarActivatedNotification::class);
    }

    public function test_activar_desde_coordinacion_tambien_notifica_a_planners(): void
    {
        Notification::fake();
        Event::fake();

        $coordinator = User::factory()->create(['is_coordinacion' => true]);
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();
        $draft = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'name' => 'Plan Coord']);
        $this->makeSlotFor($draft);

        Livewire::actingAs($coordinator)
            ->test(CoordinacionWizard::class)
            ->set('lapsoId', $lapso->id)
            ->call('activateCalendar', $draft->id);

        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $draft->fresh()->status);

        Notification::assertSentTo(
            $planner,
            TimetableCalendarActivatedNotification::class,
            fn ($n) => $n->calendarId === $draft->id && $n->calendarName === 'Plan Coord'
        );
    }

    public function test_activar_un_calendario_ya_activo_no_notifica(): void
    {
        Notification::fake();
        Event::fake();

        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();
        $active = TimetableCalendar::factory()->active()->create(['lapso_id' => $lapso->id]);
        $this->makeSlotFor($active);

        Livewire::actingAs($planner)
            ->test(PlanningWizard::class)
            ->set('lapsoId', $lapso->id)
            ->call('activateCalendar', $active->id);

        Notification::assertNothingSent();
    }

    /**
     * Slot válido para un calendario (lección + período + slot con FK reales).
     */
    private function makeSlotFor(TimetableCalendar $calendar): void
    {
        $shift = $this->makeShift();
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

    /**
     * @return array{0: Profesor, 1: Pevaluacion}
     */
    private function pevaluacionFixture($lapsoId): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => 'N-'.uniqid(), 'status_active' => 'true',
        ]);

        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'lapso_id' => $lapsoId,
        ]);

        return [$profesor, $pev];
    }
}
