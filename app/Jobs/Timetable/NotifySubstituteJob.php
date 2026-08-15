<?php

namespace App\Jobs\Timetable;

use App\Models\app\Timetable\TimetableSubstituteAssignment;
use App\Models\User;
use App\Notifications\SubstituteAssignedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SPEC-TIMETABLE-001 §10 (v1.2) — Notifica a un suplente que se le asignó
 * una suplencia (solo al usuario del Profesor suplente, por cola).
 */
class NotifySubstituteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(public int $assignmentId) {}

    public function handle(): void
    {
        $assignment = TimetableSubstituteAssignment::query()
            ->with(['slot.lesson.pevaluacion.pensum.asignatura', 'slot.lesson.pevaluacion.seccion', 'slot.period', 'absence'])
            ->find($this->assignmentId);

        if (! $assignment || $assignment->status !== 'pending') {
            return;
        }

        $substituteProfesorId = $assignment->substitute_profesor_id;
        $user = User::query()
            ->whereIn('id', \App\Models\app\Academy\Profesor::query()
                ->where('id', $substituteProfesorId)
                ->pluck('user_id'))
            ->first();

        if (! $user) {
            return;
        }

        try {
            $period = $assignment->slot?->period;

            app(\App\Services\NotificationService::class)->notifyUsers([$user], new SubstituteAssignedNotification(
                assignmentId: $assignment->id,
                calendarName: $assignment->absence?->calendar?->name ?? 'Horario',
                date: $period?->day_label ?? 'Día '.($period?->day_of_week ?? ''),
                periodLabel: $period?->period_label ?? 'bloque '.($period?->order_in_day ?? ''),
                subjectLabel: $assignment->slot?->lesson?->pevaluacion?->pensum?->asignatura?->name ?? 'Clase',
            ));

            $assignment->update(['notified_at' => now()]);
        } catch (\Throwable $e) {
            Log::channel('timetable')->warning('NotifySubstituteJob: fallo', [
                'assignment_id' => $this->assignmentId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
