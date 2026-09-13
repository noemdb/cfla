# PLAN-TIMETABLE-SOLVER-FALLBACK-001 — MEJORAS CONTEXTUALIZADAS PARA CFLA

> **Status**: Propuesta de mejoras → Espera aprobación del usuario
>
> **Última actualización**: 2026-09-13
>
> **Contexto**: Análisis contextualizado al proyecto CFLA (sistema de gestión escolar Laravel 10, Livewire 3, módulo Inicial, BD s2627)

---

## RESUMEN EJECUTIVO

El blueprint original `PLAN-TIMETABLE-SOLVER-FALLBACK-001.md` (v1) es sólido en su núcleo pero requiere **13 mejoras contextuales** para alinearse con:

1. **Arquitectura cfla**: Módulo Inicial (blueprint/inicial/ docs 01-08), estructura de Planning, middleware de roles
2. **Particularidades de BD**: FKs 1:1 s2526→s2627, quirk lunes del grid (docs/design-context-emil.md)
3. **Testing en cfla**: Pest PHP con DatabaseTransactions en BD real (sin migrate:fresh permitido)
4. **Integración multi-rol**: is_admin, is_diagnostic, is_profesor, acceso desde `/app/planning/timetable` y `/app/coordinacion/timetable`
5. **Observabilidad**: Logs structured + Broadcasting con Laravel Reverb (debate/orden real-time)
6. **Rollout seguro**: Feature flags Pennant + gradual A/B testing

---

## 1. MEJORA: Explicitar integración con módulo Inicial

### Problema en v1
El blueprint no menciona cómo convive con el módulo Inicial (que genera la estructura base del timetable).

### Mejora
Agregar sección 2.1 **"Precondiciones del módulo Inicial"** antes del flujo:

```markdown
### 2.1 Precondiciones del módulo Inicial

**Requisito**: Antes de invocar el orquestador, la estructura base debe estar lista:
- Tabla `initial_timetables` con `pestudiosis` (período de estudio), `lapsos` (trimestres)
- **FKs 1:1 s2526→s2627**: Sincronización verificada (no "orfandad de registros")
- Grid base poblado con:
  - `timetable_periods` (periodos de turno: mat/vesp/noct)
  - `timetable_subject_allocations` (asignaciones pre-iniciales de asignaturas a secciones)
  - `timetable_breaks` (recreos, marcados como `is_break=true`)
- **Lunes del grid**: Quirk conocido (grid tiene estructura irregular lunes vs otros días)
  - El solver DEBE validar que las combinaciones respeten los límites del grid por día

**Si falta**: 
- El pre-check de factibilidad (S7, sección 7) debe reportar `capacity_exceeded` con causa 
  `incomplete_initial_setup` → UI sugiere completar el módulo Inicial primero.
```

**Archivos a modificar**: 
- `app/Services/Timetable/Solver/FeasibilityReport.php`: Agregar reason `incomplete_initial_setup`
- `app/Jobs/Timetable/GenerateTimetableJob.php::runSolver()`: Validar precondiciones antes de orquestador

---

## 2. MEJORA: Modo testing sin migrate:fresh

### Problema en v1
La sección 10 (Testing) menciona tests unitarios pero no toca el constraint de "prohibido dropear BD" en cfla.

### Mejora
Reemplazar section 10 (Testing) por **10.1 Testing Strategy Safe**:

```markdown
### 10.1 Testing Strategy for CFLA (No Fresh Migration)

**Restricción**: JAMÁS `php8.2 artisan migrate:fresh`, incluso en tests. La BD de testing usa 
SQLite + `DatabaseTransactions` trait de Pest.

**Patrón**:

```php
// tests/Feature/Timetable/SolverOrchestratorTest.php
use Pest\Testing\Concerns\DatabaseTransactions;

