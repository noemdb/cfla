# Módulo Horario Escolar (Timetable) — Guía funcional y técnica

| | |
|---|---|
| **Documentación normativa** | `blueprint/school-timetable/SPEC-TIMETABLE-001-v2.md` (spec v2.1) |
| **Plan multi-calendario** | `blueprint/school-timetable/PLAN-TIMETABLE-002-VARIOS-HORARIOS-POR-LAPSO.md` |
| **Stack** | Laravel 10 · Livewire 3 · Alpine.js · MariaDB (`s2627`) · dompdf · Reverb |
| **Estado** | Implementado y probado (84 tests / 233 assertions en `php8.2 artisan test --filter=Timetable`) |

---

## 1. Qué hace

Genera y publica el **horario semanal (lunes a viernes)** de cada sección para un
lapso académico, con turnos **mañana (M)** y **tarde (T)**. La lección a programar
**es** una `Pevaluacion` existente (carga académica del módulo Planning): el
horario no duplica materias, secciones ni docentes.

Flujo de alto nivel:

```
Carga académica (pevaluacions)          ← Planning la define
        │
        ▼
Wizard 5 pasos (calendario → aulas → lecciones → disponibilidad → generar)
        │
        ▼
Solver CSP (backtracking) → dry-run → confirmar → calendario ACTIVE
        │
        ▼
Lecturas: docente / estudiante / leadership / dirección / enlace público / PDF
```

---

## 2. Accesos y rutas

| Rol | Ruta | Componente |
|---|---|---|
| Coordinación | `/app/coordinacion/timetable` | `Coordinacion\Timetable\TimetableWizard` |
| Planning | `/app/planning/timetable` | `Planning\Timetable\TimetableWizard` (subclase, cambia layout) |
| Editor | `/app/{módulo}/timetable/editor/{calendar?}` | `TimetableEditor` (gemelo Planning) |
| Suplencias | `/app/{módulo}/timetable/substitutes/{calendar?}` | `TimetableSubstitutes` (gemelo Planning) |
| Profesor | `/app/profesors/timetable` | `MyTimetable` (solo sus slots) |
| Profesor (suplencias) | `/app/profesors/timetable/substitutes` | `SubstituteInbox` (confirmar/rechazar) |
| Estudiante | `student/lms/timetable` | `Student\Lms\Timetable` (su sección) |
| Leadership | `app/leadership/timetable/{seccion?}` | `SectionGrid` (solo lectura) |
| Dirección | `app/director/timetable/{seccion?}` | `SectionGrid` (solo lectura) |
| PDF | `/app/{módulo}/timetable/pdf/{section|teacher|room}/{calendar}/{id}` | `TimetablePdfController` |
| Público | `/timetable/{section|teacher|room}/{calendar}/{id}` | `TimetablePublicController` (enlace firmado, sin login) |

Los componentes de Planning son **subclases delgadas** de los de Coordinación que
solo sobreescriben `getLayout()` (ADR-TT-006: `is_planner` comparte los mismos
permisos que `is_coordinacion`).

---

## 3. Roles y permisos

| Capacidad | `is_coordinacion` | `is_planner` | `is_leadership` | `is_director` | profesor | estudiante |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| CRUD calendario/aulas/lecciones/disponibilidad | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Generar / regenerar | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Editor manual (drag-and-drop) | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Suplencias (registrar/asignar) | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Suplencias (confirmar las propias) | — | — | — | — | ✅ | ❌ |
| Ver cualquier sección | ✅ | ✅ | ✅ RO | ✅ RO | ❌ | ❌ |
| Ver su propio horario | — | — | — | — | ✅ | ✅ |
| Publicar / exportar / enlace firmado | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## 4. Uso del wizard (paso a paso)

Ruta: `/app/planning/timetable` (o `/app/coordinacion/timetable`).

### Paso 1 · Calendario

