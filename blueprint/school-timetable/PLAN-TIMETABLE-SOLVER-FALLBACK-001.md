# PLAN-TIMETABLE-SOLVER-FALLBACK-001

## Estrategia con fallback y recursividad controlada para `Generate draft`

| | |
|---|---|
| **Estado** | Plan revisado con evidencia de datos (F0 ejecutado) — pendiente de aprobación |
| **Área** | Timetable / Step 5 / Solver / `GenerateTimetableJob` |
| **Disparador** | Botón **Generar draft** (`wire:click="runDryRun"`) en `/app/planning/timetable` y `/app/coordinacion/timetable` |
| **Problema** | Resultados con muchos slots vacíos (lecciones sin asignar) — no útiles |
| **Contrato base** | `SPEC-TIMETABLE-001-v2.md` v2.2; ADR-TT-003/007/009/011 |
| **No incluye** | Cambiar el modelo de datos, reemplazar el solver por uno externo, IA en la generación |
| **⚠️ Hallazgo clave (F0)** | La mayor parte de los huecos es **infactibilidad real de capacidad** (C-1), no heurística: el fallback/orquestador **no puede** resolverla. Ver §0. |

---

## 0. Evidencia medida (auditoría de capacidad, BD `s2627`)

Medición read-only sobre los calendarios reales (2025-2026). La estructura del
día de **turno 1** es 4 bloques de 80 min (07:00–13:05) ⇒ **20 períodos/semana**;
turno 2 ⇒ **5 períodos/semana**. Un bloque `is_break` (recreo) no es asignable.

Capacidad efectiva:
- Lección de **grupo completo**: ocupa la celda entera ⇒ capacidad **= períodos**.
- Lección de **medio grupo**: comparte celda con otra ⇒ cuenta **≈ 0.5**.
- **Docente**: no puede estar en dos celdas a la vez ⇒ capacidad **= períodos** que cubre.

| Calendario | Overflow de secciones (bloques imposibles) | Secciones excedidas | Overflow de docentes |
|---|---:|---:|---:|
| 1 · Media Gral. Ciencia y Tecnología | **92** | 8/8 | 32 (3 docentes) |
| 2 · Media General | **58** | 2/6 | 8 (2 docentes) |
| 3 · Educación Primaria | 0 | 0/12 | 0 |
| 4 · Educación Inicial | **108** | 3/3 | 24 (6 docentes) |

Ejemplos concretos (bloques requeridos vs. 20 períodos):
- Sección 48 (cal 1) = **45**; secciones 81/57/58 (cal 4) = **56**; sección 19 (cal 2) = **50**.
- Docente 132 (cal 1) = **46 bloques en 5 secciones** en 20 períodos.

`totalizacionRecurrencia.md`: 1er año = **31 bloques**, 2do = 29, 3ro = 30 — todos
por encima de 20. Sólo Primaria (≈18.6/semana) cabe.

**Conclusión de F0:** ≈ **258 bloques** (secciones) son **imposibles de agendar**
con los datos actuales; el solver los marca no asignados *por diseño*. Ninguna
cantidad de restarts, topes o reparación los ubicará. Antes de invertir en el
orquestador hay que **detectar y reportar** esta infactibilidad y reconciliar los
datos (horas `weekly_blocks` vs. capacidad del calendario, o períodos/turnos).

> Nota: F0 también confirma que el problema **no es el tiempo** (elapsed ≈ 0.0s,
> 0 timeouts): en infactibilidad el backtrack retorna de inmediato. Los
> presupuestos/restarts sólo aplican al **residual factible**.

Créditos: consultas de sólo lectura (`SELECT`) — sin cambios ni borrados de BD.

---

## 1. Objetivo

Maximizar la **cobertura de asignación** del draft (minimizar lecciones y bloques
sin asignar) mediante:

1. Una **cadena de estrategias con fallback**: si una configuración del solver
   deja huecos, se prueban alternativas y se conserva la mejor.
2. **Recursividad controlada**: reinicios (restarts) con orden semilla y
   profundización de topes, **acotada por presupuesto** (no exponencial sin fin).
3. Una **fase de reparación** que reubica lecciones que bloquean a otras antes de
   declararlas no asignadas.
4. **Distinguir infactibilidad real** (imposible por capacidad) de **no
   encontrado** (heurística/tope), informando la causa al usuario.

