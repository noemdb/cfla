# SPEC-TIMETABLE-SHARED-TEACHER-001 — Docente compartido entre lessons

| | |
|---|---|
| **Estado** | Propuesta normativa para implementación |
| **Versión** | 1.0 |
| **Autor** | Staff Engineer |
| **Fecha** | 2026-09-13 |
| **Documento base** | `SPEC-TIMETABLE-001-v2.md` |
| **Alcance** | Step 3, persistencia de lessons, solver, validación manual, publicación, IA y auditoría |

## 1. Objetivo

Permitir que un mismo docente atienda simultáneamente dos grupos/secciones
cuando la coordinación lo autorice explícitamente en las lessons involucradas.

Ejemplo: Lenin Fernández puede impartir Educación Física el martes por la tarde
en 1.er año, sección B, y en el mismo bloque atender 2.º año, sección A.
La situación deja de ser una colisión bloqueante únicamente cuando la regla de
docente compartido está autorizada y validada.

La autorización no significa que el docente esté disponible dos veces ni que
se ignore toda colisión: solo relaja la regla de ocupación del mismo docente
para el par de lessons autorizado.

## 2. Decisión de dominio

Se añade a `timetable_lessons` el campo booleano:

```text
allow_shared_teacher
```

Semántica:

- `false` por defecto: la lesson participa en la regla normal de ocupación
  docente y no puede coincidir con otra lesson del mismo profesor.
- `true`: la lesson puede formar parte de una asignación compartida con otra
  lesson del mismo profesor.
- La excepción solo se concede si **ambas lessons** tienen
  `allow_shared_teacher = true`.
- Ambas lessons deben pertenecer al mismo calendario y tener el mismo
  `profesor_id`.
- La excepción no permite tres o más lessons simultáneas del mismo docente.
- La excepción no permite compartir una lesson consigo misma ni duplicar el
  mismo slot.
- La excepción no relaja restricciones de aula, sección, período, turno,
  disponibilidad ni capacidad del período.

> **Regla de seguridad:** un checkbox individual no puede autorizar por sí solo
> una colisión. El validador debe encontrar dos lessons compatibles y verificar
> que las dos tienen el flag activado.

## 3. Requisitos funcionales

### REQ-ST-001 — Configuración en Step 3

En la fila de cada lesson del Step 3 se debe mostrar un checkbox:

```text
Permitir docente compartido
```

El checkbox debe:

- estar enlazado al estado Livewire de la lesson;
- persistir `true`/`false` en `timetable_lessons`;
- estar desmarcado por defecto;
- mostrar ayuda contextual: “Permite coincidir con otra lesson del mismo
  docente si ambas lessons tienen esta autorización”;
- funcionar junto con el guardado individual, autoguardado, carga, restore,
  importación y replicación de lessons;
- conservar su valor al navegar entre Steps 3–5.

No se debe activar automáticamente por detectar una colisión existente.

### REQ-ST-002 — Compatibilidad con carga académica

La fuente del docente continúa siendo `Pevaluacion.profesor_id`. El nuevo
campo no cambia la carga académica ni modifica `Pevaluacion`.

### REQ-ST-003 — Feedback de configuración

Si una lesson tiene el flag activo pero no existe otra lesson compatible, la UI
puede mostrar una advertencia no bloqueante:

```text
Docente compartido habilitado; todavía no existe otra lesson compatible.
```

Si solo una de dos lessons tiene el flag activo, la colisión debe mostrarse
como bloqueante con una acción clara para activar el flag en ambas o mover una
lesson.

## 4. Modelo de datos y migración

### 4.1 Columna

Crear una migración nueva, no modificar migraciones históricas:

```php
Schema::table('timetable_lessons', function (Blueprint $table): void {
    $table->boolean('allow_shared_teacher')
        ->default(false)
        ->after('is_half_group');
});
```

La migración debe ser segura para una base con datos existentes: todas las
lessons actuales quedan con `false`.

