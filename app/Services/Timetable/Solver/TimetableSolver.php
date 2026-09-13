<?php

namespace App\Services\Timetable\Solver;

/**
 * SPEC-TIMETABLE-001 §6.1 — Solver de horarios.
 *
 * Backtracking con forward-checking + restricciones duras/soft, en PHP puro y
 * sin Eloquent (recibe DTOs). Comportamiento clave:
 *
 *  - ADR-TT-007: las lecciones locked se reservan PRIMERO y su período queda
 *    fuera del dominio del resto (nunca se reasignan).
 *  - ADR-TT-003: las lecciones libres se ordenan por grado de restricción.
 *  - ADR-TT-009: al vencer el deadline se conserva la solución parcial y las
 *    restantes se marcan como no asignadas (NO se deshace el progreso).
 *  - ADR-TT-010: bloques teóricos ('t') sin aula dedicada; bloques prácticos
 *    ('p') exigen el aula de roomTypeRequired si se fijó.
 */
final class TimetableSolver
{
    /** Tope de combinaciones evaluadas por lección (acota el CSP real). */
    private const MAX_COMBOS_PER_LESSON = 1000;

    /** Tope base del pool de períodos candidatos por dominio (C(14,7)=3432 máx). */
    private const MAX_CANDIDATE_POOL = 14;

    /**
     * Tope máximo del pool cuando una lección exige más bloques que el tope
     * base (p. ej. 20 bloques T). Evita C(30,20) en instituciones grandes.
     */
    private const MAX_CANDIDATE_POOL_ABS = 26;

    /** Presupuesto de nodos del árbol de combinaciones por llamada. */
    private const MAX_COMBO_NODES = 500_000;

    /**
     * @param  LessonToSchedule[]  $lessons
     * @param  array<int, list<int>>  $availablePeriodsByTeacher  lessonId => periodIds (turno preferido primero)
     * @param  array<string, list<int>>  $roomsByType  roomType => roomIds compatibles
     * @param  array<int, array{day: int, order: int}>  $periodMeta  periodId => día/orden (heurística soft)
     */
    public function __construct(
        private array $lessons,
        private array $availablePeriodsByTeacher,
        private array $roomsByType,
        private array $periodMeta = [],
        private int $timeLimitSeconds = 30,
        private int $maxSubjectsPerPeriod = 2,
        private ?SolverAttemptConfig $config = null,
    ) {}

