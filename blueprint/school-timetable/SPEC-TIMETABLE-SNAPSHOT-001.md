# SPEC-TIMETABLE-SNAPSHOT-001 — Snapshot restaurable del calendario

| | |
|---|---|
| **Estado** | Propuesta normativa para implementación |
| **Versión** | 2.2 |
| **Autor** | Staff Engineer |
| **Fecha** | 2026-09-14 |
| **Documento base** | `SPEC-TIMETABLE-001-v2.md` |
| **Alcance** | Barra superior del wizard (export/restore), contrato de snapshot, integridad, concurrencia, persistencia, UI y pruebas |

> **Cambios respecto a v1.0 del spec.** Se corrigen inexactitudes contra el esquema real (disponibilidad ya no
> usa `period_id`; `timetable_slots` tiene columnas `is_practical`, `is_half_group`, `allow_shared_teacher` y
> claves generadas), se sustituye el bump riesgoso v1→v2 del mismo `format` por un **formato nuevo**, se precisa
> el algoritmo de checksum, y se añaden control de concurrencia, gate por estado del calendario y tratamiento
> de colisiones de unicidad. Ver §19 (changelog) y §18 (preguntas resueltas).
>
> **v2.1** añade el bloque `playbook` (§5.7): el snapshot embebe el **flujo y las reglas del solver**, en
> formato consumible por un LLM, para que un agente pueda analizar y optimizar el horario sin re-derivar las
> reglas desde el código.
>
> **v2.2** alinea el `playbook` con `mejoras/PLAN-TIMETABLE-HALFGROUP-001.md`, ya implementado
> (ADR-TT-012-08): prioridad y agrupación de medio-grupos, términos soft S-4/S-5, orden `half_group_first`
> (O-6), clustering por sección, flags `half_group_priority` / `half_group_bonus`, warning
> `half_group_isolated` y métricas derivadas.

## 1. Objetivo

Convertir el respaldo de la barra superior del wizard de horarios (Coordinación, `TimetableWizard`) en un
**snapshot completo y restaurable** del calendario, y dotar al restore de **garantías de seguridad y
reversibilidad**: preview previo, verificación de integridad, auto-backup y aplicación atómica.

Hoy el botón "Backup JSON" descarga únicamente la *demanda* del solver. Restaurarlo no devuelve un horario:
devuelve los insumos para volver a generarlo.

## 2. Situación actual (punto de partida)

Método: `TimetableWizard::downloadCalendarLessonsBackup()`
(`app/Livewire/Coordinacion/Timetable/TimetableWizard.php:3105`).

Exporta filas de `timetable_lessons` construidas por `calendarLessonsRows()`
(`TimetableWizard.php:2957`): por cada lección, `pevaluacion_id`, `academic_identity`, `labels` y
`configuration` (bloques teóricos/prácticos, turno, tipo de aula, medio-grupo, docente compartido,
prioridad, bloqueo).

**No** incluye:

| Falta | Tabla | Consecuencia |
|---|---|---|
| Horario generado | `timetable_slots` | Restaurar no devuelve el horario; hay que re-correr el solver |
| Grid de bloques | `timetable_periods` | Los slots no tienen referencia de periodo |
| Disponibilidad docente | `timetable_teacher_availability` | El Paso 4 se pierde |
| Bloqueo de sección | `seccions.timetable_locked` | El candado por sección se pierde |

Además, `TimetableLessonPersistenceService::persist()` (`app/Services/Timetable/TimetableLessonPersistenceService.php:20`)
usa `updateOrCreate` y **nunca borra**: el restore actual es puramente aditivo. No existe forma de volver a un
estado anterior.

## 3. No-objetivos

- **Portabilidad plena entre entornos o años escolares.** No se implementa un motor de resolución por nombres
  normalizados; solo se usan claves naturales donde la estructura lo exige (§7).
- **Sustituir el versionado del dominio** (`timetable_calendar_versions` + `timetable_change_logs`). Este
  spec es de respaldo/restauración, no de historial de publicación.
- **Modificar** el respaldo por sección (`downloadLessonsBackup`, `TimetableWizard.php:2827`) ni el de todos
  los calendarios (`downloadAllCalendarsBackup`, `TimetableWizard.php:3170`).

## 4. Decisiones de diseño

| # | Pregunta | Decisión | Motivo |
|---|---|---|---|
| D1 | Alcance del backup | Calendario completo: config + slots + disponibilidad + bloqueos + periods | Reproducir el horario sin re-correr el solver |
| D2 | Restore sobre calendario con horario | Reemplazo con red de seguridad | Snapshot fiel; el auto-backup lo hace reversible |
| D3 | Control previo | Preview (dry-run) + confirmación explícita | El usuario ve el impacto antes de una acción destructiva |
| D4 | **Formato** | **Nuevo** `cfla-timetable-calendar-snapshot`, `version: 1` | Evita que un mismo `format` signifique "aditivo" (v1) y "destructivo" (v2) |
| D5 | Concurrencia | Preview guarda `calendar.version` + hash; apply revalida | Un preview viejo no debe sobrescribir cambios ajenos |
| D6 | Calendario publicado | Advertencia reforzada si `status = 'active'` | Reemplazar un horario publicado es sensible |

### 4.1 Formato nuevo, no bump de `cfla-timetable-lessons-backup`

El spec v1.0 proponía conservar `format: "cfla-timetable-lessons-backup"` y subir `version` a 2. **Se
descarta.** Un mismo `format` con dos semánticas opuestas (aditivo vs. destructivo) es una trampa: cualquier
consumidor que no lea `version` borraría slots. En su lugar:

| Formato | Origen | Semántica |
|---|---|---|
| `cfla-timetable-lessons-backup` | respaldo por sección (legacy) y de calendario completo | **Aditiva** (vigente, sin cambios) |
| `cfla-timetable-calendars-backup` | respaldo de todos los calendarios | **Aditiva** (vigente, sin cambios) |
| `cfla-timetable-section-slots-backup` | respaldo de slots por sección | Reemplazo de slots de una sección (vigente) |
| **`cfla-timetable-calendar-snapshot`** | **este spec** | **Reemplazo** con red de seguridad |

