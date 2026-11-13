# ANEXO B — Modelo de datos, ciclo de vida, permisos y PDF

| | |
|---|---|
| **Documento padre** | `PLAN-SECUENCIA-DIDACTICA-001.md` |
| **Estado** | Diseño corregido en F0.5 (2026-11-13). Migraciones F1 ejecutadas en `s2627`; policy y rutas pendientes. |
| **Objetivo** | Definir tablas, reglas de persistencia, permisos y especificación del PDF del plan de clases / secuencia didáctica semanal. |

---

## 0. Decisiones cerradas (2026-11-13)

| Tema | Decisión |
|---|---|
| Unidad del plan | **Por sección y semana**. Un plan corresponde a una `seccion_id` concreta y a una semana (lunes–domingo). |
| Periodo | **La semana se elige dentro del rango derivado de los filtros** de `/app/planning/activities`. La barra no tiene selector de fechas: el rango candidato sale del lapso y de las fechas válidas de las actividades. El usuario no escribe fechas libres. |
| Aprobación | **La realiza el usuario de planificación** (`isPlanner`). No hay aprobador externo en el MVP. |
| Permisos MVP | Planificación y Admin crean/aprueban; Diagnóstico solo lectura; Dirección, Coordinación y Liderazgo quedan fuera del MVP. |
| Universo de actividades | Exactamente el resultado filtrado de la sección y semana, con `universe_hash` de los IDs ordenados. Snapshot inmutable al abrir el wizard. |
| Fuente del contenido | `activities` + `achievements`; sin IA obligatoria. |

Consecuencias directas:

1. El paso 1 resuelve la sección y muestra las **semanas del rango**; el usuario elige una. No hay fechas libres.
2. `period_start` (lunes) y `period_end` (domingo) pertenecen a la semana elegida; el plan dura 7 días o menos si la semana está cortada.
3. Si la sección no está fijada, se elige primero y el rango se recalcula para esa sección.
4. El plan no se crea si el resultado filtrado está vacío o no tiene fechas válidas.
5. El campo `approved_by` se llena con el usuario de planificación que aprueba; puede ser el mismo que creó el borrador.
6. Las fuentes son a nivel de plan; la atribución por sesión usa `didactic_plan_sessions.source_activity_id`.

---

## 1. Diagrama lógico

```
pestudios ──< grados ──< seccions
     │            │          │
     └────────────┴──────────┴──< didactic_plans >──< didactic_plan_sessions >──< didactic_plan_indicators
                                        │                        │
                                        ├──< didactic_plan_indicators
                                        └──< didactic_plan_sources >── activities >── pevaluacions

users ──< didactic_plans (created_by, updated_by, approved_by)
didactic_plans ──> didactic_plans (superseded_by_id)
```

Relaciones:

- `DidacticPlan` pertenece a `Pestudio`, `Grado`, `Seccion`, `Lapso` (opcional) y `User`.
- `DidacticPlan` tiene muchas `DidacticPlanSession`.
- `DidacticPlan` tiene muchos `DidacticPlanIndicator`.
- `DidacticPlanSession` tiene muchos `DidacticPlanIndicator`.
- `DidacticPlan` tiene muchos `DidacticPlanSource`.
- `DidacticPlanSource` pertenece a `Activity` y `Pevaluacion`.
- `DidacticPlanSession` puede referenciar una `Activity` de origen.

Las fuentes son **a nivel de plan**: una actividad puede originar varias sesiones y
la atribución se hace con `didactic_plan_sessions.source_activity_id`. Por eso
`didactic_plan_sources` no guarda `didactic_plan_session_id`.

---

## 2. Tabla `didactic_plans`