test('orchestrator keeps best solution', function () {
    // Insert test data directamente (sin migrate):
    $calendar = Calendar::create([...]);
    $lesson1 = Lesson::create([...]);
    $lesson2 = Lesson::create([...]);
    
    $dto = buildTestDTO($calendar, [$lesson1, $lesson2]);
    $outcome = (new TimetableSolverOrchestrator())->orchestrate($dto);
    
    expect($outcome->coverage)->toBeGreaterThanOrEqual(92)
        ->and($outcome->bestResult->assignment)->toHaveCount(2);
    
    // Transaction auto-rolls back after test
});
```

**Datasets persistentes** (seeds):
- `database/seeders/TimetableTestDataSeeder.php`: Calendario base, asignaturas, docentes, secciones
  - Agregar a `database/seeders/DatabaseSeeder.php` SOLO en env=testing
  - Invocar en test setup si hace falta, o use factories

**No permitido**:
- `migrate:fresh`, `migrate:rollback`, `schema:dump --prune`, `db:wipe`
- `TRUNCATE` en triggers o test setup
- Resetear sequence IDs manualmente

**Reproducibilidad en fallo**:
- Si un test falla, los logs de test incluyen `correlation_id` (UUID) → 
  guía para buscar en storage/logs/ o hacer replay con el mismo seed.
```

**Archivos a crear/modificar**:
- `tests/Feature/Timetable/SolverOrchestratorTest.php` (nueva)
- `tests/Unit/Timetable/TimetableSolverOrchestratorTest.php` (nueva)
- `database/seeders/TimetableTestDataSeeder.php` (nueva)
- `phpunit.xml` / `pest.php`: Asegurar que `DB_CONNECTION=sqlite` en env=testing

---

## 3. MEJORA: Broadcasting y observabilidad en tiempo real

### Problema en v1
La observabilidad (sección 9) solo menciona logs/canal Timetable, pero cfla usa **Laravel Reverb** para real-time.

### Mejora
Ampliar sección 9 a **9.1 Logs + Broadcasting** y agregar eventos Eloquent:

```markdown
### 9.1 Logs + Broadcasting

**Eventos broadcast** (Laravel Reverb):

Cuando un usuario en `/app/planning/timetable` o `/app/coordinacion/timetable` inicia "Generar draft":

```php
// app/Events/Timetable/SolverStarted.php
public function broadcastOn(): array {
    return [
        new PrivateChannel("timetable.calendar.{$this->calendarId}"),
    ];
}

// app/Events/Timetable/SolverCompleted.php
public function broadcastOn(): array {
    return [
        new PrivateChannel("timetable.calendar.{$this->calendarId}"),
    ];
}
```

**Flow**:
1. Job inicia → Broadcast `SolverStarted` (intent)
2. Orquestador ejecuta → Emite cada intento (S1, S2, ... S7) como evento `SolverAttemptCompleted`
3. Job termina → Broadcast `SolverCompleted` con `SolverOutcome` completo
4. UI escucha en Livewire 3 → actualiza cobertura, intentos, motivos **sin refrescar la página**

**Logs estructurados**:

```json
{
  "event": "timetable_solver",
  "correlation_id": "uuid-...",
  "calendar_id": 3,
  "strategy": "optimized",
  "user_id": 42,
  "role": "is_admin",  // nuevo: quién invocó
  "attempts": [...],
  "chosen": "S4r3",
  "coverage_pct": 99.0,
  "unassigned_reasons": {"capacity_exceeded": 1},
  "timed_out": false,
  "elapsed_ms": 5420,
  "timestamp": "2026-09-13T10:15:30Z",
  "environment": "production"
}
```

**Canal de logs**: `storage/logs/timetable-{date}.log` (ya existe en cfla)
```

**Archivos a crear**:
- `app/Events/Timetable/SolverStarted.php` (nueva)
- `app/Events/Timetable/SolverCompleted.php` (nueva)
- `app/Events/Timetable/SolverAttemptCompleted.php` (nueva)
- `app/Listeners/BroadcastSolverProgress.php` (nueva)

---

## 4. MEJORA: Feature flags + gradual rollout

