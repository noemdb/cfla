# SPEC-TIMETABLE-001: Horario Escolar Semanal — Lunes a Viernes, Turnos Mañana/Tarde (CFLA)

| | |
|---|---|
| **Estado** | Draft v2 — nivel implementación (solver, contratos Livewire, concurrencia, NFRs, testing) |
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
-- profesor_id/seccion_id se desnormalizan desde la lección para poder
-- garantizar en BD las reglas duras "docente no-doble" y "sección no-doble"
-- (ADR-TT-002). room_id es NULL en bloques teóricos sin aula dedicada.
CREATE TABLE timetable_slots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    calendar_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,    -- FK → timetable_lessons
    period_id BIGINT UNSIGNED NOT NULL,    -- FK → timetable_periods (implica día y turno)
    profesor_id BIGINT UNSIGNED NOT NULL,  -- FK → profesors (desnormalizado)
    seccion_id BIGINT UNSIGNED NOT NULL,   -- FK → seccions (desnormalizado)
    room_id BIGINT UNSIGNED NULL,          -- FK → timetable_rooms
    is_manual_override BOOLEAN NOT NULL DEFAULT false,
    locked BOOLEAN NOT NULL DEFAULT false,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    -- Índices de integridad (reglas duras a nivel BD):
    -- un docente / una sección / un aula no pueden repetirse en el mismo período.
    UNIQUE KEY uq_slot_teacher (calendar_id, period_id, profesor_id),
    UNIQUE KEY uq_slot_section (calendar_id, period_id, seccion_id),
    UNIQUE KEY uq_slot_room (calendar_id, period_id, room_id),  -- NULL no colisiona en MySQL
    UNIQUE KEY uq_slot_lesson (calendar_id, period_id, lesson_id),
    CONSTRAINT fk_slot_cal FOREIGN KEY (calendar_id) REFERENCES timetable_calendars(id) ON DELETE CASCADE,
    CONSTRAINT fk_slot_lesson FOREIGN KEY (lesson_id) REFERENCES timetable_lessons(id) ON DELETE CASCADE,
    CONSTRAINT fk_slot_profesor FOREIGN KEY (profesor_id) REFERENCES profesors(id),
    CONSTRAINT fk_slot_seccion FOREIGN KEY (seccion_id) REFERENCES seccions(id),
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
índices únicos sobre los ejes conflictivos: `profesor_id` (docente), `seccion_id`
(sección) y `room_id` (aula) por período (`timetable_slots` desnormaliza docente
y sección desde la lección). `room_id` NULL no colisiona en MySQL (múltiples NULL
permitidos en índice único), de modo que los bloques teóricos sin aula dedicada
no se bloquean entre sí.

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
   - bloques prácticos: aula libre con room.type == lesson.room_type_required (si se
     exige) y capacity >= seccion.amount_student; bloques teóricos: sin aula dedicada
   - sin conflicto de sección (otra lección de la misma sección en ese período)
   - sin conflicto de docente ni de aula en ese período
4. Reservar PRIMERO las lecciones locked (ADR-TT-007); luego backtracking con
   forward-checking; límite configurable (default 30s). Si se excede → conserva la
   solución parcial + lecciones no asignadas (para resolver a mano) (ADR-TT-009).
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

### 6.1 Diseño de clases del solver (implementación de referencia)

Namespace propuesto: `App\Services\Timetable\Solver`. Todo en PHP puro,
sin dependencias nuevas — testeable de forma unitaria sin Eloquent (recibe
DTOs, no Models).

