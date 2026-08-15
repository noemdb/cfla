<?php

namespace App\Services\Timetable;

use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableAbsence;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\app\Timetable\TimetableSubstituteAssignment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * SPEC-TIMETABLE-001 §7 — Gestión de ausencias y suplentes.
 *
 * Flujo (v1.2, ADR-TT-012):
 *  1. Coordinación registra una ausencia (docente + rango de fechas).
 *  2. Se identifican los slots afectados del docente (join por day_of_week
 *     dentro del rango).
 *  3. Se sugieren suplentes candidatos: docentes con disponibilidad en ese
 *     slot y sin doble-booking (misma validación dura que ConflictValidator).
 *  4. Coordinación confirma → se crea TimetableSubstituteAssignment (pending)
 *     y se notifica al suplente (Job de cola).
 *  5. El suplente confirma/declina desde su bandeja.
 */
class SubstituteService
{
    public function registerAbsence(
        int $calendarId,
        int $profesorId,
        string $dateStart,
        string $dateEnd,
        ?string $reason = null,
    ): TimetableAbsence {
        return TimetableAbsence::create([
            'calendar_id' => $calendarId,
            'profesor_id' => $profesorId,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'reason' => $reason,
        ]);
    }

    /**
     * Slots del docente ausente cuyo día de la semana cae dentro del rango.
     *
     * @return Collection<int, TimetableSlot>
     */
    public function affectedSlots(TimetableAbsence $absence): Collection
    {
        $start = CarbonImmutable::parse($absence->date_start);
        $end = CarbonImmutable::parse($absence->date_end);

        // day_of_week (1=Lun..7=Dom) de cada fecha dentro del rango.
        $days = [];
        for ($date = $start; $date->lte($end); $date = $date->addDay()) {
            $days[(int) $date->dayOfWeek] = true; // Carbon: 1=Lun, 7=Dom
        }

        $periodsOfDays = TimetablePeriod::query()
            ->where('calendar_id', $absence->calendar_id)
            ->whereIn('day_of_week', array_keys($days))
            ->pluck('id')
            ->all();

        return TimetableSlot::query()
            ->where('calendar_id', $absence->calendar_id)
            ->where('profesor_id', $absence->profesor_id)
            ->whereIn('period_id', $periodsOfDays)
            ->with(['lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.seccion'])
            ->get();
    }

    /**
     * Suplentes candidatos para un slot: docentes activos (distintos del
     * ausente) que NO tengan otra clase en ese período y cuyo usuario exista.
     *
     * @return Collection<int, array{profesor: Profesor, conflict: string}>
     */
    public function candidateSubstitutes(TimetableSlot $slot): Collection
    {
        $busyProfesorIds = TimetableSlot::query()
            ->where('calendar_id', $slot->calendar_id)
            ->where('period_id', $slot->period_id)
            ->where('profesor_id', '!=', $slot->profesor_id)
            ->pluck('profesor_id')
            ->unique()
            ->all();

        $candidates = Profesor::query()
            ->where('status_active', 'true')
            ->where('id', '!=', $slot->profesor_id)
            ->whereNotNull('user_id')
            ->when($busyProfesorIds, fn ($q) => $q->whereNotIn('id', $busyProfesorIds))
            ->orderBy('lastname')
            ->get();

        return $candidates->map(fn (Profesor $p) => [
            'profesor' => $p,
            'conflict' => $this->candidateConflict($slot, $p),
        ]);
    }

    /**
     * Crea la suplencia (status pending) y devuelve la asignación. La
     * notificación al suplente se dispara por cola.
     */
    public function assignSubstitute(
        TimetableAbsence $absence,
        TimetableSlot $slot,
        int $substituteProfesorId,
    ): TimetableSubstituteAssignment {
        return TimetableSubstituteAssignment::create([
            'absence_id' => $absence->id,
            'slot_id' => $slot->id,
            'substitute_profesor_id' => $substituteProfesorId,
            'status' => 'pending',
            'notified_at' => null,
        ]);
    }

    public function confirm(int $assignmentId): void
    {
        TimetableSubstituteAssignment::query()
            ->where('id', $assignmentId)
            ->update(['status' => 'confirmed']);
    }

    public function decline(int $assignmentId): void
    {
        TimetableSubstituteAssignment::query()
            ->where('id', $assignmentId)
            ->update(['status' => 'declined']);
    }

    private function candidateConflict(TimetableSlot $slot, Profesor $candidate): string
    {
        // Disponibilidad explícita no disponible → aviso (no bloquea).
        $availability = \App\Models\app\Timetable\TimetableTeacherAvailability::query()
            ->where('calendar_id', $slot->calendar_id)
            ->where('profesor_id', $candidate->id)
            ->where('period_id', $slot->period_id)
            ->first();

        if ($availability && ! $availability->is_available) {
            return 'Sin disponibilidad declarada en ese período.';
        }

        return '';
    }
}
