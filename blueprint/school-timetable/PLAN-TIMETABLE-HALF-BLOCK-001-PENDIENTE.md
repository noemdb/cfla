# PLAN-TIMETABLE-HALF-BLOCK-001 — Pendiente (Fases 4–7)

## Continuación de la implementación de «Clase mitad de bloque»

| | |
|---|---|
| **Estado** | Fases 0–3 ✅ implementadas y con suite verde. Fase 4 ✅ (persistencia + snapshot + bloqueo con mitad). Fase 5 ✅ (UI wizard + light). Fase 6 ✅ (optimizador por sección + readiness/publicación con mitades). Fase 7 ✅ (solo-lectura + PDFs + docs). **Feature completa.** |
| **Norma** | `ADR-TT-017-HALF-BLOCK.md` (contrato: sub-franja, invariantes H1–H5, B1–B3, DDL). |
| **Área** | Timetable — persistencia de slots, UI (wizard + light), editor/capacidad/publicación, docs. |
| **Contrato base** | `SPEC-TIMETABLE-001-v2.md`; ADR-TT-003/007/010/011/017. |
| **No incluye** | Cambios de esquema nuevos (la migración de Fase 1 ya está aplicada). |
| **Entorno** | PHP **8.2** (`php8.2`), BD producción `s2627`. Tests con `DatabaseTransactions`. |

> Regla de proyecto: este es un sistema en producción. Cualquier cambio de BD, `.env`,
> rutas, estado de calendario o caché requiere aviso + confirmación explícita.

---

## 0. Qué ya está hecho (Fases 0–3)

**Fase 0 — Diseño**: `ADR-TT-017-HALF-BLOCK.md`. Sub-franja = `(período, mitad)`,
`mitad ∈ {0=superior, 1=inferior}`. `half` nullable: `NULL` = bloque completo (ocupa
ambas mitades), `0`/`1` = media franja. Invariantes y DDL en el ADR.

**Fase 1 — Esquema/modelos** (migración **aplicada**, batch 29):
`database/migrations/2026_10_19_000001_add_half_block_to_timetable_lessons_and_slots.php`
- `timetable_lessons.half_block` (tinyint(1), default 0)
- `timetable_slots.half` (tinyint unsigned, **nullable**)
- `timetable_slots.slot_half_key` CHAR(2) (`'F'` para NULL, `'B0'`/`'B1'`)
- Claves generadas `slot_section_key`/`slot_teacher_key` ampliadas con sufijo de mitad;
  `uq_slot_room` redefinida para incluir `slot_half_key`; `uq_slot_lesson` sin cambios.
- Modelos: `TimetableLesson` (`half_block`), `TimetableSlot` (`half`, oculta `slot_half_key`).
- Factorías actualizadas. Datos intactos (789 lecciones / 1618 slots).

**Fase 2 — Núcleo de ocupación**:
- `app/Services/Timetable/Solver/SchedulingContext.php`: ocupación por sub-franja.
  `isFree/occupy/release` reciben `?int $half = null` **al final** (compatibilidad total:
  `null` = ambas mitades). Añadido `halfGroupLoad(period, seccion, ?half)` y
  `sectionHalfLoad(period, seccion, ?half)`.
- `SlotCandidate`: `?int $half = null`. `LessonToSchedule`: `bool $halfBlock = false`,
  `constraintDegree(..., bool $halfBlockPriority = false)`.
- `app/Services/Timetable/ConflictValidator.php`: `validate(...)` acepta `?int $half`
  (candidato de media franja filtra `half IS NULL OR half = ?`).
- `app/Livewire/Coordinacion/Timetable/TimetableWizard.php`: `buildLesson()` añade
  `half_block` y usa `blocksForMinutes($hours, $periodMinutes, $halfBlock)`
  (demanda = `ceil(hours*60 / (periodMinutes/2))` cuando es media franja).

**Fase 3 — Solver**:
- `TimetableSolver::buildDomain()` genera un `SlotCandidate` por (período, mitad) para
  lecciones `halfBlock`; pool de candidatos x2 en ese caso.
- `comboScore()` + `halfBlockGroupingScore()` (B1/B2, `half_block_bonus`).
- Constructor de `TimetableSolver`: `bool $halfBlockPriority`, `int $halfBlockBonus`.
- `SolverAttemptConfig::ORDER_HALF_BLOCK_FIRST` = `'half_block_first'`; intento **S1b**
  en `TimetableSolverOrchestrator` cuando `half_block_priority`.
