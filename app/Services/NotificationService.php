<?php

namespace App\Services;

use App\Events\NotificationReceived;
use App\Jobs\BroadcastNotificationReceived;
use App\Models\User;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
     * Ventana por defecto del anti-spam, en horas: si el destinatario ya tiene
     * sin leer un aviso del mismo tipo sobre el mismo asunto dentro de la
     * ventana, se omite el nuevo.
     */
    public const DEDUPE_HOURS = 24;

    /**
     * Ventana de la idempotencia a nivel de BD, en MINUTOS. Deliberadamente
     * corta y distinta del anti-spam: aquí lo que se evita es que un job
     * reintentado (o dos jobs concurrentes) entreguen DOS VECES la misma
     * emisión. No debe confundir "la misma emisión" con "otro evento del mismo
     * tema": por eso la huella es por evento (activity_id, diag_question_id…)
     * y no el asunto que agrupa los avisos al usuario.
     */
    public const IDEMPOTENCY_MINUTES = 60;

    /**
     * Tabla de reclamaciones para la idempotencia a nivel de BD.
     */
    public const DEDUPE_TABLE = 'notification_dedupe';

    /**
     * Evento de bitácora: resumen de una emisión (enviados, omitidos por
     * idempotencia, fallidos). Uno por llamada a `notifyUsers()`, no por
     * destinatario, para no inundar la bitácora en emitting lotes grandes.
     */
    public const AUDIT_DISPATCHED = 'notification.dispatched';

    /**
     * Evento de bitácora: fallo del broadcast (Reverb caído). Severidad
     * `alert` → se escribe de forma síncrona, porque un fallo de entrega que
     * depende de la cola no puede depender de la cola para registrarse.
     */
    public const AUDIT_BROADCAST_FAILED = 'notification.broadcast_failed';

    /**
     * Evento de bitácora: avisos marcados como leídos.
     */
    public const AUDIT_READ = 'notification.read';

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
     * Con `$fingerprint` (y `$idempotencyMinutes > 0`) se añade idempotencia a
     * nivel de BD: antes de persistir, el destinatario "reclama" la combinación
     * (tipo, huella del evento, ventana) en `notification_dedupe`, cuyo índice
     * único hace que un job reintentado o concurrente no pueda duplicar el
     * aviso. Pasarlo `null` mantiene el comportamiento abierto (solo anti-spam
     * por no leídas).
     *
     * Ojo: `$fingerprint` NO es el asunto del anti-spam. El anti-spam agrupa
     * por tema (misma pevaluación, misma asignatura) y evita el inundar al
     * usuario; la huella identifica LA EMISIÓN (activity_id, diag_question_id +
     * acción) y solo evita el doble envío de la misma.
     *
     * @param  iterable|User[]  $recipients
     * @param  array<string, scalar|null>|string|null  $fingerprint
     * @param  int  $idempotencyMinutes  ventana de la idempotencia
     * @return int destinatarios a los que se emitió el aviso
     */
    public function notifyUsers(iterable $recipients, BaseNotification $notification, $fingerprint = null, int $idempotencyMinutes = self::IDEMPOTENCY_MINUTES): int
    {
        $recipients = collect($recipients);
        $sentAt = now();
        $fingerprint = $this->normalizeFingerprint($fingerprint);
        $idempotent = $fingerprint !== null && $idempotencyMinutes > 0;

        $sent = 0;
        $duplicates = 0;
        $failed = 0;
        $claims = [];

        // `type` del payload (activity_created, peducativo_updated…): es la
        // clave por la que se agrupan y filtran las métricas.
        $payloadType = $recipients->isEmpty()
            ? ''
            : (string) (((array) $this->presentationData($notification, $recipients->first()))['type'] ?? '');

        foreach ($recipients as $recipient) {
            // El conteo de no-leídas del badge cambió para cada destinatario:
            // invalidar la caché en el mismo request (hallazgo N6).
            Cache::forget(self::UNREAD_PREFIX.$recipient->id);

            $claim = $idempotent
                ? $this->claimDedupe($recipient, $notification, $fingerprint, $idempotencyMinutes, $payloadType)
                : true;

            // Otro envío en la misma ventana ya avisó de esto mismo.
            if ($claim === false) {
                $duplicates++;

                continue;
            }

            if (is_string($claim)) {
                $claims[$recipient->id] = $claim;
            }

            // Copia por destinatario con UUID propio: el sender la persiste
            // con ese id exacto (mismo UUID en objeto y fila).
            $copy = clone $notification;
            $copy->id = (string) Str::uuid();

            try {
                // Notificación en base de datos (siempre persistida, síncrona).
                Notification::send([$recipient], $copy);
            } catch (\Throwable $e) {
                // No se emitió: se libera la reclamación para que un reintento
                // (o el propio job, si reintenta) pueda intentarlo de nuevo.
                if (is_string($claim)) {
                    $this->releaseDedupe($claim);
                    unset($claims[$recipient->id]);
                }
                $failed++;

                throw $e;
            }

            $sent++;

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

                $this->auditBroadcastFailure($copy, $recipient, $e);
            }
        }

        $this->auditDispatched($notification, $payloadType, $sent, $duplicates, $failed, $idempotent);

        return $sent;
    }

    /**
     * Normaliza el asunto a una cadena estable (mismo asunto → misma huella).
     *
     * @param  array<string, scalar|null>|string|null  $fingerprint
     */
    private function normalizeFingerprint($fingerprint): ?string
    {
        if ($fingerprint === null || $fingerprint === '' || $fingerprint === []) {
            return null;
        }

        if (is_string($fingerprint)) {
            return $fingerprint;
        }

        ksort($fingerprint);

        // `10` y `"10"` describen el mismo asunto: se normaliza el tipo.
        return (string) json_encode(array_map(
            fn ($value) => is_scalar($value) ? (string) $value : $value,
            $fingerprint
        ));
    }

    /**
     * Reclama (destinatario, tipo, huella, ventana) en la tabla de idempotencia.
     * Devuelve `true` si no hay idempotencia, la clave si se reclamó, o `false`
     * si ya estaba reclamada (hay que omitir el envío).
     */
    private function claimDedupe(User $recipient, BaseNotification $notification, string $fingerprint, int $windowMinutes, string $payloadType = '')
    {
        $bucket = (int) floor(now()->timestamp / max(1, $windowMinutes * 60));
        $key = sha1($recipient->id.'|'.$payloadType.'|'.$fingerprint.'|'.$bucket);

        $claimed = DB::table(self::DEDUPE_TABLE)->insertOrIgnore([
            'dedupe_key' => $key,
            'notifiable_type' => $recipient->getMorphClass(),
            'notifiable_id' => $recipient->id,
            'notification_class' => $notification::class,
            'payload_type' => $payloadType,
            'bucket' => $bucket,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $claimed === 1 ? $key : false;
    }

    private function releaseDedupe(string $key): void
    {
        try {
            DB::table(self::DEDUPE_TABLE)->where('dedupe_key', $key)->delete();
        } catch (\Throwable $e) {
            Log::warning('No se pudo liberar la reclamación de idempotencia', [
                'dedupe_key' => $key,
                'error' => $e->getMessage(),
            ]);
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

    /**
     * Marca como leídas las notificaciones indicadas y registra el métrico.
     * Devuelve cuántas estaban realmente sin leer.
     *
     * @param  array<int, string>  $ids
     */
    public function markAsReadFor(User $user, array $ids): int
    {
        $ids = array_values(array_filter($ids));

        if ($ids === []) {
            return 0;
        }

        $unread = $user->notifications()->whereIn('id', $ids)->whereNull('read_at')->pluck('id');

        if ($unread->isEmpty()) {
            return 0;
        }

        $user->notifications()->whereIn('id', $unread)->getQuery()->update(['read_at' => now()]);
        $this->invalidateUnreadCount($user->id);
        $this->auditRead($unread->all());

        return $unread->count();
    }

    /**
     * Resumen de una emisión, en la bitácora (categoría `notification`).
     */
    private function auditDispatched(BaseNotification $notification, string $payloadType, int $sent, int $duplicates, int $failed, bool $idempotent): void
    {
        if ($sent === 0 && $duplicates === 0 && $failed === 0) {
            return;
        }

        $this->audit(self::AUDIT_DISPATCHED, [
            'title' => 'Notificaciones emitidas: '.$notification::class,
            'description' => sprintf(
                '%d enviadas, %d omitidas por idempotencia, %d fallidas',
                $sent,
                $duplicates,
                $failed
            ),
            'metadata' => [
                'notification_class' => $notification::class,
                'payload_type' => $payloadType,
                'sent' => $sent,
                'duplicates' => $duplicates,
                'failed' => $failed,
                'idempotent' => $idempotent,
            ],
        ]);
    }

    private function auditBroadcastFailure(BaseNotification $notification, User $recipient, \Throwable $e): void
    {
        $this->audit(self::AUDIT_BROADCAST_FAILED, [
            'severity' => 'alert',
            'title' => 'Fallo al emitir el broadcast de notificación',
            'description' => $e->getMessage(),
            'metadata' => [
                'notification_class' => $notification::class,
                'user_id' => $recipient->id,
                'exception' => $e::class,
                'error' => $e->getMessage(),
            ],
        ]);
    }

    /**
     * @param  array<int, string>  $ids
     */
    private function auditRead(array $ids): void
    {
        $this->audit(self::AUDIT_READ, [
            'title' => count($ids).' notificación(es) marcada(s) como leída(s)',
            'metadata' => ['count' => count($ids)],
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function audit(string $eventType, array $context): void
    {
        try {
            Binnacle::log($eventType, $context + ['category' => 'notification']);
        } catch (\Throwable $e) {
            // Fail-open: la métrica nunca debe romper el envío.
            Log::warning('No se pudo registrar la métrica de notificación', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Anti-spam genérico: ¿hay que avisar a este usuario de un `type` sobre
     * este `subject`, o ya tiene un aviso equivalente sin leer?
     *
     * Se miran solo las **no leídas**: si el usuario ya abrió el aviso, un
     * nuevo registro del mismo asunto sí merece volver a avisarle.
     *
     * El "asunto" son claves del propio payload (`data->…`), las mismas que
     * escriben las notificaciones: `pevaluacion_id`, `peducativo_id`,
     * `diag_question_id`… Con `$subject` vacío el agrupamiento es solo por
     * tipo, que es lo adecuado para avisos que no tienen un sujeto concreto.
     *
     * @param  array<string, scalar|null>  $subject  claves `data->…` a comparar
     */
    public function shouldNotify(User $user, string $type, array $subject = [], int $hours = self::DEDUPE_HOURS): bool
    {
        $query = $user->unreadNotifications()->where('data->type', $type);

        if ($hours > 0) {
            $query->where('created_at', '>=', now()->subHours($hours));
        }

        foreach ($subject as $key => $value) {
            $value === null
                ? $query->whereNull('data->'.$key)
                : $query->where('data->'.$key, $value);
        }

        return ! $query->exists();
    }

    /**
     * Filtra un lote de destinatarios dejando solo those a los que hay que
     * avisar según el anti-spam. Devuelve la colección ya filtrada para
     * encadenar con `->isEmpty()`.
     *
     * @template T of User
     *
     * @param  iterable<T>  $recipients
     * @param  array<string, scalar|null>  $subject
     * @return \Illuminate\Support\Collection<int, T>
     */
    public function filterByDedupe(iterable $recipients, string $type, array $subject = [], int $hours = self::DEDUPE_HOURS)
    {
        return collect($recipients)
            ->filter(fn (User $user) => $this->shouldNotify($user, $type, $subject, $hours))
            ->values();
    }

    /**
     * Marca como leídas las notificaciones no leídas de un tipo para un
     * usuario (p. ej. `activity_created` al visitar su listado): el badge
     * refleja lo visto sin clics manuales. Invalida la caché de no-leídas.
     */
    public function markTypeAsRead(int $userId, string $type): void
    {
        $user = User::query()->find($userId);

        if (! $user) {
            return;
        }

        $user->unreadNotifications()
            ->where('data->type', $type)
            ->getQuery()
            ->update(['read_at' => now()]);

        $this->invalidateUnreadCount($userId);
    }
}