### 2.1 Columnas

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| `id` | bigint unsigned | No | auto | PK. |
| `pestudio_id` | int unsigned | No | — | FK → `pestudios` (`restrict`). |
| `grado_id` | int unsigned | No | — | FK → `grados` (`restrict`). |
| `seccion_id` | int unsigned | No | — | FK → `seccions` (`restrict`). Unidad del plan. |
| `lapso_id` | int unsigned | Sí | null | FK → `lapsos` (`nullOnDelete`); contexto del filtro. |
| `period_start` | date | No | — | Lunes de la semana elegida. |
| `period_end` | date | No | — | Domingo de la semana elegida; ≤ `period_start + 6 días`. |
| `axis` | varchar(150) | No | `Alimentación, cultura e identidad` | Eje articulador. |
| `project_title` | varchar(180) | Sí | null | Proyecto o título de la secuencia. |
| `objective` | text | Sí | null | Objetivo general del plan. |
| `ods` | json | Sí | null | Códigos/nombres de ODS del plan. |
| `status` | varchar(20) | No | `draft` | `draft`, `approved`, `superseded`, `archived`. |
| `version` | smallint unsigned | No | 1 | Versión del plan. |
| `source_mode` | varchar(20) | No | `filtered_activities` | `filtered_activities`, `manual`, `mixed`. |
| `filter_snapshot` | json | No | — | Instantánea de filtros que originó el plan. |
| `filter_hash` | char(64) | No | — | SHA-256 de la instantánea normalizada. |
| `universe_hash` | char(64) | No | — | SHA-256 de los IDs ordenados del universo filtrado. |
| `filtered_count` | smallint unsigned | No | 0 | Actividades del universo filtrado. |
| `selected_count` | smallint unsigned | No | 0 | Actividades usadas como fuente. |
| `notes` | text | Sí | null | Notas internas. |
| `pdf_notes` | text | Sí | null | Notas que se imprimen. |
| `checksum` | char(64) | Sí | null | Hash del contenido persistido (idempotencia). |
| `lock_version` | int unsigned | No | 0 | Bloqueo optimista. |
| `approved_key` | varchar(64) | Sí | null | Clave de vigencia; única cuando `status = approved`. |
| `created_by` | int unsigned | No | — | FK → `users` (`restrict`). |
| `updated_by` | int unsigned | Sí | null | FK → `users` (`nullOnDelete`). |
| `approved_by` | int unsigned | Sí | null | FK → `users` (`nullOnDelete`); usuario de planificación. |
| `approved_at` | timestamp | Sí | null | Fecha/hora de aprobación. |
| `superseded_by_id` | bigint unsigned | Sí | null | FK → `didactic_plans` (`nullOnDelete`). |
| `created_at` | timestamp | Sí | null | — |
| `updated_at` | timestamp | Sí | null | — |
| `deleted_at` | timestamp | Sí | null | Soft delete (solo borradores). |

### 2.2 Índices

| Índice | Columnas | Tipo | Propósito |
|---|---|---|---|
| `pk_didactic_plans` | `id` | PK | — |
| `uq_didactic_plan_version` | `seccion_id`, `lapso_id`, `period_start`, `period_end`, `version` | Único | Evita versiones duplicadas. |
| `uq_didactic_plan_approved_key` | `approved_key` | Único | Un solo plan vigente por sección + lapso + semana (los `NULL` no colisionan). |
| `idx_didactic_plan_context` | `pestudio_id`, `grado_id`, `seccion_id`, `period_start` | Normal | Búsqueda por contexto. |
| `idx_didactic_plan_status` | `status`, `period_start` | Normal | Listados y vigencia. |
| `idx_didactic_plan_lapso` | `lapso_id` | Normal | Filtro por momento. |
| `idx_didactic_plan_creator` | `created_by`, `approved_by` | Normal | Bandeja del planificador. |

### 2.3 Regla de unicidad del vigente

MariaDB no tiene índices parciales. Se usa una columna calculada por la aplicación:

```
approved_key = "{seccion_id}:{lapso_id}:{period_start}:{period_end}"
```

- Al aprobar: `status = approved` y se escribe `approved_key`.
- Al crear una nueva versión: la anterior pasa a `superseded` y su `approved_key` se pone en `NULL`.
- Los borradores tienen `approved_key = NULL`.
- El índice único sobre `approved_key` garantiza un solo vigente aunque haya concurrencia.

Complemento:

1. Unicidad de respaldo `seccion_id + lapso_id + period_start + period_end + version`.
2. La versión se calcula dentro de una transacción con `lockForUpdate()` sobre la misma clave.
3. Si dos usuarios guardan el mismo borrador, `lock_version` detecta el conflicto y se pide recargar.
4. Se rechaza un plan cuyo rango se solape con otro `approved` de la misma sección y lapso.
5. Un solo borrador por clave; se reutiliza y se actualiza.

> `shift` queda fuera del MVP: `seccions` no tiene turno. Si se necesita, se
> agrega como decisión posterior, no como columna preparada.

### 2.4 `filter_snapshot` — contrato