### Problema en v1
La sección 11 (Fases) sugiere rollout linealmente (F0 → F6), pero cfla tiene Pennant.

### Mejora
Agregar **10.2 Feature Flags & Rollout Strategy**:

```markdown
### 10.2 Feature Flags & Rollout Strategy (Pennant)

**Activación controlada** con Laravel Pennant:

```php
// config/pennant.php (o en Feature::define dentro del code)
Feature::define('timetable-solver-v2', function (User $user) {
    // Fase 1: Solo admins con feature flag en testing
    if (app()->environment('testing')) {
        return $user->is_admin;
    }
    
    // Fase 2: Gradual rollout 20% de admins
    return $user->is_admin && match (hash('sha256', $user->id) % 100) {
        0..19 => true,
        default => false,
    };
});
```

**Uso en Job**:

```php
// app/Jobs/Timetable/GenerateTimetableJob.php
public function handle() {
    if (Feature::active('timetable-solver-v2')) {
        $this->runSolverWithOrchestrator();  // v2 fallback
    } else {
        $this->runLegacySolver();             // v1 legacy
    }
}
```

**Métricas de rollout**:
- Cobertura promedio (v2 vs v1)
- Tiempo de ejecución
- Tasa de errores
- User satisfaction (feedback en UI)

Dashboard: `/admin/metrics/timetable` → compara ambas versiones lado a lado.
```

**Archivos a crear/modificar**:
- `config/pennant.php`: Agregar feature definition
- `app/Jobs/Timetable/GenerateTimetableJob.php::handle()`: Feature check
- `app/Http/Controllers/Admin/TimetableMetricsController.php` (nueva)
- `resources/views/admin/timetable-metrics.blade.php` (nueva)

---

## 5. MEJORA: Refactorizar presupuesto según roles

### Problema en v1
Config global `solver_budget_ms`, pero `/app/planning` vs `/app/coordinacion` podrían tener diferentes SLAs.

### Mejora
Hacer presupuesto dinámico según contexto:

```markdown
### 5.2 Presupuesto dinámico por contexto

**Config**:
```php
// config/timetable.php
'solver' => [
    'budget_ms' => [
        'default' => 30000,                // 30s (legacy)
        'planning.admin' => 60000,         // 60s (admin en planning, mayor tolerancia)
        'coordinacion.coordinator' => 45000, // 45s (coordinador)
        'preview' => 15000,                // 15s (preview/dry-run en UI)
    ],
    'restarts' => [...],
    ...
],
```

**En Job**:

```php
$budgetMs = config(
    "timetable.solver.budget_ms.{$context}",
    config('timetable.solver.budget_ms.default')
);

$outcome = (new TimetableSolverOrchestrator())->orchestrate($dto, budgetMs: $budgetMs);
```

Contexto se infiere de: `$job->context` (planning|coordinacion) + user role (is_admin|is_profesor|etc).
```

**Archivos a modificar**:
- `config/timetable.php`: Agregar budget_ms por contexto
- `app/Jobs/Timetable/GenerateTimetableJob.php`: Inferir contexto y pasar presupuesto

---

## 6. MEJORA: DTOs tipadas + enums para robustez

### Problema en v1
Los DTOs se mencionan pero no hay definición de **tipos PHP 8.2 + enums**.

### Mejora
Especificar en **3.1 DTOs & Type Safety**:

