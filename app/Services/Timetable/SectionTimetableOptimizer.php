<?php

namespace App\Services\Timetable;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Support\Collection;

/**
 * Motor híbrido de reorganización de los bloques de UNA sección (Paso 5).
 *
 * Combina las tres teorías del blueprint de horarios escolares:
 *
 *  1. Teoría de grafos / coloreo — construye el grafo de conflictos (docente,
 *     aula, sección) y genera soluciones iniciales con heurísticas DSATUR,
 *     Welsh-Powell y Largest-Degree-First.
 *  2. CSP (Constraint Satisfaction) — motor principal: propagación hacia
 *     adelante + backtracking con MRV/grado para garantizar las restricciones
 *     DURAS (docente, aula y sección sin doble uso; disponibilidad del docente).
 *  3. MILP (función objetivo) — ordena y pule las restricciones BLANDAS con una
 *     función de penalizaciones ponderada (colisión entre P.Estudios, huecos
 *     docentes, concentración de materias, uso de aulas, turno), optimizada con
 *     búsqueda local (mejor mejora + reinicios).
 *
 * Cierra con una validación determinista antes de publicar. NUNCA agrega,
 * quita ni cambia lecciones: solo permuta los períodos de los slots existentes,
 * respetando `timetable_lessons.locked`, `is_half_group` y `allow_shared_teacher`.
 */
class SectionTimetableOptimizer
{
    public const METHOD_DSATUR = 'dsatur';

    public const METHOD_WELSH_POWELL = 'welsh_powell';

    public const METHOD_LARGEST_DEGREE = 'largest_degree';

    public const METHOD_CSP = 'csp_backtracking';

    public const METHOD_LOCAL_SEARCH = 'local_search';

    private Collection $periods;

    /** @var array<int, array<string, mixed>> lessonId => meta */
    private array $meta = [];

    /** @var list<array<string, mixed>> nodos (slots) de la sección */
    private array $nodes = [];

    /** @var list<list<int>> adyacencia del grafo de conflictos */
    private array $adjacency = [];

    /** @var array<int, list<array{day:int, start:int, end:int}>> ocupación externa (otros P.Estudios) */
    private array $externalBusy = [];

    /** @var array<int, array<int, array<int, bool>>> periodId => profesorId => [lessonId => shared] (otras secciones del mismo calendario) */
    private array $extTeacher = [];

    /** @var array<int, array<int, bool>> periodId => roomId (otras secciones del mismo calendario) */
    private array $extRoom = [];

    private TimetableAvailabilityService $availability;

    private int $maxSubjects = 2;

    private int $sectionId = 0;

    /** @var array<string, int> */
    private array $weights = [];

    private int $cspNodeBudget = 40000;

    private float $timeBudget = 15.0;

    private int $restarts = 24;

    private int $cspNodes = 0;

    private bool $cspTimedOut = false;

    private float $deadline = 0.0;

