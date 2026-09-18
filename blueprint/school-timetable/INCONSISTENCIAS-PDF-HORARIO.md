# Inconsistencias pendientes — PDFs consolidados de horario

> **Estado**: 🟢 Conteo de bloques corregido y verificado · **Fecha**: 2026-09-18
> **Área**: `TimetablePdfController` + `TimetableViewService` (reportes de horario)
> **Contexto**: revisión de `/app/planning/timetable/pdf/teacher-block-totals`,
> `/pdf/all-pestudios` y `/pdf/all-teachers`.

## Ya corregido (2026-09-18)

| # | Hallazgo | Corrección |
|---|---|---|
| 1 | `teacher-block-totals` calculaba `hours = blocks * 2` | **No era bug**: la regla institucional es **2 horas por bloque**. Se mantiene. |
| 2 | `all-teachers` devolvía HTML aunque la ruta se llama `pdf.*` | Se mantiene el HTML imprimible (dompdf agota memoria con todos los docentes); se alinearon las etiquetas a «Consolidado de docentes» y «Generar» en `timetable-wizard.blade.php` y `timetable-light.blade.php`. |
| 3 | `all-pestudios` mezclaba `preview_payload` (cal 1/2/10) con slots persistidos (cal 4) en el mismo documento | `buildPestudioSchedules(..., forcePersistedSlots: true)` para el consolidado: fuente única desde los slots persistidos (`TimetablePdfController.php:442,595`). |
| 4 | `teacherBlockTotals()` no llamaba `raisePdfMemoryLimit()` | Agregado (`TimetablePdfController.php:752`). |
| 5 | `teacher-block-totals` contaba 1 bloque por **slot** e ignoraba participantes paralelos, inflando el total (FERNANDEZ: 30 en vez de 18) | Resuelto: **un bloque por celda visual** (P.Educativo, turno, día, orden) con docente canónico por Pevaluación. Total 601 → **579**, y ahora coincide 46/46 con la grilla de `all-teachers`. |
| 6 | Desfase de `allow_shared_teacher` entre lección y slot (27 slots) | Comando `timetable:repair-shared-teacher-drift` creado. Ver **I-1**. |

### Regla de conteo acordada (institucional)

> **Un docente atendiendo dos o más grupos/sub-grupos en el mismo bloque
> (turno + día + orden), o el mismo bloque en varios P.Estudios del mismo
> P.Educativo, se contabiliza administrativamente como 1 bloque.**

Implementación: `TimetablePdfController::teacherBlockTotalsRows()` cuenta celdas
visuales únicas por docente con la clave
`(P.Educativo, turno, día, orden de bloque)`, excluye recreos y aplica
`horas = bloques × 2`. Esta regla cubre tres casos que antes se contaban de más:

1. **Docente compartido** (`lesson.allow_shared_teacher`): varias secciones en
   el mismo bloque.
2. **Sub-grupos en paralelo** (`slot.is_half_group`): dos lecciones distintas en
   el mismo bloque (caso LUJANO: contaba 16, son 14).
3. **Mismo bloque en P.Estudios del mismo P.Educativo**: la grilla los fusiona
   (`mergePeducativoSchedules`), así que no deben sumar dos veces (caso
   FERNANDEZ: contaba 20, son 18).

Verificación contra la grilla real de `all-teachers`: **0 discrepancias en 46
docentes** (579 bloques antes y después de la sintetización).

---

## Inconsistencias de datos

### I-1 · Drift de `allow_shared_teacher` entre lección y slot

- **Estado**: 🟡 Detectado · comando de reparación disponible (no ejecutado en producción).
- **Qué pasa**: el flag está **duplicado** en `timetable_lessons` y
  `timetable_slots`. La **lección** es la fuente canónica (la usan el solver,
  `ConflictValidator`, `TimetablePublicationReadinessService`, `SectionTimetableOptimizer`
  y los reportes); el **slot** alimenta el índice único `uq_slot_teacher`
  (`calendar_id, period_id, slot_teacher_key`).
- **Evidencia** (lapso vigente): **27 slots** con `slot.allow_shared_teacher = 1`
  y `lesson.allow_shared_teacher = 0`. En **25 lecciones**. Ninguno en el sentido
  inverso. Ejemplos: slots `14948` (cal 10, per 368), `15689` (cal 10, per 356),
  `19217` (cal 10, per 358).
- **Origen**: el editor manual (`TimetableWizard::persistPreviewRows`, ~`:7499`)
  marca `allow_shared_teacher` en la **fila del slot** cuando detecta colisión de
  docente en el mismo período, pero no siempre actualiza el flag de la lección.
