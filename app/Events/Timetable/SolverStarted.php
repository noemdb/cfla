<?php

namespace App\Events\Timetable;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001-MEJORADO §3 (TT-CFP-13) — El solver
 * comienza. Permite a la UI mostrar progreso en tiempo real (Reverb).
 */
class SolverStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $calendarId,
        public bool $dryRun,
        public string $strategy,
    ) {}

    public function broadcastOn()
    {
        return new Channel('timetable.'.$this->calendarId);
    }

    public function broadcastWith(): array
    {
        return [
            'event' => 'solver_started',
            'calendar_id' => $this->calendarId,
            'dry_run' => $this->dryRun,
            'strategy' => $this->strategy,
        ];
    }
}