1. Elegir el **lapso**. Se listan las **alternativas (calendarios)** del lapso:
   N borradores + máximo **uno activo** (ADR-TT-014). Acciones por calendario:
   **Continuar**, **Activar** (solo si ya tiene slots), **Eliminar** (solo `draft`).
2. **Nuevo borrador**: nombre (único dentro del lapso, D-3) y `period_minutes`
   (duración del bloque: 30–120 min).
3. **Turnos**: el catálogo `timetable_shifts` empieza **vacío** — se crean con el
   formulario "Nuevo turno" (code `M`/`T`, nombre, hora inicio/fin). El select de
   "Turno y períodos" solo muestra turnos ya creados.
4. **Períodos**: botón *Generar períodos* crea 5 días (Lun–Vie) × 6 bloques por
   turno, calculando `start_time`/`end_time` desde `shift.start_time` +
   `period_minutes`. Los períodos `is_break` (recreos) nunca reciben slots.

> Si ya existe un calendario cargado, `mount()` abre el activo del lapso vigente
> (`activeForCurrentLapso()`), con fallback al último `draft|active`.

### Paso 2 · Aulas

- **Alta individual**: código único, nombre, capacidad, tipo
  (`aula|laboratorio|patio|cancha|taller|salon`).
- **Carga masiva** (`bulkCreateRooms`): crea automáticamente **un aula por cada
  grado/sección activa** de los pestudios activos, nombrada
  "aula/salon/ambiente {Grado} {Sección}" y con `seccion_id` asociado. Omite
  secciones que ya tienen aula (regla "uno y solo un aula por sección").

### Paso 3 · Lecciones

- Se marcan las **pevaluaciones del lapso** (checkbox); cada una derivará sus
  bloques: `weekly_blocks_t = ceil(hour_t_week × 60 / period_minutes)` y
  `weekly_blocks_p = ceil(hour_p_week × 60 / period_minutes)` (ADR-TT-004: la
  asignatura manda por defecto, editable).
- Por lección: turno, `room_type_required` (solo aplica a bloques prácticos,
  ADR-TT-010), prioridad (mayor = más restrictivo), `locked`.
- En la estrategia **optimized**, el turno configurado es la preferencia:
  primero se intentan sus períodos y, si las restricciones dejan la lección sin
  capacidad, se prueban los demás turnos del calendario. Las lecciones
  `locked` conservan exclusivamente su turno configurado. El turno efectivo de
  una asignación se determina por el período elegido.
- Sub-grupos: si la `Pevaluacion` tiene `grupo_estable_id`, dos sub-grupos de la
  misma sección pueden dictarse **en paralelo** en el mismo período (cada uno con
  su profesor); la sección completa (`grupo_estable_id = NULL`) ocupa el período
  completo para sí.
- **Importación CSV/Excel** (ticket 001g): botón *Importar* + plantilla descargable
  (`plantilla-lecciones.csv`, columnas: `pevaluacion_id,turno,bloques_t,bloques_p,aula,prioridad`).
  El turno acepta id numérico o código `M`/`T`; filas duplicadas/inválidas fallan
  limpio sin persistir nada. Requiere `maatwebsite/excel` (instalado).

### Paso 4 · Disponibilidad

- Grilla día × período por docente del calendario. Botón *Todo disponible*
  (`setAllAvailable`) como preset.
- Una fila en `timetable_teacher_availability` con `is_available=false` es regla
  **dura** para el solver.

### Paso 5 · Generar

1. **Estrategia de generación**:
   - **Optimizado** (predeterminado): ejecuta el solver y puede reacomodar
     lecciones de medio grupo para encontrar una combinación viable.
   - **Legacy**: reproduce directamente los `timetable_slots` importados,
     conservando día, franja, grupo y aula. Si el calendario no tiene slots
     importados, usa el solver como fallback y marca `assignment_source=solver`
     en el preview.
