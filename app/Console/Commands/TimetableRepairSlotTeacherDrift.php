<?php

namespace App\Console\Commands;

use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

/**
 * Repara el desfase (drift) de `timetable_slots.profesor_id` respecto al
 * profesor de su Pevaluación. La Pevaluación es la fuente canónica que usan la
 * grilla y los reportes; el `profesor_id` del slot es una columna denormalizada
 * que puede quedar obsoleta tras reasignar el docente de una Pevaluación o al
 * restaurar respaldos.
 *
 * Uso en producción:
 *   php8.2 artisan timetable:repair-slot-teacher-drift --dry-run
 *   php8.2 artisan timetable:repair-slot-teacher-drift --calendar=2 --dry-run
 *   php8.2 artisan timetable:repair-slot-teacher-drift --force
 */
class TimetableRepairSlotTeacherDrift extends Command
{
    protected $signature = 'timetable:repair-slot-teacher-drift
        {--calendar= : Repara solo el calendario indicado}
        {--dry-run : Lista los slots a corregir sin modificar la base de datos}
        {--force : Aplica los cambios sin pedir confirmación}';

    protected $description = 'Corrige timetable_slots.profesor_id cuando difiere del profesor de su Pevaluación';

    public function handle(): int
    {
        $calendarOption = $this->option('calendar');
        $calendarId = $calendarOption !== null ? (int) $calendarOption : null;

        if ($calendarOption !== null && $calendarId <= 0) {
            $this->error('--calendar debe ser un id válido.');

            return self::INVALID;
        }

        $dryRun = (bool) $this->option('dry-run');

        $rows = [];

        TimetableSlot::query()
            ->when($calendarId !== null, fn ($query) => $query->where('calendar_id', $calendarId))
            ->whereHas('lesson.pevaluacion', fn ($query) => $query->where('profesor_id', '>', 0))
            ->with(['lesson:id,pevaluacion_id', 'lesson.pevaluacion:id,profesor_id'])
            ->chunkById(500, function ($slots) use (&$rows): void {
                foreach ($slots as $slot) {
                    $expected = (int) ($slot->lesson?->pevaluacion?->profesor_id ?? 0);

                    if ($expected <= 0 || (int) $slot->profesor_id === $expected) {
                        continue;
                    }

                    $rows[] = [
                        'id' => (int) $slot->id,
                        'calendar_id' => (int) $slot->calendar_id,
                        'lesson_id' => (int) $slot->lesson_id,
                        'from' => (int) $slot->profesor_id,
                        'to' => $expected,
                    ];
                }
            });

        if ($rows === []) {
            $this->info('No se encontraron slots con profesor desfasado.');

            return self::SUCCESS;
        }

        $this->table(
            ['Slot', 'Calendario', 'Lección', 'Profesor actual', 'Profesor correcto'],
            array_map(
                fn (array $row): array => [$row['id'], $row['calendar_id'], $row['lesson_id'], $row['from'], $row['to']],
                array_slice($rows, 0, 50),
            ),
        );

        if (count($rows) > 50) {
            $this->line('… y '.(count($rows) - 50).' más.');
        }

        $this->info(count($rows).' slot(s) con profesor desfasado.');

        if ($dryRun) {
            $this->comment('Dry-run: no se modificó la base de datos.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('¿Aplicar la corrección de profesor_id?', false)) {
            $this->comment('Operación cancelada.');

            return self::SUCCESS;
        }

        $updated = 0;
        $skipped = [];

        foreach ($rows as $row) {
            try {
                $affected = TimetableSlot::query()
                    ->whereKey($row['id'])
                    ->where('profesor_id', $row['from'])
                    ->update(['profesor_id' => $row['to']]);

                if ($affected > 0) {
                    $updated++;
                }
            } catch (QueryException $exception) {
                // Choque con uq_slot_teacher (calendario, período, docente):
                // ya existe un slot del docente correcto en ese período.
                report($exception);
                $skipped[] = $row['id'];
            }
        }

        $this->info("Listo: {$updated} slot(s) corregidos.");

        if ($skipped !== []) {
            $this->warn(count($skipped).' slot(s) omitidos por conflicto de unicidad: '.implode(', ', array_slice($skipped, 0, 20)).(count($skipped) > 20 ? ' …' : ''));
        }

        return self::SUCCESS;
    }
}
