<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrige la hora final (`timetable_periods.end_time`) de bloques de horario
 * para los niveles indicados. Por defecto cambia 12:30:00 → 12:35:00 solo en
 * PRIMARIA e INICIAL.
 *
 * No elimina ni recrea períodos: solo actualiza `end_time`, por lo que los
 * slots y la disponibilidad docente que referencian el período se conservan.
 *
 * Uso en producción:
 *   php8.2 artisan timetable:adjust-period-end-time --dry-run
 *   php8.2 artisan timetable:adjust-period-end-time
 *   php8.2 artisan timetable:adjust-period-end-time --from=12:30:00 --to=12:35:00 --level=PRIMARIA --level=INICIAL --force
 */
class TimetableAdjustPeriodEndTime extends Command
{
    protected $signature = 'timetable:adjust-period-end-time
        {--from=12:30:00 : Hora final actual a reemplazar (HH:MM:SS)}
        {--to=12:35:00 : Nueva hora final (HH:MM:SS)}
        {--level=* : Niveles/planes a incluir (default: PRIMARIA e INICIAL)}
        {--calendar= : Limita a un calendar_id}
        {--dry-run : Lista los períodos a corregir sin modificar la base de datos}
        {--force : Aplica los cambios sin pedir confirmación}';

    protected $description = 'Ajusta timetable_periods.end_time para los niveles indicados (default 12:30:00 → 12:35:00 en PRIMARIA/INICIAL)';

    public function handle(): int
    {
        $from = $this->normalizeTime((string) $this->option('from'));
        $to = $this->normalizeTime((string) $this->option('to'));

        if ($from === null || $to === null) {
            $this->error('--from y --to deben tener formato HH:MM o HH:MM:SS.');

            return self::INVALID;
        }

        if ($from === $to) {
            $this->error('--from y --to son iguales; no hay nada que hacer.');

            return self::INVALID;
        }

        $levels = array_values(array_filter(array_map(
            fn ($level): string => mb_strtoupper(trim((string) $level)),
            (array) $this->option('level'),
        )));
        if ($levels === []) {
            $levels = ['PRIMARIA', 'INICIAL'];
        }

        $calendarOption = $this->option('calendar');
        $calendarId = $calendarOption !== null ? (int) $calendarOption : null;
        if ($calendarOption !== null && $calendarId <= 0) {
            $this->error('--calendar debe ser un id válido.');

            return self::INVALID;
        }

        $hasPeriodPestudio = Schema::hasColumn('timetable_periods', 'pestudio_id');

        $levelExpr = $hasPeriodPestudio
            ? "UPPER(COALESCE(NULLIF(ppe.name, ''), cpe.name, ''))"
            : "UPPER(COALESCE(cpe.name, ''))";

        $query = DB::table('timetable_periods as p')
            ->join('timetable_calendars as c', 'c.id', '=', 'p.calendar_id')
            ->leftJoin('pestudios as cpe', 'cpe.id', '=', 'c.pestudio_id')
            ->where('p.end_time', $from)
            ->when($calendarId !== null, fn ($q) => $q->where('p.calendar_id', $calendarId))
            ->where(function ($q) use ($levels, $levelExpr): void {
                foreach ($levels as $level) {
                    $q->orWhereRaw("{$levelExpr} LIKE ?", ['%'.$level.'%']);
                }
            });

        if ($hasPeriodPestudio) {
            $query->leftJoin('pestudios as ppe', 'ppe.id', '=', 'p.pestudio_id');
        }

        $rows = $query
            ->select([
                'p.id',
                'p.calendar_id',
                'c.name as calendar',
                'c.status',
                DB::raw("{$levelExpr} as level"),
                'p.day_of_week',
                'p.order_in_day',
                'p.start_time',
                'p.end_time',
                'p.is_break',
            ])
            ->orderBy('p.calendar_id')
            ->orderBy('p.day_of_week')
            ->orderBy('p.order_in_day')
            ->get();

        if ($rows->isEmpty()) {
            $this->info("No se encontraron períodos con end_time={$from} para: ".implode(', ', $levels).'.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Calendario', 'Estado', 'Nivel', 'Día', 'Orden', 'Inicio', 'Fin actual', 'Fin nuevo'],
            $rows->map(fn ($row): array => [
                $row->id,
                $row->calendar_id.' · '.$row->calendar,
                $row->status,
                $row->level,
                $row->day_of_week,
                $row->order_in_day,
                $row->start_time,
                $row->end_time,
                $to,
            ])->all(),
        );

        $this->info($rows->count().' período(s) por actualizar ('.$from.' → '.$to.').');

        $this->warnOverlaps($rows->pluck('id')->all(), $from, $to);

        if ($this->option('dry-run')) {
            $this->comment('Dry-run: no se modificó la base de datos.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('¿Aplicar el ajuste de hora final?', false)) {
            $this->comment('Operación cancelada.');

            return self::SUCCESS;
        }

        $updated = DB::table('timetable_periods')
            ->whereIn('id', $rows->pluck('id')->all())
            ->where('end_time', $from)
            ->update(['end_time' => $to]);

        $this->info("Listo: {$updated} período(s) actualizados a {$to}.");

        return self::SUCCESS;
    }

    /** Advierte si el nuevo fin pisa el inicio de otro bloque del mismo día. */
    private function warnOverlaps(array $periodIds, string $from, string $to): void
    {
        $overlaps = DB::table('timetable_periods as p')
            ->join('timetable_periods as n', function ($join): void {
                $join->on('n.calendar_id', '=', 'p.calendar_id')
                    ->on('n.day_of_week', '=', 'p.day_of_week')
                    ->on('n.shift_id', '=', 'p.shift_id')
                    ->whereColumn('n.id', '!=', 'p.id');
            })
            ->whereIn('p.id', $periodIds)
            ->where('n.start_time', '>=', $from)
            ->where('n.start_time', '<', $to)
            ->select(['p.id', 'p.calendar_id', 'p.day_of_week', 'n.start_time'])
            ->get();

        if ($overlaps->isEmpty()) {
            return;
        }

        $this->warn('Atención: '.$overlaps->count().' bloque(s) quedarían solapados con el inicio del siguiente período:');
        foreach ($overlaps->take(20) as $overlap) {
            $this->line("  - período {$overlap->id} (calendario {$overlap->calendar_id}, día {$overlap->day_of_week}) solaparía bloque que inicia {$overlap->start_time}");
        }
    }

    private function normalizeTime(string $value): ?string
    {
        $value = trim($value);
        if (! preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
            return null;
        }

        $parts = explode(':', $value);
        $hour = (int) $parts[0];
        $minute = (int) $parts[1];
        $second = (int) ($parts[2] ?? 0);

        if ($hour > 23 || $minute > 59 || $second > 59) {
            return null;
        }

        return sprintf('%02d:%02d:%02d', $hour, $minute, $second);
    }
}
