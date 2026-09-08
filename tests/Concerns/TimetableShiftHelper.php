<?php

namespace Tests\Concerns;

use App\Models\app\Timetable\TimetableShift;

/**
 * Helper de tests para crear/reutilizar turnos (catálogo compartido con code
 * único). Usa firstOrCreate para tolerar turnos 'M'/'T' preexistentes que el
 * entorno crea al correr db:seed --class=TimetableTestSeeder.
 */
trait TimetableShiftHelper
{
    protected function makeShift(
        string $code = 'M',
        string $name = 'Mañana',
        string $start = '07:00:00',
        string $end = '12:15:00',
    ): TimetableShift {
        return TimetableShift::query()->firstOrCreate(
            ['code' => $code],
            ['name' => $name, 'start_time' => $start, 'end_time' => $end],
        );
    }
}