### 4.2 Modelo

`TimetableLesson` debe incluir:

```php
protected $fillable = [
    // ...
    'allow_shared_teacher',
];

protected $casts = [
    // ...
    'allow_shared_teacher' => 'boolean',
];
```

El valor debe aparecer en backups JSON, auditorías, previews y snapshots
cuando estos representen la configuración completa de una lesson.

### 4.3 Compatibilidad

Lectores de payloads antiguos deben interpretar la ausencia del campo como
`false`. Los restores antiguos no pueden habilitar la excepción.

## 5. Contrato de colisión permitida

Para dos lessons `A` y `B`, la colisión de docente en el período `P` es
permitida solo si se cumplen todas las condiciones:

```text
A.id != B.id
A.calendar_id == B.calendar_id
A.profesor_id == B.profesor_id
A.allow_shared_teacher == true
B.allow_shared_teacher == true
A.period_id == B.period_id
```

Además:

```text
cantidad de lessons del docente en P == 2
```

La colisión sigue siendo bloqueante si:

- una de las dos lessons no tiene el flag;
- las lessons tienen docentes distintos;
- existe una tercera lesson del mismo docente en el período;
- una lesson pertenece a una sección completa incompatible con la otra;
- se excede `max_subjects_per_period`;
- se repite un aula dedicada;
- el período es recreo;
- el turno es incompatible bajo `legacy` o una lesson `locked`;
- la disponibilidad del docente no permite el bloque;
- la combinación infringe una regla de sección o grupo estable.

## 6. Solver y contexto de scheduling

La regla debe centralizarse en el mismo predicado usado por el solver, la
validación manual, readiness/publicación y drafts IA. No se deben crear
excepciones independientes en cada capa.

Se recomienda extender `SchedulingContext` con una operación equivalente a:

```php
canShareTeacher(TimetableLesson $candidate, TimetableLesson $existing): bool
```

El método debe devolver `true` únicamente para el par explícitamente
autorizado y debe rechazar una tercera ocupación.

La ocupación docente no debe representarse como un booleano simple. Debe
conservar las lessons ocupantes para poder contar y diagnosticar:

```text
teacherBusy[profesor_id][period_id] = [lesson_id, ...]
```

Al insertar una asignación:

1. cero ocupantes: permitir;
2. una ocupante compatible y ambas autorizadas: permitir;
3. una ocupante incompatible: rechazar;
4. dos ocupantes existentes: rechazar cualquier tercera lesson.

El solver debe priorizar primero las lessons normales y tratar una lesson con
`allow_shared_teacher` como una capacidad excepcional, no como una licencia
para llenar cualquier período ocupado.

## 7. Validación manual, preview y publicación

### REQ-ST-004 — Movimiento y adición manual

Al mover o agregar una lesson en Step 5, el validador debe mostrar:

- `permitida`: “Docente compartido autorizado para estas dos lessons”;
- `bloqueante`: “El docente ya está ocupado y la autorización no está activa
  en ambas lessons”;
- `bloqueante`: “El docente ya tiene dos lessons en este período”.

La operación manual no debe persistir slots automáticamente si el flujo actual
solo modifica el preview.

### REQ-ST-005 — Readiness

`TimetablePublicationReadinessService` debe excluir únicamente las colisiones
que cumplan completamente el contrato de §5. Las demás siguen contando como
conflictos duros.

El reporte debe identificar:

```text
tipo: teacher_shared_allowed | teacher_overlap_blocking
teacher_id
period_id
lesson_ids
```

### REQ-ST-006 — Publicación

La publicación debe volver a validar la regla contra el estado actual y no
confiar solo en el preview. Si el preview cambió o la configuración de una
lesson fue modificada, debe rechazar la publicación y solicitar regenerar o
revisar.

## 8. Replicación, backups e IA

