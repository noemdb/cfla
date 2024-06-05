<?php

namespace App\Providers;

use App\Models\app\Educational\DebateCompetition;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderShipped implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $competition;

    /**
     * Create a new event instance.
     */
    public function __construct(DebateCompetition $competition)
    {
        $this->competition = $competition;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('orders'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->competition->id,
            'status' => $this->competition->status_active,
        ];
    }
}
