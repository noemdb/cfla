# SPEC-TIMETABLE-MEJORAS-001: Evolución del módulo de horarios

| | |
|---|---|
| **Estado** | Draft — backlog priorizado + deuda técnica verificada (v1.1) |
| **Versión** | 1.1 |
| **Fecha** | 2026-09-10 (rev. v1.1) |
| **Módulo** | `app/Livewire/Coordinacion/Timetable` |
| **Superficie principal** | `/app/planning/timetable` |
| **Documento base** | `blueprint/school-timetable/SPEC-TIMETABLE-001.md` |
| **Documentos relacionados** | `SPEC-TIMETABLE-001-v2.md`, `PLAN-TIMETABLE-002-VARIOS-HORARIOS-POR-LAPSO.md`, `PLAN-TIMETABLE-003-NORMALIZE-HORAS.md`, `commandProduction.md`, `docs/timetable/README.md` |
| **Fuente de línea base** | Implementación vigente del wizard, solver optimizado/legacy, preview manual, publicación, PDF, paridad, medio grupo, calendarios por pestudio, disponibilidad por turno·día·bloque y confirmaciones |
| **Nota de contexto (2026-09-08/09)** | La arquitectura cambió respecto a v1.0: calendario **por pestudio**, períodos desde la estructura legacy, disponibilidad **por turno·día·bloque**, `max_subjects_per_period`, `strategy` (optimized/legacy), `is_half_group`, aulas **1:N** por sección y comandos de normalización/diagnóstico. Ver §2.2. |

## 1. Propósito

Definir la siguiente etapa de evolución del módulo de horarios sin alterar las
reglas ya estabilizadas. La prioridad pasa de completar el flujo funcional a
mejorar:

1. La calidad y explicabilidad del horario generado.
2. La seguridad de publicación y recuperación.
3. La trazabilidad de cambios manuales.
4. El rendimiento y la mantenibilidad del código.
5. La cobertura de reglas de negocio y escenarios límite.

Esta spec no autoriza por sí sola cambios destructivos de base de datos ni
cambios globales en `ConflictValidator`. Cada iniciativa debe implementarse,
probarse y documentarse mediante los IDs definidos aquí.

## 2. Línea base funcional

La implementación actual ya incluye:

- Wizard de configuración y generación.
- Carga de lessons desde `Pevaluacion`.
- Bloques teóricos y prácticos.
- Aulas filtradas por `pestudio` y aulas sin asociación.
- Replicación entre secciones del mismo grado.
- Estrategia `optimized` con fallback entre turnos.
- Estrategia `legacy`.
- Preview editable en Step 5.
- Movimientos e intercambios entre turnos con warning no bloqueante.
- Validación de conflictos de docente, sección, aula y período.
- Retiro de lessons del preview con confirmación WireUI.
- Equidad de slots entre secciones activas.
- Comparativa por sección y asignatura.
- Distinción visual para medio grupo.
- Publicación, archivado y exportación PDF.
- **Calendario por pestudio** (`pestudio_id`): un calendario = un plan de estudio.
- **Períodos desde la estructura legacy** por nivel del pestudio (tiempos exactos + recreos `is_break`).
- **Disponibilidad del docente por turno · día · bloque** (rejilla de 60 min desde el seeder de turnos), con prellenado desde sus lecciones y copia entre docentes.
- **`max_subjects_per_period`**: tope de asignaturas (paralelos/medio grupo) por período.
- **`strategy`**: `optimized` (CSP) y `legacy` (respeta la asignación fija del horario legacy).
- **`is_half_group`**: medio grupo con `slot_section_key` que permite compartir período sin colisión.
- **Aulas 1:N** por sección (`TimetableRoomEligibilityService` filtra por pestudio).
- **Persistencia de lecciones no destructiva** (`TimetableLessonPersistenceService::updateOrCreate`).
- **Comandos de operación**: `import-legacy --replace`, `backfill-horas`, `normalize-legacy-hours`, `create-section-rooms`, `diagnose`, `check-cross-booking`.
- **PDF del preview** por sección (`timetable.pdf.preview`) y **checklist pre-publicación** (`publishChecklist`).
- **Seeder de turnos** (`TimetableShiftsSeeder`, idempotente) y `config/timetable.php` (`TIMETABLE_LEGACY_CSV_DIR`).

### 2.1 Invariantes que deben preservarse

