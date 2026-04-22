<?php

namespace App\Events\Competition;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando el moderador activa o cambia la pregunta activa.
 * Notifica a todos los clientes del scoreboard para que actualicen
 * la pregunta, las opciones y reinicien el cronómetro.
 */
class QuestionActivated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $competition_id,
        public ?int $question_id,
        public ?int $time_remaining,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("competition.{$this->competition_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'question.activated';
    }

    public function broadcastWith(): array
    {
        return [
            'competition_id' => $this->competition_id,
            'question_id'    => $this->question_id,
            'time_remaining' => $this->time_remaining,
        ];
    }
}
