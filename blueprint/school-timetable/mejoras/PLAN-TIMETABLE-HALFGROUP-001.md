# PLAN-TIMETABLE-HALFGROUP-001 — Prioridad y agrupación de medio-grupos en el solver

| | |
|---|---|
| **Estado** | Propuesta para implementación |
| **Versión** | 1.0 |
| **Fecha** | 2026-09-14 |
| **Módulo** | `app/Services/Timetable/Solver`, `app/Jobs/Timetable/GenerateTimetableJob.php` |
| **Documentos relacionados** | `SPEC-TIMETABLE-001-v2.md`, `SPEC-TIMETABLE-SHARED-TEACHER-001.md`, `mejoras/SPEC-TIMETABLE-MEJORAS-001.md`, `PLAN-TIMETABLE-SOLVER-FALLBACK-001-MEJORADO.md`, `ADR-SOLVER-V2-DECISIONS.md` |
| **IDs** | HG-01 … HG-12 |

## 1. Objetivo

Priorizar la asignación de las `TimetableLesson` con `is_half_group = true` y, en lo posible, **agrupar sus bloques dentro del mismo período de la sección**, respetando el tope de asignaturas en paralelo (`max_subjects_per_period`).

La meta operativa: que dos medios-grupos de la misma sección que deben convivir (p. ej. desdoble de Educación Física H/V o ITP) queden en la **misma celda** (turno · día · bloque) en lugar de dispersarse en períodos distintos, y que ninguna lección de medio-grupo quede sin asignar mientras un bloque completo o una lección de menor prioridad ocupa su celda.

## 2. Estado actual verificado

### 2.1 El tope de paralelos vive en el calendario, no en el pestudio

> **Hallazgo (HG-00).** El usuario pide agrupar "según `pestudio.max_subjects_per_period`". Esa columna **no existe en `pestudios`**. El tope está en `timetable_calendars.max_subjects_per_period` (migración `database/migrations/2026_09_09_000002_add_max_subjects_per_period_to_timetable_calendars.php:11`), default 2, rango validado 1..10 en el wizard (`TimetableWizard.php:428,500`).
>
> Como **un calendario pertenece a un único pestudio** (invariante INV-TT-009, ADR-TT-IMP-005), el valor efectivo del pestudio *es* el del calendario. Antes de implementar hay que decidir la fuente de verdad (§8, D1).

### 2.2 Qué existe hoy

| Punto | Comportamiento actual | Evidencia |
|---|---|---|
| Restricción dura | Dos medio-grupos de la misma sección pueden ocupar la misma celda; tope = `max_subjects_per_period` | `SchedulingContext::isFree():82-84`, `occupy():118-119` |
| Orden de dominio (débil) | Para medio-grupo, los candidatos se ordenan por `halfGroupLoad` **desc** (preferir celdas ya ocupadas por otro medio-grupo) | `TimetableSolver::buildDomain():415-425` |
| Pérdida de esa preferencia | `combinationsOfSize()` re-ordena todo por `comboScore`, que **no** incluye `halfGroupLoad`; el orden de dominio solo desempata | `TimetableSolver::combinationsOfSize():458-459`, `comboScore():563-588` |
| Orden de lecciones | `constraintDegree()` = `priority*10 + (roomType?5:0) + min(blocks,9)` — **no** considera `is_half_group` | `LessonToSchedule::constraintDegree():62-67` |
| Estrategia del orquestador | S1 constraint, S2 scarcity, S3 blocks_desc, S4 random, S7 repair — ninguna prioriza medio-grupos | `TimetableSolverOrchestrator::attemptConfigs():137-152` |
| Reconocimiento de pareja | Existe en validación/readiness, **no** en la búsqueda | `ConflictValidator.php:76-78`, `TimetablePublicationReadinessService.php:104-117` |
| Clave de unicidad | `slot_teacher_key`/`slot_section_key` incluyen `lesson_id` para half-group y shared-teacher | `2026_09_11_000002:23-42`, `2026_09_13_000002:44-53` |
| Peso de capacidad | El audit cuenta medio-grupo como 0.5 bloques | `TimetableCapacityAuditService::HALF_GROUP_WEIGHT` |

### 2.3 Síntomas

1. **Dispersión**: la preferencia de agrupación se pierde tras `comboScore`; dos medios-grupos de la misma sección pueden caer en días distintos sin penalización.
2. **Sin prioridad global**: si hay escasez de períodos, una lección de grupo completo puede consumir la celda que dos medios-grupos necesitaban, y estos quedan `unassigned`.
3. **Sin pareo explícito**: el solver no sabe que `A` y `B` son las dos mitades de una misma sección; los trata como lecciones independientes.
4. **Sin métrica**: no se mide ni reporta cuántos medio-grupos quedaron agrupados vs dispersos.

