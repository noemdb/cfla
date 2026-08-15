# SPEC-TIMETABLE-001: Horario Escolar Semanal — Lunes a Viernes, Turnos Mañana/Tarde (CFLA)

| | |
|---|---|
| **Estado** | Draft — listo para descomponer en tickets |
| **Stack** | Laravel 10 · Livewire 3 · Alpine.js · Tailwind 3 · MariaDB (db `s2627`, driver `mysql`) |
| **Autor** | Staff Engineer spec para agente de código |
| **Punto de partida** | `blueprint/school-timetable/specDrive01.md` (spec semilla de dominio genérico) |
| **Supersede** | `specDrive01.md` como referencia de implementación (se conserva como registro de inspiración de dominio) |
| **Módulos relacionados** | `Pevaluacion`/`Carga Académica` (Planning), `Coordinacion`, `Planning` (asignaturas/pensum), `Estudiant`/`Inscripcion`, notificaciones (`NotificationService`), PDF (`dompdf`) |

> **Diferencia clave frente a `specDrive01.md`**: aquel propone tablas genéricas
> `timetable_subjects`/`timetable_sections` duplicando la fuente de verdad. Este
> spec **no duplica catálogos**: la lección a programar **es** una `Pevaluacion`
> existente (profesor + sección + área del pensum + lapso), las horas semanales
> **son** `Asignatura.hour_t_week`/`hour_p_week`, y la estructura
> Pestudio→Grado→Seccion→Estudiant ya existe. Se añade solo lo que falta:
> turnos (mañana/tarde), períodos, aulas, disponibilidad, resultado y ausencias.

---

## 1. Objetivo y alcance

Generar y publicar el **horario semanal (lunes a viernes)** de cada sección para
un lapso académico, con **dos turnos**: **mañana** y **tarde**. Cada sección se
ubica en un turno y sus períodos se asignan a docentes y aulas sin conflictos.

| # | Capacidad |
|---|---|
| 1 | Definir estructura del calendario: días (L–V), turnos mañana/tarde, períodos (hora de inicio/fin, duración), recreos |
| 2 | Configurar aulas y disponibilidad de docentes (grilla por período/día) |
| 3 | Tomar las **lecciones a programar desde `pevaluacions`** (sin duplicar materias/secciones/docentes) |
| 4 | Generar automáticamente el horario libre de conflictos (motor CSP + backtracking) |
| 5 | Editar manualmente el resultado con validación de conflictos en tiempo real |
| 6 | Gestionar ausencias de docentes y suplentes con recálculo |
| 7 | Publicar/exportar por sección, docente y aula (PDF + vista pública por enlace firmado) |
| 8 | Notificar cambios a los actores afectados |

**Fuera de alcance v1:** horarios individuales por estudiante (electivas/IB),
app móvil nativa, API pública, integración con sistemas externos. **El vínculo
con el estudiante** se satisface por composición: `Estudiant → Inscripcion →
Seccion → horario de la sección`.

---

## 2. Mapeo de modelos SAEFL → dominio timetable (NO duplicar)

| Dominio timetable | Modelo SAEFL reutilizado | Nota |
|---|---|---|
| **Lección a programar** | `App\Models\app\Academy\Pevaluacion` | `profesor_id + seccion_id + pensum_id + lapso_id`. La "carga académica" del módulo Planning |
| **Materia** | `Pevaluacion.pensum → Pensum → Asignatura` | `asignaturas.name`, `code` |
| **Horas semanales** | `Asignatura.hour_t_week`, `Asignatura.hour_p_week` | Teóricas y prácticas (nullable, `int`) |
| **Sección** | `App\Models\app\Academy\Seccion` | `name` (letra), `amount_student`, `grado_id` |
| **Grado** | `App\Models\app\Academy\Grado` | `pestudio_id`, `code_sm` |
| **Plan de estudio** | `App\Models\app\Academy\Pestudio` | `peducativo_id` |
| **Proyecto educativo** | `App\Models\app\Academy\Peducativo` | `pescolar_id` |
| **Docente** | `App\Models\app\Academy\Profesor` | `user_id`, `ci_profesor`, `status_active` |
| **Estudiante** | `App\Models\app\Learner\Estudiant` | vía `Inscripcion` (`estudiant_id → seccion_id`) |
| **Período académico** | `App\Models\app\Academy\Lapso` | `finicial`, `ffinal`, `academic_start_date` |

