<?php

namespace App\Services\Timetable\Solver;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001-MEJORADO §6 (TT-CFP-03) — Razones de
 * no asignación tipadas, para clasificar el residual y ofrecer feedback
 * accionable al usuario.
 */
enum UnassignedReason: string
{
    /** C-1: la sección o el docente piden más bloques que períodos hay. */
    case CapacityExceeded = 'capacity_exceeded';

    /** Hay capacidad pero la heurística/topes no ubicaron la lección. */
    case NotFound = 'not_found';

    /** C-2: el tope de combinaciones/pool descartó la combinación factible. */
    case ComboCapReached = 'combo_cap_reached';

    /** El presupuesto de tiempo se agotó (ADR-TT-009). */
    case TimeoutReached = 'timeout_reached';

    /** Falta la estructura base (períodos/asignaciones del módulo Inicial). */
    case IncompleteInitialSetup = 'incomplete_initial_setup';

    /** La fase de reparación no logró liberar un bloque para la lección. */
    case RepairFailed = 'repair_failed';

    public function label(): string
    {
        return match ($this) {
            self::CapacityExceeded => 'Exceso de capacidad (imposible de agendar)',
            self::NotFound => 'No encontrada por la búsqueda heurística',
            self::ComboCapReached => 'Tope de combinaciones alcanzado',
            self::TimeoutReached => 'Tiempo de búsqueda agotado',
            self::IncompleteInitialSetup => 'Estructura de períodos incompleta',
            self::RepairFailed => 'La reparación no pudo liberar espacio',
        };
    }

    public function action(): string
    {
        return match ($this) {
            self::CapacityExceeded => 'Ajusta horas, períodos o turnos: regenerar no la ubicará.',
            self::NotFound => 'Vuelve a generar; la estrategia con fallback puede ubicarla.',
            self::ComboCapReached => 'Vuelve a generar con más presupuesto/intentos.',
            self::TimeoutReached => 'Aumenta el presupuesto del solver o reduce restricciones.',
            self::IncompleteInitialSetup => 'Completa períodos y turnos en el Paso 1.',
            self::RepairFailed => 'Revisa las clases paralelas o bloqueadas de la sección.',
        };
    }
}
