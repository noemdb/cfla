# ADR-TT-017 — Mitad de bloque (`half_block`)

| | |
|---|---|
| **Estado** | Propuesta normativa (pendiente de aprobación) |
| **Versión** | 1.0 |
| **Autor** | Staff Engineer |
| **Fecha** | 2026-10-19 |
| **Documento base** | `SPEC-TIMETABLE-001-v2.md`, `SPEC-TIMETABLE-MEJORAS-001.md` (ADR-TT-IMP-007/008) |
| **Alcance** | Esquema, solver, validación manual, preview, persistencia, snapshot, capacidad, publicación, UI (wizard/light/editor), solo-lectura, PDFs, XLS y totalizaciones |
| **Relacionados** | `ADR-TT-012-08` (medio-grupos), `SPEC-TIMETABLE-SHARED-TEACHER-001.md` |

---

## 1. Contexto y problema

El bloque institucional equivale a **2 horas** (`INCONSISTENCIAS-PDF-HORARIO.md`). Una
asignatura con carga de **1 hora semanal** no puede llenar un bloque: hoy el sistema
la asigna igual a un período completo (overshoot) o la deja fuera.

Se necesita que ciertas asignaturas puedan ocupar **solo la mitad del tiempo** de un
bloque —la **mitad superior** o la **inferior**— de modo que dos clases distintas
puedan dictarse **una tras otra** dentro del mismo período (reparto secuencial del
tiempo), y que la mitad libre pueda quedar vacía o ser tomada por otra clase.

Esto es **ortogonal** al medio-grupo (`is_half_group`): el medio-grupo son **dos cosas
a la vez** (paralelo, mismo instante); la mitad de bloque son **dos cosas seguidas**
(secuencial). El requerimiento confirma que ambas marcas son **combinables** en la
misma lección.

## 2. Decisión de dominio

### 2.1 Sub-franja como unidad de ocupación

> **Sub-franja** = `(calendar_id, period_id, mitad)` con
> `mitad ∈ {0 = superior, 1 = inferior}`. Un período = **2 sub-franjas**.

- Una lección **normal** (`half_block = false`) ocupa las **2** sub-franjas de su período.
- Una lección **`half_block`** ocupa **1** sub-franja (0 o 1).

Los invariantes actuales (`slot_section_key`, `slot_teacher_key`, aula, `locked`)
se siguen aplicando, pero **por sub-franja** en lugar de por período. Esto convierte
la matriz 2D (paralelo × temporal) en la misma lógica de hoy evaluada por mitad.

### 2.2 Marca nueva

Se añade a `timetable_lessons`:

```text
half_block (boolean, default false)
```

- Combinable con `is_half_group` y con `allow_shared_teacher`.
- `false` por defecto: comportamiento actual (bloque completo).

### 2.3 Persistencia

`timetable_slots` incorpora:

```text
half (unsignedTinyInteger, nullable)  -- 0 = superior, 1 = inferior, NULL = bloque completo
```

**Decisión:** `half` nullable + invariantes reforzados en aplicación (mismo enfoque
que `max_subjects_per_period`, que ya es solo de app). Ventaja: las lecciones
normales conservan **1 fila** por período (`half = NULL`) y el *blast radius* sobre
snapshot/PDF/exports/import se mantiene bajo.

> **Limitación asumida:** MySQL no puede garantizar con un índice único la regla
> cruzada “bloque completo excluye mitades” ni “misma mitad colisiona” cuando el
> `slot_section_key` de medio-grupo es por-lección. Esas reglas se refuerzan en
> `ConflictValidator`, `SchedulingContext` y la grilla.

## 3. Invariantes

### 3.1 Duras (por sub-franja)

| ID | Regla | Dónde se hace cumplir |
|---|---|---|
| **H1** | Docente: máx. 1 “slot docente” por sub-franja; excepción `allow_shared_teacher` si **todas** las ocupantes la autorizan. | `SchedulingContext::isFree`, `ConflictValidator` |
| **H2** | Aula: un `room_id` no se repite en la misma sub-franja. | idem |
| **H3** | Sección/subgrupo: sección completa monopoliza la sub-franja; sub-grupo solo choca con su `grupo_estable_id`; medio-grupos comparten hasta `max_subjects_per_period`. | idem |
| **H4** | Una lección no repite la misma sub-franja. | solver + `uq_slot_lesson` |
| **H5** | **Cruzada:** si un período tiene un bloque completo, ninguna mitad puede ocupar ese período (y viceversa). | `ConflictValidator`, `SchedulingContext`, grilla (app-only) |