    /**
     * @param  array<int|string, list<array{period_id:int, room_id?:int|null, is_practical?:bool, locked?:bool}>>  $assignment
     * @return array{
     *     assignment: array<int, list<array{period_id:int, room_id:int|null, is_practical:bool, locked:bool}>>,
     *     moved: int,
     *     collisions_before: int,
     *     collisions_after: int,
     *     hard_violations: int,
     *     penalty_before: int,
     *     penalty_after: int,
     *     method: string,
     *     report: array<string, mixed>
     * }
     */
    public function optimize(TimetableCalendar $calendar, int $sectionId, array $assignment): array
    {
        $this->sectionId = $sectionId;
        $this->weights = array_merge([
            'hard' => 1000,
            'external' => 100,
            'gap' => 10,
            'distribution' => 25,
            'room' => 15,
            'off_shift' => 30,
        ], (array) config('timetable.section_optimizer.weights', []));

        $this->cspNodeBudget = (int) config('timetable.section_optimizer.csp_node_budget', 40000);
        $this->restarts = (int) config('timetable.section_optimizer.restarts', 24);
        $this->timeBudget = (float) config('timetable.section_optimizer.time_budget_seconds', 15);
        $this->deadline = microtime(true) + $this->timeBudget;

        $this->periods = TimetablePeriod::query()
            ->where('calendar_id', $calendar->id)
            ->where('is_break', false)
            ->get(['id', 'shift_id', 'day_of_week', 'order_in_day', 'start_time', 'end_time'])
            ->keyBy('id');

        $this->maxSubjects = max(1, (int) ($calendar->max_subjects_per_period ?? 2));

        $this->loadLessons($calendar);
        if ($this->meta === []) {
            return $this->emptyResult();
        }

        $this->availability = app(TimetableAvailabilityService::class);
        $this->buildNodes($assignment);

        if ($this->nodes === []) {
            return $this->emptyResult();
        }

        $this->externalBusy = $this->externalTeacherBusyMap($calendar);
        $this->buildExternalOccupancy($calendar);
        $this->buildConflictGraph();

        // ── Estado inicial (snapshot) ───────────────────────────────────────
        $current = [];
        foreach ($this->nodes as $index => $node) {
            $current[$index] = (int) $node['period_id'];
        }

        $before = $this->evaluate($current);
        $collisionsBefore = $before['hard'] + $before['external'];

        // ── Teoría 3: grafo/coloreo → soluciones iniciales ──────────────────
        $report = ['methods' => []];

        $candidates = [];
        foreach ([
            self::METHOD_DSATUR => $this->colorOrderDsatur(),
            self::METHOD_WELSH_POWELL => $this->colorOrderWelshPowell(),
            self::METHOD_LARGEST_DEGREE => $this->colorOrderLargestDegree(),
        ] as $method => $order) {
            $candidate = $this->greedyAssign($order, $current);
            $eval = $this->evaluate($candidate);
            $candidates[] = ['method' => $method, 'assign' => $candidate, 'eval' => $eval];
            $report['methods'][$method] = [
                'collisions' => $eval['hard'] + $eval['external'],
                'hard' => $eval['hard'],
                'penalty' => $eval['penalty'],
            ];
        }

        // ── Teoría 2: CSP (propagación + backtracking) ──────────────────────
        $csp = $this->cspSolve($current);
        if ($csp !== null) {
            $eval = $this->evaluate($csp);
            $candidates[] = ['method' => self::METHOD_CSP, 'assign' => $csp, 'eval' => $eval];
            $report['methods'][self::METHOD_CSP] = [
                'collisions' => $eval['hard'] + $eval['external'],
                'hard' => $eval['hard'],
                'penalty' => $eval['penalty'],
                'nodes' => $this->cspNodes,
            ];
        }

        // ── Teoría 1: función de penalizaciones (MILP) + búsqueda local ─────
        $best = $this->pickBest($candidates, $current, $before);
        $best = $this->localSearch($best);

        $after = $this->evaluate($best);
        $report['methods'][self::METHOD_LOCAL_SEARCH] = [
            'collisions' => $after['hard'] + $after['external'],
            'hard' => $after['hard'],
            'penalty' => $after['penalty'],
        ];

        // ── Validación determinista ─────────────────────────────────────────
        $violations = $this->validate($best);
        $report['violations'] = $violations;
        $report['soft'] = [
            'external' => $after['external'],
            'gaps' => $after['gaps'],
            'distribution' => $after['distribution'],
            'room' => $after['room'],
            'off_shift' => $after['off_shift'],
        ];

        $grouped = [];
        $moved = 0;
        foreach ($best as $index => $periodId) {
            $node = $this->nodes[$index];
            if ((int) $periodId !== (int) $node['period_id']) {
                $moved++;
            }
            $grouped[(int) $node['lesson_id']][] = [
                'period_id' => (int) $periodId,
                'room_id' => $node['room_id'],
                'is_practical' => (bool) $node['is_practical'],
                'locked' => (bool) $node['locked'],
            ];
        }

        return [
            'assignment' => $grouped,
            'moved' => $moved,
            'collisions_before' => $collisionsBefore,
            'collisions_after' => $after['hard'] + $after['external'],
            'hard_violations' => $after['hard'],
            'penalty_before' => $before['penalty'],
            'penalty_after' => $after['penalty'],
            'method' => $this->methodOf($candidates, $best),
            'report' => $report,
        ];
    }