- `config/timetable.php`: `half_block_priority` (default true), `half_block_bonus`
  (default 15), vía `TIMETABLE_SOLVER_HALF_BLOCK_*`.
- `TimetableSolverPlaybook`: S‑6, estrategia de orden y levers (suite anti-deriva).
- `GenerateTimetableJob`: DTO con `halfBlock`, `SlotCandidate` con `half`, config del
  orquestador.

**Tests nuevos (verde)**:
- `tests/Feature/Timetable/TimetableHalfBlockSchemaTest.php` (4)
- `tests/Unit/Timetable/HalfBlockSchedulingContextTest.php` (6)
- `tests/Unit/Timetable/HalfBlockSolverTest.php` (4)
- `tests/Feature/Timetable/HalfBlockConflictValidatorTest.php` (4)

---

## 1. Fase 4 — Persistencia y snapshot ✅ (hecha)

Objetivo: que `half` sobreviva al ciclo solver → preview → guardado en BD → snapshot,
sin cambiar el esquema.

1. **`app/Jobs/Timetable/GenerateTimetableJob.php`**
   - `persist(...)` (~L811): al construir/filtrar filas de `timetable_slots`, incluir
     `half` en el `insert`/`updateOrCreate`. Buscar donde arma el array de columnas de
     slot (junto a `is_half_group`, `allow_shared_teacher`) y añadir
     `'half' => $slot->half ?? null` (o `data_get($row, 'half')`).
   - `serializeAssignment(...)` (~L936) y `storeDryRunPreview(...)` (~L640): incluir
     `half` en el payload serializado para que el preview lo conserve.
   - `assignmentDiagnostics(...)` (~L776): opcional, exponer la mitad para reportes.
2. **`app/Livewire/Coordinacion/Timetable/TimetableWizard.php`**
   - `persistPreviewLessonSlotsToDatabase(array $lessonIds)` (~L7565): escribir
     `half` en el slot (`updateOrCreate`/`insert`), tomándolo del payload de preview.
   - `syncPreviewPayloadFromSlots()` (~L8961): incluir `'half'` al reconstruir el
     payload desde los slots (hoy solo lee periodo/aula/is_half_group).
   - Carga inicial de slots al preview (~L795) y configuración/restore (~L4461, ~L4926,
     ~L6286, ~L6448, ~L6521, ~L6632, ~L7086, ~L7407, ~L7609): añadir `half`.
   - Validación de solapes manuales (~L5674, ~L5806, ~L6341): incluir la mitad en la
     comprobación de conflicto al mover/soltar (usar `ConflictValidator` con `half`).
3. **`app/Services/Timetable/TimetableLessonPersistenceService.php`**
   - `persist(...)` (~L20–L140): incluir `'half'` en la fila del slot (junto a
     `is_half_group`).
4. **`app/Services/Timetable/TimetableCalendarSnapshotService.php`**
   - `slotSectionKey()` (~L866) y `slotTeacherKey()` (~L878): añadir el sufijo de mitad
     (`F`/`B0`/`B1`) para coincidir con las claves generadas en BD (evita falsos
     positivos de unicidad en import/export).
   - Serialización de slots (~L776, ~L796, ~L976, ~L1183, ~L1252) y descripción de
     columnas (~L274, ~L294): documentar/exportar `half` y `slot_half_key`.

**Verificación Fase 4**: test de feature que (a) soluciona una lección `half_block`,
(b) persiste el preview, (c) recarga los slots y comprueba que `half` se conserva y que
las claves generadas coinciden con las del snapshot.

---

## 2. Fase 5 — UI (wizard + light) ✅ (hecha)

1. **Wizard** `resources/views/livewire/coordinacion/timetable/timetable-wizard.blade.php`
   + componente `TimetableWizard`:
   - Checkbox/modal «Mitad de bloque» por lección que fija
     `$this->lessons[$idx]['half_block']` y **recalcula la demanda** con
     `blocksForMinutes(..., halfBlock: true)` (método ya existe).
   - Celda partida visualmente en dos sub-franjas (superior/inferior) cuando el período
     tiene alguna lección `half_block`.
   - Drag/subida con selección de mitad (`data-half`), respetando H1–H5; usar
     `ConflictValidator::validate(..., half: ...)`.
   - Al alternar `half_block`, limpiar/avisar de slots incompatibles del mismo período.