```json
{
  "search": "alimentación",
  "pestudio_id": 3,
  "grado_id": 12,
  "seccion_id": 45,
  "lapso_id": 2,
  "profesor_id": 88,
  "status_activities": "SI",
  "filter_status": "approved",
  "filter_observations": false,
  "activity_search": null,
  "week": {
    "period_start": "2026-11-17",
    "period_end": "2026-11-21"
  },
  "range_origin": "activities",
  "captured_at": "2026-11-13T12:40:00-04:00"
}
```

Reglas:

- La consulta del wizard **no pagina**: obtiene todo el universo filtrado de la sección y semana.
- No existe `date_from`/`date_to` en la barra. El rango candidato se deriva de las fechas válidas de las actividades y, como respaldo, del lapso.
- `week` guarda la semana elegida. Se valida que esté contenida en el rango candidato.
- `activity_search` es una búsqueda del wizard (`topic`, `thematic`, `teaching`, `learning`, `observations`) acotada al universo; no modifica la búsqueda de la página.
- `filter_hash` = SHA-256 del JSON con claves ordenadas y sin `captured_at`, para comparar filtros entre sesiones.
- `universe_hash` = SHA-256 de los IDs de actividad ordenados ascendentemente. Prueba pertenencia; `filter_hash` **no** la prueba.
- La instantánea es inmutable: si el usuario cambia filtros con el modal abierto, el plan conserva la original. Se avisa en el pie del modal.

### 2.5 Derivación de la semana

Orden de precedencia del **rango candidato**:

1. **Fechas válidas de las actividades del universo** (`MIN(activities.finicial)`–`MAX(activities.ffinal)`), excluyendo `0000-00-00`, nulos y fechas fuera del lapso.
2. **Rango del lapso** (`lapsos.finicial`–`lapsos.ffinal`), solo como respaldo.

Sobre ese rango:

- se generan semanas lunes–domingo;
- la primera y la última pueden estar cortadas;
- el usuario elige una semana y el plan queda limitado a ella;
- por defecto se propone la semana con más actividades afines al eje;
- el plan nunca supera 7 días.

Reglas:

- `period_end` nunca es menor que `period_start` y la diferencia máxima es 6 días.
- Si no hay ninguna fecha válida, el wizard no permite continuar: “El filtro no define una semana con fechas; ajusta los filtros”.
- Al cambiar de sección se **recalcula** el rango y las semanas: el `MIN/MAX` de todas las secciones no representa a una sola.
- El usuario **no** puede escribir fechas libres ni fuera del rango candidato.

---

## 3. Tabla `didactic_plan_sessions`

### 3.1 Columnas

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| `id` | bigint unsigned | No | auto | PK. |
| `didactic_plan_id` | bigint unsigned | No | — | FK → `didactic_plans` (`cascadeOnDelete`). |
| `date` | date | No | — | Fecha de la sesión; dentro de la semana elegida. |
| `day_index` | tinyint unsigned | No | — | 1 = lunes … 7 = domingo. |
| `sort_order` | smallint unsigned | No | 0 | Orden dentro del día. |
| `title` | varchar(180) | No | — | Tema/título. |
| `thematic` | varchar(180) | Sí | null | Integración curricular. |
| `objective` | text | Sí | null | Aprendizaje esperado. |
| `start` | text | Sí | null | INICIO. |
| `development` | text | Sí | null | DESARROLLO. |
| `close` | text | Sí | null | CIERRE. |
| `resources` | text | Sí | null | Referentes y recursos. |
| `evaluation_instrument` | varchar(60) | Sí | null | Lista de cotejo, escala, rúbrica, etc. |
| `evaluation_description` | text | Sí | null | Actividad evaluativa. |
| `observations` | text | Sí | null | Sistematización / notas. |
| `ods` | json | Sí | null | ODS de la sesión. |
| `is_non_teaching` | tinyint(1) | No | 0 | Día no lectivo / efeméride. |
| `source_activity_id` | bigint unsigned | Sí | null | FK → `activities` (`nullOnDelete`). |
| `source_field` | varchar(40) | Sí | null | Campo de origen (`teaching`, `description`, …). |
| `source_excerpt` | text | Sí | null | Extracto usado como evidencia. |
| `source_checksum` | char(64) | Sí | null | Hash del extracto de origen. |
| `confidence` | decimal(4,2) | Sí | null | 0.00–1.00. |
| `created_at` | timestamp | Sí | null | — |
| `updated_at` | timestamp | Sí | null | — |