**Regla de oro (ADR-TT-001):** el horario **nunca** crea sus propias materias,
secciones, docentes o estudiantes. Todo se lee desde las tablas académicas. Las
tablas nuevas `timetable_*` solo guardan **estructura temporal** (turnos,
períodos, aulas), **asignación** (slots), **disponibilidad** y **operación**
(ausencias/suplencias).

---

## 3. Modelo de datos nuevo (migraciones `2026_*_create_timetable_*`)

```sql
-- Turno: mañana (M) o tarde (T). Sólo estructura; los días son fijos L–V.
CREATE TABLE timetable_shifts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code ENUM('M','T') NOT NULL,
    name VARCHAR(20) NOT NULL,          -- 'Mañana' | 'Tarde'
    start_time TIME NOT NULL,           -- hora nominal de inicio del turno
    end_time TIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Un calendario por lapso académico.
CREATE TABLE timetable_calendars (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lapso_id BIGINT UNSIGNED NOT NULL,  -- FK → lapsos (período académico)
    pescolar_id BIGINT UNSIGNED NULL,   -- FK → pescolars (año escolar, opcional)
    name VARCHAR(120) NOT NULL,
    status ENUM('draft','generating','active','archived') DEFAULT 'draft',
    period_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 45,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_calendar_lapso (lapso_id),
    CONSTRAINT fk_cal_lapso FOREIGN KEY (lapso_id) REFERENCES lapsos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Períodos/bloques del día, agrupados por turno.
CREATE TABLE timetable_periods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    calendar_id BIGINT UNSIGNED NOT NULL,  -- FK → timetable_calendars
    shift_id BIGINT UNSIGNED NOT NULL,     -- FK → timetable_shifts
    day_of_week TINYINT UNSIGNED NOT NULL, -- 1=Lu … 5=Vi
    order_in_day TINYINT UNSIGNED NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_break BOOLEAN NOT NULL DEFAULT false,
    UNIQUE KEY uq_period (calendar_id, shift_id, day_of_week, order_in_day),
    CONSTRAINT fk_period_cal FOREIGN KEY (calendar_id) REFERENCES timetable_calendars(id) ON DELETE CASCADE,
    CONSTRAINT fk_period_shift FOREIGN KEY (shift_id) REFERENCES timetable_shifts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Aulas/espacios.
CREATE TABLE timetable_rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,            -- 'A1', 'LAB-QUIM', 'PATIO'
    name VARCHAR(120) NOT NULL,
    capacity SMALLINT UNSIGNED NULL,
    type ENUM('aula','laboratorio','patio','cancha','taller','salon') NOT NULL DEFAULT 'aula',
    features JSON NULL,                   -- ej. {"proyector":true}
    status_active BOOLEAN NOT NULL DEFAULT true,
    UNIQUE KEY uq_room_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Lección a programar: envoltura 1:1 de una Pevaluacion (ADR-TT-001).
CREATE TABLE timetable_lessons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    calendar_id BIGINT UNSIGNED NOT NULL,  -- FK → timetable_calendars
    pevaluacion_id BIGINT UNSIGNED NOT NULL, -- FK → pevaluacions (fuente de verdad)
    shift_id BIGINT UNSIGNED NOT NULL,     -- turno asignado a la sección/lección
    weekly_blocks_t TINYINT UNSIGNED NOT NULL DEFAULT 0, -- bloques teóricos
    weekly_blocks_p TINYINT UNSIGNED NOT NULL DEFAULT 0, -- bloques prácticos
    room_type_required ENUM('aula','laboratorio','patio','cancha','taller','salon') NULL,
    priority TINYINT UNSIGNED NOT NULL DEFAULT 0,  -- mayor = más restrictivo/importante
    locked BOOLEAN NOT NULL DEFAULT false,  -- el motor no reasigna si está en true
    UNIQUE KEY uq_lesson_pevaluacion (calendar_id, pevaluacion_id),
    CONSTRAINT fk_lesson_cal FOREIGN KEY (calendar_id) REFERENCES timetable_calendars(id) ON DELETE CASCADE,
    CONSTRAINT fk_lesson_pev FOREIGN KEY (pevaluacion_id) REFERENCES pevaluacions(id) ON DELETE CASCADE,
    CONSTRAINT fk_lesson_shift FOREIGN KEY (shift_id) REFERENCES timetable_shifts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Disponibilidad de docentes: grilla día × período.
CREATE TABLE timetable_teacher_availability (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    calendar_id BIGINT UNSIGNED NOT NULL,
    profesor_id BIGINT UNSIGNED NOT NULL,  -- FK → profesors
    period_id BIGINT UNSIGNED NOT NULL,    -- FK → timetable_periods
    is_available BOOLEAN NOT NULL DEFAULT true,
    UNIQUE KEY uq_avail (calendar_id, profesor_id, period_id),
    CONSTRAINT fk_avail_cal FOREIGN KEY (calendar_id) REFERENCES timetable_calendars(id) ON DELETE CASCADE,
    CONSTRAINT fk_avail_prof FOREIGN KEY (profesor_id) REFERENCES profesors(id) ON DELETE CASCADE,
    CONSTRAINT fk_avail_period FOREIGN KEY (period_id) REFERENCES timetable_periods(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Resultado: una lección asignada a día + período + aula.
CREATE TABLE timetable_slots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    calendar_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,    -- FK → timetable_lessons
    period_id BIGINT UNSIGNED NOT NULL,    -- FK → timetable_periods (implica día y turno)
    room_id BIGINT UNSIGNED NULL,          -- FK → timetable_rooms
    is_manual_override BOOLEAN NOT NULL DEFAULT false,
    locked BOOLEAN NOT NULL DEFAULT false,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    -- Índices de integridad: un docente/aula/sección no puede repetirse en el mismo slot.
    UNIQUE KEY uq_slot_teacher (calendar_id, period_id, lesson_id),
    CONSTRAINT fk_slot_cal FOREIGN KEY (calendar_id) REFERENCES timetable_calendars(id) ON DELETE CASCADE,
    CONSTRAINT fk_slot_lesson FOREIGN KEY (lesson_id) REFERENCES timetable_lessons(id) ON DELETE CASCADE,
    CONSTRAINT fk_slot_period FOREIGN KEY (period_id) REFERENCES timetable_periods(id) ON DELETE CASCADE,
    CONSTRAINT fk_slot_room FOREIGN KEY (room_id) REFERENCES timetable_rooms(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Log de conflictos detectados (auditoría / reglas duras).
CREATE TABLE timetable_conflicts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    calendar_id BIGINT UNSIGNED NOT NULL,
    slot_id BIGINT UNSIGNED NULL,
    type ENUM('teacher_double_booked','room_double_booked','section_double_booked',
              'availability_violation','shift_mismatch') NOT NULL,
    details JSON NULL,
    resolved BOOLEAN NOT NULL DEFAULT false,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_conf_cal FOREIGN KEY (calendar_id) REFERENCES timetable_calendars(id) ON DELETE CASCADE,
    CONSTRAINT fk_conf_slot FOREIGN KEY (slot_id) REFERENCES timetable_slots(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ausencias de docentes y asignación de suplentes (v1.2 opcional, ver §7).
CREATE TABLE timetable_absences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    calendar_id BIGINT UNSIGNED NOT NULL,
    profesor_id BIGINT UNSIGNED NOT NULL,
    date_start DATE NOT NULL, date_end DATE NOT NULL,
    reason VARCHAR(255) NULL,
    CONSTRAINT fk_abs_cal FOREIGN KEY (calendar_id) REFERENCES timetable_calendars(id) ON DELETE CASCADE,
    CONSTRAINT fk_abs_prof FOREIGN KEY (profesor_id) REFERENCES profesors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE timetable_substitute_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    absence_id BIGINT UNSIGNED NOT NULL,
    slot_id BIGINT UNSIGNED NOT NULL,
    substitute_profesor_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending','confirmed','declined') DEFAULT 'pending',
    notified_at TIMESTAMP NULL,
    CONSTRAINT fk_sub_abs FOREIGN KEY (absence_id) REFERENCES timetable_absences(id) ON DELETE CASCADE,
    CONSTRAINT fk_sub_slot FOREIGN KEY (slot_id) REFERENCES timetable_slots(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Decisión de diseño (ADR-TT-002):** la fila de integridad se garantiza **en la
capa de aplicación** (validación antes de persistir) y **a nivel de BD** con
índices únicos sobre los ejes conflictivos (docente/período, aula/período,
sección/período — este último viaja en `lesson_id`, ya que cada `timetable_lesson`
mapea una sección única a través de su `Pevaluacion`).

---

## 4. Turnos y estructura temporal (mañana / tarde)

- **Días lectivos fijos:** Lunes a Viernes (`day_of_week` 1..5). El spec no
  modela días feriados en v1 (se gestiona como ausencia/ajuste manual).
- **Turnos:** dos, `timetable_shifts` (`M`/`T`). Ejemplo nominal: Mañana
  `07:00–12:15`, Tarde `13:00–18:15`.
- **Períodos:** `timetable_periods` se crean por turno y día (orden en el día,
  `start_time`, `end_time`, `is_break` para recreos). El recreo es un período
  marcado `is_break` que nunca recibe slots.
- **Asignación de turno a sección:** en `timetable_lessons.shift_id`. La
  coordinación decide el turno de cada sección al crear las lecciones; el motor
  respeta que `lesson.shift_id == period.shift_id` (regla dura
  `shift_mismatch`).
- **Horas → bloques:** para cada lección, el número de bloques semanales se
  deriva de `Asignatura.hour_t_week`/`hour_p_week` y `period_minutes`:
  `weekly_blocks_t = ceil(hour_t_week * 60 / period_minutes)` y
  `weekly_blocks_p = ceil(hour_p_week * 60 / period_minutes)`. El wizard permite
  ajustar manualmente estos valores (la asignatura manda por defecto).

---

## 5. Carga de lecciones (wizard — pasos 1 a 5)

Reutiliza el patrón `LessonWizard` de LMS (`app/Livewire/Profesor/Lms/LessonWizard.php`):
un solo componente Livewire `App\Livewire\Coordinacion\Timetable\TimetableWizard`
con `currentStep`, sin clases de pasos separadas, layout `coordinacion.layouts.app`.

| Paso | Contenido | Validación |
|---|---|---|
| **1 · Calendario** | Elegir `Lapso` (se crea `timetable_calendars`, único por lapso), `period_minutes`, crear turnos M/T y períodos por día (con recreos) | Lapso activo; `timetable_periods` no vacío |
| **2 · Aulas** | CRUD de `timetable_rooms` (código único, capacidad, tipo) | `code` único, `capacity ≥ 1` |
| **3 · Lecciones** | Seleccionar pevaluacions del lapso (de `Pevaluacion::where('lapso_id', ...)`), asignar turno, revisar bloques teóricos/prácticos derivados, `room_type_required`, prioridad | 1 pev por lección; bloques > 0; sección con turno coherente |
| **4 · Disponibilidad** | Grilla día×período por docente (`timetable_teacher_availability`), con preset "todo disponible" | Al menos el turno de sus secciones |
| **5 · Generar** | Disparar `GenerateTimetableJob` (cola `binnacle`? no — cola por defecto del negocio; ver §6). Progreso vía Livewire polling | Calendario en `generating` |

> La carga masiva de lecciones (paso 3) en v1 se hace con el selector de
> pevaluacions del propio módulo Planning (sin dependencia nueva). Si se requiere
> CSV/Excel, `maatwebsite/excel` **no está instalado** en `cfla` (sí en
> `saefl/s2526`); añadirlo queda como ticket opcional (`SPEC-TIMETABLE-001g`).

---

## 6. Motor de asignación — `GenerateTimetableJob`

**Enfoque:** backtracking con forward-checking + restricciones duras/soft (100%
PHP, sin solver externo). Como **Job en cola** (la cola por defecto del negocio;
el driver es `database`). Emite evento Livewire `timetable.generated` al
terminar para refrescar la UI (Reverb ya configurado).

```
Entrada: timetable_lessons activas del calendario (estado draft)
Salida:  timetable_slots completos o solución parcial + infactibilidad

