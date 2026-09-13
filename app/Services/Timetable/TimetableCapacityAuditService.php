<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableCalendar;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §0/§7 — Auditoría de capacidad.
 *
 * Compara el volumen semanal que exige cada sección y cada docente contra los
 * períodos realmente asignables (no-recreo) de los turnos que usan. Un overflow
 * positivo implica bloques matemáticamente imposibles de agendar (C-1), que
 * ningún solver/reinicio puede ubicar.
 *
 * Supuestos conservadores (para no reportar falsos positivos):
 * - No se descuentan disponibilidad ni `locked` (capacidad teórica máxima).
 * - Se concede capacidad combinada de todos los turnos que usa la sección/docente.
 * - Medio grupo pesa 0.5 (puede compartir celda en pareja).
 */
class TimetableCapacityAuditService
{
    private const HALF_GROUP_WEIGHT = 0.5;

    public function audit(TimetableCalendar $calendar): CapacityAuditReport
    {
        $periodsByShift = $calendar->periods()
            ->where('is_break', false)
            ->get(['id', 'shift_id'])
            ->groupBy('shift_id')
            ->map(fn ($group): int => $group->count())
            ->mapWithKeys(fn (int $count, $shiftId): array => [(int) $shiftId => $count])
            ->all();

        $lessons = $calendar->lessons()
            ->with(['pevaluacion' => fn ($query) => $query->select('id', 'profesor_id', 'seccion_id')])
            ->get(['id', 'pevaluacion_id', 'shift_id', 'weekly_blocks_t', 'weekly_blocks_p', 'is_half_group']);

        $sections = [];
        $teachers = [];

        foreach ($lessons as $lesson) {
            $pev = $lesson->pevaluacion;

            if (! $pev) {
                continue;
            }

            $blocks = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;

            if ($blocks <= 0) {
                continue;
            }

            $shiftId = (int) $lesson->shift_id;
            $seccionId = (int) $pev->seccion_id;
            $profesorId = (int) $pev->profesor_id;

            $sections[$seccionId] ??= [
                'seccion_id' => $seccionId,
                'shift_ids' => [],
                'load' => 0.0,
                'lesson_ids' => [],
            ];
            $sections[$seccionId]['load'] += $blocks * ($lesson->is_half_group ? self::HALF_GROUP_WEIGHT : 1.0);
            $sections[$seccionId]['shift_ids'][$shiftId] = true;
            $sections[$seccionId]['lesson_ids'][] = (int) $lesson->id;

            $teachers[$profesorId] ??= [
                'profesor_id' => $profesorId,
                'shift_ids' => [],
                'load' => 0,
                'lesson_ids' => [],
            ];
            $teachers[$profesorId]['load'] += $blocks;
            $teachers[$profesorId]['shift_ids'][$shiftId] = true;
            $teachers[$profesorId]['lesson_ids'][] = (int) $lesson->id;
        }

        foreach ($sections as &$row) {
            $shiftIds = array_map('intval', array_keys($row['shift_ids']));
            $row['shift_ids'] = $shiftIds;
            $row['capacity'] = $this->capacityFor($shiftIds, $periodsByShift);
            $row['overflow'] = max(0.0, $row['load'] - $row['capacity']);
        }
        unset($row);

        foreach ($teachers as &$row) {
            $shiftIds = array_map('intval', array_keys($row['shift_ids']));
            $row['shift_ids'] = $shiftIds;
            $row['capacity'] = $this->capacityFor($shiftIds, $periodsByShift);
            $row['overflow'] = max(0, $row['load'] - $row['capacity']);
        }
        unset($row);

        ksort($sections);
        ksort($teachers);

        return new CapacityAuditReport(
            calendarId: (int) $calendar->id,
            periodsByShift: $periodsByShift,
            sections: $sections,
            teachers: $teachers,
        );
    }

    /**
     * @param  list<int>  $shiftIds
     * @param  array<int,int>  $periodsByShift
     */
    private function capacityFor(array $shiftIds, array $periodsByShift): int
    {
        $capacity = 0;

        foreach ($shiftIds as $shiftId) {
            $capacity += $periodsByShift[$shiftId] ?? 0;
        }

        return $capacity;
    }
}