### 3.2 Índices

| Índice | Columnas | Tipo | Propósito |
|---|---|---|---|
| `pk_didactic_plan_sessions` | `id` | PK | — |
| `idx_dps_plan_date` | `didactic_plan_id`, `date`, `sort_order` | Normal | Render ordenado y agrupado por día. |
| `idx_dps_source` | `source_activity_id` | Normal | Trazabilidad inversa. |

### 3.3 Reglas

- `date` debe estar entre `period_start` y `period_end` del plan. Se valida en aplicación y en `DidacticPlanService`. Es obligatoria: al dividir una sesión, la segunda exige una fecha dentro de la semana antes de guardar.
- Un plan puede tener 0 sesiones solo mientras sea borrador; para aprobar se exige al menos una sesión lectiva.
- La misma actividad puede originar varias sesiones (por ejemplo, si abarca varios días), pero cada sesión conserva su `source_activity_id`.
- `is_non_teaching = 1` permite representar feriados dentro de la semana sin inventar contenido.

---

## 4. Tabla `didactic_plan_indicators`

### 4.1 Columnas

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| `id` | bigint unsigned | No | auto | PK. |
| `didactic_plan_id` | bigint unsigned | No | — | FK → `didactic_plans` (`cascadeOnDelete`). |
| `didactic_plan_session_id` | bigint unsigned | Sí | null | FK → `didactic_plan_sessions` (`cascadeOnDelete`). `NULL` = indicador general del plan. |
| `name` | varchar(191) | No | — | Texto del indicador. |
| `weighting` | decimal(4,0) | Sí | null | Ponderación opcional (alineada con `achievements.weighting`). |
| `is_quantitative` | tinyint(1) | No | 0 | Equivale a `status_quantitative_weighting`. |
| `origin` | varchar(20) | No | `manual` | `activity`, `manual`, `suggested`. |
| `source_achievement_id` | bigint unsigned | Sí | null | FK → `achievements` (`nullOnDelete`). |
| `sort_order` | smallint unsigned | No | 0 | Orden de presentación. |
| `created_at` | timestamp | Sí | null | — |
| `updated_at` | timestamp | Sí | null | — |

### 4.2 Índices

| Índice | Columnas | Tipo | Propósito |
|---|---|---|---|
| `pk_didactic_plan_indicators` | `id` | PK | — |
| `idx_dpi_plan` | `didactic_plan_id`, `sort_order` | Normal | Indicadores generales. |
| `idx_dpi_session` | `didactic_plan_session_id`, `sort_order` | Normal | Indicadores por sesión. |
| `idx_dpi_source` | `source_achievement_id` | Normal | Trazabilidad. |

### 4.3 Reglas

- `origin = activity` exige `source_achievement_id`.
- `origin = manual` lo redacta el usuario y no requiere fuente.
- `origin = suggested` solo aparece si se habilita IA; en el MVP no se usa.
- No se duplican indicadores por sesión con el mismo `name` normalizado (trim + minúsculas + sin tildes).
- Si una actividad no tiene `achievements`, el wizard permite indicadores manuales; la aprobación exige al menos uno por sesión lectiva **o** una justificación registrada en `notes`.

---

## 5. Tabla `didactic_plan_sources`

### 5.1 Columnas

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| `id` | bigint unsigned | No | auto | PK. |
| `didactic_plan_id` | bigint unsigned | No | — | FK → `didactic_plans` (`cascadeOnDelete`). |
| `activity_id` | bigint unsigned | Sí | null | FK → `activities` (`nullOnDelete`). |
| `pevaluacion_id` | bigint unsigned | Sí | null | FK → `pevaluacions` (`nullOnDelete`). |
| `activity_checksum` | char(64) | Sí | null | Hash de los campos usados. |
| `fields_used` | json | Sí | null | `["topic","teaching","description","observations"]`. |
| `excerpt` | text | Sí | null | Extracto textual usado como fuente. |
| `included` | tinyint(1) | No | 1 | 1 = usado en el plan; 0 = visto pero descartado. |
| `created_at` | timestamp | Sí | null | — |
| `updated_at` | timestamp | Sí | null | — |

### 5.2 Índices

