<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `timetable_periods` ahora se asocia a pestudio: MEDIA GENERAL y CIENCIA Y
 * TECNOLOGÍA comparten la estructura del nivel MEDIA, por lo que generan
 * períodos idénticos. El índice único `uq_period` debe incluir `pestudio_id`.
 *
 * OJO: `uq_period` respalda la FK de `calendar_id` (error 1553 al soltarlo); se
 * añade primero `idx_period_cal` sobre calendar_id, luego se rehace el único.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_periods')) {
            return;
        }

        // 1) Índice de respaldo para la FK de calendar_id.
        $idx = array_column(DB::select('SHOW INDEX FROM timetable_periods WHERE Key_name = ?', ['idx_period_cal']), 'Key_name');
        if ($idx === []) {
            DB::statement('ALTER TABLE timetable_periods ADD INDEX idx_period_cal (calendar_id)');
        }

        // 2) Quitar el único antiguo (ya hay índice en calendar_id).
        $uq = array_column(DB::select('SHOW INDEX FROM timetable_periods WHERE Key_name = ?', ['uq_period']), 'Key_name');
        if ($uq !== []) {
            DB::statement('ALTER TABLE timetable_periods DROP INDEX uq_period');
        }

        // 3) Único nuevo incluyendo pestudio_id.
        $uq2 = array_column(DB::select('SHOW INDEX FROM timetable_periods WHERE Key_name = ?', ['uq_period']), 'Key_name');
        if ($uq2 === []) {
            DB::statement('ALTER TABLE timetable_periods
                ADD UNIQUE INDEX uq_period (calendar_id, shift_id, day_of_week, order_in_day, pestudio_id)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_periods')) {
            return;
        }

        DB::statement('ALTER TABLE timetable_periods DROP INDEX uq_period');
        DB::statement('ALTER TABLE timetable_periods ADD UNIQUE INDEX uq_period (calendar_id, shift_id, day_of_week, order_in_day)');
    }
};
