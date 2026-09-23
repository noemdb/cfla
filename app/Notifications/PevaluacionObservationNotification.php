<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifica a Planificación (is_planner) cuando Coordinación registra o
 * actualiza una observación en Pevaluacion (coordinacion/activities).
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb).
 */
class PevaluacionObservationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $message,
        public string $url,
        public int $pevaluacionId,
        public ?string $pestudioName = null,
        public ?string $asignaturaName = null,
        public ?string $seccionName = null,
        public ?string $profesorName = null,
        public ?string $lapsoName = null,
        public ?string $observationPreview = null,
        public ?string $action = null, // 'registró' | 'actualizó'
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
            'pevaluacion_id' => $this->pevaluacionId,
            'pestudio' => $this->pestudioName,
            'asignatura' => $this->asignaturaName,
            'seccion' => $this->seccionName,
            'profesor' => $this->profesorName,
            'lapso' => $this->lapsoName,
            'observation' => $this->observationPreview,
            'action' => $this->action,
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
