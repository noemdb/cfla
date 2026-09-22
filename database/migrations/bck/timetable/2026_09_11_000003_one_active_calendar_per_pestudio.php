<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_calendars')) {
            return;
        }

        $indexes = array_column(
            DB::select('SHOW INDEX FROM timetable_calendars'),
            'Key_name',
        );
        if (in_array('uq_active_lapso', $indexes, true)) {
            DB::statement('ALTER TABLE timetable_calendars DROP INDEX uq_active_lapso');
        }

        if (Schema::hasColumn('timetable_calendars', 'active_lapso_key')) {
            DB::statement('ALTER TABLE timetable_calendars DROP COLUMN active_lapso_key');
        }

        if (! Schema::hasColumn('timetable_calendars', 'active_pestudio_key')) {
            DB::statement("ALTER TABLE timetable_calendars
                ADD COLUMN active_pestudio_key VARCHAR(24)
                GENERATED ALWAYS AS (
                    IF(status = 'active' AND pestudio_id IS NOT NULL,
                        CONCAT('P', pestudio_id),
                        NULL
                    )
                ) VIRTUAL");
        }

        $indexes = array_column(
            DB::select('SHOW INDEX FROM timetable_calendars'),
            'Key_name',
        );
        if (! in_array('uq_active_pestudio', $indexes, true)) {
            DB::statement('ALTER TABLE timetable_calendars
                ADD UNIQUE INDEX uq_active_pestudio (active_pestudio_key)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_calendars')) {
            return;
        }

        $indexes = array_column(
            DB::select('SHOW INDEX FROM timetable_calendars'),
            'Key_name',
        );
        if (in_array('uq_active_pestudio', $indexes, true)) {
            DB::statement('ALTER TABLE timetable_calendars DROP INDEX uq_active_pestudio');
        }

        if (Schema::hasColumn('timetable_calendars', 'active_pestudio_key')) {
            DB::statement('ALTER TABLE timetable_calendars DROP COLUMN active_pestudio_key');
        }

        if (! Schema::hasColumn('timetable_calendars', 'active_lapso_key')) {
            DB::statement("ALTER TABLE timetable_calendars
                ADD COLUMN active_lapso_key VARCHAR(20)
                GENERATED ALWAYS AS (
                    IF(status = 'active', CONCAT('L', lapso_id), NULL)
                ) VIRTUAL");
        }

        $indexes = array_column(
            DB::select('SHOW INDEX FROM timetable_calendars'),
            'Key_name',
        );
        if (! in_array('uq_active_lapso', $indexes, true)) {
            DB::statement('ALTER TABLE timetable_calendars
                ADD UNIQUE INDEX uq_active_lapso (active_lapso_key)');
        }
    }
};
