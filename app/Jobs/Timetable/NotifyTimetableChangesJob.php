<?php

namespace App\Jobs\Timetable;

use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\User;
use App\Notifications\TimetableChangedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * SPEC-TIMETABLE-001 §10 (mejora) — Notificaciones de cambio de horario en
 * cola propia, desacopladas del job de persistencia.
 *
 * Recibe el diff real calculado por GenerateTimetableJob (§15 "diff antes de
 * aplicar") y notifica:
 *  - a los docentes cuyos slots cambiaron (solo los afectados, no todos);
 *  - a coordinación/planificación con el resumen del diff.
 */
class NotifyTimetableChangesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * @param  array<string, mixed>  $diff
     */
    public function __construct(
        public int $calendarId,
        public array $diff = [],
    ) {}

    public function handle(): void
    {
        $calendar = TimetableCalendar::query()->find($this->calendarId);

        if (! $calendar) {
            return;
        }

        $changedProfesorIds = array_map('intval', $this->diff['profesores_afectados'] ?? []);

        // Docentes afectados (Profesor.user_id) → notificación individual.
        $teacherUsers = User::query()
            ->whereIn('id', Profesor::query()
                ->whereIn('id', $changedProfesorIds)
                ->pluck('user_id'))
            ->get();

        $summary = $this->summaryMessage($calendar, $this->diff);

        if ($teacherUsers->isNotEmpty()) {
            app(\App\Services\NotificationService::class)->notifyUsers($teacherUsers, new TimetableChangedNotification(
                calendarId: $calendar->id,
                calendarName: $calendar->name,
                message: 'Tu horario fue actualizado. '.$summary,
            ));
        }

        // Resumen para coordinación/planificación.
        $coordinators = User::query()
            ->where('is_coordinacion', true)
            ->orWhere('is_planner', true)
            ->get();

        if ($coordinators->isNotEmpty()) {
            app(\App\Services\NotificationService::class)->notifyUsers($coordinators, new TimetableChangedNotification(
                calendarId: $calendar->id,
                calendarName: $calendar->name,
                message: 'Horario publicado. '.$summary,
                type: 'timetable_generated',
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $diff
     */
    private function summaryMessage(TimetableCalendar $calendar, array $diff): string
    {
        $parts = [];

        if (($diff['total_lessons'] ?? 0) > 0) {
            $parts[] = "{$diff['total_lessons']} lecciones asignadas";
        }

        if (($diff['changed'] ?? 0) > 0) {
            $parts[] = "{$diff['changed']} movieron de período";
        }

        if (($diff['removed'] ?? 0) > 0) {
            $parts[] = "{$diff['removed']} sin asignar";
        }

        if (count($changedIds = $diff['profesores_afectados'] ?? []) > 0) {
            $parts[] = count($changedIds).' docentes afectados';
        }

        return $parts === [] ? 'Sin cambios respecto al horario vigente.' : implode(', ', $parts).'.';
    }

    private function correlationId(): string
    {
        return $this->calendarId.'-'.now()->format('YmdHis');
    }
}
