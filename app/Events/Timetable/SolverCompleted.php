<?php

namespace App\Events\Timetable;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001-MEJORADO §3 (TT-CFP-13) — El solver
 * terminó. Incluye el resumen de intentos y la solución elegida.
 */
class SolverCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  list<array<string,mixed>>  $attempts
     */
    public function __construct(
        public int $calendarId,
        public bool $dryRun,
        public string $chosen,
        public int $coverageBlocks,
        public int $unassigned,
        public bool $timedOut,
        public int $elapsedMs,
        public array $attempts = [],
    ) {}

    public function broadcastOn()
    {
        return new Channel('timetable.'.$this->calendarId);
    }

    public function broadcastWith(): array
    {
        return [
            'event' => 'solver_completed',
            'calendar_id' => $this->calendarId,
            'dry_run' => $this->dryRun,
            'chosen' => $this->chosen,
            'coverage_blocks' => $this->coverageBlocks,
            'unassigned' => $this->unassigned,
            'timed_out' => $this->timedOut,
            'elapsed_ms' => $this->elapsedMs,
            'attempts' => $this->attempts,
        ];
    }
}