Regla dura intacta: nunca violar docente/sección/aula/día, disponibilidad,
`shift_mismatch`, recreos, locked ni `max_subjects_per_period`.

---

## 2. Flujo actual (verificado)

```
[Generar draft]  wire:click=runDryRun
      │
      ▼
TimetableWizard::runDryRun()
      │  GenerateTimetableJob::dispatchSync(calendarId, dryRun: true)
      ▼
GenerateTimetableJob::handle()
      ├─ strategy=legacy  → legacyAssignment()  (reproduce slots importados)
      └─ strategy=optimized → runSolver()
              ├─ construye DTOs (scoped / preserved / preassigned / locked)
              ├─ buildAvailablePeriods()  (turno preferido + fallback de turno)
              └─ new TimetableSolver(dto, …, timeLimit=30, maxSubjects)
                       └─ solve():  reserva locked/presasignados
                                    backtrack(orden = constraintDegree desc)
                                    rememberBest()  (mejor parcial)
      ▼
SolverResult{assignment, unassigned, timedOut, elapsed}
      ├─ dry-run → preview_payload (no persiste)
      └─ publish → slots
```

Evidencia (`storage/logs/timetable-2026-09-12.log`, entorno testing):
**23 corridas con `unassigned:1`, 0 con `timed_out:true`, elapsed ≈ 0.0s.**
Es decir, los huecos **no** vienen del límite de tiempo: hay causas
estructurales y de heurística/topes.

---

## 3. Diagnóstico — por qué quedan slots vacíos

| # | Causa | Evidencia en código | ¿Soluble por el solver? |
|---|---|---|---|
| C-1 | **`blocksNeeded > períodos asignables`** de la lección (horas normalizadas exceden la capacidad del calendario/turno) | `TimetableSolver::pickCombinations()` devuelve `[]` si `count(pool) < n`; el backtrack la marca no asignada | ❌ No (infactibilidad real). Requiere pre-check + reporte/ajuste |
| C-2 | **Tope de combinaciones/pool** descarta la combinación factible (queda fuera del top-1000 por score soft, o el pool se recorta a 14/26) | `MAX_COMBOS_PER_LESSON=1000`, `MAX_CANDIDATE_POOL=14`, `MAX_CANDIDATE_POOL_ABS=26`, `combine()` corta por nodos | ✅ Sí (reordenar por factibilidad / ampliar en riesgo) |
| C-3 | **Orden fijo de lecciones** (`constraintDegree` desc) sin reinicios: una decisión temprana mala condena a las posteriores | `usort($free, …)` + único `backtrack` | ✅ Sí (restarts con otras heurísticas) |
| C-4 | **Sin fase de reparación**: si una lección no cabe, no se reubica a las que ocupan sus períodos | `backtrack` solo explora hacia adelante; no reubica asignadas previas fuera del DFS | ✅ Sí (repair / LDS) |
| C-5 | **Disponibilidad restrictiva** deja pocos períodos al docente | `buildAvailablePeriods()` + `TimetableAvailabilityService` | ⚠️ Parcial (reportar) |
| C-6 | **Preservado parcial** (`existingPeriodCount < requiredBlocks`) no reserva la lección y compite por hueco | `runSolver()` (bloque `preassignedSlots`) | ✅ Sí (repair) |
| C-7 | **`max_subjects_per_period`/medio grupo** limitan celdas compartidas | `SchedulingContext::isFree()` | ❌ No (regla dura) |

**Conclusión:** el mayor volumen de huecos viene de **C-1 (infactibilidad real
por capacidad)**, medida en F0 (§0): ≈258 bloques imposibles en secciones y 64 en
docentes, concentrados en cal 1/2/4. **El orquestador con fallback NO resuelve
C-1**: sólo mejora el **residual factible** (C-2/C-3/C-4/C-6), visible sobre todo
en cal 3 (Primaria) y en el slack de cal 1/2. Por tanto el orden de valor es:

1. **Pre-check de capacidad + reporte accionable** (C-1) — evita prometer lo imposible.
2. **Reconciliación de datos** (horas `weekly_blocks` vs. capacidad) — decisión de negocio/datos.
3. **Orquestador con fallback** (C-2/C-3/C-4/C-6) — sólo sobre lo factible.

---

## 4. Arquitectura propuesta

Introducir un **orquestador de solver** que ejecuta una **cadena de intentos**
sobre el mismo problema y conserva la mejor solución:

