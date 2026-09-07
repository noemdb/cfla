<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SPEC-TIMETABLE-001 §3 — El log de conflictos registra también las lecciones
 * que el solver no pudo asignar (type 'unassigned'). Para ello se añaden
 * lesson_id/period_id (nullable) y el nuevo valor del enum.
 *
 * La migración base de timetable está archivada en database/migrations/bck/;
 * este archivo la ajusta in situ (idempotente y reversible).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_conflicts')) {
            return;
        }

        $cols = Schema::getColumnListing('timetable_conflicts');

        Schema::table('timetable_conflicts', function (Blueprint $table) use ($cols) {
            if (! in_array('lesson_id', $cols, true)) {
                $table->unsignedBigInteger('lesson_id')->nullable()->after('slot_id');
                $table->foreign('lesson_id')->references('id')->on('timetable_lessons')->onDelete('set null');
            }

            if (! in_array('period_id', $cols, true)) {
                $table->unsignedBigInteger('period_id')->nullable()->after('lesson_id');
                $table->foreign('period_id')->references('id')->on('timetable_periods')->onDelete('set null');
            }
        });

        // MariaDB/MySQL: ampliar el enum con 'unassigned' (sin doctrine/dbal).
        DB::statement("ALTER TABLE timetable_conflicts
            MODIFY type ENUM('teacher_double_booked','room_double_booked','section_double_booked',
            'availability_violation','shift_mismatch','unassigned') NOT NULL");
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_conflicts')) {
            return;
        }

        DB::statement("ALTER TABLE timetable_conflicts
            MODIFY type ENUM('teacher_double_booked','room_double_booked','section_double_booked',
            'availability_violation','shift_mismatch') NOT NULL");

        Schema::table('timetable_conflicts', function (Blueprint $table) {
            $table->dropForeign(['lesson_id']);
            $table->dropForeign(['period_id']);
            $table->dropColumn(['lesson_id', 'period_id']);
        });
    }
};