| ID | Invariante | Evidencia |
|---|---|---|
| INV-TT-001 | Una `TimetableLesson` representa una `Pevaluacion` del calendario | Modelo y generación |
| INV-TT-002 | Las horas semanales provienen de `hour_t_week`/`hour_p_week` salvo edición explícita | Step 3 y sincronización |
| INV-TT-003 | Solo secciones activas participan en la comparación de equidad | `sectionSlotParity()` |
| INV-TT-004 | Retirar del preview no elimina la lesson persistida | `removePreviewLesson()` |
| INV-TT-005 | El intercambio entre turnos puede advertir, pero no bloquear por `shift_mismatch` | Edición manual Step 5 |
| INV-TT-006 | Las restricciones duras de docente, sección, aula y período siguen bloqueando | Validación de edición |
| INV-TT-007 | Las lessons `locked` no deben cambiar de turno durante generación optimizada | `GenerateTimetableJob` |
| INV-TT-008 | Un calendario publicado conserva una representación consultable para PDF y lectores | `TimetableSlot` |
| INV-TT-009 | Un calendario pertenece a un único pestudio; sus períodos y aulas son de ese plan | `pestudio_id`, `TimetableRoomEligibilityService` |
| INV-TT-010 | La disponibilidad del docente es por turno·día·bloque, no por período del pestudio | `TimetableTeacherAvailability` |
| INV-TT-011 | Un período admite a lo sumo `max_subjects_per_period` asignaturas en paralelo | `SchedulingContext::isFree`, `ConflictValidator` |
| INV-TT-012 | Los medio-grupos comparten período solo entre sí y nunca con una sección completa | `slot_section_key` (`:H`), `ConflictValidator` |
| INV-TT-013 | `TimetableLessonPersistenceService::persist` no elimina lecciones fuera del scope de la edición | `updateOrCreate` |
| INV-TT-014 | Las lecciones `locked` conservan su asignación (estrategia `legacy`/regeneración) | `GenerateTimetableJob` |

### 2.2 Deuda técnica y hallazgos verificados (2026-09-10)

Hallazgos comprobados contra el código y la BD vigentes; alimentan las
iniciativas IMP-TT-011..015.

| ID | Hallazgo | Evidencia |
|---|---|---|
| H-01 | **Duplicidad de comandos de horas**: `timetable:backfill-horas` (mapa embebido) y `timetable:normalize-legacy-hours` (lee `legacy_carga_docentes.csv`) persiguen lo mismo con fuentes distintas | `app/Console/Commands/Timetable{BackfillHoras,NormalizeLegacyHours}.php` |
| H-02 | **Migración con prefijo duplicado** `2026_09_08_000001_*` (dos archivos) | `database/migrations/` |
| H-03 | **SPEC normativa desactualizada**: `SPEC-TIMETABLE-001-v2.md` tiene **0 menciones** de `is_half_group`, `max_subjects_per_period`, `STRATEGY_LEGACY` y `allow_multiple_rooms` | `grep` sobre la SPEC |
| H-04 | **Comandos sin pruebas**: los 6 comandos del módulo (`import-legacy`, `backfill-horas`, `normalize-legacy-hours`, `create-section-rooms`, `diagnose`, `check-cross-booking`) tienen **0 tests** | `grep` en `tests/` |
| H-05 | **Desalineación disponibilidad ↔ horas reales**: la rejilla es de 60 min desde el seeder, pero los bloques reales son 07:00–08:20, 08:35–09:55…; el solver cruza por `order_in_day` | `availabilityGrid()`, `GenerateTimetableJob::buildAvailablePeriods` |
| H-06 | **`TimetableWizard` de 4 027 líneas y 81 métodos públicos** | `wc -l` + `grep` |
| H-07 | **Inconsistencia de `hour_t/p_week`** (P=T en INICIAL y MEDIA C&T; 59/1 en 4TO) detectada por el plan de normalización | `PLAN-TIMETABLE-003-NORMALIZE-HORAS.md` |
| H-08 | **Error 1553 de migración** (`DROP INDEX` respaldando FK) resuelto en `000003` soltando la FK antes; sin prueba de instalación limpia | `2026_09_08_000003_*` |
| H-09 | **Estrategia `legacy` poco ejercitada**: regenerar un calendario importado con `optimized` deja casi todo sin asignar | dry-run observado sobre calendarios legacy |
| H-10 | Acoplamiento de reglas entre wizard y `ConflictValidator` (riesgo de divergencia) | IMP-TT-003 |