| Índice | Columnas | Tipo | Propósito |
|---|---|---|---|
| `pk_didactic_plan_sources` | `id` | PK | — |
| `uq_dps_plan_activity` | `didactic_plan_id`, `activity_id` | Único | Evita duplicar la misma fuente. |
| `idx_dps_activity` | `activity_id` | Normal | Detectar planes afectados por cambios. |
| `idx_dps_pevaluacion` | `pevaluacion_id` | Normal | Consultas por plan de evaluación. |

### 5.3 Reglas

- `included = 0` documenta actividades del universo que se revisaron y se descartaron; sirve como evidencia de auditoría, no como contenido.
- El `activity_checksum` permite detectar que la actividad cambió después de aprobar el plan.
- `activity_id` es `null` si la actividad se elimina; el extracto permanece para no perder historia.
- No se guarda `didactic_plan_session_id`: la relación fuente → sesión se resuelve con `didactic_plan_sessions.source_activity_id`.

---

## 6. Ciclo de vida

### 6.1 Estados

| Estado | Editable | PDF | Acciones |
|---|---|---|---|
| `draft` | Sí | No | Editar, aprobar, eliminar. |
| `approved` | No | Sí | Descargar PDF, crear nueva versión, archivar. |
| `superseded` | No | Sí (histórico) | Ver, descargar PDF histórico. |
| `archived` | No | No por defecto | Ver, restaurar como borrador si se permite. |

### 6.2 Transiciones

| Desde | Acción | Hacia | Condiciones |
|---|---|---|---|
| — | Crear desde wizard | `draft` | Sección y semana válidas dentro del rango filtrado. |
| `draft` | Guardar borrador | `draft` | Contexto y semana válidos; permanece en el paso 4. |
| `draft` | Aprobar | `approved` | Checklist completo o justificación; usuario `isPlanner`. |
| `draft` | Eliminar | eliminado | Solo si nunca fue aprobado. |
| `approved` | Crear nueva versión | `draft` (v+1) | Copia profunda; el vigente sigue aprobado. |
| `approved` | Archivar | `archived` | Permiso de planificación/admin; auditoría. |
| `approved` | Aprobar nueva versión | `superseded` | La nueva versión pasa a `approved` y toma el `approved_key`. |
| `archived` | Restaurar | `draft` | Opcional; nueva versión. |

Reglas:

- `approved` es inmutable. Cualquier cambio exige “Crear nueva versión”.
- Solo puede existir un `approved` por `seccion_id + lapso_id + period_start + period_end`.
- Si el plan aprobado cambia de semana, se trata como un plan nuevo: no se reutiliza la clave.
- La sección, el lapso y la semana no se editan en una nueva versión.

### 6.3 Aprobación por planificación

- El botón **Aprobar** solo aparece si `auth()->user()` es admin o planificador y la policy `approve` lo permite.
- No se pide seleccionar aprobador: el aprobador es el usuario autenticado.
- Se registra `approved_by`, `approved_at`, `approved_key` y la entrada de bitácora.
- Se permite que `created_by = approved_by`. No hay segregación de funciones en el MVP; si se requiere después, se agrega una regla de policy sin cambiar el modelo.
- La UI muestra “Aprobado por: {nombre} · {role_label} · {fecha} · v{versión}”.
- Si el usuario no es planificación ni admin, el botón no se muestra y el endpoint devuelve 403.

### 6.4 Bloqueo optimista y concurrencia

1. Al abrir el borrador, el cliente recibe `lock_version`.
2. Al guardar, se envía `lock_version` y `checksum`.
3. El servicio bloquea la fila (`lockForUpdate`), compara y:
   - si coincide, incrementa `lock_version` y guarda;
   - si no, responde con conflicto y opción “recargar y comparar”.
4. Dos aprobaciones simultáneas de la misma clave: la segunda falla por `uq_didactic_plan_approved_key` y se informa “Ya existe un plan aprobado para esta sección y semana”.

### 6.5 Desincronización con la fuente

- El plan guarda `source_checksum` por sesión y `activity_checksum` por fuente.
- Si una actividad fuente cambia, el plan **no** se modifica.
- “Revisar fuentes” compara hashes y muestra un diff resumido campo por campo.
- “Crear nueva versión desde fuentes actualizadas” es una acción explícita del usuario.
- No hay sincronización automática ni jobs en el MVP.

---

## 7. Reglas de persistencia

### 7.1 Creación de borrador (transacción)

