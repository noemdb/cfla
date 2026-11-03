<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Índices y restricción única de la planificación semanal de Inicial.
 *
 * La auditoría previa (2026-10-20) agrupó por
 * `profesor_id + grado_id + seccion_id + finicial` y no encontró duplicados,
 * así que la restricción se puede aplicar sin limpiar datos.
 *
 * La restricción única impide dos planes del mismo docente para el mismo
 * nivel, sección y semana. El clonado mueve la copia a la primera semana libre
 * para seguir siendo compatible con esta regla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eiplanningwks', function (Blueprint $table): void {
            // Listado del docente por semana.
            $table->index(
                ['profesor_id', 'finicial'],
                'eiplanningwks_profesor_finicial_index'
            );

            // Búsquedas por grupo (nivel + sección) y semana.
            $table->index(
                ['grado_id', 'seccion_id', 'finicial'],
                'eiplanningwks_grado_seccion_finicial_index'
            );

            // Una sola planificación por docente, nivel, sección y semana.
            $table->unique(
                ['profesor_id', 'grado_id', 'seccion_id', 'finicial'],
                'eiplanningwks_semana_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('eiplanningwks', function (Blueprint $table): void {
            $table->dropUnique('eiplanningwks_semana_unique');
            $table->dropIndex('eiplanningwks_profesor_finicial_index');
            $table->dropIndex('eiplanningwks_grado_seccion_finicial_index');
        });
    }
};
