<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La disponibilidad del docente se registra por TURNO · DÍA · BLOQUE (independiente
 * del pestudio), con la rejilla de 60 min definida por el seeder de turnos
 * (M 07:00–13:00 → 6 bloques, T 13:00–15:00 → 2 bloques).
 *
 * Se reemplaza `period_id` por `shift_id + day_of_week + order_in_day` (+ hora
 * del bloque). La tabla está vacía en línea base, por eso es seguro.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_teacher_availability')) {
            return;
        }

        $cols = Schema::getColumnListing('timetable_teacher_availability');

        // 0) Índice de respaldo para la FK de calendar_id (error 1553 al soltar uq_avail).
        $idxCal = array_column(DB::select('SHOW INDEX FROM timetable_teacher_availability WHERE Key_name = ?', ['idx_avail_cal']), 'Key_name');
        if ($idxCal === []) {
            DB::statement('ALTER TABLE timetable_teacher_availability ADD INDEX idx_avail_cal (calendar_id)');
        }

        // 1) Soltar el único antiguo (calendar, profesor, period) y el índice del period.
        $uq = array_column(DB::select('SHOW INDEX FROM timetable_teacher_availability WHERE Key_name = ?', ['uq_avail']), 'Key_name');
        if ($uq !== []) {
            DB::statement('ALTER TABLE timetable_teacher_availability DROP INDEX uq_avail');
        }

        // MySQL does not allow dropping an index that backs a foreign key.
        $periodForeignKeys = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['timetable_teacher_availability', 'period_id']
        );
        foreach ($periodForeignKeys as $foreignKey) {
            DB::statement(sprintf(
                'ALTER TABLE timetable_teacher_availability DROP FOREIGN KEY `%s`',
                str_replace('`', '``', $foreignKey->CONSTRAINT_NAME)
            ));
        }

        $pk = array_column(DB::select('SHOW INDEX FROM timetable_teacher_availability WHERE Key_name = ?', ['timetable_teacher_availability_period_id_foreign']), 'Key_name');
        if ($pk !== []) {
            DB::statement('ALTER TABLE timetable_teacher_availability DROP INDEX timetable_teacher_availability_period_id_foreign');
        }

        // 2) Quitar period_id.
        if (in_array('period_id', $cols, true)) {
            DB::statement('ALTER TABLE timetable_teacher_availability DROP COLUMN period_id');
        }

        // 3) Añadir turno/día/bloque (+ hora del bloque).
        $cols = Schema::getColumnListing('timetable_teacher_availability');
        if (! in_array('shift_id', $cols, true)) {
            DB::statement('ALTER TABLE timetable_teacher_availability ADD COLUMN shift_id BIGINT UNSIGNED NULL AFTER profesor_id');
        }
        if (! in_array('day_of_week', $cols, true)) {
            DB::statement('ALTER TABLE timetable_teacher_availability ADD COLUMN day_of_week TINYINT UNSIGNED NULL AFTER shift_id');
        }
        if (! in_array('order_in_day', $cols, true)) {
            DB::statement('ALTER TABLE timetable_teacher_availability ADD COLUMN order_in_day TINYINT UNSIGNED NULL AFTER day_of_week');
        }
        if (! in_array('start_time', $cols, true)) {
            DB::statement('ALTER TABLE timetable_teacher_availability ADD COLUMN start_time TIME NULL AFTER order_in_day');
        }
        if (! in_array('end_time', $cols, true)) {
            DB::statement('ALTER TABLE timetable_teacher_availability ADD COLUMN end_time TIME NULL AFTER start_time');
        }

        // 4) Índice único nuevo.
        DB::statement('ALTER TABLE timetable_teacher_availability
            ADD UNIQUE INDEX uq_avail (calendar_id, profesor_id, shift_id, day_of_week, order_in_day)');

        // 5) FK a timetable_shifts (idempotente).
        $ddl = DB::select('SHOW CREATE TABLE timetable_teacher_availability')[0]->{'Create Table'} ?? '';
        if (! str_contains($ddl, 'REFERENCES `timetable_shifts`')) {
            DB::statement('ALTER TABLE timetable_teacher_availability
                ADD CONSTRAINT timetable_teacher_availability_shift_id_foreign
                FOREIGN KEY (shift_id) REFERENCES timetable_shifts (id) ON DELETE CASCADE');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_teacher_availability')) {
            return;
        }

        $shiftForeignKeys = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['timetable_teacher_availability', 'shift_id']
        );
        foreach ($shiftForeignKeys as $foreignKey) {
            DB::statement(sprintf(
                'ALTER TABLE timetable_teacher_availability DROP FOREIGN KEY `%s`',
                str_replace('`', '``', $foreignKey->CONSTRAINT_NAME)
            ));
        }

        DB::statement('ALTER TABLE timetable_teacher_availability DROP INDEX uq_avail');
        DB::statement('ALTER TABLE timetable_teacher_availability DROP COLUMN shift_id, DROP COLUMN day_of_week, DROP COLUMN order_in_day, DROP COLUMN start_time, DROP COLUMN end_time');
        DB::statement('ALTER TABLE timetable_teacher_availability ADD COLUMN period_id BIGINT UNSIGNED NULL AFTER profesor_id');
        DB::statement('ALTER TABLE timetable_teacher_availability ADD UNIQUE INDEX uq_avail (calendar_id, profesor_id, period_id)');
    }
};