## 3. Priorización de iniciativas

| ID | Iniciativa | Prioridad | Resultado esperado |
|---|---|---:|---|
| IMP-TT-001 | Publicación segura y resumen de calidad | P0 | No publicar sin conocer el estado real del horario |
| IMP-TT-002 | Auditoría, versionado y recuperación | P0 | Reconstruir quién cambió qué y recuperar versiones |
| IMP-TT-003 | Descomposición de `TimetableWizard` | P0 | Reducir acoplamiento y riesgo de regresiones |
| IMP-TT-004 | Modelo tipado del preview | P1 | Sustituir arrays frágiles por contratos explícitos |
| IMP-TT-005 | Reglas avanzadas de calidad y equidad | P1 | Comparar carga esperada contra carga asignada |
| IMP-TT-006 | Rendimiento de Step 5 y consultas | P1 | Mantener tiempos aceptables con calendarios grandes |
| IMP-TT-007 | Historial y deshacer cambios manuales | P1 | Operación reversible y segura |
| IMP-TT-008 | Métrica de calidad del horario | P2 | Comparar alternativas con una puntuación explicable |
| IMP-TT-009 | Pruebas de propiedades del solver | P1 | Garantizar invariantes independientemente del dataset |
| IMP-TT-010 | Documentación operativa y observabilidad | P2 | Facilitar soporte y uso por coordinación |
| IMP-TT-011 | Consolidación de deuda técnica y SPEC | P0 | Un solo camino fiable para las horas y docs sin contradicciones |
| IMP-TT-012 | Alineación disponibilidad ↔ bloques reales | P1 | Disponibilidad y solver exactos por hora real |
| IMP-TT-013 | Pruebas de comandos y migración limpia | P1 | Despliegue reproducible y comandos verificados |
| IMP-TT-014 | Editor manual con reglas nuevas | P1 | Edición segura con medio grupo, paralelos y aulas 1:N |
| IMP-TT-015 | ADRs y runbook de producción | P2 | Decisiones nuevas documentadas y operables |

## 4. Requisitos funcionales

### IMP-TT-001 — Publicación segura

#### Objetivo

Convertir la publicación en una operación explícita, explicable y validada.

#### Requisitos

- **REQ-TT-001-01:** Mostrar antes de publicar un resumen del preview.
- **REQ-TT-001-02:** Separar contadores de asignadas, sin asignar, conflictos
  bloqueantes, warnings, secciones incompletas y cambios manuales.
- **REQ-TT-001-03:** Deshabilitar publicación únicamente por conflictos definidos
  como bloqueantes.
- **REQ-TT-001-04:** Mostrar una confirmación diferenciada cuando existan warnings.
- **REQ-TT-001-05:** Indicar si alguna lesson fue ubicada en un turno alternativo.
- **REQ-TT-001-06:** Registrar el resultado de la validación que precede a la
  publicación.
- **REQ-TT-001-07:** Revalidar el preview en servidor inmediatamente antes de
  persistir; nunca confiar solo en el estado renderizado de Livewire.

#### Criterios de aceptación

- **ACC-TT-001-01:** Con conflictos bloqueantes, el botón de publicación no
  ejecuta `confirmAndPublish()`.
- **ACC-TT-001-02:** Con warnings y sin conflictos bloqueantes, coordinación puede
  confirmar explícitamente la publicación.
- **ACC-TT-001-03:** El resumen coincide con los datos persistidos después de
  publicar.
- **ACC-TT-001-04:** Una modificación concurrente invalida el preview y obliga a
  regenerar o actualizarlo.

### IMP-TT-002 — Auditoría y versionado

#### Objetivo

Conservar la trazabilidad de publicaciones y modificaciones operativas.

#### Requisitos

- **REQ-TT-002-01:** Identificar cada publicación con versión, usuario, fecha,
  calendario y motivo opcional.
- **REQ-TT-002-02:** Registrar movimientos manuales, intercambios, cambios de
  turno, retiros y adiciones de lessons.
- **REQ-TT-002-03:** Guardar el calendario/version anterior relacionado cuando
  una publicación archiva otra versión.
- **REQ-TT-002-04:** Permitir consultar un resumen de diferencias entre dos
  versiones.
