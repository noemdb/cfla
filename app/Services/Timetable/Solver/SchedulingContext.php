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
    public function __construct(private int $maxSubjectsPerPeriod = 2)
    {
        $this->maxSubjectsPerPeriod = max(1, $maxSubjectsPerPeriod);
    }

    /** @var array<string, true> "periodId:profesorId" */
    private array $teacherBusy = [];

    /** @var array<string, true> "periodId:roomId" (solo roomId != null) */
    private array $roomBusy = [];

    /** @var array<string, true> "periodId:seccionId" — lección de sección completa ocupa toda la sección */
    private array $sectionWholeBusy = [];

    /** @var array<string, int> "periodId:seccionId" — nº de sub-grupos ocupando el período */
    private array $sectionGroupCount = [];

    /** @var array<string, int> Nº de lecciones de medio grupo en el período */
    private array $sectionHalfGroupCount = [];

    public function halfGroupLoad(int $periodId, int $seccionId): int
    {
        return $this->sectionHalfGroupCount["$periodId:$seccionId"] ?? 0;
    }

    /** @var array<string, true> "periodId:seccionId:grupoId" — sub-grupo ocupado */
    private array $sectionGroupBusy = [];

    /**
     * @param  int|null  $grupoEstableId  null = lección de sección completa.
     *                                    Un valor = sub-grupo (componente de formación).
     */
    public function isFree(int $periodId, int $profesorId, int $seccionId, ?int $roomId, ?int $grupoEstableId = null, bool $isHalfGroup = false): bool
    {
        if (isset($this->teacherBusy["$periodId:$profesorId"])) {
            return false;
        }

        if ($roomId !== null && isset($this->roomBusy["$periodId:$roomId"])) {
            return false;
        }

        // Sección completa ocupada → nada puede entrar (ni normal ni sub-grupo).
        if (isset($this->sectionWholeBusy["$periodId:$seccionId"])) {
            return false;
        }

        if ($isHalfGroup) {
            return ($this->sectionHalfGroupCount["$periodId:$seccionId"] ?? 0) < $this->maxSubjectsPerPeriod;
        }

        if ($grupoEstableId === null) {
            // Lección de sección completa: requiere que NO haya actividad de
            // la sección, incluidos medios grupos que ya ocupen la celda.
            return ($this->sectionGroupCount["$periodId:$seccionId"] ?? 0) === 0
                && ($this->sectionHalfGroupCount["$periodId:$seccionId"] ?? 0) === 0;
        }

        // Lección de sub-grupo: solo choca con su propio sub-grupo.
        return ! isset($this->sectionGroupBusy["$periodId:$seccionId:$grupoEstableId"]);
    }

    /**
     * @param  int|null  $grupoEstableId  null = lección de sección completa.
     */
    public function occupy(int $periodId, int $profesorId, int $seccionId, ?int $roomId, ?int $grupoEstableId = null, bool $isHalfGroup = false): void
    {
        $this->teacherBusy["$periodId:$profesorId"] = true;

        if ($roomId !== null) {
            $this->roomBusy["$periodId:$roomId"] = true;
        }

        if ($isHalfGroup) {
            $this->sectionHalfGroupCount["$periodId:$seccionId"] = ($this->sectionHalfGroupCount["$periodId:$seccionId"] ?? 0) + 1;
        } elseif ($grupoEstableId === null) {
            $this->sectionWholeBusy["$periodId:$seccionId"] = true;
        } else {
            $this->sectionGroupBusy["$periodId:$seccionId:$grupoEstableId"] = true;
            $this->sectionGroupCount["$periodId:$seccionId"] = ($this->sectionGroupCount["$periodId:$seccionId"] ?? 0) + 1;
        }
    }

    /**
     * @param  int|null  $grupoEstableId  null = lección de sección completa.
     */
    public function release(int $periodId, int $profesorId, int $seccionId, ?int $roomId, ?int $grupoEstableId = null, bool $isHalfGroup = false): void
    {
        unset($this->teacherBusy["$periodId:$profesorId"]);

        if ($roomId !== null) {
            unset($this->roomBusy["$periodId:$roomId"]);
        }

        if ($isHalfGroup) {
            $this->sectionHalfGroupCount["$periodId:$seccionId"] = ($this->sectionHalfGroupCount["$periodId:$seccionId"] ?? 1) - 1;
            if (($this->sectionHalfGroupCount["$periodId:$seccionId"] ?? 0) <= 0) {
                unset($this->sectionHalfGroupCount["$periodId:$seccionId"]);
            }
        } elseif ($grupoEstableId === null) {
            unset($this->sectionWholeBusy["$periodId:$seccionId"]);
        } else {
            unset($this->sectionGroupBusy["$periodId:$seccionId:$grupoEstableId"]);
            $this->sectionGroupCount["$periodId:$seccionId"] = ($this->sectionGroupCount["$periodId:$seccionId"] ?? 1) - 1;
            if (($this->sectionGroupCount["$periodId:$seccionId"] ?? 0) <= 0) {
                unset($this->sectionGroupCount["$periodId:$seccionId"]);
            }
        }
    }
}
