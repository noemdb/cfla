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
     * Una celda puede contener VARIOS slots (división por sub-grupos en paralelo).
     *
     * @return Collection<int, Collection<int, Collection<int, TimetableSlot>>>
     */
    public function gridForSection(TimetableCalendar $calendar, int $seccionId): Collection
    {
        $slots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('seccion_id', $seccionId)
            ->with(['lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.profesor', 'lesson.pevaluacion.grupoEstable'])
            ->get();

        return $this->buildGrid($calendar, $slots);
    }

    public function gridForTeacher(TimetableCalendar $calendar, int $profesorId): Collection
    {
        $slots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('profesor_id', $profesorId)
            ->with([
                'period',
                'room',
                'lesson.pevaluacion.pensum.asignatura',
                'lesson.pevaluacion.seccion.grado',
                'lesson.pevaluacion.grupoEstable',
            ])
            ->get();

        return $this->buildGrid($calendar, $slots);
    }

    public function gridForRoom(TimetableCalendar $calendar, int $roomId): Collection
    {
        $slots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('room_id', $roomId)
            ->with(['lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.seccion', 'lesson.pevaluacion.profesor', 'lesson.pevaluacion.grupoEstable'])
            ->get();

        return $this->buildGrid($calendar, $slots);
    }

    /**
     * @return Collection<int, Collection<int, Collection<int, TimetableSlot>>>
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
                $period = $group
                    ->filter(fn ($p) => (int) $p->day_of_week === $day)
                    ->sortBy('id')
                    ->first();
                if (! $period) {
                    $row->put($day, collect());

                    continue;
                }
                $cell = $slots->filter(fn ($s) => (int) $s->period_id === (int) $period->id);
                $row->put($day, $cell->values());
            }
            $rows->put((int) $order, $row);
        }

        return $rows;
    }
}
