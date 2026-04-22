<?php

namespace App\Events\Competition;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerSync implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $competition_id,
        public int $time_remaining,
        public bool $active,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("competition.{$this->competition_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'timer.sync';
    }

    public function broadcastWith(): array
    {
        return [
            'time_remaining' => $this->time_remaining,
            'timer_active'   => $this->active,
        ];
    }
}