- **REQ-TT-002-05:** No guardar payloads completos duplicados si un registro de
  evento puede referenciar la versión y el cambio estructurado.

#### Entidades propuestas

```text
timetable_calendar_versions
  id, calendar_id, version, status, published_by, published_at,
  quality_score, summary_json, created_at

timetable_change_logs
  id, calendar_id, version_id, user_id, action, lesson_id,
  before_json, after_json, metadata_json, created_at
```

#### Criterios de aceptación

- **ACC-TT-002-01:** Se puede identificar el usuario y la hora de cada
  publicación.
- **ACC-TT-002-02:** Un retiro del preview aparece como evento, pero no como
  eliminación de la lesson.
- **ACC-TT-002-03:** Una versión archivada permanece consultable en modo lectura.
- **ACC-TT-002-04:** Los registros de auditoría no se modifican al regenerar un
  preview posterior.

### IMP-TT-003 — Descomposición del componente

#### Objetivo

Reducir la responsabilidad de `TimetableWizard` manteniendo el contrato público
de sus métodos Livewire.

#### Servicios propuestos

| Servicio | Responsabilidad |
|---|---|
| `TimetablePreviewService` | Construcción, normalización y rehidratación del preview |
| `TimetableAssignmentService` | Movimiento, intercambio, adición y retiro |
| `TimetableConflictService` | Diagnóstico y clasificación de conflictos |
| `TimetableParityService` | Equidad por sección y asignatura |
| `TimetableReplicationService` | Replicación de carga entre secciones |
| `TimetablePublicationService` | Validación, snapshot y publicación |

#### Criterios de aceptación

- **ACC-TT-003-01:** Las acciones existentes de la vista mantienen el mismo
  comportamiento observable.
- **ACC-TT-003-02:** La lógica de dominio puede probarse sin montar Livewire.
- **ACC-TT-003-03:** El componente conserva solo coordinación de estado,
  autorización y notificaciones.
- **ACC-TT-003-04:** No se duplican reglas de conflicto entre wizard y servicios.

### IMP-TT-004 — Contrato tipado del preview

#### Objetivo

Reducir el uso de arrays anidados con claves implícitas.

#### Requisitos

- **REQ-TT-004-01:** Definir objetos para `TimetablePreview`,
  `TimetableAssignment`, `TimetableLessonPlacement` y `TimetableConflict`.
- **REQ-TT-004-02:** Normalizar entradas antiguas antes de hidratarlas.
- **REQ-TT-004-03:** Versionar la estructura serializada del preview.
- **REQ-TT-004-04:** Rechazar payloads incompletos con un error visible y
  recuperable.

### IMP-TT-005 — Calidad y equidad

#### Objetivo

Pasar de contar slots a medir si la carga académica quedó correctamente
distribuida.

#### Requisitos

- **REQ-TT-005-01:** Comparar por asignatura los bloques esperados y asignados.
- **REQ-TT-005-02:** Separar bloques teóricos y prácticos.
- **REQ-TT-005-03:** Mostrar diferencia absoluta y déficit por sección.
- **REQ-TT-005-04:** Excluir secciones inactivas y lessons fuera del calendario.
- **REQ-TT-005-05:** Marcar como warning una diferencia configurable y como
  bloqueante una diferencia que deje carga obligatoria sin asignar.
- **REQ-TT-005-06:** Mostrar la causa probable y la acción sugerida.

#### Indicadores mínimos

```text
slots esperados
slots asignados
déficit teórico
déficit práctico
diferencia máxima entre secciones
porcentaje de cobertura
```

### IMP-TT-006 — Rendimiento

#### Requisitos

- **REQ-TT-006-01:** Evitar N+1 al construir la grilla y los diagnósticos.
- **REQ-TT-006-02:** Indexar en memoria lessons por `lesson_id`, períodos,
  secciones, profesores y pensum.
- **REQ-TT-006-03:** No recalcular paridad completa cuando cambia solo una celda,
  salvo que el cambio afecte su grado.
- **REQ-TT-006-04:** Medir consultas y tiempo de render para datasets pequeños,
  medianos y grandes.
- **REQ-TT-006-05:** Mantener filtros de Step 5 sin serializar datos no visibles
  cuando sea seguro hacerlo.

#### Umbrales iniciales

