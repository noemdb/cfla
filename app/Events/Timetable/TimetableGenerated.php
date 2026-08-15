<?php

namespace App\Events\Timetable;

use App\Services\Timetable\Solver\SolverResult;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * SPEC-TIMETABLE-001 §6 — Emitido por GenerateTimetableJob al terminar para
 * que el wizard refresque la UI (Reverb / polling).
 */
class TimetableGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $calendarId,
        public bool $dryRun,
        public SolverResult $result,
    ) {}

    public function broadcastOn()
    {
        return new Channel('timetable.'.$this->calendarId);
    }

    public function broadcastWith()
    {
        return [
            'calendar_id' => $this->calendarId,
            'dry_run' => $this->dryRun,
            'timed_out' => $this->result->timedOut,
            'unassigned' => $this->result->unassigned,
            'elapsed_seconds' => round($this->result->elapsedSeconds, 2),
        ];
    }
}
