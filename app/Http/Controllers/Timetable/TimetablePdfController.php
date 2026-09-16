<?php

namespace App\Http\Controllers\Timetable;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableSlot;
use App\Services\Timetable\TimetableViewService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function teachers(Request $request, $calendarId)
    {
        // Este informe también se ofrece desde el selector de calendarios del
        // wizard, donde el calendario puede seguir siendo un draft.
        $calendar = TimetableCalendar::query()->findOrFail($calendarId);
        $profesorIds = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereNotNull('profesor_id')
            ->whereHas('lesson.pevaluacion.seccion', function ($query): void {
                $query->where('status_active', 'true')
                    ->whereHas('grado', fn ($grado) => $grado->where('status_active', 'true'));
            })
            ->distinct()
            ->pluck('profesor_id');
        $profesores = Profesor::query()
            ->whereIn('id', $profesorIds)
            ->orderBy('lastname')
            ->orderBy('name')
            ->get();
        $institucion = \App\Models\app\Entity\Institucion::orderBy('created_at', 'DESC')->first();
        $schedules = $profesores->map(fn (Profesor $profesor): array => [
            'profesor' => $profesor,
            'shifts' => $this->viewService->teacherShiftSchedules($calendar, (int) $profesor->id),
        ])->values();

        $pdf = Pdf::loadView('pdfs.timetable.teachers', [
            'calendar' => $calendar,
            'schedules' => $schedules,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream("horarios-docentes-{$calendar->id}.pdf");
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
        $usePersistedSlots = $this->shouldUsePersistedSlots($calendar);
        if (! $calendar->preview_payload && ! $isPublishedSchedule) {
            abort(404, 'No hay vista previa para este calendario.');
        }

        $assignment = collect($calendar->preview_payload['assignment'] ?? []);
        if ($usePersistedSlots) {
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
        $sections = Seccion::query()
            ->where('grado_id', $grado->id)
            ->with('grado')
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get();
        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;
        $usePersistedSlots = $this->shouldUsePersistedSlots($calendar);

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
            ->whereHas('pevaluacion.seccion', fn ($query) => $query->where('status_active', 'true'))
            ->whereHas('pevaluacion.seccion.grado', fn ($query) => $query->where('status_active', 'true'))
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.profesor', 'pevaluacion.seccion'])
            ->get();

        $sectionSchedules = $sections->map(function (Seccion $section) use (
            $calendar,
            $assignment,
            $periods,
            $lessons,
            $usePersistedSlots
        ): array {
            $sectionLessons = $lessons->filter(fn (TimetableLesson $lesson): bool => (int) $lesson->pevaluacion?->seccion_id === (int) $section->id);
            $sectionAssignment = $assignment;
            if ($usePersistedSlots) {
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

        $schedules = $this->buildPestudioSchedules($calendar, $pestudio);

        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();
        $pdf = Pdf::loadView('pdfs.timetable.preview-pestudio', [
            'calendar' => $calendar,
            'pestudio' => $pestudio,
            'gradeSchedules' => $schedules['gradeSchedules'],
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'isPublishedSchedule' => $schedules['isPublishedSchedule'],
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("horarios-{$pestudio->name}.pdf");
    }

    /**
     * Indica si el horario debe resolverse desde los slots persistidos en BD
     * (calendario publicado o clonado, cuyo `preview_payload` no contiene
     * 'assignment') en lugar del payload de vista previa (dry-run).
     */
    private function shouldUsePersistedSlots(TimetableCalendar $calendar): bool
    {
        return ! is_array($calendar->preview_payload['assignment'] ?? null);
    }

    /**
     * Horarios por grado/sección de un P.ESTUDIO para un calendario (compartido
     * por el PDF de un P.Estudio y el consolidado de todos los P.Estudios).
     *
     * @return array{isPublishedSchedule: bool, gradeSchedules: \Illuminate\Support\Collection<int, array>}
     */
    private function buildPestudioSchedules(TimetableCalendar $calendar, Pestudio $pestudio): array
    {
        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;
        $usePersistedSlots = $this->shouldUsePersistedSlots($calendar);

        if (! $calendar->preview_payload && ! $isPublishedSchedule) {
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
            ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.profesor', 'pevaluacion.seccion.grado'])
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

        return [
            'isPublishedSchedule' => $isPublishedSchedule,
            'gradeSchedules' => $gradeSchedules,
        ];
    }

    /**
     * PDF consolidado de TODOS los P.Estudios con calendario activo del lapso
     * vigente (un bloque por P.Estudio).
     */
    public function previewAllPestudios(Request $request)
    {
        $lapso = Lapso::current();
        if (! $lapso) {
            abort(404, 'No hay lapso vigente.');
        }

        $calendars = TimetableCalendar::query()
            ->forLapso($lapso->id)
            ->active()
            ->with('pestudio')
            ->orderBy('pestudio_id')
            ->get();

        $pestudiosData = $calendars->map(function (TimetableCalendar $calendar): ?array {
            $pestudio = $calendar->pestudio;
            if (! $pestudio) {
                return null;
            }

            if (! $calendar->preview_payload && $calendar->status !== TimetableCalendar::STATUS_ACTIVE) {
                return null;
            }

            $schedules = $this->buildPestudioSchedules($calendar, $pestudio);
            if ($schedules['gradeSchedules']->isEmpty()) {
                return null;
            }

            return [
                'calendar' => $calendar,
                'pestudio' => $pestudio,
                'isPublishedSchedule' => $schedules['isPublishedSchedule'],
                'gradeSchedules' => $schedules['gradeSchedules'],
            ];
        })->filter()->values();

        if ($pestudiosData->isEmpty()) {
            abort(404, 'No hay horarios disponibles para los P.Estudios del lapso vigente.');
        }

        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();
        $pdf = Pdf::loadView('pdfs.timetable.preview-all-pestudios', [
            'lapso' => $lapso,
            'pestudiosData' => $pestudiosData,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('horarios-todos-pestudios.pdf');
    }

    /**
     * PDF consolidado de los horarios de TODOS los profesores con asignaciones
     * en los calendarios activos del lapso vigente.
     */
    public function allTeachers(Request $request)
    {
        $lapso = Lapso::current();
        if (! $lapso) {
            abort(404, 'No hay lapso vigente.');
        }

        $calendars = TimetableCalendar::query()
            ->forLapso($lapso->id)
            ->active()
            ->with('pestudio')
            ->orderBy('pestudio_id')
            ->get();

        $calendarsData = $calendars->map(function (TimetableCalendar $calendar): ?array {
            $profesorIds = TimetableSlot::query()
                ->where('calendar_id', $calendar->id)
                ->whereNotNull('profesor_id')
                ->whereHas('lesson.pevaluacion.seccion', function ($query): void {
                    $query->where('status_active', 'true')
                        ->whereHas('grado', fn ($grado) => $grado->where('status_active', 'true'));
                })
                ->distinct()
                ->pluck('profesor_id');

            $profesores = Profesor::query()
                ->whereIn('id', $profesorIds)
                ->orderBy('lastname')
                ->orderBy('name')
                ->get();

            $schedules = $profesores->map(fn (Profesor $profesor): array => [
                'profesor' => $profesor,
                'shifts' => $this->viewService->teacherShiftSchedules($calendar, (int) $profesor->id),
            ])->values();

            if ($schedules->isEmpty()) {
                return null;
            }

            return [
                'calendar' => $calendar,
                'pestudio' => $calendar->pestudio,
                'schedules' => $schedules,
            ];
        })->filter()->values();

        if ($calendarsData->isEmpty()) {
            abort(404, 'No hay docentes con asignaciones en los calendarios activos del lapso vigente.');
        }

        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();
        $pdf = Pdf::loadView('pdfs.timetable.teachers-all', [
            'lapso' => $lapso,
            'calendarsData' => $calendarsData,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('horarios-docentes-todos.pdf');
    }

    /**
     * Formato tipo horario por asignaturas asociadas a un área de conocimiento.
     *
     * Una grilla días × bloques por cada asignatura del área, cruzando todas
     * las secciones activas del calendario (sección/docente/aula por celda).
     */
    public function previewArea(Request $request, $calendarId, $areaId)
    {
        $calendar = TimetableCalendar::query()->findOrFail($calendarId);
        $area = AreaConocimiento::query()->with('pestudio')->findOrFail($areaId);

        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;
        $usePersistedSlots = $this->shouldUsePersistedSlots($calendar);
        if (! $calendar->preview_payload && ! $isPublishedSchedule) {
            abort(404, 'No hay vista previa para este calendario.');
        }

        $assignment = collect($calendar->preview_payload['assignment'] ?? []);
        if ($usePersistedSlots) {
            $assignment = TimetableSlot::query()
                ->where('calendar_id', $calendar->id)
                ->get(['lesson_id', 'period_id', 'room_id'])
                ->groupBy('lesson_id')
                ->map(fn ($slots) => $slots->map(fn ($slot) => [
                    'period_id' => (int) $slot->period_id,
                    'room_id' => (int) ($slot->room_id ?? 0),
                ])->values()->all());
        }

        $periods = TimetablePeriod::query()
            ->where('calendar_id', $calendar->id)
            ->with('shift')
            ->orderBy('day_of_week')
            ->orderBy('order_in_day')
            ->get()
            ->keyBy('id');

        // Asignaturas asociadas al área de conocimiento (pivote campo_conocimientos).
        $asignaturaIds = DB::table('campo_conocimientos')
            ->where('area_conocimiento_id', $area->id)
            ->pluck('asignatura_id')
            ->unique()
            ->values();

        $asignaturas = Asignatura::query()
            ->whereIn('id', $asignaturaIds)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $lessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('pevaluacion.seccion', fn ($query) => $query->where('status_active', 'true'))
            ->whereHas('pevaluacion.pensum.asignatura', fn ($query) => $query->whereIn('asignaturas.id', $asignaturaIds))
            ->with([
                'pevaluacion.pensum.asignatura',
                'pevaluacion.profesor',
                'pevaluacion.seccion.grado',
                'pevaluacion.grupoEstable',
            ])
            ->get();

        $roomIds = collect($assignment)
            ->flatMap(fn ($slots) => collect($slots)->pluck('room_id'))
            ->filter()
            ->unique()
            ->values();
        $rooms = TimetableRoom::query()->whereIn('id', $roomIds)->get()->keyBy('id');

        // Grilla única: todas las asignaturas del área en el mismo horario.
        $assignmentsByPeriod = [];
        foreach ($lessons as $lesson) {
            foreach ($assignment->get((string) $lesson->id, $assignment->get($lesson->id, [])) as $slot) {
                $period = $periods->get((int) ($slot['period_id'] ?? 0));
                if (! $period) {
                    continue;
                }

                $pev = $lesson->pevaluacion;
                $assignmentsByPeriod[$period->id][] = [
                    'asignatura' => $pev?->pensum?->asignatura?->name ?? '?',
                    'profesor' => trim(($pev?->profesor?->lastname ?? '').' '.($pev?->profesor?->name ?? '')),
                    'grado' => $pev?->seccion?->grado?->name ?? '',
                    'seccion' => $pev?->seccion?->name ?? '',
                    'grupo' => $pev?->grupoEstable?->name,
                    'room' => $rooms->get((int) ($slot['room_id'] ?? 0))?->name,
                ];
            }
        }

        // Orden estable por asignatura dentro de cada celda.
        foreach ($assignmentsByPeriod as $periodId => $cells) {
            usort($cells, fn (array $a, array $b): int => strcasecmp($a['asignatura'], $b['asignatura']));
            $assignmentsByPeriod[$periodId] = $cells;
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

        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();
        $pdf = Pdf::loadView('pdfs.timetable.preview-area', [
            'calendar' => $calendar,
            'area' => $area,
            'shiftGrids' => $shiftGrids,
            'asignaturasCount' => $asignaturas->count(),
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'isPublishedSchedule' => $isPublishedSchedule,
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('formato-area-'.Str::slug((string) $area->name).'.pdf');
    }
}