1. Validar que la sección, el lapso y la semana provienen del snapshot y que la semana está dentro del rango candidato.
2. Reejecutar `FilteredActivitiesQuery` para la sección y semana y comparar el `universe_hash`; alternativamente, validar pertenencia con el `universe_hash` guardado. **No** usar `filter_hash` para probar pertenencia.
3. Buscar el borrador único con la misma clave (`seccion_id + lapso_id + period_start + period_end`):
   - si existe, actualizar y registrar `updated_by`;
   - si no, crear con `version = 1`.
4. Reemplazar sesiones, indicadores y fuentes en una transacción controlada.
5. Recalcular `checksum`, `filtered_count` y `selected_count`.
6. Registrar auditoría.

### 7.2 Aprobación (transacción)

1. `lockForUpdate()` sobre la clave del plan.
2. Validar checklist.
3. Calcular la siguiente versión si corresponde.
4. Marcar la versión anterior como `superseded` y limpiar su `approved_key`.
5. Escribir `status`, `approved_by`, `approved_at`, `approved_key`.
6. Auditar y registrar evento `didactic_plan.approved`.

### 7.3 Eliminación

- Solo borradores con `deleted_at IS NULL`.
- Soft delete del plan; sesiones, indicadores y fuentes se conservan para auditoría hasta purga manual.
- Un plan aprobado no se elimina; se archiva.

### 7.4 Validaciones de dominio

| Regla | Nivel |
|---|---|
| `period_end ≥ period_start` y diferencia ≤ 6 días | Aplicación. |
| `period_start` es lunes y `period_end` domingo (o corte del rango) | Servicio. |
| `date` de sesión dentro de la semana | Aplicación. |
| Una sesión lectiva por fecha mínima al aprobar | Servicio. |
| Indicador con `origin = activity` requiere fuente | Servicio. |
| `selected_count ≤ filtered_count` | Servicio. |
| Actividad de otra sección o fuera del `universe_hash` no se persiste como fuente | Servicio. |
| No duplicar `(plan, activity_id)` | Índice único. |
| No duplicar vigente por clave | Índice único. |
| No solapar el rango con otro `approved` de la misma sección y lapso | Servicio + consulta. |

---

## 8. Permisos

### 8.1 Matriz MVP

| Acción | Planificación | Admin | Diagnóstico | Dirección / Coordinación / Liderazgo |
|---|---|---|---|---|
| Ver | Sí | Sí | Solo lectura | No (MVP) |
| Crear/editar borrador | Sí | Sí | No | No |
| Aprobar | Sí | Sí | No | No |
| Archivar | Sí | Sí | No | No |
| Descargar PDF | Sí | Sí | Sí | No |

### 8.2 Reglas de autorización

- Rutas bajo el grupo `planning` (`auth` + `isPlanner`) y policy `DidacticPlanPolicy`.
- `view`: admin, planificador o diagnóstico (este último solo lectura). En el MVP no hay alcance por sección.
- `create`: `isPlanner` (admin o planificador).
- `update`: `draft` + `isPlanner`.
- `approve`: `isPlanner` + estado `draft`.
- `downloadPdf`: `view` + estado `approved`/`superseded`. Un borrador no genera PDF oficial.
- `archive`: `isPlanner`.
- Nunca confiar en `seccion_id` del cliente: recalcular la coherencia `seccion → grado → pestudio`.

> `IsPlanner.php` permite hoy admin, planificador y diagnóstico. Diagnóstico solo
> puede leer y descargar; las acciones de escritura se bloquean en la policy. Si
> más adelante se requiere alcance por sección, se implementa y se prueba con 403.

### 8.3 Auditoría (contrato `Auditable`)

`DidacticPlan` declara allowlist:

```
id, pestudio_id, grado_id, seccion_id, lapso_id, period_start, period_end,
axis, project_title, status, version, source_mode, filter_hash, universe_hash,
filtered_count, selected_count, approved_by, approved_at, superseded_by_id
```

Se auditan: crear, guardar borrador, aprobar, archivar, nueva versión y descargar PDF (evento de lectura). No se audita cada pulsación ni el contenido completo de las actividades.

---

## 9. PDF imprimible

### 9.1 Ruta y controlador

