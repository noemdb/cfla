<?php

namespace Tests\Feature\App\Notifications;

use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Idempotencia a nivel de BD (ítem 8).
 *
 * El anti-spam por no leídas es un SELECT y luego un INSERT: dos jobs
 * concurrentes, o el reintento de un job, pueden pasar ambos el SELECT y
 * duplicar el aviso. La tabla `notification_dedupe` con índice único lo
 * cierra: el segundo envío choca contra la base de datos y se omite.
 */
class NotificationIdempotencyTest extends TestCase
{
    use DatabaseTransactions;

    private function service(): NotificationService
    {
        return app(NotificationService::class);
    }

    private function notification(int $activityId = 1, int $pevaluacionId = 10): ActivityCreatedNotification
    {
        return new ActivityCreatedNotification(
            type: 'activity_created',
            message: 'Actividad '.$activityId,
            url: route('app.notifications.index'),
            activityId: $activityId,
            pevaluacionId: $pevaluacionId,
            asignaturaName: 'MATEMÁTICAS',
        );
    }

    private function claimsFor(User $user): int
    {
        return DB::table(NotificationService::DEDUPE_TABLE)
            ->where('notifiable_id', $user->id)
            ->count();
    }

    public function test_el_primer_envio_pasa_y_reclama(): void
    {
        $user = User::factory()->create();

        $sent = $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);

        $this->assertSame(1, $sent);
        $this->assertSame(1, $user->notifications()->count());
        $this->assertSame(1, $this->claimsFor($user));
    }

    public function test_la_misma_emision_no_se_duplica(): void
    {
        $user = User::factory()->create();

        $this->service()->notifyUsers([$user], $this->notification(1), ['activity_id' => 1]);
        // Reintento del mismo job: misma huella → la BD lo rechaza.
        $second = $this->service()->notifyUsers([$user], $this->notification(1), ['activity_id' => 1]);

        $this->assertSame(0, $second);
        $this->assertSame(1, $user->notifications()->count());
    }

    public function test_otra_emision_del_mismo_tema_si_pasa(): void
    {
        $user = User::factory()->create();

        // Misma pevaluación (mismo tema, que el anti-spam de 24h agruparía)
        // pero OTRA actividad: es un evento nuevo y debe avisar.
        $this->service()->notifyUsers([$user], $this->notification(1, 10), ['activity_id' => 1]);
        $second = $this->service()->notifyUsers([$user], $this->notification(2, 10), ['activity_id' => 2]);

        $this->assertSame(1, $second);
        $this->assertSame(2, $user->notifications()->count());
    }

    public function test_la_idempotencia_es_por_destinatario(): void
    {
        $uno = User::factory()->create();
        $otro = User::factory()->create();

        $this->service()->notifyUsers([$uno], $this->notification(), ['activity_id' => 1]);
        // El mismo evento para otro usuario: le toca a él también.
        $sent = $this->service()->notifyUsers([$otro], $this->notification(), ['activity_id' => 1]);

        $this->assertSame(1, $sent);
        $this->assertSame(1, $otro->notifications()->count());
    }

    public function test_sin_huella_no_hay_reclamacion_ni_bloqueo(): void
    {
        $user = User::factory()->create();

        $a = $this->service()->notifyUsers([$user], $this->notification());
        $b = $this->service()->notifyUsers([$user], $this->notification());

        $this->assertSame([1, 1], [$a, $b]);
        $this->assertSame(0, $this->claimsFor($user), 'sin huella no debe crearse reclamación');
    }

    public function test_una_ventana_de_cero_desactiva_la_idempotencia(): void
    {
        $user = User::factory()->create();

        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1], 0);
        $second = $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1], 0);

        $this->assertSame(1, $second);
        $this->assertSame(0, $this->claimsFor($user));
    }

    public function test_un_lote_con_duplicados_solo_avisa_a_los_que_corresponde(): void
    {
        $conAviso = User::factory()->create();
        $sinAviso = User::factory()->create();

        $this->service()->notifyUsers([$conAviso], $this->notification(), ['activity_id' => 1]);

        $sent = $this->service()->notifyUsers(
            [$conAviso, $sinAviso],
            $this->notification(),
            ['activity_id' => 1]
        );

        $this->assertSame(1, $sent, 'solo el que no tenía la reclamación recibe el aviso');
        $this->assertSame(1, $conAviso->notifications()->count());
        $this->assertSame(1, $sinAviso->notifications()->count());
    }

    public function test_la_reclamacion_se_libera_si_el_envio_falla(): void
    {
        $user = User::factory()->create();

        // Rompe toDatabase() para forzar el fallo de persistencia.
        $broken = new class extends ActivityCreatedNotification
        {
            public function __construct() {}

            public function toDatabase($notifiable): array
            {
                throw new \RuntimeException('payload roto');
            }
        };

        try {
            $this->service()->notifyUsers([$user], $broken, ['activity_id' => 1]);
            $this->fail('debería propagar el error de toDatabase()');
        } catch (\RuntimeException $e) {
            $this->assertSame('payload roto', $e->getMessage());
        }

        $this->assertSame(0, $user->notifications()->count());
        $this->assertSame(0, $this->claimsFor($user), 'una reclamación sin envío no debe bloquear el reintento');
    }

    public function test_el_comando_de_poda_informa_y_no_toca_las_vivas(): void
    {
        $user = User::factory()->create();
        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);

        // Claim vieja: se inserta a mano porque no queremos esperar 30 días.
        $stale = 'sha1-claim-vieja-'.uniqid();
        DB::table(NotificationService::DEDUPE_TABLE)->insert([
            'dedupe_key' => $stale,
            'notifiable_type' => $user->getMorphClass(),
            'notifiable_id' => $user->id,
            'notification_class' => ActivityCreatedNotification::class,
            'payload_type' => 'activity_created',
            'bucket' => 1,
            'created_at' => now()->subDays(90),
            'updated_at' => now()->subDays(90),
        ]);

        $this->artisan('notifications:prune-dedupe', ['--days' => 30, '--dry-run' => true])
            ->expectsOutputToContain('Se eliminarían')
            ->assertExitCode(0);

        $this->assertTrue(
            DB::table(NotificationService::DEDUPE_TABLE)->where('dedupe_key', $stale)->exists(),
            '--dry-run no debe borrar nada'
        );

        $this->artisan('notifications:prune-dedupe', ['--days' => 30])->assertExitCode(0);

        $this->assertFalse(
            DB::table(NotificationService::DEDUPE_TABLE)->where('dedupe_key', $stale)->exists(),
            'la claim caducada debe eliminarse'
        );
        $this->assertSame(1, $this->claimsFor($user), 'la claim vigente debe sobrevivir a la poda');
    }
}