### 3.2 Blandas (solver, nunca garantía)

| ID | Regla |
|---|---|
| **B1** | Preferir juntar dos mitades en el mismo período (análogo a `half_group_bonus`). |
| **B2** | Preferir que las dos mitades de un período sean de asignaturas distintas. |
| **B3** | La mitad concreta (superior/inferior) es indiferente para el coste; la fija el solver o el usuario al mover. |

### 3.3 Sin cambios

- Turno: preferencia en `optimized`, dura en `legacy` y para `locked`.
- `locked`, `is_manual_override`, badges de colisión, undo.
- `is_half_group` y `allow_shared_teacher` conservan su semántica.

#### 3.3.1 `locked` en lecciones `half_block` (resuelto, Fase 4)

Un bloqueo **completo** de una lección `half_block` se materializa como
*preasignación* (`preassignedSlots`) y no vía `lockedPeriodIds`. Motivo:
`lockedPeriodIds` es una lista de períodos sin mitad, por lo que (a) no puede
expresar la mitad concreta de cada slot y (b) colapsa dos mitades del mismo
período en un solo período (`unique()`), desactivando el bloqueo. La
preasignación conserva `(periodId, half)` y el solver la reserva primero, de
modo que la mitad bloqueada se respeta y la otra mitad queda libre para otra
lección. Aplica por igual a estrategia `optimized` y `legacy`.

## 4. Demanda / sesiones

`weekly_blocks_t` / `weekly_blocks_p` expresan **número de unidades asignables**, y la
unidad cambia de tamaño según la marca:

```text
normal:      weekly_blocks = ceil(hour * 60 / periodMinutes)
half_block:  weekly_blocks = ceil(hour * 60 / (periodMinutes / 2))
```

`buildLesson()` (`TimetableWizard.php`) es el único punto de derivación; `hour_t_week`
/ `hour_p_week` no cambian. Una asignatura de 1 h entra como 1 mitad; una de 4 h como
6 mitades.

## 5. Modelo de datos y migración

Nueva migración en `database/migrations/` (se ejecuta con `artisan migrate` y luego se
**archiva** en `database/migrations/bck/timetable/`, como el resto del historial).

### 5.1 Columnas

```php
Schema::table('timetable_lessons', function (Blueprint $table): void {
    $table->boolean('half_block')->default(false)->after('allow_shared_teacher');
});

Schema::table('timetable_slots', function (Blueprint $table): void {
    $table->unsignedTinyInteger('half')->nullable()->after('locked');
});
```

### 5.2 Columnas generadas y claves únicas

Se reconstruyen las claves para incluir la mitad, de modo que **dos mitades
secuenciales del mismo período no colisionen en la misma sección/docente/aula**:

```sql
-- claves por dimensión, con sufijo de mitad
slot_section_key = CONCAT(<clave actual>, ':', IF(half IS NULL, 'F', CONCAT('B', half)))
slot_teacher_key = CONCAT(<clave actual>, ':', IF(half IS NULL, 'F', CONCAT('B', half)))

-- auxiliar no-nulo para índices sobre columnas nullable
slot_half_key    = IF(half IS NULL, 'F', CONCAT('B', half))

uq_slot_section  = (calendar_id, period_id, slot_section_key)
uq_slot_teacher  = (calendar_id, period_id, slot_teacher_key)
uq_slot_room     = (calendar_id, period_id, room_id, slot_half_key)   -- redefinido
uq_slot_lesson   = (calendar_id, period_id, lesson_id)                -- sin cambio
```

`uq_slot_lesson` no incluye mitad: el solver no coloca dos sub-franjas de la misma
lección en el mismo período.

### 5.3 Seguridad para datos existentes

- Todas las lecciones quedan con `half_block = false`.
- Todos los slots quedan con `half = NULL` (bloque completo).
- Cero cambios de comportamiento hasta que la UI permita marcar `half_block`.

## 6. Matriz de convivencia (una misma sub-franja)

| Ocupante A ↓ / B → | Completa | Medio-grupo | Sub-grupo |
|---|---|---|---|
| **Completa** | ✗ H3 | ✗ H3 | ✗ H3 |
| **Medio-grupo** | ✗ H3 | ✓ hasta `max_subjects_per_period` | ✓ (distinto grupo) |
| **Sub-grupo** | ✗ H3 | ✓ (distinto grupo) | ✓ solo mismo `grupo_estable_id` |

