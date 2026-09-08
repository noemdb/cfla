<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Relaja la regla "una sección → un aula" (índice único uq_room_seccion de
 * 2026_09_07_000003) a 1:N: una sección puede tener varias aulas vinculadas.
 *
 * Se crea primero un índice normal idx_room_seccion para respaldar la FK
 * fk_room_seccion (MariaDB no permite dejar la columna sin índice) y luego se
 * elimina el único. Los NULL de las aulas genéricas siguen sin colisionar
 * (ya no hay índice que colisione).
 *
 * Idempotente por índice. down(): solo recrea uq_room_seccion si no existen
 * duplicados de seccion_id (no se puede imponer unicidad retroactivamente).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_rooms')) {
            return;
        }

        if (DB::select('SHOW INDEX FROM timetable_rooms WHERE Key_name = ?', ['idx_room_seccion']) === []) {
            DB::statement('ALTER TABLE timetable_rooms ADD INDEX idx_room_seccion (seccion_id)');
        }

        if (DB::select('SHOW INDEX FROM timetable_rooms WHERE Key_name = ?', ['uq_room_seccion']) !== []) {
            DB::statement('ALTER TABLE timetable_rooms DROP INDEX uq_room_seccion');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_rooms')) {
            return;
        }

        if (DB::select('SHOW INDEX FROM timetable_rooms WHERE Key_name = ?', ['uq_room_seccion']) === []) {
            $duplicados = DB::select(
                'SELECT seccion_id FROM timetable_rooms
                    WHERE seccion_id IS NOT NULL
                    GROUP BY seccion_id HAVING COUNT(*) > 1 LIMIT 1'
            );

            if ($duplicados === []) {
                DB::statement('ALTER TABLE timetable_rooms ADD UNIQUE INDEX uq_room_seccion (seccion_id)');
            }
        }

        if (DB::select('SHOW INDEX FROM timetable_rooms WHERE Key_name = ?', ['idx_room_seccion']) !== []) {
            DB::statement('ALTER TABLE timetable_rooms DROP INDEX idx_room_seccion');
        }
    }
};
