<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifica al profesor (profesor.user) cuando una de sus actividades es
 * aprobada (activity.status: 0/null → 1) por un usuario con rol is_planner,
 * is_coordinacion o is_leadership.
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb).
 */
class ActivityApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $message,
        public string $url,
        public int $activityId,
        public ?string $topic = null,
        public ?string $asignaturaName = null,
        public ?string $approverName = null,
        public ?string $approverRole = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'url' => $this->url,
            'activity_id' => $this->activityId,
            'topic' => $this->topic,
            'asignatura' => $this->asignaturaName,
            'approver_name' => $this->approverName,
            'approver_role' => $this->approverRole,
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
