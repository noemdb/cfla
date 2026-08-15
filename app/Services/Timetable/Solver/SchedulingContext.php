<?php

namespace App\Services\Timetable\Solver;

/**
 * SPEC-TIMETABLE-001 §6.1 — Estado mutable del backtracking: qué está ocupado.
 *
 * ADR-TT-008: roomBusy solo se toca cuando $roomId !== null; si no, la clave
 * "periodId:" con roomId null colisionaría entre secciones sin aula dedicada.
 */
final class SchedulingContext
{
    /** @var array<string, true> "periodId:profesorId" */
    private array $teacherBusy = [];

    /** @var array<string, true> "periodId:roomId" (solo roomId != null) */
    private array $roomBusy = [];

    /** @var array<string, true> "periodId:seccionId" */
    private array $sectionBusy = [];

    public function isFree(int $periodId, int $profesorId, int $seccionId, ?int $roomId): bool
    {
        if (isset($this->teacherBusy["$periodId:$profesorId"])) {
            return false;
        }

        if (isset($this->sectionBusy["$periodId:$seccionId"])) {
            return false;
        }

        if ($roomId !== null && isset($this->roomBusy["$periodId:$roomId"])) {
            return false;
        }

        return true;
    }

    public function occupy(int $periodId, int $profesorId, int $seccionId, ?int $roomId): void
    {
        $this->teacherBusy["$periodId:$profesorId"] = true;
        $this->sectionBusy["$periodId:$seccionId"] = true;

        if ($roomId !== null) {
            $this->roomBusy["$periodId:$roomId"] = true;
        }
    }

    public function release(int $periodId, int $profesorId, int $seccionId, ?int $roomId): void
    {
        unset($this->teacherBusy["$periodId:$profesorId"]);
        unset($this->sectionBusy["$periodId:$seccionId"]);

        if ($roomId !== null) {
            unset($this->roomBusy["$periodId:$roomId"]);
        }
    }
}