    /**
     * @param  array<int|string, list<array{period_id:int, room_id?:int|null, is_practical?:bool, locked?:bool}>>  $assignment
     */
    private function buildNodes(array $assignment): void
    {
        $this->nodes = [];

        foreach ($this->meta as $lessonId => $meta) {
            $slots = $assignment[(string) $lessonId] ?? $assignment[$lessonId] ?? [];

            foreach ($slots as $slot) {
                $periodId = (int) ($slot['period_id'] ?? 0);
                if ($periodId <= 0 || ! $this->periods->has($periodId)) {
                    continue;
                }

                $locked = ! empty($slot['locked']) || $meta['lesson_locked'];

                $this->nodes[] = [
                    'lesson_id' => (int) $lessonId,
                    'profesor_id' => $meta['profesor_id'],
                    'grupo_estable_id' => $meta['grupo_estable_id'],
                    'is_half_group' => $meta['is_half_group'],
                    'allow_shared_teacher' => $meta['allow_shared_teacher'],
                    'shift_id' => $meta['shift_id'],
                    'room_type_required' => $meta['room_type_required'],
                    'locked' => $locked,
                    'period_id' => $periodId,
                    'room_id' => ! empty($slot['room_id']) ? (int) $slot['room_id'] : null,
                    'is_practical' => (bool) ($slot['is_practical'] ?? false),
                    'domain' => $this->buildDomain($meta, $periodId, $locked),
                ];
            }
        }
    }

    /**
     * Dominio de períodos permitidos (restricciones duras de disponibilidad y
     * turno). El período actual siempre se conserva para no volver infactible
     * el problema.
     *
     * @return list<int>
     */
    private function buildDomain(array $meta, int $currentPeriodId, bool $locked): array
    {
        $domain = [];

        foreach ($this->periods as $period) {
            if ((int) $period->shift_id !== (int) $meta['shift_id']) {
                continue;
            }
            if (! $this->availability->isAvailable($meta['calendar_id'], $meta['profesor_id'], $period)) {
                continue;
            }
            $domain[] = (int) $period->id;
        }

        if (! $locked) {
            foreach ($this->periods as $period) {
                if ((int) $period->shift_id === (int) $meta['shift_id']) {
                    continue;
                }
                if (! $this->availability->isAvailable($meta['calendar_id'], $meta['profesor_id'], $period)) {
                    continue;
                }
                $domain[] = (int) $period->id;
            }
        }

        if (! in_array($currentPeriodId, $domain, true)) {
            $domain[] = $currentPeriodId;
        }

        return array_values(array_unique($domain));
    }

    private function loadLessons(TimetableCalendar $calendar): void
    {
        $lessons = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->whereHas('pevaluacion', fn ($query) => $query->where('seccion_id', $this->sectionId))
            ->with('pevaluacion')
            ->get();

        $this->meta = [];
        foreach ($lessons as $lesson) {
            $pev = $lesson->pevaluacion;
            if (! $pev) {
                continue;
            }

            $this->meta[(int) $lesson->id] = [
                'calendar_id' => (int) $calendar->id,
                'profesor_id' => (int) $pev->profesor_id,
                'seccion_id' => (int) $pev->seccion_id,
                'grupo_estable_id' => $pev->grupo_estable_id ? (int) $pev->grupo_estable_id : null,
                'is_half_group' => (bool) $lesson->is_half_group,
                'allow_shared_teacher' => (bool) $lesson->allow_shared_teacher,
                'shift_id' => (int) $lesson->shift_id,
                'room_type_required' => $lesson->room_type_required,
                'lesson_locked' => (bool) $lesson->locked,
            ];
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // Teoría de grafos / coloreo
    // ────────────────────────────────────────────────────────────────────────

    private function buildConflictGraph(): void
    {
        $count = count($this->nodes);
        $this->adjacency = array_fill(0, $count, []);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                if ($this->nodesConflict($this->nodes[$i], $this->nodes[$j])) {
                    $this->adjacency[$i][] = $j;
                    $this->adjacency[$j][] = $i;
                }
            }
        }
    }

    private function nodesConflict(array $a, array $b): bool
    {
        // Docente: choque salvo que AMBOS permitan docente compartido.
        if ($a['profesor_id'] === $b['profesor_id']
            && ! ($a['allow_shared_teacher'] && $b['allow_shared_teacher'])) {
            return true;
        }

        // Aula.
        if ($a['room_id'] !== null && $a['room_id'] === $b['room_id']) {
            return true;
        }

        // Sección.
        if ($a['is_half_group'] && $b['is_half_group']) {
            return $this->maxSubjects < 2;
        }
        if ($a['is_half_group'] !== $b['is_half_group']) {
            return true;
        }

        $aGroup = $a['grupo_estable_id'];
        $bGroup = $b['grupo_estable_id'];

        if ($aGroup === null || $bGroup === null) {
            return true;
        }

        return $aGroup === $bGroup;
    }

