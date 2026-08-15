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
     * @param  array<int, list<int>>  $availablePeriodsByTeacher  profesorId => periodIds (ya filtrado por turno y disponibilidad)
     * @param  array<string, list<int>>  $roomsByType  roomType => roomIds compatibles
     * @param  array<int, array{day: int, order: int}>  $periodMeta  periodId => día/orden (heurística soft)
     */
    public function __construct(
        private array $lessons,
        private array $availablePeriodsByTeacher,
        private array $roomsByType,
        private array $periodMeta = [],
        private int $timeLimitSeconds = 30,
    ) {}

    public function solve(): SolverResult
    {
        $started = microtime(true);
        $deadline = $started + $this->timeLimitSeconds;

        $ctx = new SchedulingContext;
        $assignment = [];
        $unassigned = [];

        // ADR-TT-007: reservar primero las locked.
        foreach ($this->lessons as $lesson) {
            if (! $lesson->locked) {
                continue;
            }

            $combo = [];
            $conflict = false;
            foreach ($lesson->lockedPeriodIds as $pId) {
                if (! $ctx->isFree($pId, $lesson->profesorId, $lesson->seccionId, null)) {
                    $conflict = true;
                    break;
                }
                $ctx->occupy($pId, $lesson->profesorId, $lesson->seccionId, null);
                $combo[] = new SlotCandidate($pId, null, false);
            }

            if ($conflict) {
                $unassigned[] = $lesson->lessonId;
            } else {
                $assignment[$lesson->lessonId] = $combo;
            }
        }

        // Lecciones libres por grado de restricción (ADR-TT-003).
        $free = array_values(array_filter(
            $this->lessons,
            fn (LessonToSchedule $l) => ! $l->locked,
        ));
        usort($free, fn (LessonToSchedule $a, LessonToSchedule $b) => $b->constraintDegree() <=> $a->constraintDegree()
        );

        $timedOut = false;
        $this->backtrack($free, 0, $ctx, $assignment, $unassigned, $deadline, $timedOut);

        return new SolverResult(
            $assignment,
            $unassigned,
            $timedOut,
            microtime(true) - $started,
        );
    }

    /**
     * @param  LessonToSchedule[]  $lessons
     * @param  array<int, list<SlotCandidate>>  $assignment
     * @param  array<int, int>  $unassigned
     */
    private function backtrack(
        array $lessons,
        int $index,
        SchedulingContext $ctx,
        array &$assignment,
        array &$unassigned,
        float $deadline,
        bool &$timedOut,
    ): bool {
        if ($index >= count($lessons)) {
            return true;
        }

        // ADR-TT-009: corte por tiempo conserva la solución parcial.
        if (microtime(true) > $deadline) {
            $timedOut = true;
            for ($i = $index; $i < count($lessons); $i++) {
                $unassigned[] = $lessons[$i]->lessonId;
            }

            return true;
        }

        $lesson = $lessons[$index];
        $domain = $this->buildDomain($lesson, $ctx);

        foreach ($this->combinationsOfSize($domain, $lesson) as $combo) {
            foreach ($combo as $slot) {
                $ctx->occupy($slot->periodId, $lesson->profesorId, $lesson->seccionId, $slot->roomId);
            }
            $assignment[$lesson->lessonId] = $combo;

            if ($this->backtrack($lessons, $index + 1, $ctx, $assignment, $unassigned, $deadline, $timedOut)) {
                return true;
            }

            foreach ($combo as $slot) {
                $ctx->release($slot->periodId, $lesson->profesorId, $lesson->seccionId, $slot->roomId);
            }
            unset($assignment[$lesson->lessonId]);
        }

        // Sin combinación viable: se reporta como no asignada y se continúa.
        $unassigned[] = $lesson->lessonId;

        return $this->backtrack($lessons, $index + 1, $ctx, $assignment, $unassigned, $deadline, $timedOut);
    }

    /**
     * Dominio por tipo de bloque (ADR-TT-004/010), ya filtrado por
     * turno + disponibilidad (entrada) y libre en ctx.
     *
     * @return array{t: list<SlotCandidate>, p: list<SlotCandidate>}
     */
    private function buildDomain(LessonToSchedule $lesson, SchedulingContext $ctx): array
    {
        $base = $this->availablePeriodsByTeacher[$lesson->profesorId] ?? [];
        $domain = ['t' => [], 'p' => []];

        foreach ($base as $periodId) {
            if ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, null)) {
                $domain['t'][] = new SlotCandidate($periodId, null, false);
            }

            if ($lesson->roomTypeRequired !== null) {
                foreach ($this->roomsByType[$lesson->roomTypeRequired] ?? [] as $roomId) {
                    if ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, $roomId)) {
                        $domain['p'][] = new SlotCandidate($periodId, $roomId, true);
                    }
                }
            } elseif ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, null)) {
                $domain['p'][] = new SlotCandidate($periodId, null, true);
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
        $tCombos = $this->pickCombinations($domain['t'], $lesson->blocksT);
        $pCombos = $this->pickCombinations($domain['p'], $lesson->blocksP);

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
