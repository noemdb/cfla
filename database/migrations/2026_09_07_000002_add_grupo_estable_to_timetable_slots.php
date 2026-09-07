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

        if (! in_array('grupo_estable_id', $cols, true)) {
            Schema::table('timetable_slots', function (Blueprint $table) {
                $table->unsignedBigInteger('grupo_estable_id')->nullable()->after('seccion_id');
                $table->foreign('grupo_estable_id')->references('id')->on('grupo_estables')->onDelete('set null');
            });
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
