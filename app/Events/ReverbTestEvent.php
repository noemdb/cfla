<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento de diagnóstico para verificar Reverb end-to-end.
 * Emitido por el comando `reverb:test` sobre un canal público `reverb.test`.
 */
class ReverbTestEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $message = 'Reverb OK',
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('reverb.test'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reverb.test';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
