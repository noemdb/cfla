<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Avisos del ciclo de publicación de una lección en el LMS
 * (`lms_activity_publications`).
 *
 * - `lms_activity_published`   → la lección pasó a PUBLISHED.
 * - `lms_publication_deleted`  → se eliminó el registro de publicación.
 *
 * Solo canal database: la campana del navbar la recibe por
 * NotificationService (DB + broadcast Reverb). El destino del clic lo decide
 * `NotificationTargetResolver` según el rol (previsualización para
 * planificación/jefatura, editor para el profesor, listados para el resto).
 */
class LmsActivityPublicationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $message,
        public string $url,
        public int $publicationId,
        public int $activityId,
        public ?int $pevaluacionId = null,
        public ?string $asignaturaName = null,
        public ?string $gradoName = null,
        public ?string $seccionName = null,
        public ?string $actorName = null,
        public ?string $actorRole = null,
        public ?string $publishedAt = null,
        public ?string $action = null,
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
            'publication_id' => $this->publicationId,
            'activity_id' => $this->activityId,
            'pevaluacion_id' => $this->pevaluacionId,
            'asignatura' => $this->asignaturaName,
            'grado' => $this->gradoName,
            'seccion' => $this->seccionName,
            'actor' => $this->actorName,
            'actor_role' => $this->actorRole,
            'published_at' => $this->publishedAt,
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