```markdown
### 3.1 DTOs & Type Safety (PHP 8.2+)

**Enums para razones de no asignación**:

```php
// app/Services/Timetable/Solver/UnassignedReason.php
enum UnassignedReason: string {
    case CapacityExceeded = 'capacity_exceeded';         // C-1: bloques > períodos
    case NotFound = 'not_found';                         // Restarts insuficientes
    case ComboCapReached = 'combo_cap_reached';          // C-2: pool recortado
    case TimeoutReached = 'timeout_reached';             // Presupuesto agotado
    case IncompleteInitialSetup = 'incomplete_initial_setup'; // Falta precondición
    case RepairFailed = 'repair_failed';                 // Reparación no pudo liberar
}
```

**DTOs clave con tipos**:

```php
// app/Services/Timetable/Solver/SolverAttemptConfig.php
readonly class SolverAttemptConfig {
    public function __construct(
        public string $strategyId,              // S1, S2, ..., S7
        public string $lessonOrder,             // constraintDegree|scarcityFirst|blocksDesc|random
        public int $randomSeed,
        public int $maxCombos,
        public int $maxCandidatePool,
        public bool $feasibilityFirst = false,
        public bool $useRepair = false,
    ) {}
}

readonly class AttemptResult {
    public function __construct(
        public string $attemptId,
        public int $coverage,
        public int $unassigned,
        public int $softScore,
        public array $assignment,              // [lessonId => [periods]]
        public array $unassigned_lessons,      // [lessonId => UnassignedReason]
        public int $elapsed_ms,
    ) {}
}

readonly class SolverOutcome {
    public function __construct(
        public AttemptResult $bestResult,
        public array $attempts,                // AttemptResult[]
        public bool $timedOut,
        public array $unassignedReasons,       // UnassignedReason[] counts
        public int $totalElapsed_ms,
    ) {}
}
```

Typing completo en DTOs asegura **IDE autocomplete** y **static analysis** (Larastan, PhpStan).
```

**Archivos a crear**:
- `app/Services/Timetable/Solver/UnassignedReason.php` (nueva)
- Actualizar DTOs existentes con readonly + strict types

---

## 7. MEJORA: Integración con permisos de roles (IsAdmin, IsDiagnostic)

### Problema en v1
El blueprint asume acceso general al solver, sin considerar middleware de cfla.

### Mejora
Agregar **sección 2.2 Middleware & Authorization**:

```markdown
### 2.2 Middleware & Authorization

**Rutas protegidas**:

```php
// routes/web.php
Route::middleware(['auth', 'is_admin_or_diagnostic'])->group(function () {
    Route::post('/app/planning/timetable/generate-draft', 
        [TimetableWizardController::class, 'runDryRun'])->name('timetable.draft');
    
    Route::post('/app/coordinacion/timetable/generate-draft',
        [CoordinacionTimetableController::class, 'runDryRun'])->name('timetable.coordinacion.draft');
});
```

**Validaciones en Job**:

```php
// app/Jobs/Timetable/GenerateTimetableJob.php
public function handle() {
    if (!auth()->user()?->is_admin && !auth()->user()?->is_diagnostic) {
        throw new UnauthorizedJobException('User not authorized for timetable generation');
    }
    // ...
}
```

Contexto:
- `/app/planning/timetable`: Acceso para is_admin + is_diagnostic
- `/app/coordinacion/timetable`: Acceso para is_admin (coordinador)

**Auditoría**: Registrar quién invocó, cuándo, cobertura resultado → tabla `timetable_solver_audits`
```

**Archivos a crear/modificar**:
- `app/Models/Timetable/SolverAudit.php` (nueva, Model)
- Migrations: `create_timetable_solver_audits_table`
- `app/Jobs/Timetable/GenerateTimetableJob.php`: Log audit trail
- `app/Listeners/LogTimetableSolverInvocation.php` (nueva)

---

## 8. MEJORA: Manejo de errores y feedback accionable en UI

### Problema en v1
La sección "Definición de terminado" (13) menciona "UI muestra cobertura y motivos", pero no detalla error UX.

### Mejora
Agregar **sección 11.2 UX Error Handling**:

