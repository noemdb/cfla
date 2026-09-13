<?php

namespace App\Events\Timetable;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001-MEJORADO §3 (TT-CFP-13) — Un intento
 * (S1..S7) terminó. La UI puede actualizar cobertura/intentos sin refrescar.
 */
class SolverAttemptCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $calendarId,
        public string $attemptId,
        public int $assigned,
        public int $unassigned,
        public int $score,
        public bool $timedOut,
        public int $elapsedMs,
    ) {}

    public function broadcastOn()
    {
        return new Channel('timetable.'.$this->calendarId);
    }

    public function broadcastWith(): array
    {
        return [
            'event' => 'solver_attempt_completed',
            'calendar_id' => $this->calendarId,
            'attempt_id' => $this->attemptId,
            'assigned' => $this->assigned,
            'unassigned' => $this->unassigned,
            'score' => $this->score,
            'timed_out' => $this->timedOut,
            'elapsed_ms' => $this->elapsedMs,
        ];
    }
}
