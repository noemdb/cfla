<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast genérico emitido por NotificationService cuando se persiste una
 * notificación de base de datos (blueprint/notifications, hallazgo N2).
 *
 * El cliente (NotificationBell) usa la información del payload para la
 * inserción optimista: el evento llega antes del commit de la transacción que
 * persiste la fila, así que el dropdown no depende de releer la BD al instante
 * (hallazgo N5); se reconcilia contra la BD al abrir/refrescar.
 */
class NotificationReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $notificationId,
        public array $payload,
        public int $userId,
    ) {}

    /**
     * Canal privado del destinatario (App.Models.User.{id}).
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.Models.User.'.$this->userId);
    }

    public function broadcastAs(): string
    {
        return 'notification.received';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notificationId,
            'data' => $this->payload,
        ];
    }
}