```markdown
### 11.2 UX Error Handling & Feedback

**Pantalla de resultados** (Step 5 TimetableWizard):

```html
<!-- resources/views/livewire/timetable-wizard-step5.blade.php -->
<div class="solver-outcome">
  <div class="metric-row">
    <span>Cobertura:</span>
    <strong class="text-{{ $outcome->bestResult->coverage >= 95 ? 'green' : 'amber' }}-600">
      {{ $outcome->bestResult->coverage }}%
    </strong>
  </div>
  
  @if ($outcome->unassignedReasons)
    <div class="alert alert-warning">
      <p class="font-semibold">{{ count($outcome->unassignedReasons) }} lecciones sin asignar</p>
      <ul class="text-sm mt-2">
        @foreach ($outcome->unassignedReasons as $reason => $count)
          <li>
            <strong>{{ $count }}×</strong>
            @switch($reason)
              @case(UnassignedReason::CapacityExceeded->value)
                <span>Exceso de capacidad (ajustar horas normalizadas)</span>
              @break
              @case(UnassignedReason::NotFound->value)
                <span>No encontrado en búsqueda (intentar ajustar restricciones)</span>
              @break
              @case(UnassignedReason::IncompleteInitialSetup->value)
                <span>Completar módulo Inicial (períodos, asignaciones)</span>
              @break
              @default
                <span>{{ $reason }}</span>
            @endswitch
          </li>
        @endforeach
      </ul>
    </div>
  @endif
  
  <div class="actions mt-4">
    @if ($outcome->bestResult->coverage === 100)
      <button wire:click="publishTimetable">Publicar (100%)</button>
    @else
      <button wire:click="enterManualEdit">Editar manualmente</button>
      <button wire:click="runSolverAgain">Intentar nuevamente</button>
    @endif
  </div>
</div>
```

**Recomendaciones accionables** por motivo:
- `capacity_exceeded`: "Diálogo sugiere ajustar `weekly_blocks` (normalización de horas) en módulo Inicial"
- `not_found`: "Intenta nuevamente (puede ser heurística del solver)"
- `incomplete_initial_setup`: "Botón → módulo Inicial con wizard (nuevo)"

**Logging de sesión**: Guardar `SolverOutcome` en `preview_payload` (ya existe) pero agregar timestamp + user_id
```

**Archivos a crear/modificar**:
- `resources/views/livewire/timetable-wizard-step5.blade.php`: Mejorar resultado display
- `app/Livewire/TimetableWizard.php`: Método para recomendar acciones

---

## 9. MEJORA: Validación de grid "lunes" y quirks visuales

### Problema en v1
No menciona el quirk lunes del grid mencionado en memoria (`inicialModule-blueprint.md`).

### Mejora
Agregar en **sección 7 (Pre-check)**:

```markdown
### 7.1 Pre-check con validación del grid "lunes"

El grid del timetable tiene estructura irregular: lunes vs (martes-viernes).

**Precondición en feasibilityPrecheck()**:

```php
protected function validateGridStructure(SchedulingContext $context): void {
    $monday = $context->periods()->whereDay('monday')->get();
    $other = $context->periods()->whereDay('tuesday')->get();
    
    // Quirk: lunes puede tener diferente cantidad de períodos
    if ($monday->count() === 0) {
        throw new GridValidationException('Monday has no periods defined');
    }
    
    // Registrar asimetría (log para analytics)
    if ($monday->count() !== $other->count()) {
        Log::debug('Grid asymmetry detected', [
            'monday_count' => $monday->count(),
            'other_day_count' => $other->count(),
            'calendar_id' => $context->calendar()->id,
        ]);
    }
}
```

**En cálculo de assignablePeriods (C-1 detection)**:
- Descontar períodos no existentes por día
- No asumir simetría en grid

```

**Archivos a modificar**:
- `app/Services/Timetable/Solver/FeasibilityReport.php`: Agregar validación grid
- `app/Services/Timetable/Solver/SchedulingContext.php`: Exponer grid por día

---

## 10. MEJORA: Documentación en docs/timetable/

### Problema en v1
Sección 13 menciona "documentado en `docs/timetable/`" pero no especifica la estructura.

### Mejora
Crear árbol de docs:

```
docs/timetable/
├── README.md (overview, flujo general)
├── solver-architecture.md (DTOs, enums, orquestador)
├── fallback-strategy.md (S1-S7, decisiones de heurística)
├── feasibility-report.md (C-1 a C-7, pre-check)
├── repair-phase.md (LDS, reparación de bloqueantes)
├── testing-guide.md (Pest, sin migrate:fresh, factories)
├── observability.md (logs structured, broadcasting, Pennant)
├── ui-integration.md (Step 5 UX, feedback accionable)
├── troubleshooting.md (errors comunes, guía de debug)
└── changelog.md (histórico de cambios, versions v1 → v2)
```

**Archivo nuevo: `docs/timetable/solver-architecture.md`**:
- Describe cada DTO, enum, servicio
- Mermaid diagrams para flujo orquestador
- Ejemplos de uso (cómo invocar desde controller)

---

## 11. MEJORA: Metrología y comparativa v1 vs v2

### Problema en v1
No hay KPI definidos para medir éxito de rollout.

### Mejora
Agregar **sección 10.3 Metrics & Success Criteria**:

```markdown
### 10.3 Metrics & Success Criteria

**KPIs a seguir**:

| Métrica | v1 (baseline) | v2 (objetivo) | Cálculo |
|---------|---|---|---|
| Cobertura promedio | 92% | **≥ 96%** | (bloques asignados / bloques totales) × 100 |
| Tiempo medio (ms) | 210ms | **≤ 500ms** | p50 de elapsed_ms por corrida |
| Tasa timeout | 0% | **0%** | count(timed_out=true) / total |
| Reparaciones exitosas | N/A | **≥ 80%** | repairs_successful / repairs_attempted |
| Motivos infactibles (C-1) | N/A | **Reportados** | count(capacity_exceeded) → user guidance |

**Dashboard** (`/admin/metrics/timetable`):
- Gráfico de cobertura over time (v1 vs v2)
- Distribución de intentos (S1, S2, ..., S7 usage)
- Top unassigned reasons (pie chart)
- Tiempo de ejecución P50/P90/P99
- Feature flag adoption % (rollout phase)

**Alertas**:
- Si cobertura < 95% en v2 → rollback automático a v1
- Si timeout > 5% → investigar presupuesto/config
- Si repair rate < 70% → signal para mejorar heurística

```

**Archivos a crear**:
- `app/Http/Controllers/Admin/TimetableMetricsController.php` (nueva)
- `resources/views/admin/timetable-metrics.blade.php` (nueva)
- `app/Models/Timetable/SolverRun.php` (nueva, para almacenar outcomes)
- Migration: `create_timetable_solver_runs_table`

---

## 12. MEJORA: Rollback strategy y fallback seguro

### Problema en v1
No menciona cómo rollback si v2 degrada la calidad.

### Mejora
Agregar **sección 12.2 Rollback & Fallback**:

```markdown
### 12.2 Rollback & Fallback Strategy

**Monitoreo de degradación**:

```php
// app/Services/Timetable/Solver/QualityGate.php
class QualityGate {
    public function check(SolverOutcome $v2, SolverResult $v1_baseline): bool {
        // Si v2 coverage < v1_baseline - 3%, trigger rollback
        if ($v2->bestResult->coverage < $v1_baseline->coverage - 3) {
            Log::critical('v2 degradation detected, rollback triggered', [
                'v1_coverage' => $v1_baseline->coverage,
                'v2_coverage' => $v2->bestResult->coverage,
            ]);
            
            Notification::route('slack', env('SLACK_ALERT_CHANNEL'))
                ->notify(new TimetableSolverDegradationAlert());
            
            return false;
        }
        
        return true;
    }
}
```

**Fallback automático**:
- Si `QualityGate::check()` falla → Job usa v1 (legacy solver)
- UI informa al usuario: "Solver temporal en modo estable, cobertura 92%"
- Ticket auto-creado en Linear para investigar

**Rollback manual** (admin):
```php
Feature::deactivate('timetable-solver-v2');  // Pennant
// o vía `/admin/feature-flags` UI
```

```