    private function colorOrderDsatur(): array
    {
        $count = count($this->nodes);
        $colorOf = array_fill(0, $count, null);
        $saturation = array_fill(0, $count, 0);
        $neighborColors = array_fill(0, $count, []);
        $order = [];

        for ($step = 0; $step < $count; $step++) {
            $best = -1;
            $bestSat = -1;
            $bestDegree = -1;

            for ($i = 0; $i < $count; $i++) {
                if ($colorOf[$i] !== null) {
                    continue;
                }
                $sat = count($neighborColors[$i]);
                $degree = count($this->adjacency[$i]);
                if ($sat > $bestSat || ($sat === $bestSat && $degree > $bestDegree)) {
                    $best = $i;
                    $bestSat = $sat;
                    $bestDegree = $degree;
                }
            }

            if ($best < 0) {
                break;
            }

            $order[] = $best;
            $colorOf[$best] = -1;
            foreach ($this->adjacency[$best] as $neighbor) {
                $neighborColors[$neighbor][$best] = true;
            }
        }

        return $order;
    }

    private function colorOrderWelshPowell(): array
    {
        $count = count($this->nodes);
        $degrees = [];
        for ($i = 0; $i < $count; $i++) {
            $degrees[$i] = count($this->adjacency[$i]);
        }

        $order = range(0, max(0, $count - 1));
        usort($order, fn (int $a, int $b): int => $degrees[$b] <=> $degrees[$a]);

        return array_values($order);
    }

    private function colorOrderLargestDegree(): array
    {
        // Largest Degree First con remoción iterativa: en cada paso toma el nodo
        // con más conflictos pendientes.
        $count = count($this->nodes);
        $remaining = array_fill(0, $count, true);
        $order = [];

        for ($step = 0; $step < $count; $step++) {
            $best = -1;
            $bestDegree = -1;
            for ($i = 0; $i < $count; $i++) {
                if (! $remaining[$i]) {
                    continue;
                }
                $degree = 0;
                foreach ($this->adjacency[$i] as $neighbor) {
                    if ($remaining[$neighbor]) {
                        $degree++;
                    }
                }
                if ($degree > $bestDegree) {
                    $best = $i;
                    $bestDegree = $degree;
                }
            }
            if ($best < 0) {
                break;
            }
            $order[] = $best;
            $remaining[$best] = false;
        }

        return $order;
    }

    /**
     * Asigna colores (períodos) siguiendo un orden del grafo, eligiendo en cada
     * paso el período factible con menor costo.
     *
     * @param  list<int>  $order
     * @param  array<int, int>  $fallback
     * @return array<int, int>
     */
    private function greedyAssign(array $order, array $fallback): array
    {
        $assign = $fallback;
        $state = $this->baseState();

        // Los nodos bloqueados se fijan primero.
        foreach ($this->nodes as $index => $node) {
            if ($node['locked']) {
                $this->occupy($state, $node, (int) $assign[$index]);
            }
        }

        foreach ($order as $index) {
            $node = $this->nodes[$index];
            if ($node['locked']) {
                continue;
            }

            $bestPeriod = (int) $assign[$index];
            $bestCost = PHP_INT_MAX;

            foreach ($node['domain'] as $periodId) {
                if (! $this->isFree($node, $periodId, $state)) {
                    continue;
                }
                $cost = $this->periodSoftCost($node, $periodId);
                if ($cost < $bestCost) {
                    $bestCost = $cost;
                    $bestPeriod = $periodId;
                }
            }

            $assign[$index] = $bestPeriod;
            $this->occupy($state, $node, $bestPeriod);
        }

        return $assign;
    }

    // ────────────────────────────────────────────────────────────────────────
    // Teoría CSP: propagación hacia adelante + backtracking
    // ────────────────────────────────────────────────────────────────────────

    /**
     * @param  array<int, int>  $fallback
     * @return array<int, int>|null
     */
    private function cspSolve(array $fallback): ?array
    {
        $this->cspNodes = 0;
        $this->cspTimedOut = false;

        $order = $this->nodesByMrv();
        $state = $this->baseState();
        $assign = $fallback;

        foreach ($this->nodes as $index => $node) {
            if ($node['locked']) {
                $this->occupy($state, $node, (int) $assign[$index]);
            }
        }

        return $this->cspSearch($order, 0, $assign, $state);
    }

