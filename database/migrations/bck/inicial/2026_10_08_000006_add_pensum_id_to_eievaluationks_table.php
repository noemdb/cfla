<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pensum (área de aprendizaje del grado) opcional en el plan de evaluación.
 * Mismo criterio que en `eiplanningwks`/`eiplanningbwks`/`eiprojectks`/
 * `eispecialks`: conveniencia de cabecera, NULLABLE sin default, FK
 * `nullOnDelete`, guarda `hasColumn`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('eievaluationks', 'pensum_id')) {
            return;
        }

        Schema::table('eievaluationks', function (Blueprint $table) {
            $table->unsignedBigInteger('pensum_id')
                ->nullable()
                ->after('seccion_id')
                ->comment('Área de aprendizaje (pensum) del grado — opcional');

            $table->foreign('pensum_id', 'eievaluationks_pensum_id_foreign')
                ->references('id')
                ->on('pensums')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('eievaluationks', 'pensum_id')) {
            return;
        }

        Schema::table('eievaluationks', function (Blueprint $table) {
            $table->dropForeign('eievaluationks_pensum_id_foreign');
            $table->dropColumn('pensum_id');
        });
    }
};