| Elemento | Valor |
|---|---|
| Ruta | `GET /app/planning/activities/didactic-plans/{didacticPlan}/pdf` |
| Nombre | `app.planning.activities.didactic-plans.pdf` |
| Controlador | `App\Http\Controllers\Planning\DidacticPlanPdfController` |
| Métodos | `stream()` (vista previa), `download()` (descarga) |
| Middleware | `auth`, `isPlanner`, policy `view` |
| Motor | DomPDF, igual que `ActivityPdfController` |

Reglas:

- Solo se genera PDF de un plan `approved` o `superseded`. Un `draft` devuelve 403; no hay marca de agua en el MVP.
- El nombre del archivo es `PSD-{seccion}-{period_start}-v{version}.pdf`.
- No se generan PDFs en cola en el MVP; la generación es síncrona.
- Se registra el evento de descarga con usuario, plan y versión.
- Carta vertical siempre en el MVP; no se cambia de orientación automáticamente.

### 9.2 Vista

`resources/views/pdfs/planning/didactic-plans/plan.blade.php`.

Estructura:

1. **Encabezado institucional**
   - nombre de la institución (`Institucion`), RIF, dirección y teléfono si existen;
   - título “PLAN DE CLASES / SECUENCIA DIDÁCTICA”;
   - subtítulo “Educación Primaria · {grado} {sección} · {semana}”.
2. **Bloque de contexto**
   - plan de estudio, grado y sección;
   - semana elegida (`period_start` – `period_end`);
   - momento/lapso;
   - eje articulador;
   - proyecto/título;
   - objetivo general;
   - ODS del plan.
3. **Sesiones** (una fila o bloque por sesión):
   - fecha y día;
   - título/tema;
   - objetivo/aprendizaje;
   - INICIO;
   - DESARROLLO;
   - CIERRE;
   - evaluación: instrumento + descripción;
   - indicadores de logro;
   - ODS de la sesión;
   - recursos/referentes.
4. **Notas para el PDF** (`pdf_notes`).
5. **Firmas**
   - “Elaborado por”: nombre y `role_label` de `created_by`;
   - “Aprobado por (Planificación)”: nombre, `role_label` y fecha de `approved_by`;
   - sin línea de coordinación/dirección en el MVP.
6. **Pie de página**
   - código `PSD-{seccion}-{period_start}-v{version}`;
   - fecha/hora de generación;
   - “Documento generado por SAEFL”;
   - número de página y total;
   - `universe_hash` corto (8 caracteres) para trazabilidad.

### 9.3 Maquetación

| Aspecto | Regla |
|---|---|
| Orientación | Carta vertical siempre en el MVP. |
| Fuente | Helvetica 6–10 pt, coherente con los PDF existentes. |
| Colores | Grises y un acento institucional discreto; sin imágenes remotas. |
| Saltos de página | Evitar cortar una sesión; `page-break-inside: avoid` por bloque. |
| Sesiones múltiples | 2–3 sesiones por página; segunda página de anexos si hay más. |
| Texto largo | `word-wrap`, tamaños reducidos y anexo de fuentes. |
| Seguridad | Todo el texto escapado con `{{ }}`; nunca `{!! !!}` con datos de usuario. |
| Rendimiento | `enable_font_subsetting`, `dpi 72`, Helvetica, sin SVG pesados. |

### 9.4 Variantes

| Variante | Contenido | Uso |
|---|---|---|
| Oficial | Contexto + sesiones + evaluación + indicadores + firmas | Archivo y firma física. |
| Resumen | Contexto + títulos + ODS + una línea por sesión | Cartelera o dirección. |
| Anexo | Fuentes, extractos y hashes | Auditoría interna; no se imprime por defecto. |

### 9.5 Qué no debe contener el PDF

- Datos personales de estudiantes o representantes.
- Menús, dietas, calorías o inventario de comedor.
- Contenido de lecciones LMS.
- Textos inventados sin fuente o sin marca de “sugerido”.
- Comentarios internos de revisión (`activities.comments`), salvo que el usuario los incorpore expresamente.

---

## 10. Migración y despliegue

> El proyecto está en producción. Aplicar `cfla-cambios-produccion`: avisar, confirmar y no usar comandos destructivos.

### 10.1 Migraciones

- Cuatro migraciones **aditivas**:
  - `2026_11_13_000001_create_didactic_plans_table.php`
  - `2026_11_13_000002_create_didactic_plan_sessions_table.php`
  - `2026_11_13_000003_create_didactic_plan_indicators_table.php`
  - `2026_11_13_000004_create_didactic_plan_sources_table.php`
