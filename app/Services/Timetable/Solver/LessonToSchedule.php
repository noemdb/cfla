<?php

namespace App\Services\Timetable\Solver;

/**
 * SPEC-TIMETABLE-001 §6.1 — DTO inmutable de una lección a programar.
 *
 * La lección envuelve una Pevaluacion (ADR-TT-001): docente, sección, turno y
 * el desglose teórico/práctico (ADR-TT-010) derivado de Asignatura.hour_t_week
 * / hour_p_week. room_type_required SOLO se exige para bloques prácticos.
 */
final class LessonToSchedule
{
    public function __construct(
        public readonly int $lessonId,
        public readonly int $seccionId,
        public readonly int $profesorId,
        public readonly int $shiftId,
        public readonly int $blocksT,
        public readonly int $blocksP,
        public readonly ?string $roomTypeRequired = null,
        public readonly int $priority = 0,
        public readonly bool $locked = false,
        /** @var array<int, int> Si locked=true, períodos ya fijados. */
        public readonly array $lockedPeriodIds = [],
    ) {}

    public function blocksNeeded(): int
    {
        return $this->blocksT + $this->blocksP;
    }

    /**
     * Máscara de restricción para ADR-TT-003 (orden por grado de restricción).
     * Mayor valor = más restrictivo = se asigna primero.
     */
    public function constraintDegree(): int
    {
        return ($this->priority * 10)
            + ($this->roomTypeRequired !== null ? 5 : 0)
            + min($this->blocksNeeded(), 9);
    }
}