    public function solve(): SolverResult
    {
        $started = microtime(true);
        $deadline = $started + $this->timeLimitSeconds;

        $ctx = new SchedulingContext($this->maxSubjectsPerPeriod);
        $assignment = [];
        $unassigned = [];

        // Las asignaciones parciales válidas se conservan y se completan.
        foreach ($this->lessons as $lesson) {
            if ($lesson->preassignedSlots === []) {
                continue;
            }

            foreach ($lesson->preassignedSlots as $slot) {
                if (! $ctx->isFree(
                    $slot->periodId,
                    $lesson->profesorId,
                    $lesson->seccionId,
                    $slot->roomId,
                    $lesson->grupoEstableId,
                    $lesson->isHalfGroup,
                    $lesson->lessonId,
                    $lesson->allowSharedTeacher,
                )) {
                    $unassigned[] = $lesson->lessonId;

                    continue 2;
                }
            }

            foreach ($lesson->preassignedSlots as $slot) {
                $ctx->occupy(
                    $slot->periodId,
                    $lesson->profesorId,
                    $lesson->seccionId,
                    $slot->roomId,
                    $lesson->grupoEstableId,
                    $lesson->isHalfGroup,
                    $lesson->lessonId,
                    $lesson->allowSharedTeacher,
                );
            }
            $assignment[$lesson->lessonId] = $lesson->preassignedSlots;
        }

        // ADR-TT-007: reservar primero las locked.
        foreach ($this->lessons as $lesson) {
            if ($lesson->preassignedSlots !== []) {
                continue;
            }
            $lockedPeriods = array_values(array_unique(array_map('intval', $lesson->lockedPeriodIds)));
            $hasCompleteLock = $lesson->locked
                && $lesson->blocksNeeded() > 0
                && count($lockedPeriods) === $lesson->blocksNeeded();

            if (! $hasCompleteLock) {
                continue;
            }

            $combo = [];
            $conflict = false;
            foreach ($lockedPeriods as $pId) {
                if (! $ctx->isFree($pId, $lesson->profesorId, $lesson->seccionId, null, $lesson->grupoEstableId, $lesson->isHalfGroup, $lesson->lessonId, $lesson->allowSharedTeacher)) {
                    $conflict = true;
                    break;
                }
                $ctx->occupy($pId, $lesson->profesorId, $lesson->seccionId, null, $lesson->grupoEstableId, $lesson->isHalfGroup, $lesson->lessonId, $lesson->allowSharedTeacher);
                $combo[] = new SlotCandidate($pId, null, false);
            }

            if ($conflict) {
                $unassigned[] = $lesson->lessonId;
            } else {
                $assignment[$lesson->lessonId] = $combo;
            }
        }

        // Lecciones libres ordenadas según la estrategia del intento (ADR-TT-003
        // por defecto; el orquestador usa otras para explorar alternativas).
        $free = array_values(array_filter(
            $this->lessons,
            fn (LessonToSchedule $l) => ! $l->locked
                || $l->blocksNeeded() <= 0
                || count(array_unique(array_map('intval', $l->lockedPeriodIds))) !== $l->blocksNeeded(),
        ));
        $this->orderLessons($free);

        $timedOut = false;
        $bestAssignment = $assignment;
        $bestUnassigned = array_values(array_unique(array_merge(
            array_map('intval', $unassigned),
            array_map(fn (LessonToSchedule $lesson): int => $lesson->lessonId, $free),
        )));
        $this->backtrack(
            $free,
            0,
            $ctx,
            $assignment,
            $unassigned,
            $deadline,
            $timedOut,
            $bestAssignment,
            $bestUnassigned,
        );

        return new SolverResult(
            $bestAssignment,
            $bestUnassigned,
            $timedOut,
            microtime(true) - $started,
        );
    }

    /**
     * Ordena las lecciones libres según la estrategia del intento.
     *
     * @param  LessonToSchedule[]  $free
     */
    private function orderLessons(array &$free): void
    {
        $ordering = $this->config->ordering ?? SolverAttemptConfig::ORDER_CONSTRAINT;

        switch ($ordering) {
            case SolverAttemptConfig::ORDER_SCARCITY:
                usort($free, fn (LessonToSchedule $a, LessonToSchedule $b): int => $this->scarcity($a) <=> $this->scarcity($b));
                break;
            case SolverAttemptConfig::ORDER_BLOCKS_DESC:
                usort($free, fn (LessonToSchedule $a, LessonToSchedule $b): int => $b->blocksNeeded() <=> $a->blocksNeeded());
                break;
            case SolverAttemptConfig::ORDER_RANDOM:
                $this->deterministicShuffle($free, $this->config->seed);
                break;
            case SolverAttemptConfig::ORDER_REPAIR:
                $this->repairOrder($free);
                break;
            case SolverAttemptConfig::ORDER_CONSTRAINT:
            default:
                usort($free, fn (LessonToSchedule $a, LessonToSchedule $b): int => $b->constraintDegree() <=> $a->constraintDegree());
                break;
        }
    }

    /**
     * Reparación (TT-CFP-11): coloca primero las lecciones indicadas en
     * `priorityLessonIds` (las que quedaron sin asignar) y, entre ellas, las de
     * más bloques; el resto se ordena por grado de restricción. Así el
     * backtracking reubica a las "bloqueantes" para liberarles espacio.
     *
     * @param  LessonToSchedule[]  $free
     */
    private function repairOrder(array &$free): void
    {
        $priority = array_flip(array_map('intval', $this->config->priorityLessonIds ?? []));

        usort($free, function (LessonToSchedule $a, LessonToSchedule $b) use ($priority): int {
            $aPriority = isset($priority[$a->lessonId]) ? 1 : 0;
            $bPriority = isset($priority[$b->lessonId]) ? 1 : 0;

            if ($aPriority !== $bPriority) {
                return $bPriority <=> $aPriority;
            }

            if ($aPriority === 1) {
                return $b->blocksNeeded() <=> $a->blocksNeeded();
            }

            return $b->constraintDegree() <=> $a->constraintDegree();
        });
    }

