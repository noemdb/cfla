<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Asocia cada aula/salón/ambiente a una sección (grado/sección) para garantizar
 * la regla "uno y solo un aula por grado/sección" (índice único). La columna es
 * nullable: las aulas genéricas (laboratorios, patios...) no se asocian a una
 * sección y los NULL no colisionan en el índice único.
 *
 * Idempotente por columna/FK/índice (la columna, la FK y el índice se crean por
 * separado para tolerar ejecuciones interrumpidas).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_rooms')) {
            return;
        }

        if (! Schema::hasColumn('timetable_rooms', 'seccion_id')) {
            Schema::table('timetable_rooms', function (Blueprint $table) {
                // seccions.id es int unsigned → debe coincidir para la FK.
                $table->unsignedInteger('seccion_id')->nullable()->after('id');
            });
        }

        $fk = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'timetable_rooms' AND COLUMN_NAME = 'seccion_id'");
        if ($fk === []) {
            DB::statement('ALTER TABLE timetable_rooms ADD CONSTRAINT fk_room_seccion FOREIGN KEY (seccion_id) REFERENCES seccions(id) ON DELETE SET NULL');
        }

        $idx = DB::select('SHOW INDEX FROM timetable_rooms WHERE Key_name = ?', ['uq_room_seccion']);
        if ($idx === []) {
            DB::statement('ALTER TABLE timetable_rooms ADD UNIQUE INDEX uq_room_seccion (seccion_id)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_rooms')) {
            return;
        }

        if (DB::select('SHOW INDEX FROM timetable_rooms WHERE Key_name = ?', ['uq_room_seccion']) !== []) {
            DB::statement('ALTER TABLE timetable_rooms DROP INDEX uq_room_seccion');
        }

        $fk = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'timetable_rooms' AND COLUMN_NAME = 'seccion_id'");
        if ($fk !== []) {
            Schema::table('timetable_rooms', function (Blueprint $table) {
                $table->dropForeign(['seccion_id']);
            });
        }

        if (Schema::hasColumn('timetable_rooms', 'seccion_id')) {
            Schema::table('timetable_rooms', function (Blueprint $table) {
                $table->dropColumn('seccion_id');
            });
        }
    }
};
