<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifica a Planificación (is_planner) cuando se crea, actualiza o elimina
 * un Programa Educativo (Peducativo).
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb).
 */
class PeducativoNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type, // peducativo_created | peducativo_updated | peducativo_deleted
        public string $message,
        public string $url,
        public int $peducativoId,
        public ?string $peducativoName = null,
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
            'peducativo_id' => $this->peducativoId,
            'peducativo_name' => $this->peducativoName,
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
