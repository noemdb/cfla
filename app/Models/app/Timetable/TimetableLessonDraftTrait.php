<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Pevaluacion;
use App\Services\Timetable\TimetableLessonPersistenceService;

trait TimetableLessonDraftTrait
{
    /**
     * Persiste la selección actual de lecciones como borrador.
     *
     * @param  array<int|string, array<string, mixed>>  $lessons
     */
    protected function persistTimetableLessonDraft(int $calendarId, array $lessons): void
    {
        app(TimetableLessonPersistenceService::class)->persist($calendarId, $lessons);
    }

    /**
     * Recalcula los bloques desde las horas actuales de las asignaturas.
     * No modifica `asignaturas`; devuelve la selección actualizada.
     *
     * @param  array<int|string, array<string, mixed>>  $lessons
     * @return array<int|string, array<string, mixed>>
     */
    protected function recalculateTimetableLessonBlocks(int $calendarId, array $lessons): array
    {
        $calendar = TimetableCalendar::query()->findOrFail($calendarId);
        $periodMinutes = max(1, (int) $calendar->period_minutes);
        $pevs = Pevaluacion::query()
            ->with('pensum.asignatura')
            ->whereIn('id', array_keys($lessons))
            ->get()
            ->keyBy('id');

        foreach ($lessons as $pevId => &$lesson) {
            $asignatura = $pevs->get((int) $pevId)?->pensum?->asignatura;
            if (! $asignatura) {
                continue;
            }

            $lesson['weekly_blocks_t'] = (int) ceil(
                ((int) ($asignatura->hour_t_week ?? 0)) * 60 / $periodMinutes
            );
            $lesson['weekly_blocks_p'] = (int) ceil(
                ((int) ($asignatura->hour_p_week ?? 0)) * 60 / $periodMinutes
            );
        }
        unset($lesson);

        return $lessons;
    }
}