    /**
     * Orden MRV + grado: primero las variables con menos valores posibles y, a
     * igualdad, las de mayor grado (más restrictivas).
     *
     * @return list<int>
     */
    private function nodesByMrv(): array
    {
        $count = count($this->nodes);
        $order = [];
        for ($i = 0; $i < $count; $i++) {
            if (! $this->nodes[$i]['locked']) {
                $order[] = $i;
            }
        }

        usort($order, function (int $a, int $b): int {
            $domainA = count($this->nodes[$a]['domain']);
            $domainB = count($this->nodes[$b]['domain']);
            if ($domainA !== $domainB) {
                return $domainA <=> $domainB;
            }

            return count($this->adjacency[$b]) <=> count($this->adjacency[$a]);
        });

        return $order;
    }

    /**
     * @param  list<int>  $order
     * @param  array<int, int>  $assign
     * @param  array<string, mixed>  $state
     * @return array<int, int>|null
     */
    private function cspSearch(array $order, int $position, array $assign, array $state): ?array
    {
        if (++$this->cspNodes > $this->cspNodeBudget || microtime(true) > $this->deadline) {
            $this->cspTimedOut = true;

            return null;
        }

        if ($position >= count($order)) {
            return $assign;
        }

        $index = $order[$position];
        $node = $this->nodes[$index];

        foreach ($node['domain'] as $periodId) {
            if (! $this->isFree($node, $periodId, $state)) {
                continue;
            }

            $assign[$index] = $periodId;
            $this->occupy($state, $node, $periodId);

            if ($this->hasSupport($order, $position + 1, $state)) {
                $result = $this->cspSearch($order, $position + 1, $assign, $state);
                if ($result !== null) {
                    return $result;
                }
            }

            $this->release($state, $node, $periodId);

            if ($this->cspTimedOut) {
                return null;
            }
        }

        return null;
    }

    /**
     * Forward-checking: comprueba que toda variable futura conserve al menos un
     * valor factible.
     *
     * @param  list<int>  $order
     * @param  array<string, mixed>  $state
     */
    private function hasSupport(array $order, int $position, array $state): bool
    {
        $limit = min(count($order), $position + 12);

        for ($i = $position; $i < $limit; $i++) {
            $node = $this->nodes[$order[$i]];
            $supported = false;
            foreach ($node['domain'] as $periodId) {
                if ($this->isFree($node, $periodId, $state)) {
                    $supported = true;
                    break;
                }
            }
            if (! $supported) {
                return false;
            }
        }

        return true;
    }

    // ────────────────────────────────────────────────────────────────────────
    // Teoría MILP: función de penalizaciones + búsqueda local
    // ────────────────────────────────────────────────────────────────────────

    /**
     * @param  list<array{method:string, assign:array<int,int>, eval:array<string,int>}>  $candidates
     * @param  array<int, int>  $current
     * @param  array<string, int>  $before
     * @return array<int, int>
     */
    private function pickBest(array $candidates, array $current, array $before): array
    {
        $best = $current;
        $bestPenalty = $before['penalty'];

        foreach ($candidates as $candidate) {
            if ($candidate['eval']['penalty'] < $bestPenalty) {
                $best = $candidate['assign'];
                $bestPenalty = $candidate['eval']['penalty'];
            }
        }

        return $best;
    }

    /**
     * Búsqueda local (mejor mejora + reinicios) minimizando la función de
     * penalizaciones. Nunca empeora el estado inicial.
     *
     * @param  array<int, int>  $assign
     * @return array<int, int>
     */
    private function localSearch(array $assign): array
    {
        $best = $assign;
        $bestEval = $this->evaluate($best);

        $this->hillClimb($best);
        $eval = $this->evaluate($best);
        if ($eval['penalty'] < $bestEval['penalty']) {
            $bestEval = $eval;
        } else {
            $best = $assign;
        }

        for ($restart = 0; $restart < $this->restarts; $restart++) {
            if (microtime(true) > $this->deadline) {
                break;
            }
            if (($bestEval['hard'] + $bestEval['external']) === 0 && $bestEval['penalty'] === 0) {
                break;
            }

            $candidate = $best;
            $this->perturb($candidate, 7001 + $restart);
            $this->hillClimb($candidate);

            $eval = $this->evaluate($candidate);
            if ($eval['penalty'] < $bestEval['penalty']) {
                $best = $candidate;
                $bestEval = $eval;
            }
        }

        return $best;
    }

