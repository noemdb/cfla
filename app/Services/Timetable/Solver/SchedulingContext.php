<?php

namespace App\Services\Timetable\Solver;

/**
 * SPEC-TIMETABLE-001 §6.1 — Estado mutable del backtracking: qué está ocupado.
 *
 * ADR-TT-008: roomBusy solo se toca cuando $roomId !== null; si no, la clave
 * "periodId:" con roomId null colisionaría entre secciones sin aula dedicada.
 *
 * ADR-TT-017 — Mitad de bloque: la ocupación se evalúa por **sub-franja**
 * (período + mitad). El parámetro `$half` es opcional y va al final:
 *   - `null`  = bloque completo → ocupa/libera las dos mitades (0 y 1).
 *   - `0`/`1` = media franja concreta.
 *
 * Así una lección completa choca con cualquier mitad del período (H5) y dos
 * mitades secuenciales del mismo período no chocan entre sí (H1–H3).
 */
final class SchedulingContext
{
    public function __construct(private int $maxSubjectsPerPeriod = 2)
    {
        $this->maxSubjectsPerPeriod = max(1, $maxSubjectsPerPeriod);
    }

    /** @var array<string, array<int, bool>> "periodId:half:profesorId" => [lessonId => true] (ocupantes) */
    private array $teacherOccupants = [];

    /** @var array<string, array<int, bool>> "periodId:half:profesorId" => [lessonId => allow_shared_teacher] */
    private array $teacherOccupantShared = [];

    /** @var array<string, true> "periodId:half:roomId" (solo roomId != null) */
    private array $roomBusy = [];

    /** @var array<string, true> "periodId:half:seccionId" — lección de sección completa ocupa toda la sección */
    private array $sectionWholeBusy = [];

    /** @var array<string, int> "periodId:half:seccionId" — nº de sub-grupos ocupando la sub-franja */
    private array $sectionGroupCount = [];

    /** @var array<string, int> Nº de lecciones de medio grupo en la sub-franja */
    private array $sectionHalfGroupCount = [];

    /** @var array<string, int> "periodId:half:seccionId" — carga total de la sección en la sub-franja */
    private array $sectionLoad = [];

    /**
     * @param  int|null  $half  null = máximo del período completo; 0/1 = sub-franja.
     */
    public function sectionHalfLoad(int $periodId, int $seccionId, ?int $half = null): int
    {
        if ($half !== null) {
            return $this->sectionLoad["$periodId:$half:$seccionId"] ?? 0;
        }

        return max(
            $this->sectionLoad["$periodId:0:$seccionId"] ?? 0,
            $this->sectionLoad["$periodId:1:$seccionId"] ?? 0,
        );
    }

    /**
     * @param  int|null  $half  null = suma/máximo del período completo; 0/1 = sub-franja.
     */
    public function halfGroupLoad(int $periodId, int $seccionId, ?int $half = null): int
    {
        if ($half !== null) {
            return $this->sectionHalfGroupCount["$periodId:$half:$seccionId"] ?? 0;
        }

        return max(
            $this->sectionHalfGroupCount["$periodId:0:$seccionId"] ?? 0,
            $this->sectionHalfGroupCount["$periodId:1:$seccionId"] ?? 0,
        );
    }

    /** @var array<string, true> "periodId:half:seccionId:grupoId" — sub-grupo ocupado */
    private array $sectionGroupBusy = [];

    /**
     * @param  int|null  $grupoEstableId  null = lección de sección completa.
     *                                    Un valor = sub-grupo (componente de formación).
     * @param  int|null  $half  null = bloque completo (ambas mitades); 0/1 = media franja.
     */
    public function isFree(
        int $periodId,
        int $profesorId,
        int $seccionId,
        ?int $roomId,
        ?int $grupoEstableId = null,
        bool $isHalfGroup = false,
        int $lessonId = 0,
        bool $allowSharedTeacher = false,
        ?int $half = null,
    ): bool {
        foreach ($this->halvesFor($half) as $currentHalf) {
            if (! $this->isFreeInHalf(
                $periodId,
                $currentHalf,
                $profesorId,
                $seccionId,
                $roomId,
                $grupoEstableId,
                $isHalfGroup,
                $allowSharedTeacher,
            )) {
                return false;
            }
        }

        return true;
    }

