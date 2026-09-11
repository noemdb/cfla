<?php

namespace App\Jobs\Timetable;

use App\Events\Timetable\TimetableGenerated;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableConflict;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetableSlot;
use App\Services\Timetable\Solver\LessonToSchedule;
use App\Services\Timetable\Solver\SlotCandidate;
use App\Services\Timetable\Solver\SolverResult;
use App\Services\Timetable\Solver\TimetableSolver;
use App\Services\Timetable\TimetableRoomEligibilityService;
use App\Services\Timetable\TimetableAvailabilityService;
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
        public ?array $previewPayload = null,
        public ?array $pevaluacionIds = null,
        public ?array $lessonIds = null,
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
            'strategy' => $calendar->strategy ?: TimetableCalendar::DEFAULT_STRATEGY,
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
        if (! $this->dryRun && $this->previewPayload !== null) {
            return new SolverResult(
                assignment: collect($this->previewPayload['assignment'] ?? [])
                    ->filter(fn (array $slots, $lessonId): bool => $this->lessonIds === null
                        || in_array((int) $lessonId, $this->lessonIds, true))
                    ->mapWithKeys(fn (array $slots, $lessonId) => [
                        (int) $lessonId => collect($slots)->map(fn (array $slot) => new SlotCandidate(
                            periodId: (int) ($slot['period_id'] ?? 0),
                            roomId: ! empty($slot['room_id']) ? (int) $slot['room_id'] : null,
                            isPractical: (bool) ($slot['is_practical'] ?? false),
                        ))
                            ->filter(fn (SlotCandidate $slot) => $slot->periodId > 0)
                            ->unique(fn (SlotCandidate $slot): int => $slot->periodId)
                            ->values()
                            ->all(),
                    ])
                    ->all(),
                unassigned: array_map('intval', $this->previewPayload['unassigned'] ?? []),
            );
        }

        if (($calendar->strategy ?? TimetableCalendar::DEFAULT_STRATEGY) === TimetableCalendar::STRATEGY_LEGACY) {
            $legacyResult = $this->legacyAssignment($calendar);

            if ($legacyResult !== null) {
                return $legacyResult;
            }
        }

        $lessons = TimetableLesson::query()
            ->with('slots', 'pevaluacion', 'pevaluacion.pensum.asignatura', 'pevaluacion.seccion', 'pevaluacion.profesor')
            ->where('calendar_id', $calendar->id)
            ->get();
        $scopedLessonIds = $this->scopedLessonIds($calendar);
        $previousAssignment = $this->previewPayload['assignment'] ?? $calendar->preview_payload['assignment'] ?? [];

        $dto = [];

        foreach ($lessons as $lesson) {
            $pev = $lesson->pevaluacion;

            if (! $pev || ! $pev->seccion || ! $pev->profesor) {
                continue;
            }

            $isInScope = $scopedLessonIds === null || in_array((int) $lesson->id, $scopedLessonIds, true);
            $preservedPeriodIds = [];
            $preassignedSlots = [];

            if (! $isInScope) {
                $preservedPeriodIds = collect($previousAssignment[(string) $lesson->id] ?? $previousAssignment[$lesson->id] ?? [])
                    ->pluck('period_id')
                    ->map(fn ($id) => (int) $id)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                // Unchecked lessons are outside this dry-run scope. They are
                // preserved from the existing preview, but never generated
                // from scratch by the solver.
                if ($preservedPeriodIds === []) {
                    continue;
                }
            }

            $existingAssignment = $previousAssignment[(string) $lesson->id]
                ?? $previousAssignment[$lesson->id]
                ?? null;
            $existingSlots = is_array($existingAssignment) && $existingAssignment !== []
                ? collect($existingAssignment)
                : $lesson->slots;
            $requiredBlocks = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
            $existingPeriodCount = $existingSlots instanceof \Illuminate\Support\Collection
                ? $existingSlots->pluck('period_id')->filter()->unique()->count()
                : collect($existingSlots)->pluck('period_id')->filter()->unique()->count();
            if ($existingPeriodCount > 0 && $existingPeriodCount < $requiredBlocks) {
                $preassignedSlots = collect($existingSlots)
                    ->map(fn ($slot): SlotCandidate => new SlotCandidate(
                        periodId: (int) data_get($slot, 'period_id'),
                        roomId: data_get($slot, 'room_id') ? (int) data_get($slot, 'room_id') : null,
                        isPractical: (bool) data_get($slot, 'is_practical', false),
                    ))
                    ->filter(fn (SlotCandidate $slot): bool => $slot->periodId > 0)
                    ->unique(fn (SlotCandidate $slot): int => $slot->periodId)
                    ->values()
                    ->all();
            }

            // Lecciones locked: sus períodos ya fijados (slots locked).
            // Las lecciones legacy marcadas como medio grupo deben poder
            // reubicarse para formar parejas en una misma celda.
            $lockedPeriodIds = $lesson->slots
                ->where('locked', true)
                ->pluck('period_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
            $hasCompleteLockedAssignment = $lesson->weekly_blocks_t + $lesson->weekly_blocks_p > 0
                && count($lockedPeriodIds) === $lesson->weekly_blocks_t + $lesson->weekly_blocks_p;

            $isLocked = (bool) $lesson->locked
                && $hasCompleteLockedAssignment
                && (
                    ($calendar->strategy ?? TimetableCalendar::DEFAULT_STRATEGY) === TimetableCalendar::STRATEGY_LEGACY
                    || ! (bool) $lesson->is_half_group
                );
            $lockedPeriods = $isLocked ? $lockedPeriodIds : [];
            if (! $isInScope) {
                $lockedPeriods = $preservedPeriodIds;
                $isLocked = count($lockedPeriods) === $lesson->weekly_blocks_t + $lesson->weekly_blocks_p;

                // A partial preserved assignment must remain in the preview,
                // but cannot safely reserve the lesson in this scoped solve.
                if (! $isLocked) {
                    continue;
                }
            }

            $dto[] = new LessonToSchedule(
                lessonId: $lesson->id,
                seccionId: $pev->seccion_id,
                profesorId: $pev->profesor_id,
                shiftId: $lesson->shift_id,
                blocksT: (int) $lesson->weekly_blocks_t,
                blocksP: (int) $lesson->weekly_blocks_p,
                roomTypeRequired: $lesson->room_type_required,
                isHalfGroup: (bool) $lesson->is_half_group,
                priority: (int) $lesson->priority,
                locked: $isLocked,
                lockedPeriodIds: $lockedPeriods,
                grupoEstableId: $pev->grupo_estable_id ? (int) $pev->grupo_estable_id : null,
                preassignedSlots: $preassignedSlots,
            );
        }

        $availableByTeacher = $this->buildAvailablePeriods($calendar, $dto);
        $roomsByType = $this->buildRoomsByType($calendar);
        $periodMeta = $this->buildPeriodMeta($calendar);

        return (new TimetableSolver(
            $dto,
            $availableByTeacher,
            $roomsByType,
            $periodMeta,
            30,
            max(1, (int) ($calendar->max_subjects_per_period ?? 2)),
        ))
            ->solve();
    }

    /**
     * Legacy es una reproducción de las posiciones importadas, no una nueva
     * distribución heurística. Si el calendario aún no tiene slots importados,
     * se devuelve null para permitir el fallback del solver.
     */
    private function legacyAssignment(TimetableCalendar $calendar): ?SolverResult
    {
        $lessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->with('slots')
            ->get();
        $scopedLessonIds = $this->scopedLessonIds($calendar);

        if ($lessons->isEmpty() || ! $lessons->contains(fn (TimetableLesson $lesson) => $lesson->slots->isNotEmpty())) {
            return null;
        }

        $assignment = [];
        $unassigned = [];
        $periodLoad = [];
        $maxSubjectsPerPeriod = max(1, (int) ($calendar->max_subjects_per_period ?? 2));

        foreach ($lessons as $lesson) {
            if ($scopedLessonIds !== null && ! in_array((int) $lesson->id, $scopedLessonIds, true)) {
                continue;
            }
            $lessonSlots = $lesson->slots->values();
            $lessonLoad = $lessonSlots
                ->groupBy(fn (TimetableSlot $slot) => $slot->period_id.':'.$slot->seccion_id)
                ->map->count();
            $exceedsCapacity = $lessonLoad->contains(
                fn (int $count, string $key): bool => ($periodLoad[$key] ?? 0) + $count > $maxSubjectsPerPeriod,
            );

            if ($exceedsCapacity) {
                $unassigned[] = (int) $lesson->id;

                continue;
            }

            foreach ($lessonLoad as $key => $count) {
                $periodLoad[$key] = ($periodLoad[$key] ?? 0) + $count;
            }

            $slots = $lessonSlots
                ->map(fn (TimetableSlot $slot) => new SlotCandidate(
                    periodId: (int) $slot->period_id,
                    roomId: $slot->room_id ? (int) $slot->room_id : null,
                    isPractical: false,
                ))
                ->all();

            if ($slots === []) {
                $unassigned[] = (int) $lesson->id;
            } else {
                $assignment[(int) $lesson->id] = $slots;
            }
        }

        return new SolverResult(
            assignment: $assignment,
            unassigned: $unassigned,
        );
    }

    /**
     * Períodos disponibles por lección: primero el turno configurado y luego
     * los demás turnos del calendario. Así el solver conserva la preferencia
     * del Step 3, pero puede usar otro turno si el preferido queda ocupado.
     *
     * @param  LessonToSchedule[]  $lessons
     * @return array<int, list<int>> lessonId => periodIds
     */
    private function buildAvailablePeriods(TimetableCalendar $calendar, array $lessons): array
    {
        $periods = $calendar->periods()
            ->where('is_break', false)
            ->get(['id', 'shift_id', 'day_of_week', 'order_in_day']);
        $byShift = $periods->groupBy('shift_id')->map(fn ($g) => $g->values()->all())->all();

        $availability = app(TimetableAvailabilityService::class);

        $result = [];
        foreach ($lessons as $lesson) {
            $profesorId = $lesson->profesorId;
            $shiftId = $lesson->shiftId;

            $preferredPeriods = $byShift[$shiftId] ?? [];
            $fallbackPeriods = $lesson->locked
                ? []
                : $periods
                    ->reject(fn ($period) => (int) $period->shift_id === (int) $shiftId)
                    ->values()
                    ->all();
            $periodsForShift = array_merge($preferredPeriods, $fallbackPeriods);
            $result[$lesson->lessonId] = array_values(array_map(
                fn ($p) => $p->id,
                array_filter(
                    $periodsForShift,
                    fn ($p) => $availability->isAvailable(
                        $calendar->id,
                        $profesorId,
                        $p,
                    ),
                ),
            ));
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
        return app(TimetableRoomEligibilityService::class)->idsByType($calendar);
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
        $assignment = $this->serializeAssignment($result);
        $unassigned = $result->unassigned;
        if ($this->pevaluacionIds !== null) {
            $scopedLessonIds = $this->scopedLessonIds($calendar) ?? [];
            $previous = $calendar->preview_payload ?? [];
            $previousAssignment = $previous['assignment'] ?? [];
            $lessonsById = $calendar->lessons()
                ->get(['id', 'weekly_blocks_t', 'weekly_blocks_p'])
                ->keyBy('id');
            $partialUntouchedIds = [];
            $untouchedAssignment = collect($previousAssignment)
                ->filter(function ($slots, $lessonId) use ($scopedLessonIds, $lessonsById, &$partialUntouchedIds): bool {
                    $lessonId = (int) $lessonId;
                    if (in_array($lessonId, $scopedLessonIds, true)) {
                        return false;
                    }

                    $lesson = $lessonsById->get($lessonId);
                    $required = $lesson
                        ? (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p
                        : 0;
                    $assigned = collect($slots)->pluck('period_id')->filter()->unique()->count();
                    if ($required > 0 && $assigned !== $required) {
                        $partialUntouchedIds[] = $lessonId;

                        return false;
                    }

                    return true;
                })
                ->all();
            // Preserve lesson IDs as array keys; merge() reindexes numeric keys
            // and makes the preview grid unable to resolve lesson assignments.
            $assignment = $assignment + $untouchedAssignment;
            $unassigned = collect($previous['unassigned'] ?? [])
                ->filter(fn ($lessonId) => ! in_array((int) $lessonId, $scopedLessonIds, true))
                ->merge($partialUntouchedIds)
                ->merge($unassigned)
                ->map(fn ($lessonId) => (int) $lessonId)
                ->unique()
                ->values()
                ->all();
        }
        $calendar->update([
            'preview_payload' => [
                'generated_at' => now()->toIso8601String(),
                'dry_run' => true,
                'strategy' => $calendar->strategy ?: TimetableCalendar::DEFAULT_STRATEGY,
                'assignment_source' => $calendar->strategy === TimetableCalendar::STRATEGY_LEGACY
                    && $this->hasLegacySlots($calendar) ? 'legacy_slots' : 'solver',
                'max_subjects_per_period' => max(1, (int) ($calendar->max_subjects_per_period ?? 2)),
                'timed_out' => $result->timedOut,
                'elapsed_seconds' => round($result->elapsedSeconds, 2),
                'assignment' => $assignment,
                'unassigned' => $unassigned,
                'assignment_diagnostics' => $this->assignmentDiagnostics($calendar, $assignment),
            ],
            'status' => 'draft',
        ]);
    }

    /**
     * Returns lesson IDs for the selected academic loads, or null for the
     * normal full-calendar generation path.
     *
     * @return array<int>|null
     */
    private function scopedLessonIds(TimetableCalendar $calendar): ?array
    {
        if ($this->pevaluacionIds === null) {
            return null;
        }

        return $calendar->lessons()
            ->whereIn('pevaluacion_id', array_map('intval', $this->pevaluacionIds))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Keeps the preview honest when a legacy or manual assignment has fewer
     * periods than the lesson requires.
     *
     * @param  array<string, list<array{period_id:int, room_id:int|null, is_practical:bool}>>  $assignment
     * @return list<array<string, int>>
     */
    private function assignmentDiagnostics(TimetableCalendar $calendar, array $assignment): array
    {
        return $calendar->lessons()
            ->get(['id', 'weekly_blocks_t', 'weekly_blocks_p'])
            ->map(function (TimetableLesson $lesson) use ($assignment): ?array {
                $required = (int) $lesson->weekly_blocks_t + (int) $lesson->weekly_blocks_p;
                $assigned = collect($assignment[(string) $lesson->id] ?? [])->pluck('period_id')->unique()->count();

                return $assigned === $required ? null : [
                    'lesson_id' => (int) $lesson->id,
                    'required_blocks' => $required,
                    'assigned_blocks' => $assigned,
                    'missing_blocks' => max(0, $required - $assigned),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function hasLegacySlots(TimetableCalendar $calendar): bool
    {
        return $calendar->slots()->exists();
    }

    /**
     * Persiste la asignación en una transacción (ADR-TT-002) con el bloqueo
     * optimista del §15. En una regeneración no-dry-run el diff contra el
     * preview previo no aplica: el preview siempre se confirma explícitamente.
     *
     * PLAN-TIMETABLE-002 §4.3: antes de activar este calendario se demueve
     * (status=archived) al activo anterior del mismo lapso (I-4). Si dos
     * publicaciones del mismo lapso corren a la vez, uq_active_lapso rechaza
     * la segunda: se captura, se revierte el objetivo a draft y se loguea.
     */
    private function persist(TimetableCalendar $calendar, SolverResult $result, int $expectedVersion): void
    {
        try {
            DB::transaction(function () use ($calendar, $result, $expectedVersion) {
                // Row-lock: serializa democión + activación por calendario.
                $row = TimetableCalendar::query()->lockForUpdate()->find($calendar->id);

                if (! $row || $row->version !== $expectedVersion) {
                    Log::channel('timetable')->warning('GenerateTimetableJob: conflicto de versión, no persiste', [
                        'correlation_id' => $this->correlationId(),
                        'calendar_id' => $this->calendarId,
                        'expected_version' => $expectedVersion,
                    ]);

                    // Recuperación: un job stale no debe dejar el calendario en 'generating'.
                    TimetableCalendar::query()
                        ->where('id', $calendar->id)
                        ->where('status', TimetableCalendar::STATUS_GENERATING)
                        ->update(['status' => TimetableCalendar::STATUS_DRAFT]);

                    return;
                }

                // Democión del activo anterior del lapso antes de activar este.
                TimetableCalendar::query()
                    ->forLapso($row->lapso_id)
                    ->where('id', '!=', $row->id)
                    ->active()
                    ->update(['status' => TimetableCalendar::STATUS_ARCHIVED]);

                $row->update([
                    'version' => $expectedVersion + 1,
                    'status' => TimetableCalendar::STATUS_ACTIVE,
                    'quality_score' => $this->qualityScore($result),
                    'preview_payload' => null,
                ]);

                $slotQuery = TimetableSlot::query()->where('calendar_id', $calendar->id);
                $conflictQuery = TimetableConflict::query()->where('calendar_id', $calendar->id);
                if ($this->lessonIds !== null) {
                    $slotQuery->whereIn('lesson_id', $this->lessonIds);
                    $conflictQuery->whereIn('lesson_id', $this->lessonIds);
                }
                $slotQuery->delete();
                $conflictQuery->delete();

                $slotRows = [];
                $now = now();
                $lessonsById = TimetableLesson::query()
                    ->where('calendar_id', $calendar->id)
                    ->with('pevaluacion')
                    ->get()
                    ->keyBy('id');
                foreach ($result->assignment as $lessonId => $slots) {
                    if ($this->lessonIds !== null && ! in_array((int) $lessonId, $this->lessonIds, true)) {
                        continue;
                    }

                    $lesson = $lessonsById->get($lessonId);

                    if (! $lesson || ! $lesson->pevaluacion) {
                        continue;
                    }

                    foreach ($slots as $slot) {
                        $slotRows[] = [
                            'calendar_id' => $calendar->id,
                            'lesson_id' => $lessonId,
                            'period_id' => $slot->periodId,
                            'profesor_id' => $lesson->pevaluacion->profesor_id,
                            'seccion_id' => $lesson->pevaluacion->seccion_id,
                            'grupo_estable_id' => $lesson->pevaluacion->grupo_estable_id ? (int) $lesson->pevaluacion->grupo_estable_id : null,
                            'is_half_group' => (bool) $lesson->is_half_group,
                            'room_id' => $slot->roomId,
                            'locked' => $lesson->locked,
                            'is_manual_override' => false,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                if ($slotRows !== []) {
                    TimetableSlot::query()->insert($slotRows);
                }

                foreach ($result->unassigned as $lessonId) {
                    if ($this->lessonIds !== null && ! in_array((int) $lessonId, $this->lessonIds, true)) {
                        continue;
                    }

                    TimetableConflict::create([
                        'calendar_id' => $calendar->id,
                        'lesson_id' => $lessonId,
                        'period_id' => null,
                        'type' => 'unassigned',
                        'details' => ['reason' => 'Sin combinación viable (solver).'],
                    ]);
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Carrera de activación (otro calendario del lapso se activó antes):
            // la transacción revierte y el objetivo vuelve a draft.
            TimetableCalendar::query()
                ->where('id', $calendar->id)
                ->where('status', TimetableCalendar::STATUS_GENERATING)
                ->update(['status' => TimetableCalendar::STATUS_DRAFT]);

            Log::channel('timetable')->warning('GenerateTimetableJob: carrera de activación, se revierte a draft', [
                'correlation_id' => $this->correlationId(),
                'calendar_id' => $this->calendarId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
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