2. **Previsualizar (dry-run)**: ejecuta la estrategia seleccionada sin escribir
   `timetable_slots`; el resultado se serializa en
   `timetable_calendars.preview_payload`. En Legacy, el origen queda indicado
   como `assignment_source=legacy_slots`. La importación legacy crea también
   las lecciones de todas las `Pevaluacion` del pestudio aunque no tengan un
   slot legacy mapeado; esas lecciones quedan en `unassigned` para poder
   agregarlas manualmente desde una celda vacía.
   Una lección `locked` solo se considera fijada si sus slots cubren exactamente
   todos sus bloques T/P requeridos. En estrategia optimizada, una fijación
   parcial se libera para que el solver la recalcule; en Legacy se conserva la
   posición importada y se reporta como incompleta.
3. **Confirmar y publicar**: persiste slots, demueve al activo anterior del lapso
   a `archived` y activa este (invariante DB `uq_active_lapso`).
4. En la grilla del preview, cada lección asignada se puede arrastrar a otro
   período de clase del mismo turno. El movimiento valida docente y sección,
   excluye recreos y conserva el cambio al confirmar la publicación.
5. Con lecciones sin asignar queda en `draft` con conflictos `type='unassigned'`
   para resolver a mano en el editor.

Para auditar un calendario sin modificar datos:

```bash
php8.2 artisan timetable:diagnose --calendar=4
php8.2 artisan timetable:diagnose --calendar=4 --json
```

El diagnóstico informa bloques requeridos/asignados/faltantes, fijaciones
parciales, duplicados y slots inválidos.

---

## 5. El solver (motor de asignación)

`App\Services\Timetable\Solver\*` — PHP puro, sin Eloquent (DTOs), testeable
aislado. Lo invoca `GenerateTimetableJob` (cola `database`, `dispatchSync` desde
el wizard).

**Reglas duras (nunca se violan):**

- Un docente no puede estar en dos slots del mismo período (`uq_slot_teacher`).
- Una sección (o sub-grupo) no puede duplicarse en el mismo período
  (`uq_slot_section` sobre columna generada `slot_section_key`).
- Un aula no puede alojar dos lecciones en el mismo período (`uq_slot_room`;
  `room_id=NULL` no colisiona — bloques teóricos sin aula dedicada).
- El período debe ser del turno de la lección (`shift_mismatch`).
- Respetar la disponibilidad marcada del docente.

**Restricciones soft (heurísticas de `comboScore`, desempate):**

- Distribuir bloques de una misma lección en días distintos/no consecutivos.
- Bloques teóricos en períodos tempranos.
- `quality_score` = % de lecciones asignadas; se persiste en el calendario.

**Comportamiento clave:**

- Orden por grado de restricción (ADR-TT-003): más bloques → mayor prioridad →
  requiere aula especial primero.
- Lecciones `locked` se reservan primero y nunca se reasignan (ADR-TT-007).
- Corte por tiempo (default 30s): conserva la **solución parcial**, el resto pasa
  a `unassigned` (ADR-TT-009). Pool adaptativo de combinaciones con presupuesto
  de nodos (ADR-TT-011) para lecciones de muchos bloques.

---

## 6. Estados del calendario

```
draft ──(Generar)──▶ generating ──(solver ok)──▶ active  (demueve al activo anterior → archived)
                       │
                       └─(dry-run o conflictos)──▶ draft (preview_payload / conflictos unassigned)
active ──(cierre de lapso / otra alternativa)──▶ archived (terminal, solo lectura)
```

- Máximo UNO `active` por lapso — garantizado **en BD** con columna generada
  `active_lapso_key` + índice único (NULLs no colisionan).
- `version` (bloqueo optimista, §15): cada escritura lo incrementa; un job o
  editor con versión desactualizada es rechazado.
- Eliminar solo `draft` (`deleteDraft()`); archivados conservan historial.

---

## 7. Editor manual y suplencias