    private function isFreeInHalf(
        int $periodId,
        int $half,
        int $profesorId,
        int $seccionId,
        ?int $roomId,
        ?int $grupoEstableId,
        bool $isHalfGroup,
        bool $allowSharedTeacher,
    ): bool {
        $teacherKey = "$periodId:$half:$profesorId";
        $occupants = $this->teacherOccupants[$teacherKey] ?? [];

        if ($occupants !== []) {
            $allShared = collect($occupants)->keys()->every(
                fn ($lessonId): bool => (bool) ($this->teacherOccupantShared[$teacherKey][$lessonId] ?? false),
            );

            // La excepción de docente compartido exige que TODAS las lessons
            // ocupantes estén autorizadas y que la candidata también lo esté.
            if (! ($allowSharedTeacher && $allShared)) {
                return false;
            }
        }

        if ($roomId !== null && isset($this->roomBusy["$periodId:$half:$roomId"])) {
            return false;
        }

        // Sección completa ocupada → nada puede entrar (ni normal ni sub-grupo).
        if (isset($this->sectionWholeBusy["$periodId:$half:$seccionId"])) {
            return false;
        }

        if ($isHalfGroup) {
            return ($this->sectionHalfGroupCount["$periodId:$half:$seccionId"] ?? 0) < $this->maxSubjectsPerPeriod;
        }

        if ($grupoEstableId === null) {
            // Lección de sección completa: requiere que NO haya actividad de
            // la sección, incluidos medios grupos que ya ocupen la sub-franja.
            return ($this->sectionGroupCount["$periodId:$half:$seccionId"] ?? 0) === 0
                && ($this->sectionHalfGroupCount["$periodId:$half:$seccionId"] ?? 0) === 0;
        }

        // Lección de sub-grupo: solo choca con su propio sub-grupo.
        return ! isset($this->sectionGroupBusy["$periodId:$half:$seccionId:$grupoEstableId"]);
    }

    /**
     * @param  int|null  $grupoEstableId  null = lección de sección completa.
     * @param  int|null  $half  null = bloque completo (ambas mitades); 0/1 = media franja.
     */
    public function occupy(
        int $periodId,
        int $profesorId,
        int $seccionId,
        ?int $roomId,
        ?int $grupoEstableId = null,
        bool $isHalfGroup = false,
        int $lessonId = 0,
        bool $allowSharedTeacher = false,
        ?int $half = null,
    ): void {
        foreach ($this->halvesFor($half) as $currentHalf) {
            $this->occupyInHalf(
                $periodId,
                $currentHalf,
                $profesorId,
                $seccionId,
                $roomId,
                $grupoEstableId,
                $isHalfGroup,
                $lessonId,
                $allowSharedTeacher,
            );
        }
    }

