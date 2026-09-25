<?php

namespace App\Console\Commands;

use App\Models\BinnacleEntry;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Métricas de notificaciones (ítem 9).
 *
 * Lee de la bitácola los eventos que emite `NotificationService`:
 *   - notification.dispatched        → enviadas / omitidas por idempotencia / fallidas
 *   - notification.broadcast_failed  → Reverb caído (severidad alert)
 *   - notification.read              → avisos marcados como leídos
 *
 * Completa con el estado real de la tabla `notifications` (acumulado, no por
 * ventana). Pensado para diagnosticar "no me llegan avisos": si `sent` es 0
 * el problema está en el emisor; si `sent` sube y `duplicates` también, es el
 * anti-spam/idempotencia; si hay `broadcast_failed`, es la entrega en tiempo
 * real (el poll lo cubre).
 */
class NotificationStats extends Command
{
    protected $signature = 'notifications:stats
        {--days=7 : Ventana de análisis en días}
        {--type= : Filtrar por tipo del payload (p. ej. activity_created)}
        {--json : Salida en JSON}';

    protected $description = 'Métricas de notificaciones (enviadas, omitidas, fallidas, leídas)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $type = $this->option('type');
        $since = now()->subDays($days);

        $entries = BinnacleEntry::query()
            ->where('event_category', 'notification')
            ->where('created_at', '>=', $since);

        if ($type) {
            $entries->where('metadata->payload_type', $type);
        }

        $dispatched = (clone $entries)->where('event_type', NotificationService::AUDIT_DISPATCHED)->get();
        $broadcastFailed = (clone $entries)->where('event_type', NotificationService::AUDIT_BROADCAST_FAILED)->count();
        $readEvents = (clone $entries)->where('event_type', NotificationService::AUDIT_READ)->get();

        $sent = (int) $dispatched->sum(fn ($entry) => (int) ($entry->metadata['sent'] ?? 0));
        $duplicates = (int) $dispatched->sum(fn ($entry) => (int) ($entry->metadata['duplicates'] ?? 0));
        $failed = (int) $dispatched->sum(fn ($entry) => (int) ($entry->metadata['failed'] ?? 0));
        $read = (int) $readEvents->sum(fn ($entry) => (int) ($entry->metadata['count'] ?? 0));

        $byClass = $dispatched
            ->groupBy(fn ($entry) => class_basename((string) ($entry->metadata['notification_class'] ?? '?')))
            ->map(fn ($group, $class) => [
                'notification' => $class,
                'calls' => $group->count(),
                'sent' => (int) $group->sum(fn ($entry) => (int) ($entry->metadata['sent'] ?? 0)),
                'duplicates' => (int) $group->sum(fn ($entry) => (int) ($entry->metadata['duplicates'] ?? 0)),
                'failed' => (int) $group->sum(fn ($entry) => (int) ($entry->metadata['failed'] ?? 0)),
            ])
            ->sortByDesc('sent')
            ->values()
            ->all();

        $notifications = DB::table('notifications');
        if ($type) {
            $notifications->where('data->type', $type);
        }

        $totalRows = (clone $notifications)->count();
        $unreadRows = (clone $notifications)->whereNull('read_at')->count();

        $payload = [
            'window_days' => $days,
            'since' => $since->toDateTimeString(),
            'emitted' => ['sent' => $sent, 'duplicates' => $duplicates, 'failed' => $failed],
            'read' => $read,
            'broadcast_failed' => $broadcastFailed,
            'stored' => [
                'total' => $totalRows,
                'unread' => $unreadRows,
                'read' => $totalRows - $unreadRows,
                'read_rate' => $totalRows > 0 ? round(($totalRows - $unreadRows) / $totalRows * 100, 1) : null,
            ],
            'by_notification' => $byClass,
        ];

        if ($this->option('json')) {
            $this->line((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("Notificaciones — últimos {$days} días (desde {$payload['since']})");

        $this->table(['Métrica', 'Valor'], [
            ['Enviadas', $sent],
            ['Omitidas por idempotencia/anti-spam', $duplicates],
            ['Fallidas al emitir', $failed],
            ['Marcadas como leídas', $read],
            ['Fallos de broadcast (Reverb)', $broadcastFailed],
        ]);

        $this->newLine();
        $this->info('Acumulado en la tabla notifications');
        $this->table(['Total', 'Sin leer', 'Leídas', '% leídas'], [[
            $totalRows,
            $unreadRows,
            $totalRows - $unreadRows,
            $payload['stored']['read_rate'] !== null ? $payload['stored']['read_rate'].'%' : '—',
        ]]);

        if ($byClass !== []) {
            $this->newLine();
            $this->info('Por tipo de notificación');
            $this->table(['Notificación', 'Llamadas', 'Enviadas', 'Omitidas', 'Fallidas'], $byClass);
        }

        if ($type) {
            $this->newLine();
            $this->line("  (filtrado por data->type = {$type})");
        }

        $this->newLine();
        $this->line('  Diagnóstico: enviados=0 → el emisor no dispara · duplicados altos → anti-spam/idempotencia · broadcast_failed>0 → Reverb caído (lo cubre el poll)');

        return self::SUCCESS;
    }
}
