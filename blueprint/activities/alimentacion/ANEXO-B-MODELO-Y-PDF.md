# ANEXO B — Modelo de datos, ciclo de vida, permisos y PDF

| | |
|---|---|
| **Documento padre** | `PLAN-SECUENCIA-DIDACTICA-001.md` |
| **Estado** | Diseño. Sin código ni migraciones ejecutadas. |
| **Objetivo** | Definir tablas, reglas de persistencia, permisos y especificación del PDF del plan de clases / secuencia didáctica. |

---

## 0. Decisiones cerradas (2026-11-13)

| Tema | Decisión |
|---|---|
| Unidad del plan | **Por sección**. Un plan corresponde a una `seccion_id` concreta. |
| Periodo | **El rango de fechas proviene de los filtros** de `/app/planning/activities`. El wizard no inventa ni edita libremente una semana. |
| Aprobación | **La realiza el usuario de planificación** (`isPlanner`). No hay aprobador externo en el MVP. |
| Universo de actividades | Exactamente el resultado filtrado. Snapshot inmutable al abrir el wizard. |
| Fuente del contenido | `activities` + `achievements`; sin IA obligatoria. |

Consecuencias directas:

1. No existe selector de semana en el paso 1; existe una **confirmación de periodo** de solo lectura.
2. `period_start` y `period_end` se calculan desde la instantánea de filtros (o desde las actividades filtradas si la barra no expone fechas).
3. El plan no se crea si el resultado filtrado está vacío o no tiene fechas.
4. El campo `approved_by` se llena con el usuario de planificación que aprueba; puede ser el mismo que creó el borrador.

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
| `period_start` | date | No | — | Inicio del rango filtrado. |
| `period_end` | date | No | — | Fin del rango filtrado. ≥ `period_start`. |
| `shift` | varchar(20) | No | `single` | `single`, `morning`, `afternoon`. |
| `axis` | varchar(150) | No | `Alimentación, cultura e identidad` | Eje articulador. |
| `project_title` | varchar(180) | Sí | null | Proyecto o título de la secuencia. |
| `objective` | text | Sí | null | Objetivo general del plan. |
| `ods` | json | Sí | null | Códigos/nombres de ODS del plan. |
| `status` | varchar(20) | No | `draft` | `draft`, `approved`, `superseded`, `archived`. |
| `version` | smallint unsigned | No | 1 | Versión del plan. |
| `source_mode` | varchar(20) | No | `filtered_activities` | `filtered_activities`, `manual`, `mixed`. |
| `filter_snapshot` | json | No | — | Instantánea de filtros que originó el plan. |
| `filter_hash` | char(64) | No | — | SHA-256 de la instantánea normalizada. |
| `filtered_count` | smallint unsigned | No | 0 | Actividades del resultado filtrado. |
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
| `uq_didactic_plan_version` | `seccion_id`, `period_start`, `period_end`, `shift`, `version` | Único | Evita versiones duplicadas. |
| `uq_didactic_plan_approved_key` | `approved_key` | Único | Un solo plan vigente por sección + periodo + turno (los `NULL` no colisionan). |
| `idx_didactic_plan_context` | `pestudio_id`, `grado_id`, `seccion_id`, `period_start` | Normal | Búsqueda por contexto. |
| `idx_didactic_plan_status` | `status`, `period_start` | Normal | Listados y vigencia. |
| `idx_didactic_plan_lapso` | `lapso_id` | Normal | Filtro por momento. |
| `idx_didactic_plan_creator` | `created_by`, `approved_by` | Normal | Bandeja del planificador. |

### 2.3 Regla de unicidad del vigente

MariaDB no tiene índices parciales. Se usa una columna calculada por la aplicación:

```
approved_key = "{seccion_id}:{period_start}:{period_end}:{shift}"
```

- Al aprobar: `status = approved` y se escribe `approved_key`.
- Al crear una nueva versión: la anterior pasa a `superseded` y su `approved_key` se pone en `NULL`.
- Los borradores tienen `approved_key = NULL`.
- El índice único sobre `approved_key` garantiza un solo vigente aunque haya concurrencia.

Complemento:

1. Unicidad de respaldo `seccion_id + period_start + period_end + shift + version`.
2. La versión se calcula dentro de una transacción con `lockForUpdate()` sobre la misma clave.
3. Si dos usuarios guardan el mismo borrador, `lock_version` detecta el conflicto y se pide recargar.

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
  "paginate": 9999,
  "date_from": null,
  "date_to": null,
  "captured_at": "2026-11-13T12:40:00-04:00"
}
```

Reglas:

- `paginate` se fuerza a `9999` en la consulta del wizard: nunca se limita a la página visible.
- `date_from` / `date_to` se llenan solo si la barra de filtros expone un rango de fechas. Mientras no exista, el wizard los deriva del resultado.
- `filter_hash` = SHA-256 del JSON con claves ordenadas y sin `captured_at`, para comparar universos entre sesiones.
- La instantánea es inmutable: si el usuario cambia filtros con el modal abierto, el plan conserva la original. Se avisa en el pie del modal.

### 2.5 Derivación del periodo

Orden de precedencia:

1. **Rango explícito del filtro** (`date_from` / `date_to`), si existe en la barra.
2. **Rango de las actividades filtradas**: `MIN(activities.finicial)` y `MAX(activities.ffinal)`.
3. **Rango del lapso** (`lapsos.finicial` / `lapsos.ffinal`), solo como respaldo si las actividades no tienen fechas válidas.

Reglas:

- `period_end` nunca es menor que `period_start`.
- Si el resultado filtrado no tiene ninguna fecha válida, el wizard no permite continuar: “El filtro no define un periodo con fechas; ajusta los filtros”.
- Si el rango supera 7 días, el paso 1 muestra el rango completo y ofrece **dividir en planes semanales**; por defecto se propone la semana que contiene las actividades seleccionadas.
- El rango máximo permitido por plan es de 31 días; por encima, se obliga a dividir.
- El usuario **no** puede escribir fechas fuera del rango filtrado; solo puede elegir un subconjunto de días dentro de él.

---

## 3. Tabla `didactic_plan_sessions`

### 3.1 Columnas

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| `id` | bigint unsigned | No | auto | PK. |
| `didactic_plan_id` | bigint unsigned | No | — | FK → `didactic_plans` (`cascadeOnDelete`). |
| `date` | date | No | — | Fecha de la sesión; dentro del periodo. |
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

- `date` debe estar entre `period_start` y `period_end` del plan. Se valida en aplicación y en `DidacticPlanService`.
- Un plan puede tener 0 sesiones solo mientras sea borrador; para aprobar se exige al menos una sesión lectiva.
- La misma actividad puede originar varias sesiones (por ejemplo, si abarca varios días), pero cada sesión conserva su `source_activity_id`.
- `is_non_teaching = 1` permite representar feriados dentro del rango sin inventar contenido.

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
| `didactic_plan_session_id` | bigint unsigned | Sí | null | FK → `didactic_plan_sessions` (`nullOnDelete`). |
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

- `included = 0` documenta actividades del resultado filtrado que se revisaron y se descartaron; sirve como evidencia de auditoría, no como contenido.
- El `activity_checksum` permite detectar que la actividad cambió después de aprobar el plan.
- `activity_id` es `null` si la actividad se elimina; el extracto permanece para no perder historia.

---

## 6. Ciclo de vida

### 6.1 Estados

| Estado | Editable | PDF | Acciones |
|---|---|---|---|
| `draft` | Sí | No (salvo marca de agua) | Editar, aprobar, eliminar. |
| `approved` | No | Sí | Descargar PDF, crear nueva versión, archivar. |
| `superseded` | No | Sí (histórico) | Ver, descargar PDF histórico. |
| `archived` | No | No por defecto | Ver, restaurar como borrador si se permite. |

### 6.2 Transiciones

| Desde | Acción | Hacia | Condiciones |
|---|---|---|---|
| — | Crear desde wizard | `draft` | Filtros con sección y periodo válidos. |
| `draft` | Guardar borrador | `draft` | Contexto y periodo válidos. |
| `draft` | Aprobar | `approved` | Checklist completo o justificación; usuario `isPlanner`. |
| `draft` | Eliminar | eliminado | Solo si nunca fue aprobado. |
| `approved` | Crear nueva versión | `draft` (v+1) | Copia profunda; el vigente sigue aprobado. |
| `approved` | Archivar | `archived` | Permiso de dirección/planificación; auditoría. |
| `approved` | Aprobar nueva versión | `superseded` | La nueva versión pasa a `approved` y toma el `approved_key`. |
| `archived` | Restaurar | `draft` | Opcional; nueva versión. |

Reglas:

- `approved` es inmutable. Cualquier cambio exige “Crear nueva versión”.
- Solo puede existir un `approved` por `seccion_id + period_start + period_end + shift`.
- Si el plan aprobado cambia de periodo (porque cambió el filtro), se trata como un plan nuevo: no se reutiliza la clave.

### 6.3 Aprobación por planificación

- El botón **Aprobar** solo aparece si `auth()->user()` cumple `isPlanner` y la policy `approve` sobre la sección.
- No se pide seleccionar aprobador: el aprobador es el usuario autenticado.
- Se registra `approved_by`, `approved_at`, `approved_key` y la entrada de bitácora.
- Se permite que `created_by = approved_by`. No hay segregación de funciones en el MVP; si se requiere después, se agrega una regla de policy sin cambiar el modelo.
- La UI muestra “Aprobado por: {nombre} · {fecha} · v{versión}”.
- Si el usuario no es planificación, el botón no se muestra y el endpoint devuelve 403.

### 6.4 Bloqueo optimista y concurrencia

1. Al abrir el borrador, el cliente recibe `lock_version`.
2. Al guardar, se envía `lock_version` y `checksum`.
3. El servicio bloquea la fila (`lockForUpdate`), compara y:
   - si coincide, incrementa `lock_version` y guarda;
   - si no, responde con conflicto y opción “recargar y comparar”.
4. Dos aprobaciones simultáneas de la misma clave: la segunda falla por `uq_didactic_plan_approved_key` y se informa “Ya existe un plan aprobado para esta sección y periodo”.

### 6.5 Desincronización con la fuente

- El plan guarda `source_checksum` por sesión y `activity_checksum` por fuente.
- Si una actividad fuente cambia, el plan **no** se modifica.
- “Revisar fuentes” compara hashes y muestra un diff resumido campo por campo.
- “Crear nueva versión desde fuentes actualizadas” es una acción explícita del usuario.
- No hay sincronización automática ni jobs en el MVP.

---

## 7. Reglas de persistencia

### 7.1 Creación de borrador (transacción)

1. Validar que la sección y el periodo provienen del snapshot.
2. Validar que cada actividad fuente pertenece al resultado filtrado (`filter_hash`).
3. Buscar borrador con la misma clave y mismo `created_by`:
   - si existe, actualizar;
   - si no, crear con `version = 1`.
4. Reemplazar sesiones, indicadores y fuentes (borrado lógico o reemplazo controlado).
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
| `period_end ≥ period_start` | Aplicación + CHECK si el motor lo permite. |
| `date` de sesión dentro del periodo | Aplicación. |
| Una sesión lectiva por fecha mínima al aprobar | Servicio. |
| Indicador con `origin = activity` requiere fuente | Servicio. |
| `selected_count ≤ filtered_count` | Servicio. |
| Actividad de otra sección no se persiste como fuente | Servicio. |
| No duplicar `(plan, activity_id)` | Índice único. |
| No duplicar vigente por clave | Índice único. |

---

## 8. Permisos

### 8.1 Matriz MVP

| Acción | Planificación | Dirección | Coordinación | Liderazgo | Admin |
|---|---|---|---|---|---|
| Ver plan de su alcance | Sí | Sí | Sí | Solo sus áreas | Sí |
| Crear borrador | Sí | Sí | No | No | Sí |
| Editar borrador | Sí | Sí | No | No | Sí |
| Aprobar | **Sí** | Sí | No | No | Sí |
| Archivar | Sí | Sí | No | No | Sí |
| Descargar PDF | Sí | Sí | Sí | Solo sus áreas | Sí |

### 8.2 Reglas de autorización

- Rutas bajo el grupo `planning` (`auth` + `isPlanner`) y policy `DidacticPlanPolicy`.
- `view`: usuario con alcance sobre `seccion_id` (misma lógica que actividades).
- `create`: `isPlanner` + `pestudio_id` con `planning_module = true`.
- `update`: `draft` + alcance + `isPlanner`.
- `approve`: `isPlanner` + alcance + estado `draft`.
- `downloadPdf`: `view` + estado `approved`/`superseded` (en `draft`, solo con marca de agua si se habilita).
- `archive`: `isPlanner` o admin.
- Nunca confiar en `seccion_id` del cliente: recalcular la coherencia `seccion → grado → pestudio`.

### 8.3 Auditoría (contrato `Auditable`)

`DidacticPlan` declara allowlist:

```
id, pestudio_id, grado_id, seccion_id, lapso_id, period_start, period_end,
shift, axis, project_title, status, version, source_mode, filter_hash,
filtered_count, selected_count, approved_by, approved_at, superseded_by_id
```

Se auditan: crear, guardar borrador, aprobar, archivar, nueva versión, dividir periodo y descargar PDF (evento de lectura). No se audita cada pulsación ni el contenido completo de las actividades.

---

## 9. PDF imprimible

### 9.1 Ruta y controlador

| Elemento | Valor |
|---|---|
| Ruta | `GET /app/planning/activities/didactic-plans/{didacticPlan}/pdf` |
| Nombre | `planning.activities.didactic-plans.pdf` |
| Controlador | `App\Http\Controllers\Planning\DidacticPlanPdfController` |
| Métodos | `stream()` (vista previa), `download()` (descarga) |
| Middleware | `auth`, `isPlanner`, policy `view` |
| Motor | DomPDF, igual que `ActivityPdfController` |

Reglas:

- Solo se genera PDF de un plan `approved` o `superseded`. Un `draft` solo se renderiza con marca de agua “BORRADOR — NO OFICIAL” si se habilita.
- El nombre del archivo es `PSD-{seccion}-{period_start}-v{version}.pdf`.
- No se generan PDFs en cola en el MVP; la generación es síncrona.
- Se registra el evento de descarga con usuario, plan y versión.

### 9.2 Vista

`resources/views/pdfs/planning/didactic-plans/plan.blade.php`.

Estructura:

1. **Encabezado institucional**
   - nombre de la institución (`Institucion`), RIF, dirección y teléfono si existen;
   - título “PLAN DE CLASES / SECUENCIA DIDÁCTICA”;
   - subtítulo “Educación Primaria · {grado} {sección} · {periodo}”.
2. **Bloque de contexto**
   - plan de estudio, grado y sección;
   - periodo tomado de los filtros (`period_start` – `period_end`);
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
   - “Elaborado por”: nombre y cargo de `created_by`;
   - “Aprobado por (Planificación)”: nombre y cargo de `approved_by`, fecha;
   - línea opcional de coordinación/dirección según formato institucional.
6. **Pie de página**
   - código `PSD-{seccion}-{period_start}-v{version}`;
   - fecha/hora de generación;
   - “Documento generado por SAEFL”;
   - número de página y total;
   - `filter_hash` corto (8 caracteres) para trazabilidad.

### 9.3 Maquetación

| Aspecto | Regla |
|---|---|
| Orientación | Carta vertical por defecto; horizontal si hay más de 4 sesiones o textos largos. |
| Fuente | Helvetica 6–10 pt, coherente con los PDF existentes. |
| Colores | Grises y un acento institucional discreto; sin imágenes remotas. |
| Saltos de página | Evitar cortar una sesión; `page-break-inside: avoid` por bloque. |
| Sesiones múltiples | Una página por bloque de 2–3 sesiones; anexo si hay más. |
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
  - `2026_11_20_000001_create_didactic_plans_table.php`
  - `2026_11_20_000002_create_didactic_plan_sessions_table.php`
  - `2026_11_20_000003_create_didactic_plan_indicators_table.php`
  - `2026_11_20_000004_create_didactic_plan_sources_table.php`
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

- Crear plan con sección y periodo derivados del filtro.
- Rechazar plan sin sección.
- Rechazar plan con periodo invertido.
- Rechazar sesión fuera del periodo.
- Rechazar fuente de otra sección.
- Rechazar actividad fuera del `filter_hash`.
- Unicidad de versión.
- Unicidad de vigente con dos aprobaciones simultáneas.
- Conflicto de `lock_version`.
- Nueva versión copia sesiones, indicadores y fuentes.
- Soft delete solo en borradores.
- Auditoría de creación, aprobación, archivado y descarga.

### 11.2 PDF

- Encabezado institucional presente.
- Contexto con sección, periodo y eje.
- Cada sesión con inicio, desarrollo, cierre, evaluación, indicadores y ODS.
- Textos largos y acentos sin romper.
- Saltos de página sin cortar sesiones.
- Firmas de elaboración y aprobación con nombre del usuario de planificación.
- Sin datos de estudiantes.
- `draft` sin PDF oficial (o con marca de agua).
- 403 para usuario sin alcance.

### 11.3 Criterios de aceptación del anexo

1. Existen las cuatro tablas con índices y FKs correctos.
2. El plan siempre tiene sección y periodo derivados de los filtros.
3. Solo hay un plan aprobado por sección y periodo.
4. La aprobación la registra un usuario de planificación.
5. El PDF oficial contiene la secuencia didáctica, la evaluación, los indicadores, los ODS y las firmas.
6. Todo cambio queda auditado.
7. La migración es aditiva y reversible solo con backup.

---

## 12. Anexos relacionados

- [`PLAN-SECUENCIA-DIDACTICA-001.md`](PLAN-SECUENCIA-DIDACTICA-001.md): plan maestro.
- [`ANEXO-A-EXTRACCION.md`](ANEXO-A-EXTRACCION.md): contrato de filtros, mapeo y consolidación.
- [`README.md`](README.md): índice y resumen.