```php
// App\Services\Timetable\Solver\LessonToSchedule.php  (DTO, inmutable)
final class LessonToSchedule
{
    public function __construct(
        public readonly int $lessonId,
        public readonly int $seccionId,
        public readonly int $profesorId,
        public readonly int $shiftId,
        public readonly int $blocksT,            // weekly_blocks_t (§4): bloques teóricos
        public readonly int $blocksP,            // weekly_blocks_p (§4): bloques prácticos
        public readonly ?string $roomTypeRequired, // SOLO exigido para bloques prácticos
        public readonly int $priority,
        public readonly bool $locked,
        public readonly array $lockedPeriodIds = [], // si locked=true, períodos ya fijados
    ) {}

    public function blocksNeeded(): int { return $this->blocksT + $this->blocksP; }
}

// App\Services\Timetable\Solver\SlotCandidate.php
final class SlotCandidate
{
    public function __construct(
        public readonly int $periodId,
        public readonly ?int $roomId,   // null = bloque teórico sin aula dedicada
        public readonly bool $isPractical,
    ) {}
}

// App\Services\Timetable\Solver\SchedulingContext.php
// Estado mutable durante el backtracking: qué está ocupado.
// OJO (ADR-TT-008): roomBusy solo se toca cuando $roomId !== null; si no,
// "periodId:" con roomId null colisionaría entre secciones sin aula.
final class SchedulingContext
{
    /** @var array<string,true> "periodId:profesorId" */
    private array $teacherBusy = [];
    /** @var array<string,true> "periodId:roomId"  (solo roomId != null) */
    private array $roomBusy = [];
    /** @var array<string,true> "periodId:seccionId" */
    private array $sectionBusy = [];

    public function isFree(int $periodId, int $profesorId, int $seccionId, ?int $roomId): bool
    {
        if (isset($this->teacherBusy["$periodId:$profesorId"])) return false;
        if (isset($this->sectionBusy["$periodId:$seccionId"])) return false;
        if ($roomId !== null && isset($this->roomBusy["$periodId:$roomId"])) return false;
        return true;
    }

    public function occupy(int $periodId, int $profesorId, int $seccionId, ?int $roomId): void
    {
        $this->teacherBusy["$periodId:$profesorId"] = true;
        $this->sectionBusy["$periodId:$seccionId"] = true;
        if ($roomId !== null) $this->roomBusy["$periodId:$roomId"] = true;
    }

    public function release(int $periodId, int $profesorId, int $seccionId, ?int $roomId): void
    {
        unset($this->teacherBusy["$periodId:$profesorId"]);
        unset($this->sectionBusy["$periodId:$seccionId"]);
        if ($roomId !== null) unset($this->roomBusy["$periodId:$roomId"]);
    }
}

// App\Services\Timetable\Solver\TimetableSolver.php
final class TimetableSolver
{
    /** @param LessonToSchedule[] $lessons
     *  @param array<int,int[]> $availablePeriodsByTeacher  profesorId => [periodId,...]
     *  @param array<int,SlotCandidate[]> $roomsByType       roomType => aulas compatibles */
    public function __construct(
        private array $lessons,
        private array $availablePeriodsByTeacher,
        private array $roomsByType,
        private int $timeLimitSeconds = 30,
    ) {}

    /** @return SolverResult */
    public function solve(): SolverResult
    {
        $ctx = new SchedulingContext();
        $assignment = [];   // lessonId => [SlotCandidate, ...] (uno por bloque)
        $unassigned = [];

        // 1) Reservar PRIMERO las lecciones locked (ADR-TT-007): nunca se reasignan
        //    y su período queda fuera del dominio del resto. Si una locked no cabe
        //    porque ya está ocupada por otra locked -> se marca como conflicto.
        $locked = array_filter($this->lessons, fn($l) => $l->locked);
        $free   = array_filter($this->lessons, fn($l) => !$l->locked);

        foreach ($locked as $lesson) {
            $combo = [];
            foreach ($lesson->lockedPeriodIds as $pId) {
                if (!$ctx->isFree($pId, $lesson->profesorId, $lesson->seccionId, null)) {
                    $unassigned[] = $lesson->lessonId; // conflicto entre locked: resolver a mano
                    $combo = [];
                    break;
                }
                $ctx->occupy($pId, $lesson->profesorId, $lesson->seccionId, null);
                $combo[] = new SlotCandidate($pId, null, false);
            }
            $assignment[$lesson->lessonId] = $combo;
        }

        // 2) Lecciones libres, ordenadas por grado de restricción (ADR-TT-003)
        $ordered = $this->orderByConstraintDegree(array_values($free));
        $deadline = microtime(true) + $this->timeLimitSeconds;

        $this->backtrack($ordered, 0, $ctx, $assignment, $unassigned, $deadline);

        return new SolverResult($assignment, $unassigned);
    }

    private function backtrack(
        array $lessons, int $index, SchedulingContext $ctx,
        array &$assignment, array &$unassigned, float $deadline
    ): bool {
        if ($index >= count($lessons)) return true;

        // Corte por tiempo (ADR-TT-009): marcar el resto como NO asignadas y
        // DEVOLVER true. Devolver false desharía (release) todo lo ya asignado,
        // perdiendo la solución parcial — este es el comportamiento correcto.
        if (microtime(true) > $deadline) {
            for ($i = $index; $i < count($lessons); $i++) {
                $unassigned[] = $lessons[$i]->lessonId;
            }
            return true;
        }

        $lesson = $lessons[$index];
        $domain = $this->buildDomain($lesson, $ctx); // forward-checking: dominio ya filtrado

        foreach ($this->combinationsOfSize($domain, $lesson) as $combo) {
            foreach ($combo as $slot) {
                $ctx->occupy($slot->periodId, $lesson->profesorId, $lesson->seccionId, $slot->roomId);
            }
            $assignment[$lesson->lessonId] = $combo;

            if ($this->backtrack($lessons, $index + 1, $ctx, $assignment, $unassigned, $deadline)) {
                return true;
            }
            // fallo más adelante -> deshacer y probar siguiente combinación
            foreach ($combo as $slot) {
                $ctx->release($slot->periodId, $lesson->profesorId, $lesson->seccionId, $slot->roomId);
            }
            unset($assignment[$lesson->lessonId]);
        }

        // sin combinación viable: se reporta como no asignada y se continúa (no aborta todo el proceso)
        $unassigned[] = $lesson->lessonId;
        return $this->backtrack($lessons, $index + 1, $ctx, $assignment, $unassigned, $deadline);
    }

    private function orderByConstraintDegree(array $lessons): array
    {
        usort($lessons, fn($a, $b) =>
            [$b->priority, $b->roomTypeRequired !== null, $b->blocksNeeded()]
            <=> [$a->priority, $a->roomTypeRequired !== null, $a->blocksNeeded()]
        );
        return $lessons;
    }

    /** Dominio por tipo de bloque (ADR-TT-004/ADR-TT-010):
     *  - 't': períodos libres del turno, roomId=null (teórico, sin aula dedicada)
     *  - 'p': períodos libres con aula del tipo roomTypeRequired libre (si se exige);
     *         si no se exige, idéntico a 't'.
     *  NOTA: el filtro de turno (lesson.shift == period.shift) y la disponibilidad
     *  docente ya se aplican al construir $availablePeriodsByTeacher en la capa que
     *  prepara los datos del solver; aquí solo se chequea "libre" en el contexto. */
    private function buildDomain(LessonToSchedule $lesson, SchedulingContext $ctx): array
    {
        $base = $this->availablePeriodsByTeacher[$lesson->profesorId] ?? [];
        $domain = ['t' => [], 'p' => []];
                $domain['t'][] = new SlotCandidate($periodId, null, false);
            }
            if ($lesson->roomTypeRequired !== null) {
                foreach ($this->roomsByType[$lesson->roomTypeRequired] ?? [] as $room) {
                    if ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, $room->roomId)) {
                        $domain['p'][] = new SlotCandidate($periodId, $room->roomId, true);
                    }
                }
            } elseif ($ctx->isFree($periodId, $lesson->profesorId, $lesson->seccionId, null)) {
                $domain['p'][] = new SlotCandidate($periodId, null, true);
            }
        }
        return $domain;
    }

    /** Genera combinaciones: elige $lesson->blocksT de $domain['t'] y
     *  $lesson->blocksP de $domain['p'], sin repetir período dentro de la lección,
     *  respetando la heurística soft del §6 (no-huecos, no-consecutivo si >1,
     *  teóricos tempranos) — devuelve las mejores primero. */
    private function combinationsOfSize(array $domain, LessonToSchedule $lesson): iterable { /* ... */ }
}
```

