<?php

namespace App\Services\Timetable\Solver;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §4/§5 — Configuración de un intento del
 * solver. El orquestador encadena varios intentos con distinto orden de
 * lecciones y semilla, conservando la mejor solución (fallback + recursividad
 * controlada).
 */
final class SolverAttemptConfig
{
    /** Orden actual del solver: mayor grado de restricción primero (ADR-TT-003). */
    public const ORDER_CONSTRAINT = 'constraint';

    /** Docentes con menos períodos disponibles primero (cuellos de botella). */
    public const ORDER_SCARCITY = 'scarcity';

    /** Lecciones con más bloques primero (las difíciles de ubicar). */
    public const ORDER_BLOCKS_DESC = 'blocks_desc';

    /** Orden aleatorio determinista por semilla (restarts). */
    public const ORDER_RANDOM = 'random';

    public function __construct(
        public readonly string $id = 'S1',
        public readonly string $ordering = self::ORDER_CONSTRAINT,
        public readonly int $seed = 0,
        public readonly int $timeLimitSeconds = 8,
    ) {}
}
