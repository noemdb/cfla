<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Recordatorio diario para el Jefe de Área cuando han pasado más de cinco días
 * desde la última actividad registrada/actualizada (updated_at) en sus áreas
 * (planning:daily-reminders, 06:00).
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb).
 */
class StaleActivitiesReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $message,
        public string $url,
        public int $daysSince,
        public ?string $lastActivityAt = null,
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
            'type' => 'stale_activities_reminder',
            'message' => $this->message,
            'url' => $this->url,
            'days_since' => $this->daysSince,
            'last_activity_at' => $this->lastActivityAt,
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
