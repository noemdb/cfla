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
        $query = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('lesson.pevaluacion.seccion', function ($query): void {
                $query->where('status_active', 'true')
                    ->whereHas('grado', fn ($grado) => $grado->where('status_active', 'true'));
            });

        $this->applyEffectiveTeacherFilter($query, $profesorId);

        $slots = $query
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

    /**
     * IDs de docentes con slots en el calendario (secciones y grados activos).
     *
     * El docente se deriva de `pevaluacion.profesor_id` (fuente canónica que
     * muestran la grilla y los reportes), no de la columna denormalizada
     * `timetable_slots.profesor_id`, que puede quedar desfasada al reasignar
     * una Pevaluación.
     *
     * @return Collection<int, int>
     */
    public function teacherIdsForCalendar(TimetableCalendar $calendar): Collection
    {
        return TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('lesson.pevaluacion.seccion', function ($query): void {
                $query->where('status_active', 'true')
                    ->whereHas('grado', fn ($grado) => $grado->where('status_active', 'true'));
            })
            ->with('lesson.pevaluacion:id,profesor_id')
            ->get(['id', 'lesson_id', 'profesor_id'])
            ->map(fn (TimetableSlot $slot): int => (int) ($slot->lesson?->pevaluacion?->profesor_id ?? $slot->profesor_id))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();
    }

    /**
     * Restringe la consulta a los slots cuyo docente efectivo es $profesorId.
     *
     * El docente canónico es `pevaluacion.profesor_id`; `timetable_slots.profesor_id`
     * solo se usa como respaldo cuando la lesson no tiene Pevaluación asociada.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TimetableSlot>  $query
     */
    private function applyEffectiveTeacherFilter($query, int $profesorId): void
    {
        $query->where(function ($query) use ($profesorId): void {
            $query->whereHas('lesson.pevaluacion', fn ($pev) => $pev->where('profesor_id', $profesorId))
                ->orWhere(function ($query) use ($profesorId): void {
                    $query->whereDoesntHave('lesson.pevaluacion')
                        ->where('profesor_id', $profesorId);
                });
        });
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
    /**
     * Schedules del docente separados por turno, para que los recreos de cada
     * turno se muestren correctamente (sin conflación de turnos).
     *
     * @return list<array{shift: TimetableShift, periods: \Illuminate\Support\Collection, grid: \Illuminate\Support\Collection}>
     */
    public function teacherShiftSchedules(TimetableCalendar $calendar, int $profesorId): array
    {
        $query = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('lesson.pevaluacion.seccion', function ($query): void {
                $query->where('status_active', 'true')
                    ->whereHas('grado', fn ($grado) => $grado->where('status_active', 'true'));
            });

        $this->applyEffectiveTeacherFilter($query, $profesorId);

        $slots = $query
            ->with(['period', 'room', 'lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.seccion.grado', 'lesson.pevaluacion.grupoEstable'])
            ->get();

        $shiftIds = $calendar->periods()->pluck('shift_id')->unique()->values();
        $shifts = \App\Models\app\Timetable\TimetableShift::query()->whereIn('id', $shiftIds)->get();

        $schedules = [];

        foreach ($shifts as $shift) {
            $shiftPeriods = $calendar->periods()
                ->where('shift_id', $shift->id)
                ->with('shift')
                ->orderBy('day_of_week')
                ->orderBy('order_in_day')
                ->get()
                ->groupBy('order_in_day');

            $grid = collect();

            foreach ($shiftPeriods as $order => $group) {
                $row = collect();
                foreach (range(1, 5) as $day) {
                    $dayPeriods = $group
                        ->filter(fn ($p) => (int) $p->day_of_week === $day)
                        ->reject(fn ($p) => (bool) $p->is_break)
                        ->sortBy('id');
                    $cell = collect();
                    foreach ($dayPeriods as $period) {
                        $cell = $cell->merge($slots->filter(fn ($s) => (int) $s->period_id === (int) $period->id)->values());
                    }
                    $row->put($day, $cell->values());
                }
                $grid->put((int) $order, $row);
            }

            $periods = $shiftPeriods->map(fn ($group) => $group->keyBy('day_of_week'));

            $schedules[] = [
                'shift' => $shift,
                'periods' => $periods,
                'grid' => $grid,
            ];
        }

        return $schedules;
    }

    /**
     * Fusiona los horarios por turno de varios P.Estudios de un mismo
     * P.Educativo en un único horario por turno (sin subdivisiones por
     * P.Estudio), concatenando los slots que caen en el mismo bloque/día.
     *
     * @param  array<int, array{shift: \App\Models\app\Timetable\TimetableShift, periods: Collection, grid: Collection}>  ...$schedulesList
     * @return list<array{shift: \App\Models\app\Timetable\TimetableShift, periods: Collection, grid: Collection}>
     */
    public function mergePeducativoSchedules(array ...$schedulesList): array
    {
        $merged = [];

        foreach ($schedulesList as $schedules) {
            foreach ($schedules as $schedule) {
                $shiftId = (int) $schedule['shift']->id;

                if (! isset($merged[$shiftId])) {
                    $merged[$shiftId] = [
                        'shift' => $schedule['shift'],
                        'periods' => collect(),
                        'grid' => collect(),
                    ];
                }

                // Períodos: order => (día => período). Conserva el primero visto
                // (define el horario mostrado en la columna "Bloque").
                foreach ($schedule['periods'] as $order => $dayPeriods) {
                    $existing = $merged[$shiftId]['periods']->get($order, collect());

                    foreach ($dayPeriods as $day => $period) {
                        if (! $existing->has($day)) {
                            $existing->put($day, $period);
                        }
                    }

                    $merged[$shiftId]['periods']->put($order, $existing);
                }

                // Grilla: order => (día => slots). Concatena los slots de todos
                // los P.Estudios del P.Educativo.
                foreach ($schedule['grid'] as $order => $dayCells) {
                    $existing = $merged[$shiftId]['grid']->get($order, collect());

                    foreach ($dayCells as $day => $cell) {
                        $existing->put($day, $existing->get($day, collect())->merge($cell)->values());
                    }

                    $merged[$shiftId]['grid']->put($order, $existing);
                }
            }
        }

        return collect($merged)
            ->sortBy(fn (array $schedule): string => (string) ($schedule['shift']->start_time ?? ''))
            ->values()
            ->all();
    }

    /**
     * Horarios del docente agrupados por P.Educativo (fusionados) para varios
     * calendarios, p. ej. todos los de un lapso. Devuelve un único horario por
     * P.Educativo.
     *
     * @param  iterable<TimetableCalendar>  $calendars
     * @return list<array{peducativo: ?\App\Models\app\Academy\Peducativo, schedules: list<array{shift: \App\Models\app\Timetable\TimetableShift, periods: Collection, grid: Collection}>}>
     */
    public function teacherPeducativoSchedulesForCalendars(iterable $calendars, int $profesorId): array
    {
        $byPeducativo = [];

        foreach ($calendars as $calendar) {
            $shifts = $this->teacherShiftSchedules($calendar, $profesorId);

            if ($shifts === []) {
                continue;
            }

            $peducativo = $calendar->pestudio?->peducativo;
            $key = (int) ($peducativo?->id ?? 0);

            if (! isset($byPeducativo[$key])) {
                $byPeducativo[$key] = ['peducativo' => $peducativo, 'schedules' => []];
            }

            $byPeducativo[$key]['schedules'][] = $shifts;
        }

        $result = [];

        foreach ($byPeducativo as $data) {
            $result[] = [
                'peducativo' => $data['peducativo'],
                'schedules' => $this->mergePeducativoSchedules(...$data['schedules']),
            ];
        }

        usort($result, function (array $a, array $b): int {
            $orderA = (int) ($a['peducativo']?->order ?? 0);
            $orderB = (int) ($b['peducativo']?->order ?? 0);

            return $orderA <=> $orderB
                ?: strcasecmp((string) ($a['peducativo']?->name ?? ''), (string) ($b['peducativo']?->name ?? ''));
        });

        return $result;
    }

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
                // Solo períodos de clase (no recreos) de ese día. En turnos donde
                // un mismo order_in_day comparte recreo del otro turno, se debe
                // tomar el período de clase real (p. ej. turno tarde order 2).
                $dayPeriods = $group
                    ->filter(fn ($p) => (int) $p->day_of_week === $day)
                    ->reject(fn ($p) => (bool) $p->is_break)
                    ->sortBy('id');
                $cell = collect();
                foreach ($dayPeriods as $period) {
                    $cell = $cell->merge(
                        $slots->filter(fn ($s) => (int) $s->period_id === (int) $period->id)->values(),
                    );
                }
                $row->put($day, $cell->values());
            }
            $rows->put((int) $order, $row);
        }

        return $rows;
    }
}