2. **Light** `resources/views/livewire/planning/timetable/timetable-light.blade.php` +
   `app/Livewire/Planning/Timetable/TimetableLight.php` (rama sin commitear de la
   reestructuración `/light`; coordinar antes de commitear).
   - Reutilizar el partial compartido `step3-lessons` (ver `docs/timetable/README.md`).
   - Mostrar y permitir editar la mitad; el guardado (`save`→grid) ya llama a
     `syncPreviewPayloadFromSlots()`.

**Verificación Fase 5**: tests Livewire de crear una lección `half_block`, asignar
mitades y comprobar el render de celdas partidas.

### Implementación (Fase 5)

- **Toggle de lección** — `TimetableWizard::setLessonHalfBlock(int $pevId, ?bool $value = null)`:
  conmuta `lessons[$pevId]['half_block']`, recalcula `weekly_blocks_t/p` con la duración de
  período del calendario y avisa si ya hay slots en el preview. La vista usa un checkbox
  con `wire:click` (no `wire:model`) porque el cambio debe disparar el recálculo; el estado
  `checked` se refresca incluyendo el flag en el `wire:key`.
- **Columna «Mitad ½»** — en el partial compartido
  `partials/step3-lessons-table.blade.php`, por lo que aparece tanto en el wizard como en el
  light (pestaña Clases). Deshabilitada cuando la sección está bloqueada o la lección no está
  seleccionada.
- **Celdas partidas** — `previewSectionGrid()` emite `half` (`0/1/null`) y `half_block`; las
  grillas del wizard y del light agrupan por mitad y renderizan dos sub-franjas apiladas
  («½ sup.» / «½ inf.») con borde discontinuo y badge `½`. La mitad inferior de cada celda
  expone una **zona de drop** propia; el slot del light arrastra `data-half` y suelta con
  `movePreviewLesson(..., half)`.
- **Mitades en la persistencia manual** — `movePreviewLesson(..., ?int $half = null)` y
  `addPreviewLesson(int $lessonId, ?int $half = null)` aceptan la mitad objetivo y la
  persisten; `detectCrossSectionCollision(..., ?int $half)` ignora la colisión de docente
  entre dos mitades distintas del mismo período (secuenciales, no solapadas).
- **Limitación conocida (UX, no de datos)**: en una celda vacía el drop conserva la mitad
  que ya traía la lección arrastrada (o `null` para bloque completo); para forzar una mitad
  concreta se suelta sobre la sub-franja correspondiente de una celda que ya tiene mitades.
  No afecta a la corrección (el solver valida todo), solo a la ergonomía.
- **Tests** — `tests/Feature/Timetable/HalfBlockUiTest.php` (6): recálculo de demanda al
  conmutar (3/2 → 6/4), control en la tabla, persistencia de `half_block`, y asignación de
  mitades explícitas en el light (mover y agregar) + render de la celda partida.

---

## 3. Fase 6 — Editor, capacidad y publicación ✅ (hecha)

1. **`app/Services/Timetable/SectionTimetableOptimizer.php`** — ocupación por sub-franja:
   - Cada nodo lleva `half` (de `$slot['half']`; `null` = bloque completo) y se preserva
     en la asignación de salida (`'half' => $node['half']`).
   - `isFree/occupy/release` ahora delegan en variantes `*InHalf` y una ayuda
     `halvesFor(?int $half)` (null → `[0,1]`). Las claves internas pasan a
     `"periodId:half:..."` (docente, aula, `whole`, `half`, `group`, `group_count`).
   - `nodesConflict()` usa `halvesOverlap()`: mitades disjuntas (0 vs 1) no generan arista
     de conflicto, de modo que el coloreo/CSP pueden compartir el período.
   - Ocupación externa **dura** (`buildExternalOccupancy`, otras secciones del calendario)
     y **blanda** (ventanas de `externalTeacherBusyMap`) también por sub-franja: la ventana
     horaria de un nodo se calcula con `periodHalfWindow()` (media franja bloquea media hora
     del período). Conserva la equivalencia total cuando todos los nodos son `null`.
