<?php

namespace App\Http\Controllers\Timetable;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableSlot;
use App\Services\Timetable\TimetableViewService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * SPEC-TIMETABLE-001 §8 — Exportación PDF del horario por sección/docente/aula.
 *
 * Mismo patrón que BinnaclePdfController / CatchmentPDFController.
 */
class TimetablePdfController extends Controller
{
    public function __construct(private TimetableViewService $viewService) {}

    public function section(Request $request, $calendarId, $seccionId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $seccion = Seccion::query()->with('grado')->findOrFail($seccionId);

        $grid = $this->viewService->gridForSection($calendar, $seccionId);

        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.timetable.section', [
            'calendar' => $calendar,
            'seccion' => $seccion,
            'grid' => $grid,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horario-{$seccion->name}.pdf");
    }

    public function teacher(Request $request, $calendarId, $profesorId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $profesor = Profesor::query()->findOrFail($profesorId);

        $grid = $this->viewService->gridForTeacher($calendar, $profesorId);

        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.timetable.teacher', [
            'calendar' => $calendar,
            'profesor' => $profesor,
            'grid' => $grid,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horario-{$profesor->lastname}-{$profesor->name}.pdf");
    }

    public function room(Request $request, $calendarId, $roomId)
    {
        $calendar = $this->viewService->activeCalendarOrFail($calendarId);
        $room = TimetableRoom::query()->findOrFail($roomId);

        $grid = $this->viewService->gridForRoom($calendar, $roomId);

        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.timetable.room', [
            'calendar' => $calendar,
            'room' => $room,
            'grid' => $grid,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horario-aula-{$room->code}.pdf");
    }

