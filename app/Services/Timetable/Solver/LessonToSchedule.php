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
        /** Sub-grupo (componente de formación). null = lección de sección completa. */
        public readonly ?int $grupoEstableId = null,
        public readonly bool $isHalfGroup = false,
        /** @var list<SlotCandidate> Slots válidos ya asignados que deben conservarse. */
        public readonly array $preassignedSlots = [],
    ) {}

    public function blocksNeeded(): int
    {
        return $this->blocksT + $this->blocksP;
    }

    public function remainingBlocksT(): int
    {
        return max(0, $this->blocksT - $this->assignedBlocks(false));
    }

    public function remainingBlocksP(): int
    {
        return max(0, $this->blocksP - $this->assignedBlocks(true));
    }

    private function assignedBlocks(bool $practical): int
    {
        return count(array_filter(
            $this->preassignedSlots,
            fn (SlotCandidate $slot): bool => $slot->isPractical === $practical,
        ));
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
