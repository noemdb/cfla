<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Los períodos del horario deben estar asociados al PLAN DE ESTUDIO (pestudio):
 * cada pestudio tiene su propia estructura de bloques (del legacy), de modo que
 * un calendario guarda períodos por cada pestudio que cubre.
 *
 * Añade `pestudio_id` (nullable, FK → pestudios) a timetable_periods. Idempotente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_periods')) {
            return;
        }

        $cols = Schema::getColumnListing('timetable_periods');
        if (in_array('pestudio_id', $cols, true)) {
            return;
        }

        Schema::table('timetable_periods', function (Blueprint $table) {
            $table->unsignedInteger('pestudio_id')->nullable()->after('shift_id');
        });

        // FK idempotente (igual patrón que las otras migraciones de ajuste).
        $ddl = DB::select('SHOW CREATE TABLE timetable_periods')[0]->{'Create Table'} ?? '';
        if (! str_contains($ddl, 'REFERENCES `pestudios`')) {
            DB::statement('ALTER TABLE timetable_periods
                ADD CONSTRAINT timetable_periods_pestudio_id_foreign
                FOREIGN KEY (pestudio_id) REFERENCES pestudios (id) ON DELETE CASCADE');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_periods')) {
            return;
        }

        Schema::table('timetable_periods', function (Blueprint $table) {
            $table->dropForeign(['pestudio_id']);
            $table->dropColumn('pestudio_id');
        });
    }
};
