<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableConflict;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;

/**
 * SPEC-TIMETABLE-001 §6 — Validación síncrona de las reglas duras.
 *
 * Se usa por el editor manual (§7) ANTES de persistir un slot movido, y por el
 * wizard antes de encolar. No ejecuta el solver: solo valida un slot candidato.
 *
 * Reglas duras (§6): docente doble, aula doble, sección doble, período en otro
 * turno, disponibilidad no disponible.
 */
class ConflictValidator
{
    /**
     * Comprueba si un slot candidato es válido en el calendario.
     *
     * @param  int  $lessonId  Lección que se quiere ubicar
     * @param  int  $periodId  Período destino
     * @param  int|null  $ignoreSlotId  Slot que se está moviendo (edición), se excluye del chequeo
     * @return array{valid: bool, reasons: list<string>}
     */
    public function validate(
        int $calendarId,
        int $lessonId,
        int $periodId,
        int $profesorId,
        int $seccionId,
        ?int $roomId = null,
        ?int $ignoreSlotId = null,
    ): array {
        $reasons = [];

        // Turno: el período debe pertenecer al turno de la lección.
        $period = TimetablePeriod::query()
            ->with('shift')
            ->find($periodId);

        $lesson = \App\Models\app\Timetable\TimetableLesson::query()
            ->with('shift')
            ->find($lessonId);

        if (! $period || ! $lesson) {
            return ['valid' => false, 'reasons' => ['Período o lección inexistente.']];
        }

        if ($period->shift_id !== $lesson->shift_id) {
            $reasons[] = 'El período pertenece a otro turno.';
        }

        // Docente doble: otro slot (distinto al ignorado) ocupa al docente.
        $conflictQuery = function (int $calendarId, int $periodId, ?int $ignoreSlotId) {
            return TimetableSlot::query()
                ->where('calendar_id', $calendarId)
                ->where('period_id', $periodId)
                ->when($ignoreSlotId, fn ($q) => $q->where('id', '!=', $ignoreSlotId));
        };

        if ($conflictQuery($calendarId, $periodId, $ignoreSlotId)
            ->where('profesor_id', $profesorId)
            ->exists()) {
            $reasons[] = 'El docente ya tiene clase en ese período.';
        }

        if ($conflictQuery($calendarId, $periodId, $ignoreSlotId)
            ->where('seccion_id', $seccionId)
            ->exists()) {
            $reasons[] = 'La sección ya tiene clase en ese período.';
        }

        if ($roomId !== null
            && $conflictQuery($calendarId, $periodId, $ignoreSlotId)
                ->where('room_id', $roomId)
                ->exists()) {
            $reasons[] = 'El aula ya está ocupada en ese período.';
        }

        // Disponibilidad del docente en ese período (si hay fila explícita).
        $availability = \App\Models\app\Timetable\TimetableTeacherAvailability::query()
            ->where('calendar_id', $calendarId)
            ->where('profesor_id', $profesorId)
            ->where('period_id', $periodId)
            ->first();

        if ($availability && ! $availability->is_available) {
            $reasons[] = 'El docente no está disponible en ese período.';
        }

        return [
            'valid' => $reasons === [],
            'reasons' => $reasons,
        ];
    }

    /**
     * Registra un conflicto residual en timetable_conflicts (mejor esfuerzo).
     *
     * @param  array<string, mixed>  $details
     */
    public function recordConflict(
        int $calendarId,
        int $lessonId,
        int $periodId,
        string $type,
        array $details = [],
    ): void {
        try {
            TimetableConflict::updateOrCreate(
                [
                    'calendar_id' => $calendarId,
                    'lesson_id' => $lessonId,
                    'period_id' => $periodId,
                    'type' => $type,
                ],
                ['details' => $details],
            );
        } catch (\Throwable) {
            // El registro de conflictos es mejor esfuerzo: no rompe el flujo.
        }
    }
}