El botón de restore acepta tanto el **snapshot v1** (reemplazo) como el **legacy `cfla-timetable-lessons-backup`**
(aditivo, §10.4), de modo que los respaldos ya descargados siguen sirviendo.

## 5. Contrato del snapshot

### 5.1 Estructura

```jsonc
{
  "format": "cfla-timetable-calendar-snapshot",
  "version": 1,
  "scope": "calendar",
  "exported_at": "2026-09-14T10:00:00-04:00",
  "exported_by": 3,
  "app_version": "…",
  "environment": "production",
  "checksum": "sha256:9f2c…",
  "checksum_algo": "sha256-semantic-v1",
  "schema": { /* documentación autocontenida de este formato */ },
  "calendar": { /* idéntico al respaldo de calendario vigente */ },

  "periods": [
    { "shift_code": "M", "day_of_week": 1, "order_in_day": 3,
      "start_time": "09:00", "end_time": "09:45", "is_break": false }
  ],

  "section_locks": [
    { "seccion_id": 42, "seccion": "A", "grado": "1er Año", "pestudio": "Educación Media", "locked": true }
  ],

  "availability": [
    { "profesor_id": 7, "profesor": "López, Ana", "shift_code": "M",
      "day_of_week": 1, "order_in_day": 3, "start_time": "09:00", "end_time": "09:45",
      "is_available": true }
  ],

  "lessons": [ /* mismo formato de lección del respaldo vigente */ ],

  "slots": [
    { "pevaluacion_id": 501,
      "period": { "shift_code": "M", "day_of_week": 1, "order_in_day": 3 },
      "room_id": 12, "profesor_id": 7, "seccion_id": 42, "grupo_estable_id": null,
      "is_manual_override": false, "locked": false,
      "is_practical": false,
      "is_half_group": false, "allow_shared_teacher": false }
  ],

  "playbook": { /* §5.7 — flujo y reglas del solver, para análisis/optimización por un LLM */ }
}
```

### 5.2 Campos informativos vs. vinculantes

- **Vinculantes** (se usan para emparejar en el restore): `pevaluacion_id` + `academic_identity`,
  `shift_code`, `day_of_week`, `order_in_day`, `room_id`, `profesor_id`, `seccion_id`, `grupo_estable_id`.
- **Informativos** (nunca emparejan): `labels`, `profesor` (nombre), `start_time`/`end_time`, `is_break`,
  `grado`, `pestudio`, `exported_at`, `exported_by`, `app_version`, `environment`.
- `calendar.id`: informativo. El calendario destino se resuelve por `lapso_id` + `pestudio_id`, no por `id`
  (los IDs no son portables entre entornos).

### 5.3 Campos de slot desnormalizados (precedencia)

`timetable_slots` **desnormaliza** `profesor_id`, `seccion_id` y `grupo_estable_id` (base
`2026_08_15_000001_create_timetable_tables.php:138-140`). En el restore estos valores **se derivan de la
lección (`pevaluacion`) resuelta**, no del snapshot. Los del snapshot se usan solo como validación: si no
coinciden, se registra advertencia y prevalece la lección. Esto evita drift silencioso.

### 5.4 `slots` ausente vs. `slots: []`

- **`slots` ausente** (o payload legacy): no se toca el horario (modo aditivo / solo configuración).
- **`slots: []`** (clave presente y vacía): el horario del calendario queda **vacío** tras el restore (las
  lessons se recrean, los slots caen por *cascade* y no se reinserta ninguno). Es intencional; el preview
  debe decirlo explícitamente (§9.2) y la confirmación reforzarse.

### 5.5 `is_practical`

`timetable_slots.is_practical` **no está en la migración base** (`2026_08_15_000001`); existe en el esquema
de producción y todo el código la trata con guardas `Schema::hasColumn` (p. ej.
`TimetableWizard.php:6215,6346`). El snapshot la incluye cuando la columna existe y el restore solo la
escribe cuando existe. Es el flag que distingue bloque teórico/práctico: omitirla pierde la ubicación de las
prácticas.

### 5.6 `is_half_group` y `allow_shared_teacher`

Ambos existen en `timetable_lessons` y `timetable_slots` (añadidos por `2026_09_09_000001` y
`2026_09_13_000002`) y **alteran los índices únicos** vía columnas generadas (§6, §8). El snapshot los
exporta y el restore los aplica; su efecto en la unicidad se valida en el preview (§9.2).

**Prioridad y agrupación de medio-grupos (ADR-TT-012-08, HG-01…HG-11).** Con
`timetable.solver.half_group_priority = true` (default), el solver explora los medio-grupos antes que los
grupos completos y prefiere las celdas que ya agrupan mitades de una misma sección, con una bonificación
soft `timetable.solver.half_group_bonus` (default 20, siempre < 100). Es una **preferencia, no una regla
dura**: si no hay celda compartida viable, la mitad se ubica en otro período. El contrato del snapshot **no
cambia** por esto (`is_half_group` ya se exportaba); lo que cambia es lo que el `playbook` (§5.7) declara.

### 5.7 Bloque `playbook` — flujo y reglas del solver para análisis por un LLM

El snapshot no es solo un respaldo: es también el **insumo de análisis**. Para que un agente pueda evaluar y
optimizar el horario sin acceso al código, el JSON embebe un bloque `playbook` con el flujo de la
funcionalidad y las reglas vigentes del solver, tomadas de sus métodos.

**Reglas de diseño del bloque:**

1. **Fuente única, nunca a mano.** Se genera desde `TimetableSolverPlaybook` (clase dedicada, §13), que es la
   única copia de las reglas. Prohibido redactarlo como prosa suelta que envejezca.
