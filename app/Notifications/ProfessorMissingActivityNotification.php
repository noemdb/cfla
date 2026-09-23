<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifica al profesor (is_profesor) cuando no tiene al menos una actividad
 * registrada en sus pevaluaciones asignadas (para el lapso vigente).
 *
 * Enviada todos los lunes vía schedule `professors:notify-missing-activities`.
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb).
 */
class ProfessorMissingActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $message,
        public string $url,
        public int $missingCount,
        public int $totalPevaluaciones,
        public ?string $lapsoName = null,
        public ?int $lapsoId = null,
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
            'type' => 'professor_missing_activity',
            'message' => $this->message,
            'url' => $this->url,
            'missing_count' => $this->missingCount,
            'total_pevaluaciones' => $this->totalPevaluaciones,
            'lapso_id' => $this->lapsoId,
            'lapso_name' => $this->lapsoName,
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