- **Editor** (`/timetable/editor/{calendar}`): grilla Lu–Vie × períodos por
  sección o docente. Drag-and-drop con Alpine (HTML5 nativo, sin librería). Al
  soltar, `ConflictValidator` valida las reglas duras en vivo; si falla muestra
  el conflicto inline y no persiste. Mueve slots (`moveSlot`), coloca lecciones
  no asignadas (`dropLesson`), elimina (`removeSlot`), fija/libera (`locked`).
- **Suplencias** (`TimetableSubstitutes`): registrar ausencia (docente, rango de
  fechas, motivo) → muestra slots afectados por `day_of_week` → sugiere
  candidatos → asigna suplente (`pending`) → `NotifySubstituteJob` notifica. El
  suplente confirma/rechaza desde `SubstituteInbox`; nunca auto-confirmado
  (ADR-TT-012).

---

## 8. Publicación y vistas

- **Enlace firmado**: `URL::temporarySignedRoute('timetable.public.*', now()->addDays(7))`
  — lectura sin login; expira según TTL.
- **PDF**: por sección/docente/aula (`Barryvdh\DomPDF`, vistas `pdfs/timetable/*`).
- **Lecturas por rol** resuelven el activo del lapso vigente con
  `TimetableCalendar::activeForCurrentLapso()` (fallback: último activo, D-2):
  - Estudiante: su sección vía `Inscripcion` (sin inscripción → 404).
  - Profesor: solo sus slots; además recibe `shareUrl` firmada.
  - Leadership/Director: cualquier sección, solo lectura (ADR-TT-013).

---

## 9. Notificaciones

| Evento | Mecanismo |
|---|---|
| Horario publicado con cambios | `GenerateTimetableJob` calcula el **diff real** (`computeDiff`: lecciones movidas, removidas, docentes afectados) y encola `NotifyTimetableChangesJob`; solo se notifica a docentes con cambios; coordinación recibe resumen. Regeneración idéntica → diff vacío → no notifica. |
| Suplencia asignada | `NotifySubstituteJob` → `SubstituteAssignedNotification` (database + broadcast `NotificationReceived` vía Reverb). |
| Cambios de horario | `TimetableChangedNotification` vía `NotificationService::notifyUsers()` (canal database). |

---

## 10. Modelo de datos

```
timetable_shifts            catálogo global M/T (code único); se garantiza con
                            TimetableShiftsSeeder (M 07:00–12:30 · T 13:00–15:00)
timetable_calendars         N por lapso · UNO POR PESTUDIO (pestudio_id, FK);
                            status; version; quality_score; preview_payload;
                            active_lapso_key (generada) + uq_active_lapso
timetable_periods           por calendario+turno+día (L–V × orden, is_break);
                            heredan el pestudio del calendario (sin columna)
timetable_rooms             catálogo global; code único; seccion_id opcional (aula por sección)
timetable_lessons           1:1 con pevaluacion por calendario; bloques_t/p; room_type_required;
                            priority; locked; shift_id
timetable_teacher_availability  por (calendario, profesor, turno, día, bloque)
                            con start_time/end_time del bloque — rejilla de 60 min
                            desde el seeder (M 6 bloques 07:00–13:00, T 2 13:00–15:00)
timetable_slots             resultado: lesson+period (+profesor/seccion/grupo_estable desnormalizados,
                            room_id nullable); únicos (calendar, period, {teacher|section_key|room});
                            is_manual_override; locked
timetable_conflicts         auditoría: teacher/room/section_double_booked,
                            availability_violation, shift_mismatch, unassigned
                            (lesson_id/period_id nullable para unassigned)
timetable_absences          ausencias de docentes por calendario (fecha inicio/fin)
timetable_substitute_assignments  suplente por slot; pending|confirmed|declined
```

Reglas de integridad (ADR-TT-002): validación en aplicación (`ConflictValidator`)
+ índices únicos en BD. Los `room_id` NULL no colisionan en MySQL/MariaDB.

