<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ADR-TT-017 — Mitad de bloque (half_block).
 *
 * - timetable_lessons.half_block: marca la lección como unidades de media franja.
 * - timetable_slots.half: 0 = superior, 1 = inferior, NULL = bloque completo.
 *
 * Las claves generadas de sección y docente incorporan el sufijo de mitad para
 * que dos mitades secuenciales del mismo período no colisionen; `uq_slot_room`
 * incluye `slot_half_key` (auxiliar no-nulo) por el mismo motivo. La regla
 * cruzada "bloque completo excluye mitades" se refuerza en aplicación.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_lessons') || ! Schema::hasTable('timetable_slots')) {
            return;
        }

        if (! Schema::hasColumn('timetable_lessons', 'half_block')) {
            Schema::table('timetable_lessons', function (Blueprint $table): void {
                $table->boolean('half_block')->default(false)->after('allow_shared_teacher');
            });
        }

        if (! Schema::hasColumn('timetable_slots', 'half')) {
            Schema::table('timetable_slots', function (Blueprint $table): void {
                $table->unsignedTinyInteger('half')->nullable()->after('locked');
            });
        }

        foreach (['uq_slot_room', 'uq_slot_section', 'uq_slot_teacher'] as $index) {
            $this->dropIndexIfExists($index);
        }

        foreach (['slot_half_key', 'slot_section_key', 'slot_teacher_key'] as $column) {
            if (Schema::hasColumn('timetable_slots', $column)) {
                DB::statement("ALTER TABLE timetable_slots DROP COLUMN $column");
            }
        }

        DB::statement(
            "ALTER TABLE timetable_slots
             ADD COLUMN slot_half_key CHAR(2)
             GENERATED ALWAYS AS (
                 IF(half IS NULL, 'F', CONCAT('B', half))
             ) VIRTUAL"
        );

        DB::statement(
            "ALTER TABLE timetable_slots
             ADD COLUMN slot_section_key VARCHAR(48)
             GENERATED ALWAYS AS (
                 CONCAT(
                     IF(is_half_group = 1,
                        CONCAT('S', seccion_id, ':H', lesson_id),
                        IF(grupo_estable_id IS NULL,
                           CONCAT('S', seccion_id, ':0'),
                           CONCAT('S', seccion_id, ':G', grupo_estable_id)
                        )
                     ),
                     ':',
                     IF(half IS NULL, 'F', CONCAT('B', half))
                 )
             ) VIRTUAL"
        );

        DB::statement(
            "ALTER TABLE timetable_slots
             ADD COLUMN slot_teacher_key VARCHAR(48)
             GENERATED ALWAYS AS (
                 CONCAT(
                     IF(is_half_group = 1 OR allow_shared_teacher = 1,
                        CONCAT('T', profesor_id, ':S', lesson_id),
                        CONCAT('T', profesor_id)
                     ),
                     ':',
                     IF(half IS NULL, 'F', CONCAT('B', half))
                 )
             ) VIRTUAL"
        );

        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_section (calendar_id, period_id, slot_section_key)');
        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_teacher (calendar_id, period_id, slot_teacher_key)');
        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_room (calendar_id, period_id, room_id, slot_half_key)');
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_slots')) {
            return;
        }

        foreach (['uq_slot_room', 'uq_slot_section', 'uq_slot_teacher'] as $index) {
            $this->dropIndexIfExists($index);
        }

        foreach (['slot_half_key', 'slot_section_key', 'slot_teacher_key'] as $column) {
            if (Schema::hasColumn('timetable_slots', $column)) {
                DB::statement("ALTER TABLE timetable_slots DROP COLUMN $column");
            }
        }

        // Restaura las claves previas (sin dimensión de mitad).
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

        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_section (calendar_id, period_id, slot_section_key)');
        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_teacher (calendar_id, period_id, slot_teacher_key)');
        DB::statement('ALTER TABLE timetable_slots ADD UNIQUE INDEX uq_slot_room (calendar_id, period_id, room_id)');

        if (Schema::hasColumn('timetable_slots', 'half')) {
            Schema::table('timetable_slots', fn (Blueprint $table) => $table->dropColumn('half'));
        }

        if (Schema::hasColumn('timetable_lessons', 'half_block')) {
            Schema::table('timetable_lessons', fn (Blueprint $table) => $table->dropColumn('half_block'));
        }
    }

    private function dropIndexIfExists(string $name): void
    {
        $exists = array_column(
            DB::select('SHOW INDEX FROM timetable_slots WHERE Key_name = ?', [$name]),
            'Key_name'
        );

        if ($exists !== []) {
            DB::statement("ALTER TABLE timetable_slots DROP INDEX $name");
        }
    }
};