- **Por qué no se puede «bajar» el flag del slot**: al resetear a 0 se
  reconstruye `slot_teacher_key` como `T{profesor_id}` y colisionaría con
  `uq_slot_teacher` si ya existe otro slot normal del mismo docente en ese
  período. Verificado: **11 de 27** slots caerían en esa colisión.
- **Reparación segura**: elevar `lesson.allow_shared_teacher` a `true` cuando
  alguno de sus slots ya lo tiene, y propagar a todos los slots de la lección
  (solo **relaja** restricciones; nunca rompe `uq_slot_teacher`).
- **Comando**:
  ```bash
  php8.2 artisan timetable:repair-shared-teacher-drift --dry-run
  php8.2 artisan timetable:repair-shared-teacher-drift --calendar=2 --dry-run
  php8.2 artisan timetable:repair-shared-teacher-drift --force
  ```
  Implementado en `app/Console/Commands/TimetableRepairSharedTeacherDrift.php`.
- **Impacto en la totalización**: verificado que el total por docente **no
  cambia** con la reparación (583 antes y después), porque el reporte ya
  colapsa por `(calendario, período)` cuando el flag de la lección está en `1`
  o cuando el slot fue marcado. La reparación alinea la fuente canónica para el
  solver/validación, no el reporte.

---

## Pendientes

### P-2 · Hora de bloque arbitraria al fusionar P.Educativos

- **Ubicación**: `TimetableViewService.php:220-232` (`mergePeducativoSchedules`).
- **Qué pasa**: al fusionar los horarios de varios P.Estudios de un mismo
  P.Educativo, se conserva **el primero visto** para cada `order`/día
  (`if (! $existing->has($day))`). Si dos P.Estudios del mismo P.Educativo
  tienen horarios de inicio/fin distintos para el mismo bloque, la columna
  «Bloque» del reporte puede mostrar la hora de cualquiera de ellos de forma no
  determinista (depende del orden de iteración de calendarios).
- **Riesgo**: inconsistencia visual entre reportes/impresiones del mismo docente.
- **Corrección propuesta**: definir un criterio determinista (p. ej., conservar
  el rango más temprano o el de mayor duración; o mostrar el rango combinado), o
  al menos documentar que el bloque sigue el primer P.Estudio ordenado por
  `peducativo.order`/nombre.

### P-3 · `isPublishedSchedule` calculado pero no usado en `all-pestudios`

- **Ubicación**: `TimetablePdfController.php:444,556,603` y
  `resources/views/pdfs/timetable/preview-all-pestudios.blade.php`.
- **Qué pasa**: `previewAllPestudios()` calcula y pasa `isPublishedSchedule` a
  la vista, pero la vista **no lo referencia** (a diferencia de
  `preview-section`, `preview-grade`, `preview-pestudio`, `preview-area`, que sí
  lo usan para el badge «HORARIO PUBLICADO» / «BORRADOR — NO PUBLICADO»).
- **Riesgo**: el consolidado no advierte si mezcla calendarios en borrador
  publicados; código muerto.
- **Corrección propuesta**: usar el flag en la vista (badge por P.Estudio) o
  eliminarlo del `return` si se decide que el consolidado siempre es de activos.

### P-4 · Hora académica hardcodeada (`* 2`) y ausente del resto de reportes

- **Ubicación**: `TimetablePdfController.php` (`teacherBlockTotalsRows`) +
  `teacher-block-totals.blade.php`.
- **Qué pasa**: la regla «2 horas por bloque» es institucional y se asume fija.
  Si alguna vez un calendario usara otra equivalencia, el cálculo no se ajusta.
- **Decisión**: **no cambiar** (regla confirmada por el usuario). Solo se deja
  constancia de que el `2` es una constante institucional, no derivada de
  `period_minutes`.

---

## Verificación

- `teacher-block-totals` vs grilla de `all-teachers`: **0 discrepancias en 46
  docentes** (579 bloques). Comparación ejecutada con los servicios reales
  (`TimetableViewService::teacherPeducativoSchedulesForCalendars`) contra
  `teacherBlockTotalsRows()`.
- Tests de regresión agregados en `tests/Feature/Timetable/TimetableGlobalPdfTest.php`:
  - `test_teacher_block_totals_counts_one_per_visual_cell` — 3 secciones
    compartidas en el mismo bloque cuentan 1 (horas = 2).
  - `test_teacher_block_totals_counts_separate_periods_as_two` — dos bloques del
    mismo día cuentan 2; un recreo no cuenta.
- Suites relacionadas: `TimetableGlobalPdfTest` (8 passed), `TimetableAreaFormatTest`,
  `TimetablePublicationTest`, `TimetableRoleViewTest` → **28 passed** (2026-09-18).
- Fallos preexistentes (no relacionados): `TimetableWizardTest`
  (bulk rooms / import CSV) → 6 fallos que ya existían en HEAD.