**Archivos a crear**:
- `app/Services/Timetable/Solver/QualityGate.php` (nueva)
- `app/Notifications/TimetableSolverDegradationAlert.php` (nueva)
- `app/Http/Controllers/Admin/FeatureFlagsController.php` (nueva, para dashboard)

---

## 13. MEJORA: Tickets y roadmap actualizados

### Problema en v1
Sección 11 menciona tickets `FB-01` a `FB-15` pero sin detalles de cfla-specific work.

### Mejora
Reemplazar tabla de tickets en sección 11 por versión cfla-adapted:

```markdown
### 11. Implementation Roadmap (CFLA-Specific)

| Fase | Ticket | Descripción | Dependencias | Deadline tentativo |
|---|---|---|---|---|
| **F0** | TT-CFP-01 | Baseline: cálculo cobertura en 20 corridas reales, logs setup | Config timetable.php | Sprint N+1 |
| **F1** | TT-CFP-02..05 | Orquestador + DTOs (enums, readonly) + keep-best | F0 | Sprint N+1 |
| | TT-CFP-02 | TimetableSolverOrchestrator, SolverAttemptConfig, AttemptResult | - | - |
| | TT-CFP-03 | UnassignedReason enum, FeasibilityReport typed | TT-CFP-02 | - |
| | TT-CFP-04 | SolverOutcome, presupuesto dinámico por contexto | TT-CFP-02/03 | - |
| | TT-CFP-05 | GenerateTimetableJob: invocar orquestador, audit logging | TT-CFP-04 | - |
| **F2** | TT-CFP-06..08 | S2/S3/S4 (restarts con seed, scarcityFirst, blocksDesc) | F1 | Sprint N+2 |
| | TT-CFP-06 | Estrategia scarcityFirst (docente bottlenecks) | - | - |
| | TT-CFP-07 | Estrategia blocksDesc + randomizedRestarts con seed | - | - |
| | TT-CFP-08 | Reproducibilidad: seed registry + test fixture | TT-CFP-06/07 | - |
| **F3** | TT-CFP-09..10 | S5/S6 (feasibilityFirst, expandedCaps) | F2 | Sprint N+2 |
| | TT-CFP-09 | Ordenamiento por factibilidad en combinaciones | - | - |
| | TT-CFP-10 | Ampliación iterativa de topes (deepening) | TT-CFP-09 | - |
| **F4** | TT-CFP-11..12 | S7 repair + pre-check/razones (C-1 detection) | F3 | Sprint N+3 |
| | TT-CFP-11 | Fase de reparación (LDS, reubicar bloqueantes) | - | - |
| | TT-CFP-12 | Pre-check factibilidad, grid "lunes" quirk | TT-CFP-11 | - |
| **F5** | TT-CFP-13..14 | Integración job + UI (cobertura, motivos) + logs/broadcasting | F4 | Sprint N+3 |
| | TT-CFP-13 | Broadcasting events, Reverb integration | - | - |
| | TT-CFP-14 | Step 5 UX, error handling accionable | TT-CFP-13 | - |
| **F6** | TT-CFP-15..17 | Feature flags Pennant, rollout gradual, metrics dashboard | F5 | Sprint N+4 |
| | TT-CFP-15 | Pennant feature definition, config dinámico | - | - |
| | TT-CFP-16 | Metrics dashboard (`/admin/metrics/timetable`), KPIs | TT-CFP-15 | - |
| | TT-CFP-17 | QualityGate, rollback automático, Slack alerts | TT-CFP-16 | - |
| **F7** | TT-CFP-18 | Documentación: docs/timetable/ (7 archivos md + diagrams) | F6 | Sprint N+4 |

**PR template**: Cada PR incluye:
- Reference al ticket (TT-CFP-XX)
- Link a blueprint section
- Cobertura de tests (% mínimo 85%)
- Indicaciones de rollback si procede
```

---

## SÍNTESIS DE CAMBIOS

