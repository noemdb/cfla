<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notificación de diagnóstico para probar la campana del navbar.
 * Persiste en la tabla `notifications` (vía NotificationService) y emite el
 * broadcast `NotificationReceived` para que el dropdown se actualice en vivo.
 */
class ReverbTestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $message = 'Reverb: prueba de notificación en el navbar',
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
            'type' => 'reverb_test',
            'message' => $this->message,
            'url' => route('app.notifications.index'),
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