    /**
     * PREVIEW del Paso 5 — Exporta a PDF el horario previsualizado (preview_payload)
     * de una sección, sin necesidad de publicar el calendario.
     */
    public function previewSection(Request $request, $calendarId, $seccionId)
    {
        $calendar = TimetableCalendar::query()->findOrFail($calendarId);
        $seccion = Seccion::query()->with('grado')->findOrFail($seccionId);

        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;
        if (! $calendar->preview_payload && ! $isPublishedSchedule) {
            abort(404, 'No hay vista previa para este calendario.');
        }

        $assignment = collect($calendar->preview_payload['assignment'] ?? []);
        if ($isPublishedSchedule) {
            $assignment = TimetableSlot::query()
                ->where('calendar_id', $calendar->id)
                ->where('seccion_id', $seccionId)
                ->get(['lesson_id', 'period_id', 'room_id'])
                ->groupBy('lesson_id')
                ->map(fn ($slots) => $slots->map(fn ($slot) => [
                    'period_id' => (int) $slot->period_id,
                    'room_id' => (int) ($slot->room_id ?? 0),
                ])->values()->all());
        }

        // Lecciones de la sección con su asignatura/profesor/grupo.
        $lessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('pevaluacion', fn ($q) => $q->where('seccion_id', $seccionId))
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.profesor', 'pevaluacion.grupoEstable'])
            ->get();

        // Períodos del calendario (id => día/orden/hora), agrupados por turno
        // para conservar la misma separación visual del Step 5.
        $periods = TimetablePeriod::query()
            ->where('calendar_id', $calendar->id)
            ->with('shift')
            ->orderBy('day_of_week')
            ->orderBy('order_in_day')
            ->get()
            ->keyBy('id');

        $assignmentsByPeriod = [];
        $teacherSummary = [];
        foreach ($lessons as $lesson) {
            $lessonAssignments = $assignment->get((string) $lesson->id, $assignment->get($lesson->id, []));
            foreach ($lessonAssignments as $slot) {
                $period = $periods->get((int) ($slot['period_id'] ?? 0));
                if (! $period) {
                    continue;
                }
                $pev = $lesson->pevaluacion;
                $teacherId = (int) ($pev?->profesor_id ?? 0);
                if ($teacherId > 0) {
                    if (! isset($teacherSummary[$teacherId])) {
                        $teacherSummary[$teacherId] = [
                            'name' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
                            'blocks' => 0,
                        ];
                    }
                    $teacherSummary[$teacherId]['blocks']++;
                }
                $assignmentsByPeriod[$period->id][] = [
                    'asignatura' => $pev?->pensum?->asignatura?->name ?? '?',
                    'profesor' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
                    'grupo' => $pev?->grupoEstable?->name,
                    'room_id' => (int) ($slot['room_id'] ?? 0),
                ];
            }
        }

        $shiftGrids = $periods
            ->groupBy('shift_id')
            ->map(function ($shiftPeriods) use ($assignmentsByPeriod) {
                return [
                    'shift' => $shiftPeriods->first()->shift,
                    'rows' => $shiftPeriods
                        ->groupBy('order_in_day')
                        ->sortKeys()
                        ->map(function ($dayPeriods) use ($assignmentsByPeriod) {
                            $rowPeriod = $dayPeriods->first();

                            return [
                                'period' => $rowPeriod,
                                'days' => collect(range(1, 5))->mapWithKeys(function ($day) use ($dayPeriods, $assignmentsByPeriod) {
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

        $teacherSummary = collect($teacherSummary)
            ->filter(fn (array $teacher): bool => $teacher['blocks'] > 0)
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.timetable.preview-section', [
            'calendar' => $calendar,
            'seccion' => $seccion,
            'shiftGrids' => $shiftGrids,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'isPublishedSchedule' => $isPublishedSchedule,
            'teacherSummary' => $teacherSummary,
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("preview-horario-{$seccion->name}.pdf");
    }

    public function previewGrade(Request $request, $calendarId, $gradoId)
    {
        $calendar = TimetableCalendar::query()->findOrFail($calendarId);
        $grado = Grado::query()->findOrFail($gradoId);
        $sections = Seccion::query()->where('grado_id', $grado->id)->with('grado')->orderBy('name')->get();
        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;

        if (! $calendar->preview_payload && ! $isPublishedSchedule) {
            abort(404, 'No hay vista previa para este calendario.');
        }

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
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.profesor', 'pevaluacion.seccion'])
            ->get();

        $sectionSchedules = $sections->map(function (Seccion $section) use (
            $calendar,
            $assignment,
            $periods,
            $lessons,
            $isPublishedSchedule
        ): array {
            $sectionLessons = $lessons->filter(fn (TimetableLesson $lesson): bool => (int) $lesson->pevaluacion?->seccion_id === (int) $section->id);
            $sectionAssignment = $assignment;
            if ($isPublishedSchedule) {
                $sectionAssignment = TimetableSlot::query()
                    ->where('calendar_id', $calendar->id)
                    ->where('seccion_id', $section->id)
                    ->get(['lesson_id', 'period_id', 'room_id'])
                    ->groupBy('lesson_id')
                    ->map(fn ($slots) => $slots->map(fn ($slot) => [
                        'period_id' => (int) $slot->period_id,
                        'room_id' => (int) ($slot->room_id ?? 0),
                    ])->values()->all());
            }

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
                        $teacherSummary[$teacherId]['blocks']++;
                    }
                    $assignmentsByPeriod[$period->id][] = [
                        'asignatura' => $pev?->pensum?->asignatura?->name ?? '?',
                        'profesor' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
                    ];
                }
            }
            $teacherSummary = collect($teacherSummary)
                ->filter(fn (array $teacher): bool => $teacher['blocks'] > 0)
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();

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
                'teacherSummary' => $teacherSummary,
            ];
        })->filter(fn (array $schedule): bool => collect($schedule['shiftGrids'])->flatMap(fn ($grid) => $grid['rows'])->contains(
            fn (array $row): bool => collect($row['days'])->contains(fn (array $day): bool => $day['assignments'] !== [])
        ))->values();

        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();
        $pdf = Pdf::loadView('pdfs.timetable.preview-grade', [
            'calendar' => $calendar,
            'grado' => $grado,
            'sectionSchedules' => $sectionSchedules,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'isPublishedSchedule' => $isPublishedSchedule,
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horarios-{$grado->name}.pdf");
    }

    public function previewPestudio(Request $request, $calendarId, $pestudioId)
    {
        $calendar = TimetableCalendar::query()->findOrFail($calendarId);
        $pestudio = Pestudio::query()->findOrFail($pestudioId);
        $grades = Grado::query()
            ->where('pestudio_id', $pestudio->id)
            ->with(['seccions' => fn ($query) => $query->orderBy('name')])
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;

        if (! $calendar->preview_payload && ! $isPublishedSchedule) {
            abort(404, 'No hay vista previa para este calendario.');
        }

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
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.profesor', 'pevaluacion.seccion.grado'])
            ->get();

        $gradeSchedules = $grades->map(function (Grado $grado) use (
            $calendar,
            $assignment,
            $periods,
            $lessons,
            $isPublishedSchedule
        ): array {
            $sectionSchedules = $grado->seccions->map(function (Seccion $section) use (
                $calendar,
                $assignment,
                $periods,
                $lessons,
                $isPublishedSchedule
            ): array {
                $sectionLessons = $lessons->filter(fn (TimetableLesson $lesson): bool => (int) $lesson->pevaluacion?->seccion_id === (int) $section->id);
                $sectionAssignment = $isPublishedSchedule
                    ? TimetableSlot::query()->where('calendar_id', $calendar->id)->where('seccion_id', $section->id)
                        ->get(['lesson_id', 'period_id', 'room_id'])->groupBy('lesson_id')
                        ->map(fn ($slots) => $slots->map(fn ($slot) => [
                            'period_id' => (int) $slot->period_id,
                            'room_id' => (int) ($slot->room_id ?? 0),
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
                            $teacherSummary[$teacherId]['blocks']++;
                        }
                        $assignmentsByPeriod[$period->id][] = [
                            'asignatura' => $pev?->pensum?->asignatura?->name ?? '?',
                            'profesor' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
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
                    'teacherSummary' => collect($teacherSummary)->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values()->all(),
                ];
            })->filter(fn (array $schedule): bool => collect($schedule['shiftGrids'])->flatMap(fn ($grid) => $grid['rows'])->contains(
                fn (array $row): bool => collect($row['days'])->contains(fn (array $day): bool => $day['assignments'] !== [])
            ))->values()->all();

            return ['grado' => $grado, 'sectionSchedules' => $sectionSchedules];
        })->filter(fn (array $schedule): bool => $schedule['sectionSchedules'] !== [])->values();

        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();
        $pdf = Pdf::loadView('pdfs.timetable.preview-pestudio', [
            'calendar' => $calendar,
            'pestudio' => $pestudio,
            'gradeSchedules' => $gradeSchedules,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'isPublishedSchedule' => $isPublishedSchedule,
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horarios-{$pestudio->name}.pdf");
    }
}
