<?php

namespace App\Events\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonScheduled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  User[]  $recipients  Usuarios responsables (planner/admin/leadership/coordinación).
     */
    public function __construct(
        public Activity $activity,
        public array $recipients,
        public string $teacherName,
        public string $scheduledFor,
    ) {}

    /**
     * Canal privado por destinatario (App.Models.User.{id}).
     */
    public function broadcastOn(): array
    {
        return collect($this->recipients)
            ->map(fn (User $user) => new PrivateChannel('App.Models.User.'.$user->id))
            ->values()
            ->all();
    }

    public function broadcastAs(): string
    {
        return 'lesson.scheduled';
    }

    public function broadcastWith(): array
    {
        $title = $this->activity->topic ?? 'Lección';

        return [
            'type'          => 'lesson_scheduled',
            'activity_id'   => $this->activity->id,
            'teacher_name'  => $this->teacherName,
            'lesson_title'  => $title,
            'scheduled_at'  => $this->scheduledFor,
            'message'       => "{$this->teacherName} ha programado la lección «{$title}» para aprobación de Planificación.",
            'url'           => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
        ];
    }
}