```
GenerateTimetableJob::runSolver()
      │
      ▼
App\Services\Timetable\Solver\TimetableSolverOrchestrator
      ├─ feasibilityPrecheck(dto, context)   → reporta C-1 (infactible real)
      ├─ for each attempt in strategyChain:
      │      result = (new TimetableSolver(dto, …, attempt.config)).solve()
      │      keep best by (coverage desc, softScore desc)
      │      earlyStop if coverage == 100% and score >= threshold
      ├─ repairPhase(best, dto, context)      → reubica bloqueantes (C-4/C-6)
      └─ SolverOutcome{result, attemptsUsed, unassignedReasons}
```

DTOs nuevos:
- `SolverAttemptConfig` (orden de lecciones, topes de combinación, `randomSeed`, `softScoring`, `useExclusions`).
- `AttemptResult` (coverage, softScore, assignment, unassigned, elapsed).
- `SolverOutcome` (mejor `AttemptResult` + `attempts[]` + `unassignedReasons`).
- `FeasibilityReport` (por lección: `blocksNeeded`, `assignablePeriods`, `reason`).

`TimetableSolver` se mantiene como motor puro (sin UI); el orquestador lo invoca
varias veces. Se conserva `SolverResult` para compatibilidad.

---

## 5. Cadena de estrategias (fallback)

Orden de intentos; se detiene al alcanzar cobertura completa con score aceptable
o al agotar presupuesto global.

| Intento | Estrategia | Para qué |
|---|---|---|
| **S1** | `constraintDegree` desc (actual) | Línea base; casos fáciles |
| **S2** | `scarcityFirst` (docente con menos períodos disponibles primero) | Liberar cuellos de botella temprano |
| **S3** | `blocksDesc` (más bloques primero) | Lecciones "grandes" difíciles de ubicar tarde |
| **S4** | `randomizedRestarts` (N reinicios, `randomSeed` por intento) | Salir de mínimos locales; **recursividad controlada** |
| **S5** | `feasibilityFirst` (combinaciones ordenadas por **factibilidad** —menos constricción— en vez de por soft) | Evitar C-2 (combo factible fuera del top-1000) |
| **S6** | `expandedCaps` (pool/topes ampliados en lecciones "en riesgo") | Lecciones >14 bloques o pool recortado |
| **S7** | `repair` (solo si quedan no asignadas tras S1..S6) | Reubicar bloqueantes (C-4/C-6) |

Reglas:
- Cada intento parte de locked/presasignados reservados igual que hoy.
- **Criterio de "mejor":** `coverage` (bloques asignados) desc; desempate por
  `softScore` (días repartidos, sin huecos, teóricos tempranos).
- **Nunca** se acepta un intento que viole una regla dura.
- Reproducibilidad: `randomSeed` fijo por corrida (configurable), registrado en
  logs para repetir un intento.

### 5.1 Recursividad controlada
- **Presupuesto global** `TIMETABLE_SOLVER_BUDGET_MS` (p. ej. 30–60s) repartido
  entre intentos; cada intento con su propio deadline para no consumir todo.
- **Reinicios acotados**: `TIMETABLE_SOLVER_RESTARTS` (p. ej. 8) en S4.
- **Profundización de topes** (iterative deepening): si un intento queda corto
  por tope de combinaciones, se repite con topes mayores hasta un máximo
  (`TIMETABLE_SOLVER_MAX_COMBOS`), solo para las lecciones no asignadas.
- **Corte por nodos/tiempo** conserva el mejor parcial (`rememberBest`), ahora a
  nivel de orquestador.

---

## 6. Fase de reparación (S7)

Para cada lección no asignada:
1. Identificar los períodos de su dominio que están ocupados por otras lecciones
   (mismo docente/sección/aula, o cupo `max_subjects_per_period`).
2. Intentar **reubicar** esas lecciones bloqueantes a otros períodos libres de su
   dominio (movimiento puntual validado por `ConflictValidator`).
3. Si la reubicación libera el período, asignar la lección pendiente.
4. Limitar a `TIMETABLE_SOLVER_REPAIR_MOVES` (p. ej. 200) movimientos; registrar
   los no reparables.

Si la lección es **infactible por capacidad** (C-1), no se intenta reparar: se
reporta con su causa.

---

## 7. Pre-check de factibilidad y reporte

Antes de buscar, calcular por lección:
`blocksNeeded` vs `assignablePeriods` (períodos libres de su turno, con
disponibilidad, descontando `is_break`).