## 3. Decisiones de diseño

| # | Pregunta | Decisión propuesta | Motivo |
|---|---|---|---|
| D1 | Fuente del tope | Usar `timetable_calendars.max_subjects_per_period` (efectivo por pestudio). **No** migrar `pestudios` salvo que se pida explícitamente | Ya es el valor que gobierna solver, validador y audit; evita doble fuente de verdad |
| D2 | Alcance de la prioridad | Orden de lecciones + orden de combinaciones, **no** una regla dura nueva | No romper factibilidad ni las reglas existentes |
| D3 | Definición de "agrupar" | Minimizar el número de períodos distintos usados por los medio-grupos de una misma sección, respetando el tope | Es la interpretación operativa de "compartir slot" |
| D4 | Pareo | Agrupación *blanda* guiada por carga, sin pareo bipartito obligatorio | El pareo rígido puede volver infactibles datasets que hoy se resuelven |
| D5 | Prioridad configurable | Sí: un multiplicador/flag por calendario y el `priority` existente | Permite apagar la prioridad sin desplegar |
| D6 | Compatibilidad | Cero cambios en el contrato de persistencia y snapshot | La salida sigue siendo `SlotCandidate[]` |

## 4. Requisitos

### HG-01 — Orden de bloques: los medio-grupos primero

**Objetivo.** Que las lecciones `is_half_group` se exploren antes que las de grupo completo con igual o menor restricción.

**Cambio.** En `LessonToSchedule::constraintDegree()` incorporar el flag de medio-grupo con un peso relevante:

```php
public function constraintDegree(): int
{
    return ($this->priority * 10)
        + ($this->isHalfGroup ? 6 : 0)          // HG-01: medio-grupo pesa más
        + ($this->roomTypeRequired !== null ? 5 : 0)
        + min($this->blocksNeeded(), 9);
}
```

**Alternativa:** un `halfGroupBoost` explícito en `SolverAttemptConfig` para no alterar el orden por defecto sin opt-in (ver D5).

**Criterio de aceptación.**
- Con dos lecciones (una `isHalfGroup` y otra completa) y una sola celda disponible, la exploración intenta primero el medio-grupo.
- Suite `TimetableSolverTest` y `TimetableSolverOrchestratorTest` sigue verde.

### HG-02 — Orden de dominios: agrupar por carga de medio-grupo

**Objetivo.** Que cada bloque de un medio-grupo prefiera las celdas donde ya hay otro medio-grupo de la misma sección.

**Cambio.** En `buildDomain()` ya se ordena por `halfGroupLoad` desc; **propagar esa señal** al `comboScore` para que sobreviva al re-ordenamiento de `combinationsOfSize()`/`pickCombinations()`:

```php
private function comboScore(array $combo): int
{
    $score = 0;
    foreach ($combo as $slot) {
        $meta = $this->periodMeta[$slot->periodId] ?? null;
        if ($meta) {
            $days[$meta['day']] = ...;
            if (! $slot->isPractical && $meta['order'] > 3) { $score -= 10; }

            // HG-02: bonifica celdas que ya agrupan medio-grupos de la sección.
            $score += $this->halfGroupBonus($slot->periodId, $slot->seccionId);
        }
    }
    ...
}
```

`halfGroupBonus()` se alimenta de `SchedulingContext::halfGroupLoad()` (ya existe, `:36-39`). El bonus debe ser **menor** que el de "día distinto" (+100) para no romper la distribución, p. ej. `+20` por celda que ya tiene al menos un medio-grupo de la misma sección.

**Nota.** `comboScore()` hoy no recibe la sección; hay que pasarla (la lección sí la tiene). Cambio de firma interno, sin impacto externo.

**Criterio de aceptación.**
- Dado un medio-grupo con dos celdas libres (una con otro medio-grupo de la misma sección, otra vacía), el solver elige la celda compartida.
- La distribución por días de una lección normal no regresa.

### HG-03 — Prioridad de estrategia en el orquestador

**Objetivo.** Añadir un intento que priorice medio-grupos cuando la cobertura total no se alcanza.