### 10.1 Cambios 2026-09-08 (ajustes de arquitectura)

| Cambio | Migración |
|---|---|
| `pestudio_id` en `timetable_calendars` (un calendario = un plan) | `2026_09_08_000004` |
| Disponibilidad por turno·día·bloque (reemplaza period_id) | `2026_09_08_000003` |
| Índices de soporte (idx_period_cal, idx_avail_cal) | incluidos arriba |

**Comandos de producción** (`docs` en cada comando):
```bash
php8.2 artisan migrate --force                              # tablas del módulo
php8.2 artisan db:seed --class=TimetableShiftsSeeder --force  # turnos M/T (idempotente)
php8.2 artisan timetable:normalize-legacy-hours --lapso=1 --dry-run
php8.2 artisan timetable:normalize-legacy-hours --lapso=1 --force # fuente normativa de horas
# timetable:backfill-horas queda deprecado y solo se conserva por compatibilidad.
php8.2 artisan timetable:import-legacy --lapso=1              # importa y deja el calendario optimizado por defecto
php8.2 artisan timetable:import-legacy --lapso=1 --strategy=legacy # conserva explícitamente la estrategia legacy
php8.2 artisan timetable:create-section-rooms --dry-run     # audita un aula por sección
php8.2 artisan timetable:create-section-rooms               # crea aulas activas por sección
```
La ruta de los CSVs legacy es configurable: `TIMETABLE_LEGACY_CSV_DIR` (default
`blueprint/school-timetable/legacy/csv`).

### 10.2 Publicación, recuperación y observabilidad

- El Step 5 revalida el preview en servidor antes de publicar.
- Los conflictos duros bloquean la publicación; las advertencias se muestran
  separadamente.
- `Deshacer último cambio` restaura la última modificación manual registrada.
- `Restaurar dry-run` recupera la asignación generada antes de editar el preview.
- Las publicaciones y cambios manuales se registran en
  `timetable_calendar_versions` y `timetable_change_logs`.
- Las ejecuciones del solver escriben `correlation_id`, estrategia, calendario,
  duración, asignadas y no asignadas en el canal `timetable`.

---

## 11. Dónde está cada cosa

| Pieza | Archivo |
|---|---|
| Wizard (5 pasos) | `app/Livewire/Coordinacion/Timetable/TimetableWizard.php` |
| Wizard Planning | `app/Livewire/Planning/Timetable/TimetableWizard.php` |
| Editor manual | `app/Livewire/Coordinacion/Timetable/TimetableEditor.php` (+ gemelo Planning) |
| Suplencias | `app/Livewire/Coordinacion/Timetable/TimetableSubstitutes.php` (+ gemelo Planning) |
| Bandeja suplente | `app/Livewire/Profesor/Timetable/SubstituteInbox.php` |
| Vistas por rol | `app/Livewire/Timetable/TimetableRoleView.php` + `MyTimetable`, `Student\Lms\Timetable`, `SectionGrid` ×2 |
| Solver | `app/Services/Timetable/Solver/{TimetableSolver,SchedulingContext,LessonToSchedule,SlotCandidate,SolverResult}.php` |
| Validador de conflictos | `app/Services/Timetable/ConflictValidator.php` |
| Generación de grillas | `app/Services/Timetable/TimetableViewService.php` |
| Sugerencia de suplentes | `app/Services/Timetable/SubstituteService.php` |
| Job de generación | `app/Jobs/Timetable/GenerateTimetableJob.php` |
| Jobs de notificación | `app/Jobs/Timetable/{NotifyTimetableChangesJob,NotifySubstituteJob}.php` |
| Modelos | `app/Models/app/Timetable/Timetable*.php` |
| Migraciones | `database/migrations/2026_09_07_*` y `2026_09_08_*` (ajustes); base en `database/migrations/bck/timetable/` |
| Import CSV | `app/Imports/TimetableLessonsImport.php` |
| Comando backfill de horas | `app/Console/Commands/TimetableBackfillHoras.php` |
| Comando import legacy | `app/Console/Commands/TimetableImportLegacy.php` |
| Seeder de turnos | `database/seeders/TimetableShiftsSeeder.php` |
| Config del módulo | `config/timetable.php` (`legacy_csv_dir` vía `TIMETABLE_LEGACY_CSV_DIR`) |
| PDFs | `app/Http/Controllers/Timetable/TimetablePdfController.php`, vistas `resources/views/pdfs/timetable/` |
| PDF del preview | `TimetablePdfController::previewSection()` · ruta `timetable.pdf.preview` |
| Vistas públicas | `app/Http/Controllers/Timetable/TimetablePublicController.php`, `resources/views/timetable/public.blade.php` |
| Rutas | `routes/web.php` (`timetable.public.*` firmadas; grupos coordinacion/planning/profesors/student/leadership/director) |
| Menú navbar | `config/menus.php` (grupo *Herramientas* → *Herramientas* de Planning) |
| Seeder de prueba | `database/seeders/TimetableTestSeeder.php` (seed fijo 20260818, reproducible) |

