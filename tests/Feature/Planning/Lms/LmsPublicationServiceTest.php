<?php

namespace Tests\Feature\Planning\Lms;

use App\Events\Lms\LessonScheduled;
use App\Jobs\BroadcastLessonScheduled;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Notifications\LessonScheduledForApproval;
use App\Services\Lms\LmsPublicationService;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Opción 11 — Emisión centralizada y segura: LmsPublicationService::publish()
 * es el ÚNICO punto de emisión. Verifica que un SCHEDULED notifique una sola
 * vez (broadcast + notificación DB) y que la publicación autorizada no emita.
 * Opción 2 (H3): los destinatarios se scopean por rol — leadership solo recibe
 * si la asignatura de la lección está en sus áreas; coordinación solo si el
 * pestudio está en su scope de peducativos.
 */
class LmsPublicationServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeProfesor(): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '12345678',
            'status_active' => 'true',
        ]);

        return ['id' => $profesor->id, 'user_id' => $user->id];
    }

    private function makeLesson(array $options = []): array
    {
        // Scope de coordinación: el pestudio vive en un peducativo del coordinador.
        $coordinator = $options['coordinator'] ?? null;
        $peducativo = $coordinator
            ? Peducativo::factory()->create(['manager_id' => $coordinator->id, 'status_active' => 'true'])
            : Peducativo::factory()->create(['status_active' => 'true']);

        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => $peducativo->id,
            'status_active' => 'true',
            'planning_module' => 1,
        ]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);

        // Scope de leadership: la asignatura entra en un área del líder.
        $leader = $options['leader'] ?? null;
        if ($leader) {
            $area = AreaConocimiento::create([
                'leader_id' => $leader->id,
                'name' => 'Área de Ciencias',
                'code' => 'CIEN',
                'peducativo_id' => $peducativo->id,
                'pestudio_id' => $pestudio->id,
                'order' => 1,
            ]);
            $asignatura->areasConocimiento()->attach($area->id);
        }

        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $this->makeProfesor()['id'],
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic' => 'Lección de prueba',
            'status' => true,
        ]);

        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Sección 1',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        return [$activity, $peducativo, $asignatura];
    }

    /** @test */
    public function publish_sin_autorizacion_notifica_una_sola_vez_broadcast_y_db(): void
    {
        Event::fake();
        Notification::fake();

        $teacher = $this->makeProfesor();
        $planner = User::factory()->create(['is_planner' => true]);
        $admin = User::factory()->create(['is_admin' => true]);
        $leader = User::factory()->create(['is_leadership' => true]);
        $coordinator = User::factory()->create(['is_coordinacion' => true]);

        [$activity] = $this->makeLesson([
            'leader' => $leader,
            'coordinator' => $coordinator,
        ]);

        app(LmsPublicationService::class)->publish(
            $activity,
            ['publish_at' => now()->addDay()],
            $teacher['user_id'],
            false
        );

        $this->assertDatabaseHas('lms_activity_publications', [
            'activity_id' => $activity->id,
            'status' => 'SCHEDULED',
            'published_at' => null,
        ]);

        Event::assertDispatchedTimes(LessonScheduled::class, 1);
        foreach ([$planner, $admin, $leader, $coordinator] as $recipient) {
            Notification::assertSentToTimes($recipient, LessonScheduledForApproval::class, 1);
        }

        // Auditoría (Opción 10): una fila por evento en el punto central.
        // channel_count es dinámico (hay más usuarios globales preexistentes),
        // así que solo verificamos la identidad del evento y sus datos fijos.
        $this->assertDatabaseHas('broadcast_events', [
            'event' => 'lesson.scheduled',
            'subject_type' => (new Activity)->getMorphClass(),
            'subject_id' => $activity->id,
            'actor_user_id' => $teacher['user_id'],
            'delivered' => false,
        ]);
    }

    /** @test */
    public function publish_sin_autorizacion_no_notifica_a_leadership_fuera_de_su_area(): void
    {
        Event::fake();
        Notification::fake();

        $teacher = $this->makeProfesor();
        $inScopeLeader = User::factory()->create(['is_leadership' => true]);
        $outOfScopeLeader = User::factory()->create(['is_leadership' => true]);

        [$activity] = $this->makeLesson(['leader' => $inScopeLeader]);

        // Área asignada a outOfScopeLeader que NO cubre la asignatura de la lección.
        $otherPeducativo = Peducativo::factory()->create(['status_active' => 'true']);
        $otherPestudio = Pestudio::factory()->create([
            'peducativo_id' => $otherPeducativo->id,
            'status_active' => 'true',
            'planning_module' => 1,
        ]);
        $otherArea = AreaConocimiento::create([
            'leader_id' => $outOfScopeLeader->id,
            'name' => 'Área de Letras',
            'code' => 'LETRAS',
            'peducativo_id' => $otherPeducativo->id,
            'pestudio_id' => $otherPestudio->id,
            'order' => 2,
        ]);
        Asignatura::factory()->create(['pestudio_id' => $otherPestudio->id])
            ->areasConocimiento()->attach($otherArea->id);

        app(LmsPublicationService::class)->publish(
            $activity,
            ['publish_at' => now()->addDay()],
            $teacher['user_id'],
            false
        );

        // El líder que SÍ cubre la asignatura recibe; el otro NO.
        Notification::assertSentToTimes($inScopeLeader, LessonScheduledForApproval::class, 1);
        Notification::assertNotSentTo($outOfScopeLeader, LessonScheduledForApproval::class);
    }

    /** @test */
    public function publish_sin_autorizacion_no_notifica_a_coordinacion_fuera_de_su_scope(): void
    {
        Event::fake();
        Notification::fake();

        $teacher = $this->makeProfesor();
        $outOfScopeCoordinator = User::factory()->create(['is_coordinacion' => true]);

        // Lección en un peducativo NO gestionado por outOfScopeCoordinator.
        [$activity] = $this->makeLesson();

        app(LmsPublicationService::class)->publish(
            $activity,
            ['publish_at' => now()->addDay()],
            $teacher['user_id'],
            false
        );

        Notification::assertNotSentTo($outOfScopeCoordinator, LessonScheduledForApproval::class);
    }

    /** @test */
    public function publish_autorizado_no_emite_broadcast_ni_notificacion(): void
    {
        Event::fake();
        Notification::fake();

        $teacher = $this->makeProfesor();
        [$activity] = $this->makeLesson();
        User::factory()->create(['is_planner' => true]);

        app(LmsPublicationService::class)->publish(
            $activity,
            ['publish_at' => now()],
            $teacher['user_id'],
            true
        );

        $this->assertDatabaseHas('lms_activity_publications', [
            'activity_id' => $activity->id,
            'status' => 'PUBLISHED',
        ]);

        Event::assertNotDispatched(LessonScheduled::class);
        Notification::assertNothingSent();
    }

    /** @test */
    public function reprogramar_no_acumula_notificaciones(): void
    {
        Event::fake();
        Notification::fake();

        $teacher = $this->makeProfesor();
        [$activity] = $this->makeLesson();
        $planner = User::factory()->create(['is_planner' => true]);

        $service = app(LmsPublicationService::class);

        $service->publish($activity, ['publish_at' => now()->addDay()], $teacher['user_id'], false);
        $service->publish($activity, ['publish_at' => now()->addDays(2)], $teacher['user_id'], false);

        // 1 broadcast + 1 notificación por cada llamada, nunca acumulados
        Event::assertDispatchedTimes(LessonScheduled::class, 2);
        Notification::assertSentToTimes($planner, LessonScheduledForApproval::class, 2);
    }

    /** @test
     * Opción 9 — Crash-guard: verifica que el código del crash-guard existe
     * (try/catch alrededor del dispatch en notifyScheduled). El flujo normal
     * sigue funcionando: notificación DB + broadcast + log SCHEDULE.
     * La prueba de fallo real de Reverb requiere integración con Reverb caído.
     */
    public function crash_guard_estructura_try_catch_existe_y_flujo_normal_funciona(): void
    {
        Event::fake();
        Notification::fake();
        Queue::fake();

        $teacher = $this->makeProfesor();
        [$activity] = $this->makeLesson();
        $planner = User::factory()->create(['is_planner' => true]);

        $service = app(LmsPublicationService::class);
        $service->publish($activity, ['publish_at' => now()->addDay()], $teacher['user_id'], false);

        // Flujo normal: notificación DB enviada
        Notification::assertSentToTimes($planner, LessonScheduledForApproval::class, 1);

        // Broadcast disparado (recolectado por Event::fake)
        Event::assertDispatchedTimes(LessonScheduled::class, 1);

        // Log de actividad SCHEDULE registrado
        $this->assertDatabaseHas('lms_activity_logs', [
            'activity_id' => $activity->id,
            'event' => 'SCHEDULE',
        ]);

        // Push inmediato OK: NO se encola job de respaldo (no hay fallo).
        Queue::assertNothingPushed(BroadcastLessonScheduled::class);
    }

    /** @test
     * Opción 9 — Crash-guard con fallo REAL de Reverb: el broadcaster lanza
     * excepción (Reverb caído). El request no se rompe, la notificación DB se
     * persiste y se encola el job de respaldo BroadcastLessonScheduled con
     * reintentos/backoff para re-emitir cuando Reverb vuelva.
     */
    public function reverb_caido_no_rompe_request_y_encola_job_respaldo(): void
    {
        Event::fake();
        Notification::fake();
        Queue::fake();

        // Forzamos el fallo: el dispatcher lanza para el evento de broadcast.
        $dispatcher = new class extends Dispatcher
        {
            public function dispatch($event, $payload = [], $halt = false)
            {
                if ($event instanceof LessonScheduled) {
                    throw new \Exception('Connection to reverb failed [tcp://127.0.0.1:8090]');
                }

                return parent::dispatch($event, $payload, $halt);
            }
        };
        app()->instance('events', $dispatcher);

        $teacher = $this->makeProfesor();
        [$activity] = $this->makeLesson();
        $planner = User::factory()->create(['is_planner' => true]);

        $service = app(LmsPublicationService::class);
        $service->publish($activity, ['publish_at' => now()->addDay()], $teacher['user_id'], false);

        // Request no roto: notificación DB persistida.
        Notification::assertSentToTimes($planner, LessonScheduledForApproval::class, 1);

        // Log SCHEDULE registrado.
        $this->assertDatabaseHas('lms_activity_logs', [
            'activity_id' => $activity->id,
            'event' => 'SCHEDULE',
        ]);

        // Job de respaldo encolado para reintentar el broadcast.
        Queue::assertPushed(BroadcastLessonScheduled::class, 1);

        // Auditoría (Opción 10): la fila se registra incluso si el push falla.
        $this->assertDatabaseHas('broadcast_events', [
            'event' => 'lesson.scheduled',
            'subject_id' => $activity->id,
            'delivered' => false,
        ]);
    }
}