2. **Excluido del checksum** (§7). Es documentación, no dato semántico: si la redacción cambia en un parche,
   no debe invalidar los snapshots ya emitidos.
3. **Salida, nunca entrada.** El restore **no** lo lee ni aplica reglas desde él. Un archivo manipulado no
   puede alterar el comportamiento del sistema.
4. **Versionado propio.** `playbook_version` permite al consumidor saber qué revisión de reglas está leyendo.

```jsonc
"playbook": {
  "playbook_version": 1,
  "purpose": "Contexto operativo autosuficiente: permite analizar y optimizar este horario sin acceso al código.",
  "generated_from": {
    "spec": "SPEC-TIMETABLE-SNAPSHOT-001",
    "module": "app/Services/Timetable/Solver",
    "entry": "GenerateTimetableJob::runSolver()"
  },

  "flow": {
    "generation": [
      "Carga las lessons activas del calendario; descarta las que no resuelven pevaluación/sección/profesor.",
      "Reconstruye los bloqueos (locked) y detecta bloques preasignados (0 < existentes < requeridos).",
      "Construye el dominio de períodos por turno (excluye is_break; turno preferido primero, con fallback salvo si la lección está locked).",
      "Ordena las lecciones según el intento y, con half_group_priority activo, deja consecutivos los medio-grupos de una misma sección (HG-04).",
      "Encadena intentos con distinto orden y semilla (S1, S1h si half_group_priority, S2, S3, S4r0..S4r{n}), conservando la mejor solución (keep-best).",
      "Si queda residual, ejecuta la fase de reparación priorizando las lecciones sin asignar.",
      "Persiste los slots, o guarda un preview si es dry-run. En ambos casos calcula las métricas de agrupación (HG-05)."
    ],
    "export": [
      "Recolecta periods, section_locks, availability, lessons y slots del calendario seleccionado.",
      "Calcula el checksum semántico (§7) y serializa."
    ],
    "preview": ["Valida formato/versión/checksum.", "Calcula el diff contra la BD sin escribir (§9.2)."],
    "apply": ["Revalida concurrencia.", "Escribe el auto-backup.", "Reemplaza dentro de una transacción (§9.3)."]
  },

  "objective": {
    "kind": "lexicographic_keep_best",
    "primary": "bloques asignados (cobertura)",
    "secondary": "score soft: Σ comboScore por lección + bonus de agrupación de medio-grupos",
    "reference": "TimetableSolverOrchestrator::isBetter() / TimetableSolver::qualityScore()"
  },

  "hard_rules": [
    { "id": "D-1", "rule": "Un docente no puede tener dos clases en el mismo período.",
      "exception": "allow_shared_teacher: permitido solo si TODAS las lecciones ocupantes Y la candidata lo autorizan.",
      "method": "SchedulingContext::isFree()" },
    { "id": "D-2", "rule": "Un aula no puede estar ocupada dos veces en el mismo período.",
      "detail": "Solo aplica cuando room_id no es null (ADR-TT-008).",
      "method": "SchedulingContext::isFree()" },
    { "id": "D-3", "rule": "Una lección de sección completa bloquea la celda para toda la sección.",
      "method": "SchedulingContext::isFree()" },
    { "id": "D-4", "rule": "Una lección de sección completa exige la celda de sección totalmente vacía (sin subgrupos ni medio-grupos).",
      "method": "SchedulingContext::isFree()" },
    { "id": "D-5", "rule": "Una lección de subgrupo solo colisiona con su propio subgrupo.",
      "method": "SchedulingContext::isFree()" },
    { "id": "D-6", "rule": "Por celda se admiten hasta max_subjects_per_period medio-grupos.",
      "default": 2, "method": "SchedulingContext::isFree()" },
    { "id": "D-7", "rule": "El turno preferido es una preferencia con fallback en el solver, pero es duro en la edición manual.",
      "detail": "No hay fallback de turno cuando la lección está locked.",
      "method": "GenerateTimetableJob::buildAvailablePeriods() / ConflictValidator" },
    { "id": "D-8", "rule": "El docente debe estar disponible en el bloque.",
      "method": "TimetableAvailabilityService::isAvailable()" },
    { "id": "D-9", "rule": "Las lecciones locked se reservan primero y no se reasignan; requieren lock completo (nº de bloques asignados == bloques requeridos).",
      "method": "TimetableSolver::solve()" },
    { "id": "D-10", "rule": "Los períodos de receso (is_break) no admiten clases.",
      "method": "GenerateTimetableJob::buildAvailablePeriods()" },
    { "id": "D-11", "rule": "Los bloques preasignados deben ser válidos; si no, la lección queda sin asignar.",
      "method": "TimetableSolver::solve()" },
    { "id": "D-12", "rule": "Una lección no puede repetir el mismo período entre su bloque teórico y el práctico.",
      "method": "TimetableSolver::combinationsOfSize()" }
  ],

  "soft_rules": {
    "sign": "mayor score = mejor",
    "methods": {
      "per_lesson": "TimetableSolver::comboScore()",
      "per_assignment": "TimetableSolver::qualityScore() = Σ comboScore + halfGroupGroupingScore()"
    },
    "terms": [
      { "id": "S-1", "weight": 100, "term": "por cada día distinto usado por la lección", "intent": "repartir la carga semanal" },
      { "id": "S-2", "weight": -50, "term": "por cada par de días consecutivos usados por la lección", "intent": "evitar bloques en días seguidos" },
      { "id": "S-3", "weight": -10, "term": "por cada bloque teórico con order_in_day > 3", "intent": "evitar teoría al final del día" },
      { "id": "S-4", "weight": "+half_group_bonus", "term": "por cada bloque de una lección medio-grupo ubicado en una celda que ya tiene al menos otro medio-grupo de la misma sección", "intent": "concentrar las mitades de una sección (HG-02)", "applies_when": "half_group_priority && lección is_half_group" }
    ],
    "assignment_term": {
      "id": "S-5",
      "method": "TimetableSolver::halfGroupGroupingScore()",
      "weight": "+half_group_bonus",
      "term": "Σ sobre celdas (período·sección) de max(0, mitades − 1) × half_group_bonus",
      "note": "Se suma una sola vez sobre la asignación completa. Es el desempate entre intentos con igual cobertura (HG-05): n mitades en una celda forman n−1 parejas."
    },
    "ordering": [
      { "id": "O-1", "order": "constraint", "effect": "Más restringida primero: priority*10 + (half_group_priority && is_half_group ? 6 : 0) + (room_type_required != null ? 5 : 0) + min(bloques, 9)." },
      { "id": "O-2", "order": "scarcity", "effect": "Docentes con menos períodos disponibles primero." },
      { "id": "O-3", "order": "blocks_desc", "effect": "Lecciones con más bloques primero." },
      { "id": "O-4", "order": "random", "effect": "Barajado determinista por semilla (restarts)." },
      { "id": "O-5", "order": "repair", "effect": "Prioriza las lecciones que quedaron sin asignar." },
      { "id": "O-6", "order": "half_group_first", "effect": "Medio-grupos primero (agrupados por sección asc), luego por grado de restricción. Intento 'S1h', solo si half_group_priority (HG-03)." }
    ],
    "clustering": {
      "method": "TimetableSolver::clusterHalfGroupsBySection()",
      "effect": "Reordenamiento estable que deja consecutivos los medio-grupos de una misma sección (cuando hay ≥2) para que el backtracking los explore juntos (HG-04). Se aplica en todo intento salvo 'half_group_first', y solo si half_group_priority.",
      "note": "Es blando: no obliga a que las mitades compartan período."
    }
  },

  "levers": [
    { "field": "priority", "effect": "Se ubica antes (no es restricción dura)." },
    { "field": "locked", "effect": "Reserva bloques si el lock es completo; una lección medio-grupo nunca se trata como locked salvo en strategy 'legacy'." },
    { "field": "is_half_group", "effect": "Ocupa la celda de su sección como medio-grupo (hasta max_subjects_per_period, D-6). Con half_group_priority se explora antes que los grupos completos (HG-01) y se prefiere la celda que ya agrupa mitades de la sección (HG-02/S-5)." },
    { "field": "allow_shared_teacher", "effect": "Relaja solo D-1 (docente); nunca sección ni aula." },
    { "field": "room_type_required", "effect": "Solo se exige en bloques prácticos; los teóricos van sin aula." },
    { "field": "shift_id", "effect": "Turno preferido; puede haber fallback salvo en locked." },
    { "field": "max_subjects_per_period", "effect": "Tope de medio-grupos por celda." },
    { "field": "strategy", "effect": "'legacy' reproduce los slots importados; 'optimized' (default) ejecuta el solver CSP." }
  ],

  "diagnostics": {
    "emitted_today": ["capacity_exceeded", "not_found"],
    "note": "El pipeline clasifica el residual en DOS cubetas string: 'capacity_exceeded' (lección marcada por TimetableCapacityAuditService) y 'not_found' (el resto). Los seis códigos del enum UnassignedReason existen como vocabulario tipado, pero hoy NO se emiten desde ningún punto del pipeline: se documentan porque son la nomenclatura prevista.",
    "codes": [
      { "code": "capacity_exceeded", "label": "Exceso de capacidad (imposible de agendar)", "action": "Ajusta horas, períodos o turnos: regenerar no la ubicará." },
      { "code": "not_found", "label": "No encontrada por la búsqueda heurística", "action": "Vuelve a generar; la estrategia con fallback puede ubicarla." },
      { "code": "combo_cap_reached", "label": "Tope de combinaciones alcanzado", "action": "Vuelve a generar con más presupuesto/intentos." },
      { "code": "timeout_reached", "label": "Tiempo de búsqueda agotado", "action": "Aumenta el presupuesto del solver o reduce restricciones." },
      { "code": "incomplete_initial_setup", "label": "Estructura de períodos incompleta", "action": "Completa períodos y turnos en el Paso 1." },
      { "code": "repair_failed", "label": "La reparación no pudo liberar espacio", "action": "Revisa las clases paralelas o bloqueadas de la sección." }
    ],
    "grouping_warnings": [
      { "type": "half_group_isolated", "source": "TimetablePublicationReadinessService::isolatedHalfGroups()",
        "message": "Hay {n} medio-grupo(s) sin agrupar en un mismo período con su par de sección.",
        "blocking": false,
        "action": "Revisa si falta celda con tope disponible, hay choque de docente o disponibilidad bloqueada." }
    ]
  },

  "limits": {
    "budget_seconds": 30, "attempt_seconds": 8, "restarts": 6, "repair_attempts": 2,
    "max_combos_per_lesson": 1000, "max_candidate_pool": 14,
    "max_candidate_pool_abs": 26, "max_combo_nodes": 500000
  },

  "config": {
    "half_group_priority": true,
    "half_group_bonus": 20,
    "note": "Valores EFECTIVOS al momento del export, leídos de config('timetable.solver.*'). No son defaults hardcodeados: si el entorno cambia la variable, el playbook lo refleja."
  },

  "derived_metrics": {
    "note": "No se almacenan en el snapshot (son recalculables desde los slots); se documentan para que un analista use las mismas definiciones que el sistema (SolverOutcome::halfGroupMetrics()).",
    "keys": {
      "half_group_lessons": "lecciones is_half_group, asignadas + sin asignar",
      "half_group_grouped_periods": "celdas (período·sección) con ≥2 mitades",
      "half_group_isolated": "celdas (período·sección) con exactamente 1 mitad",
      "half_group_unassigned": "mitades sin ningún slot"
    }
  },

  "invariants": [
    "El playbook es documentación de salida: el restore NUNCA lo lee ni aplica reglas desde él.",
    "No entra en el checksum (§7): editarlo no invalida snapshots existentes.",
    "Describe el solver en el momento del export; no sustituye al código ni es fuente de verdad.",
    "Los pesos y reglas viven en código y en config/timetable.php (variables de entorno); no existe una tabla de pesos configurable por calendario."
  ]
}
```

