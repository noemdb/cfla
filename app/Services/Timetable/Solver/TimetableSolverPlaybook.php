<?php

namespace App\Services\Timetable\Solver;

use App\Jobs\Timetable\GenerateTimetableJob;
use App\Services\Timetable\ConflictValidator;
use App\Services\Timetable\TimetableAvailabilityService;
use App\Services\Timetable\TimetablePublicationReadinessService;
use ReflectionClass;
use Throwable;

/**
 * SPEC-TIMETABLE-SNAPSHOT-001 §5.7 — Única fuente del bloque `playbook`.
 *
 * El snapshot del calendario embebe un bloque `playbook` para que un agente
 * pueda analizar y optimizar el horario sin acceso al código: describe el flujo
 * de la funcionalidad, las reglas duras/blandas del solver, las palancas
 * accionables, los diagnósticos, los límites y la configuración efectiva.
 *
 * Reglas de diseño (§5.7):
 *  1. Fuente única: esta clase es la única copia de las reglas.
 *  2. Excluido del checksum (§7): editarlo no invalida snapshots ya emitidos.
 *  3. Salida, nunca entrada: el restore NO lo lee ni aplica reglas desde él.
 *  4. Versionado propio (`playbook_version`).
 *
 * Para que no envejezca en silencio, el bloque se **deriva del código** cuando
 * es posible: los diagnósticos salen del enum `UnassignedReason`, el orden de
 * asignación de las constantes de `SolverAttemptConfig`, los límites de las
 * constantes de `TimetableSolver` y las métricas derivadas de
 * `SolverOutcome::halfGroupMetrics()`. Lo que queda como prosa (reglas duras,
 * palancas) es texto descriptivo, y la suite anti-deriva (§14, casos 17–19)
 * comprueba que los métodos citados existan de verdad.
 */
final class TimetableSolverPlaybook
{
    /** Revisión de las reglas descritas. Sube cuando cambie el contenido. */
    public const VERSION = 1;

    private const SOLVER = TimetableSolver::class;

    private const ORCHESTRATOR = TimetableSolverOrchestrator::class;

    private const CONTEXT = SchedulingContext::class;

    private const OUTCOME = SolverOutcome::class;

