<?php

namespace Database\Seeders;

use App\Models\app\Timetable\TimetableShift;
use Illuminate\Database\Seeder;

/**
 * Catálogo de turnos del módulo de horarios (SPEC-TIMETABLE-001 §4).
 *
 * Idempotente: usa updateOrCreate por `code` (único), así que en producción
 * crea los turnos si no existen o ALINEA sus ventanas al estándar si ya existen.
 * No borra ni toca calendarios/períodos ya generados.
 *
 * La generación de períodos (importación y wizard) redondea el inicio a la hora
 * y crea SOLO bloques completos de 60 min, por lo que las ventanas producen:
 *   - M (07:00–12:30) → 5 bloques 07:00–12:00
 *   - T (13:00–15:00) → 2 bloques 13:00–14:00 · 14:00–15:00
 *
 * Uso en producción:
 *   php8.2 artisan db:seed --class=TimetableShiftsSeeder --force
 */
class TimetableShiftsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [TimetableShift::CODE_MORNING, 'Mañana', '07:00:00', '12:30:00'],
            [TimetableShift::CODE_AFTERNOON, 'Tarde', '13:00:00', '15:00:00'],
        ] as [$code, $name, $start, $end]) {
            TimetableShift::query()->updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'start_time' => $start, 'end_time' => $end],
            );
        }
    }
}
