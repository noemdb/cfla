<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SPEC-TIMETABLE-001 §3 + consideración "componentes de formación":
 *
 * Una sección puede dividirse en sub-grupos (GrupoEstable) que se dictan en
 * PARALELO en el mismo bloque, cada uno con su propio profesor. Para ello:
 *  - se añade `grupo_estable_id` (nullable) a timetable_slots;
 *  - se reemplaza `uq_slot_section (calendar_id, period_id, seccion_id)` por un
 *    índice sobre una columna generada `slot_section_key`, que distingue la
 *    lección de sección completa (':0') de la de un sub-grupo (':G{id}').
 *    Así dos sub-grupos distintos pueden compartir período, pero la misma
 *    unidad (sección completa o el mismo sub-grupo) no se duplica.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_slots')) {
            return;
        }

        $cols = Schema::getColumnListing('timetable_slots');

        // ---- A) Columna `grupo_estable_id` como INT UNSIGNED (mismo tipo que grupo_estables.id).
        //      La migración base (bck/, no ejecutada por Artisan) la dejaba como BIGINT UNSIGNED;
        //      un FK bigint→int lanza MySQL "errno 150: Foreign key constraint is incorrectly formed".
        //      Si la columna ya existe como bigint (legacy), se normaliza a int unsigned: es seguro
        //      porque todo valor proviene de grupo_estables.id (INT UNSIGNED) y cabe.
        if (in_array('grupo_estable_id', $cols, true)) {
            $dbName = DB::select('SELECT DATABASE() AS db')[0]?->db;
            $type = DB::select(
                "SELECT COLUMN_TYPE FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'timetable_slots' AND COLUMN_NAME = 'grupo_estable_id'",
                [$dbName]
            );
            if (str_starts_with($type[0]?->COLUMN_TYPE ?? '', 'bigint')) {
                DB::statement('ALTER TABLE timetable_slots MODIFY COLUMN grupo_estable_id INT UNSIGNED NULL DEFAULT NULL');
            }
        } else {
            Schema::table('timetable_slots', function (Blueprint $table) {
                $table->unsignedInteger('grupo_estable_id')->nullable()->after('seccion_id');
            });
        }

        // ---- B) FK → grupo_estables (idempotente: solo si no existe ya un FK hacia esa tabla).
        $ddl = DB::select('SHOW CREATE TABLE timetable_slots')[0]?->{'Create Table'} ?? '';
        if (! str_contains($ddl, 'REFERENCES `grupo_estables`')) {
            DB::statement('ALTER TABLE timetable_slots
                ADD CONSTRAINT timetable_slots_grupo_estable_id_foreign
                FOREIGN KEY (grupo_estable_id) REFERENCES grupo_estables (id) ON DELETE SET NULL');
        }

        if (! in_array('slot_section_key', $cols, true)) {
            DB::statement("ALTER TABLE timetable_slots
                ADD COLUMN slot_section_key VARCHAR(30)
                GENERATED ALWAYS AS (
                    IF(grupo_estable_id IS NULL, CONCAT('S', seccion_id, ':0'), CONCAT('S', seccion_id, ':G', grupo_estable_id))
                ) VIRTUAL AFTER grupo_estable_id");
        }

        // Reemplaza el índice único de sección por el que considera el sub-grupo.
        if (in_array('seccion_id', $cols, true)) {
            $indexes = array_column(DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', ['uq_slot_section']), 'Key_name');
            if ($indexes !== []) {
                DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_section');
            }
            DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_section (calendar_id, period_id, slot_section_key)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_slots')) {
            return;
        }

        $cols = Schema::getColumnListing('timetable_slots');

        $indexes = array_column(DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', ['uq_slot_section']), 'Key_name');
        if ($indexes !== []) {
            DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_section');
        }

        if (in_array('slot_section_key', $cols, true)) {
            DB::statement('ALTER TABLE timetable_slots DROP COLUMN slot_section_key');
        }

        if (in_array('seccion_id', $cols, true)) {
            DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_section (calendar_id, period_id, seccion_id)');
        }

        if (in_array('grupo_estable_id', $cols, true)) {
            Schema::table('timetable_slots', function (Blueprint $table) {
                $table->dropForeign(['grupo_estable_id']);
                $table->dropColumn('grupo_estable_id');
            });
        }
    }
};