**Complejidad y por qué el corte por tiempo es aceptable:** el backtracking puro
es exponencial en el peor caso, pero el ordenamiento por grado de restricción
(ADR-TT-003) + forward-checking reduce drásticamente el árbol en instituciones
típicas (<100 docentes, <40 secciones). El `timeLimitSeconds` es la válvula de
escape: preferible una solución parcial en 30s + resolución manual, a bloquear
el job indefinidamente (ver §13).

### 6.2 Métrica de calidad de una solución (para desempate y para reportar al usuario)

```
score(solution) = Σ por lección:
    - PENALTY_GAP           * huecos_en_turno_del_docente
    - PENALTY_CONSECUTIVE   * (bloques_misma_leccion_en_dias_consecutivos si blocksNeeded>1)
    - PENALTY_LATE_THEORY   * (bloque_teorico asignado en período tardío)
    - PENALTY_SHIFT_CROSS   * (docente dicta en M y T el mismo día)
score más bajo = mejor. Se usa solo para elegir entre combinaciones candidatas
del mismo dominio (desempate), no como criterio de aceptación — las reglas
duras del §6 siguen siendo obligatorias.
```

Persistir el `score` final en `timetable_calendars.quality_score` (columna
nueva, `DECIMAL(8,2) NULL`) para que coordinación pueda comparar regeneraciones.

### 6.3 Modo *dry-run* (previsualización antes de comprometer)

