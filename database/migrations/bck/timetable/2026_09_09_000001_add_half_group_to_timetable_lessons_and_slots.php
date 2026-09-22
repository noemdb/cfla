<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('timetable_lessons', 'is_half_group')) {
            Schema::table('timetable_lessons', function (Blueprint $table): void {
                $table->boolean('is_half_group')->default(false)->after('room_type_required');
            });
        }

        if (! Schema::hasColumn('timetable_slots', 'is_half_group')) {
            Schema::table('timetable_slots', function (Blueprint $table): void {
                $table->boolean('is_half_group')->default(false)->after('grupo_estable_id');
            });
        }

        if (Schema::hasColumn('timetable_slots', 'slot_section_key')) {
            DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_section');
            DB::statement('ALTER TABLE timetable_slots DROP COLUMN slot_section_key');
        }

        DB::statement(
            "ALTER TABLE timetable_slots
             ADD COLUMN slot_section_key VARCHAR(30)
             GENERATED ALWAYS AS (
                 IF(is_half_group = 1,
                    CONCAT('S', seccion_id, ':H', lesson_id),
                    IF(grupo_estable_id IS NULL,
                       CONCAT('S', seccion_id, ':0'),
                       CONCAT('S', seccion_id, ':G', grupo_estable_id)
                    )
                 )
             ) VIRTUAL"
        );
        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_section (calendar_id, period_id, slot_section_key)');
    }

    public function down(): void
    {
        if (Schema::hasColumn('timetable_slots', 'slot_section_key')) {
            DB::statement('ALTER TABLE timetable_slots DROP INDEX uq_slot_section');
            DB::statement('ALTER TABLE timetable_slots DROP COLUMN slot_section_key');
        }

        DB::statement(
            "ALTER TABLE timetable_slots
             ADD COLUMN slot_section_key VARCHAR(30)
             GENERATED ALWAYS (
                 IF(grupo_estable_id IS NULL,
                    CONCAT('S', seccion_id, ':0'),
                    CONCAT('S', seccion_id, ':G', grupo_estable_id)
                 )
             ) VIRTUAL"
        );
        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_section (calendar_id, period_id, slot_section_key)');

        if (Schema::hasColumn('timetable_slots', 'is_half_group')) {
            Schema::table('timetable_slots', fn (Blueprint $table) => $table->dropColumn('is_half_group'));
        }

        if (Schema::hasColumn('timetable_lessons', 'is_half_group')) {
            Schema::table('timetable_lessons', fn (Blueprint $table) => $table->dropColumn('is_half_group'));
        }
    }
};
