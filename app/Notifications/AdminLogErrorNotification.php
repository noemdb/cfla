<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Alerta de error del log de Laravel para el superadmin.
 *
 * Es el ÚNICO tipo de notificación que recibe el superadmin (`userId=1`):
 * `NotificationService` filtra todo lo demás aunque tenga otros roles. La
 * emite el comando `admin:notify-log-errors`, que vigila `storage/logs` y
 * solo avisa de entradas de nivel ERROR.
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb). La URL apunta al visor (`admin/logs`), donde el
 * admin puede ver el contexto completo de la línea.
 */
class AdminLogErrorNotification extends Notification
{
    use Queueable;

    /**
     * Tipo del payload. `NotificationService` solo deja pasar este tipo al
     * superadmin (`config/notifications.php#superadmin_types`).
     */
    public const TYPE = 'laravel_log_error';

    public function __construct(
        public string $type,
        public string $message,
        public string $url,
        public string $logFile,
        public string $logDate,
        public ?string $logEnv = null,
        public ?string $logContext = null,
        public ?string $logHash = null,
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
            'log_file' => $this->logFile,
            'log_date' => $this->logDate,
            'log_env' => $this->logEnv,
            'log_context' => $this->logContext,
            'log_hash' => $this->logHash,
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
