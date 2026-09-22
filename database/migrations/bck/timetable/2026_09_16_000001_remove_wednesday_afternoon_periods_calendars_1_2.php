<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corrección puntual y acotada: elimina los períodos del MIÉRCOLES del
     * turno de la TARDE en los calendarios 1 y 2 (recreo 13:05–13:35 y bloque
     * de clase 13:35–14:55). Estos calendarios no deben tener ese período
     * registrado.
     *
     * Alcance (para no tocar nada más):
     *  - calendar_id IN (1, 2);
     *  - solo el turno de la tarde (código 'T');
     *  - solo day_of_week = 3 (miércoles).
     *
     * Idempotente: si ya no existen esos períodos, no hace nada.
     */
    public function up(): void
    {
        if (! Schema::hasTable('timetable_periods')) {
            return;
        }

        $calendarIds = [1, 2];
        $afternoonShiftIds = DB::table('timetable_shifts')
            ->where('code', 'T')
            ->pluck('id')
            ->all();

        if ($afternoonShiftIds === []) {
            return;
        }

        $periodIds = DB::table('timetable_periods')
            ->whereIn('calendar_id', $calendarIds)
            ->whereIn('shift_id', $afternoonShiftIds)
            ->where('day_of_week', 3)
            ->pluck('id')
            ->all();

        if ($periodIds === []) {
            return;
        }

        DB::transaction(function () use ($periodIds): void {
            // Los slots del período se eliminan explícitamente (y se cuentan)
            // en lugar de depender del ON DELETE CASCADE; los conflicts quedan
            // con period_id NULL (ON DELETE SET NULL).
            $deletedSlots = DB::table('timetable_slots')
                ->whereIn('period_id', $periodIds)
                ->delete();

            $deletedPeriods = DB::table('timetable_periods')
                ->whereIn('id', $periodIds)
                ->delete();

            report(sprintf(
                'RemoveWednesdayAfternoonPeriods: eliminados %d períodos (miércoles, turno tarde) en calendarios 1 y 2 y %d slots asociados. Period IDs: %s.',
                $deletedPeriods,
                $deletedSlots,
                implode(',', $periodIds),
            ));
        });
    }

    public function down(): void
    {
        // No reversible: los períodos y slots eliminados no se pueden
        // reconstruir de forma segura (los IDs ya existen en el autoincrement).
    }
};
