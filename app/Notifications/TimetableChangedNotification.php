<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * SPEC-TIMETABLE-001 §10 — Notificación de cambio de horario.
 * Canal database (tabla notifications), coherente con el sistema existente.
 */
class TimetableChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $calendarId,
        public string $calendarName,
        public string $message,
        public string $type = 'timetable_changed',
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
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'calendar_id' => $this->calendarId,
            'calendar_name' => $this->calendarName,
            'message' => $this->message,
            'url' => '/app/coordinacion/timetable',
            // Se mantiene la clave histórica para no romper consumidores
            // externos que aún la lean de filas ya persistidas.
            'action_url' => '/app/coordinacion/timetable',
        ];
    }
}