La dimensión temporal la resuelve el sufijo de mitad: evaluar la matriz **por
sub-franja** permite que la mitad superior y la inferior tengan ocupaciones distintas.

## 7. Solver

- `SlotCandidate` gana `?int $half = null`.
- `LessonToSchedule` gana `bool $halfBlock = false`; `constraintDegree()` suma peso
  cuando la marca está activa.
- `SchedulingContext`: `isFree/occupy/release` ganan `?int $half`; las claves internas
  pasan a `periodId:half:...`. Una lección completa se registra en `[0,1]`; una de
  mitad, en la suya.
- `buildDomain()`: para `half_block`, cada período disponible produce candidatos por
  mitad; para normal, un candidato por período (`half = null`).
- `combinationsOfSize()`: rechaza repetir la misma `(period, half)`.
- Orquestador: nuevo intento `S1b` “half-block-first”; `comboScore()` suma
  `half_block_bonus` al completar la segunda mitad de un período (B1).
- Config (`config/timetable.php`):

```php
'half_block_priority' => (bool) env('TIMETABLE_SOLVER_HALF_BLOCK_PRIORITY', true),
'half_block_bonus'    => (int)  env('TIMETABLE_SOLVER_HALF_BLOCK_BONUS', 15),
```

## 8. Validación, capacidad y publicación

- `ConflictValidator::validate(...)` gana `?int $half = null` y aplica H1–H5 por
  sub-franja.
- `TimetableCapacityAuditService`: mide en sub-franjas; capacidad = períodos × 2;
  `half_block` pesa 0.5 (1 sub-franja) como hoy pesa el medio-grupo.
- `TimetablePublicationReadinessService`: nueva verificación cruzada completo↔mitad
  (claves `periodId:half:` para docente/sección/aula; un `half = null` registra ambas
  mitades) y advertencia no bloqueante `half_group_isolated` calculada por sub-franja.
- `TimetableWizard::publish()`: el chequeo de colisiones contra slots preservados y
  `$internalPublicationConflict` ignoran dos mitades secuenciales distintas; el payload
  publicado (`$existingAssignment`, `sectionReorderSnapshot()`) conserva `half`.
- `SectionTimetableOptimizer` (Paso 5, por sección): estado por sub-franja
  (`halvesFor`/`halvesOverlap`, claves `periodId:half:...`), ocupación externa dura y
  ventanas horarias blandas (`periodHalfWindow`) por mitad; `half` se preserva al permutar
  períodos. `max_subjects_per_period` limita medio-grupos **por sub-franja**.

## 9. UI

- Cada celda se parte en 2 regiones cuando contiene `half_block`; el bloque completo
  ocupa ambas.
- **Drag & drop** (light, editor): la mitad se deduce por la posición Y del *drop*
  dentro de la celda (mitad superior / inferior).
- **Wizard** (sin DnD): el modal de agregar permite elegir la mitad.
- `previewSectionGrid()` emite `half` por asignación; los badges de colisión se
  evalúan por sub-franja.

## 10. Compatibilidad

- **Legacy import:** no marca `half_block`; slots con `half = NULL` (sin cambios).
- **Snapshot/backup:** incluir `half` (slot) y `half_block` (lección).
- **IA:** incluir la marca en el contexto; la IA no la cambia por sí sola.
- **Optimizador de sección:** tratar mitades como sub-franjas y respetar H5.

## 11. Pruebas de aceptación

### Esquema y modelo
- [x] La migración agrega `half_block` y `half` con los defaults definidos.
- [x] Lecciones/slots existentes quedan `false` / `NULL`.
- [x] Los índices permiten dos mitades del mismo período (sección/docente/aula) y
      siguen bloqueando la misma mitad.
- [x] `TimetableLesson`/`TimetableSlot` castean y llenan los campos nuevos.

### Motor (unit, sin Eloquent)
- [x] `half_block` coloca 1 sub-franja; normal 2.
- [x] H5: un bloque completo rechaza cualquier mitad en el mismo período.
- [x] H1/H2/H3 se evalúan por sub-franja (dos mitades secuenciales no chocan).
- [x] La excepción `allow_shared_teacher` sigue exigiendo autorización de todas.

### Solver
- [x] Coverage y soft score no empeoran; B1 agrupa mitades cuando es posible.
- [x] `locked` y turno conservan su política.

