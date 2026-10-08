<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pensum (área de aprendizaje del grado) opcional en la cabecera de la
 * planificación semanal.
 *
 * Se ancla a la carga académica del docente: el `pevaluacion_id` de una
 * pevaluación apunta a un `pensum_id`, y ese pensum es el área (asignatura ×
 * grado). Es un campo de conveniencia para relacionar el plan con su área sin
 * depender de los resúmenes.
 *
 * ─────────────────────────────────────────────────────────────────
 * MIGRACIÓN ADITIVA — OPCIONAL POR DISEÑO
 * ─────────────────────────────────────────────────────────────────
 * NULLABLE sin default: los planes existentes quedan en NULL y siguen válidos.
 * FK con `nullOnDelete` (borrar un pensum no arrastra el plan, solo lo
 * desvincula). `Schema::hasColumn` la hace re-ejecutable.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('eiplanningwks', 'pensum_id')) {
            return;
        }

        Schema::table('eiplanningwks', function (Blueprint $table) {
            $table->unsignedBigInteger('pensum_id')
                ->nullable()
                ->after('seccion_id')
                ->comment('Área de aprendizaje (pensum) del grado — opcional');

            $table->foreign('pensum_id', 'eiplanningwks_pensum_id_foreign')
                ->references('id')
                ->on('pensums')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('eiplanningwks', 'pensum_id')) {
            return;
        }

        Schema::table('eiplanningwks', function (Blueprint $table) {
            $table->dropForeign('eiplanningwks_pensum_id_foreign');
            $table->dropColumn('pensum_id');
        });
    }
};