- **Replicación a otra sección:** copiar el flag solo si la coordinación lo
  solicita explícitamente; no asumir que la misma autorización aplica al
  nuevo par.
- **Backup/restore:** incluir `allow_shared_teacher` en `configuration`.
- **CSV/importación:** aceptar una columna opcional normalizada como
  `allow_shared_teacher`; valores desconocidos deben rechazarse, no convertirse
  silenciosamente en `true`.
- **IA:** incluir el flag en el contexto, pero prohibir que la IA lo cambie.
  El modelo puede proponer slots; la autorización permanece bajo control local.
- **Snapshot/hash:** cualquier cambio del flag debe invalidar el snapshot de
  una propuesta IA.

## 9. Auditoría y observabilidad

Registrar en `timetable_change_logs`:

- activación/desactivación del flag;
- usuario;
- calendario, lesson y profesor;
- valor anterior y nuevo;
- motivo/origen (`step3`, `restore`, `import`, `replication`).

Los diagnósticos deben permitir distinguir:

```text
colisión docente normal
colisión docente compartido autorizada
colisión docente compartido incompleta
tercera lesson del mismo docente
```

No se deben ocultar conflictos mediante un mensaje genérico de “horario
válido”.

## 10. Pruebas de aceptación

### Datos y persistencia

- [ ] La migración agrega la columna con default `false`.
- [ ] Lessons existentes quedan con `allow_shared_teacher = false`.
- [ ] El modelo castea el valor como booleano.
- [ ] Guardar Step 3 conserva `true` y `false`.
- [ ] Payload Livewire parcial ausente interpreta `false` sin romper el guardado.

### Reglas

- [ ] Dos lessons del mismo profesor y período, ambas `true`, son permitidas.
- [ ] Una `true` y otra `false` son rechazadas.
- [ ] Ambas `false` son rechazadas.
- [ ] Tres lessons del mismo profesor en el período son rechazadas.
- [ ] Dos lessons de profesores distintos no usan esta excepción.
- [ ] La excepción no permite recreos, aulas duplicadas ni conflictos de sección.
- [ ] Una lesson bloqueada conserva sus restricciones.
- [ ] `legacy` respeta la política dura de turno.

### UI y flujo

- [ ] El checkbox aparece en Step 3 con etiqueta y ayuda.
- [ ] El valor se mantiene al navegar y recargar el wizard.
- [ ] El preview muestra la colisión autorizada como informativa.
- [ ] El preview muestra la colisión incompleta como bloqueante.
- [ ] La publicación revalida la regla.
- [ ] El backup/restore conserva el campo.
- [ ] La réplica no habilita implícitamente el campo.

### Regresión

- [ ] Los conflictos existentes de docente, sección, grupo y aula no cambian
      fuera de la excepción definida.
- [ ] Los tests actuales de medio grupo y grupo estable siguen verdes.
- [ ] Los drafts IA siguen rechazando operaciones que produzcan una tercera
      ocupación o una colisión no autorizada.

## 11. Orden de implementación

1. Migración y modelo.
2. DTO/payload de lessons, persistencia y backups.
3. Checkbox y estado de Step 3.
4. Predicado central de colisión compartida.
5. Solver y disponibilidad.
6. Preview/manual moves/readiness/publicación.
7. Replicación, importación y restore.
8. Contexto y validación IA.
9. Auditoría, documentación y pruebas de aceptación.

## 12. Criterio de finalización

La funcionalidad se considera implementada únicamente cuando:

- el flag se guarda de forma segura y compatible;
- la autorización requiere ambas lessons;
- el solver y todas las validaciones usan la misma regla;
- una tercera lesson continúa siendo bloqueante;
- la publicación revalida el estado actual;
- existen pruebas automatizadas de persistencia, solver, preview, publicación,
  backup/restore y regresión;
- la documentación funcional y el contrato de `SPEC-TIMETABLE-001-v2.md`
  quedan actualizados con referencia a este spec.