    /**
     * @param  array<int, int>  $assign
     */
    private function hillClimb(array &$assign): void
    {
        $current = $this->evaluate($assign)['penalty'];
        $iterations = 0;
        $probes = 0;

        while ($iterations++ < 80 && microtime(true) <= $this->deadline) {
            $bestDelta = 0;
            $bestMove = null;

            // Movimientos de un nodo.
            foreach ($this->nodes as $index => $node) {
                if ($node['locked']) {
                    continue;
                }
                foreach ($node['domain'] as $periodId) {
                    if ($periodId === (int) $assign[$index]) {
                        continue;
                    }
                    $probe = $assign;
                    $probe[$index] = $periodId;
                    $penalty = $this->evaluate($probe)['penalty'];
                    $delta = $current - $penalty;
                    if ($delta > $bestDelta) {
                        $bestDelta = $delta;
                        $bestMove = ['type' => 'move', 'i' => $index, 'p' => $periodId];
                    }
                    if ((++$probes % 400) === 0 && microtime(true) > $this->deadline) {
                        break 3;
                    }
                }
            }

            // Intercambios entre dos nodos.
            $count = count($this->nodes);
            for ($i = 0; $i < $count; $i++) {
                if ($this->nodes[$i]['locked']) {
                    continue;
                }
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($this->nodes[$j]['locked'] || (int) $assign[$i] === (int) $assign[$j]) {
                        continue;
                    }
                    $probe = $assign;
                    $probe[$i] = (int) $assign[$j];
                    $probe[$j] = (int) $assign[$i];
                    $penalty = $this->evaluate($probe)['penalty'];
                    $delta = $current - $penalty;
                    if ($delta > $bestDelta) {
                        $bestDelta = $delta;
                        $bestMove = ['type' => 'swap', 'i' => $i, 'j' => $j];
                    }
                    if ((++$probes % 400) === 0 && microtime(true) > $this->deadline) {
                        break 3;
                    }
                }
            }

            if ($bestMove === null) {
                break;
            }

            if ($bestMove['type'] === 'move') {
                $assign[$bestMove['i']] = $bestMove['p'];
            } else {
                $tmp = $assign[$bestMove['i']];
                $assign[$bestMove['i']] = $assign[$bestMove['j']];
                $assign[$bestMove['j']] = $tmp;
            }

            $current -= $bestDelta;
        }
    }

    /**
     * @param  array<int, int>  $assign
     */
    private function perturb(array &$assign, int $seed): void
    {
        mt_srand($seed);

        $movable = [];
        foreach ($this->nodes as $index => $node) {
            if (! $node['locked']) {
                $movable[] = $index;
            }
        }
        if ($movable === []) {
            return;
        }

        $moves = max(1, intdiv(count($movable), 3));
        for ($k = 0; $k < $moves; $k++) {
            $index = $movable[mt_rand(0, count($movable) - 1)];
            $domain = $this->nodes[$index]['domain'];
            shuffle($domain);
            foreach ($domain as $periodId) {
                if ($periodId !== (int) $assign[$index]) {
                    $assign[$index] = $periodId;
                    break;
                }
            }
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // Evaluación (restricciones duras + función objetivo blanda)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * @param  array<int, int>  $assign
     * @return array{hard:int, external:int, gaps:int, distribution:int, room:int, off_shift:int, penalty:int}
     */
    private function evaluate(array $assign): array
    {
        $state = $this->baseState();

        $hard = 0;
        $external = 0;
        $room = 0;
        $offShift = 0;

        $byTeacherDay = [];
        $byLessonDay = [];

        foreach ($this->nodes as $index => $node) {
            $periodId = (int) ($assign[$index] ?? $node['period_id']);
            $period = $this->periods->get($periodId);

            if (! $period) {
                $hard++;

                continue;
            }

            if (! $this->isFree($node, $periodId, $state)) {
                $hard++;
            }
            $this->occupy($state, $node, $periodId);

            if ($this->periodOverlapsExternal($node['profesor_id'], $period)) {
                $external++;
            }

            if ((int) $period->shift_id !== (int) $node['shift_id']) {
                $offShift++;
            }

            if ($node['is_practical'] && $node['room_id'] === null) {
                $room++;
            }

            $byTeacherDay[$node['profesor_id'].':'.$period->day_of_week][] = (int) $period->order_in_day;
            $byLessonDay[$node['lesson_id'].':'.$period->day_of_week] = ($byLessonDay[$node['lesson_id'].':'.$period->day_of_week] ?? 0) + 1;
        }

        $gaps = 0;
        foreach ($byTeacherDay as $orders) {
            $unique = array_values(array_unique($orders));
            $gaps += (max($unique) - min($unique) + 1) - count($unique);
        }

        $distribution = 0;
        foreach ($byLessonDay as $count) {
            $distribution += max(0, $count - 1);
        }

        $w = $this->weights;
        $penalty = $w['hard'] * $hard
            + $w['external'] * $external
            + $w['gap'] * $gaps
            + $w['distribution'] * $distribution
            + $w['room'] * $room
            + $w['off_shift'] * $offShift;

        return [
            'hard' => $hard,
            'external' => $external,
            'gaps' => $gaps,
            'distribution' => $distribution,
            'room' => $room,
            'off_shift' => $offShift,
            'penalty' => $penalty,
        ];
    }

    /**
     * Coste blando de colocar un nodo en un período (para las heurísticas de
     * coloreo), sin contar la penalización dura.
     */
    private function periodSoftCost(array $node, int $periodId): int
    {
        $period = $this->periods->get($periodId);
        if (! $period) {
            return PHP_INT_MAX;
        }

        $w = $this->weights;
        $cost = 0;

        if ($this->periodOverlapsExternal($node['profesor_id'], $period)) {
            $cost += $w['external'];
        }
        if ((int) $period->shift_id !== (int) $node['shift_id']) {
            $cost += $w['off_shift'];
        }

        return $cost;
    }

    /**
     * @param  array<int, int>  $assign
     * @return list<array{lesson_id:int, period_id:int, reason:string}>
     */
    private function validate(array $assign): array
    {
        $state = $this->baseState();
        $violations = [];

        foreach ($this->nodes as $index => $node) {
            $periodId = (int) ($assign[$index] ?? $node['period_id']);

            if (! $this->isFree($node, $periodId, $state)) {
                $violations[] = [
                    'lesson_id' => (int) $node['lesson_id'],
                    'period_id' => $periodId,
                    'reason' => 'conflicto_duro',
                ];
            }
            $this->occupy($state, $node, $periodId);
        }

        return $violations;
    }

    // ────────────────────────────────────────────────────────────────────────
    // Estado de ocupación
    // ────────────────────────────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private function baseState(): array
    {
        return [
            'teacher' => $this->extTeacher,
            'room' => $this->extRoom,
            'whole' => [],
            'half' => [],
            'group' => [],
            'group_count' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function isFree(array $node, int $periodId, array $state): bool
    {
        $teacherKey = $periodId.':'.$node['profesor_id'];
        $occupants = $state['teacher'][$teacherKey] ?? [];

        if ($occupants !== []) {
            $allShared = true;
            foreach ($occupants as $shared) {
                if (! $shared) {
                    $allShared = false;
                    break;
                }
            }
            if (! ($node['allow_shared_teacher'] && $allShared)) {
                return false;
            }
        }

        if ($node['room_id'] !== null && isset($state['room'][$periodId.':'.$node['room_id']])) {
            return false;
        }

        if (isset($state['whole'][$periodId])) {
            return false;
        }

        if ($node['is_half_group']) {
            return ($state['half'][$periodId] ?? 0) < $this->maxSubjects;
        }

        if ($node['grupo_estable_id'] === null) {
            return ($state['group_count'][$periodId] ?? 0) === 0
                && ($state['half'][$periodId] ?? 0) === 0;
        }

        return ! isset($state['group'][$periodId.':'.$node['grupo_estable_id']]);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function occupy(array &$state, array $node, int $periodId): void
    {
        $state['teacher'][$periodId.':'.$node['profesor_id']][$node['lesson_id']] = (bool) $node['allow_shared_teacher'];

        if ($node['room_id'] !== null) {
            $state['room'][$periodId.':'.$node['room_id']] = true;
        }

        if ($node['is_half_group']) {
            $state['half'][$periodId] = ($state['half'][$periodId] ?? 0) + 1;
        } elseif ($node['grupo_estable_id'] === null) {
            $state['whole'][$periodId] = true;
        } else {
            $state['group'][$periodId.':'.$node['grupo_estable_id']] = true;
            $state['group_count'][$periodId] = ($state['group_count'][$periodId] ?? 0) + 1;
        }
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function release(array &$state, array $node, int $periodId): void
    {
        $teacherKey = $periodId.':'.$node['profesor_id'];
        unset($state['teacher'][$teacherKey][$node['lesson_id']]);
        if (($state['teacher'][$teacherKey] ?? []) === []) {
            unset($state['teacher'][$teacherKey]);
        }

        if ($node['room_id'] !== null) {
            unset($state['room'][$periodId.':'.$node['room_id']]);
        }

        if ($node['is_half_group']) {
            $state['half'][$periodId] = ($state['half'][$periodId] ?? 1) - 1;
            if (($state['half'][$periodId] ?? 0) <= 0) {
                unset($state['half'][$periodId]);
            }
        } elseif ($node['grupo_estable_id'] === null) {
            unset($state['whole'][$periodId]);
        } else {
            unset($state['group'][$periodId.':'.$node['grupo_estable_id']]);
            $state['group_count'][$periodId] = ($state['group_count'][$periodId] ?? 1) - 1;
            if (($state['group_count'][$periodId] ?? 0) <= 0) {
                unset($state['group_count'][$periodId]);
            }
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // Ocupación externa
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Ocupación (docente/aula) de las OTRAS secciones del mismo calendario:
     * restricción dura.
     */
    private function buildExternalOccupancy(TimetableCalendar $calendar): void
    {
        $this->extTeacher = [];
        $this->extRoom = [];

        $sectionLessonIds = array_keys($this->meta);

        $slots = TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->whereNotIn('lesson_id', $sectionLessonIds)
            ->with('lesson:id,allow_shared_teacher')
            ->get(['period_id', 'lesson_id', 'profesor_id', 'room_id']);

        foreach ($slots as $slot) {
            $periodId = (int) $slot->period_id;
            $this->extTeacher[$periodId][(int) $slot->profesor_id][(int) $slot->lesson_id] = (bool) ($slot->lesson?->allow_shared_teacher);

            if ($slot->room_id !== null) {
                $this->extRoom[$periodId.':'.(int) $slot->room_id] = true;
            }
        }
    }

    /**
     * @return array<int, list<array{day:int, start:int, end:int}>>
     */
    private function externalTeacherBusyMap(TimetableCalendar $calendar): array
    {
        $otherCalendarIds = TimetableCalendar::query()
            ->forLapso($calendar->lapso_id)
            ->active()
            ->where('id', '!=', $calendar->id)
            ->when($calendar->pestudio_id, fn ($query) => $query->where('pestudio_id', '!=', $calendar->pestudio_id))
            ->pluck('id');

        if ($otherCalendarIds->isEmpty()) {
            return [];
        }

        $slots = TimetableSlot::query()
            ->whereIn('calendar_id', $otherCalendarIds)
            ->whereNotNull('profesor_id')
            ->whereHas('lesson.pevaluacion.seccion', fn ($query) => $query->where('seccions.status_active', 'true'))
            ->whereHas('lesson.pevaluacion.seccion.grado', fn ($query) => $query->where('grados.status_active', 'true'))
            ->with('period:id,day_of_week,start_time,end_time,is_break')
            ->get(['profesor_id', 'period_id']);

        $map = [];
        foreach ($slots as $slot) {
            $period = $slot->period;
            if (! $period || $period->is_break) {
                continue;
            }
            $map[(int) $slot->profesor_id][] = [
                'day' => (int) $period->day_of_week,
                'start' => $this->minutes((string) $period->start_time),
                'end' => $this->minutes((string) $period->end_time),
            ];
        }

        return $map;
    }

    private function periodOverlapsExternal(int $profesorId, TimetablePeriod $period): bool
    {
        foreach ($this->externalBusy[$profesorId] ?? [] as $busy) {
            if ($busy['day'] === (int) $period->day_of_week
                && $this->minutes((string) $period->start_time) < $busy['end']
                && $this->minutes((string) $period->end_time) > $busy['start']) {
                return true;
            }
        }

        return false;
    }

    private function minutes(string $time): int
    {
        [$hours, $minutes] = array_pad(explode(':', $time), 2, '0');

        return ((int) $hours) * 60 + (int) $minutes;
    }

    /**
     * @param  list<array{method:string, assign:array<int,int>, eval:array<string,int>}>  $candidates
     * @param  array<int, int>  $best
     */
    private function methodOf(array $candidates, array $best): string
    {
        foreach ($candidates as $candidate) {
            if ($candidate['assign'] === $best) {
                return $candidate['method'];
            }
        }

        return self::METHOD_LOCAL_SEARCH;
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyResult(): array
    {
        return [
            'assignment' => [],
            'moved' => 0,
            'collisions_before' => 0,
            'collisions_after' => 0,
            'hard_violations' => 0,
            'penalty_before' => 0,
            'penalty_after' => 0,
            'method' => self::METHOD_LOCAL_SEARCH,
            'report' => ['methods' => [], 'violations' => [], 'soft' => []],
        ];
    }
}
