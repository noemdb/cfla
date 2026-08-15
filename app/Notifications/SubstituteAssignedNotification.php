<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * SPEC-TIMETABLE-001 §10 (v1.2) — Suplencia asignada con confirmar/rechazar.
 * Canal database; el suplente la ve en su bandeja de notificaciones y el
 * enlace lleva a la vista de suplencias del profesor.
 */
class SubstituteAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $assignmentId,
        public string $calendarName,
        public string $date,
        public string $periodLabel,
        public string $subjectLabel,
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
            'event_type' => 'substitute_assigned',
            'assignment_id' => $this->assignmentId,
            'calendar_name' => $this->calendarName,
            'message' => "Suplencia asignada el {$this->date} ({$this->periodLabel}) para «{$this->subjectLabel}». Confirmá o rechazá en tu bandeja.",
            'action_url' => '/app/profesors/timetable/substitutes',
        ];
    }
}
