<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La asociación a pestudio se mueve del PERÍODO al CALENDARIO:
 *  - `timetable_calendars` gana `pestudio_id` (cada calendario = un plan de estudio).
 *  - `timetable_periods` pierde `pestudio_id` (los períodos heredan el pestudio del calendario).
 */
return new class extends Migration
{
    public function up(): void
    {
        // A) timetable_calendars.pestudio_id
        if (Schema::hasTable('timetable_calendars')) {
            $cols = Schema::getColumnListing('timetable_calendars');
            if (! in_array('pestudio_id', $cols, true)) {
                DB::statement('ALTER TABLE timetable_calendars ADD COLUMN pestudio_id INT UNSIGNED NULL AFTER pescolar_id');
            }
            $ddl = DB::select('SHOW CREATE TABLE timetable_calendars')[0]->{'Create Table'} ?? '';
            if (! str_contains($ddl, 'REFERENCES `pestudios`')) {
                DB::statement('ALTER TABLE timetable_calendars
                    ADD CONSTRAINT timetable_calendars_pestudio_id_foreign
                    FOREIGN KEY (pestudio_id) REFERENCES pestudios (id) ON DELETE CASCADE');
            }
        }

        // B) timetable_periods: quitar pestudio_id y rehacer el único sin él.
        if (Schema::hasTable('timetable_periods')) {
            $uq = array_column(DB::select('SHOW INDEX FROM timetable_periods WHERE Key_name = ?', ['uq_period']), 'Key_name');
            if ($uq !== []) {
                DB::statement('ALTER TABLE timetable_periods DROP INDEX uq_period');
            }

            $cols = Schema::getColumnListing('timetable_periods');
            if (in_array('pestudio_id', $cols, true)) {
                $fk = array_column(DB::select('SHOW INDEX FROM timetable_periods WHERE Key_name = ?', ['timetable_periods_pestudio_id_foreign']), 'Key_name');
                if ($fk !== []) {
                    DB::statement('ALTER TABLE timetable_periods DROP FOREIGN KEY timetable_periods_pestudio_id_foreign');
                    DB::statement('ALTER TABLE timetable_periods DROP INDEX timetable_periods_pestudio_id_foreign');
                }
                DB::statement('ALTER TABLE timetable_periods DROP COLUMN pestudio_id');
            }

            $uq2 = array_column(DB::select('SHOW INDEX FROM timetable_periods WHERE Key_name = ?', ['uq_period']), 'Key_name');
            if ($uq2 === []) {
                DB::statement('ALTER TABLE timetable_periods
                    ADD UNIQUE INDEX uq_period (calendar_id, shift_id, day_of_week, order_in_day)');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('timetable_calendars')) {
            Schema::table('timetable_calendars', function ($table) {
                $table->dropForeign(['pestudio_id']);
                $table->dropColumn('pestudio_id');
            });
        }

        if (Schema::hasTable('timetable_periods')) {
            DB::statement('ALTER TABLE timetable_periods DROP INDEX uq_period');
            DB::statement('ALTER TABLE timetable_periods ADD COLUMN pestudio_id INT UNSIGNED NULL AFTER shift_id');
            DB::statement('ALTER TABLE timetable_periods
                ADD UNIQUE INDEX uq_period (calendar_id, shift_id, day_of_week, order_in_day, pestudio_id)');
        }
    }
};
