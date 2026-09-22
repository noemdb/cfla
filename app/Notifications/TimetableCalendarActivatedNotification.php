<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Aviso a los usuarios de Planificación (is_planner) cuando se activa un
 * calendario de horario desde el módulo Planning (/app/planning/timetable).
 *
 * Solo canal database: la campana del navbar la recibe por NotificationService
 * (DB + broadcast Reverb). El enlace abre el wizard con el calendario activado
 * seleccionado.
 */
class TimetableCalendarActivatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $calendarId,
        public string $calendarName,
        public ?string $lapsoName = null,
        public ?string $pestudioName = null,
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
        $context = collect([$this->lapsoName, $this->pestudioName])
            ->filter()
            ->implode(' · ');

        $message = "El horario «{$this->calendarName}» fue activado"
            .($context !== '' ? " ({$context})" : '')
            .'.';

        return [
            'type' => 'timetable_calendar_activated',
            'message' => $message,
            'url' => route('app.planning.timetable', ['calendarId' => $this->calendarId]),
            'calendar_id' => $this->calendarId,
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
