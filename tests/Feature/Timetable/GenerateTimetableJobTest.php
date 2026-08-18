<?php

namespace Tests\Feature\Timetable;

use App\Jobs\Timetable\GenerateTimetableJob;
use App\Jobs\Timetable\NotifyTimetableChangesJob;
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
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §6 / §18 — Flujo del motor en cola (dry-run + persistir).
 * Usa la BD MySQL real (s2627) con transactions, igual que el resto del repo.
 */
class GenerateTimetableJobTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dry_run_stores_preview_payload_without_persisting_slots(): void
    {
        $fixture = $this->smallFeasibleFixture();

        GenerateTimetableJob::dispatchSync($fixture['calendar']->id, dryRun: true);

        $calendar = $fixture['calendar']->fresh();
        $this->assertNotNull($calendar->preview_payload);
        $this->assertArrayHasKey('assignment', $calendar->preview_payload);
        $this->assertTrue($calendar->preview_payload['dry_run']);
        $this->assertSame('draft', $calendar->status);
        $this->assertSame(0, TimetableSlot::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_dry_run_assigns_all_lessons_on_feasible_dataset(): void
    {
        $fixture = $this->smallFeasibleFixture();

        GenerateTimetableJob::dispatchSync($fixture['calendar']->id, dryRun: true);

        $payload = $fixture['calendar']->fresh()->preview_payload;
        $this->assertSame([], $payload['unassigned']);
        $this->assertCount(2, $payload['assignment']);
    }

    public function test_confirm_persists_slots_and_sets_status_active(): void
    {
        $fixture = $this->smallFeasibleFixture();

        GenerateTimetableJob::dispatchSync($fixture['calendar']->id, dryRun: false);

        $calendar = $fixture['calendar']->fresh();
        $this->assertSame('active', $calendar->status);
        $this->assertNull($calendar->preview_payload);
        $this->assertGreaterThan(0, $calendar->version);
        $this->assertSame(4, TimetableSlot::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_persisted_slots_have_no_double_booking_per_teacher(): void
    {
        $fixture = $this->smallFeasibleFixture();

        GenerateTimetableJob::dispatchSync($fixture['calendar']->id, dryRun: false);

        $slots = TimetableSlot::query()->where('calendar_id', $fixture['calendar']->id)->get();
        $seen = [];
        foreach ($slots as $slot) {
            $key = $slot->period_id.':'.$slot->profesor_id;
            $this->assertArrayNotHasKey($key, $seen, "doble-booking docente en período {$slot->period_id}");
            $seen[$key] = true;
        }
    }

    public function test_infeasible_dataset_reports_conflicts(): void
    {
        $fixture = $this->smallFeasibleFixture();

        // Un solo docente con carga excesiva → lección sin asignar.
        $this->addOverloadedLesson($fixture);

        GenerateTimetableJob::dispatchSync($fixture['calendar']->id, dryRun: true);

        $payload = $fixture['calendar']->fresh()->preview_payload;
        $this->assertNotEmpty($payload['unassigned']);
    }

    public function test_job_is_queued_not_run_inline(): void
    {
        Queue::fake();
        $fixture = $this->smallFeasibleFixture();

        GenerateTimetableJob::dispatch($fixture['calendar']->id);

        Queue::assertPushed(GenerateTimetableJob::class);
    }

    public function test_confirm_notifies_affected_teachers_and_coordinators(): void
    {
        $fixture = $this->smallFeasibleFixture();

        $coordinator = \App\Models\User::factory()->create(['is_coordinacion' => true]);

        // El diff se calcula ANTES de persistir (igual que en producción, §15
        // "diff antes de aplicar"): con el calendario sin slots, todas las
        // lecciones son nuevas → ambos docentes quedan afectados.
        $calendar = $fixture['calendar']->fresh();
        $job = new GenerateTimetableJob($calendar->id, dryRun: false);
        $solver = new \ReflectionMethod($job, 'runSolver');
        $solver->setAccessible(true);
        $computeDiff = new \ReflectionMethod($job, 'computeDiff');
        $computeDiff->setAccessible(true);
        $result = $solver->invoke($job, $calendar);
        $diff = $computeDiff->invoke($job, $calendar, $result);

        // Confirmar (persiste slots) y procesar el job de notificaciones en
        // cola (síncrono) con el mismo diff que encola GenerateTimetableJob.
        GenerateTimetableJob::dispatchSync($fixture['calendar']->id, dryRun: false);

        NotifyTimetableChangesJob::dispatchSync($fixture['calendar']->id, $diff);

        // El usuario del profesor A recibe notificación DB.
        $this->assertDatabaseHas('notifications', [
            'type' => \App\Notifications\TimetableChangedNotification::class,
            'notifiable_id' => $fixture['profesorA']->user_id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'type' => \App\Notifications\TimetableChangedNotification::class,
            'notifiable_id' => $coordinator->id,
        ]);
    }

    public function test_confirm_queues_notification_job_separately(): void
    {
        Queue::fake();

        $fixture = $this->smallFeasibleFixture();

        // handle() corre inline; NotifyTimetableChangesJob::dispatch() se
        // captura por el fake (no se ejecuta en el mismo request).
        (new GenerateTimetableJob($fixture['calendar']->id, dryRun: false))->handle();

        Queue::assertPushed(NotifyTimetableChangesJob::class);
        Queue::assertPushed(NotifyTimetableChangesJob::class, fn (NotifyTimetableChangesJob $job) => $job->calendarId === $fixture['calendar']->id);
    }

    public function test_notification_diff_only_mentions_changed_teachers(): void
    {
        $fixture = $this->smallFeasibleFixture();
        $coordinator = \App\Models\User::factory()->create(['is_coordinacion' => true]);

        // Publicar una vez → 2 lecciones asignadas.
        GenerateTimetableJob::dispatchSync($fixture['calendar']->id, dryRun: false);

        // Segunda generación: idéntica → diff vacío (0 cambios).
        // El diff no debe incluir profesores "afectados" si nada cambió.
        $calendar = $fixture['calendar']->fresh();
        $job = new GenerateTimetableJob($calendar->id, dryRun: false);

        $reflection = new \ReflectionMethod($job, 'computeDiff');
        $reflection->setAccessible(true);
        $solver = new \ReflectionMethod($job, 'runSolver');
        $solver->setAccessible(true);
        $result = $solver->invoke($job, $calendar);
        $diff = $reflection->invoke($job, $calendar, $result);

        $this->assertSame(2, $diff['total_lessons']);
        $this->assertSame(0, $diff['changed']);
        $this->assertSame(0, $diff['removed']);
        $this->assertSame([], $diff['profesores_afectados']);
    }

    // ─── Fixtures ──────────────────────────────────────────────

    /**
     * Calendario con 5 días × 3 períodos, 2 lecciones (2 docentes distintos,
     * 2 bloques c/u) → factible.
     */
    private function smallFeasibleFixture(): array
    {
        $user = User::factory()->create();
        $profesorA = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1001', 'status_active' => 'true',
        ]);
        $profesorB = Profesor::create([
            'user_id' => $user->id, 'name' => 'Beto', 'lastname' => 'Pérez',
            'ci_profesor' => '1002', 'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id]);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id]);

        $asignaturaA = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);
        $asignaturaB = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);

        $pensumA = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignaturaA->id,
        ]);
        $pensumB = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignaturaB->id,
        ]);

        $lapso = Lapso::factory()->create();
        $pevA = Pevaluacion::factory()->create([
            'profesor_id' => $profesorA->id, 'seccion_id' => $seccionA->id,
            'pensum_id' => $pensumA->id, 'lapso_id' => $lapso->id,
        ]);
        $pevB = Pevaluacion::factory()->create([
            'profesor_id' => $profesorB->id, 'seccion_id' => $seccionB->id,
            'pensum_id' => $pensumB->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = TimetableShift::factory()->create();

        // 5 días × 3 períodos = 15 períodos (60 min c/u, sin recreos).
        $periods = [];
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                $periods[] = TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id,
                    'shift_id' => $shift->id,
                    'day_of_week' => $day,
                    'order_in_day' => $order,
                    'is_break' => false,
                ]);
            }
        }

        $lessonA = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevA->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevB->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        return compact('calendar', 'shift', 'periods', 'lessonA', 'lessonB', 'profesorA', 'profesorB');
    }

    /**
     * Añade una tercera lección al mismo docente A con bloques suficientes
     * como para saturarlo (15 períodos, 3 bloques + 13 más → infactible).
     */
    private function addOverloadedLesson(array $fixture): void
    {
        $asignaturaC = Asignatura::factory()->create(['hour_t_week' => 9, 'hour_p_week' => 0]);
        $pensumC = Pensum::factory()->create([
            'pestudio_id' => $fixture['lessonA']->pevaluacion->pensum->pestudio_id,
            'grado_id' => $fixture['lessonA']->pevaluacion->seccion->grado_id,
            'asignatura_id' => $asignaturaC->id,
        ]);
        $seccionC = Seccion::factory()->create([
            'grado_id' => $fixture['lessonA']->pevaluacion->seccion->grado_id,
        ]);
        $pevC = Pevaluacion::factory()->create([
            'profesor_id' => $fixture['profesorA']->id,
            'seccion_id' => $seccionC->id,
            'pensum_id' => $pensumC->id,
            'lapso_id' => $fixture['calendar']->lapso_id,
        ]);

        TimetableLesson::factory()->create([
            'calendar_id' => $fixture['calendar']->id, 'pevaluacion_id' => $pevC->id,
            'shift_id' => $fixture['shift']->id,
            'weekly_blocks_t' => 14, 'weekly_blocks_p' => 0,
        ]);
    }
}
