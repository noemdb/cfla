<?php

namespace App\Services;

use App\Events\NotificationReceived;
use App\Models\User;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Punto central para emitir notificaciones de base de datos (blueprint/
 * notifications, hallazgo N2): persiste la fila en `notifications` y, además,
 * emite el broadcast `NotificationReceived` por destinatario para que el
 * dropdown del navbar se actualice en tiempo real.
 *
 * Regla de oro: toda notificación DB del sistema se emite por este servicio
 * para heredar automáticamente el broadcast y la invalidación de la caché de
 * no-leídas (N6).
 */
class NotificationService
{
    /**
     * Prefijo de caché del conteo de notificaciones no leídas por usuario
     * (badge de la campana). Se invalida al notificar y al marcar leídas.
     */
    public const UNREAD_PREFIX = 'user_unread_notifications_';

    /**
     * TTL de caché (segundos) alineado con la cadencia de `wire:poll`
     * (config('broadcasting.poll_interval'), default 5000ms).
     */
    public static function cacheTtlSeconds(): int
    {
        return max(1, (int) ceil((int) config('broadcasting.poll_interval', 5000) / 1000));
    }

    /**
     * Persiste la notificación DB para cada destinatario y emite el broadcast
     * optimista por usuario (crash-guard: si Reverb está caído, no rompe el
     * request; el poll del dropdown cubre la actualización).
     *
     * @param  iterable|User[]  $recipients
     */
    public function notifyUsers(iterable $recipients, BaseNotification $notification): void
    {
        $recipients = collect($recipients);
        $sentAt = now();

        // El conteo de no-leídas del badge cambió para cada destinatario:
        // invalidar la caché en el mismo request (hallazgo N6).
        foreach ($recipients as $recipient) {
            Cache::forget(self::UNREAD_PREFIX.$recipient->id);
        }

        // Notificación en base de datos (siempre persistida, síncrona).
        Notification::send($recipients->all(), $notification);

        // Broadcast optimista por destinatario (hallazgo N5): el payload lleva
        // el id real de la fila y los datos de presentación, de modo que el
        // cliente puede insertar el item sin esperar el commit de la BD.
        foreach ($recipients as $recipient) {
            $id = $recipient->notifications()
                ->orderByDesc('created_at')
                ->first()
                ?->id;

            if (! $id) {
                continue;
            }

            try {
                $payload = $notification->toDatabase($recipient)
                    + ['created_at' => $sentAt->toIso8601String()];

                // Dispatch POSICIONAL: Dispatchable::dispatch() es variádico y
                // PHP rechaza argumentos nombrados (Unknown named parameter).
                NotificationReceived::dispatch($id, $payload, $recipient->id);
            } catch (\Throwable $e) {
                Log::warning('NotificationReceived falló (Reverb caído), cubre poll', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Conteo de notificaciones no leídas del usuario, cacheado por usuario
     * (TTL = poll interval). Con N campanas en la página o el poll activo,
     * solo la primera consulta toca la BD.
     */
    public function unreadCountFor(int $userId): int
    {
        return (int) Cache::remember(self::UNREAD_PREFIX.$userId, self::cacheTtlSeconds(), function () use ($userId) {
            return User::query()->find($userId)?->unreadNotifications()->count() ?? 0;
        });
    }

    public function invalidateUnreadCount(int $userId): void
    {
        Cache::forget(self::UNREAD_PREFIX.$userId);
    }
}