### Persistencia, UI, editor y publicación
- [x] `half` sobrevive solver → preview → persistencia → snapshot (`HalfBlockPersistenceTest`).
- [x] Toggle de `half_block` recalcula demanda; grillas con sub-franjas (`HalfBlockUiTest`).
- [x] El optimizador por sección evalúa por sub-franja y preserva `half`
      (`HalfBlockOptimizerPublicationTest`).
- [x] La publicación no marca dos mitades secuenciales como doble reserva, pero sí la
      misma sub-franja o un bloque completo (`HalfBlockOptimizerPublicationTest`,
      `TimetablePublicationTest`).
- [x] La auditoría de capacidad pesa `half_block` como 0.5
      (`TimetableCapacityAuditTest`).

### Solo-lectura, PDFs, XLS y totalizaciones (Fase 7)
- [x] La vista pública firmada, las vistas por rol y los PDFs (sección/docente/aula/
      docentes-todos y formatos de vista previa) parten la celda en dos sub-franjas
      («½ sup.» / «½ inf.») cuando hay mitades (`HalfBlockReadOnlyTest`).
- [x] Una celda normal (bloque completo, `half = NULL`) no altera su layout
      (`HalfBlockReadOnlyTest`).
- [x] Los XLS consolidados de todos los P.Estudios y todos los docentes replican las
      bandas «½ sup.» / «½ inf.» dentro de la celda y ajustan la altura de la fila
      (`TimetableGlobalPdfTest`).
- [x] Los resúmenes de profesores y la totalización de bloques por docente cuentan
      cada media franja como 0.5 bloque; dos mitades del mismo docente en una celda
      visual suman como máximo 1 bloque y las horas académicas siguen siendo
      `2 × bloques` (`TimetableGlobalPdfTest`).
- [x] `docs/timetable/README.md` y `SPEC-TIMETABLE-001-v2.md` (§8, §11) documentan
      `half_block` con referencia a este ADR.

### Regresión
- [x] `HalfGroupSolverTest`, `TimetableWizardTest`, `TimetableLightTest` verdes.
- [x] Snapshot/PDF/export sin cambios para lecciones normales.

## 12. Orden de implementación

1. **Fase 0** — este ADR + invariantes. ✅
2. **Fase 1** — migración (Fase 5) + modelos + factory. *(cambio sensible en BD)* ✅
3. **Fase 2** — núcleo de ocupación: `SlotCandidate`, `LessonToSchedule`,
   `SchedulingContext`, `ConflictValidator`, `buildLesson`. ✅
4. **Fase 3** — solver: `buildDomain`, `comboScore`, orquestador, config + tests. ✅
5. **Fase 4** — persistencia: `persistPreviewLessonSlotsToDatabase`,
   `TimetableLessonPersistenceService`, `syncPreviewPayloadFromSlots`,
   `GenerateTimetableJob::persist`, snapshot. ✅
6. **Fase 5** — UI: wizard + light (celdas partidas, drop por mitad, modal). ✅
7. **Fase 6** — editor + capacidad + publicación/readiness. ✅
8. **Fase 7** — solo-lectura, PDFs y docs (`docs/timetable/README.md`,
   `SPEC-TIMETABLE-001-v2.md` §11). ✅

## 13. Criterio de finalización

- `half_block` se persiste de forma segura y compatible.
- Las invariantes H1–H5 se cumplen en solver, validación manual y publicación.
- Dos mitades secuenciales conviven; un bloque completo bloquea ambas mitades.
- Existen pruebas automatizadas de esquema, motor, solver y regresión.
- La documentación funcional y el contrato v2 quedan actualizados con referencia a
  este ADR.

## 14. Estado de implementación

Avance al 2026-10-19 (Fases 0–7 completas; suite de Timetable en verde):

- **Migración** `2026_10_19_000001_add_half_block_to_timetable_lessons_and_slots`
  aplicada (`migrate --force`): `timetable_lessons.half_block` (bool, default 0),
  `timetable_slots.half` (tinyint unsigned, nullable), `slot_half_key CHAR(2)`,
  claves generadas y `uq_slot_room` ampliada. Datos existentes intactos
  (789 lecciones / 1618 slots verificados).
- **Modelos/factorías**: `TimetableLesson` (`half_block`), `TimetableSlot`
  (`half`, oculta `slot_half_key`), factorías actualizadas.