`GenerateTimetableJob` acepta `dryRun: bool`. En modo dry-run:
- corre el solver completo,
- **no** escribe en `timetable_slots`,
- serializa el resultado a `timetable_calendars.preview_payload` (columna
  `JSON NULL` nueva) para que el wizard lo muestre antes de "Confirmar y
  publicar".
- Confirmar dispara un segundo job liviano que solo persiste el `preview_payload`
  ya validado (evita recalcular).

Esto es necesario porque una regeneración accidental sobre un calendario
`active` no debe pisar el horario en producción sin que coordinación lo revise.

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
| **TT-007** | Regenerar un calendario `active` siempre pasa por modo `dryRun`; los slots/lecciones `locked` se preservan íntegros | Nunca se pisa un horario publicado sin revisión explícita; lo fijado a mano no se reasigna |
| **TT-008** | `roomBusy` en `SchedulingContext` solo se toca cuando `roomId !== null` | Evita que "periodId:" con `roomId=null` colisione entre secciones sin aula dedicada |
| **TT-009** | Corte por tiempo: al vencer el deadline se marcan las restantes como no asignadas y se devuelve `true` (conserva la solución parcial) | Devolver `false` desharía todo lo ya asignado (release en cascada) y se perdería el progreso |
| **TT-010** | El DTO mantiene `blocksT`/`blocksP` separados; `room_type_required` solo aplica a bloques prácticos | Preserva el desglose teórico/práctico (requisito `hour_t_week`/`hour_p_week`) y evita exigir laboratorio a bloques teóricos |

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
| **`SPEC-TIMETABLE-001h`** | Modo dry-run + diff + bloqueo optimista (§6.3, §14, §15) | Regenerar un calendario `active` nunca persiste sin confirmación; `version` desactualizada rechaza el `UPDATE` |
| **`SPEC-TIMETABLE-001i`** | Observabilidad (§17) + seeder de dataset sintético (§18) | Canal `timetable` loggea cada corrida del job; `TimetableTestSeeder` reproducible con seed fijo |

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

---

## 14. Máquina de estados — `timetable_calendars.status`

```
draft ──(wizard paso 5: "Generar")──▶ generating
generating ──(solver termina, dry_run=false)──▶ draft   (si hay lecciones sin asignar → revisar)
generating ──(solver termina, todo asignado, dry_run=false)──▶ active
generating ──(solver termina, dry_run=true)──▶ draft    (con preview_payload listo para revisar)
active ──(coordinación edita a mano vía §7)──▶ active   (no cambia de estado; slots individuales se marcan is_manual_override)
active ──(coordinación dispara "Regenerar")──▶ generating  (requiere confirmación explícita, ADR-TT-007)
active ──(cierre de lapso)──▶ archived
archived ──▶ (terminal; solo lectura, no editable)
```

**ADR-TT-007:** regenerar un calendario `active` **siempre** pasa por modo
`dryRun` (§6.3) primero; nunca se sobreescribe `timetable_slots` en un solo
paso sin confirmación explícita del usuario. Los slots con `locked=true` se
preservan íntegros en cualquier regeneración (se pasan al solver como ya
asignados, no como dominio libre).

---

## 15. Concurrencia y regeneración segura

- **Bloqueo optimista:** `timetable_calendars` añade `version INT UNSIGNED
  DEFAULT 0`. El wizard y el editor manual incluyen `version` en cada
  escritura; un `UPDATE ... WHERE id=? AND version=?` que afecta 0 filas
  dispara un error "otro usuario modificó este horario, recarga" en Livewire
  (evita que dos coordinadores pisen cambios entre sí).
- **`GenerateTimetableJob` es idempotente por diseño:** se identifica con un
  `job_id` almacenado en `timetable_calendars.active_job_id`; si se dispara una
  segunda generación mientras la primera corre, el wizard bloquea el botón
  "Generar" (deshabilitado si `status == 'generating'`) en vez de encolar un
  job duplicado.
