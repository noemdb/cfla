<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Lms\BroadcastEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Auditoría y métricas de eventos broadcast (Opción 10).
 *
 * Registra una fila en `broadcast_events` + una línea JSON en
 * `storage/logs/broadcast.log` por cada evento emitido desde el punto central
 * de emisión (LmsPublicationService). El flag `delivered` se marca por ACK
 * del cliente (POST /api/broadcast/ack) cuando el navegador recibe el evento.
 */
class BroadcastAudit
{
    public const LOG_CHANNEL = 'broadcast';

    public function log(
        string $event,
        ?Model $subject,
        ?int $actorUserId,
        array $recipientIds,
        string $driver = 'reverb'
    ): BroadcastEvent {
        $recipientIds = array_values(array_unique($recipientIds));

        $record = BroadcastEvent::create([
            'event' => $event,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'actor_user_id' => $actorUserId,
            'recipient_ids' => $recipientIds,
            'channel_count' => count($recipientIds),
            'driver' => $driver,
            'delivered' => false,
        ]);

        Log::channel(self::LOG_CHANNEL)->info('broadcast.dispatched', [
            'event_id' => $record->id,
            'event' => $event,
            'subject_type' => $record->subject_type,
            'subject_id' => $record->subject_id,
            'actor_user_id' => $actorUserId,
            'recipient_ids' => $recipientIds,
            'channel_count' => $record->channel_count,
            'driver' => $driver,
            'dispatched_at' => now()->toIso8601String(),
        ]);

        return $record;
    }

    public function ack(int $eventId): bool
    {
        $record = BroadcastEvent::find($eventId);

        if (! $record) {
            return false;
        }

        if ($record->markDelivered()) {
            Log::channel(self::LOG_CHANNEL)->info('broadcast.delivered', [
                'event_id' => $eventId,
                'event' => $record->event,
                'acked_at' => now()->toIso8601String(),
            ]);
        }

        return true;
    }
}
