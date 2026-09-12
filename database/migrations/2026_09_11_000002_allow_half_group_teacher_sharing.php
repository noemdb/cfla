<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_slots') || ! Schema::hasColumn('timetable_slots', 'is_half_group')) {
            return;
        }

        $indexes = array_column(
            DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', ['uq_slot_teacher']),
            'Key_name'
        );
        if ($indexes !== []) {
            DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_teacher');
        }

        if (! Schema::hasColumn('timetable_slots', 'slot_teacher_key')) {
            DB::statement(
                "ALTER TABLE timetable_slots
                 ADD COLUMN slot_teacher_key VARCHAR(40)
                 GENERATED ALWAYS AS (
                     IF(is_half_group = 1,
                        CONCAT('T', profesor_id, ':H', lesson_id),
                        CONCAT('T', profesor_id)
                     )
                 ) VIRTUAL"
            );
        }

        $teacherIndex = array_column(
            DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', ['uq_slot_teacher']),
            'Key_name'
        );
        if ($teacherIndex === []) {
            DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_teacher (calendar_id, period_id, slot_teacher_key)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_slots')) {
            return;
        }

        $indexes = array_column(
            DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', ['uq_slot_teacher']),
            'Key_name'
        );
        if ($indexes !== []) {
            DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_teacher');
        }

        if (Schema::hasColumn('timetable_slots', 'slot_teacher_key')) {
            DB::statement('ALTER TABLE timetable_slots DROP COLUMN slot_teacher_key');
        }

        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_teacher (calendar_id, period_id, profesor_id)');
    }
};