- **Diff antes de aplicar una regeneración:** al confirmar un dry-run sobre un
  calendario `active`, el sistema calcula la diferencia slot a slot contra el
  horario vigente y se la muestra a coordinación (ej. "12 lecciones cambian de
  período, 3 docentes afectados") antes de persistir — reutiliza el mismo
  payload de notificaciones del §10.

---

## 16. Contratos de componentes Livewire (API pública)

### `App\Livewire\Coordinacion\Timetable\TimetableWizard`

```php
class TimetableWizard extends Component
{
    public int $currentStep = 1;
    public ?int $calendarId = null;     // null hasta paso 1 completado
    public int $lapsoId;
    public int $periodMinutes = 45;

    // Paso 3
    public array $selectedPevaluacionIds = [];

    // Paso 5
    public bool $dryRun = true;

    public function nextStep(): void {}
    public function previousStep(): void {}
    public function saveCalendarStructure(): void {}      // paso 1
    public function saveRooms(): void {}                  // paso 2
    public function deriveLessonsFromPevaluaciones(): void {} // paso 3 (§2, §4)
    public function saveAvailability(): void {}            // paso 4
    public function generate(): void {                    // paso 5, dispara job §6
        // valida status == draft, encola GenerateTimetableJob($this->calendarId, dryRun: $this->dryRun)
    }
    public function confirmPreview(): void {}              // solo si dryRun=true y hay preview_payload

    #[On('timetable.generated')]                           // evento del job (§6, Reverb)
    public function onGenerated(int $calendarId): void {}
}
```

### `App\Livewire\Coordinacion\Timetable\TimetableEditor`

```php
class TimetableEditor extends Component
{
    public int $calendarId;
    public string $viewBy = 'seccion';   // 'seccion' | 'profesor'
    public ?int $filterId = null;
    public int $version;                 // para bloqueo optimista (§15)

    public function moveSlot(int $slotId, int $newPeriodId, ?int $newRoomId): void {
        // valida reglas duras del §6 vía App\Services\Timetable\ConflictValidator
        // si falla: $this->addError('conflict', "...") y NO persiste
        // si ok: persiste con WHERE version=$this->version, incrementa version, dispara evento
    }
    public function toggleLock(int $slotId): void {}
}
```

**Regla de diseño:** ningún componente Livewire llama directamente al
`TimetableSolver` (§6.1) — siempre a través de `GenerateTimetableJob` (async)
o de `App\Services\Timetable\ConflictValidator` (síncrono, solo valida un
slot a la vez, reutilizado tanto por el editor como por el job).

---

## 17. Requisitos no funcionales y observabilidad

| NFR | Objetivo |
|---|---|
| Tiempo de generación | p95 < 30s para ≤ 60 docentes / ≤ 30 secciones (institución típica CFLA); corte duro configurable (`timeLimitSeconds`, §6.1) |
| Editor manual (mover un slot) | Respuesta < 300ms (validación síncrona, sin llamar al solver completo) |
| Disponibilidad de la vista pública | Cacheable (Laravel cache, TTL 5 min) — un calendario `active` cambia poco una vez publicado |
| Logging | `GenerateTimetableJob` loggea inicio/fin/duración/lecciones no asignadas/score con un `correlation_id` = `calendar_id` + timestamp, canal `timetable` (nuevo canal en `config/logging.php`) |
| Métricas mínimas | Contador de regeneraciones por calendario; histograma de duración del job; conteo de conflictos residuales por tipo (§3 `timetable_conflicts`) |
| Auditoría | Cada cambio manual en `timetable_slots` (mover/lock) registra `updated_by` (ya patrón estándar del proyecto) |

---

## 18. Estrategia de testing

| Nivel | Qué cubre |
|---|---|
| **Unit — `TimetableSolver`** | Sin Eloquent, solo DTOs (§6.1): dataset factible pequeño (3 lecciones, 2 docentes) → sin conflictos; dataset infactible (1 docente, 40 horas/semana) → reporta no asignadas, nunca doble-booking; respeta `locked` (no reasigna y **reserva primero**); respeta `shift_mismatch`; **timeout → conserva la solución parcial** (no vacía); **aulas `roomId=null` no colisionan entre secciones**; bloques prácticos exigen aula del tipo pedido |
| **Unit — `ConflictValidator`** | Cada regla dura del §6 probada aislada (docente doble, aula doble, sección doble, disponibilidad, turno) |
| **Feature — `TimetableWizard`** | Flujo completo de los 5 pasos con `Livewire::test()`; paso 3 deriva bloques correctamente desde `hour_t_week/hour_p_week` (regla de redondeo del §4) |
| **Feature — `TimetableEditor`** | `moveSlot` a un slot conflictivo falla con error inline y no persiste; bloqueo optimista: dos "usuarios" (dos instancias del componente con `version` desactualizado) → el segundo falla con mensaje de recarga |
| **Feature — regeneración sobre `active`** | Confirma que `dryRun` no toca `timetable_slots`; confirmar preview aplica el diff y preserva slots `locked` |
| **Feature — publicación** | Enlace firmado expira según TTL configurado; estudiante solo ve su propia sección; docente solo sus slots |
| **Seeder de dataset sintético** | `TimetableTestSeeder`: genera un lapso con N secciones/M docentes/pevaluaciones aleatorias pero reproducibles (seed fijo) para usar en los tests de arriba y en QA manual |