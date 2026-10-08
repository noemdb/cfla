<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pensum (área de aprendizaje del grado) opcional en el plan especial.
 * Mismo criterio que en `eiplanningwks`/`eiplanningbwks`/`eiprojectks`:
 * conveniencia de cabecera, NULLABLE sin default, FK `nullOnDelete`, guarda
 * `hasColumn`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('eispecialks', 'pensum_id')) {
            return;
        }

        Schema::table('eispecialks', function (Blueprint $table) {
            $table->unsignedBigInteger('pensum_id')
                ->nullable()
                ->after('seccion_id')
                ->comment('Área de aprendizaje (pensum) del grado — opcional');

            $table->foreign('pensum_id', 'eispecialks_pensum_id_foreign')
                ->references('id')
                ->on('pensums')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('eispecialks', 'pensum_id')) {
            return;
        }

        Schema::table('eispecialks', function (Blueprint $table) {
            $table->dropForeign('eispecialks_pensum_id_foreign');
            $table->dropColumn('pensum_id');
        });
    }
};
