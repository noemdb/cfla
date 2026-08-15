# SPEC-TIMETABLE-001 — Módulo de Horarios Escolares (Timetabling)

> **⚠️ DOCUMENTO SUPERSEDIDO — referencia de dominio.**
> Este documento (genérico de dominio) queda sustituido para la implementación
> por `blueprint/school-timetable/SPEC-TIMETABLE-001.md`, que ancla el dominio a
> los modelos reales de SAEFL (`pevaluacion`, `pensum`, `asignatura`, `seccion`,
> `grado`, `pestudio`, `peducativo`, `estudiant`, `inscripcion`, `profesor`) y
> añade turnos mañana/tarde, lunes a viernes. Se conserva como registro de la
> inspiración funcional original.

**Autor:** Staff Engineer spec para agente de código
**Contexto:** Módulo nuevo dentro de SAEFL (Laravel 10 + Livewire 3 + MariaDB)
**Inspiración funcional:** Categoría de software "school timetable generator" (patrones de mercado: wizard de configuración, motor de asignación con detección de conflictos, editor drag-and-drop, gestión de sustitutos, exportación). No se copia código, texto ni diseño de ningún producto de terceros — este documento describe **comportamiento funcional genérico del dominio**, no una implementación de referencia.
**Estado:** DRAFT — listo para descomponer en tickets

---

## 1. Objetivo y alcance

Construir un módulo `timetable_` dentro de SAEFL que permita a una institución:

1. Definir la estructura base del horario (períodos, días, bloques, descansos).
2. Cargar/mantener catálogos: materias, secciones/clases, aulas, docentes.
3. Definir "lecciones" (subject + section + docente + carga horaria semanal + restricciones).
4. Generar automáticamente un horario libre de conflictos mediante un algoritmo de asignación con restricciones (CSP).
5. Editar manualmente el resultado con re-validación de conflictos en tiempo real (drag-and-drop).
6. Gestionar ausencias de docentes y asignación de suplentes, con recálculo del horario afectado.
7. Publicar/exportar horarios por sección, por docente y por aula (PDF, vista pública por enlace).
8. Notificar cambios de horario a los actores afectados.

**Fuera de alcance v1:** scheduling a nivel de estudiante individual (IB/electivas por alumno), app móvil nativa, integración con sistemas de terceros vía API pública.

---

## 2. Actores y permisos

Reusar el sistema de roles existente de SAEFL (`is_coordinacion`, `is_leadership`, `is_director`) y añadir:

| Rol | Puede |
|---|---|
| `is_coordinacion` | CRUD completo del módulo, ejecutar generación automática, publicar horarios |
| Docente (`is_teacher`, si existe) | Ver su propio horario, solicitar cambios/ausencias |
| `is_director` | Solo lectura (igual que el resto del sistema) |
| Estudiante/representante | Ver horario de su(s) sección(es) vía vista pública |

---

## 3. Modelo de datos

```
timetable_periods          -- bloques horarios del día (ej. "1ra hora", 07:00-07:45)
  id, name, start_time, end_time, order, is_break (bool)

timetable_calendars        -- un horario por año escolar / lapso
  id, school_year_id (FK), name, status (draft|generating|active|archived)

timetable_rooms            -- aulas/espacios
  id, name, capacity, type (aula|laboratorio|patio|...), features (json)

timetable_subjects         -- puede reusar subjects existentes de SAEFL si ya existen
  id, name, weekly_hours_default, requires_room_type (nullable)

timetable_sections         -- mapea a "clases/grados" existentes en SAEFL (grado+sección)
  id, grade_section_id (FK a tabla existente), student_count

timetable_teacher_availability
  id, teacher_id (FK users), period_id, day_of_week, is_available (bool)

timetable_lessons          -- unidad de asignación: qué se debe programar
  id, calendar_id, subject_id, section_id, teacher_id,
  weekly_hours, room_type_required, priority (int),
  constraints (json: ej. "no_consecutive", "preferred_periods")

timetable_slots            -- resultado: una lección asignada a día+período+aula
  id, lesson_id, calendar_id, day_of_week, period_id, room_id,
  is_manual_override (bool), locked (bool)

timetable_conflicts        -- log de conflictos detectados (para auditoría)
  id, calendar_id, slot_id, type (teacher_double_booked|room_double_booked|
  section_double_booked|availability_violation), resolved (bool)

timetable_absences         -- ausencias de docentes
  id, teacher_id, date_start, date_end, reason, substitute_teacher_id (nullable)

timetable_substitute_assignments
  id, absence_id, slot_id (la clase concreta afectada), substitute_teacher_id,
  status (pending|confirmed|declined), notified_at
```

**Índices críticos:** `(calendar_id, day_of_week, period_id, teacher_id)` único parcial para detectar doble-booking de docente a nivel de BD además de en la capa de aplicación; igual para `room_id` y `section_id`.

---

## 4. Flujo de configuración (wizard) — Livewire multi-step

Mismo patrón de wizard que ya usan (`LessonWizard` en LMS): componente Livewire con pasos, estado persistido en sesión/borrador hasta confirmar.

1. **Paso 1 — Estructura base:** días lectivos, cantidad de períodos, duración, descansos/recreos.
2. **Paso 2 — Aulas:** alta/edición de `timetable_rooms`.
3. **Paso 3 — Materias:** alta/edición o reuso de materias existentes + horas semanales por defecto.
4. **Paso 4 — Docentes y disponibilidad:** importar docentes existentes de SAEFL, marcar disponibilidad por período (grid clickeable).
5. **Paso 5 — Lecciones:** cruzar sección × materia × docente × horas semanales. Soporta importación masiva vía CSV/Excel (reusar `maatwebsite/excel` si ya está en el proyecto).
6. **Paso 6 — Generar:** disparar el job de asignación automática (ver §5). Mostrar progreso vía Livewire polling o Laravel Echo si hay websockets configurados.