**Cambio.** Nuevo `ORDER_HALF_GROUP_FIRST` en `SolverAttemptConfig` y en `TimetableSolver::orderLessons()`: ordena por `isHalfGroup` desc, luego por `halfGroupLoad` del dominio, luego por `constraintDegree`. Se inserta como intento temprano (p. ej. S1b) o como intento de reparación alternativo.

**Criterio de aceptación.**
- Un dataset donde el orden `constraint` deja medios-grupos sin asignar mejora la cobertura de medio-grupos con `half_group_first`.
- El orquestador sigue haciendo *early stop* con cobertura total.

### HG-04 — Pareo blando por sección (agrupación dirigida)

**Objetivo.** Intentar que los medio-grupos de una misma sección compartan el mayor número de períodos posible.

**Diseño.** Fase previa al backtracking (en el orquestador o en el solver) que:

1. Agrupa los `LessonToSchedule` con `isHalfGroup` por `seccionId`.
2. Emite un **orden de lecciones** que alterna los grupos de forma que dos mitades se exploren consecutivamente.
3. Opcionalmente, preasigna la primera mitad del grupo a un período y obliga a la segunda a considerar primero ese mismo período (soft, no `locked`).

**Restricción.** Nunca convertir el pareo en regla dura: si no hay celda conjunta viable, la segunda mitad puede irse a otro período (comportamiento actual). Esto preserva la invariante INV-TT-012 (medio-grupos comparten solo entre sí).

**Criterio de aceptación.**
- Con `T` períodos y `2k` medio-grupos de una sección, el número de períodos usados tiende a `k` (agrupados) sin aumentar `unassigned`.
- No se generan colisiones de docente/sección/aula.

### HG-05 — Métrica de agrupación

**Objetivo.** Medir y exponer el resultado.

**Métricas.**
- `half_group_lessons`: total de lecciones `is_half_group`.
- `half_group_grouped_periods`: períodos con ≥2 medios-grupos de la misma sección.
- `half_group_isolated`: medio-grupos en un período sin su par.
- `half_group_unassigned`.

**Integración.** Añadir a `SolverOutcome::attemptSummary()` (o a un nuevo `assignmentMetrics()`) y al `preview_payload` generado por `GenerateTimetableJob::storeDryRunPreview()`. El readiness (`TimetablePublicationReadinessService`) puede emitir un **warning** (no bloqueante) si hay medio-grupos aislados.

**Criterio de aceptación.**
- El dry-run muestra la métrica; un dataset totalmente agrupado reporta 0 aislados.

### HG-06 — Respeto estricto del tope

**Objetivo.** Garantizar que la agrupación nunca exceda `max_subjects_per_period`.

**Estado.** `SchedulingContext::isFree()` ya lo aplica (`:82-84`) y `ConflictValidator` también (`:92-94`). HG no debe relajarlo.

**Criterio de aceptación.**
- Con tope 2, un tercer medio-grupo de la misma sección en la misma celda se rechaza.
- Con tope 3 (configurable 1..10), tres medios-grupos comparten celda.

### HG-07 — No regresión de `locked`/`preassigned`

**Objetivo.** La nueva prioridad no altera reservas existentes.

**Criterio de aceptación.**
- Una lección de medio-grupo `locked` con lock completo conserva sus períodos (ADR-TT-007).
- Los `preassignedSlots` se respetan antes de cualquier priorización.

### HG-08 — Interacción con docente compartido

**Objetivo.** Coordinar HG con `allow_shared_teacher` sin duplicar reglas.

**Nota.** `SchedulingContext::isFree()` ya permite que un docente ocupe varias celdas si todas las lecciones autorizan. HG no debe introducir una segunda noción de "docente compartido"; debe reutilizar la existente.

**Criterio de aceptación.**
- Un medio-grupo con docente compartido y su par se agrupan igual.
- Un medio-grupo no compartido sigue vetado contra un grupo completo.

### HG-09 — Configurabilidad

**Objetivo.** Poder apagar/atenuar la prioridad sin desplegar.

**Diseño.** Añadir a `config/timetable.php`:

```php
'solver' => [
    ...
    'half_group_priority' => (bool) env('TIMETABLE_SOLVER_HALF_GROUP_PRIORITY', true),
    'half_group_bonus' => (int) env('TIMETABLE_SOLVER_HALF_GROUP_BONUS', 20),
],
```

y pasarlos al orquestador/solver. Si `half_group_priority=false`, el comportamiento vuelve a la línea base.

**Criterio de aceptación.**
- Con `false`, los tests de regresión reflejan el comportamiento previo.

### HG-10 — Reporte accionable de medio-grupos dispersos

**Objetivo.** Que coordinación entienda por qué un medio-grupo quedó aislado.

