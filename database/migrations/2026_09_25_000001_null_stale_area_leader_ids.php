<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Higiene de datos (ítem 3): 9 áreas de conocimiento apuntan a leader_id
 * rancios — usuario inactivo (1962, disable) o usuarios activos sin ningún
 * rol responsable (3136, 3269, 1826) — que nunca pueden recibir ni abrir las
 * notificaciones de jefatura. Se ponen en NULL con guarda doble (por área y
 * por leader esperado): idempotente y no toca asignaciones válidas hechas
 * después de este análisis.
 *
 * Detalle verificado 2026-09-25:
 * - área 14 (CIENCIAS NATURALES PRI) → 1962 (disable)
 * - áreas 23, 24, 25 (INGLÉS) → 3136 (sin roles)
 * - áreas 28, 29, 30 (INGLÉS) → 3269 (sin roles)
 * - áreas 45, 47 (FORMACIÓN HUMANO CRISTIANA) → 1826 (sin rol)
 */
return new class extends Migration
{
    private const STALE = [
        14 => 1962,
        23 => 3136,
        24 => 3136,
        25 => 3136,
        28 => 3269,
        29 => 3269,
        30 => 3269,
        45 => 1826,
        47 => 1826,
    ];

    public function up(): void
    {
        foreach (self::STALE as $areaId => $leaderId) {
            DB::table('area_conocimientos')
                ->where('id', $areaId)
                ->where('leader_id', $leaderId)
                ->update(['leader_id' => null]);
        }
    }

    public function down(): void
    {
        // Irreversible por diseño: no se conserva a quién apuntaba cada área
        // (eran usuarios inactivos o sin rol). Reasignar jefaturas es una
        // decisión administrativa, no un rollback técnico.
    }
};
