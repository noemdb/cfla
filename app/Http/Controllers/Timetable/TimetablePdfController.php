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

    /**
     * Los informes multi-registro (todos los docentes, todos los grados, etc.)
     * generan un único documento HTML que dompdf debe parsear y renderizar; el
     * límite por defecto (128M) se agota fácilmente. Se sube para estas
     * exportaciones puntuales.
     */
    private function raisePdfMemoryLimit(): void
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);
    }

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
        $this->raisePdfMemoryLimit();

        // Este informe también se ofrece desde el selector de calendarios del
        // wizard, donde el calendario puede seguir siendo un draft.
        $calendar = TimetableCalendar::query()->findOrFail($calendarId);
        // Docentes derivados de la Pevaluación (fuente canónica), no de la
        // columna denormalizada `timetable_slots.profesor_id`.
        $profesorIds = $this->viewService->teacherIdsForCalendar($calendar);
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

        // El asistente «light» edita directamente `timetable_slots` (sin
        // preview_payload): con `?source=persisted` el PDF refleja esos slots.
        $forcePersisted = $request->query('source') === 'persisted';

        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;
        $usePersistedSlots = $forcePersisted || $this->shouldUsePersistedSlots($calendar);

        if ($forcePersisted) {
            if (! $calendar->slots()->exists()) {
                abort(404, 'El calendario no tiene horario generado.');
            }
        } elseif (! $calendar->preview_payload && ! $isPublishedSchedule) {
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
        $this->raisePdfMemoryLimit();

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
        $this->raisePdfMemoryLimit();

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
    private function buildPestudioSchedules(TimetableCalendar $calendar, Pestudio $pestudio, bool $forcePersistedSlots = false): array
    {
        $isPublishedSchedule = ! $calendar->preview_payload && $calendar->status === TimetableCalendar::STATUS_ACTIVE;
        $usePersistedSlots = $forcePersistedSlots || $this->shouldUsePersistedSlots($calendar);

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
                            'grupo' => $pev?->grupo_estable_id ? $pev?->grupoEstable?->name : null,
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
     * Consolidado de TODOS los P.Estudios con calendario activo del lapso
     * vigente (un bloque por P.Estudio). Admite `?format=pdf|html|xls`.
     */
    public function previewAllPestudios(Request $request)
    {
        $this->raisePdfMemoryLimit();

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

            $schedules = $this->buildPestudioSchedules($calendar, $pestudio, forcePersistedSlots: true);
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
        $fecha = now()->isoFormat('DD [de] MMMM [de] YYYY');

        return match (strtolower((string) $request->query('format', 'pdf'))) {
            'html' => response()->view('pdfs.timetable.preview-all-pestudios', [
                'lapso' => $lapso,
                'pestudiosData' => $pestudiosData,
                'institucion' => $institucion,
                'fecha' => $fecha,
            ]),
            'xls', 'csv' => $this->allPestudiosXls($lapso, $pestudiosData, $institucion, $fecha),
            default => $this->allPestudiosPdf($lapso, $pestudiosData, $institucion, $fecha),
        };
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{calendar:\App\Models\app\Timetable\TimetableCalendar, pestudio:\App\Models\app\Academy\Pestudio, isPublishedSchedule:bool, gradeSchedules:\Illuminate\Support\Collection<int, array>}>  $pestudiosData
     */
    private function allPestudiosPdf(Lapso $lapso, \Illuminate\Support\Collection $pestudiosData, \App\Models\app\Entity\Institucion $institucion, string $fecha)
    {
        $pdf = Pdf::loadView('pdfs.timetable.preview-all-pestudios', [
            'lapso' => $lapso,
            'pestudiosData' => $pestudiosData,
            'institucion' => $institucion,
            'fecha' => $fecha,
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('horarios-todos-pestudios.pdf');
    }

    /**
     * XLSX (real, con estilo) que replica lo más fielmente posible la estructura
     * y paleta de colores del PDF «todos los P.Estudios»: encabezado de
     * institución en teal, título del P.Estudio, rótulos de nivel/sección en
     * azul, grillas turno × días con recreos resaltados y el resumen de
     * profesores por sección.
     *
     * @param  \Illuminate\Support\Collection<int, array{calendar:\App\Models\app\Timetable\TimetableCalendar, pestudio:\App\Models\app\Academy\Pestudio, isPublishedSchedule:bool, gradeSchedules:\Illuminate\Support\Collection<int, array>}>  $pestudiosData
     */
    private function allPestudiosXls(Lapso $lapso, \Illuminate\Support\Collection $pestudiosData, \App\Models\app\Entity\Institucion $institucion, string $fecha)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Una hoja por P.Estudio (calendario), cada una con su encabezado para
        // que resulte independiente, igual que cada bloque del PDF. La primera
        // reutiliza la hoja activa por defecto; las restantes se crean nuevas.
        $usedTitles = [];
        $first = true;
        foreach ($pestudiosData as $pestudioData) {
            $sheet = $first ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $first = false;

            $pestudio = $pestudioData['pestudio'];
            $sheet->setTitle($this->uniqueSheetTitle((string) $pestudio->name, $usedTitles));

            $this->writePestudioSheet($sheet, $pestudioData, $lapso, $institucion, $fecha);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'horarios-todos-pestudios-'.now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8',
        ]);
    }

    /**
     * Nombre de hoja válido de Excel: ≤31 caracteres y sin caracteres
     * reservados. Garantiza unicidad añadiendo un sufijo numérico.
     *
     * @param  array<int|string, string>  $usedTitles
     */
    private function uniqueSheetTitle(string $name, array &$usedTitles): string
    {
        $clean = preg_replace('/[\\\\\/\?\*\[\]:]/', ' ', $name);
        $clean = trim(mb_substr((string) $clean, 0, 31));

        if ($clean === '') {
            $clean = 'P.Estudio';
        }

        $base = $clean;
        $candidate = $base;
        $i = 2;
        while (isset($usedTitles[$candidate])) {
            $suffix = ' ('.$i.')';
            $candidate = mb_substr($base, 0, 31 - mb_strlen($suffix)).$suffix;
            $i++;
        }

        $usedTitles[$candidate] = true;

        return $candidate;
    }

    /**
     * Escribe el encabezado general + el bloque de horarios de un P.Estudio en
     * la hoja indicada, replicando la paleta del PDF.
     *
     * @param  array{calendar:\App\Models\app\Timetable\TimetableCalendar, pestudio:\App\Models\app\Academy\Pestudio, isPublishedSchedule:bool, gradeSchedules:\Illuminate\Support\Collection<int, array>}  $pestudioData
     */
    private function writePestudioSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $pestudioData, Lapso $lapso, \App\Models\app\Entity\Institucion $institucion, string $fecha): void
    {
        // Paleta espejo del PDF (ver preview-all-pestudios.blade.php).
        $teal = 'FF0D9488';
        $ink = 'FF1A1A2E';
        $grayText = 'FF6B7280';
        $grayStrong = 'FF374151';
        $gradeBg = 'FFEFF6FF';
        $gradeBorder = 'FFBFDBFE';
        $gradeText = 'FF1D4ED8';
        $sectionText = 'FF047857';
        $timeBg = 'FFF8FAFC';
        $breakBg = 'FFFFFBEB';
        $breakText = 'FFA16207';
        $gridBorder = 'FFD1D5DB';
        $summaryHeader = 'FF475569';

        $styleTealHeader = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => $teal]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $col = 1;

        // Encabezado general: institución / título / lapso.
        $sheet->mergeCellsByColumnAndRow($col, 1, $col + 5, 1);
        $sheet->setCellValue([$col, 1], mb_strtoupper((string) ($institucion?->name ?? 'INSTITUCIÓN EDUCATIVA')));
        $sheet->getStyle([$col, 1])->getFont()->setBold(true)->setSize(14)->getColor()->setARGB($teal);
        $sheet->getStyle([$col, 1])->getAlignment()->setHorizontal('center');

        $sheet->mergeCellsByColumnAndRow($col, 2, $col + 5, 2);
        $sheet->setCellValue([$col, 2], 'HORARIOS DE CLASES — TODOS LOS P.ESTUDIOS');
        $sheet->getStyle([$col, 2])->getFont()->setBold(true)->setSize(11)->getColor()->setARGB($grayStrong);
        $sheet->getStyle([$col, 2])->getAlignment()->setHorizontal('center');

        $sheet->mergeCellsByColumnAndRow($col, 3, $col + 5, 3);
        $sheet->setCellValue([$col, 3], ($lapso->name ?? '').' · '.$fecha);
        $sheet->getStyle([$col, 3])->getFont()->setSize(9)->getColor()->setARGB($grayText);
        $sheet->getStyle([$col, 3])->getAlignment()->setHorizontal('center');

        $sheet->getColumnDimension('A')->setWidth(18);
        foreach (['B', 'C', 'D', 'E', 'F'] as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setWidth(22);
        }

        $pestudio = $pestudioData['pestudio'];
        $calendar = $pestudioData['calendar'];

        // ── Info del calendario en el encabezado ──────────────────────────
        $statusLabel = match ($calendar->status) {
            'active' => 'Activo',
            'draft' => 'Borrador',
            'generating' => 'Generando',
            'archived' => 'Archivado',
            default => ucfirst((string) $calendar->status),
        };
        $quality = $calendar->quality_score !== null
            ? trim(rtrim(rtrim((string) $calendar->quality_score, '0'), '.'), ' ').'%'
            : '—';
        $strategyLabel = ($calendar->strategy ?? 'optimized') === 'legacy' ? 'Legacy' : 'Optimizado';

        $sheet->mergeCellsByColumnAndRow($col, 4, $col + 5, 4);
        $sheet->setCellValue([$col, 4], 'Calendario: '.$calendar->name.'   ·   Estado: '.$statusLabel.'   ·   Versión: v'.$calendar->version.'   ·   Calidad: '.$quality);
        $sheet->getStyle([$col, 4])->getFont()->setBold(true)->setSize(8)->getColor()->setARGB($grayStrong);
        $sheet->getStyle([$col, 4])->getAlignment()->setHorizontal('left');

        $sheet->mergeCellsByColumnAndRow($col, 5, $col + 5, 5);
        $sheet->setCellValue([$col, 5], 'Estrategia: '.$strategyLabel.'   ·   Bloques: '.$calendar->period_minutes.' min   ·   Registrado: '.$calendar->created_at?->isoFormat('DD/MM/YYYY HH:mm').'   ·   Actualizado: '.$calendar->updated_at?->isoFormat('DD/MM/YYYY HH:mm'));
        $sheet->getStyle([$col, 5])->getFont()->setSize(8)->getColor()->setARGB($grayText);
        $sheet->getStyle([$col, 5])->getAlignment()->setHorizontal('left');

        $row = 7;

        // Título del P.Estudio (teal, a sangre completa).
        $sheet->mergeCellsByColumnAndRow($col, $row, $col + 5, $row);
        $sheet->setCellValue([$col, $row], 'P.Estudio: '.$pestudio->name.'   ·   '.$calendar->name);
        $sheet->getStyle([$col, $row])->applyFromArray($styleTealHeader);
        $sheet->getStyle([$col, $row])->getAlignment()->setHorizontal('left');
        $sheet->getRowDimension($row)->setRowHeight(20);
        $row++;

        foreach ($pestudioData['gradeSchedules'] as $gradeSchedule) {
            $grado = $gradeSchedule['grado'];

            foreach ($gradeSchedule['sectionSchedules'] as $schedule) {
                $seccion = $schedule['section'];

                // Rótulo nivel/sección (azul).
                $sheet->mergeCellsByColumnAndRow($col, $row, $col + 5, $row);
                $sheet->setCellValue([$col, $row], 'Nivel '.$grado->name.'  ·  Sección '.$seccion->name);
                $sheet->getStyle([$col, $row])->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => $gradeText], 'size' => 10],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => $gradeBg]],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $gradeBorder]]],
                    'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;

                foreach ($schedule['shiftGrids'] as $shiftGrid) {
                    // Encabezado de turno + grilla.
                    $sheet->mergeCellsByColumnAndRow($col, $row, $col + 5, $row);
                    $sheet->setCellValue([$col, $row], 'Turno: '.($shiftGrid['shift']->name ?? 'Turno '.$shiftGrid['shift']->id));
                    $sheet->getStyle([$col, $row])->getFont()->setBold(true)->setSize(9)->getColor()->setARGB($sectionText);
                    $row++;

                    // Cabecera de días.
                    $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                    $sheet->setCellValue([$col, $row], 'Hora');
                    foreach ($days as $i => $day) {
                        $sheet->setCellValue([$col + 1 + $i, $row], $day);
                    }
                    $sheet->getStyle([$col, $row, $col + 5, $row])->applyFromArray($styleTealHeader);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                    $row++;

                    foreach ($shiftGrid['rows'] as $gridRow) {
                        $period = $gridRow['period'];
                        $isBreak = (bool) $period->is_break;

                        $sheet->setCellValue([$col, $row], substr((string) $period->start_time, 0, 5).'–'.substr((string) $period->end_time, 0, 5));
                        $sheet->getStyle([$col, $row])->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['argb' => $grayStrong], 'size' => 9],
                            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => $timeBg]],
                            'alignment' => ['horizontal' => 'center', 'vertical' => 'top'],
                            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $gridBorder]]],
                        ]);

                        foreach ($gridRow['days'] as $day) {
                            if (! $day['period']) {
                                continue;
                            }

                            $cellCol = $col + 1 + ((int) $day['period']->day_of_week ?? 0) - 1;
                            if ($cellCol < $col + 1 || $cellCol > $col + 5) {
                                continue;
                            }

                            if ($isBreak) {
                                $sheet->setCellValue([$cellCol, $row], 'RECESO');
                                $sheet->getStyle([$cellCol, $row])->applyFromArray([
                                    'font' => ['bold' => true, 'color' => ['argb' => $breakText], 'size' => 8],
                                    'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => $breakBg]],
                                    'alignment' => ['horizontal' => 'center', 'vertical' => 'top'],
                                    'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $gridBorder]]],
                                ]);
                                continue;
                            }

                            $lines = [];
                            foreach ($day['assignments'] as $cell) {
                                $lines[] = $cell['asignatura'].($cell['profesor'] ? ' · '.$cell['profesor'] : '');
                            }
                            $sheet->setCellValue([$cellCol, $row], implode("\n", $lines) ?: null);
                            $sheet->getStyle([$cellCol, $row])->applyFromArray([
                                'font' => ['size' => 8, 'color' => ['argb' => $ink]],
                                'alignment' => ['horizontal' => 'left', 'vertical' => 'top', 'wrapText' => true],
                                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $gridBorder]]],
                            ]);
                        }

                        $sheet->getRowDimension($row)->setRowHeight(28);
                        $row++;
                    }

                    $row++; // separación entre turnos.
                }

                // Resumen de profesores de la sección.
                if (! empty($schedule['teacherSummary'])) {
                    $sheet->mergeCellsByColumnAndRow($col, $row, $col + 5, $row);
                    $sheet->setCellValue([$col, $row], 'Profesores asociados a la sección ('.count($schedule['teacherSummary']).')');
                    $sheet->getStyle([$col, $row])->getFont()->setBold(true)->setSize(9)->getColor()->setARGB($grayStrong);
                    $row++;

                    // El nombre del profesor se combina en 3 celdas (A:C); el
                    // contador de bloques va en la columna D.
                    $sheet->mergeCellsByColumnAndRow($col, $row, $col + 2, $row);
                    $sheet->setCellValue([$col, $row], 'Profesor');
                    $sheet->setCellValue([$col + 3, $row], 'Bloques asignados');
                    $sheet->getStyle([$col, $row, $col + 3, $row])->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 8],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => $summaryHeader]],
                        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    ]);
                    $row++;

                    foreach ($schedule['teacherSummary'] as $teacher) {
                        $sheet->mergeCellsByColumnAndRow($col, $row, $col + 2, $row);
                        $sheet->setCellValue([$col, $row], $teacher['name'] ?: 'Sin nombre');
                        $sheet->setCellValue([$col + 3, $row], $teacher['blocks']);
                        $sheet->getStyle([$col, $row, $col + 3, $row])->applyFromArray([
                            'font' => ['size' => 8, 'color' => ['argb' => $ink]],
                            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => $gridBorder]]],
                        ]);
                        $row++;
                    }

                    $row++;
                }

                $row++; // separación entre secciones.
            }
        }

        // Pie.
        $sheet->mergeCellsByColumnAndRow($col, $row, $col + 5, $row);
        $sheet->setCellValue([$col, $row], 'Generado el '.$fecha.' · '.($institucion?->name ?? ''));
        $sheet->getStyle([$col, $row])->getFont()->setSize(8)->getColor()->setARGB($grayText);
        $sheet->getStyle([$col, $row])->getAlignment()->setHorizontal('center');
    }

    /**
     * PDF consolidado de los horarios de TODOS los profesores con asignaciones
     * en los calendarios activos del lapso vigente.
     */
    public function allTeachers(Request $request)
    {
        $this->raisePdfMemoryLimit();

        $lapso = Lapso::current();
        if (! $lapso) {
            abort(404, 'No hay lapso vigente.');
        }

        $calendars = TimetableCalendar::query()
            ->forLapso($lapso->id)
            ->active()
            ->with('pestudio.peducativo')
            ->orderBy('pestudio_id')
            ->get();

        // Agrupado por docente y luego por P.Educativo, en un ÚNICO horario por
        // P.Educativo (los P.Estudios que lo componen se fusionan, sin
        // subdividir). Cada profesor aparece una sola vez y todos sus horarios
        // quedan consecutivos. El docente se resuelve desde la Pevaluación
        // (fuente canónica de la grilla), no desde la columna denormalizada
        // `timetable_slots.profesor_id`.
        $teachers = [];

        foreach ($calendars as $calendar) {
            $profesorIds = $this->viewService->teacherIdsForCalendar($calendar);

            if ($profesorIds->isEmpty()) {
                continue;
            }

            $profesores = Profesor::query()->whereIn('id', $profesorIds)->get();

            foreach ($profesores as $profesor) {
                $shifts = $this->viewService->teacherShiftSchedules($calendar, (int) $profesor->id);

                if ($shifts === []) {
                    continue;
                }

                $key = (int) $profesor->id;

                if (! isset($teachers[$key])) {
                    $teachers[$key] = [
                        'profesor' => $profesor,
                        'peducativos' => [],
                    ];
                }

                $peducativo = $calendar->pestudio?->peducativo;
                $peducativoKey = (int) ($peducativo?->id ?? 0);

                if (! isset($teachers[$key]['peducativos'][$peducativoKey])) {
                    $teachers[$key]['peducativos'][$peducativoKey] = [
                        'peducativo' => $peducativo,
                        'schedules' => [],
                    ];
                }

                // Se acumulan los horarios de cada P.Estudio para fusionarlos.
                $teachers[$key]['peducativos'][$peducativoKey]['schedules'][] = $shifts;
            }
        }

        // Fusiona los horarios de todos los P.Estudios de cada P.Educativo y
        // ordena los P.Educativos por `order` y nombre.
        foreach ($teachers as &$teacher) {
            foreach ($teacher['peducativos'] as &$peducativoData) {
                $peducativoData['schedules'] = $this->viewService->mergePeducativoSchedules(...$peducativoData['schedules']);
            }
            unset($peducativoData);

            uasort($teacher['peducativos'], function (array $a, array $b): int {
                $orderA = (int) ($a['peducativo']?->order ?? 0);
                $orderB = (int) ($b['peducativo']?->order ?? 0);

                return $orderA <=> $orderB
                    ?: strcasecmp((string) ($a['peducativo']?->name ?? ''), (string) ($b['peducativo']?->name ?? ''));
            });
        }
        unset($teacher);

        // Orden por CI del docente (natural). Los que no tienen CI van al final.
        $teachersData = collect($teachers)
            ->sortBy(
                function (array $row): string {
                    $ci = trim((string) ($row['profesor']->ci_profesor ?? ''));

                    return $ci === '' ? '~' : mb_strtoupper($ci);
                },
                SORT_NATURAL,
            )
            ->values();

        if ($teachersData->isEmpty()) {
            abort(404, 'No hay docentes con asignaciones en los calendarios activos del lapso vigente.');
        }

        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();

        // Orientación del print: portrait (por defecto) o landscape.
        $orientation = $request->query('orientation') === 'landscape' ? 'landscape' : 'portrait';

        // Cantidad de horarios (docentes) por página impresa: 2 por defecto,
        // acotado a 1..6 para evitar tarjetas ilegibles.
        $perPage = (int) $request->query('per_page', 2);
        $perPage = max(1, min(6, $perPage));

        // Se renderiza como HTML (no PDF): el consolidado de todos los docentes
        // del lapso genera un documento tan grande que dompdf agota la memoria
        // en el servidor. El navegador renderiza el HTML sin ese límite.
        return view('timetable.teachers-all', [
            'lapso' => $lapso,
            'teachersData' => $teachersData,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'orientation' => $orientation,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Totalización de bloques por docente en los calendarios activos del lapso
     * vigente: nombre, CI, total de bloques asignados y horas académicas
     * (2 × bloques).
     */
    public function teacherBlockTotals(Request $request)
    {
        $this->raisePdfMemoryLimit();

        $lapso = Lapso::current();
        if (! $lapso) {
            abort(404, 'No hay lapso vigente.');
        }

        $calendarIds = $this->activeCalendarIdsForLapso($lapso->id, $request->query('peducativo'));

        if ($calendarIds->isEmpty()) {
            abort(404, 'No hay calendarios activos en el lapso vigente.');
        }

        $rows = $this->teacherBlockTotalsRows($calendarIds);

        if ($rows->isEmpty()) {
            abort(404, 'No hay docentes con bloques asignados en los calendarios activos del lapso vigente.');
        }

        return match (strtolower((string) $request->query('format', 'pdf'))) {
            'xls', 'csv' => $this->teacherBlockTotalsCsv($rows, $lapso),
            'html' => $this->teacherBlockTotalsHtml($rows, $lapso),
            default => $this->teacherBlockTotalsPdf($rows, $lapso),
        };
    }

    /**
     * Calendarios activos del lapso, opcionalmente acotados a los P.Estudios de
     * un P.Educativo (filtro del modal de totalización).
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function activeCalendarIdsForLapso(int $lapsoId, $peducativoId = null): \Illuminate\Support\Collection
    {
        $query = TimetableCalendar::query()->forLapso($lapsoId)->active();

        if (filled($peducativoId)) {
            $pestudioIds = Pestudio::query()
                ->where('peducativo_id', (int) $peducativoId)
                ->pluck('id');

            if ($pestudioIds->isEmpty()) {
                return collect();
            }

            $query->whereIn('pestudio_id', $pestudioIds);
        }

        return $query->pluck('id');
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{name:string, ci:string, blocks:int, hours:int}>  $rows
     */
    private function teacherBlockTotalsPdf(\Illuminate\Support\Collection $rows, Lapso $lapso)
    {
        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();
        $pdf = Pdf::loadView('pdfs.timetable.teacher-block-totals', [
            'rows' => $rows,
            'lapso' => $lapso,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'totalBlocks' => $rows->sum('blocks'),
            'totalHours' => $rows->sum('hours'),
        ]);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('totalizacion-bloques-docentes.pdf');
    }

    /**
     * XLS (CSV UTF-8 con BOM, abre en Excel). Incluye las columnas
     * «Horas Administrativa» y «Horas Formativas» (vacías, para llenado manual).
     *
     * @param  \Illuminate\Support\Collection<int, array{name:string, ci:string, blocks:int, hours:int}>  $rows
     */
    private function teacherBlockTotalsCsv(\Illuminate\Support\Collection $rows, Lapso $lapso)
    {
        $filename = 'totalizacion-bloques-docentes-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');

            // BOM para Excel.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Docente', 'C.I.', 'Bloques', 'Horas académicas',
                'Horas Administrativas', 'Horas Formación',
            ]);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['name'],
                    $row['ci'],
                    $row['blocks'],
                    $row['hours'],
                    '',
                    '',
                ]);
            }

            fputcsv($out, [
                'TOTAL ('.count($rows).' docentes)',
                '',
                $rows->sum('blocks'),
                $rows->sum('hours'),
                '',
                '',
            ]);

            fclose($out);
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array{name:string, ci:string, blocks:int, hours:int}>  $rows
     */
    private function teacherBlockTotalsHtml(\Illuminate\Support\Collection $rows, Lapso $lapso)
    {
        $institucion = \App\Models\app\Entity\Institucion::orderByDesc('created_at')->first();

        return response()->view('pdfs.timetable.teacher-block-totals', [
            'rows' => $rows,
            'lapso' => $lapso,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            'totalBlocks' => $rows->sum('blocks'),
            'totalHours' => $rows->sum('hours'),
        ]);
    }

    /**
     * Bloques por docente de los calendarios indicados.
     *
     * Regla institucional: **un bloque por celda visual ocupada**. La celda que
     * ve el usuario es la de la grilla fusionada del P.Educativo:
     * (P.Educativo, turno, día, orden de bloque). Un docente que en el mismo
     * bloque atiende varias secciones (docente compartido), sub-grupos en
     * paralelo (`is_half_group`) o el mismo bloque en varios P.Estudios del
     * mismo P.Educativo cuenta **una sola vez**.
     *
     * - Docente canónico: `pevaluacion.profesor_id`, con respaldo en
     *   `timetable_slots.profesor_id` cuando la lección no tiene Pevaluación.
     * - Los recreos no cuentan.
     * - Horas académicas = 2 × bloques (regla institucional).
     *
     * @param  \Illuminate\Support\Collection<int, int|string>  $calendarIds
     * @return \Illuminate\Support\Collection<int, array{name:string, ci:string, blocks:int, hours:int}>
     */
    public function teacherBlockTotalsRows($calendarIds): \Illuminate\Support\Collection
    {
        $slots = TimetableSlot::query()
            ->whereIn('calendar_id', $calendarIds)
            ->whereHas('lesson.pevaluacion.seccion', fn ($query) => $query->where('seccions.status_active', 'true'))
            ->whereHas('lesson.pevaluacion.seccion.grado', fn ($query) => $query->where('grados.status_active', 'true'))
            ->with([
                'lesson:id,allow_shared_teacher,pevaluacion_id',
                'lesson.pevaluacion:id,profesor_id',
                'lesson.pevaluacion.profesor',
                'period:id,shift_id,day_of_week,order_in_day,is_break',
                'calendar:id,pestudio_id',
                'calendar.pestudio:id,peducativo_id',
            ])
            ->get(['id', 'calendar_id', 'period_id', 'profesor_id', 'lesson_id']);

        $byTeacher = [];

        foreach ($slots as $slot) {
            // Los recreos nunca reciben clases; se excluyen por robustez.
            if ((bool) $slot->period?->is_break) {
                continue;
            }

            $pev = $slot->lesson?->pevaluacion;
            $profesorId = (int) ($pev?->profesor_id ?? 0) ?: (int) $slot->profesor_id;

            if ($profesorId <= 0) {
                continue;
            }

            if (! isset($byTeacher[$profesorId])) {
                $profesor = $pev?->profesor;

                $byTeacher[$profesorId] = [
                    'name' => trim(($profesor?->lastname ?? '').' '.($profesor?->name ?? '')) ?: 'Sin nombre',
                    'ci' => (string) ($profesor?->ci_profesor ?? '—'),
                    'cells' => [],
                ];
            }

            // Celda visual = P.Educativo + turno + día + orden de bloque.
            $key = implode('|', [
                (int) ($slot->calendar?->pestudio?->peducativo_id ?? 0),
                (int) ($slot->period?->shift_id ?? 0),
                (int) ($slot->period?->day_of_week ?? 0),
                (int) ($slot->period?->order_in_day ?? 0),
            ]);

            $byTeacher[$profesorId]['cells'][$key] = true;
        }

        return collect($byTeacher)
            ->map(function (array $row): array {
                $blocks = count($row['cells']);

                return [
                    'name' => $row['name'],
                    'ci' => $row['ci'],
                    'blocks' => $blocks,
                    'hours' => $blocks * 2,
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Formato tipo horario por asignaturas asociadas a un área de conocimiento.
     *
     * Una grilla días × bloques por cada asignatura del área, cruzando todas
     * las secciones activas del calendario (sección/docente/aula por celda).
     */
    public function previewArea(Request $request, $calendarId, $areaId)
    {
        $this->raisePdfMemoryLimit();

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