- Si `assignablePeriods < blocksNeeded` → **infactible real (C-1)**: la lección
  se reporta con `reason = capacity_exceeded` y, opcionalmente, un aviso en UI
  sugiriendo ajustar `weekly_blocks` (normalización de horas) o ampliar períodos.
- Si `0 < assignablePeriods` y aun así queda sin asignar → `reason =
  not_found` (restarts/topes/reparación insuficientes) → se muestra como
  incidencia resoluble a mano.

El `unassigned` del `SolverOutcome` lleva `lesson_id + reason`, reutilizado por
`publishChecklist`/`generationConflictGroups` para mensajes accionables.

---

## 8. Cambios de código previstos

| Archivo | Cambio |
|---|---|
| `app/Services/Timetable/Solver/TimetableSolverOrchestrator.php` | **Nuevo**: cadena de intentos, presupuesto, keep-best, repair |
| `app/Services/Timetable/Solver/SolverAttemptConfig.php` | **Nuevo** DTO de configuración de intento |
| `app/Services/Timetable/Solver/AttemptResult.php` | **Nuevo** resultado por intento (coverage/score) |
| `app/Services/Timetable/Solver/SolverOutcome.php` | **Nuevo** resultado consolidado + razones |
| `app/Services/Timetable/Solver/FeasibilityReport.php` | **Nuevo** reporte C-1 |
| `app/Services/Timetable/Solver/TimetableSolver.php` | Aceptar `SolverAttemptConfig` (orden, topes, seed, factibilidad-primero); preservar comportamiento por defecto |
| `app/Jobs/Timetable/GenerateTimetableJob.php` | Usar el orquestador en vez de invocar el solver directo; log de intentos/cobertura/razones |
| `config/timetable.php` | Presupuestos: `solver_budget_ms`, `solver_restarts`, `solver_max_combos`, `solver_repair_moves`, `solver_seed` |
| `.env.example` | Variables del bloque solver |
| `TimetableWizard.php` / vista Step 5 | Mostrar cobertura y motivos (`capacity_exceeded` vs `not_found`) |

Sin migraciones. Sin cambios de esquema.

---

## 9. Observabilidad

En el canal `timetable`, por corrida:
```json
{
  "event": "timetable_solver",
  "correlation_id": "...",
  "calendar_id": 3,
  "strategy": "optimized",
  "attempts": [
    {"id": "S1", "coverage": 92, "unassigned": 6, "score": 410, "elapsed_ms": 210},
    {"id": "S4r3", "coverage": 99, "unassigned": 1, "score": 470, "elapsed_ms": 1800}
  ],
  "chosen": "S4r3",
  "coverage_pct": 99.0,
  "unassigned_reasons": {"capacity_exceeded": 1},
  "timed_out": false
}
```

---

## 10. Testing

**Unit (capacidad — F1a, primero):**
- Sección con `Σ blocks > períodos` → `capacity_exceeded` con overflow exacto.
- Docente con `Σ blocks > períodos` → `capacity_exceeded` (independiente de sección).
- Sección/docente en el límite (`= períodos`) → factible, sin falso positivo.
- Media grupo cuenta 0.5; grupo completo 1; `is_break` no cuenta.

**Unit (solver/orquestador, sin Eloquent):**
- Un intento deja 3 sin asignar; S4/repair sube la cobertura → se elige el mejor.
- Dataset **infactible** (bloques > períodos) → `capacity_exceeded`, sin bucle.
- Timeout global → conserva el mejor parcial (no lo descarta).
- Reproducibilidad por `randomSeed` (mismo resultado con misma semilla).
- Ningún intento viola regla dura (docente/sección/aula/turno/recreo/locked).
- Repair reubica una lección bloqueante y libera la pendiente (C-4/C-6).
- `feasibilityFirst` recupera un caso que el orden por soft dejaba fuera (C-2).
- `max_subjects_per_period`/medio grupo respetados en todos los intentos.

**Feature:** `GenerateTimetableJob` dry-run reporta `SolverOutcome` con motivos;
`TimetableWizardTest` cubre que la UI muestra cobertura, `capacity_exceeded` y
`not_found` por separado.

**Auditoría de datos:** `timetable:audit-capacity --dry-run` reproduce los
números de §0 sobre `s2627` (cal 1=92, cal 2=58, cal 3=0, cal 4=108).

