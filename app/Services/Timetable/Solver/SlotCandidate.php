<?php

namespace App\Services\Timetable\Solver;

/**
 * SPEC-TIMETABLE-001 §6.1 — Slot candidato: un período con un aula opcional.
 * roomId null = bloque teórico sin aula dedicada.
 */
final class SlotCandidate
{
    public function __construct(
        public readonly int $periodId,
        public readonly ?int $roomId = null,
        public readonly bool $isPractical = false,
    ) {}
}