    private const JOB = GenerateTimetableJob::class;

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return [
            'playbook_version' => self::VERSION,
            'purpose' => 'Contexto operativo autosuficiente: permite analizar y optimizar este horario sin acceso al código.',
            'generated_from' => [
                'spec' => 'SPEC-TIMETABLE-SNAPSHOT-001',
                'module' => 'app/Services/Timetable/Solver',
                'entry' => 'GenerateTimetableJob::runSolver()',
            ],

            'flow' => [
                'generation' => [
                    'Carga las lessons activas del calendario; descarta las que no resuelven pevaluación/sección/profesor.',
                    'Reconstruye los bloqueos (locked) y detecta bloques preasignados (0 < existentes < requeridos).',
                    'Construye el dominio de períodos por turno (excluye is_break; turno preferido primero, con fallback salvo si la lección está locked).',
                    'Ordena las lecciones según el intento y, con half_group_priority activo, deja consecutivos los medio-grupos de una misma sección (HG-04).',
                    'Encadena intentos con distinto orden y semilla (S1, S1h si half_group_priority, S2, S3, S4r0..S4r{n}), conservando la mejor solución (keep-best).',
                    'Si queda residual, ejecuta la fase de reparación priorizando las lecciones sin asignar.',
                    'Persiste los slots, o guarda un preview si es dry-run. En ambos casos calcula las métricas de agrupación (HG-05).',
                ],
                'export' => [
                    'Recolecta periods, section_locks, availability, lessons y slots del calendario seleccionado.',
                    'Calcula el checksum semántico (§7) y serializa.',
                ],
                'preview' => [
                    'Valida formato/versión/checksum.',
                    'Calcula el diff contra la BD sin escribir (§9.2).',
                ],
                'apply' => [
                    'Revalida concurrencia.',
                    'Escribe el auto-backup.',
                    'Reemplaza dentro de una transacción (§9.3).',
                ],
            ],

            'objective' => [
                'kind' => 'lexicographic_keep_best',
                'primary' => 'bloques asignados (cobertura)',
                'secondary' => 'score soft: Σ comboScore por lección + bonus de agrupación de medio-grupos',
                'reference' => self::ref(self::ORCHESTRATOR, 'isBetter').' / '.self::ref(self::SOLVER, 'qualityScore'),
            ],

            'hard_rules' => $this->hardRules(),
            'soft_rules' => $this->softRules(),
            'levers' => $this->levers(),
            'diagnostics' => $this->diagnostics(),
            'limits' => $this->limits(),
            'config' => $this->config(),
            'derived_metrics' => $this->derivedMetrics(),

            'invariants' => [
                'El playbook es documentación de salida: el restore NUNCA lo lee ni aplica reglas desde él.',
                'No entra en el checksum (§7): editarlo no invalida snapshots existentes.',
                'Describe el solver en el momento del export; no sustituye al código ni es fuente de verdad.',
                'Los pesos y reglas viven en código y en config/timetable.php (variables de entorno); no existe una tabla de pesos configurable por calendario.',
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function hardRules(): array
    {
        return [
            ['id' => 'D-1', 'rule' => 'Un docente no puede tener dos clases en el mismo período.',
                'exception' => 'allow_shared_teacher: permitido solo si TODAS las lecciones ocupantes Y la candidata lo autorizan.',
                'method' => self::ref(self::CONTEXT, 'isFree')],
            ['id' => 'D-2', 'rule' => 'Un aula no puede estar ocupada dos veces en el mismo período.',
                'detail' => 'Solo aplica cuando room_id no es null (ADR-TT-008).',
                'method' => self::ref(self::CONTEXT, 'isFree')],
            ['id' => 'D-3', 'rule' => 'Una lección de sección completa bloquea la celda para toda la sección.',
                'method' => self::ref(self::CONTEXT, 'isFree')],
            ['id' => 'D-4', 'rule' => 'Una lección de sección completa exige la celda de sección totalmente vacía (sin subgrupos ni medio-grupos).',
                'method' => self::ref(self::CONTEXT, 'isFree')],
            ['id' => 'D-5', 'rule' => 'Una lección de subgrupo solo colisiona con su propio subgrupo.',
                'method' => self::ref(self::CONTEXT, 'isFree')],
            ['id' => 'D-6', 'rule' => 'Por celda se admiten hasta max_subjects_per_period medio-grupos.',
                'default' => 2, 'method' => self::ref(self::CONTEXT, 'isFree')],
            ['id' => 'D-7', 'rule' => 'El turno preferido es una preferencia con fallback en el solver, pero es duro en la edición manual.',
                'detail' => 'No hay fallback de turno cuando la lección está locked.',
                'method' => self::ref(self::JOB, 'buildAvailablePeriods').' / '.self::ref(ConflictValidator::class, 'validate')],
            ['id' => 'D-8', 'rule' => 'El docente debe estar disponible en el bloque.',
                'method' => self::ref(TimetableAvailabilityService::class, 'isAvailable')],
            ['id' => 'D-9', 'rule' => 'Las lecciones locked se reservan primero y no se reasignan; requieren lock completo (nº de bloques asignados == bloques requeridos).',
                'method' => self::ref(self::SOLVER, 'solve')],
            ['id' => 'D-10', 'rule' => 'Los períodos de receso (is_break) no admiten clases.',
                'method' => self::ref(self::JOB, 'buildAvailablePeriods')],
            ['id' => 'D-11', 'rule' => 'Los bloques preasignados deben ser válidos; si no, la lección queda sin asignar.',
                'method' => self::ref(self::SOLVER, 'solve')],
            ['id' => 'D-12', 'rule' => 'Una lección no puede repetir el mismo período entre su bloque teórico y el práctico.',
                'method' => self::ref(self::SOLVER, 'combinationsOfSize')],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function softRules(): array
    {
        return [
            'sign' => 'mayor score = mejor',
            'methods' => [
                'per_lesson' => self::ref(self::SOLVER, 'comboScore'),
                'per_assignment' => self::ref(self::SOLVER, 'qualityScore')
                    .' = Σ comboScore + '.self::ref(self::SOLVER, 'halfGroupGroupingScore'),
            ],
            'terms' => [
                ['id' => 'S-1', 'weight' => 100, 'term' => 'por cada día distinto usado por la lección', 'intent' => 'repartir la carga semanal'],
                ['id' => 'S-2', 'weight' => -50, 'term' => 'por cada par de días consecutivos usados por la lección', 'intent' => 'evitar bloques en días seguidos'],
                ['id' => 'S-3', 'weight' => -10, 'term' => 'por cada bloque teórico con order_in_day > 3', 'intent' => 'evitar teoría al final del día'],
                ['id' => 'S-4', 'weight' => '+half_group_bonus', 'term' => 'por cada bloque de una lección medio-grupo ubicado en una celda que ya tiene al menos otro medio-grupo de la misma sección', 'intent' => 'concentrar las mitades de una sección (HG-02)', 'applies_when' => 'half_group_priority && lección is_half_group'],
            ],
            'assignment_term' => [
                'id' => 'S-5',
                'method' => self::ref(self::SOLVER, 'halfGroupGroupingScore'),
                'weight' => '+half_group_bonus',
                'term' => 'Σ sobre celdas (período·sección) de max(0, mitades − 1) × half_group_bonus',
                'note' => 'Se suma una sola vez sobre la asignación completa. Es el desempate entre intentos con igual cobertura (HG-05): n mitades en una celda forman n−1 parejas.',
            ],
            'ordering' => $this->ordering(),
            'clustering' => [
                'method' => self::ref(self::SOLVER, 'clusterHalfGroupsBySection'),
                'effect' => "Reordenamiento estable que deja consecutivos los medio-grupos de una misma sección (cuando hay ≥2) para que el backtracking los explore juntos (HG-04). Se aplica en todo intento salvo 'half_group_first', y solo si half_group_priority.",
                'note' => 'Es blando: no obliga a que las mitades compartan período.',
            ],
        ];
    }

    /**
     * Orden de asignación: se deriva del mapa indexado por las constantes de
     * `SolverAttemptConfig`, de modo que añadir una constante sin documentarla
     * aquí hace fallar la suite anti-deriva (§14, caso 19).
     *
     * @return list<array{id: string, order: string, effect: string}>
     */
    private function ordering(): array
    {
        $map = [
            SolverAttemptConfig::ORDER_CONSTRAINT => 'Más restringida primero: priority*10 + (half_group_priority && is_half_group ? 6 : 0) + (room_type_required != null ? 5 : 0) + min(bloques, 9).',
            SolverAttemptConfig::ORDER_SCARCITY => 'Docentes con menos períodos disponibles primero.',
            SolverAttemptConfig::ORDER_BLOCKS_DESC => 'Lecciones con más bloques primero.',
            SolverAttemptConfig::ORDER_RANDOM => 'Barajado determinista por semilla (restarts).',
            SolverAttemptConfig::ORDER_REPAIR => 'Prioriza las lecciones que quedaron sin asignar.',
            SolverAttemptConfig::ORDER_HALF_GROUP_FIRST => "Medio-grupos primero (agrupados por sección asc), luego por grado de restricción. Intento 'S1h', solo si half_group_priority (HG-03).",
        ];

        $ordering = [];
        $index = 0;
        foreach ($map as $order => $effect) {
            $ordering[] = ['id' => 'O-'.(++$index), 'order' => $order, 'effect' => $effect];
        }

        return $ordering;
    }

    /**
     * @return list<array{field: string, effect: string}>
     */
    private function levers(): array
    {
        return [
            ['field' => 'priority', 'effect' => 'Se ubica antes (no es restricción dura).'],
            ['field' => 'locked', 'effect' => "Reserva bloques si el lock es completo; una lección medio-grupo nunca se trata como locked salvo en strategy 'legacy'."],
            ['field' => 'is_half_group', 'effect' => 'Ocupa la celda de su sección como medio-grupo (hasta max_subjects_per_period, D-6). Con half_group_priority se explora antes que los grupos completos (HG-01) y se prefiere la celda que ya agrupa mitades de la sección (HG-02/S-5).'],
            ['field' => 'allow_shared_teacher', 'effect' => 'Relaja solo D-1 (docente); nunca sección ni aula.'],
            ['field' => 'room_type_required', 'effect' => 'Solo se exige en bloques prácticos; los teóricos van sin aula.'],
            ['field' => 'shift_id', 'effect' => 'Turno preferido; puede haber fallback salvo en locked.'],
            ['field' => 'max_subjects_per_period', 'effect' => 'Tope de medio-grupos por celda.'],
            ['field' => 'strategy', 'effect' => "'legacy' reproduce los slots importados; 'optimized' (default) ejecuta el solver CSP."],
        ];
    }

    /**
     * Los códigos de diagnóstico se derivan del enum `UnassignedReason`, así que
     * `code`/`label`/`action` son idénticos a los del enum por construcción
     * (§14, caso 18).
     *
     * @return array<string, mixed>
     */
    private function diagnostics(): array
    {
        $codes = [];
        foreach (UnassignedReason::cases() as $reason) {
            $codes[] = [
                'code' => $reason->value,
                'label' => $reason->label(),
                'action' => $reason->action(),
            ];
        }

        return [
            'emitted_today' => ['capacity_exceeded', 'not_found'],
            'note' => "El pipeline clasifica el residual en DOS cubetas string: 'capacity_exceeded' (lección marcada por TimetableCapacityAuditService) y 'not_found' (el resto). Los seis códigos del enum UnassignedReason existen como vocabulario tipado, pero hoy NO se emiten desde ningún punto del pipeline: se documentan porque son la nomenclatura prevista.",
            'codes' => $codes,
            'grouping_warnings' => [
                [
                    'type' => 'half_group_isolated',
                    'source' => self::ref(TimetablePublicationReadinessService::class, 'isolatedHalfGroups'),
                    'message' => 'Hay {n} medio-grupo(s) sin agrupar en un mismo período con su par de sección.',
                    'blocking' => false,
                    'action' => 'Revisa si falta celda con tope disponible, hay choque de docente o disponibilidad bloqueada.',
                ],
            ],
        ];
    }

    /**
     * Límites del solver, leídos de las constantes reales de `TimetableSolver`
     * para que no envejezcan, más los de configuración.
     *
     * @return array<string, int>
     */
    private function limits(): array
    {
        return [
            'budget_seconds' => (int) config('timetable.solver.budget_seconds', 30),
            'attempt_seconds' => (int) config('timetable.solver.attempt_seconds', 8),
            'restarts' => (int) config('timetable.solver.restarts', 6),
            'repair_attempts' => 2, // default del constructor de TimetableSolverOrchestrator (no hay clave de config)
            'max_combos_per_lesson' => $this->solverConstant('MAX_COMBOS_PER_LESSON', 1000),
            'max_candidate_pool' => $this->solverConstant('MAX_CANDIDATE_POOL', 14),
            'max_candidate_pool_abs' => $this->solverConstant('MAX_CANDIDATE_POOL_ABS', 26),
            'max_combo_nodes' => $this->solverConstant('MAX_COMBO_NODES', 500000),
        ];
    }

    /**
     * Palancas activas al momento del export, leídas de config/timetable.php
     * (variables de entorno). No son defaults hardcodeados (§13).
     *
     * @return array<string, mixed>
     */
    private function config(): array
    {
        return [
            'half_group_priority' => (bool) config('timetable.solver.half_group_priority', true),
            'half_group_bonus' => (int) config('timetable.solver.half_group_bonus', 20),
            'note' => "Valores EFECTIVOS al momento del export, leídos de config('timetable.solver.*'). No son defaults hardcodeados: si el entorno cambia la variable, el playbook lo refleja.",
        ];
    }

    /**
     * Métricas de agrupación: no se almacenan en el snapshot (son recalculables
     * desde los slots) pero se documentan con las MISMAS claves que produce
     * `SolverOutcome::halfGroupMetrics()` (§14, caso 21).
     *
     * @return array<string, mixed>
     */
    private function derivedMetrics(): array
    {
        $probe = new SolverOutcome(
            new AttemptResult('probe', new SolverResult([], [], false, 0.0), 0, 0),
            [],
        );
        $descriptions = [
            'half_group_lessons' => 'lecciones is_half_group, asignadas + sin asignar',
            'half_group_grouped_periods' => 'celdas (período·sección) con ≥2 mitades',
            'half_group_isolated' => 'celdas (período·sección) con exactamente 1 mitad',
            'half_group_unassigned' => 'mitades sin ningún slot',
        ];

        $keys = [];
        foreach (array_keys($probe->halfGroupMetrics([])) as $key) {
            $keys[$key] = $descriptions[$key] ?? '';
        }

        return [
            'note' => 'No se almacenan en el snapshot (son recalculables desde los slots); se documentan para que un analista use las mismas definiciones que el sistema (SolverOutcome::halfGroupMetrics()).',
            'keys' => $keys,
        ];
    }

    /** Valor de una constante (posiblemente privada) de TimetableSolver. */
    private function solverConstant(string $name, int $fallback): int
    {
        try {
            $constant = (new ReflectionClass(self::SOLVER))->getReflectionConstant($name);
            if ($constant !== false) {
                return (int) $constant->getValue();
            }
        } catch (Throwable) {
            // Sin acceso a la constante: se usa el valor documentado.
        }

        return $fallback;
    }

    /** Referencia `Clase::metodo()` (FQCN sin barra inicial) para el análisis del LLM. */
    private static function ref(string $class, string $method): string
    {
        return $class.'::'.$method.'()';
    }
}
