<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifica al Jefe de Área (leader_id del AreaConocimiento asociado vía
 * campo_conocimientos.pensum_id) cuando se registra una nueva actividad de
 * planificación en /app/profesors/activities/create/{pevaluacionId}.
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb).
 */
class ActivityCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $message,
        public string $url,
        public int $activityId,
        public ?string $asignaturaName = null,
        public ?string $gradoName = null,
        public ?string $seccionName = null,
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
            'asignatura' => $this->asignaturaName,
            'grado' => $this->gradoName,
            'seccion' => $this->seccionName,
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
