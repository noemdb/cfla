<?php

namespace Tests\Feature\Planning\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\BroadcastEvent;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Services\Lms\BroadcastAudit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Opción 10 — Auditoría + métricas en tiempo real.
 * BroadcastAudit::log() registra la fila en `broadcast_events` + línea JSON en
 * el canal de log `broadcast`. El ACK marca `delivered` de forma idempotente.
 * El comando `broadcast:stats` agrega métricas de la ventana.
 */
class BroadcastAuditTest extends TestCase
{
    use DatabaseTransactions;

    private function makeActivity(): Activity
    {
        $peducativo = Peducativo::factory()->create(['status_active' => 'true']);
        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => $peducativo->id,
            'status_active' => 'true',
            'planning_module' => 1,
        ]);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['pestudio_id' => $pestudio->id]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '87654321',
            'status_active' => 'true',
        ]);

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        return Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic' => 'Lección',
            'status' => true,
        ]);
    }

    public function test_log_registra_fila_y_log_json(): void
    {
        // Canal de log apuntado a un archivo temporal para verificar la línea JSON.
        $logFile = storage_path('logs/broadcast-test-'.uniqid().'.log');
        config(['logging.channels.broadcast' => [
            'driver' => 'single',
            'path' => $logFile,
            'level' => 'info',
        ]]);

        $actor = User::factory()->create(['is_planner' => true]);
        $recipients = User::factory()->count(3)->create();

        $activity = $this->makeActivity();

        $record = app(BroadcastAudit::class)->log(
            event: 'lesson.scheduled',
            subject: $activity,
            actorUserId: $actor->id,
            recipientIds: $recipients->pluck('id')->all(),
        );

        $this->assertDatabaseHas('broadcast_events', [
            'id' => $record->id,
            'event' => 'lesson.scheduled',
            'subject_type' => (new Activity)->getMorphClass(),
            'subject_id' => $activity->id,
            'actor_user_id' => $actor->id,
            'channel_count' => 3,
            'delivered' => false,
        ]);

        $this->assertSame($recipients->pluck('id')->sort()->values()->all(), $record->recipient_ids);

        $this->assertTrue(file_exists($logFile), 'El canal broadcast no escribió el log');
        $content = file_get_contents($logFile);
        $this->assertStringContainsString('broadcast.dispatched', $content);
        $this->assertStringContainsString('"event_id":'.$record->id, $content);
        $this->assertStringContainsString('lesson.scheduled', $content);

        @unlink($logFile);
    }

    public function test_ack_marca_delivered_de_forma_idempotente(): void
    {
        $event = BroadcastEvent::create([
            'event' => 'lesson.scheduled',
            'channel_count' => 2,
            'delivered' => false,
        ]);

        $audit = app(BroadcastAudit::class);

        $this->assertTrue($audit->ack($event->id));
        $this->assertDatabaseHas('broadcast_events', ['id' => $event->id, 'delivered' => true]);

        // Idempotente: repetir no rompe.
        $this->assertTrue($audit->ack($event->id));
        $this->assertSame(1, BroadcastEvent::where('id', $event->id)->count());
    }

    public function test_ack_con_id_inexistente_devuelve_false(): void
    {
        $this->assertFalse(app(BroadcastAudit::class)->ack(999999));
    }

    public function test_endpoint_ack_marca_delivered(): void
    {
        $event = BroadcastEvent::create([
            'event' => 'lesson.scheduled',
            'channel_count' => 2,
            'delivered' => false,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/broadcast/ack', ['event_id' => $event->id])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('broadcast_events', ['id' => $event->id, 'delivered' => true]);
    }

    public function test_endpoint_ack_acepta_sesion_web_sin_token(): void
    {
        // Escenario real del browser: el ACK se envía desde una página Livewire
        // autenticada por sesión HTTP (cookies), no con un token Sanctum Bearer.
        // EnsureFrontendRequestsAreStateful (grupo `api`) puentea la sesión web.
        $event = BroadcastEvent::create([
            'event' => 'lesson.scheduled',
            'channel_count' => 2,
            'delivered' => false,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/broadcast/ack', ['event_id' => $event->id])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('broadcast_events', ['id' => $event->id, 'delivered' => true]);
    }

    public function test_endpoint_ack_valida_event_id(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/broadcast/ack', ['event_id' => 999999])
            ->assertJsonValidationErrors('event_id');
    }

    public function test_endpoint_ack_requiere_auth(): void
    {
        $this->postJson('/api/broadcast/ack', ['event_id' => 1])->assertUnauthorized();
    }

    public function test_comando_stats_muestra_metricas(): void
    {
        BroadcastEvent::create([
            'event' => 'lesson.scheduled',
            'channel_count' => 3,
            'delivered' => true,
        ]);
        BroadcastEvent::create([
            'event' => 'lesson.scheduled',
            'channel_count' => 2,
            'delivered' => false,
        ]);

        $this->artisan('broadcast:stats', ['--hours' => 24])
            ->expectsOutputToContain('Eventos emitidos')
            ->expectsOutputToContain('2')
            ->assertExitCode(0);
    }
}