| Sub-bloque | Para qué lo usará el LLM |
|---|---|
| `flow` | Entender el ciclo export → preview → apply y el pipeline de generación |
| `objective` | Saber qué significa "mejor": cobertura primero, luego score soft |
| `hard_rules` | Explicar por qué una ubicación es inválida (y qué excepción la relaja) |
| `soft_rules` | Razonar sobre calidad: pesos y orden de asignación |
| `levers` | Proponer cambios accionables (qué tocar y qué efecto tiene) |
| `diagnostics` | Interpretar el residual (`unassigned`) y los avisos de agrupación, y sugerir la acción correspondiente |
| `limits` | Conocer el presupuesto y los topes que explican un timeout o un residual |
| `config` | Ver qué palancas estaban activas en el export (`half_group_priority`, `half_group_bonus`) |
| `derived_metrics` | Calcular las métricas de agrupación con las mismas definiciones que el sistema |
| `invariants` | No proponer cambios que el sistema no puede aplicar |

**Test anti-deriva (§14, casos 17–19).** El bloque se valida contra el código: todo caso de `UnassignedReason`
debe aparecer en `diagnostics.codes`; toda constante de orden de `SolverAttemptConfig` debe aparecer en
`soft_rules.ordering`; y todo método citado en `hard_rules`/`soft_rules` debe existir (vía `Reflection`). Si
alguien añade una regla y no actualiza el playbook, la suite falla.