2. **Capacidad** — `max_subjects_per_period` sigue limitando los medio-grupos **por
   sub-franja** (`isHalfGroup`: `half[period:half] < maxSubjects`). Interacción con mitades:
   una celda `(período)` puede alojar **2 mitades distintas de la sección** (H1–H3) **más**
   los medio-grupos admitidos por cada sub-franja (hasta `max_subjects_per_period` por
   mitad); un bloque completo (`half = null`) monopoliza ambas sub-franjas (H5). El
   optimizador refleja exactamente la semántica de `SchedulingContext`.
3. **Readiness/publicación** — se hizo consciente de la mitad:
   - `TimetablePublicationReadinessService`: `teacher/section/roomPeriods` se indexan por
     `periodId:half:` (un `null` registra ambas mitades); `isolatedHalfGroups` agrupa por
     `periodId:half:seccion` (los medio-grupos comparten celda solo en la misma sub-franja).
   - `TimetableWizard::publish()`: el chequeo de colisiones contra slots preservados compara
     mitades; `$internalPublicationConflict` ignora dos mitades secuenciales distintas;
     `$existingAssignment` y `sectionReorderSnapshot()` preservan `half` en el payload.

**Verificación Fase 6**: `tests/Feature/Timetable/HalfBlockOptimizerPublicationTest.php` (8):
optimizador con dos mitades secuenciales (0 colisiones), bloque completo vs mitad (resuelve),
dos mitades sobre la misma sub-franja (conflicto); readiness/publicación con dos mitades
secuenciales, misma sub-franja, docente compartido en mitades secuenciales vs misma
sub-franja, y bloque completo vs mitad. Capacidad: `TimetableCapacityAuditTest` suma el
caso `half_block` (peso 0.5). Suite completa `tests/Unit/Timetable` +
`tests/Feature/Timetable`: **388 passed (1363 assertions)**; `pint` limpio.

---

## 4. Fase 7 — Solo-lectura, PDFs y docs ✅ (hecha)

1. **Vistas de solo lectura y PDFs** — ✅ renderizan la celda partida (dos mitades) cuando
   corresponde, sin alterar el layout de una celda normal.
2. **`docs/timetable/README.md`** y **`SPEC-TIMETABLE-001-v2.md`** (§8 y §11): documentan
   `half_block` con referencia a `ADR-TT-017-HALF-BLOCK.md`.
3. Este documento y el ADR quedan actualizados con las Fases ✅.

### Implementación (Fase 7)

- **Partial compartido** `resources/views/timetable/partials/half-cell.blade.php`: agrupa los
  slots de una celda por sub-franja (`half` `null` = bloque completo, `0` = superior, `1` =
  inferior), apila las dos mitades con rótulo «½ sup.» / «½ inf.» y un separador discontinuo, y
  recibe las líneas de cada asignatura mediante un `callable` `$lines` (así cada vista conserva
  su propia línea de metadatos: docente, sección, grupo, aula). Una celda normal se renderiza
  como antes (sin bandas de mitad). El `@empty` conserva su marcador configurable.
- **Vistas por modelo** (`TimetableSlot`): `timetable/public.blade.php` (enlace firmado por
  sección/docente/aula), `pdfs/timetable/section.blade.php`, `pdfs/timetable/teacher.blade.php`,
  `pdfs/timetable/room.blade.php`, `pdfs/timetable/teachers.blade.php`,
  `pdfs/timetable/teachers-all.blade.php`, `timetable/teachers-all.blade.php`,
  `livewire/timetable/role-view.blade.php` (estudiante/docente/leadership/dirección) y
  `livewire/profesor/timetable/my-timetable.blade.php`.
- **Vistas de vista previa** (arrays de `preview` con `half`): `pdfs/timetable/preview-section`,
  `preview-grade`, `preview-pestudio`, `preview-all-pestudios` y `preview-area` (esta última
  conserva año/sección/aula/grupo por línea y el envoltorio `.entry`).
- **Controlador** `TimetablePdfController`: la rama de **slots persistidos** (que hoy alimenta
  los PDFs de la vista previa y el formato por área) selecciona `half` y lo propaga a
  `$assignmentsByPeriod` en las cuatro construcciones (sección, grado, p.eStudio, área), de modo
  que el `half` llega a las plantillas de preview.