    /**
     * Períodos disponibles del docente (menor = más escaso = se asigna primero).
     */
    private function scarcity(LessonToSchedule $lesson): int
    {
        $base = $this->availablePeriodsByTeacher[$lesson->lessonId]
            ?? $this->availablePeriodsByTeacher[$lesson->profesorId]
            ?? [];

        return count($base);
    }

    /**
     * Fisher-Yates determinista por semilla (restarts reproducibles).
     *
     * @param  list<LessonToSchedule>  $items
     */
    private function deterministicShuffle(array &$items, int $seed): void
    {
        mt_srand($seed);

        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }
    }

    /**
     * Score soft global de una asignación (suma del §6.2 por lección). Lo usa el
     * orquestador para desempatar intentos con la misma cobertura.
     *
     * @param  array<int, list<SlotCandidate>>  $assignment
     */
    public function qualityScore(array $assignment): int
    {
        $score = 0;

        foreach ($assignment as $slots) {
            $score += $this->comboScore($slots);
        }

        return $score;
    }

    /**
     * @param  LessonToSchedule[]  $lessons
     * @param  array<int, list<SlotCandidate>>  $assignment
     * @param  array<int, int>  $unassigned
     * @param  array<int, list<SlotCandidate>>  $bestAssignment
     * @param  array<int, int>  $bestUnassigned
     */
    private function backtrack(
        array $lessons,
        int $index,
        SchedulingContext $ctx,
        array &$assignment,
        array &$unassigned,
        float $deadline,
        bool &$timedOut,
        array &$bestAssignment,
        array &$bestUnassigned,
    ): bool {
        $this->rememberBest($assignment, $unassigned, $bestAssignment, $bestUnassigned);

        if ($index >= count($lessons)) {
            return $unassigned === [];
        }

        // ADR-TT-009: corte por tiempo conserva la solución parcial.
        if (microtime(true) > $deadline) {
            $timedOut = true;

            return false;
        }

        $lesson = $lessons[$index];
        $domain = $this->buildDomain($lesson, $ctx);

        foreach ($this->combinationsOfSize($domain, $lesson) as $combo) {
            foreach ($combo as $slot) {
                $ctx->occupy($slot->periodId, $lesson->profesorId, $lesson->seccionId, $slot->roomId, $lesson->grupoEstableId, $lesson->isHalfGroup, $lesson->lessonId, $lesson->allowSharedTeacher);
            }
            $existingSlots = $assignment[$lesson->lessonId] ?? [];
            $assignment[$lesson->lessonId] = array_merge($existingSlots, $combo);

            if ($this->backtrack(
                $lessons,
                $index + 1,
                $ctx,
                $assignment,
                $unassigned,
                $deadline,
                $timedOut,
                $bestAssignment,
                $bestUnassigned,
            )) {
                return true;
            }

            foreach ($combo as $slot) {
                $ctx->release($slot->periodId, $lesson->profesorId, $lesson->seccionId, $slot->roomId, $lesson->grupoEstableId, $lesson->isHalfGroup, $lesson->lessonId);
            }
            if ($existingSlots === []) {
                unset($assignment[$lesson->lessonId]);
            } else {
                $assignment[$lesson->lessonId] = $existingSlots;
            }
        }

        // Se permite omitir esta lección, pero solo después de explorar las
        // combinaciones. Así una asignación temprana no condena a una lección
        // posterior cuando existe una redistribución completa.
        $unassigned[] = $lesson->lessonId;
        $complete = $this->backtrack(
            $lessons,
            $index + 1,
            $ctx,
            $assignment,
            $unassigned,
            $deadline,
            $timedOut,
            $bestAssignment,
            $bestUnassigned,
        );
        array_pop($unassigned);

        return $complete;
    }

    /**
     * Conserva la mejor solución parcial cuando el problema no tiene una
     * solución completa o vence el presupuesto de búsqueda.
     *
     * @param  array<int, list<SlotCandidate>>  $assignment
     * @param  array<int, int>  $unassigned
     * @param  array<int, list<SlotCandidate>>  $bestAssignment
     * @param  array<int, int>  $bestUnassigned
     */
    private function rememberBest(
        array $assignment,
        array $unassigned,
        array &$bestAssignment,
        array &$bestUnassigned,
    ): void {
        $assignedBlocks = array_sum(array_map('count', $assignment));
        $bestBlocks = array_sum(array_map('count', $bestAssignment));

        if ($assignedBlocks <= $bestBlocks) {
            return;
        }

        $bestAssignment = $assignment;
        $bestUnassigned = array_values(array_unique(array_map('intval', $unassigned)));
        $assignedIds = array_map('intval', array_keys($assignment));
        foreach ($this->lessons as $lesson) {
            if (! in_array($lesson->lessonId, $assignedIds, true)
                && ! in_array($lesson->lessonId, $bestUnassigned, true)) {
                $bestUnassigned[] = $lesson->lessonId;
            }
        }
    }

    /**
     * Dominio por tipo de bloque (ADR-TT-004/010), ya filtrado por
     * turno + disponibilidad (entrada) y libre en ctx.
     *
     * @return array{t: list<SlotCandidate>, p: list<SlotCandidate>}
     */
    private function buildDomain(LessonToSchedule $lesson, SchedulingContext $ctx): array
    {
        $base = $this->availablePeriodsByTeacher[$lesson->lessonId]
            ?? $this->availablePeriodsByTeacher[$lesson->profesorId]
            ?? [];
        $domain = ['t' => [], 'p' => []];

        foreach ($base as $periodId) {
            if ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, null, $lesson->grupoEstableId, $lesson->isHalfGroup, $lesson->lessonId, $lesson->allowSharedTeacher)) {
                $domain['t'][] = new SlotCandidate($periodId, null, false);
            }

            if ($lesson->roomTypeRequired !== null) {
                foreach ($this->roomsByType[$lesson->roomTypeRequired] ?? [] as $roomId) {
                    if ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, $roomId, $lesson->grupoEstableId, $lesson->isHalfGroup, $lesson->lessonId, $lesson->allowSharedTeacher)) {
                        $domain['p'][] = new SlotCandidate($periodId, $roomId, true);
                    }
                }
            } elseif ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, null, $lesson->grupoEstableId, $lesson->isHalfGroup, $lesson->lessonId, $lesson->allowSharedTeacher)) {
                $domain['p'][] = new SlotCandidate($periodId, null, true);
            }
        }

        if ($lesson->isHalfGroup) {
            foreach (['t', 'p'] as $blockType) {
                usort(
                    $domain[$blockType],
                    fn (SlotCandidate $a, SlotCandidate $b): int => $ctx->halfGroupLoad(
                        $b->periodId,
                        $lesson->seccionId,
                    ) <=> $ctx->halfGroupLoad($a->periodId, $lesson->seccionId),
                );
            }
        }

        return $domain;
    }

    /**
     * Combina bloques teóricos (de 't') y prácticos (de 'p') sin repetir
     * período dentro de la lección, ordenando las combinaciones por la
     * heurística soft del §6.2 (más días distintos primero, teóricos tempranos).
     *
     * El espacio se acota: pool de candidatos limitado y tope de combinaciones
     * por lección (CSP real con lecciones de 2-8 bloques, no explota).
     *
     * @param  array{t: list<SlotCandidate>, p: list<SlotCandidate>}  $domain
     * @return iterable<list<SlotCandidate>>
     */
    private function combinationsOfSize(array $domain, LessonToSchedule $lesson): iterable
    {
        $tCombos = $this->pickCombinations($domain['t'], $lesson->remainingBlocksT());
        $pCombos = $this->pickCombinations($domain['p'], $lesson->remainingBlocksP());

        $results = [];
        foreach ($tCombos as $tCombo) {
            foreach ($pCombos as $pCombo) {
                $periods = array_map(fn (SlotCandidate $s) => $s->periodId, $tCombo);
                $pPeriods = array_map(fn (SlotCandidate $s) => $s->periodId, $pCombo);
                if (count(array_unique(array_merge($periods, $pPeriods))) !== count($periods) + count($pPeriods)) {
                    continue; // período repetido dentro de la misma lección
                }
                $results[] = array_merge($tCombo, $pCombo);
            }
        }

        usort($results, fn (array $a, array $b) => $this->comboScore($b) <=> $this->comboScore($a)
        );

        yield from array_slice($results, 0, self::MAX_COMBOS_PER_LESSON);
    }

    /**
     * Combinaciones de $n slots de la lista (sin repetición), acotadas:
     * el pool se limita a los mejores MAX_CANDIDATE_POOL candidatos y el
     * resultado a MAX_COMBOS_PER_LESSON.
     *
     * @param  list<SlotCandidate>  $candidates
     * @return list<list<SlotCandidate>>
     */
    private function pickCombinations(array $candidates, int $n): array
    {
        if ($n <= 0) {
            return [[]];
        }

        if (count($candidates) < $n) {
            return [];
        }

        // Ordenar candidatos por heurística para quedarnos con el mejor pool.
        usort($candidates, fn (SlotCandidate $a, SlotCandidate $b) => $this->comboScore([$b]) <=> $this->comboScore([$a])
        );

        // ADR-TT-011: el pool crece adaptativamente con $n para que una lección
        // con muchos bloques (ej. 20 T) no quede huérfana por un tope fijo.
        $pool = $candidates;
        if (count($pool) > self::MAX_CANDIDATE_POOL) {
            $pool = array_slice($pool, 0, min(
                max(self::MAX_CANDIDATE_POOL, $n),
                self::MAX_CANDIDATE_POOL_ABS,
            ));
        }

        if (count($pool) < $n) {
            return [];
        }

        $results = [];

        // ADR-TT-011: cuando n > m/2, generar las combinaciones de EXCLUSIONES
        // (m-n) y complementar: C(26,25) se resuelve como C(26,1) + complemento.
        if ($n > count($pool) / 2) {
            $excluded = [];
            $nodes = 0;
            $this->combine($pool, count($pool) - $n, 0, [], $excluded, $nodes);
            foreach ($excluded as $exclude) {
                $excludeIds = array_flip(array_map(fn (SlotCandidate $s) => $s->periodId, $exclude));
                $results[] = array_values(array_filter(
                    $pool,
                    fn (SlotCandidate $s) => ! isset($excludeIds[$s->periodId]),
                ));
            }
        } else {
            $nodes = 0;
            $this->combine($pool, $n, 0, [], $results, $nodes);
        }

        usort($results, fn (array $a, array $b) => $this->comboScore($b) <=> $this->comboScore($a)
        );

        return array_slice($results, 0, self::MAX_COMBOS_PER_LESSON);
    }

    /**
     * @param  list<SlotCandidate>  $candidates
     * @param  list<SlotCandidate>  $current
     * @param  list<list<SlotCandidate>>  $results
     */
    private function combine(array $candidates, int $n, int $start, array $current, array &$results, int &$nodes): void
    {
        $nodes++;
        if ($n === 0) {
            $results[] = $current;

            return;
        }

        // Corte temprano (ADR-TT-011): basta con MAX_COMBOS_PER_LESSON
        // resultados y un presupuesto de nodos para no generar C(26,13)≈10M
        // combinaciones (los resultados se ordenan por heurística al final).
        if (count($results) >= self::MAX_COMBOS_PER_LESSON
            || $nodes >= self::MAX_COMBO_NODES) {
            return;
        }

        for ($i = $start; $i < count($candidates); $i++) {
            $current[] = $candidates[$i];
            $this->combine($candidates, $n - 1, $i + 1, $current, $results, $nodes);
            array_pop($current);
        }
    }

    /**
     * Heurística soft del §6.2: mejor score = menos penalizaciones.
     *  - +100 por día distinto usado (distribuir bloques)
     *  - -50 por día consecutivo entre bloques de la misma lección (si >1)
     *  - -10 por bloque teórico en período tardío (order > 3)
     *
     * @param  list<SlotCandidate>  $combo
     */
    private function comboScore(array $combo): int
    {
        $score = 0;
        $days = [];
        foreach ($combo as $slot) {
            $meta = $this->periodMeta[$slot->periodId] ?? null;
            if ($meta) {
                $days[$meta['day']] = ($days[$meta['day']] ?? 0) + 1;
                if (! $slot->isPractical && $meta['order'] > 3) {
                    $score -= 10;
                }
            }
        }

        $score += count($days) * 100;

        $dayList = array_keys($days);
        sort($dayList);
        for ($i = 1; $i < count($dayList); $i++) {
            if ($dayList[$i] === $dayList[$i - 1] + 1) {
                $score -= 50;
            }
        }

        return $score;
    }
}