## 6. Mapeo de identidad

El restore **borra y recrea** las lessons, así que sus `lesson_id` cambian. Los slots **no pueden**
referenciar PKs locales; se re-vinculan por claves estables:

| Relación | Clave de re-vinculación | Motivo |
|---|---|---|
| slot → lección | `pevaluacion_id`, con respaldo en `academic_identity` | Mecanismo ya existente en el restore actual (`TimetableWizard.php:3490`) |
| slot → periodo | `(shift_code, day_of_week, order_in_day)` | `period_id` se regenera; la terna es estable |
| **availability → bloque** | `(profesor_id, shift_code, day_of_week, order_in_day)` | **No** usa `period_id`: la disponibilidad es por turno·día·bloque (`2026_09_08_000003`, §9.10) |
| room / profesor / sección / grupo | `room_id`, `profesor_id`, `seccion_id`, `grupo_estable_id` | FK a entidades que **no** se recrean |

`shift_code` es la clave del turno (`M` = mañana, `T` = tarde), no `shift_id`. Se resuelve con
`resolveShiftId()` (`TimetableWizard.php:2777`), que ya acepta código o id.

## 7. Integridad: checksum

`checksum_algo: "sha256-semantic-v1"` define el algoritmo de forma unívoca:

1. Tomar del payload **solo el contenido semántico**, en este orden fijo de claves:
   `calendar`, `periods`, `section_locks`, `availability`, `lessons`, `slots`.
2. Ordenar recursivamente las claves de cada objeto (`ksort`).
3. Serializar con `json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)`
   — **sin** `JSON_PRETTY_PRINT`.
4. `checksum = 'sha256:' . hash('sha256', $canonical)`.

**Se excluyen del hash** `checksum`, `checksum_algo`, `exported_at`, `exported_by`, `app_version`,
`environment`, **`schema`** y **`playbook`**. Excluir `schema` y `playbook` es deliberado: son documentación
embebida, y si su redacción cambiara en un parche no debe invalidar todos los snapshots existentes.
`exported_at`/`exported_by` son metadatos no semánticos.

- Se calcula al exportar (paso 4 de §8).
- El restore lo recalcula en la Fase 1 (§9.1). Si no coincide → se rechaza con "archivo corrupto o alterado"
  y **no se toca la BD**.
- **No** se hashean los bytes del archivo (que van con `JSON_PRETTY_PRINT`); se hashea el re-encode canónico.
- En payloads legacy sin `checksum` la comprobación se omite.

## 8. Flujo de export

`downloadCalendarLessonsBackup()` extendido:

1. Guards vigentes sin cambios: `calendarId` presente → si no, warning "Calendario requerido"; calendario
   existente → si no, error "Calendario no encontrado".
2. Recolecta `periods`, `section_locks`, `availability`, `lessons` (`calendarLessonsRows()`, ya existe) y
   `slots` (§5.5 para `is_practical`; §5.6 para half-group/shared-teacher).
3. Si `lessons === []` → warning "Sin lessons para respaldar" y retorno nulo (igual que hoy).
4. Añade metadatos de auditoría (`exported_by`, `app_version`, `environment`), `checksum`, `checksum_algo`,
   `version: 1` y el `schema` actualizado.
5. Nombre de archivo: `snapshot-calendario-{id}-{Ymd_His}.json`.
6. `response()->streamDownload(...)` con
   `JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR`.

## 9. Flujo de restore

### 9.1 Fase 1 — Validar (no toca la BD)

1. `calendarId` presente y archivo seleccionado.
2. Extensión `.json`, **MIME** `application/json`/`text/plain` y tamaño ≤ 5 MB (límite vigente). La
   validación MIME se añade a la de extensión.
3. `json_decode` con `JSON_THROW_ON_ERROR`.
4. `format` reconocido:
   - `cfla-timetable-calendar-snapshot` (reemplazo) → `version === 1` y verificar `checksum` (§7);
   - `cfla-timetable-lessons-backup` (legacy, aditivo) → flujo actual `applyLessonsBackupFile()`
     (`TimetableWizard.php:3412`), sin cambios;
   - cualquier otro → rechazo.
