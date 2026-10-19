# PLAN-TIMETABLE-HALF-BLOCK-001 — Pendiente (Fases 4–7)

## Continuación de la implementación de «Clase mitad de bloque»

| | |
|---|---|
| **Estado** | Fases 0–3 ✅ implementadas y con suite verde. Fases 4–7 **pendientes** (este doc). |
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

## 1. Fase 4 — Persistencia y snapshot (SIGUIENTE)

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

## 2. Fase 5 — UI (wizard + light)

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

---

## 3. Fase 6 — Editor, capacidad y publicación

1. **`app/Services/Timetable/SectionTimetableOptimizer.php`** (tiene su **propio**
   `isFree/occupy/release` en `$state`, ~L978/L1019/L1040): añadir la dimensión de mitad
   (`$state['halfBlock'][$periodId]`), con la misma semántica que `SchedulingContext`.
2. **Capacidad**: `max_subjects_per_period` (en `timetable_calendars`) sigue limitando
   medios grupos por celda; documentar cómo interactúa con mitades (una celda puede
   alojar 2 mitades distintas + medios grupos, según H1–H5).
3. **Readiness/publicación**: revisar los chequeos de unicidad de publicación que usan
   `slot_section_key`/`slot_teacher_key` (solo `TimetableCalendarSnapshotService`
   apareció en la búsqueda) y cualquier validación de "doble" para que contemple `half`.

**Verificación Fase 6**: test del optimizador por sección con dos mitades secuenciales y
un bloque completo; test de readiness/publicación con mitades.

---

## 4. Fase 7 — Solo-lectura, PDFs y docs

1. Vistas de solo lectura y PDFs: renderizar la celda partida (dos mitades) cuando
   corresponda; no romper el layout de una celda normal.
2. `docs/timetable/README.md` y `SPEC-TIMETABLE-001-v2.md` (§11): documentar `half_block`
   con referencia a `ADR-TT-017-HALF-BLOCK.md`.
3. Actualizar este documento/ADR al cerrar (marcar Fases ✅).

---

## 5. Decisiones abiertas

- **Migración**: sigue en `database/migrations/` (top-level) para desarrollo. Al cerrar
  la feature, archivar a `database/migrations/bck/timetable/` (convención del proyecto;
  los tests NO reconstruyen esquema). Decidir antes del cierre.
- **`.env`**: las claves `TIMETABLE_SOLVER_HALF_BLOCK_PRIORITY` / `_BONUS` son opcionales
  (defaults en config). Si se quieren fijar en producción, requiere confirmación.
- **Lecciones `half_block` + `locked`**: hoy el solver trata un bloque `locked` como
  completo (`half = null`). Falta decidir cómo fijar la mitad de un bloque bloqueado
  (p. ej. `lockedPeriodIds` con mitad, o columna aparte). Documentar en Fase 4/5.
- **Rama `/light`**: la reestructuración de `/light` está sin commitear; el auto-commit
  `wip <timestamp>` del repo la viene registrando. Coordinar el orden de commits con la
  UI de media franja.

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
