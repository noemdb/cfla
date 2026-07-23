<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LessonScheduledForApproval extends Notification
{
    use Queueable;

    public function __construct(
        private int $activityId,
        private string $teacherName,
        private string $activityTitle,
        private string $scheduledAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'activity_id'    => $this->activityId,
            'type'           => 'lesson_scheduled',
            'teacher_name'   => $this->teacherName,
            'activity_title' => $this->activityTitle,
            'scheduled_at'   => $this->scheduledAt,
            'message'        => "{$this->teacherName} ha programado la lección «{$this->activityTitle}» para aprobación de Planificación.",
            'url'            => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
        ];
    }
}