1. Derivar por lección: {seccion, docente(vía pev), asignatura, bloques_t, bloques_p,
   shift, room_type_required, priority, locked}
2. Ordenar lecciones por "grado de restricción" descendente (ADR-TT-003):
   - más bloques, mayor priority, room_type_required != null,
     docente con menor disponibilidad libre
3. Dominio de slots candidatos por lección:
   - períodos con period.shift == lesson.shift  (regla shift_mismatch)
   - período disponible según timetable_teacher_availability (si existe fila: respetar)
   - aula libre: room.type == lesson.room_type_required (si se exige) y capacity >= seccion.amount_student
   - sin conflicto de sección (otra lección de la misma sección en ese período)
   - sin conflicto de docente ni de aula en ese período
4. Backtracking con forward-checking; límite configurable (default 30s). Si se
   excede → mejor solución parcial + lecciones no asignadas (para resolver a mano).
5. Restricciones soft (penalizan, no bloquean; desempate):
   - sin huecos en el turno del docente (preferir períodos contiguos)
   - distribuir bloques de una misma lección en días NO consecutivos cuando >1
   - teóricos en períodos tempranos; prácticos en laboratorio/espacio si aplica
   - evitar que un docente cruce de turno el mismo día (si dicta en ambos)
6. Persistir en transacción; registrar conflictos residuales en timetable_conflicts.
```

**Reglas duras (nunca se violan):** docente doble, aula doble, sección doble,
periodo en otro turno, disponibilidad marcada como no disponible.

**Test de aceptación mínimos:** (a) dataset sintético factible pequeño → horario
completo sin conflictos; (b) dataset infactible (docente sobresaturado) → reporta
lecciones sin asignar y NO produce doble-booking; (c) bloques_t/bloques_p
respetados contra `hour_t_week`/`hour_p_week`.

---

## 7. Editor manual (drag-and-drop)

- Grilla días (columnas Lu–Vi) × períodos (filas por turno), por **sección** o
  por **docente**.
- **Sin librería externa** (no hay dependencia drag-and-drop en el repo): Alpine.js
  con drag nativo HTML5 (`draggable`, `dragstart/dragover/drop`) — coherente con
  el stack actual (Alpine ya presente).
- Al soltar: validación síncrona de las reglas duras **antes** de persistir;
  si falla, se muestra el conflicto inline y no se guarda.
- `locked = true` (en `timetable_lessons` o `timetable_slots`) → el motor lo
  respeta en una regeneración posterior.
- `is_manual_override = true` en el slot marcado a mano.

---

## 8. Publicación y exportación

- **Vista pública por sección/docente** vía `URL::temporarySignedRoute` (patrón
  "compartir por enlace" ya usado en la app): lectura de un `timetable_calendar`
  activo, sin login.
- **PDF** por sección/docente/aula con `Barryvdh\DomPDF\Facade\Pdf::loadView(...)`
  (mismo patrón que `BinnaclePdfController` y `CatchmentPDFController`).
- **Vista del estudiante** (rol `is_student`): su sección vía
  `Estudiant → inscripcion → seccion → slots`; **docente** (rol `is_profesor`):
  solo sus slots.

---

## 9. Roles y permisos (matriz RBAC)

| Capacidad | `is_coordinacion` | `is_planner` | `is_leadership` | `is_director` | `profesor` | `estudiante` |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| CRUD calendario/aulas/lecciones/disponibilidad | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Ejecutar generación / regeneración | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Editor manual + suplencias | ✅ | ✅ | ✅ (ver) | ❌ | ❌ | ❌ |
| Ver horario de cualquier sección | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Ver su propio horario | — | — | — | — | ✅ | ✅ (su sección) |
| Publicar / exportar / enlace firmado | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |

**Nota:** `is_coordinacion` e `is_planner` (flags booleanos en `users`, rol
"Coordinación" / "Planificación" en `User::getRoleLabelAttribute`) comparten el
**mismo set de permisos** en este módulo (ADR-TT-006). La diferencia es
puramente nominal: ambos gestionan el horario.

Middleware: grupo `Route::prefix('coordinacion')->middleware(['auth','isCoordinacion'])`
(ya existe `IsCoordinacion`); para `is_planner` reutiliza el acceso de gestión
del módulo Planning (los flags no se combinan en `User::isAdminOrDiagnostic`),
usando una autorización por gate o la comprobación `is_coordinacion || is_planner`;
lectura para leadership/director reutiliza `isLeadership`/`isDirector`.

---

## 10. Notificaciones

Reutilizar `App\Services\NotificationService::notifyUsers()` (canal `database` +
broadcast `NotificationReceived`, tabla `notifications` existente).

- Cambio de horario que afecta a un docente o sección → notificación DB al
  docente (`Profesor.user_id`).
- Regeneración completa → notificación de resumen a coordinación.
- Suplencia asignada → notificación con confirmar/rechazar (v1.2).

---

## 11. ADRs (decisiones de arquitectura)

| ADR | Decisión | Por qué |
|---|---|---|
| **TT-001** | Las lecciones se toman de `pevaluacions` existentes; no se duplican catálogos | Fuente de verdad única; el horario es una **vista programable** de la carga académica |
| **TT-002** | Integridad de conflictos en aplicación + índices únicos en BD | Defensa en profundidad (igual que ADR-004 de binnacle) |
| **TT-003** | Orden de asignación por "grado de restricción" (heurística), no aleatorio | Mayor ratio de éxito del backtracking sin solver externo |
| **TT-004** | Horas teóricas/prácticas derivadas de `Asignatura` pero ajustables en la lección | La asignatura define el default; la coordinación puede afinar |
| **TT-005** | Turno es propiedad de la lección (sección), no del período global | Una sección entera comparte turno; el motor cruza `lesson.shift == period.shift` |
| **TT-006** | `is_coordinacion` e `is_planner` comparten el mismo set de permisos en el módulo | Flags nominales distintos (rol de UI "Coordinación"/"Planificación") con idéntico alcance operativo sobre el horario |

---

## 12. Tickets de descomposición (criterios de aceptación)

| Ticket | Alcance | Aceptación |
|---|---|---|
| **`SPEC-TIMETABLE-001a`** | Migraciones `timetable_*` + modelos Eloquent + factories | Las tablas existen con FKs a `lapsos/pevaluacions/profesors/seccions/asignaturas`; `Schema` test pasa |
| **`SPEC-TIMETABLE-001b`** | Wizard pasos 1–4 (calendario, aulas, lecciones desde pev, disponibilidad) | Se crea un calendario con turnos M/T, períodos y lecciones derivadas de `hour_t_week/hour_p_week` |
| **`SPEC-TIMETABLE-001c`** | `GenerateTimetableJob` (motor §6) | Tests (a)/(b)/(c) del §6 pasan; job en cola, no bloquea request |
| **`SPEC-TIMETABLE-001d`** | Editor drag-and-drop Alpine con validación de reglas duras | Soltar un slot conflictivo muestra error inline y no persiste |
| **`SPEC-TIMETABLE-001e`** | Ausencias/suplencias + notificaciones | Ausencia → slots afectados identificados → suplente sugerido → notificación DB al suplente |
| **`SPEC-TIMETABLE-001f`** | PDF + vista pública firmada + vistas por rol | PDF por sección/docente/aula; enlace firmado sin login; estudiante ve solo su sección |
| **`SPEC-TIMETABLE-001g`** (opcional) | Importación CSV/Excel de lecciones | Requiere añadir `maatwebsite/excel`; import falla limpio ante filas duplicadas |

---

## 13. Riesgos y decisiones abiertas

- **Rendimiento del backtracking** en instituciones grandes (>80 docentes): si se
  supera el límite de tiempo → solución parcial + resolución manual (recomendado,
  igual que `specDrive01.md` §11).
- **Carga docente cruzada entre lapsos**: un calendario por lapso es lo mínimo;
  si la carga cambia entre lapsos, se crea un calendario por lapso (ya cubierto
  por `uq_calendar_lapso`).
- **Docentes que dictan en ambos turnos**: el motor permite slots en M y T para
  el mismo docente, penalizando huecos y cruces diarios (soft).
- **Recreos y horas especiales** (actos, deporte): períodos `is_break` y aulas
  tipo `patio/cancha` cubren la mayoría; casos puntuales se resuelven con slots
  manuales `locked`.
- **Horas prácticas sin laboratorio**: si `room_type_required` se deja nulo, el
  motor usa cualquier aula libre (el desglose T/P sigue siendo informativo).