5. `calendar.lapso_id` y `calendar.pestudio_id` coinciden con el calendario destino (igual que hoy).
6. El usuario autenticado tiene permiso de coordinación sobre el módulo (mismo guard que el resto del wizard).

Cualquier fallo → notificación de error y fin. Sin escrituras.

### 9.2 Fase 2 — Preview (dry-run)

Calcula contra la BD actual, sin escribir:

| Métrica | Descripción |
|---|---|
| Filas del snapshot resolubles / no resolubles | Match por `pevaluacion_id`, respaldo en `academic_identity` |
| Lessons actuales que se perderán | Las que están en BD y **no** en el snapshot |
| Slots actuales | Conteo de `timetable_slots` del calendario (se sustituyen) |
| Slots nuevos / omitidos | Del snapshot con terna de periodo resoluble; omitidos con motivo |
| Disponibilidad reemplazada | Filas de `timetable_teacher_availability` del calendario |
| Bloqueos de sección | Cambios en `seccions.timetable_locked` |
| **Colisiones de unicidad** | Pares de slots que violarían `uq_slot_lesson` / `uq_slot_section` / `uq_slot_room` / `uq_slot_teacher` (incluye el efecto de half-group/shared-teacher en las claves generadas) |
| **Advertencias** | Periodo inexistente en el destino; `profesor_id`/`seccion_id`/`room_id` no resueltos; drift en campos desnormalizados (§5.3); `slots: []` ⇒ "el horario quedará vacío" (§5.4) |

El preview guarda en sesión: el **payload validado**, el **hash del diff** y `calendar.version` en el momento
del cálculo. Se abre el modal (§11).

### 9.3 Fase 3 — Aplicar (tras confirmación)

**Concurrencia (D5):** antes de escribir, revalidar que `calendar.version` no cambió y que el hash del estado
actual coincide con el del preview. Si cambió → abortar con "el calendario cambió desde el preview; vuelve a
previsualizar".

1. **Auto-backup (red de seguridad).** Genera el mismo snapshot del estado **actual** del calendario y lo
   escribe en `storage/app/timetable-snapshots/auto-{id}-{Ymd_His}-{hash8}.json` (el sufijo `hash8` evita
   colisiones dentro del mismo segundo). Si falla → **aborta todo**, no se aplica nada.
2. **Gate por estado (D6).** Si `calendar.status === 'active'`, la confirmación del modal (§11) advierte de
   forma explícita que se reemplaza un horario publicado.
3. **Transacción** (`DB::transaction`):
   - borra las `lessons` del calendario → los slots caen por *cascade* (§9.5);
   - borra las filas de `availability` del calendario;
   - limpia los `timetable_conflicts` huérfanos del calendario;
   - recrea las lessons vía `persist()` (`TimetableLessonPersistenceService`);
   - **no** borra ni recrea `periods`: los usa como referencia y crea solo los faltantes por
     `(shift_code, day_of_week, order_in_day)`;
   - recrea los slots mapeando lesson→pev y period→terna `(shift, day, order)`, derivando los campos
     desnormalizados de la lesson (§5.3) y respetando `is_practical` solo si la columna existe (§5.5);
   - restaura `availability` (por la terna de turno·día·bloque, §6) y `seccions.timetable_locked`.
4. **Reporte.** Notificación de éxito con: N lessons, M slots, K filas de disponibilidad, J bloqueos, filas
   omitidas, colisiones resueltas y **ruta del auto-backup**.
5. **Fallo.** Rollback total de la transacción. El auto-backup permanece en disco y se informa su ruta.

### 9.4 Inserciones y rendimiento

Los slots se insertan por *chunks* (p. ej. 500 filas) dentro de la transacción, para no superar el límite de
placeholders ni bloquear los índices únicos con una sola sentencia gigante. Calendarios grandes pueden
tardar; el `wire:loading` existente sigue cubriendo la espera.

### 9.5 Reversibilidad real del auto-backup

El auto-backup se escribe **con el mismo `format` y `build()`**, de modo que es un snapshot válido que puede
re-aplicarse. Además de informar la ruta, el reporte deja una acción **"Deshacer último restore"** (misma
sesión) que vuelve a entrar por el flujo normal de preview/apply usando ese archivo. La rotación/limpieza en
disco queda fuera de alcance (§17).

### 9.6 Representación de medio-grupo y colisiones

Un mismo `lesson`/`period` puede producir **dos slots** válidos cuando `is_half_group = true` (la clave
generada `slot_teacher_key` incluye `lesson_id`, `2026_09_11_000002:23-42` y
`2026_09_13_000002:44-53`). El snapshot conserva ambas filas; el preview deduplica por
`(lesson_id, period_id, seccion_id, grupo_estable_id, is_practical)` y reporta cualquier colisión residual
contra `uq_slot_*` antes de escribir.

## 10. Compatibilidad y versionado

- El respaldo legacy `cfla-timetable-lessons-backup` sigue restaurándose con el comportamiento **aditivo**
  vigente; el botón lo detecta y lo enruta a `applyLessonsBackupFile()`.
- El snapshot `cfla-timetable-calendar-snapshot` v1 activa el modo **reemplazo**.
- La clave `schema` embebida documenta el formato del snapshot; sigue siendo autocontenida y legible por
  agentes. **No entra en el checksum** (§7).
- Ante futuros cambios incompatibles se incrementa `version` del snapshot y el restore decide por ella.

## 11. Restricciones de integridad referencial

Verificado en `database/migrations/bck/timetable/2026_08_15_000001_create_timetable_tables.php` y en las
migraciones posteriores del módulo:

