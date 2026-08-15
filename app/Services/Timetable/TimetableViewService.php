<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SPEC-TIMETABLE-001 §8 — Construye las grillas de horario (sección/docente/
 * aula) reutilizables por el PDF y la vista pública firmada.
 */
class TimetableViewService
{
    public function activeCalendarOrFail($calendarId): TimetableCalendar
    {
        $calendar = TimetableCalendar::query()
            ->where('id', $calendarId)
            ->where('status', 'active')
            ->first();

        if (! $calendar) {
            throw new NotFoundHttpException('Calendario no publicado.');
        }

        return $calendar;
    }

    /**
     * Grilla día (col) × período (fila) para una sección.
     *
     * @return Collection<int, Collection<int, TimetableSlot|null>>
     */
    public function gridForSection(TimetableCalendar $calendar, int $seccionId): Collection
    {
        $slots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('seccion_id', $seccionId)
            ->with(['lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.profesor'])
            ->get();

        return $this->buildGrid($calendar, $slots);
    }

    public function gridForTeacher(TimetableCalendar $calendar, int $profesorId): Collection
    {
        $slots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('profesor_id', $profesorId)
            ->with(['lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.seccion'])
            ->get();

        return $this->buildGrid($calendar, $slots);
    }

    public function gridForRoom(TimetableCalendar $calendar, int $roomId): Collection
    {
        $slots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('room_id', $roomId)
            ->with(['lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.seccion', 'lesson.pevaluacion.profesor'])
            ->get();

        return $this->buildGrid($calendar, $slots);
    }

    /**
     * @return Collection<int, Collection<int, TimetableSlot|null>>
     */
    private function buildGrid(TimetableCalendar $calendar, Collection $slots): Collection
    {
        $periods = $calendar->periods()
            ->orderBy('day_of_week')
            ->orderBy('order_in_day')
            ->get();

        $rows = collect();

        foreach ($periods->groupBy('order_in_day') as $order => $group) {
            $row = collect();
            foreach (range(1, 5) as $day) {
                $period = $group->first(fn ($p) => (int) $p->day_of_week === $day);
                if (! $period) {
                    $row->put($day, null);

                    continue;
                }
                $row->put($day, $slots->first(fn ($s) => (int) $s->period_id === (int) $period->id));
            }
            $rows->put((int) $order, $row);
        }

        return $rows;
    }
}
