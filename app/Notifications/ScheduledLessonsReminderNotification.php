<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Recordatorio diario para el Jefe de Área cuando tiene 5 o más lecciones
 * (actividades con publicación LMS SCHEDULED) programadas para dentro de tres
 * días (planning:daily-reminders, 06:00).
 *
 * Un resumen por jefe y día. Solo canal database: la campana del navbar la
 * recibe por NotificationService (DB + broadcast Reverb).
 */
class ScheduledLessonsReminderNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<int, int>  $activityIds
     */
    public function __construct(
        public string $message,
        public string $url,
        public int $count,
        public string $date,
        public array $activityIds = [],
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
            'type' => 'scheduled_lessons_reminder',
            'message' => $this->message,
            'url' => $this->url,
            'count' => $this->count,
            'date' => $this->date,
            'activity_ids' => $this->activityIds,
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
