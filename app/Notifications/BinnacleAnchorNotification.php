<?php

namespace App\Notifications;

use App\Models\BinnacleEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Confirmación del ancla externa del hash-chain (Spec §8.3 / mejora #6).
 * El comando binnacle:anchor la envía a admin/dirección cuando --notify:
 * documenta el hash publicado (el "sello" del ancla) fuera del archivo local.
 */
class BinnacleAnchorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public BinnacleEntry $entry,
        public string $path,
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
            ->subject('[Bitácora] Ancla externa del hash-chain publicada')
            ->greeting('Ancla de integridad publicada')
            ->line("Se publicó el ancla de la cadena de integridad (evento #{$this->entry->id}).")
            ->line("Evento: **{$this->entry->event_type}**")
            ->line('Entry hash:')
            ->line("```{$this->entry->entry_hash}```")
            ->line("Archivo ancla: `{$this->path}`")
            ->line('Guarda este hash como referencia fuera de la BD; permite detectar manipulación de la cadena.')
            ->action('Ver panel de bitácora', url('/admin/binnacle'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => 'binnacle_anchor_sent',
            'entry_id' => $this->entry->id,
            'entry_hash' => $this->entry->entry_hash,
            'path' => $this->path,
        ];
    }
}
