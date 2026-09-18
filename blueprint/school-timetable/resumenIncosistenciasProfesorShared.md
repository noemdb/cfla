# Resumen — Inconsistencias en totalización de bloques por docente

> **Fecha**: 2026-09-18 · **Área**: `teacher-block-totals` (PDF consolidado)
> **Regla institucional aplicada**: un docente atendiendo dos o más
> grupos/sub-grupos en el mismo bloque cuenta como **1 bloque**.
> **Documento técnico completo**: `INCONSISTENCIAS-PDF-HORARIO.md`

## Contexto

La totalización contaba **1 bloque por fila** de `timetable_slots`. Eso inflaba
el total cuando un docente aparece en varias filas para el **mismo bloque
horario** por tres mecanismos:

| Mecanismo | Campo | Qué representa |
|---|---|---|
| Docente compartido | `timetable_lessons.allow_shared_teacher` | El docente dicta varias secciones en el mismo bloque |
| Sub-grupos en paralelo | `timetable_slots.is_half_group` | Dos lecciones distintas en el mismo bloque |
| Varios P.Estudios del mismo P.Educativo | fusión `mergePeducativoSchedules` | La grilla fusiona los P.Estudios; no deben sumar doble |

Corrección (`TimetablePdfController::teacherBlockTotalsRows`): se cuenta **una
celda visual por docente** con la clave `(P.Educativo, turno, día, orden de
bloque)`, excluyendo recreos.

## Tabla resumen por docente

Datos del lapso vigente con los calendarios activos (1, 2, 4, 10).

**Significado de cada columna:**

- **Docente**: nombre completo del profesor (apellido, nombre) según la Pevaluación.
- **CI**: cédula de identidad del docente.
- **Slots**: filas de `timetable_slots` asignadas al docente (valor crudo, sin colapsar).
- **Mecanismo detectado**: por qué el conteo anterior inflaba sus bloques.
- **Antes**: bloques que reportaba el totalizador **antes** de la corrección (1 por slot).
- **Después**: bloques tras la corrección (1 por celda visual ocupada).
- **Delta**: diferencia `Después − Antes`; negativo = bloques que se dejaron de contar de más.

## Tabla resumen por docente
| Docente | CI | Slots | Mecanismo detectado | Antes | Después | Delta |
|---|---|---:|---|---:|---:|---:|
| FERNANDEZ YOVERA LENIN ALBERTO | 17700109 | 30 | Compartido (27) + 2 celdas en dos P.Estudios del mismo P.Educativo | **30** | **18** | −12 |
| GARRIDO MARIANGEL | 23573152 | 11 | Compartido (11) + sub-grupos (2), 5 bloques con >1 slot en la misma celda | **11** | **5** | −6 |
| SALAS GRATEROL RICHARD JOSE | 15483521 | 19 | Compartido (8), 2 celdas con 2 secciones cada una | **19** | **17** | −2 |
| LUJANO MEZA SOLEIL DE LOS ÁNGELES | 20319608 | 16 | Sub-grupos en paralelo (4 slots half-group) | **16** | **14** | −2 |
| **Total general** | — | **601** | — | **601** | **579** | **−20** |

Solo 4 de 46 docentes estaban afectados; los otros 42 ya coincidían.

### Detalle por docente

- **FERNANDEZ**: 4 celdas con 4 slots cada una (mismo bloque, varias secciones).
  Dos de esas celdas (día 2 y día 4, turno tarde) aparecen en los calendarios 1
  y 2, que son P.Estudios del **mismo P.Educativo** y la grilla fusiona.
- **GARRIDO**: 5 celdas visuales en total; 4 de ellas con 2–3 slots cada una
  (secciones 1, 9, 8, 2, 11, 12, 10, 3, 7, 5, 6 según el día).
- **SALAS GRATEROL**: 2 celdas con 2 secciones compartidas (días 1 y 5, turno tarde).
- **LUJANO**: 2 celdas con 2 sub-grupos en paralelo (días 1 y 3, turno
  mañana). No tiene bloques compartidos (`allow_shared_teacher = 0`): su
  corrección proviene exclusivamente de los sub-grupos half-group.

## Verificación

- Comparación `teacher-block-totals` vs grilla real de `all-teachers`
  (`TimetableViewService::teacherPeducativoSchedulesForCalendars`):
  **0 discrepancias en 46 docentes** (579 bloques).
- Tests de regresión: `tests/Feature/Timetable/TimetableGlobalPdfTest.php`
  - `test_teacher_block_totals_counts_one_per_visual_cell`
  - `test_teacher_block_totals_counts_separate_periods_as_two`
- Suites: `TimetableGlobalPdfTest`, `TimetableAreaFormatTest`,
  `TimetablePublicationTest`, `TimetableRoleViewTest` → **26 passed**.

## Pendiente relacionado

Desfase de `allow_shared_teacher` entre lección y slot (27 slots): comando
`php8.2 artisan timetable:repair-shared-teacher-drift` (dry-run primero).
**No afecta** el reporte actual, que ya colapsa por celda visual.
