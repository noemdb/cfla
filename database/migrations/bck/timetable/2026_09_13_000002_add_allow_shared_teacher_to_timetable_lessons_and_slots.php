<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_lessons') || ! Schema::hasTable('timetable_slots')) {
            return;
        }

        if (! Schema::hasColumn('timetable_lessons', 'allow_shared_teacher')) {
            Schema::table('timetable_lessons', function (Blueprint $table): void {
                $table->boolean('allow_shared_teacher')->default(false)->after('is_half_group');
            });
        }

        if (! Schema::hasColumn('timetable_slots', 'allow_shared_teacher')) {
            Schema::table('timetable_slots', function (Blueprint $table): void {
                $table->boolean('allow_shared_teacher')->default(false)->after('is_half_group');
            });
        }

        // Reconstruye slot_teacher_key: si la lesson es de medio grupo o de
        // docente compartido, la clave incluye la lesson (permite que el docente
        // atienda dos grupos en el mismo bloque); en otro caso sigue siendo
        // única por docente.
        $teacherIndex = array_column(
            DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', ['uq_slot_teacher']),
            'Key_name'
        );
        if ($teacherIndex !== []) {
            DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_teacher');
        }

        if (Schema::hasColumn('timetable_slots', 'slot_teacher_key')) {
            DB::statement('ALTER TABLE timetable_slots DROP COLUMN slot_teacher_key');
        }

        DB::statement(
            "ALTER TABLE timetable_slots
             ADD COLUMN slot_teacher_key VARCHAR(40)
             GENERATED ALWAYS AS (
                 IF(is_half_group = 1 OR allow_shared_teacher = 1,
                    CONCAT('T', profesor_id, ':S', lesson_id),
                    CONCAT('T', profesor_id)
                 )
             ) VIRTUAL"
        );

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

        $teacherIndex = array_column(
            DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', ['uq_slot_teacher']),
            'Key_name'
        );
        if ($teacherIndex !== []) {
            DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_teacher');
        }

        if (Schema::hasColumn('timetable_slots', 'slot_teacher_key')) {
            DB::statement('ALTER TABLE timetable_slots DROP COLUMN slot_teacher_key');
        }

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

        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_teacher (calendar_id, period_id, slot_teacher_key)');

        if (Schema::hasColumn('timetable_slots', 'allow_shared_teacher')) {
            Schema::table('timetable_slots', function (Blueprint $table): void {
                $table->dropColumn('allow_shared_teacher');
            });
        }

        if (Schema::hasColumn('timetable_lessons', 'allow_shared_teacher')) {
            Schema::table('timetable_lessons', function (Blueprint $table): void {
                $table->dropColumn('allow_shared_teacher');
            });
        }
    }
};