- No se modifican `activities`, `pevaluacions`, `achievements` ni `seccions`.
- Tipos de FK alineados con la base `s2627`:
  - `pestudios.id`, `grados.id`, `seccions.id`, `lapsos.id`, `users.id` → `int unsigned`;
  - `activities.id`, `pevaluacions.id`, `achievements.id` → `bigint unsigned`.
- `ON DELETE` explícito:
  - plan → sesiones: `cascade`;
  - plan → indicadores: `cascade`;
  - sesión → indicadores: `cascade`;
  - plan → fuentes: `cascade`;
  - `source_activity_id` → `activities`: `nullOnDelete`;
  - `source_achievement_id` → `achievements`: `nullOnDelete`;
  - `pevaluacion_id` → `pevaluacions`: `nullOnDelete`;
  - `created_by` → `users`: `restrict`.
- Sin `migrate:fresh`, `db:wipe`, `schema:dump --prune` ni rollbacks masivos.

### 10.2 Orden de despliegue

1. Backup de base y prueba de restauración.
2. Desplegar código con `DIDACTIC_PLANS_ENABLED=false`.
3. `php8.2 artisan migrate` (solo migraciones nuevas).
4. Verificar tablas, índices y FKs.
5. Activar la bandera para un usuario de planificación (canario).
6. Crear un plan real desde filtros de 2.º A y aprobarlo.
7. Descargar e imprimir el PDF; validar firmas y saltos de página.
8. Activar para el resto de planificación.
9. Monitorear 48 horas.

### 10.3 Rollback

- Desactivar la bandera: oculta botón y endpoints sin tocar datos.
- Las tablas nuevas pueden quedar vacías; no afectan al resto.
- No borrar tablas en producción sin backup verificado y ventana de mantenimiento.
- Si se revierte la migración, hacerlo solo con backup y confirmación explícita.

### 10.4 Datos existentes

- Sin backfill masivo.
- Los planes históricos, si se quieren importar, van por comando aparte, revisado y auditado.
- No se migran menús ni datos de comedor porque no forman parte del alcance.

---

## 11. Pruebas del modelo y del PDF

### 11.1 Persistencia

- Crear plan con sección y semana válidas dentro del rango.
- Rechazar plan sin sección.
- Rechazar plan con semana invertida o de más de 7 días.
- Rechazar sesión fuera de la semana.
- Rechazar fuente de otra sección.
- Rechazar actividad fuera del `universe_hash`; comprobar que `filter_hash` no se usa para pertenencia.
- Rechazar solapamiento con otro plan aprobado de la misma sección y lapso.
- Un solo borrador por clave.
- Unicidad de versión.
- Unicidad de vigente con dos aprobaciones simultáneas.
- Conflicto de `lock_version`.
- Nueva versión copia sesiones, indicadores y fuentes.
- Soft delete solo en borradores.
- Auditoría de creación, aprobación, archivado y descarga.

### 11.2 PDF

- Encabezado institucional presente.
- Contexto con sección, semana y eje.
- Cada sesión con inicio, desarrollo, cierre, evaluación, indicadores y ODS.
- Textos largos y acentos sin romper.
- Saltos de página sin cortar sesiones.
- Firmas de elaboración y aprobación con nombre y `role_label`.
- Sin datos de estudiantes.
- `draft` devuelve 403; sin marca de agua.
- 403 para usuario sin permiso.

### 11.3 Criterios de aceptación del anexo

1. Existen las cuatro tablas con índices y FKs correctos.
2. El plan siempre tiene sección, lapso y una semana derivada del rango filtrado.
3. Solo hay un plan aprobado por sección, lapso y semana; sin solapamientos.
4. La aprobación la registra un usuario de planificación y queda en `approved_by`.
5. El PDF oficial contiene la secuencia didáctica, la evaluación, los indicadores, los ODS y las firmas.
6. Todo cambio queda auditado.
7. La migración es aditiva y reversible solo con backup.

---

## 12. Anexos relacionados

- [`PLAN-SECUENCIA-DIDACTICA-001.md`](PLAN-SECUENCIA-DIDACTICA-001.md): plan maestro.
- [`ANEXO-A-EXTRACCION.md`](ANEXO-A-EXTRACCION.md): contrato de filtros, mapeo y consolidación.
- [`README.md`](README.md): índice y resumen.
