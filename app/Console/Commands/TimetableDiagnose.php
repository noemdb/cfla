<?php

namespace App\Console\Commands;

use App\Models\app\Timetable\TimetableCalendar;
use Illuminate\Console\Command;

class TimetableDiagnose extends Command
{
    protected $signature = 'timetable:diagnose
        {--calendar= : Id del calendario a diagnosticar}
        {--json : Imprime el diagnóstico como JSON}';

    protected $description = 'Diagnostica asignaciones incompletas y datos inconsistentes de un calendario';

    public function handle(): int
    {
        $calendarId = (int) $this->option('calendar');
        if ($calendarId <= 0) {
            $this->error('Debes indicar --calendar=<id>.');

            return self::INVALID;
        }

        $calendar = TimetableCalendar::query()
            ->with(['pestudio', 'lessons.pevaluacion.pensum.asignatura', 'lessons.pevaluacion.seccion.grado', 'lessons.slots.period'])
            ->find($calendarId);

        if (! $calendar) {
            $this->error("No existe el calendario {$calendarId}.");

            return self::FAILURE;
        }

        $diagnosis = [
            'calendar_id' => $calendar->id,
            'calendar' => $calendar->name,
            'strategy' => $calendar->strategy,
            'lessons' => $calendar->lessons->count(),
            'slots' => $calendar->slots()->count(),
            'incomplete_lessons' => [],
            'duplicate_lesson_periods' => [],
            'invalid_slots' => [],
        ];

        foreach ($calendar->lessons as $lesson) {
            $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
            $periodIds = $lesson->slots->pluck('period_id')->map(fn ($id) => (int) $id)->values();
            $assigned = $periodIds->unique()->count();

            if ($assigned !== $required) {
                $diagnosis['incomplete_lessons'][] = [
                    'lesson_id' => $lesson->id,
                    'subject' => $lesson->pevaluacion?->pensum?->asignatura?->name,
                    'section' => $lesson->pevaluacion?->seccion?->name,
                    'section_id' => $lesson->pevaluacion?->seccion_id,
                    'required_blocks' => $required,
                    'assigned_blocks' => $assigned,
                    'missing_blocks' => max(0, $required - $assigned),
                    'locked_slots' => $lesson->slots->where('locked', true)->count(),
                ];
            }

            $duplicates = $periodIds->countBy()->filter(fn (int $count) => $count > 1);
            foreach ($duplicates as $periodId => $count) {
                $diagnosis['duplicate_lesson_periods'][] = [
                    'lesson_id' => $lesson->id,
                    'period_id' => (int) $periodId,
                    'slots' => $count,
                ];
            }

            foreach ($lesson->slots as $slot) {
                if (! $slot->period || $slot->period->calendar_id !== $calendar->id || $slot->period->is_break) {
                    $diagnosis['invalid_slots'][] = [
                        'slot_id' => $slot->id,
                        'lesson_id' => $lesson->id,
                        'period_id' => $slot->period_id,
                        'reason' => ! $slot->period ? 'period_missing' : ($slot->period->is_break ? 'break_period' : 'foreign_calendar_period'),
                    ];
                }
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($diagnosis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->info("Diagnóstico del calendario {$calendar->id}: {$calendar->name}");
            $this->table(['Métrica', 'Valor'], [
                ['Estrategia', $calendar->strategy],
                ['Lecciones', $diagnosis['lessons']],
                ['Slots', $diagnosis['slots']],
                ['Lecciones incompletas', count($diagnosis['incomplete_lessons'])],
                ['Duplicados lección/período', count($diagnosis['duplicate_lesson_periods'])],
                ['Slots inválidos', count($diagnosis['invalid_slots'])],
            ]);

            if ($diagnosis['incomplete_lessons'] !== []) {
                $this->table(
                    ['Lección', 'Asignatura', 'Sección', 'Requeridos', 'Asignados', 'Faltantes', 'Locked'],
                    collect($diagnosis['incomplete_lessons'])->map(fn (array $row) => [
                        $row['lesson_id'], $row['subject'] ?? '—', ($row['section'] ?? '—').' (#'.$row['section_id'].')',
                        $row['required_blocks'], $row['assigned_blocks'], $row['missing_blocks'], $row['locked_slots'],
                    ])->all(),
                );
            }
        }

        return self::SUCCESS;
    }
}
