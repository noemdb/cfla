<?php

namespace Tests\Feature\App\Notifications;

use App\Models\BinnacleEntry;
use App\Models\User;
use App\Notifications\ActivityCreatedNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Métricas de notificaciones (ítem 9).
 *
 * `NotificationService` escribe en la bitácora un resumen por emisión
 * (`notification.dispatched`), los fallos de entrega
 * (`notification.broadcast_failed`) y los marcados como leídos
 * (`notification.read`). `notifications:stats` las agrega.
 */
class NotificationMetricsTest extends TestCase
{
    use DatabaseTransactions;

    private function service(): NotificationService
    {
        return app(NotificationService::class);
    }

    private function notification(int $activityId = 1): ActivityCreatedNotification
    {
        return new ActivityCreatedNotification(
            type: 'activity_created',
            message: 'Actividad '.$activityId,
            url: route('app.notifications.index'),
            activityId: $activityId,
            pevaluacionId: 10,
        );
    }

    private function dispatchedEntries(): \Illuminate\Support\Collection
    {
        return BinnacleEntry::where('event_type', NotificationService::AUDIT_DISPATCHED)
            ->where('event_category', 'notification')
            ->get();
    }

    /**
     * La bitácora es append-only y hay entradas históricas de otros procesos,
     * así que se mide el DELTA de cada test en vez de un absoluto.
     */
    private function dispatchedCount(): int
    {
        return $this->dispatchedEntries()->count();
    }

    public function test_una_emision_registra_las_metricas_en_la_bitacora(): void
    {
        $user = User::factory()->create();
        $before = $this->dispatchedCount();

        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);

        $this->assertSame($before + 1, $this->dispatchedCount(), 'debe registrarse una entrada por emisión');

        $entry = $this->dispatchedEntries()->last();

        $this->assertNotNull($entry, 'debe registrarse notification.dispatched');
        $this->assertSame('notification', $entry->event_category);
        $this->assertSame(ActivityCreatedNotification::class, $entry->metadata['notification_class']);
        $this->assertSame('activity_created', $entry->metadata['payload_type']);
        $this->assertSame(1, $entry->metadata['sent']);
        $this->assertSame(0, $entry->metadata['duplicates']);
        $this->assertTrue($entry->metadata['idempotent']);
    }

    public function test_los_omitidos_por_idempotencia_se_cuentan_como_duplicados(): void
    {
        $user = User::factory()->create();

        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);
        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);

        $last = $this->dispatchedEntries()->last();

        $this->assertSame(0, $last->metadata['sent']);
        $this->assertSame(1, $last->metadata['duplicates']);
    }

    public function test_una_emision_sin_destinatarios_no_registra_nada(): void
    {
        $before = $this->dispatchedCount();

        $this->service()->notifyUsers([], $this->notification(), ['activity_id' => 1]);

        $this->assertSame($before, $this->dispatchedCount());
    }

    public function test_marcar_como_leidas_registra_el_metrico(): void
    {
        $user = User::factory()->create();
        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);
        $this->service()->notifyUsers([$user], $this->notification(2), ['activity_id' => 2]);

        $read = $this->service()->markAsReadFor($user, $user->notifications()->pluck('id')->all());

        $this->assertSame(2, $read);

        $entry = BinnacleEntry::where('event_type', NotificationService::AUDIT_READ)->latest('id')->first();
        $this->assertNotNull($entry);
        $this->assertSame(2, $entry->metadata['count']);
    }

    public function test_marcar_ya_leidas_no_cuenta_de_nuevo(): void
    {
        $user = User::factory()->create();
        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);
        $ids = $user->notifications()->pluck('id')->all();

        $this->assertSame(1, $this->service()->markAsReadFor($user, $ids));
        $this->assertSame(0, $this->service()->markAsReadFor($user, $ids), 'lo ya leído no se vuelve a contar');
        $this->assertSame(0, $this->service()->markAsReadFor($user, []));
    }

    public function test_el_comando_de_stats_agrega_y_es_consistente(): void
    {
        // La bitácora es append-only y accumulates desde siempre: se mide el
        // delta que aporta este test, no un absoluto.
        $before = $this->runStatsJson();

        $user = User::factory()->create();
        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);
        $this->service()->notifyUsers([$user], $this->notification(), ['activity_id' => 1]);
        $this->service()->notifyUsers([$user], $this->notification(2), ['activity_id' => 2]);
        $this->service()->markAsReadFor($user, $user->notifications()->pluck('id')->all());

        $json = $this->runStatsJson();

        // 2 enviados (el segundo y el tercero) y 1 duplicado (el reintento del
        // primero, frenado por la idempotencia).
        $this->assertSame(2, $json['emitted']['sent'] - $before['emitted']['sent']);
        $this->assertSame(1, $json['emitted']['duplicates'] - $before['emitted']['duplicates']);
        $this->assertSame(0, $json['emitted']['failed'] - $before['emitted']['failed']);
        $this->assertSame(2, $json['read'] - $before['read']);
        $this->assertSame(0, $json['broadcast_failed']);

        // El acumulado de la tabla notifications.
        $this->assertGreaterThanOrEqual($json['stored']['unread'], $json['stored']['total']);
        $this->assertSame(
            $json['stored']['total'] - $json['stored']['unread'],
            $json['stored']['read']
        );
        $this->assertNotNull($json['stored']['read_rate']);

        $this->assertNotEmpty($json['by_notification']);
        $this->assertSame('ActivityCreatedNotification', $json['by_notification'][0]['notification']);
    }

    /**
     * Ejecuta el comando y decodifica su salida JSON.
     */
    private function runStatsJson(): array
    {
        $code = Artisan::call('notifications:stats', ['--days' => 1, '--json' => true]);

        $this->assertSame(0, $code, 'el comando debe terminar con éxito');

        return json_decode(trim(Artisan::output()), true) ?? [];
    }
}