**Verificación Fase 7**: `tests/Feature/Timetable/HalfBlockReadOnlyTest.php` (6): vistas pública
firmada, PDF de sección, PDF de docente, preview de sección y preview de área parten la celda con
dos mitades; y una celda normal (bloque completo) **no** muestra bandas de mitad. Suite completa
`tests/Unit/Timetable` + `tests/Feature/Timetable` verde; `pint` limpio.

**Ajuste posterior (tab Clases de `/light`)**: el resumen del Paso 3 reutilizado en el modo rápido
ya no muestra el bloque «Dry-run» (el light persiste directo en `timetable_slots`). El partial
`step3-lessons-summary.blade.php` acepta `summaryMode` (`preview` para el wizard, `persisted` para
`/light`) y en modo persistido muestra celdas ocupadas, clases ubicadas/sin ubicar y hora del último
guardado; si la sección no tiene lecciones, muestra una guía en lugar de `0/0`. Para cruzar cada
Pevaluación con `preview['assignment']` (indexado por `timetable_lessons.id`), `loadLessons()` ahora
incluye `id` en `$lessons` y `saveLessons()` refresca el estado tras persistir. Cubierto por
`TimetableLightTest` (resumen con cifras reales y estado vacío).

---

## 5. Decisiones (estado)

- **Migración**: sin acción por ahora. Sigue en `database/migrations/` (top-level). Al cerrar
  la feature, archivar a `database/migrations/bck/timetable/` (convención del proyecto;
  los tests NO reconstruyen esquema). Requiere confirmación.
- **`.env`**: sin acción por ahora. Las claves `TIMETABLE_SOLVER_HALF_BLOCK_PRIORITY` /
  `_BONUS` son opcionales (defaults en config); fijarlas en producción requiere confirmación
  + `config:cache`.
- **Lecciones `half_block` + `locked`** — ✅ **resuelta (Fase 4, opción A)**: un bloqueo
  completo de una lección `half_block` ya no se representa con `lockedPeriodIds` (no
  distingue la mitad ni soporta dos mitades del mismo período). Se resuelve como
  *preasignación* (`preassignedSlots`, que sí conserva `half`), de modo que el solver fija
  período **y** mitad exactos. Ver `GenerateTimetableJob::runSolver()` (rama
  `$isLocked && $lesson->half_block`) y el test
  `HalfBlockPersistenceTest::test_locked_half_block_lesson_keeps_its_exact_half`.
- **Rama `/light`** — ✅ **resuelta**: la reestructuración de `/light` ya está commiteada
  (`TimetableLight` en Planning y Coordinación + `timetable-light.blade.php`). No hay WIP
  que coordinar; se trabaja sobre `main`.

---

## 6. Comandos de verificación

```bash
# Lint de sintaxis + formato (PHP 8.2)
php8.2 -l app/Services/Timetable/Solver/SchedulingContext.php
php8.2 vendor/bin/pint app/Services/Timetable app/Jobs/Timetable \
  app/Livewire/Coordinacion/Timetable app/Livewire/Planning/Timetable

# Tests del dominio
php8.2 artisan test tests/Unit/Timetable
php8.2 artisan test tests/Feature/Timetable

# Migración (ya aplicada)
php8.2 artisan migrate:status | grep half_block
```

> Nota de flakiness conocida: `TimetableGlobalPdfTest` falla de forma intermitente en
> corridas largas (colisión de unicidad de factorías contra la BD de producción); pasa
> en aislamiento y en la corrida completa. Es previo y ajeno a esta feature.

---

## 7. Riesgos y seguimiento

- **Suplentes**: `SubstituteService::candidateConflict()` considera a un docente ocupado
  todo el período (no distingue mitad). Es conservador (puede descartar un suplente válido
  si el docente solo tiene una mitad ocupada), no genera horarios inválidos. Fuera del
  alcance de Fase 6; candidato a refinamiento.
- **Solo-lectura / PDFs** — ✅ **resuelto (Fase 7)**: `TimetableViewService` ya alimenta las
  vistas de solo lectura con `half`, y tanto las vistas por modelo como los PDFs de vista previa
  renderizan la celda partida mediante `resources/views/timetable/partials/half-cell.blade.php`.
- **Sin datos en producción**: todavía no existen lecciones `half_block` publicadas, por lo
  que cualquier brecha residual de publicación es inofensiva hoy; al publicar el primer horario
  con mitades, validar el render de las celdas partidas (vista pública + PDFs).
