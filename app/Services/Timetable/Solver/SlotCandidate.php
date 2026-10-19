<?php

namespace App\Services\Timetable\Solver;

/**
 * SPEC-TIMETABLE-001 §6.1 — Slot candidato: un período con un aula opcional.
 * roomId null = bloque teórico sin aula dedicada.
 *
 * ADR-TT-017: `half` (0 = superior, 1 = inferior) indica media franja;
 * `null` = bloque completo (ambas mitades).
 */
final class SlotCandidate
{
    public function __construct(
        public readonly int $periodId,
        public readonly ?int $roomId = null,
        public readonly bool $isPractical = false,
        public readonly ?int $half = null,
    ) {}
}
