<?php

namespace App\Console\Commands;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Console\Command;

/**
 * Con calendarios por PESTUDIO, los índices únicos de slots son por calendario:
 * un docente que dicta en dos planes puede quedar a la misma hora en DOS
 * calendarios activos del lapso (doble-booking invisible para la BD).
 *
 * Este comando audita los solapes reales de horario (por hora de inicio del
 * período + día) entre todos los calendarios activos del mismo lapso.
 *
 * Uso:
 *   php8.2 artisan timetable:check-cross-booking
 *   php8.2 artisan timetable:check-cross-booking --lapso=1
 */
class TimetableCheckCrossBooking extends Command
{
    protected $signature = 'timetable:check-cross-booking
        {--lapso= : Id del lapso a auditar (default: lapsos con calendarios activos)}';

    protected $description = 'Detecta docentes doble-booked entre calendarios activos de un mismo lapso';

    public function handle(): int
    {
        $lapsoId = $this->option('lapso') ? (int) $this->option('lapso') : null;

        $calendars = TimetableCalendar::query()
            ->active()
            ->when($lapsoId, fn ($q) => $q->forLapso($lapsoId))
            ->with('pestudio')
            ->get();

        if ($calendars->isEmpty()) {
            $this->info('No hay calendarios activos para auditar.');

            return self::SUCCESS;
        }

        $this->info('Calendarios activos: '.$calendars->count().' ('.$calendars->pluck('pestudio.name')->implode(' · ').')');

        // slots por calendario: profesor → (day|hora_inicio) => calendarios
        $occurrences = [];
        $periodCache = [];

        foreach ($calendars as $calendar) {
            $slots = TimetableSlot::query()
                ->where('calendar_id', $calendar->id)
                ->with(['lesson.pevaluacion.profesor'])
                ->get();

            foreach ($slots as $slot) {
                if (! isset($periodCache[$slot->period_id])) {
                    $periodCache[$slot->period_id] = TimetablePeriod::query()->find($slot->period_id);
                }
                $period = $periodCache[$slot->period_id];
                if (! $period) {
                    continue;
                }

                $key = $slot->profesor_id.'|'.$period->day_of_week.'|'.substr((string) $period->start_time, 0, 5);
                $occurrences[$key][] = [
                    'calendar' => $calendar,
                    'slot' => $slot,
                    'period' => $period,
                ];
            }
        }

        // Detectar claves con >1 calendario distinto.
        $conflicts = 0;
        $rows = [];
        foreach ($occurrences as $key => $entries) {
            $uniqueCalendars = collect($entries)->unique(fn ($e) => $e['calendar']->id);
            if ($uniqueCalendars->count() > 1) {
                $conflicts++;
                [$profesorId, $day, $hora] = explode('|', $key);
                $calNames = $uniqueCalendars->map(fn ($e) => $e['calendar']->pestudio?->name ?? $e['calendar']->name)->implode(' ↔ ');
                $rows[] = [
                    'profesor_id' => $profesorId,
                    'día' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'][(int) $day - 1] ?? $day,
                    'hora' => $hora,
                    'calendarios' => $calNames,
                    'slots' => count($entries),
                ];
            }
        }

        if ($conflicts === 0) {
            $this->info('✓ Sin doble-booking docente entre calendarios activos.');

            return self::SUCCESS;
        }

        $this->warn("Se detectaron {$conflicts} solape(s) de docente entre calendarios activos:");
        $this->table(['Profesor', 'Día', 'Hora', 'Calendarios (pestudios)', 'Slots'], $rows);

        return self::FAILURE;
    }
}
