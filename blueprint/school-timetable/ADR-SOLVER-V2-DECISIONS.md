# ADR: Timetable Solver v2 — Decisiones Arquitectónicas

**ADR-TT-012** — Mejoras contextualizadas al blueprint PLAN-TIMETABLE-SOLVER-FALLBACK-001  
**Fecha**: 2026-09-13  
**Estado**: Propuesta → Espera aprobación  
**Afecta**: Todos los módulos que generan horarios (Planning, Coordinación, Inicial)

---

## 📋 Tabla de Contenidos

1. [ADR-TT-012-01: Testing Pattern (Pest + DatabaseTransactions)](#adr-tt-012-01)
2. [ADR-TT-012-02: DTOs con Enums + Readonly](#adr-tt-012-02)
3. [ADR-TT-012-03: Feature Flags (Pennant) para Rollout](#adr-tt-012-03)
4. [ADR-TT-012-04: Broadcasting Real-Time (Reverb)](#adr-tt-012-04)
5. [ADR-TT-012-05: Presupuesto Dinámico por Contexto](#adr-tt-012-05)
6. [ADR-TT-012-06: Audit Trail para Solver Invocations](#adr-tt-012-06)
7. [ADR-TT-012-07: Pre-check Factibilidad (C-1 Detection)](#adr-tt-012-07)

---

<a name="adr-tt-012-01"></a>

## ADR-TT-012-01: Testing Pattern (Pest + DatabaseTransactions)

**Decisión**: Usar `DatabaseTransactions` trait de Pest + factories en lugar de seeders destructivos.

### Contexto
- **Restricción cfla**: ⚠️ **PROHIBIDO `migrate:fresh`**, incluso en tests (incidente 2026-09-07)
- Tests deben ejecutarse contra BD real (s2627, SQLite en testing)
- Reproducibilidad: cada test auto-rollback de cambios

### Alternativas Consideradas

| Alternativa | Pros | Contras | Decisión |
|---|---|---|---|
| **DatabaseTransactions + factories** | ✅ Auto-rollback, BD real, seguro | Más setup inicial | ✅ **ELEGIDA** |
| `migrate:fresh` + seeders | Limpio, estado conocido | ❌ Violaría restricción cfla, lento | ❌ Descartado |
| In-memory (SQLite :memory:) | Rápido, aislado | ❌ Schema desincronizado, quirks lunes no testeable | ❌ Descartado |
| Mocks/Stubs de BD | Rápido, unitario | ❌ No valida SQL real, regresiones no detectadas | ❌ Parcial (solo unit, no feature) |

### Decision
✅ **DatabaseTransactions + Factories + Seeders persistentes**

```php
// tests/Feature/Timetable/SolverOrchestratorTest.php
use Pest\Testing\Concerns\DatabaseTransactions;

test('orchestrator keeps best solution', function () {
    DatabaseTransactions::begin();  // Explicit (auto-implícito en Pest)
    
    $calendar = Calendar::factory()->create();
    $lesson1 = Lesson::factory()->for($calendar)->create(['weekly_blocks' => 4]);
    $lesson2 = Lesson::factory()->for($calendar)->create(['weekly_blocks' => 5]);
    
    $outcome = (new TimetableSolverOrchestrator())->orchestrate($dto);
    
    expect($outcome->bestResult->coverage)->toBeGreaterThanOrEqual(92);
    // Auto-rollback al terminar test
})->transaction();
```

### Implicaciones
- ✅ Seguro (sin dropeos)
- ✅ Reproducible (mismo factory seed → mismo resultado si no hay randomness)
- ✅ Rápido (transacciones vs migraciones)
- ⚠️ Quirk lunes debe estar en schema base (no creado en test)

### Dependencias
- Pest PHP ≥ 2.0 (ya en cfla)
- Factories en `database/factories/`
- Seeder base en `database/seeders/TimetableTestDataSeeder.php`

---

<a name="adr-tt-012-02"></a>

## ADR-TT-012-02: DTOs con Enums + Readonly

**Decisión**: Usar `readonly class` + `enum UnassignedReason` en lugar de arrays/strings loose.

### Contexto
- Solver retorna múltiples DTOs con datos complejos (attempts, coverage, unassigned reasons)
- Risk: typos en keys, falta de autocomplete IDE, refactoring fragile
- cfla es PHP 8.2+ → soporte full para readonly/enums

### Alternativas

| Alternativa | Pros | Contras | Decisión |
|---|---|---|---|
| **Readonly + Enums** | ✅ Type-safe, IDE autocomplete, immutable | Más boilerplate inicial | ✅ **ELEGIDA** |
| Mutable Eloquent-like model | ✅ Familiar, validations | ❌ Overengineered para DTO, mutable = bugs | ❌ Descartado |
| Loose arrays | ✅ Rápido | ❌ Sin tipos, fácil typos, no escalable | ❌ Descartado |
| Mapped Collections | ✅ Flexible | ❌ Type-loose, overhead | ❌ Descartado |

### Decision
✅ **Readonly classes + Enum UnassignedReason**

```php
// app/Services/Timetable/Solver/UnassignedReason.php
enum UnassignedReason: string {
    case CapacityExceeded = 'capacity_exceeded';
    case NotFound = 'not_found';
    case ComboCapReached = 'combo_cap_reached';
    case TimeoutReached = 'timeout_reached';
    case IncompleteInitialSetup = 'incomplete_initial_setup';
    case RepairFailed = 'repair_failed';
}

// app/Services/Timetable/Solver/SolverOutcome.php
readonly class SolverOutcome {
    public function __construct(
        public AttemptResult $bestResult,
        public array $attempts,              // AttemptResult[]
        public bool $timedOut,
        public array $unassignedReasons,     // [UnassignedReason => int]
        public int $totalElapsed_ms,
    ) {}
}
```

### Implicaciones
- ✅ Type-safe: Larastan/PhpStan detectan errores
- ✅ IDE autocomplete: `$reason->case` vs `$reason['case']`
- ✅ Immutable: no accidentes por mutación
- ✅ Serializable a JSON (enum values)
- ⚠️ Frontend debe mapear enum values a UI strings

### Dependencias
- PHP 8.2+ (ya en cfla)
- Larastan para static analysis (recomendado)
- Frontend: mapeo enum → labels en Blade

---

<a name="adr-tt-012-03"></a>

## ADR-TT-012-03: Feature Flags (Pennant) para Rollout

**Decisión**: Usar Laravel Pennant para A/B testing v1 vs v2 en producción.

### Contexto
- v2 es un cambio significativo en lógica de búsqueda
- Risk: silently degradar cobertura sin notarse
- cfla es Laravel 10+ → Pennant integrado
- Necesidad: gradual rollout (20% → 50% → 100%)

### Alternativas

| Alternativa | Pros | Contras | Decisión |
|---|---|---|---|
| **Pennant (Laravel native)** | ✅ Built-in, scoped, A/B ready | ⚠️ Requiere config inicial | ✅ **ELEGIDA** |
| Config file (env vars) | ✅ Simple | ❌ Manual toggle, no A/B, no scoping | ❌ Descartado |
| Custom toggle service | ✅ Control total | ❌ Reinventar rueda, maintenance | ❌ Descartado |
| Chaos engineering tool | ✅ Sophisticated | ❌ Overkill, overhead | ❌ Descartado |

### Decision
✅ **Pennant feature gates con scoping por user role**

```php
// config/pennant.php (o AppServiceProvider)
Feature::define('timetable-solver-v2', function (User $user) {
    // Testing: solo admins
    if (app()->environment('testing')) {
        return $user->is_admin;
    }
    
    // Production: gradual rollout
    return match ($user->is_admin) {
        true => hash('sha256', $user->id) % 100 < 20,  // 20% admins
        false => false,                                  // 0% otros
    };
});
```

**Flow**:
1. **Phase 1 (Day 1-3)**: 20% de admins → monitoring
2. **Phase 2 (Day 4-7)**: 50% de admins si OK
3. **Phase 3 (Day 8+)**: 100% si cobertura ≥ baseline

### Implicaciones
- ✅ Rollback instant: `Feature::deactivate()`
- ✅ A/B testing metrics: compare v1 vs v2 users
- ✅ Zero downtime: ambas versiones conviven
- ✅ User-scoped: no afecta a todos simultáneamente
- ⚠️ Observabilidad: registrar feature flag en logs

### Dependencias
- Laravel 10+ (ya en cfla)
- Pennant driver (database/cache/redis)
- Metrics dashboard para tracking

---

<a name="adr-tt-012-04"></a>

## ADR-TT-012-04: Broadcasting Real-Time (Reverb)

**Decisión**: Usar Laravel Reverb para broadcast de progreso del solver a UI.

### Contexto
- Solver puede tardar 30-60s
- UX actual: usuario espera sin feedback
- cfla ya usa Reverb (supervisor-reverb.conf) para debates
- Reutilizar infraestructura existente

### Alternativas

| Alternativa | Pros | Contras | Decisión |
|---|---|---|---|
| **Reverb (Laravel native)** | ✅ Ya configurado en cfla, real-time, Pusher protocol | ⚠️ Overhead WebSocket | ✅ **ELEGIDA** |
| Polling (AJAX cada 2s) | ✅ Simple | ❌ Lag, overhead HTTP, user frustration | ❌ Descartado |
| Server-Sent Events (SSE) | ✅ Unidireccional, eficiente | ❌ No en cfla actualmente | ❌ Parcial (future) |
| WebSockets custom | ✅ Control total | ❌ Duplicar Reverb, maintenance | ❌ Descartado |

### Decision
✅ **Reverb Broadcasting con eventos Eloquent**

```php
// app/Events/Timetable/SolverAttemptCompleted.php
class SolverAttemptCompleted implements ShouldBroadcast {
    public function broadcastOn(): array {
        return [
            new PrivateChannel("timetable.calendar.{$this->calendarId}"),
        ];
    }
    
    public function broadcastWith(): array {
        return [
            'attempt_id' => $this->attempt->id,
            'coverage' => $this->attempt->coverage,
            'elapsed_ms' => $this->attempt->elapsed_ms,
        ];
    }
}
```

**UI (Livewire 3)**:
```blade
<script>
    window.Echo.private(`timetable.calendar.{{ $calendar->id }}`)
        .listen('SolverAttemptCompleted', (data) => {
            Livewire.dispatch('attempt-completed', data);
        });
</script>
```

### Implicaciones
- ✅ Real-time feedback (250-500ms latency)
- ✅ Escalable (Reverb ya en prod)
- ✅ Secure (private channels, auth via Livewire)
- ✅ Low overhead (reusa WebSocket existente)
- ⚠️ Requiere Reverb running (`artisan reverb:start`)

### Dependencias
- Laravel Reverb ≥ 0.1 (ya en cfla)
- WebSocket client en frontend (Echo.js)
- Supervisor config para reverb:start

---

<a name="adr-tt-012-05"></a>

## ADR-TT-012-05: Presupuesto Dinámico por Contexto

**Decisión**: Presupuesto de solver varía según contexto (planning vs preview), no global único.

### Contexto
- `/app/planning/timetable`: Admin, puede esperar 60s
- `/app/coordinacion/timetable`: Coordinador, prefiere 45s
- Dry-run en preview: usuario en navegador, máximo 15s
- v1 usa `timeLimit=30` global

### Alternativas

| Alternativa | Pros | Contras | Decisión |
|---|---|---|---|
| **Dinámico por contexto** | ✅ Optimizado por SLA, mejor UX | ⚠️ Config más compleja | ✅ **ELEGIDA** |
| Global fixed 60s | ✅ Simple | ❌ Overkill para preview, insuficiente para planning | ❌ Descartado |
| Por usuario role + calendar size | ✅ Fino | ❌ Overcomplicado, marginal ROI | ❌ Descartado |

### Decision
✅ **Config anidado `config/timetable.php` por contexto**

```php
// config/timetable.php
'solver' => [
    'budget_ms' => [
        'default' => 30000,
        'planning.admin' => 60000,
        'coordinacion.coordinator' => 45000,
        'preview' => 15000,
    ],
],
```

**En Job**:
```php
$context = "{$request->route()->getName()}.{$user->role}";
$budgetMs = config("timetable.solver.budget_ms.{$context}",
    config('timetable.solver.budget_ms.default'));

$outcome = $orchestrator->orchestrate($dto, budgetMs: $budgetMs);
```

### Implicaciones
- ✅ SLA-driven: cada contexto obtiene presupuesto justo
- ✅ UX mejorada: preview no espera 60s
- ✅ Config centralizado
- ⚠️ Debe inferir contexto correctamente (route + role)

### Dependencias
- `config/timetable.php` con estructura anidada
- Route names claramente definidos
- User role migration (is_admin, is_profesor, etc.)

---

<a name="adr-tt-012-06"></a>

## ADR-TT-012-06: Audit Trail para Solver Invocations

**Decisión**: Registrar cada invocación a solver en tabla `timetable_solver_audits` con quién/cuándo/resultado.

### Contexto
- Debugging: "¿por qué ese usuario no ve cobertura 100%?"
- Compliance: auditoría de cambios en horarios
- Metrics: quién invoca solver, cuántas veces
- cfla usa roles (is_admin, is_diagnostic, is_profesor)

### Alternativas

| Alternativa | Pros | Contras | Decisión |
|---|---|---|---|
| **Audit DB + logs** | ✅ Queryable, reporte, compliance | ⚠️ Más storage | ✅ **ELEGIDA** |
| Solo logs (file) | ✅ Simple | ❌ No queryable, fácil perder, no escalable | ❌ Descartado |
| Eloquent model observers | ✅ Automático | ❌ Frágil, overhead | ❌ Parcial |

### Decision
✅ **Tabla `timetable_solver_audits` + Listener**

```php
// database/migrations/YYYY_MM_DD_create_timetable_solver_audits_table.php
Schema::create('timetable_solver_audits', function (Blueprint $table) {
    $table->id();
    $table->uuid('correlation_id')->unique();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('user_role')->nullable();  // is_admin, is_diagnostic, etc.
    $table->unsignedBigInteger('calendar_id');
    $table->string('context');                 // planning|coordinacion|preview
    $table->decimal('coverage_pct', 5, 2);
    $table->array('unassigned_reasons')->nullable(); // JSON: [reason => count]
    $table->boolean('timed_out')->default(false);
    $table->integer('elapsed_ms');
    $table->timestamps();
    
    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    $table->foreign('calendar_id')->references('id')->on('calendars');
    $table->index('user_id');
    $table->index('calendar_id');
    $table->index('created_at');
});
```

**Listener**:
```php
// app/Listeners/LogTimetableSolverInvocation.php
class LogTimetableSolverInvocation {
    public function handle(SolverCompleted $event): void {
        SolverAudit::create([
            'correlation_id' => $event->correlationId,
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'calendar_id' => $event->calendarId,
            'context' => request()->route()->getName(),
            'coverage_pct' => $event->outcome->bestResult->coverage,
            'unassigned_reasons' => $event->outcome->unassignedReasons,
            'timed_out' => $event->outcome->timedOut,
            'elapsed_ms' => $event->outcome->totalElapsed_ms,
        ]);
    }
}
```

### Implicaciones
- ✅ Queryable: `SolverAudit::whereUserId($id)->whereCoveragePctLessThan(95)`
- ✅ Compliance-ready
- ✅ Debugging: correlation_id ↔ logs
- ⚠️ Tabla crece: 1000 invocs/day = 30K/month (considerar archiving)

### Dependencias
- Migration + model `SolverAudit`
- Event listener registrado
- Retention policy (archiving / purge > 90 days)

---

<a name="adr-tt-012-07"></a>

## ADR-TT-012-07: Pre-check Factibilidad (C-1 Detection)

**Decisión**: Antes de invocar solver, validar que cada lección tiene capacidad suficiente.

### Contexto
- Problema C-1: bloques requeridos > períodos disponibles = imposible
- Hoy: solver tira no-asignada sin feedback
- User frustración: "¿por qué no asignó?"
- Solución: detectar y reportar con `reason = capacity_exceeded` + sugerencias

### Alternativas

| Alternativa | Pros | Contras | Decisión |
|---|---|---|---|
| **Pre-check + reportar** | ✅ Fail-fast, feedback claro, user guidance | ⚠️ Overhead validación | ✅ **ELEGIDA** |
| Solver solo, post-analyze | ✅ Simple | ❌ User frustración, sin acción clara | ❌ Descartado |
| IA auto-fix de horas | ✅ Magical | ❌ Riesgo de cambios no deseados, out-of-scope | ❌ Descartado |

### Decision
✅ **Método `feasibilityPrecheck()` en Orchestrator**

```php
// app/Services/Timetable/Solver/TimetableSolverOrchestrator.php
protected function feasibilityPrecheck(TimetableDTO $dto): array {
    $unassigned_reasons = [];
    
    foreach ($dto->lessons as $lesson) {
        $assignable_periods = $this->countAssignablePeriods($lesson);
        $blocks_needed = $lesson->normalized_blocks;
        
        if ($assignable_periods < $blocks_needed) {
            $unassigned_reasons[$lesson->id] = UnassignedReason::CapacityExceeded;
            
            Log::warning('Lesson infeasible by capacity', [
                'lesson_id' => $lesson->id,
                'blocks_needed' => $blocks_needed,
                'assignable_periods' => $assignable_periods,
                'teacher_id' => $lesson->teacher_id,
                'section_id' => $lesson->section_id,
            ]);
        }
    }
    
    return $unassigned_reasons;
}
```

**Flow**:
```php
public function orchestrate(TimetableDTO $dto): SolverOutcome {
    // 1. Pre-check
    $infeasible = $this->feasibilityPrecheck($dto);
    if ($infeasible) {
        // Si TODAS las lecciones son infactibles → early return
        if (count($infeasible) === count($dto->lessons)) {
            return new SolverOutcome(
                bestResult: /* empty */,
                unassignedReasons: $infeasible,
                timedOut: false,
                ...
            );
        }
    }
    
    // 2. Proceder con orquestador (para lecciones factibles)
    // ...
}
```

### Implicaciones
- ✅ Fail-fast: detecta antes de buscar
- ✅ User feedback claro: "Exceso de horas en Matemática (16 bloques vs 10 períodos disponibles)"
- ✅ Reduce desperdicios: 30s búsqueda inútil
- ⚠️ Overhead O(n) negligible en pre-check

### Dependencias
- `countAssignablePeriods($lesson)`: método que cuenta períodos libres por turno
- Grid validation (incluye quirk lunes)
- Logging para debugging

---

## 🎯 Matriz de Decisiones

| ADR | Decisión | Alternativa rechazada | Por qué | Risk mitigation |
|---|---|---|---|---|
| 012-01 | DatabaseTransactions + factories | migrate:fresh | Violaría restricción cfla | Seeder base persisten, factories estables |
| 012-02 | Readonly + Enums | Loose arrays | Type-safety, IDE support | Larastan static analysis |
| 012-03 | Pennant feature gates | Custom toggle | Native, built-in, A/B ready | Pennant docs, gradual rollout |
| 012-04 | Reverb broadcasting | Polling AJAX | Real-time, ya en infraestructura | Reverb monitoring, WebSocket uptime |
| 012-05 | Dinámico por contexto | Global fixed | SLA-driven UX | Config clear, tests de valores |
| 012-06 | Audit DB + logs | Solo logs | Queryable, compliance | Retention policy, archiving strategy |
| 012-07 | Pre-check factibilidad | Solver solo | Fail-fast, user guidance | Logging detallado, test fixtures |

---

## 🔄 Trade-offs Summary

| Aspecto | Ganancia | Costo | Aceptable |
|---|---|---|---|
| Type safety | Alto (IDE, Larastan) | Boilerplate inicial | ✅ Sí |
| Rollout seguro | Alto (gradual, rollback) | Config Pennant | ✅ Sí |
| Real-time feedback | Medio (UX) | WebSocket overhead | ✅ Sí (ya tiene Reverb) |
| Audit trail | Medio (compliance) | Storage, query overhead | ✅ Sí (retention policy) |
| Pre-check | Medio (user guidance) | O(n) overhead | ✅ Sí (negligible) |

---

## 📝 Revisión de ADRs

| ADR | Reviewer | Feedback | Status |
|---|---|---|---|
| 012-01..07 | TBD | Pendiente | 🔴 Draft |
| | | | |

**Próximo paso**: Revisión por tech lead + arquitecto cfla.

---

**Documento**: ADR-SOLVER-V2-DECISIONS.md  
**ADRs**: 7 (012-01 a 012-07)  
**Estado**: Propuesta  
**Fecha**: 2026-09-13  
**Autor**: Claude Code