**Diseño.** En el readiness/preview, cuando `half_group_isolated > 0`, listar por sección los grupos afectados y la causa probable (sin celda con tope disponible, choque de docente, disponibilidad bloqueada). Reutilizar el patrón de `actionableConflicts` (`TimetableWizard.php:5821+`).

**Criterio de aceptación.**
- El reporte nombra la sección, el grupo y la acción sugerida.

### HG-11 — Pruebas

Extender/crear:

| # | Caso | Archivo | Aserción |
|---|---|---|---|
| 1 | Medio-grupos de la misma sección comparten período | `TimetableSolverTest` | `halfGroupLoad` beneficia la celda compartida |
| 2 | Prioridad ante escasez | `TimetableSolverTest` | El medio-grupo se asigna antes que el grupo completo |
| 3 | Tope respetado (2 y 3) | `SharedTeacherSolverTest`/nuevo | No se excede `max_subjects_per_period` |
| 4 | Métrica de agrupación | nuevo `HalfGroupMetricsTest` | 0 aislados en dataset agrupado |
| 5 | Flag off | `TimetableSolverOrchestratorTest` | Comportamiento línea base |
| 6 | `locked` + medio-grupo | `TimetableSolverTest` | Reserva preservada |
| 7 | Interacción shared-teacher | `SharedTeacherSolverTest` | Sin doble regla |
| 8 | Propiedad: no doble-booking | property test | Docente/sección/aula ≤ reglas |

**Comando.** `php8.2 artisan config:clear && php8.2 artisan test --filter=Timetable`

### HG-12 — Documentación

- Actualizar `SPEC-TIMETABLE-001-v2.md` (§ solver) y `ADR-SOLVER-V2-DECISIONS.md` con un `ADR-TT-012-XX` para medio-grupos prioritarios.
- Documentar en `SPEC-TIMETABLE-MEJORAS-001.md` (IMP-TT-014) el comportamiento de agrupación.

## 5. Diseño técnico resumido

```
GenerateTimetableJob
  └─ arma LessonToSchedule[] (ya trae isHalfGroup, seccionId)
       ↓
TimetableSolverOrchestrator
  ├─ HG-03: cadena de intentos incluye ORDER_HALF_GROUP_FIRST
  ├─ HG-04: pareo blando por sección al ordenar (pre-backtracking)
  └─ HG-05: métricas de agrupación en SolverOutcome
       ↓
TimetableSolver
  ├─ HG-01: constraintDegree() bonifica isHalfGroup
  ├─ HG-02: comboScore() bonifica celdas con halfGroupLoad > 0
  └─ SchedulingContext (sin cambios de contrato)
       ↓
persist() (sin cambios) → slots con is_half_group
```

**Invariantes preservadas:** INV-TT-006 (reglas duras), INV-TT-007/014 (locked), INV-TT-011 (tope), INV-TT-012 (medio-grupos solo entre sí).

## 6. Fases de implementación

| Fase | Entregable | Requisitos | Verificación |
|---|---|---|---|
| F1 | Ponderación de medio-grupo en orden de bloques y combos | HG-01, HG-02 | Tests 1, 2, 8 |
| F2 | Estrategia `half_group_first` en el orquestador | HG-03 | Test 2, 5 |
| F3 | Pareo blando por sección | HG-04 | Tests 1, 3 |
| F4 | Métricas y reporte en preview/readiness | HG-05, HG-10 | Tests 4 |
| F5 | Configurabilidad y documentación | HG-09, HG-12 | Test 5 |
| F6 | Cobertura de interacciones (locked/shared/tope) | HG-06, HG-07, HG-08 | Tests 3, 6, 7 |

Cada fase solo se cierra con sus criterios de aceptación y evidencia de test.

## 7. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| La prioridad dispersa otras lecciones y baja cobertura global | El orquestador hace keep-best por bloques asignados; la prioridad solo desempata |
| El pareo rígido vuelve infactible un dataset | HG-04 es blando: la segunda mitad puede ir a otro período |
| El bonus de agrupación compite con "día distinto" | Escalar el bonus por debajo de +100 (HG-02) |
| Alteración de determinismo de restarts | El bonus es determinista y depende solo del estado del `SchedulingContext` |
| Divergencia con `ConflictValidator` | Reutilizar `max_subjects_per_period` y la misma semántica de celda |
| Uso de `pestudios.max_subjects_per_period` inexistente | D1: usar el del calendario; migrar pestudios solo si se aprueba |