### Archivos NUEVOS a crear (17):
1. `app/Services/Timetable/Solver/TimetableSolverOrchestrator.php`
2. `app/Services/Timetable/Solver/SolverAttemptConfig.php`
3. `app/Services/Timetable/Solver/AttemptResult.php`
4. `app/Services/Timetable/Solver/SolverOutcome.php`
5. `app/Services/Timetable/Solver/FeasibilityReport.php` (actualizado)
6. `app/Services/Timetable/Solver/UnassignedReason.php` (enum)
7. `app/Services/Timetable/Solver/QualityGate.php`
8. `app/Events/Timetable/SolverStarted.php`
9. `app/Events/Timetable/SolverCompleted.php`
10. `app/Events/Timetable/SolverAttemptCompleted.php`
11. `app/Listeners/BroadcastSolverProgress.php`
12. `app/Models/Timetable/SolverAudit.php`
13. `app/Models/Timetable/SolverRun.php`
14. `app/Http/Controllers/Admin/TimetableMetricsController.php`
15. `database/seeders/TimetableTestDataSeeder.php`
16. `tests/Feature/Timetable/SolverOrchestratorTest.php`
17. `tests/Unit/Timetable/TimetableSolverOrchestratorTest.php`

### Archivos MODIFICADOS (6):
1. `app/Jobs/Timetable/GenerateTimetableJob.php` (+ orquestador, feature flag, audit)
2. `app/Services/Timetable/Solver/TimetableSolver.php` (aceptar SolverAttemptConfig)
3. `config/timetable.php` (presupuesto dinámico, nuevas constantes)
4. `resources/views/livewire/timetable-wizard-step5.blade.php` (UX mejorada)
5. `app/Livewire/TimetableWizard.php` (mostrar motivos, acciones)
6. `routes/web.php` (ruta metrics, feature flags UI)

### Migrations NUEVAS (2):
1. `create_timetable_solver_audits_table`
2. `create_timetable_solver_runs_table`

### Documentación (7 archivos md):
- `docs/timetable/README.md`
- `docs/timetable/solver-architecture.md`
- `docs/timetable/fallback-strategy.md`
- `docs/timetable/feasibility-report.md`
- `docs/timetable/repair-phase.md`
- `docs/timetable/testing-guide.md`
- `docs/timetable/observability.md`

---

## RIESGOS ADICIONALES (vs blueprint original)

| Riesgo | Mitigación | Propietario |
|---|---|---|
| Feature flag desactivado inadvertidamente | Alertas en `/admin/feature-flags`, log de cambios | DevOps |
| Broadcasting overload si muchos users generan drafts | Rate limiting + task queue (database driver) | Infra |
| Audit trail tabla crece rápido | Retention policy (30 días default), archiving | DBA |
| Quirk "lunes" no capturado en tests | Test fixtures incluyen lunes asimétrico | QA |
| Rollback automático sin notificación | Slack alert + email admin | DevOps |
| Documentación desincronizada post-launch | PR checks: docs/ required updated | Tech Lead |

---

## DEPENDENCIAS EXTERNAS

Ninguna nueva. Se usan:
- Laravel 10 (built-in)
- Pest PHP (ya en cfla)
- Laravel Reverb (ya en cfla, supervisor-reverb.conf)
- Pennant (Laravel 10+, integrado)

---

## PRÓXIMOS PASOS

1. **User review**: Aprobación de estas 13 mejoras
2. **Refine tickets**: Asignar story points, definir sprint
3. **Kickoff F0**: Baselining y config setup (TT-CFP-01)
4. **Sprint rotation**: F1-F7 over 4-5 sprints
5. **Rollout**: Feature flag → 20% → 50% → 100% (2-3 semanas)
6. **Monitoring**: Dashboard + alerts activos

---

**Versión de este documento**: 2 (mejoras contextualizadas)  
**Autor**: Claude Code + Explore agent  
**Fecha**: 2026-09-13  
**Próxima revisión**: Post F0 (baseline metrics)