    private function occupyInHalf(
        int $periodId,
        int $half,
        int $profesorId,
        int $seccionId,
        ?int $roomId,
        ?int $grupoEstableId,
        bool $isHalfGroup,
        int $lessonId,
        bool $allowSharedTeacher,
    ): void {
        $teacherKey = "$periodId:$half:$profesorId";
        $this->teacherOccupants[$teacherKey][$lessonId] = true;
        $this->teacherOccupantShared[$teacherKey][$lessonId] = $allowSharedTeacher;

        if ($roomId !== null) {
            $this->roomBusy["$periodId:$half:$roomId"] = true;
        }

        $sectionLoadKey = "$periodId:$half:$seccionId";
        $this->sectionLoad[$sectionLoadKey] = ($this->sectionLoad[$sectionLoadKey] ?? 0) + 1;

        if ($isHalfGroup) {
            $sectionHalfKey = "$periodId:$half:$seccionId";
            $this->sectionHalfGroupCount[$sectionHalfKey] = ($this->sectionHalfGroupCount[$sectionHalfKey] ?? 0) + 1;
        } elseif ($grupoEstableId === null) {
            $this->sectionWholeBusy["$periodId:$half:$seccionId"] = true;
        } else {
            $this->sectionGroupBusy["$periodId:$half:$seccionId:$grupoEstableId"] = true;
            $sectionGroupKey = "$periodId:$half:$seccionId";
            $this->sectionGroupCount[$sectionGroupKey] = ($this->sectionGroupCount[$sectionGroupKey] ?? 0) + 1;
        }
    }

    /**
     * @param  int|null  $grupoEstableId  null = lección de sección completa.
     * @param  int|null  $half  null = bloque completo (ambas mitades); 0/1 = media franja.
     */
    public function release(
        int $periodId,
        int $profesorId,
        int $seccionId,
        ?int $roomId,
        ?int $grupoEstableId = null,
        bool $isHalfGroup = false,
        int $lessonId = 0,
        ?int $half = null,
    ): void {
        foreach ($this->halvesFor($half) as $currentHalf) {
            $this->releaseInHalf(
                $periodId,
                $currentHalf,
                $profesorId,
                $seccionId,
                $roomId,
                $grupoEstableId,
                $isHalfGroup,
                $lessonId,
            );
        }
    }

    private function releaseInHalf(
        int $periodId,
        int $half,
        int $profesorId,
        int $seccionId,
        ?int $roomId,
        ?int $grupoEstableId,
        bool $isHalfGroup,
        int $lessonId,
    ): void {
        $teacherKey = "$periodId:$half:$profesorId";
        unset($this->teacherOccupants[$teacherKey][$lessonId]);
        unset($this->teacherOccupantShared[$teacherKey][$lessonId]);
        if (($this->teacherOccupants[$teacherKey] ?? []) === []) {
            unset($this->teacherOccupants[$teacherKey]);
            unset($this->teacherOccupantShared[$teacherKey]);
        }

        if ($roomId !== null) {
            unset($this->roomBusy["$periodId:$half:$roomId"]);
        }

        $sectionLoadKey = "$periodId:$half:$seccionId";
        $this->sectionLoad[$sectionLoadKey] = ($this->sectionLoad[$sectionLoadKey] ?? 1) - 1;
        if (($this->sectionLoad[$sectionLoadKey] ?? 0) <= 0) {
            unset($this->sectionLoad[$sectionLoadKey]);
        }

        if ($isHalfGroup) {
            $sectionHalfKey = "$periodId:$half:$seccionId";
            $this->sectionHalfGroupCount[$sectionHalfKey] = ($this->sectionHalfGroupCount[$sectionHalfKey] ?? 1) - 1;
            if (($this->sectionHalfGroupCount[$sectionHalfKey] ?? 0) <= 0) {
                unset($this->sectionHalfGroupCount[$sectionHalfKey]);
            }
        } elseif ($grupoEstableId === null) {
            unset($this->sectionWholeBusy["$periodId:$half:$seccionId"]);
        } else {
            unset($this->sectionGroupBusy["$periodId:$half:$seccionId:$grupoEstableId"]);
            $sectionGroupKey = "$periodId:$half:$seccionId";
            $this->sectionGroupCount[$sectionGroupKey] = ($this->sectionGroupCount[$sectionGroupKey] ?? 1) - 1;
            if (($this->sectionGroupCount[$sectionGroupKey] ?? 0) <= 0) {
                unset($this->sectionGroupCount[$sectionGroupKey]);
            }
        }
    }

    /** @return array<int, int> */
    private function halvesFor(?int $half): array
    {
        return $half === null ? [0, 1] : [$half];
    }
}