| FK | Regla | Consecuencia de diseño |
|---|---|---|
| `timetable_slots.lesson_id` → `timetable_lessons` | `onDelete('cascade')` | Borrar la lesson **ya** elimina sus slots; no hay que borrarlos aparte |
| `timetable_slots.period_id` → `timetable_periods` | `onDelete('cascade')` | **No** se borran los periods: arrastrarían slots |
| `timetable_conflicts.slot_id`, `.lesson_id`, `.period_id` | `onDelete('set null')` | Dejan filas huérfanas; se limpian explícitamente |
| `timetable_teacher_availability.shift_id` → `timetable_shifts` | `onDelete('cascade')` | La disponibilidad **no** depende de `periods` |
| `timetable_teacher_availability.calendar_id` | `onDelete('cascade')` | La disponibilidad es **por calendario**: restaurarla no afecta a otros calendarios |

**Corrección importante frente a v1.0:** la disponibilidad docente **ya no** referencia `period_id`. La
migración `2026_09_08_000003_rework_teacher_availability_per_shift.php:59-84` elimina esa columna y la
reemplaza por `shift_id + day_of_week + order_in_day + start_time + end_time`, con único
`uq_avail (calendar_id, profesor_id, shift_id, day_of_week, order_in_day)`. El restore re-vincula por la
terna de turno·día·bloque, no por periodo.

> **Nota de portabilidad de esquema:** la migración base vive en `database/migrations/bck/timetable/`, fuera
> del descubrimiento de Artisan. Un entorno reconstruido solo desde `database/migrations/` podría no
> reproducir estas FK (ni las columnas `is_practical`, `is_half_group`, `allow_shared_teacher` ni las claves
> generadas). El diseño **no** debe asumir el *cascade* ni la presencia de esas columnas sin verificar en
> tiempo de ejecución (`Schema::hasColumn`):

| Columna | Migración | Uso en restore |
|---|---|---|
| `is_practical` | ausente de migraciones (presente en prod) | Escribir solo si `hasColumn` |
| `is_half_group` | `2026_09_09_000001` | Recrear slots y validar `slot_teacher_key` |
| `allow_shared_teacher` | `2026_09_13_000002` | Recrear slots y validar `slot_teacher_key` |
| `slot_teacher_key`, `slot_section_key` | generadas (`2026_09_11_000002`, `2026_09_13_000002`) | No se escriben; las calcula MySQL |

## 12. UI

- El botón de la barra superior pasa a **"Snapshot JSON"**, con tooltip "snapshot completo del calendario:
  configuración + horario".
- El restore **deja de aplicar directo**. Al seleccionar el archivo se dispara el preview
  (`updatedCalendarLessonsBackupFile`) y se abre el modal.
- **Modal** (reutiliza `dialog()->confirm` de WireUI, ya usado en el wizard):
  - resumen del preview (§9.2);
  - advertencias, filas omitidas y colisiones;
  - aviso explícito de que se tomará un auto-backup y de que la acción es destructiva;
  - aviso reforzado si `calendar.status === 'active'` (D6);
  - acciones: "Aplicar snapshot" (color `negative`) y "Cancelar".
- Se conservan los indicadores `wire:loading` / `wire:target` existentes.

## 13. Interfaz del servicio

`TimetableWizard` supera las 8.000 líneas. La lógica nueva se extrae a un servicio para no engordar el
componente:

**`app/Services/Timetable/TimetableCalendarSnapshotService.php`**

| Método | Firma | Responsabilidad |
|---|---|---|
| `build` | `build(TimetableCalendar $calendar): array` | Arma el payload + `checksum` (§7) |
| `verify` | `verify(array $payload): array` | Valida formato, versión y checksum; devuelve metadatos o lanza excepción |
| `canonicalize` | `canonicalize(array $payload): string` | Re-encode canónico para el hash (§7) |
| `preview` | `preview(TimetableCalendar $calendar, array $payload): array` | Diff contra la BD, sin escribir; incluye colisiones y `calendar.version` |
| `apply` | `apply(TimetableCalendar $calendar, array $payload): array` | Revalida concurrencia + auto-backup + transacción + reporte |

El componente Livewire queda como orquestador: guards, notificaciones, sesión y modal.

El bloque `playbook` (§5.7) lo produce **`TimetableSolverPlaybook`**
(`app/Services/Timetable/Solver/TimetableSolverPlaybook.php`), invocado por `build()` como única fuente de las
reglas. `verify()`, `preview()` y `apply()` **lo ignoran por completo**: es salida, no entrada (§5.7, regla 3).
Se genera con los **valores efectivos** de `config('timetable.solver.*')` (presupuesto, `restarts`,
`half_group_priority`, `half_group_bonus`), no con los defaults hardcodeados, para que un snapshot antiguo
documente las reglas que realmente se aplicaron cuando se exportó.

## 14. Pruebas

Extienden `tests/Feature/Timetable/TimetableCalendarBackupTest.php` (usa `DatabaseTransactions`):

