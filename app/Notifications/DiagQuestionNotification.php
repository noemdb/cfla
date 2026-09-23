<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifica a Planificación (is_planner) y Jefe de Área (is_leadership acotado
 * al área de conocimiento del Pensum) cuando se crea, actualiza o elimina
 * una pregunta diagnóstica (DiagQuestion).
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb).
 */
class DiagQuestionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type, // diag_question_created | diag_question_updated | diag_question_deleted
        public string $message,
        public string $url,
        public int $diagQuestionId,
        public ?int $pensumId = null,
        public ?string $asignaturaName = null,
        public ?string $pensumLabel = null,
        public ?string $action = null, // creado | actualizado | eliminado
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
            'diag_question_id' => $this->diagQuestionId,
            'pensum_id' => $this->pensumId,
            'asignatura' => $this->asignaturaName,
            'pensum_label' => $this->pensumLabel,
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
