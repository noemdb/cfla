<?php

namespace App\Events\Competition;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando el moderador activa un nuevo debate.
 * Notifica a todos los clientes del scoreboard para que actualicen
 * los datos del debate activo (nombre, grado, secciones).
 */
class DebateActivated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $competition_id,
        public ?int $debate_id,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("competition.{$this->competition_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'debate.activated';
    }

    public function broadcastWith(): array
    {
        return [
            'competition_id' => $this->competition_id,
            'debate_id'      => $this->debate_id,
        ];
    }
}