---

## 5. Motor de asignación (algoritmo)

**Enfoque recomendado:** backtracking con heurísticas + reparación local, no un solver genérico externo (mantiene el stack 100% PHP, sin dependencias pesadas).

```
Entrada: lista de timetable_lessons pendientes del calendar activo
Salida: asignación completa a (day, period, room) o reporte de infactibilidad parcial

1. Ordenar lecciones por "grado de restricción" (más restringidas primero):
   - docentes con menos disponibilidad
   - materias que requieren tipo de aula específico
   - mayor cantidad de horas semanales
2. Para cada lección, generar dominio de slots candidatos:
   - filtrar por disponibilidad del docente (timetable_teacher_availability)
   - filtrar por aulas compatibles y libres
   - excluir slots ya ocupados por la misma sección
3. Backtracking con forward-checking:
   - al asignar un slot, propagar restricciones (reducir dominio de lecciones no asignadas)
   - si un dominio queda vacío, backtrack
   - límite de tiempo configurable (ej. 30s) -> si se excede, devolver mejor solución parcial
     + lista de lecciones no asignadas para resolución manual
4. Restricciones "soft" (no bloquean pero penalizan, usadas para desempate):
   - evitar huecos en el horario del docente
   - distribuir una materia en días no consecutivos si tiene >1 hora/semana
   - preferir períodos tempranos para materias "core"
5. Persistir resultado en timetable_slots dentro de una transacción DB.
   Registrar cualquier conflicto residual en timetable_conflicts.
```

Implementar como **Job en cola** (`GenerateTimetableJob`), no síncrono en el request — horarios grandes (50+ docentes) pueden tardar. Emitir evento Livewire al finalizar para refrescar la UI.

---

## 6. Editor manual (drag-and-drop)

- Componente Livewire + Alpine.js (o Livewire `wire:sortable` si el proyecto ya usa alguna lib de drag-drop) sobre una grilla días×períodos por sección o por docente.
- Al soltar una lección en un nuevo slot: validación síncrona contra las tres reglas duras (docente, aula, sección no duplicados) **antes** de persistir — mostrar el conflicto inline sin guardar si falla.
- `locked = true` en un slot evita que el motor automático lo reasigne en una regeneración futura (para permitir ajustes manuales que sobrevivan a un "regenerar").

---

## 7. Gestión de ausencias y suplentes

1. Coordinación registra una ausencia (`timetable_absences`) para un docente en un rango de fechas.
2. Sistema identifica automáticamente los `timetable_slots` afectados (join por teacher_id + día de la semana dentro del rango).
3. Sugiere suplentes candidatos: docentes con disponibilidad libre en ese slot y (idealmente) misma área/materia — reusar lógica de disponibilidad del §3.
4. Coordinación confirma → se crea `timetable_substitute_assignments` → notificación al suplente (reusar sistema de notificaciones de SAEFL si existe, o Laravel Notifications con canal mail/database).

---

## 8. Publicación y exportación

- Vista pública de solo lectura por sección/docente vía enlace firmado (`URL::temporarySignedRoute`) — sin necesidad de login, igual al patrón "compartir por enlace" de la categoría.
- Exportación a PDF por sección/docente/aula (reusar librería PDF ya presente en el stack, ej. dompdf/spatie).
- Vista de docente dentro del sistema (si hay login de docentes) mostrando solo su propio horario.

---

## 9. Notificaciones

- Cambio de horario que afecta a un docente o sección → notificación (email + in-app).
- Asignación de suplencia → notificación inmediata al suplente con confirmar/rechazar.
- Regeneración completa del horario → notificación resumen a coordinación.

---

## 10. Criterios de aceptación (para descomponer en tickets)

- [ ] `SPEC-TIMETABLE-001a`: modelos + migraciones + factories de las tablas del §3.
- [ ] `SPEC-TIMETABLE-001b`: wizard de configuración (pasos 1–5) con validación por paso.
- [ ] `SPEC-TIMETABLE-001c`: `GenerateTimetableJob` con el algoritmo del §5 + tests con dataset sintético (mínimo: caso factible pequeño, caso infactible que debe reportar lecciones sin asignar).
- [ ] `SPEC-TIMETABLE-001d`: editor drag-and-drop con validación de conflictos en tiempo real.
- [ ] `SPEC-TIMETABLE-001e`: módulo de ausencias/suplentes + notificaciones.
- [ ] `SPEC-TIMETABLE-001f`: exportación PDF + vista pública por enlace firmado.
- [ ] `SPEC-TIMETABLE-001g`: importación masiva CSV/Excel de lecciones (paso 5 del wizard).

## 11. Riesgos y decisiones abiertas

- **Rendimiento del backtracking** en instituciones grandes (>80 docentes): si el job supera el límite de tiempo configurado, decidir si se acepta solución parcial + resolución manual, o se ofrece ajustar restricciones y reintentar. Recomendado: parcial + manual (más simple, más control para coordinación).
- **Reuso vs. duplicación de catálogos:** decidir si `timetable_subjects`/`timetable_sections` son tablas nuevas o vistas/alias sobre las tablas académicas ya existentes de SAEFL (`pensum`, grados/secciones). Recomendado: FK directa a las tablas existentes para no duplicar fuente de verdad.
- **Multi-lapso:** un `timetable_calendar` por año escolar es lo mínimo; evaluar si se necesita uno por lapso si la carga docente cambia entre lapsos.