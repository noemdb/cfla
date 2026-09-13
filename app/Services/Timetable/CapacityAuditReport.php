<?php

namespace App\Services\Timetable;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §0/§7 — Reporte de capacidad de un
 * calendario: cuántos bloques pide cada sección/docente frente a los períodos
 * realmente asignables. Sirve para separar infactibilidad real (C-1) de
 * "no encontrado" por heurística.
 *
 * @phpstan-type SectionRow array{seccion_id:int,shift_ids:list<int>,load:float,capacity:int,overflow:float,lesson_ids:list<int>}
 * @phpstan-type TeacherRow array{profesor_id:int,shift_ids:list<int>,load:int,capacity:int,overflow:int,lesson_ids:list<int>}
 */
final class CapacityAuditReport
{
    /**
     * @param  array<int,int>  $periodsByShift  shiftId => períodos no-break
     * @param  array<int, SectionRow>  $sections  seccionId => fila
     * @param  array<int, TeacherRow>  $teachers  profesorId => fila
     */
    public function __construct(
        public readonly int $calendarId,
        public readonly array $periodsByShift,
        public readonly array $sections,
        public readonly array $teachers,
    ) {}

    /**
     * @return list<SectionRow>
     */
    public function overflowSections(): array
    {
        return array_values(array_filter(
            $this->sections,
            fn (array $row): bool => $row['overflow'] > 0,
        ));
    }

    /**
     * @return list<TeacherRow>
     */
    public function overflowTeachers(): array
    {
        return array_values(array_filter(
            $this->teachers,
            fn (array $row): bool => $row['overflow'] > 0,
        ));
    }

    public function overflowBlocksSections(): float
    {
        return array_sum(array_map(fn (array $row): float => $row['overflow'], $this->overflowSections()));
    }

    public function overflowBlocksTeachers(): float
    {
        return array_sum(array_map(fn (array $row): float => $row['overflow'], $this->overflowTeachers()));
    }

    /**
     * Lecciones que pertenecen a una sección o a un docente con overflow. Es un
     * conjunto candidato: si la sección excede, al menos `overflow` bloques de
     * sus lecciones no podrán ubicarse, aunque no sepamos cuáles exactamente.
     *
     * @return list<int>
     */
    public function overCapacityLessonIds(): array
    {
        $ids = [];
        foreach ($this->overflowSections() as $row) {
            foreach ($row['lesson_ids'] as $id) {
                $ids[$id] = true;
            }
        }
        foreach ($this->overflowTeachers() as $row) {
            foreach ($row['lesson_ids'] as $id) {
                $ids[$id] = true;
            }
        }

        return array_map('intval', array_keys($ids));
    }

    public function hasOverflow(): bool
    {
        return $this->overflowSections() !== [] || $this->overflowTeachers() !== [];
    }

    /**
     * Resumen compacto para el preview_payload (sin listas completas).
     *
     * @return array<string,mixed>
     */
    public function summary(): array
    {
        return [
            'periods_by_shift' => $this->periodsByShift,
            'overflow_blocks_sections' => $this->overflowBlocksSections(),
            'overflow_blocks_teachers' => $this->overflowBlocksTeachers(),
            'overflow_sections' => count($this->overflowSections()),
            'overflow_teachers' => count($this->overflowTeachers()),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'calendar_id' => $this->calendarId,
            'periods_by_shift' => $this->periodsByShift,
            'sections' => array_values($this->sections),
            'teachers' => array_values($this->teachers),
            'summary' => $this->summary(),
        ];
    }
}
