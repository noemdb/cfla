<?php

namespace App\Console\Commands;

use App\Models\app\Timetable\TimetableCalendar;
use App\Services\Timetable\CapacityAuditReport;
use App\Services\Timetable\TimetableCapacityAuditService;
use Illuminate\Console\Command;

/**
 * PLAN-TIMETABLE-SOLVER-FALLBACK-001 §11 (F1b) — Auditoría de capacidad.
 *
 * Lista, por calendario, las secciones y docentes cuyo volumen semanal excede
 * los períodos asignables. Esos bloques son imposibles de agendar (C-1): el
 * comando sirve para reconciliar datos (horas `weekly_blocks` vs. períodos).
 *
 * Sólo lectura: no modifica la base de datos.
 */
class TimetableAuditCapacity extends Command
{
    protected $signature = 'timetable:audit-capacity
        {--calendar= : Id(s) de calendario separados por coma (por defecto: todos)}
        {--json : Imprime el diagnóstico como JSON}';

    protected $description = 'Audita la capacidad semanal por sección y docente (bloques imposibles de agendar)';

    public function handle(TimetableCapacityAuditService $auditService): int
    {
        $calendarIds = $this->calendarIds();
        $query = TimetableCalendar::query()->orderBy('id');

        if ($calendarIds !== []) {
            $query->whereIn('id', $calendarIds);
        }

        $calendars = $query->get();

        if ($calendars->isEmpty()) {
            $this->error($calendarIds !== []
                ? 'No existe ningún calendario con esos ids.'
                : 'No hay calendarios registrados.');

            return self::FAILURE;
        }

        $reports = $calendars
            ->map(fn (TimetableCalendar $calendar) => [
                'calendar' => $calendar,
                'report' => $auditService->audit($calendar),
            ]);

        if ($this->option('json')) {
            $this->line(json_encode(
                $reports->map(fn (array $entry) => $entry['report']->toArray())->all(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
            ));

            return self::SUCCESS;
        }

        foreach ($reports as $entry) {
            $this->renderReport($entry['calendar'], $entry['report']);
        }

        return self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function calendarIds(): array
    {
        $raw = (string) $this->option('calendar');

        if (trim($raw) === '') {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(fn (string $id): int => (int) trim($id))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function renderReport(TimetableCalendar $calendar, CapacityAuditReport $report): void
    {
        $this->newLine();
        $this->info("Calendario {$calendar->id} · {$calendar->name}");

        $this->table(
            ['Turno', 'Períodos asignables'],
            collect($report->periodsByShift)
                ->map(fn (int $count, int $shiftId): array => ["#{$shiftId}", $count])
                ->values()
                ->all(),
        );

        $sections = $report->overflowSections();

        if ($sections === []) {
            $this->line('  Secciones: sin overflow.');
        } else {
            $this->warn('  Secciones excedidas (bloques imposibles: '.$report->overflowBlocksSections().')');
            $this->table(
                ['Sección', 'Turnos', 'Carga', 'Capacidad', 'Overflow'],
                array_map(fn (array $row): array => [
                    '#'.$row['seccion_id'],
                    implode(',', $row['shift_ids']),
                    $row['load'],
                    $row['capacity'],
                    $row['overflow'],
                ], $sections),
            );
        }

        $teachers = $report->overflowTeachers();

        if ($teachers === []) {
            $this->line('  Docentes: sin overflow.');
        } else {
            $this->warn('  Docentes excedidos (bloques imposibles: '.$report->overflowBlocksTeachers().')');
            $this->table(
                ['Docente', 'Turnos', 'Carga', 'Capacidad', 'Overflow'],
                array_map(fn (array $row): array => [
                    '#'.$row['profesor_id'],
                    implode(',', $row['shift_ids']),
                    $row['load'],
                    $row['capacity'],
                    $row['overflow'],
                ], $teachers),
            );
        }
    }
}