- **Motor**: `SchedulingContext` evalúa la ocupación por sub-franja
  (`half = null` ocupa/libera ambas mitades); se añadió `sectionHalfLoad(...)`
  para el pareo blando. `SlotCandidate` incorpora `?int $half`
  (`null` = bloque completo); `LessonToSchedule` incorpora `bool $halfBlock`.
- **Validación manual**: `ConflictValidator::validate(...)` acepta `?int $half`
  (filtro `half IS NULL OR half = ?` cuando el candidato es media franja).
- **Solver**: `buildDomain` genera un candidato por (período, mitad) para
  lecciones `halfBlock`; `comboScore`/`qualityScore` aplican el bonus de pareo
  (B1/B2) vía `halfBlockGroupingScore`; nuevo intento `S1b`
  (`ORDER_HALF_BLOCK_FIRST`) y claves `half_block_priority` / `half_block_bonus`.
  El playbook (`TimetableSolverPlaybook`) documenta S-6, la estrategia y los
  levers para mantener la suite anti-deriva.
- **Demanda**: `TimetableWizard::blocksForMinutes()` — una lección `half_block`
  cuenta sub-franjas (`hour*60 / (periodMinutes/2)`), duplicando las unidades.
- **Persistencia (Fase 4)**: `half` viaja por `persistPreviewLessonSlotsToDatabase`,
  `TimetableLessonPersistenceService`, `syncPreviewPayloadFromSlots` y el snapshot
  (`slotSectionKey`/`slotTeacherKey` con sufijo de mitad). El bloqueo de una lección
  `half_block` se resuelve como preasignación (§3.3.1).
- **UI (Fase 5)**: toggle `setLessonHalfBlock()` con recálculo de demanda; columna
  «Mitad ½» en el partial compartido `step3-lessons-table`; `previewSectionGrid` emite
  `half`/`half_block`; grillas del wizard y del light con sub-franjas apiladas y zónas de
  drop por mitad; `movePreviewLesson(..., ?int $half)` y `addPreviewLesson(..., ?int $half)`
  persisten la mitad y `detectCrossSectionCollision(..., ?int $half)` no marca colisión
  entre dos mitades distintas del mismo período. Tests: `HalfBlockUiTest` (6).
- **Editor/capacidad/publicación (Fase 6)**: `SectionTimetableOptimizer` evalúa la
  ocupación por sub-franja (nodos con `half`, claves `"periodId:half:..."` en
  `isFree/occupy/release`, `halvesOverlap()` en el grafo de conflictos, ocupación externa
  dura y ventanas horarias blandas con `periodHalfWindow()`), preservando `half` en la
  salida. `max_subjects_per_period` limita los medio-grupos **por sub-franja**.
  `TimetablePublicationReadinessService` indexa docente/sección/aula por `periodId:half:`
  y agrupa medio-grupos por sub-franja; `TimetableWizard::publish()` compara mitades en
  el chequeo contra slots preservados, en `$internalPublicationConflict` y preserva `half`
  en `$existingAssignment`/`sectionReorderSnapshot()`. Tests:
  `HalfBlockOptimizerPublicationTest` (8).

- **Solo-lectura, PDFs, XLS y docs (Fase 7)**: parcial compartido
  `resources/views/timetable/partials/half-cell.blade.php` que agrupa los slots de una celda
  por sub-franja y apila las mitades con rótulo «½ sup.» / «½ inf.»; una celda normal no
  cambia. Se aplica en la vista pública firmada (`timetable/public`), las vistas por rol
  (`TimetableRoleView` y `MyTimetable`), los PDFs por modelo (sección, docente, aula,
  docentes-todos), los PDFs de vista previa (sección, grado, p.eStudio, all-p.eStudios y
  área) y los consolidados XLS. `TimetablePdfController` propaga `half` desde la rama de
  slots persistidos a los formatos de vista previa y usa `xlsHalfAwareLines()` para las
  celdas XLS. Los resúmenes de profesores y `teacherBlockTotalsRows()` ponderan la media
  franja como 0.5 bloque (tope 1 por celda visual) y mantienen `horas = 2 × bloques`.
  `docs/timetable/README.md` y `SPEC-TIMETABLE-001-v2.md` (§8, §11) documentan `half_block`
  con referencia a este ADR. Tests: `HalfBlockReadOnlyTest` (11) y
  `TimetableGlobalPdfTest`.

Implementación completa. El detalle accionable y las decisiones abiertas (migración a
`bck/`, claves `.env` opcionales) viven en
`PLAN-TIMETABLE-HALF-BLOCK-001-PENDIENTE.md`.

