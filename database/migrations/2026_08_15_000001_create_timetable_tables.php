<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SPEC-TIMETABLE-001 §3. Tablas del módulo de horarios (turnos mañana/tarde).
 * Migración única: estructura + asignación + operación (ausencias/suplencias).
 *
 * Idempotente (create-if-not-exists por tabla): permite convivir con tablas
 * preexistentes creadas fuera del sistema de migraciones (ej. sesión paralela),
 * sin drop ni pérdida de datos. Advertencia: las FKs que referencien una tabla
 * que ya existía previamente no se recrean (asume esquema compatible).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── Turnos ─────────────────────────────────────────────────────────
        if (! Schema::hasTable('timetable_shifts')) {
            Schema::create('timetable_shifts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->enum('code', ['M', 'T'])->unique();
                $table->string('name', 20);
                $table->time('start_time');
                $table->time('end_time');
                $table->timestamps();
            });
        }

        // ─── Calendarios (varios por lapso; máximo UNO activo, ADR-TT-014) ──
        if (! Schema::hasTable('timetable_calendars')) {
            Schema::create('timetable_calendars', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('lapso_id');   // lapsos.id es int unsigned
                $table->unsignedInteger('pescolar_id')->nullable(); // pescolars.id int unsigned
                $table->string('name', 120);
                $table->enum('status', ['draft', 'generating', 'active', 'archived'])->default('draft');
                // PLAN-TIMETABLE-002: columna generada que solo aporta clave si
                // status='active' → N borradores/alternativas por lapso, máximo
                // UNO activo (los NULL no colisionan en índice único).
                $table->string('active_lapso_key', 20)
                    ->storedAs("IF(status = 'active', CONCAT('L', lapso_id), NULL)");
                $table->unsignedSmallInteger('period_minutes')->default(45);
                $table->unsignedInteger('version')->default(0);          // §15 bloqueo optimista
                $table->decimal('quality_score', 8, 2)->nullable();      // §6.2
                $table->json('preview_payload')->nullable();             // §6.3 dry-run
                $table->unsignedBigInteger('active_job_id')->nullable(); // §15
                $table->timestamps();

                $table->index('lapso_id', 'idx_cal_lapso');              // backing de la FK
                $table->unique('active_lapso_key', 'uq_active_lapso');
                $table->foreign('lapso_id')->references('id')->on('lapsos')->onDelete('cascade');
                $table->foreign('pescolar_id')->references('id')->on('pescolars')->onDelete('cascade');
            });
        }

        // ─── Períodos del día (por turno y día de la semana) ───────────────
        if (! Schema::hasTable('timetable_periods')) {
            Schema::create('timetable_periods', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('calendar_id');
                $table->unsignedBigInteger('shift_id');
                $table->unsignedTinyInteger('day_of_week'); // 1=Lu … 5=Vi
                $table->unsignedTinyInteger('order_in_day');
                $table->time('start_time');
                $table->time('end_time');
                $table->boolean('is_break')->default(false);

                $table->unique(['calendar_id', 'shift_id', 'day_of_week', 'order_in_day'], 'uq_period');
                $table->foreign('calendar_id')->references('id')->on('timetable_calendars')->onDelete('cascade');
                $table->foreign('shift_id')->references('id')->on('timetable_shifts');
            });
        }

        // ─── Aulas ──────────────────────────────────────────────────────────
        if (! Schema::hasTable('timetable_rooms')) {
            Schema::create('timetable_rooms', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code', 20);
                $table->string('name', 120);
                $table->unsignedSmallInteger('capacity')->nullable();
                $table->enum('type', ['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'])->default('aula');
                $table->json('features')->nullable();
                $table->boolean('status_active')->default(true);

                $table->unique('code', 'uq_room_code');
            });
        }

        // ─── Lecciones: envoltura 1:1 de pevaluacion (ADR-TT-001) ──────────
        if (! Schema::hasTable('timetable_lessons')) {
            Schema::create('timetable_lessons', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('calendar_id');
                $table->unsignedBigInteger('pevaluacion_id');
                $table->unsignedBigInteger('shift_id');
                $table->unsignedTinyInteger('weekly_blocks_t')->default(0);
                $table->unsignedTinyInteger('weekly_blocks_p')->default(0);
                $table->enum('room_type_required', ['aula', 'laboratorio', 'patio', 'cancha', 'taller', 'salon'])->nullable();
                $table->unsignedTinyInteger('priority')->default(0);
                $table->boolean('locked')->default(false);

                $table->unique(['calendar_id', 'pevaluacion_id'], 'uq_lesson_pevaluacion');
                $table->foreign('calendar_id')->references('id')->on('timetable_calendars')->onDelete('cascade');
                $table->foreign('pevaluacion_id')->references('id')->on('pevaluacions')->onDelete('cascade');
                $table->foreign('shift_id')->references('id')->on('timetable_shifts');
            });
        }

        // ─── Disponibilidad de docentes (grilla día × período) ──────────────
        if (! Schema::hasTable('timetable_teacher_availability')) {
            Schema::create('timetable_teacher_availability', function (Blueprint $table) {
$table->bigIncrements('id');
            $table->unsignedBigInteger('calendar_id');
            $table->unsignedInteger('profesor_id');   // profesors.id es int unsigned
            $table->unsignedBigInteger('period_id');
            $table->boolean('is_available')->default(true);

            $table->unique(['calendar_id', 'profesor_id', 'period_id'], 'uq_avail');
                $table->foreign('calendar_id')->references('id')->on('timetable_calendars')->onDelete('cascade');
                $table->foreign('profesor_id')->references('id')->on('profesors')->onDelete('cascade');
                $table->foreign('period_id')->references('id')->on('timetable_periods')->onDelete('cascade');
            });
        }

        // ─── Slots: resultado de la asignación (ADR-TT-002) ─────────────────
        if (! Schema::hasTable('timetable_slots')) {
            Schema::create('timetable_slots', function (Blueprint $table) {
$table->bigIncrements('id');
            $table->unsignedBigInteger('calendar_id');
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('period_id');
            $table->unsignedInteger('profesor_id');   // profesors.id int unsigned (desnormalizado §3)
            $table->unsignedInteger('seccion_id');    // seccions.id int unsigned (desnormalizado §3)
            $table->unsignedBigInteger('room_id')->nullable();
                $table->boolean('is_manual_override')->default(false);
                $table->boolean('locked')->default(false);
                $table->timestamps();

                $table->unique(['calendar_id', 'period_id', 'profesor_id'], 'uq_slot_teacher');
                $table->unique(['calendar_id', 'period_id', 'seccion_id'], 'uq_slot_section');
                $table->unique(['calendar_id', 'period_id', 'room_id'], 'uq_slot_room'); // NULL no colisiona en MySQL
                $table->unique(['calendar_id', 'period_id', 'lesson_id'], 'uq_slot_lesson');
                $table->foreign('calendar_id')->references('id')->on('timetable_calendars')->onDelete('cascade');
                $table->foreign('lesson_id')->references('id')->on('timetable_lessons')->onDelete('cascade');
                $table->foreign('period_id')->references('id')->on('timetable_periods')->onDelete('cascade');
                $table->foreign('profesor_id')->references('id')->on('profesors');
                $table->foreign('seccion_id')->references('id')->on('seccions');
                $table->foreign('room_id')->references('id')->on('timetable_rooms');
            });
        }

        // ─── Log de conflictos (auditoría) ──────────────────────────────────
        if (! Schema::hasTable('timetable_conflicts')) {
            Schema::create('timetable_conflicts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('calendar_id');
                $table->unsignedBigInteger('slot_id')->nullable();
                $table->enum('type', [
                    'teacher_double_booked', 'room_double_booked', 'section_double_booked',
                    'availability_violation', 'shift_mismatch',
                ]);
                $table->json('details')->nullable();
                $table->boolean('resolved')->default(false);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('calendar_id')->references('id')->on('timetable_calendars')->onDelete('cascade');
                $table->foreign('slot_id')->references('id')->on('timetable_slots')->onDelete('set null');
            });
        }

        // ─── Ausencias y suplencias (v1.2) ──────────────────────────────────
        if (! Schema::hasTable('timetable_absences')) {
            Schema::create('timetable_absences', function (Blueprint $table) {
$table->bigIncrements('id');
            $table->unsignedBigInteger('calendar_id');
            $table->unsignedInteger('profesor_id');   // profesors.id int unsigned
            $table->date('date_start');
            $table->date('date_end');
            $table->string('reason', 255)->nullable();

                $table->foreign('calendar_id')->references('id')->on('timetable_calendars')->onDelete('cascade');
                $table->foreign('profesor_id')->references('id')->on('profesors')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('timetable_substitute_assignments')) {
            Schema::create('timetable_substitute_assignments', function (Blueprint $table) {
$table->bigIncrements('id');
            $table->unsignedBigInteger('absence_id');
            $table->unsignedBigInteger('slot_id');
            $table->unsignedInteger('substitute_profesor_id');   // profesors.id int unsigned
                $table->enum('status', ['pending', 'confirmed', 'declined'])->default('pending');
                $table->timestamp('notified_at')->nullable();

                $table->foreign('absence_id')->references('id')->on('timetable_absences')->onDelete('cascade');
                $table->foreign('slot_id')->references('id')->on('timetable_slots')->onDelete('cascade');
                $table->foreign('substitute_profesor_id')->references('id')->on('profesors')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_substitute_assignments');
        Schema::dropIfExists('timetable_absences');
        Schema::dropIfExists('timetable_conflicts');
        Schema::dropIfExists('timetable_slots');
        Schema::dropIfExists('timetable_teacher_availability');
        Schema::dropIfExists('timetable_lessons');
        Schema::dropIfExists('timetable_rooms');
        Schema::dropIfExists('timetable_periods');
        Schema::dropIfExists('timetable_calendars');
        Schema::dropIfExists('timetable_shifts');
    }
};