**Regresión:** `php8.2 artisan test --filter='Timetable|Solver'` y Pint; sin
comandos destructivos.

---

## 11. Fases y tickets

| Fase | Entregable | Tickets | Verificación | Estado |
|---|---|---|---|---|
| **F0** | Auditoría de capacidad (evidencia §0): overflow por sección/docente | FB-01 | Números medidos en BD `s2627` | ✅ |
| **F1a** | Pre-check de capacidad + reporte `capacity_exceeded` (sección y docente) y mensaje accionable en Step 5 | FB-16..18 | Unit + Feature: reporta imposibles sin intentar agendar | ✅ |
| **F1b** | Comando `timetable:audit-capacity` que lista secciones/docentes excedidos y el overflow | FB-19 | Salida determinista sobre `s2627` | ✅ (la reconciliación de datos es decisión de negocio) |
| **F2** | Orquestador + DTOs + keep-best + presupuesto (sólo residual factible) | FB-02..05 | Unit: un intento vs varios | ✅ |
| **F3** | Estrategias S1/S2/S3/S4 (restarts con seed) | FB-06..08 | Unit: cobertura no baja; reproducibilidad | ✅ |
| **F4** | S5/S6 (factibilidad-primero, topes expandidos) | FB-09..10 | Unit: C-2 resuelto | ⏳ pendiente |
| **F5** | S7 repair + razones `not_found` | FB-11..12 | Unit: repair + razones | ⏳ pendiente (razones `not_found` ya expuestas) |
| **F6** | Integración job + UI (cobertura y motivos) + logs | FB-13..14 | Feature + smoke | ✅ (job + Step 5 + logs) |
| **F7** | Rollout y métricas | FB-15 | Comparar cobertura antes/después (esperado: cal 3 y slack) | ⏳ pendiente |

IDs de commit: `feat(timetable): solver orchestrator with fallback [TT-FB-xx]`.

---

## 12. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Explosión de tiempo por N intentos | Presupuesto global + deadline por intento + early-stop |
| Resultado no reproducible | `randomSeed` fijo por corrida, registrado |
| Degradar calidad soft a cambio de cobertura | Keep-best por (coverage, score); el score solo desempata cobertura igual |
| Repair mover demasiado | Tope de movimientos + validación por `ConflictValidator` |
| Enmascarar infactibilidad real | Pre-check C-1 con `capacity_exceeded` explícito |
| Regresión en locked/legacy/preasignados | Reservar locked igual que hoy; tests dedicados |

---

## 13. Definición de terminado

- **El sistema reporta `capacity_exceeded`** (sección y docente) con el overflow
  exacto y un mensaje accionable, antes de mostrar "slots vacíos" como un fallo
  del solver. La UI distingue claramente **imposible** de **no encontrado**.
- Existe `timetable:audit-capacity --dry-run` que reproduce §0 y guía la
  reconciliación de datos (horas/períodos).
- El orquestador conserva siempre la **mejor** solución por cobertura y no
  empeora la línea base actual en el **residual factible** (no promete resolver C-1).
- Restarts reproducibles por semilla; presupuesto respetado (sin timeouts no
  controlados).
- `capacity_exceeded` se reporta distinto de `not_found`.
- La UI del Step 5 muestra cobertura y motivos; el editor permite resolver a mano.
- Solver sigue siendo PHP puro y testeable sin Eloquent; reglas duras intactas.
- Tests unitarios/feature verdes; Pint limpio; documentado en `docs/timetable/`.

---

## 14. Fuera de alcance

- Optimización multiobjetivo avanzada (varias métricas ponderadas configurables).
- Paralelización/hilos dentro del solver.
- Ajuste automático de `weekly_blocks` (se **reporta** la infactibilidad C-1; el
  ajuste de horas es responsabilidad del comando de normalización).
- IA generando el horario (el IA-draft es otra iniciativa).

---

## 15. Anexo — mapeo de causas a tickets

| Causa | Ticket |
|---|---|
| C-1 capacity_exceeded (sección/docente) | FB-16/FB-17/FB-18 (reporte) · FB-19 (auditoría datos) |
| C-2 topes/combo cap | FB-09 |
| C-3 orden fijo | FB-06/FB-08 |
| C-4 sin repair | FB-11 |
| C-6 preservado parcial | FB-11 |
| C-5 disponibilidad | FB-17 (reporte) |
| C-7 reglas duras | Intactas (tests de no-regresión) |
