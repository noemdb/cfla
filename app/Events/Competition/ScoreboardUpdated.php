<?php

namespace App\Events\Competition;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando se registra, anula o restaura una respuesta.
 * Notifica a todos los clientes del scoreboard para que actualicen
 * los resultados preliminares (scores por sección).
 */
class ScoreboardUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $competition_id,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("competition.{$this->competition_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'scoreboard.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'competition_id' => $this->competition_id,
        ];
    }
}
