<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Recordatorio diario de actividades de planificación sin aprobar cuya fecha
 * inicial (finicial) cae en tres días (planning:daily-reminders, 06:00).
 *
 * Un resumen por usuario y rol: Jefe de Área (is_leadership, scoped a sus
 * áreas), Coordinación (is_coordinacion, scoped a sus peducativos) y
 * Planificación (is_planner, global). Solo canal database: la campana del
 * navbar la recibe por NotificationService (DB + broadcast Reverb).
 */
class PendingApprovalReminderNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<int, int>  $activityIds
     */
    public function __construct(
        public string $type,
        public string $message,
        public string $url,
        public int $count,
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
            'type' => $this->type,
            'message' => $this->message,
            'url' => $this->url,
            'count' => $this->count,
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