| # | Caso | Aserción |
|---|---|---|
| 1 | Export snapshot | Incluye `periods`, `slots`, `availability`, `section_locks` y checksum válido |
| 2 | Checksum alterado | Rechazado **y BD intacta** |
| 3 | **Checksum estable** | Re-encode idéntico produce el mismo hash; cambiar `schema`/`exported_at` **no** lo cambia |
| 4 | Preview | No escribe (conteos sin cambios) |
| 5 | Restore snapshot | Los slots viejos desaparecen y se aplican los del snapshot |
| 6 | Legacy `cfla-timetable-lessons-backup` | Sigue siendo aditivo: no borra slots |
| 7 | Auto-backup | Se crea antes de aplicar (`Storage::fake`) y es re-aplicable |
| 8 | Dry-run | Reporta omitidos cuando la `pevaluacion_id` no existe |
| 9 | Rollback | Forzando una excepción a mitad, la BD queda igual |
| 10 | **Concurrencia** | Cambiar `calendar.version` entre preview y apply aborta sin escribir |
| 11 | **`slots: []`** | Vacía el horario y el preview lo advierte |
| 12 | **Periodo no resoluble** | Se omite con advertencia; no aborta el restore |
| 13 | **Half-group** | Round-trip de dos slots medio-grupo con la clave generada correcta |
| 14 | **Availability** | Round-trip por `shift_id + day_of_week + order_in_day` (+ horas) |
| 15 | **Section locks** | Round-trip de `seccions.timetable_locked` |
| 16 | **Portabilidad** | Snapshot con `calendar.id` distinto empareja por `lapso_id` + `pestudio_id` |
| 17 | **Playbook fuera del checksum** | Editar `playbook` (o añadir/quitar claves) **no** cambia el checksum; el snapshot sigue siendo válido |
| 18 | **Playbook cubre los diagnósticos** | Todo caso del enum `UnassignedReason` aparece en `playbook.diagnostics.codes` con `code`/`label`/`action` idénticos a los del enum |
| 19 | **Playbook sin referencias muertas** | Todo método citado en `hard_rules`/`soft_rules` existe (vía `Reflection`), y toda constante de orden de `SolverAttemptConfig` aparece en `soft_rules.ordering` — incluida `ORDER_HALF_GROUP_FIRST` (O-6) |
| 20 | **Playbook refleja la config efectiva** | Con `timetable.solver.half_group_bonus` cambiado, `playbook.config.half_group_bonus` cambia y el **checksum no** |
| 21 | **Métricas derivadas coherentes** | `playbook.derived_metrics` describe exactamente las claves que devuelve `SolverOutcome::halfGroupMetrics()` |

## 15. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| La fase destructiva borra datos por error | Checksum + preview obligatorio + auto-backup previo + transacción |
| El *cascade* no existe en algún entorno | Prueba de reemplazo que verifica que los slots viejos desaparecen; si no, borrado explícito |
| Un preview viejo pisa cambios ajenos | `calendar.version` + hash del diff revalidados en apply (§9.3) |
| Colisión de índices únicos al reinsertar | Dedup en preview + inserción por chunks (§9.4, §9.6) |
| El auto-backup en disco crece sin control | Fuera de alcance: rotación/limpieza en otro documento; esta versión añade acción "Deshacer" |
| Snapshot de calendarios grandes | Se mantiene `streamDownload`; el preview opera sobre conteos agregados, no sobre el volcado completo |

## 16. Archivos afectados

| Archivo | Cambio |
|---|---|
| `app/Services/Timetable/TimetableCalendarSnapshotService.php` | **Nuevo** — `build` / `verify` / `canonicalize` / `preview` / `apply` |
| `app/Services/Timetable/Solver/TimetableSolverPlaybook.php` | **Nuevo** — única fuente del bloque `playbook` (§5.7): flujo, reglas duras/blandas, palancas, diagnósticos, límites |
| `app/Livewire/Coordinacion/Timetable/TimetableWizard.php` | Extiende `downloadCalendarLessonsBackup`; `restoreCalendarLessonsBackup` orquesta preview → confirmar → aplicar; detecta legacy |
| `resources/views/livewire/coordinacion/timetable/timetable-wizard.blade.php` | Renombra el botón, dispara el preview al elegir archivo, añade el modal |
| `tests/Feature/Timetable/TimetableCalendarBackupTest.php` | Casos de snapshot, checksum, preview, reemplazo, concurrencia, legacy, rollback |

## 17. Plan de implementación

1. `TimetableCalendarSnapshotService::build()` + `canonicalize()` + `TimetableSolverPlaybook` + export +
   pruebas 1, 3, 17, 18, 19.
2. `verify()` + validación de checksum + prueba de rechazo (caso 2).
3. `preview()` + pruebas de dry-run, omitidos, colisiones, `slots: []` (casos 4, 8, 11, 12, 13).
4. `apply()` (concurrencia + auto-backup + transacción + reporte) + pruebas de reemplazo, legacy, rollback,
   concurrencia, availability, section locks (casos 5, 6, 7, 9, 10, 14, 15).
5. UI: botón, disparo del preview, modal y acción "Deshacer".
6. Verificación manual: descargar snapshot, elegir restore, revisar el modal, cancelar y aplicar.

## 18. Preguntas resueltas

1. **`section_locks`** — se mantiene, pero **opcional** y con advertencia explícita: toca `seccions`, fuera de
   las tablas de horario, y el lock es por sección (no por calendario), por lo que puede afectar a otros
   calendarios que compartan la sección. El preview lista los cambios y el usuario confirma.
2. **Nombre del botón/archivo** — **"Snapshot JSON"** y `snapshot-calendario-{id}-{Ymd_His}.json`.

## 19. Changelog

| Versión | Fecha | Cambio |
|---|---|---|
| 1.0 | 2026-09-14 | Propuesta inicial. |
| 2.0 | 2026-09-14 | Corrige §11 (availability sin `period_id`); añade `is_practical`, `is_half_group`, `allow_shared_teacher` y claves generadas; formato nuevo `cfla-timetable-calendar-snapshot` en vez de bump destructivo; checksum semántico exacto y excluyente de `schema`; concurrencia (D5), gate por estado (D6), precedencia de desnormalizados, semántica de `slots: []`, reversibilidad del auto-backup, dedup de colisiones y pruebas ampliadas. |
| 2.1 | 2026-09-14 | Añade el bloque `playbook` (§5.7): el snapshot embebe el flujo y las reglas del solver (duras, blandas, palancas, diagnósticos, límites) en formato consumible por un LLM. Excluido del checksum y nunca leído por el restore. Pruebas anti-deriva 17–19. |
| 2.2 | 2026-09-14 | Alinea el `playbook` con `PLAN-TIMETABLE-HALFGROUP-001.md` (ADR-TT-012-08): añade S-4 (bonus en `comboScore`) y S-5 (`halfGroupGroupingScore`), el orden O-6 `half_group_first` y el clustering HG-04; añade los bloques `config` (valores efectivos) y `derived_metrics`; documenta el warning `half_group_isolated`; amplía §5.6 y las pruebas 20–21. |