---

## 12. Testing y operación

```bash
# Suite completa del módulo (usa la BD real con DatabaseTransactions, NO borra datos)
php8.2 artisan config:clear
php8.2 artisan test --filter=Timetable

# Tests unitarios del solver (sin Eloquent)
php8.2 artisan test --testsuite=unit --filter=TimetableSolver

# Seeder de dataset sintético (QA manual; reproducible)
php8.2 artisan db:seed --class=TimetableTestSeeder

# Estilo
./vendor/bin/pint app/Jobs/Timetable app/Services/Timetable app/Livewire/Coordinacion/Timetable app/Livewire/Planning/Timetable

# Log del canal timetable (correlation_id = calendar_id-timestamp)
tail -f storage/logs/laravel.log | grep timetable

# Importación legacy (opción 3): un CALENDARIO POR PESTUDIO
php8.2 artisan timetable:import-legacy --lapso=1 --dry-run
php8.2 artisan timetable:import-legacy --lapso=1
```

Cobertura: solver (factible/infactible/locked/timeout/sub-grupos/pool adaptativo),
ConflictValidator por regla, wizard end-to-end, editor con bloqueo optimista,
multi-calendario (democión, índice DB, dry-run), publicación (enlace firmado,
vistas por rol), notificaciones (diff vacío no notifica), suplencias,
disponibilidad por turno·día·bloque (prefill desde lecciones) y duplicación
de borradores.

## 13. FAQ operativa

- **El select "Turno y períodos" está vacío** — el catálogo `timetable_shifts`
  está vacío: corre `php8.2 artisan db:seed --class=TimetableShiftsSeeder --force`.
- **"Los períodos de este turno ya están generados"** — usa el botón
  **Regenerar** del paso 1 (recrea los períodos del turno desde la estructura
  del pestudio).
- **"El borrador no tiene horario generado"** — *Activar* exige slots: corre el
  dry-run y confirma la publicación primero.
- **Lecciones sin asignar** — quedan como conflictos `unassigned` visibles en el
  editor; resuélvelas a mano (drag) o ajusta disponibilidad/aulas y regenera.
- **"Otro usuario modificó este horario"** — bloqueo optimista (§15): recarga la
  página para tomar la `version` vigente.
- **T=0/P=0 en lecciones** — la asignatura no tiene `hour_t_week/hour_p_week`;
  corre `php8.2 artisan timetable:backfill-horas` (mapa normalizado por plan).
- **El Paso 3 solo muestra un plan** — es el comportamiento correcto: cada
  calendario es de UN pestudio y el paso filtra las pevaluaciones a ese plan.
- **Exportar el preview** — en el paso 5, con la vista previa lista, botón
  «Exportar PDF» sobre la pestaña de la sección activa (ruta `timetable.pdf.preview`).
