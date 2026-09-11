<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;

class TimetablePublicationReadinessService
{
    /**
     * @return array{
     *     ready: bool,
     *     assigned: int,
     *     unassigned: int,
     *     hard_conflicts: list<array<string, mixed>>,
     *     warnings: list<array<string, mixed>>,
     *     quality: array{coverage: float, score: float},
     *     manual_override: bool
     * }
     */
    public function evaluate(TimetableCalendar $calendar, array $preview): array
    {
        $lessons = $calendar->lessons()
            ->with('pevaluacion.pensum.asignatura', 'pevaluacion.seccion', 'pevaluacion.profesor')
            ->get()
            ->keyBy('id');
        $periods = $calendar->periods()->get()->keyBy('id');
        $assignment = collect($preview['assignment'] ?? []);
        $unassigned = collect($preview['unassigned'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
        $hardConflicts = [];
        $warnings = [];
        $teacherPeriods = [];
        $sectionPeriods = [];
        $roomPeriods = [];
        $assignedBlocks = 0;
        $requiredBlocks = 0;

        foreach ($lessons as $lesson) {
            if (! $lesson->pevaluacion) {
                $hardConflicts[] = $this->conflict($lesson, 'missing_pevaluacion', null, 0)
                    + [
                        'pevaluacion_id' => (int) $lesson->pevaluacion_id,
                        'resolution' => 'Restaura la Pevaluacion o retira esta lesson huérfana del calendario.',
                    ];

                continue;
            }

            $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
            $requiredBlocks += $required;
            $slots = collect($assignment->get((string) $lesson->id, $assignment->get($lesson->id, [])));
            $lessonAssignedBlocks = $slots->pluck('period_id')->filter()->unique()->count();
            $assignedBlocks += $lessonAssignedBlocks;
            if ($required > 0 && $lessonAssignedBlocks !== $required) {
                $hardConflicts[] = $this->conflict($lesson, 'incomplete_assignment', null)
                    + [
                        'required_blocks' => $required,
                        'assigned_blocks' => $lessonAssignedBlocks,
                        'missing_blocks' => max(0, $required - $lessonAssignedBlocks),
                    ];
            }

            foreach ($slots as $slot) {
                $periodId = (int) ($slot['period_id'] ?? 0);
                $period = $periods->get($periodId);
                if (! $period) {
                    $hardConflicts[] = $this->conflict($lesson, 'period_not_in_calendar', null, $periodId);
                    continue;
                }
                if ($period->is_break) {
                    $hardConflicts[] = $this->conflict($lesson, 'break_period', $period, $periodId);
                }

                $teacherId = (int) ($lesson->pevaluacion?->profesor_id ?? 0);
                $sectionId = (int) ($lesson->pevaluacion?->seccion_id ?? 0);
                $roomId = (int) ($slot['room_id'] ?? 0);
                $key = $periodId.':';

                if ($teacherId && isset($teacherPeriods[$key.$teacherId])) {
                    $hardConflicts[] = $this->conflict($lesson, 'teacher_double_booked', $period, $periodId);
                }
                if ($sectionId && isset($sectionPeriods[$key.$sectionId])) {
                    $existingSectionLesson = $lessons->get($sectionPeriods[$key.$sectionId]);
                    $halfGroupPair = (bool) $lesson->is_half_group
                        && (bool) $existingSectionLesson?->is_half_group;
                    if (! $halfGroupPair) {
                        $hardConflicts[] = $this->conflict($lesson, 'section_double_booked', $period, $periodId);
                    }
                }
                if ($roomId && isset($roomPeriods[$key.$roomId])) {
                    $hardConflicts[] = $this->conflict($lesson, 'room_double_booked', $period, $periodId);
                }

                if ($teacherId) {
                    $teacherPeriods[$key.$teacherId] = true;
                }
                if ($sectionId) {
                    $sectionPeriods[$key.$sectionId] = (int) $lesson->id;
                }
                if ($roomId) {
                    $roomPeriods[$key.$roomId] = true;
                }
            }
        }

        if ($unassigned->isNotEmpty()) {
            $warnings[] = [
                'type' => 'unassigned_lessons',
                'count' => $unassigned->count(),
                'message' => 'Hay lecciones sin asignar.',
            ];
        }

        $coverage = $requiredBlocks > 0 ? round(($assignedBlocks / $requiredBlocks) * 100, 2) : 100.0;
        $score = max(0, round($coverage - (count($hardConflicts) * 10), 2));

        return [
            'ready' => $hardConflicts === [],
            'assigned' => $lessons->filter(function (TimetableLesson $lesson) use ($assignment): bool {
                $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
                $slots = collect($assignment->get((string) $lesson->id, $assignment->get($lesson->id, [])));

                return $required > 0
                    && $slots->pluck('period_id')->filter()->unique()->count() === $required;
            })->count(),
            'unassigned' => $unassigned->count(),
            'hard_conflicts' => $hardConflicts,
            'warnings' => $warnings,
            'quality' => ['coverage' => $coverage, 'score' => $score],
            'manual_override' => (bool) ($preview['manual_override'] ?? false),
        ];
    }

    private function conflict(
        TimetableLesson $lesson,
        string $type,
        ?TimetablePeriod $period,
        int $periodId = 0,
    ): array
    {
        $subject = $lesson->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura sin nombre';
        $section = $lesson->pevaluacion?->seccion?->name ?? 'Sección sin nombre';
        $teacher = trim(($lesson->pevaluacion?->profesor?->lastname ?? '').' '.($lesson->pevaluacion?->profesor?->name ?? ''));
        $labels = [
            'incomplete_assignment' => 'Asignación incompleta',
            'missing_pevaluacion' => 'Referencia académica huérfana',
            'period_not_in_calendar' => 'Período inválido',
            'break_period' => 'Período de receso',
            'teacher_double_booked' => 'Docente duplicado',
            'section_double_booked' => 'Sección duplicada',
            'room_double_booked' => 'Aula duplicada',
        ];

        return [
            'type' => $type,
            'title' => $labels[$type] ?? 'Conflicto bloqueante',
            'lesson_id' => (int) $lesson->id,
            'subject' => $subject,
            'section' => $section,
            'teacher' => $teacher !== '' ? $teacher : 'Sin docente',
            'period_id' => $periodId,
            'period' => $period
                ? ($period->period_label.' · '.substr((string) $period->start_time, 0, 5).'–'.substr((string) $period->end_time, 0, 5))
                : 'Sin período válido',
        ];
    }
}