| Escenario | Umbral objetivo |
|---|---:|
| Preview pequeño, hasta 100 lessons | Render servidor < 1.5 s |
| Preview mediano, 101–300 lessons | Render servidor < 3 s |
| Diagnóstico completo, hasta 300 lessons | < 4 s |
| Movimiento de una lesson | < 1.5 s |

Los umbrales deben medirse en el entorno de prueba del proyecto y documentar
hardware, volumen y cantidad de consultas.

### IMP-TT-007 — Historial y deshacer

- **REQ-TT-007-01:** Mostrar que existen cambios manuales pendientes.
- **REQ-TT-007-02:** Permitir deshacer el último cambio.
- **REQ-TT-007-03:** Permitir restaurar el preview al último snapshot generado.
- **REQ-TT-007-04:** Pedir confirmación si se perderán cambios manuales.
- **REQ-TT-007-05:** El deshacer no debe modificar lessons persistidas hasta
  publicar o guardar explícitamente.

### IMP-TT-008 — Puntuación de calidad

La puntuación debe ser explicable, no una caja negra.

```text
quality_score =
  cobertura de lessons
  - conflictos bloqueantes
  - warnings ponderados
  - penalización por déficit de paridad
  - penalización por cambios de turno
  - penalización por huecos
```

- **REQ-TT-008-01:** Mostrar desglose por factor.
- **REQ-TT-008-02:** Permitir comparar dos previews o versiones.
- **REQ-TT-008-03:** No usar la puntuación como sustituto de una regla dura.

### IMP-TT-009 — Pruebas del solver

Agregar pruebas de propiedades además de fixtures concretos:

- **PROP-TT-009-01:** Una lesson nunca ocupa un recreo.
- **PROP-TT-009-02:** Un docente no tiene dos lessons en el mismo período.
- **PROP-TT-009-03:** Una sección no tiene dos lessons en el mismo período.
- **PROP-TT-009-04:** Un aula no se duplica en el mismo período.
- **PROP-TT-009-05:** Una lesson asignada siempre referencia un período válido.
- **PROP-TT-009-06:** Una lesson bloqueada conserva su turno.
- **PROP-TT-009-07:** Las lessons no asignadas aparecen en el diagnóstico.
- **PROP-TT-009-08:** El fallback de turno respeta primero el turno preferido.

### IMP-TT-010 — Documentación y observabilidad

- **REQ-TT-010-01:** Documentar el flujo operativo para coordinación.
- **REQ-TT-010-02:** Documentar cada tipo de warning y conflicto bloqueante.
- **REQ-TT-010-03:** Registrar duración, estrategia, calendario, cantidad de
  lessons, asignadas y no asignadas en cada generación.
- **REQ-TT-010-04:** Usar un `correlation_id` para enlazar generación,
  diagnóstico, publicación y errores.
- **REQ-TT-010-05:** No registrar datos personales innecesarios en logs.

### IMP-TT-011 — Consolidación de deuda técnica y SPEC

#### Objetivo

Eliminar fuentes de verdad contradictorias y alinear la documentación normativa con el código.

#### Requisitos

- **REQ-TT-011-01:** Unificar la normalización de horas en **un solo comando** (recomendado: el que deriva del legacy), deprecando el otro con aviso.
- **REQ-TT-011-02:** Renombrar la migración con prefijo duplicado para que el orden sea unívoco.
- **REQ-TT-011-03:** Actualizar `SPEC-TIMETABLE-001-v2.md` con `is_half_group`, `max_subjects_per_period`, `strategy`, aulas 1:N y calendario por pestudio.
- **REQ-TT-011-04:** Documentar en la SPEC la disponibilidad por turno·día·bloque y la rejilla del seeder.
- **REQ-TT-011-05:** Declarar el estado de `backfill-horas` (vigente/deprecado) en `commandProduction.md`.

#### Criterios de aceptación

- **ACC-TT-011-01:** Existe un único comando recomendado para normalizar horas y el otro emite un aviso de deprecación.
- **ACC-TT-011-02:** Ningún par de migraciones comparte timestamp.
- **ACC-TT-011-03:** La SPEC menciona los cinco conceptos arquitectónicos nuevos.

### IMP-TT-012 — Alineación disponibilidad ↔ bloques reales

#### Objetivo

Que la disponibilidad y el solver se crucen por **hora real**, no por un índice de orden que puede no coincidir con el bloque legacy.

#### Requisitos

