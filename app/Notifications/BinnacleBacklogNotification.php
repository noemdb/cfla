<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Alerta de backlog de la cola binnacle (mejora propuesta #1).
 * Se envía cuando el comando binnacle:watch detecta jobs acumulados
 * por encima del umbral configurado (worker caído o congestionado).
 */
class BinnacleBacklogNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $pending,
        public int $threshold,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[Bitácora] Backlog en cola binnacle: {$this->pending} jobs")
            ->error()
            ->line('El worker de la cola de bitácora tiene un backlog acumulado.')
            ->line("Jobs pendientes: **{$this->pending}** (umbral: {$this->threshold}).")
            ->line('Si los eventos info/warning no se persisten, revisa el estado del worker `cfla-binnacle-queue` o el cron `schedule:run`.')
            ->action('Ver panel de bitácora', url('/admin/binnacle'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => 'queue_backlog',
            'pending' => $this->pending,
            'threshold' => $this->threshold,
        ];
    }
}
