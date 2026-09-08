<?php

namespace App\Services;

use App\Events\NotificationReceived;
use App\Jobs\BroadcastNotificationReceived;
use App\Models\User;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

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
     * Cada destinatario recibe una copia con UUID propio fijado de antemano:
     * Laravel respeta el id ya asignado (NotificationSender::sendToNotifiable
     * solo setea $notification->id si está vacío), así conocemos el id de
     * cada fila persistida sin releer la tabla. Esto elimina la carrera del
     * patrón orderByDesc('created_at')->first(), que con dos notificaciones
     * casi simultáneas al mismo usuario podía emparejar el id de una con el
     * payload de la otra.
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

        foreach ($recipients as $recipient) {
            // Copia por destinatario con UUID propio: el sender la persiste
            // con ese id exacto (mismo UUID en objeto y fila).
            $copy = clone $notification;
            $copy->id = (string) Str::uuid();

            // Notificación en base de datos (siempre persistida, síncrona).
            // Fuera del crash-guard: un fallo aquí es un error real que debe
            // propagar (p. ej. reintentar el job que la encoló).
            Notification::send([$recipient], $copy);

            // Armado del payload fuera del try: un error aquí (p. ej.
            // toDatabase() roto) es un bug determinista; reintentar no lo
            // arregla, así que no tiene sentido re-emitirlo.
            $payload = $this->presentationData($copy, $recipient)
                + ['created_at' => $sentAt->toIso8601String()];

            try {
                // Broadcast optimista (hallazgo N5): el payload lleva el id
                // real de la fila y los datos de presentación, de modo que el
                // cliente puede insertar el item sin esperar el commit de la BD.
                //
                // Dispatch POSICIONAL: Dispatchable::dispatch() es variádico y
                // PHP rechaza argumentos nombrados (Unknown named parameter).
                NotificationReceived::dispatch($copy->id, $payload, $recipient->id);
            } catch (\Throwable $e) {
                // Fallo de entrega (Reverb caído): no romper el request — la
                // fila ya está en la BD y el poll la cubre —, encolar
                // re-emisión con backoff y registrar la causa.
                Log::warning('NotificationReceived falló, cubre poll + reemisión', [
                    'user_id' => $recipient->id,
                    'notification_id' => $copy->id,
                    'exception' => $e::class,
                    'error' => $e->getMessage(),
                ]);

                BroadcastNotificationReceived::dispatch($copy->id, $payload, $recipient->id);
            }
        }
    }

    /**
     * Datos de presentación para el broadcast: replica el fallback de
     * DatabaseChannel::getData() — toDatabase() si existe, si no toArray() —
     * para que toda notificación DB (defina uno u otro) pase por aquí.
     *
     * @return array<string, mixed>
     */
    private function presentationData(BaseNotification $notification, User $recipient): array
    {
        if (method_exists($notification, 'toDatabase')) {
            return (array) $notification->toDatabase($recipient);
        }

        return (array) $notification->toArray($recipient);
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