- **REQ-TT-012-01:** Definir la disponibilidad del docente contra los bloques reales del período (o mapear por hora de inicio).
- **REQ-TT-012-02:** El solver debe verificar solape por intervalo horario (`[start,end)`) y día, no solo por `order_in_day`.
- **REQ-TT-012-03:** Migrar los registros de disponibilidad existentes sin pérdida (o regenerarlos).
- **REQ-TT-012-04:** Cubrir con pruebas el caso de un bloque legacy que cruza dos bloques de 60 min.

### IMP-TT-013 — Pruebas de comandos y migración limpia

#### Requisitos

- **REQ-TT-013-01:** Test de cada comando (`import-legacy`, `backfill-horas`/`normalize`, `create-section-rooms`, `diagnose`, `check-cross-booking`) con BD real y `DatabaseTransactions`.
- **REQ-TT-013-02:** Test de `migrate` desde cero (o sobre esquema base) que verifique que no surja el error 1553.
- **REQ-TT-013-03:** Verificar idempotencia de `--replace` y del seeder de turnos.
- **REQ-TT-013-04:** Test de la estrategia `legacy`: un calendario importado regenera respetando su asignación fija.

### IMP-TT-014 — Editor manual con reglas nuevas

#### Requisitos

- **REQ-TT-014-01:** Validación en vivo con `is_half_group` y `max_subjects_per_period`.
- **REQ-TT-014-02:** Manejo de aulas 1:N (mover a una de las aulas de la sección).
- **REQ-TT-014-03:** Feedback visual de conflictos que distingan bloqueante de warning.
- **REQ-TT-014-04:** Reutilizar exclusivamente `ConflictValidator` (sin duplicar reglas).

### IMP-TT-015 — ADRs y runbook de producción

#### Requisitos

- **REQ-TT-015-01:** ADRs para medio grupo, estrategia, `max_subjects_per_period`, pestudio-por-calendario y aulas 1:N.
- **REQ-TT-015-02:** Runbook: orden de comandos (seeder → normalize horas → import), backups y rollback seguro.
- **REQ-TT-015-03:** Checklist de despliegue con verificación de migraciones y datos.

## 5. Fases de implementación

| Fase | Entregable | IDs | Verificación |
|---|---|---|---|
| F0 | Línea base y métricas | — | Suite Timetable + consultas baseline |
| F1 | Resumen y validación de publicación | IMP-TT-001 | Tests de publicación y warnings |
| F2 | Auditoría y versiones | IMP-TT-002 | Tests de snapshot, historial y archivado |
| F3 | Servicios de dominio | IMP-TT-003, IMP-TT-004 | Tests unitarios + regresión Livewire |
| F4 | Equidad avanzada | IMP-TT-005, IMP-TT-008 | Fixtures con déficit T/P y secciones activas |
| F5 | Rendimiento | IMP-TT-006 | Perfil de consultas y benchmarks |
| F6 | Deshacer y recuperación | IMP-TT-007 | Tests de reversión y confirmación |
| F7 | Propiedades del solver | IMP-TT-009 | Dataset sintético y generador de casos |
| F8 | Documentación y observabilidad | IMP-TT-010 | Revisión técnica y operativa |
| F9 | Consolidación y docs | IMP-TT-011, IMP-TT-015 | SPEC actualizada, comandos de horas unificados y ADRs |
| F10 | Disponibilidad real, comandos y editor | IMP-TT-012, IMP-TT-013, IMP-TT-014 | Tests de comandos + migración limpia + editor con reglas nuevas |

Una fase no se considera completada hasta que sus criterios de aceptación estén
marcados con evidencia en el registro de cambios.

## 6. Matriz de trazabilidad

