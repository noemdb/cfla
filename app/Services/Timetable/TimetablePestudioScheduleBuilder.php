<?php

namespace App\Services\Timetable;

use App\Http\Controllers\Timetable\Concerns\InteractsWithTimetablePdf;
use App\Http\Controllers\Timetable\Concerns\MeasuresTimetableBlocks;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;

/**
 * Horarios por grado/sección de un P.ESTUDIO para un calendario (compartido
 * por el PDF de un P.Estudio y el consolidado de todos los P.Estudios).
 *
 * Extraído de TimetablePdfController::buildPestudioSchedules(); comparte con
 * el controller las métricas de bloques y la semántica del badge vía los
 * traits de `App\Http\Controllers\Timetable\Concerns`.
 */
class TimetablePestudioScheduleBuilder
{
    use InteractsWithTimetablePdf;
    use MeasuresTimetableBlocks;

    /**
     * @param  bool  $forcePersistedSlots  El consolidado de todos los
     *                                     P.Estudios exige leer siempre de los
     *                                     slots persistidos (fuente canónica),
     *                                     sin mezclar `preview_payload`.
     * @return array{isPublishedSchedule: bool, gradeSchedules: \Illuminate\Support\Collection<int, array>}
     */
    public function build(TimetableCalendar $calendar, Pestudio $pestudio, bool $forcePersistedSlots = false): array
    {
        $usePersistedSlots = $forcePersistedSlots || $this->shouldUsePersistedSlots($calendar);
        $isPublishedSchedule = $this->isPublishedSchedule($calendar, $usePersistedSlots);

        if ($forcePersistedSlots) {
            // El consolidado de todos los P.Estudios solo incluye calendarios
            // activos y debe leerse de una única fuente canónica (los slots
            // persistidos) para no mezclar `preview_payload` de unos con
            // slots de otros en el mismo documento. Un calendario sin slots
            // produce un bloque vacío que el llamador filtra.
        } elseif (! $calendar->preview_payload && ! $isPublishedSchedule) {
            abort(404, 'No hay vista previa para este calendario.');
        }

        $grades = Grado::query()
            ->where('pestudio_id', $pestudio->id)
            ->where('status_active', 'true')
            ->with(['seccions' => fn ($query) => $query->where('status_active', 'true')->orderBy('name')])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $assignment = collect($calendar->preview_payload['assignment'] ?? []);
        $periods = TimetablePeriod::query()
            ->where('calendar_id', $calendar->id)
            ->with('shift')
            ->orderBy('day_of_week')
            ->orderBy('order_in_day')
            ->get()
            ->keyBy('id');
        $lessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('pevaluacion.seccion', fn ($query) => $query->where('status_active', 'true'))
            ->whereHas('pevaluacion.seccion.grado', fn ($query) => $query->where('status_active', 'true'))
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.profesor', 'pevaluacion.seccion.grado', 'pevaluacion.grupoEstable'])
            ->get();

        $gradeSchedules = $grades->map(function (Grado $grado) use (
            $calendar,
            $assignment,
            $periods,
            $lessons,
            $usePersistedSlots
        ): array {
            $sectionSchedules = $grado->seccions->map(function (Seccion $section) use (
                $calendar,
                $assignment,
                $periods,
                $lessons,
                $usePersistedSlots
            ): array {
                $sectionLessons = $lessons->filter(fn (TimetableLesson $lesson): bool => (int) $lesson->pevaluacion?->seccion_id === (int) $section->id);
                $sectionAssignment = $usePersistedSlots
                    ? TimetableSlot::query()->where('calendar_id', $calendar->id)->where('seccion_id', $section->id)
                        ->get(['lesson_id', 'period_id', 'room_id', 'half'])->groupBy('lesson_id')
                        ->map(fn ($slots) => $slots->map(fn ($slot) => [
                            'period_id' => (int) $slot->period_id,
                            'room_id' => (int) ($slot->room_id ?? 0),
                            'half' => $slot->half !== null ? (int) $slot->half : null,
                        ])->values()->all())
                    : $assignment;
                $assignmentsByPeriod = [];
                $teacherSummary = [];

                foreach ($sectionLessons as $lesson) {
                    foreach ($sectionAssignment->get((string) $lesson->id, $sectionAssignment->get($lesson->id, [])) as $slot) {
                        $period = $periods->get((int) ($slot['period_id'] ?? 0));
                        if (! $period) {
                            continue;
                        }
                        $pev = $lesson->pevaluacion;
                        $teacherId = (int) ($pev?->profesor_id ?? 0);
                        if ($teacherId > 0) {
                            $teacherSummary[$teacherId] ??= [
                                'name' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
                                'blocks' => 0,
                            ];
                            $teacherSummary[$teacherId]['blocks'] += $this->halfBlockWeight($slot['half'] ?? null);
                        }
                        $assignmentsByPeriod[$period->id][] = [
                            'asignatura' => $pev?->pensum?->asignatura?->name ?? '?',
                            'profesor' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
                            'grupo' => $this->grupoEstableName($pev),
                            'half' => $slot['half'] ?? null,
                        ];
                    }
                }

                $shiftGrids = $periods->groupBy('shift_id')->map(function ($shiftPeriods) use ($assignmentsByPeriod): array {
                    return [
                        'shift' => $shiftPeriods->first()->shift,
                        'rows' => $shiftPeriods->groupBy('order_in_day')->sortKeys()->map(function ($dayPeriods) use ($assignmentsByPeriod): array {
                            $rowPeriod = $dayPeriods->first();

                            return [
                                'period' => $rowPeriod,
                                'days' => collect(range(1, 5))->mapWithKeys(function (int $day) use ($dayPeriods, $assignmentsByPeriod): array {
                                    $period = $dayPeriods->firstWhere('day_of_week', $day);

                                    return [$day => [
                                        'period' => $period,
                                        'assignments' => $period ? ($assignmentsByPeriod[$period->id] ?? []) : [],
                                    ]];
                                })->all(),
                            ];
                        })->values()->all(),
                    ];
                })->values()->all();

                return [
                    'section' => $section,
                    'shiftGrids' => $shiftGrids,
                    'teacherSummary' => collect($teacherSummary)
                        ->map(function (array $teacher): array {
                            $teacher['blocks'] = $this->normalizeBlockValue((float) $teacher['blocks']);

                            return $teacher;
                        })
                        ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                        ->values()
                        ->all(),
                ];
            })->filter(fn (array $schedule): bool => collect($schedule['shiftGrids'])->flatMap(fn ($grid) => $grid['rows'])->contains(
                fn (array $row): bool => collect($row['days'])->contains(fn (array $day): bool => $day['assignments'] !== [])
            ))->values()->all();

            return ['grado' => $grado, 'sectionSchedules' => $sectionSchedules];
        })->filter(fn (array $schedule): bool => $schedule['sectionSchedules'] !== [])->values();

        return [
            'isPublishedSchedule' => $isPublishedSchedule,
            'gradeSchedules' => $gradeSchedules,
        ];
    }
}
