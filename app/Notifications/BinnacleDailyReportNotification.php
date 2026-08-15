<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Resumen diario de la bitácora (mejora propuesta #5).
 * El comando binnacle:report envía a admin/dirección un resumen del día
 * anterior: eventos critical/alert, accesos y top actores. El envío queda
 * auditado en la propia bitácora (event_type=binnacle_report_sent).
 */
class BinnacleDailyReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $data  Resumen generado por BinnacleReport
     */
    public function __construct(
        public array $data,
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
        $date = $this->data['date'] ?? now()->subDay()->toDateString();

        $message = (new MailMessage)
            ->subject("[Bitácora] Resumen diario {$date}")
            ->greeting('Resumen de auditoría diaria')
            ->line("Resumen de la bitácora del día **{$date}**.")
            ->line("Eventos críticos (critical/alert): **{$this->data['critical_count']}**")
            ->line("Accesos registrados: **{$this->data['access_count']}**")
            ->line("Entradas totales: **{$this->data['total_entries']}**");

        if (! empty($this->data['top_actors'])) {
            $message->line('Actores con más actividad:');
            foreach ($this->data['top_actors'] as $actor) {
                $message->line("- {$actor['identifier']}: **{$actor['count']}** eventos");
            }
        }

        if (! empty($this->data['recent_critical'])) {
            $message->line('Últimos eventos críticos:');
            foreach ($this->data['recent_critical'] as $entry) {
                $message->line("- [{$entry['severity']}] {$entry['title']} ({$entry['time']})");
            }
        }

        return $message
            ->action('Ver panel de bitácora', url('/admin/binnacle'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => 'binnacle_daily_report',
            'date' => $this->data['date'] ?? null,
            'critical_count' => $this->data['critical_count'] ?? 0,
            'access_count' => $this->data['access_count'] ?? 0,
            'total_entries' => $this->data['total_entries'] ?? 0,
        ];
    }
}