| Requisito | Implementación prevista | Prueba mínima | Estado |
|---|---|---|---|
| REQ-TT-001-01..07 | `TimetablePublicationService`, Step 5 | `TimetablePublicationTest` | Pendiente |
| REQ-TT-002-01..05 | Migraciones, auditoría y vista de versiones | `TimetableAuditTest` | Pendiente |
| REQ-TT-003-01..04 | Servicios extraídos de `TimetableWizard` | Tests unitarios + wizard | Pendiente |
| REQ-TT-004-01..04 | DTOs/serializador de preview | `TimetablePreviewContractTest` | Pendiente |
| REQ-TT-005-01..06 | `TimetableParityService` | `TimetableParityTest` | Pendiente |
| REQ-TT-006-01..05 | Índices y memoización del preview | Benchmark/consulta | Pendiente |
| REQ-TT-007-01..05 | `undoLastPreviewChange()`, `restoreGeneratedPreview()` y logs de preview | `TimetableUndoTest` | Implementación inicial |
| REQ-TT-008-01..03 | `TimetableQualityScore` | `TimetableQualityScoreTest` | Pendiente |
| PROP-TT-009-01..08 | Solver y validadores | `TimetableSolverPropertyTest` | Pendiente |
| REQ-TT-010-01..05 | Logs de generación, readiness y `correlation_id` | Smoke test + revisión | Implementación inicial |
| REQ-TT-011-01..05 | Deprecación del comando duplicado y runbook | Revisión + `grep` | Implementación inicial |
| REQ-TT-012-01..04 | `TimetableAvailabilityService` por intervalo real | `TimetableAvailabilityTimeTest` | Implementación inicial |
| REQ-TT-013-01..04 | Tests de comandos y migración limpia | `TimetableCommandsTest`, `TimetableMigrationsTest` | Pendiente |
| REQ-TT-014-01..04 | Editor con medio grupo/paralelos/aulas 1:N | `TimetableEditorTest` (ampliado) | Pendiente |
| REQ-TT-015-01..03 | `commandProduction.md` y registro de decisiones | Revisión operativa | Implementación inicial |

## 7. Convención de tickets y commits

Cada trabajo debe usar el identificador de iniciativa y requisito:

```text
TT-IMP-001: agregar resumen previo a publicación
TT-IMP-001 / REQ-TT-001-07
```

Formato recomendado de commit:

```text
feat(timetable): add publication readiness summary [TT-IMP-001]
```

Formato recomendado para pruebas:

```text
test_timetable_publication_blocks_on_hard_conflicts
test_timetable_publication_allows_confirmed_warnings
```

Cada PR o cambio debe indicar:

- IDs de requisitos cubiertos.
- Archivos modificados.
- Migraciones nuevas o descartadas.
- Evidencia de pruebas.
- Riesgos y compatibilidad.
- Requisitos aún pendientes.

## 8. Registro de decisiones

### ADR-TT-IMP-001 — Publicación revalidada en servidor

**Decisión:** la publicación siempre vuelve a validar el preview dentro de la
transacción, aunque la UI ya lo haya validado.

**Motivo:** Livewire puede transportar un estado obsoleto y las reglas duras no
deben depender del cliente.

**Consecuencia:** existe una consulta/costo adicional, compensado por
integridad operativa.

### ADR-TT-IMP-002 — Warnings no sustituyen restricciones duras

**Decisión:** el cambio de turno continúa siendo warning en la edición manual,
pero docente, sección, aula, período inválido y recreo siguen siendo bloqueantes.

**Motivo:** el intercambio entre turnos es una operación institucional válida,
pero no debe ocultar colisiones reales.

### ADR-TT-IMP-003 — Auditoría estructurada sobre snapshots indiscriminados

**Decisión:** guardar eventos de cambio estructurados y snapshots de versión,
evitando duplicar todo el payload en cada interacción.

**Motivo:** mantiene trazabilidad y controla el crecimiento de almacenamiento.

### ADR-TT-IMP-004 — Compatibilidad incremental

**Decisión:** extraer servicios sin cambiar inicialmente los nombres de métodos
Livewire ni el contrato visible de la vista.

**Motivo:** reduce riesgo en un componente que ya tiene múltiples flujos
operativos y pruebas de regresión.

### ADR-TT-IMP-005 — Calendario por pestudio

**Decisión:** cada `timetable_calendar` pertenece a un `pestudio`; sus períodos
provienen de la estructura del nivel de ese plan.

**Motivo:** los bloques horarios difieren por nivel (MEDIA vs PRIMARIA vs
INICIAL); un calendario único multi-nivel generaba períodos solapados.

### ADR-TT-IMP-006 — Disponibilidad por turno · día · bloque

**Decisión:** la disponibilidad del docente se registra por turno + día + bloque
(rejilla del seeder), independiente del pestudio.

**Motivo:** un docente puede dictar en varios planes; la disponibilidad debe ser
una sola rejilla coherente (ver IMP-TT-012 para el cruce por hora real).

