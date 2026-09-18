<?php

namespace App\Console\Commands;

use App\Models\app\Timetable\TimetableLesson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Repara el desfase (drift) de `timetable_slots.allow_shared_teacher` respecto
 * de `timetable_lessons.allow_shared_teacher`.
 *
 * El flag está duplicado: la lección es la fuente canónica que usan el solver,
 * la validación de conflictos, readiness y los reportes, mientras que el slot
 * alimenta el índice único `uq_slot_teacher`. Cuando el editor marca un slot
 * como compartido (colisión docente en el mismo período) pero la lección quedó
 * con el flag en `false`, los reportes cuentan el bloque como normal (sin
 * colapsar) y la totalización se infla.
 *
 * La reparación SOLO relaja: eleva el flag de la lección a `true` cuando alguno
 * de sus slots ya lo tiene, y propaga ese valor a todos los slots de la lección.
 * Nunca baja un flag a `false`, porque eso violaría `uq_slot_teacher` cuando
 * existe otro slot normal del mismo docente en el período.
 *
 * Uso en producción:
 *   php8.2 artisan timetable:repair-shared-teacher-drift --dry-run
 *   php8.2 artisan timetable:repair-shared-teacher-drift --calendar=2 --dry-run
 *   php8.2 artisan timetable:repair-shared-teacher-drift --force
 */
class TimetableRepairSharedTeacherDrift extends Command
{
    protected $signature = 'timetable:repair-shared-teacher-drift
        {--calendar= : Repara solo el calendario indicado}
        {--dry-run : Lista las lecciones a corregir sin modificar la base de datos}
        {--force : Aplica los cambios sin pedir confirmación}';

    protected $description = 'Alinea allow_shared_teacher entre timetable_lessons y sus timetable_slots';

    public function handle(): int
    {
        $calendarOption = $this->option('calendar');
        $calendarId = $calendarOption !== null ? (int) $calendarOption : null;

        if ($calendarOption !== null && $calendarId <= 0) {
            $this->error('--calendar debe ser un id válido.');

            return self::INVALID;
        }

        $dryRun = (bool) $this->option('dry-run');

        $lessons = TimetableLesson::query()
            ->when($calendarId !== null, fn ($query) => $query->where('calendar_id', $calendarId))
            ->with(['slots:id,lesson_id,allow_shared_teacher'])
            ->get(['id', 'calendar_id', 'allow_shared_teacher']);

        $rows = [];

        foreach ($lessons as $lesson) {
            if ($lesson->slots->isEmpty()) {
                continue;
            }

            $lessonFlag = (bool) $lesson->allow_shared_teacher;
            $slotFlag = $lesson->slots->contains(fn ($slot) => (bool) $slot->allow_shared_teacher);
            $desired = $lessonFlag || $slotFlag;

            if ($desired === $lessonFlag) {
                continue;
            }

            $rows[] = [
                'id' => (int) $lesson->id,
                'calendar_id' => (int) $lesson->calendar_id,
                'slots' => $lesson->slots->count(),
                'shared_slots' => $lesson->slots->where('allow_shared_teacher', true)->count(),
                'from' => (int) $lessonFlag,
                'to' => 1,
            ];
        }

        if ($rows === []) {
            $this->info('No se encontraron lecciones con allow_shared_teacher desfasado.');

            return self::SUCCESS;
        }

        $this->table(
            ['Lección', 'Calendario', 'Slots', 'Slots compartidos', 'Flag lección', 'Flag correcto'],
            array_map(
                fn (array $row): array => [$row['id'], $row['calendar_id'], $row['slots'], $row['shared_slots'], $row['from'], $row['to']],
                array_slice($rows, 0, 50),
            ),
        );

        if (count($rows) > 50) {
            $this->line('… y '.(count($rows) - 50).' más.');
        }

        $this->info(count($rows).' lección(es) con allow_shared_teacher desfasado.');

        if ($dryRun) {
            $this->comment('Dry-run: no se modificó la base de datos.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('¿Aplicar la alineación de allow_shared_teacher?', false)) {
            $this->comment('Operación cancelada.');

            return self::SUCCESS;
        }

        $lessonsUpdated = 0;
        $slotsUpdated = 0;

        DB::transaction(function () use ($rows, &$lessonsUpdated, &$slotsUpdated): void {
            foreach (array_chunk($rows, 200) as $chunk) {
                $lessonIds = array_column($chunk, 'id');

                $lessonsUpdated += TimetableLesson::query()
                    ->whereIn('id', $lessonIds)
                    ->where('allow_shared_teacher', false)
                    ->update(['allow_shared_teacher' => true]);

                // Propaga a los slots para que el índice único y los reportes
                // queden alineados con la lección (solo relaja).
                $slotsUpdated += DB::table('timetable_slots')
                    ->whereIn('lesson_id', $lessonIds)
                    ->where('allow_shared_teacher', false)
                    ->update(['allow_shared_teacher' => true]);
            }
        });

        $this->info("Listo: {$lessonsUpdated} lección(es) y {$slotsUpdated} slot(s) corregidos.");

        return self::SUCCESS;
    }
}