## 8. Preguntas abiertas

1. **D1** — ¿Confirmas que el tope es `timetable_calendars.max_subjects_per_period`? ¿Se desea además un override a nivel `pestudios` (requeriría migración + herencia calendario→pestudio)?
2. **D4/P1** — ¿"Agrupar" debe ser **blando** (preferencia) o **duro** (obligar a que las mitades compartan período cuando exista combinación)? La opción dura simplifica la expectativa de negocio pero puede reducir cobertura.
3. **P2** — ¿La prioridad de medio-grupos debe poder configurarse **por calendario** (columna) o basta la variable de entorno global?
4. **P3** — ¿El pareo debe considerar `grupo_estable_id` (que las dos mitades sean del mismo componente de formación) o cualquier medio-grupo de la sección?

## 9. Registro de cambios

| Fecha | Versión | Cambio |
|---|---|---|
| 2026-09-14 | 1.0 | Plan inicial de prioridad y agrupación de medio-grupos |

## 10. Registro de implementación

Decisión confirmada: **D1** = usar `timetable_calendars.max_subjects_per_period` (no migrar `pestudios`); **D4** = agrupación **blanda**.

| ID | Estado | Archivo / cambio | Prueba |
|---|---|---|---|
| HG-01 | Implementado | `LessonToSchedule::constraintDegree(bool $halfGroupPriority)` (+6 si half-group) | `HalfGroupSolverTest::test_half_group_is_scheduled_before_whole_group_under_scarcity` |
| HG-02 | Implementado | `TimetableSolver::comboScore($combo, $ctx, $lesson)` bonifica celdas con `halfGroupLoad > 0` | `HalfGroupSolverTest::test_grouped_cell_is_preferred_over_empty_cell` |
| HG-03 | Implementado | `SolverAttemptConfig::ORDER_HALF_GROUP_FIRST`; intento `S1h` en el orquestador; `TimetableSolver::halfGroupOrder()` | `HalfGroupSolverTest::test_half_groups_of_same_section_share_a_period` |
| HG-04 | Implementado | `TimetableSolver::clusterHalfGroupsBySection()` (pareo blando estable) | idem HG-03 |
| HG-05 | Implementado | `SolverOutcome::halfGroupMetrics()`; métricas en `preview_payload` y logs del job | `HalfGroupSolverTest::test_orchestrator_metrics_report_grouped_half_groups` |
| HG-06 | Sin cambio | Tope ya aplicado por `SchedulingContext::isFree()` | `HalfGroupSolverTest::test_max_subjects_per_period_is_respected` |
| HG-07 | Sin regresión | Reservas `locked`/`preassigned` previas a la prioridad | Suite `TimetableSolverTest` verde |
| HG-08 | Sin cambio | Reutiliza la semántica existente de docente compartido | `SharedTeacherSolverTest` verde |
| HG-09 | Implementado | `config/timetable.php`: `half_group_priority`, `half_group_bonus` | `HalfGroupSolverTest::test_priority_can_be_disabled` |
| HG-10 | Implementado | `TimetablePublicationReadinessService::isolatedHalfGroups()` (warning no bloqueante) | cubierto por readiness/preview |
| HG-11 | Implementado | `tests/Unit/Timetable/HalfGroupSolverTest.php` (6 casos) | 6 passed (14 assertions) |
| HG-12 | Implementado | ADR-TT-012-08 en `ADR-SOLVER-V2-DECISIONS.md`; §6.2/§6.4 de `SPEC-TIMETABLE-001-v2.md` | revisión documental |
| UI | Implementado | Bloque "Medio-grupos" en Step 5 (`timetable-wizard.blade.php`) leyendo `preview.half_group_metrics` | `GenerateTimetableJobTest` valida la métrica en el payload |

### Evidencia de la suite

- `php8.2 artisan test --filter="Solver|GenerateTimetableJob|Publication|Models|Backup"` → **77 passed (259 assertions)**.
- `HalfGroupSolverTest` → **6 passed (14 assertions)**.
- Verificación final `GenerateTimetableJobTest|HalfGroupSolverTest|SolverTest|SolverOrchestratorTest|SharedTeacherSolverTest` → **55 passed (219 assertions)**.
- Los 6 fallos de `TimetableWizardTest` (rooms/import Excel) son **preexistentes** y ajenos a este cambio (verificado con `git stash`).

### Pendientes

- Añadir el pareo también por `grupo_estable_id` si se aprueba P3.
- (Opcional) Endpoint/panel de métricas agregadas de agrupación por calendario.