### ADR-TT-IMP-007 — Medio grupo vía clave de sección

**Decisión:** dos medio-grupos pueden compartir período mediante
`slot_section_key = S{seccion}:H{lesson}`, sin chocar con la sección completa.

**Motivo:** soportar desdobles (p. ej. Educación Física H/V, ITP) sin relajar la
regla de "sección no doble".

### ADR-TT-IMP-008 — Paralelos acotados por `max_subjects_per_period`

**Decisión:** un período admite a lo sumo N asignaturas en paralelo
(configurable por calendario).

**Motivo:** permite paralelos legítimos y evita sobre-saturación de una sección.

### ADR-TT-IMP-009 — Persistencia de lecciones no destructiva

**Decisión:** guardar lecciones con `updateOrCreate` por scope; nunca borrar
todas las del calendario al guardar el Paso 3.

**Motivo:** corregir el defecto que vaciaba lecciones al editar una selección
parcial.

## 9. No alcance de esta spec

- Reemplazar el solver por un servicio externo.
- Crear catálogos paralelos de materias, docentes o secciones.
- Cambiar globalmente la semántica de `ConflictValidator`.
- Eliminar datos históricos o calendarios archivados.
- Introducir una aplicación móvil.
- Publicar automáticamente un horario con conflictos bloqueantes.

## 10. Registro de cambios del documento

| Fecha | Versión | Cambio | Responsable |
|---|---|---|---|
| 2026-09-10 | 1.0 | Creación de spec de mejoras generales con trazabilidad | Equipo CFLA |

## 11. Registro de implementación

| Fase | Estado | Evidencia | Pendiente |
|---|---|---|---|
| F0 | Completada | Suite `tests/Feature/Timetable`: 150 pruebas, 449 assertions | Mantener baseline en cada fase |
| F1 | Completada inicial | `TimetablePublicationReadinessService`, resumen Step 5 y revalidación server-side | Pruebas específicas de conflictos duros y warnings confirmables |
| F2 | Completada inicial | Migración `2026_09_10_000001_create_timetable_audit_tables.php`, modelos de versiones/logs y eventos de publicación/preview | Vista de historial y snapshots completos por publicación |
| F3 | Completada inicial | Servicio reutilizable de readiness y disponibilidad; wrappers conservados en Livewire | Extraer assignment/conflict/parity y DTO versionado del preview |
| F4 | Completada inicial | Cobertura y quality score explicable incorporados al readiness | Déficit separado T/P y comparación avanzada por asignatura |
| F5 | Completada inicial | Eager loading, batch insert y caché de disponibilidad por scope | Benchmarks reproducibles e índices adicionales |
| F6 | Completada inicial | `undoLastPreviewChange()` y `restoreGeneratedPreview()` en Step 5; botones de recuperación | Ampliar cobertura con tests de cada tipo de cambio |
| F7 | Completada inicial | Solver conserva asignaciones parciales, respeta bloqueos, recreos y fallback de turno; regresión existente verde | Añadir generador/property tests dedicado |
| F8 | Completada inicial | Logs de generación con `correlation_id`, eventos de preview/publicación y resumen de readiness | Runbook operativo y métricas persistidas por ejecución |
| F9 | Completada inicial | `backfill-horas` emite deprecación; `normalize-legacy-hours` queda recomendado; comandos de producción actualizados | Terminar consolidación de SPEC v2 y ADRs formales |
| F10 | Completada inicial | Disponibilidad validada por intervalo real con fallback legacy por `order_in_day`; editor reutiliza `ConflictValidator` | Tests específicos de solapamiento y comandos/migración limpia |

### 11.1 Convención de evidencia

Cada cambio de implementación debe añadir una fila a esta sección o actualizar
la fila de su fase con:

- archivos modificados;
- prueba ejecutada;
- cantidad de pruebas/assertions;
- migraciones aplicadas;
- requisitos `REQ-TT-*` cubiertos;
- pendientes y riesgos conocidos.
| 2026-09-10 | 1.1 | Complemento: arquitectura vigente (calendario por pestudio, disponibilidad por turno, medio grupo, `max_subjects_per_period`, `strategy`, aulas 1:N), §2.2 de deuda verificada (H-01..H-10), iniciativas IMP-TT-011..015, fases F9–F10, trazabilidad ampliada y ADRs TT-IMP-005..009 | Equipo CFLA |
