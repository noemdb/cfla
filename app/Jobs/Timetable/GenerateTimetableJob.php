<?php

namespace App\Jobs\Timetable;

use App\Events\Timetable\TimetableGenerated;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableConflict;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableSlot;
use App\Services\Timetable\Solver\LessonToSchedule;
use App\Services\Timetable\Solver\SolverResult;
use App\Services\Timetable\Solver\TimetableSolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SPEC-TIMETABLE-001 §6 — Motor de asignación en cola.
 *
 * Idempotente por diseño: se identifica con calendar_id + timestamp y guarda el
 * resultado en el propio calendario (preview_payload en dry-run). Si un job de
 * la misma lección/calendario ya corrió, el lock optimista (§15) rechaza el
 * UPDATE y el job termina sin persistir.
 */
class GenerateTimetableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 120;

    public function __construct(
        public int $calendarId,
        public bool $dryRun = false,
    ) {}

    public function handle(): void
    {
        $calendar = TimetableCalendar::query()->find($this->calendarId);

        if (! $calendar) {
            Log::channel('timetable')->warning('GenerateTimetableJob: calendario no encontrado', [
                'correlation_id' => $this->correlationId(),
            ]);

            return;
        }

        // §15 bloqueo optimista: un job stale (versión anterior) no pisa.
        $expectedVersion = $calendar->version;

        Log::channel('timetable')->info('GenerateTimetableJob: inicio', [
            'correlation_id' => $this->correlationId(),
            'calendar_id' => $this->calendarId,
            'dry_run' => $this->dryRun,
        ]);

        $calendar->update(['status' => 'generating']);

        $result = $this->runSolver($calendar);

        if ($this->dryRun) {
            $this->storeDryRunPreview($calendar, $result);
        } else {
            $diff = $this->computeDiff($calendar, $result);
            $this->persist($calendar, $result, $expectedVersion);
            $this->notifyAffected($calendar, $diff);
        }

        Log::channel('timetable')->info('GenerateTimetableJob: fin', [
            'correlation_id' => $this->correlationId(),
            'calendar_id' => $this->calendarId,
            'dry_run' => $this->dryRun,
            'elapsed_seconds' => round($result->elapsedSeconds, 2),
            'unassigned' => count($result->unassigned),
            'timed_out' => $result->timedOut,
        ]);

        broadcast(new TimetableGenerated($calendar->id, $this->dryRun, $result));
    }

    private function runSolver(TimetableCalendar $calendar): SolverResult
    {
        $lessons = TimetableLesson::query()
            ->with('pevaluacion', 'pevaluacion.pensum.asignatura', 'pevaluacion.seccion', 'pevaluacion.profesor')
            ->where('calendar_id', $calendar->id)
            ->get();

        $dto = [];

        foreach ($lessons as $lesson) {
            $pev = $lesson->pevaluacion;

            if (! $pev || ! $pev->seccion || ! $pev->profesor) {
                continue;
            }

            // Lecciones locked: sus períodos ya fijados (slots locked).
            $lockedPeriods = $lesson->locked
                ? $lesson->slots()->where('locked', true)->pluck('period_id')->map(fn ($v) => (int) $v)->all()
                : [];

            $dto[] = new LessonToSchedule(
                lessonId: $lesson->id,
                seccionId: $pev->seccion_id,
                profesorId: $pev->profesor_id,
                shiftId: $lesson->shift_id,
                blocksT: (int) $lesson->weekly_blocks_t,
                blocksP: (int) $lesson->weekly_blocks_p,
                roomTypeRequired: $lesson->room_type_required,
                priority: (int) $lesson->priority,
                locked: (bool) $lesson->locked,
                lockedPeriodIds: $lockedPeriods,
            );
        }

        $availableByTeacher = $this->buildAvailablePeriods($calendar, $dto);
        $roomsByType = $this->buildRoomsByType($calendar);
        $periodMeta = $this->buildPeriodMeta($calendar);

        return (new TimetableSolver($dto, $availableByTeacher, $roomsByType, $periodMeta))
            ->solve();
    }

    /**
     * Períodos disponibles por docente: turno de la lección + disponibilidad
     * (timetable_teacher_availability) + sin recreos.
     *
     * @param  LessonToSchedule[]  $lessons
     * @return array<int, list<int>>
     */
    private function buildAvailablePeriods(TimetableCalendar $calendar, array $lessons): array
    {
        $periods = $calendar->periods()
            ->where('is_break', false)
            ->get(['id', 'shift_id'])
            ->groupBy('shift_id')
            ->map(fn ($group) => $group->pluck('id')->values()->all());

        // fallback: si la tabla de períodos no distingue turno, usar todos.
        $byShift = $periods->toArray();

        $availability = $calendar->availabilities()->get()->groupBy('profesor_id');

        $result = [];
        foreach ($lessons as $lesson) {
            $profesorId = $lesson->profesorId;
            $shiftId = $lesson->shiftId;

            $periodsForShift = $byShift[$shiftId] ?? ($periods->flatten()->all() ?: []);

            $avail = $availability->get($profesorId, collect());
            $availByPeriod = $avail->keyBy('period_id');

            $available = array_values(array_filter(
                $periodsForShift,
                fn (int $pId) => ! isset($availByPeriod[$pId]) || $availByPeriod[$pId]->is_available,
            ));

            $result[$profesorId] = $available;
        }

        return $result;
    }

    /**
     * Aulas por tipo.
     *
     * @return array<string, list<int>>
     */
    private function buildRoomsByType(TimetableCalendar $calendar): array
    {
        $rooms = TimetableRoom::query()
            ->where('status_active', true)
            ->get();

        $byType = [];
        foreach ($rooms as $room) {
            $byType[$room->type][] = (int) $room->id;
        }

        return $byType;
    }

    /**
     * @return array<int, array{day: int, order: int}>
     */
    private function buildPeriodMeta(TimetableCalendar $calendar): array
    {
        return $calendar->periods()->get()
            ->mapWithKeys(fn ($p) => [(int) $p->id => [
                'day' => (int) $p->day_of_week,
                'order' => (int) $p->order_in_day,
            ]])
            ->all();
    }

    private function storeDryRunPreview(TimetableCalendar $calendar, SolverResult $result): void
    {
        $calendar->update([
            'preview_payload' => [
                'generated_at' => now()->toIso8601String(),
                'dry_run' => true,
                'timed_out' => $result->timedOut,
                'elapsed_seconds' => round($result->elapsedSeconds, 2),
                'assignment' => $this->serializeAssignment($result),
                'unassigned' => $result->unassigned,
            ],
            'status' => 'draft',
        ]);
    }

    /**
     * Persiste la asignación en una transacción (ADR-TT-002) con el bloqueo
     * optimista del §15. En una regeneración no-dry-run el diff contra el
     * preview previo no aplica: el preview siempre se confirma explícitamente.
     */
    private function persist(TimetableCalendar $calendar, SolverResult $result, int $expectedVersion): void
    {
        DB::transaction(function () use ($calendar, $result, $expectedVersion) {
            $updated = TimetableCalendar::query()
                ->where('id', $calendar->id)
                ->where('version', $expectedVersion)
                ->update([
                    'version' => $expectedVersion + 1,
                    'status' => 'active',
                    'quality_score' => $this->qualityScore($result),
                    'preview_payload' => null,
                ]);

            if ($updated === 0) {
                Log::channel('timetable')->warning('GenerateTimetableJob: conflicto de versión, no persiste', [
                    'correlation_id' => $this->correlationId(),
                    'calendar_id' => $this->calendarId,
                    'expected_version' => $expectedVersion,
                ]);

                return;
            }

            TimetableSlot::query()->where('calendar_id', $calendar->id)->delete();
            TimetableConflict::query()->where('calendar_id', $calendar->id)->delete();

            foreach ($result->assignment as $lessonId => $slots) {
                $lesson = TimetableLesson::query()
                    ->with('pevaluacion')
                    ->find($lessonId);

                if (! $lesson || ! $lesson->pevaluacion) {
                    continue;
                }

                foreach ($slots as $slot) {
                    TimetableSlot::create([
                        'calendar_id' => $calendar->id,
                        'lesson_id' => $lessonId,
                        'period_id' => $slot->periodId,
                        'profesor_id' => $lesson->pevaluacion->profesor_id,
                        'seccion_id' => $lesson->pevaluacion->seccion_id,
                        'room_id' => $slot->roomId,
                        'locked' => $lesson->locked,
                        'is_manual_override' => false,
                    ]);
                }
            }

            foreach ($result->unassigned as $lessonId) {
                TimetableConflict::create([
                    'calendar_id' => $calendar->id,
                    'lesson_id' => $lessonId,
                    'period_id' => null,
                    'type' => 'unassigned',
                    'details' => ['reason' => 'Sin combinación viable (solver).'],
                ]);
            }
        });
    }

    /**
     * @return array<string, list<array{period_id: int, room_id: int|null, is_practical: bool}>>
     */
    private function serializeAssignment(SolverResult $result): array
    {
        $out = [];
        foreach ($result->assignment as $lessonId => $slots) {
            $out[(string) $lessonId] = array_map(
                fn ($s) => [
                    'period_id' => $s->periodId,
                    'room_id' => $s->roomId,
                    'is_practical' => $s->isPractical,
                ],
                $slots,
            );
        }

        return $out;
    }

    private function qualityScore(SolverResult $result): float
    {
        $total = count($result->assignment) + count($result->unassigned);

        return $total === 0 ? 0.0 : (count($result->assignment) / $total) * 100;
    }

    /**
     * SPEC-TIMETABLE-001 §10 — Calcula el diff real entre el horario vigente
     * (slots persistidos) y el resultado del solver, para que las
     * notificaciones (§15 "diff antes de aplicar") reflejen SOLO los cambios.
     *
     * @return array<string, mixed>
     */
    private function computeDiff(TimetableCalendar $calendar, SolverResult $result): array
    {
        // Vigente: lesson_id => set de period_ids.
        $oldByLesson = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->get(['lesson_id', 'period_id'])
            ->groupBy('lesson_id')
            ->map(fn ($group) => $group->pluck('period_id')->sort()->values()->all())
            ->all();

        $newByLesson = [];
        $profesorByLesson = [];
        foreach ($result->assignment as $lessonId => $slots) {
            $newByLesson[(int) $lessonId] = collect($slots)
                ->map(fn ($s) => (int) $s->periodId)
                ->sort()
                ->values()
                ->all();
        }

        $lessonProfesores = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->with('pevaluacion')
            ->get()
            ->pluck('pevaluacion.profesor_id', 'id')
            ->all();

        $changed = 0;
        $affectedProfesores = [];

        foreach ($newByLesson as $lessonId => $periods) {
            $old = $oldByLesson[$lessonId] ?? [];
            if ($old !== $periods) {
                $changed++;
                $profesorId = $lessonProfesores[$lessonId] ?? null;
                if ($profesorId) {
                    $affectedProfesores[(int) $profesorId] = true;
                }
            }
        }

        // Lecciones sin asignar que antes sí tenían slots → "removidas".
        $removed = 0;
        foreach ($result->unassigned as $lessonId) {
            if (isset($oldByLesson[$lessonId]) && $oldByLesson[$lessonId] !== []) {
                $removed++;
                $profesorId = $lessonProfesores[$lessonId] ?? null;
                if ($profesorId) {
                    $affectedProfesores[(int) $profesorId] = true;
                }
            }
        }

        return [
            'total_lessons' => count($result->assignment),
            'changed' => $changed,
            'removed' => $removed,
            'profesores_afectados' => array_keys($affectedProfesores),
        ];
    }

    /**
     * SPEC-TIMETABLE-001 §10 (mejora) — Notifica a docentes afectados y a
     * coordinación vía un job de cola propio, con el diff real. No bloquea la
     * persistencia ni el request.
     *
     * @param  array<string, mixed>  $diff
     */
    private function notifyAffected(TimetableCalendar $calendar, array $diff): void
    {
        try {
            NotifyTimetableChangesJob::dispatch($calendar->id, $diff);
        } catch (\Throwable $e) {
            Log::channel('timetable')->warning('GenerateTimetableJob: fallo al encolar notificaciones', [
                'correlation_id' => $this->correlationId(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function correlationId(): string
    {
        return $this->calendarId.'-'.now()->format('YmdHis');
    }
}
