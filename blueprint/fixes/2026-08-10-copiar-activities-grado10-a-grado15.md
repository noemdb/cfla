# Plan integral — Copiar Activities de CUARTO AÑO (grado 10 → grado 15)

> **Fecha:** 2026-08-10
> **Ámbito:** corrección de datos — migración de `activities` (y relaciones) entre periodos escolares.
> **Modelos involucrados:** `Activity`, `Asignatura`, `Grado`, `Seccion`, `Peducativo`, `Pensum`, `Pestudio`, `Profesor`, `Pevaluacion`, `Pescolar`.
> **Estado:** PLAN — pendiente de elegir opción por el usuario. No se ha ejecutado ninguna escritura.

---

## 1. Contexto / problema

Hay `activities` registradas asociadas a `Pevaluacion` del **pestudio 2 + grado 10 (CUARTO AÑO)**. Esos datos corresponden a otro periodo escolar. Se requiere llevar esas activities al **grado 15 (CUARTO AÑO)** del periodo actual, replicando el contenido que mejor corresponda según `pensum.asignaturaId`.

---

## 2. Diagnóstico de estado actual (verificado en BD 2026-08-10)

### ⚠️ Discrepancias con la premisa del pedido

| Premisa del pedido | Estado real en BD |
|---|---|
| grado 10 está **inactivo** | `grados.status_active = 'true'` (¡ACTIVO!) |
| grado 15 está **activo** | `grados.status_active = 'false'` (¡INACTIVO!) |
| grado 15 pertenece a pestudio 2 | `grados.pestudio_id = 4` (pertenece a pestudio 4) |
| secciones de grado 10 inactivas | secc 19 (A) y 20 (B) están `status_active='true'` |
| secciones de grado 15 activas | secc 71 (A) y 72 (B) `'true'`; 73 (C) y 74 (U) `'false'` |

**Interpretación probable:** la premisa describe el estado DESEADO tras la corrección (periodo actual = grado 15), pero la BD todavía refleja el periodo anterior (grado 10 activo). **El comando de copia NO debe tocar estos status por defecto** (ver variante `--flip-status` en §5).

### Estructura fuente — pestudio 2 + grado 10

| Concepto | Valor |
|---|---|
| Pensums | **12** — asignaturas 71–81 y 255 (todas `status_active='true'`) |
| Pevaluaciones | **86** — secc 19 "A" × 43, secc 20 "B" × 43; lapso 1 × 30, lapso 2 × 28, lapso 3 × 28 |
| Pevs con activities | **3** |
| Activities | **6** (5 de BIOLOGÍA, 1 de CASTELLANO) |
| Achievements | 17 (en 3 activities) |
| LMS: publicaciones | 2 (`lms_activity_publications`) |
| LMS: secciones | 4 (`lms_activity_sections` → con `lms_activity_contents`) |
| LMS: recursos | 1 (`lms_activity_resources`, media compartida) |
| LMS: embeds HTML | 3 (`lms_html_embeds`) |
| LMS: links / comments | 0 / 0 |
| LMS: logs | 2 (`lms_activity_logs`) |

### Estructura destino — grado 15

| Concepto | Valor |
|---|---|
| Pensums pestudio 2 | **0** (los 10 existentes son de pestudio 4, currículo nuevo: asignaturas 124–131, 193, 194) |
| Pevaluaciones | **0** |
| Secciones activas | 71 "A", 72 "B" (mapean 1:1 por nombre con 19/20) |
| Inscripciones | 0 en ambos grados (sin datos que proteger) |

---

## 3. Objetivo

Copiar a `pestudio 2 + grado 15` el contenido de planificación del `pestudio 2 + grado 10`:

1. **Pensums:** clonar los 12 pensums de grado 10 → grado 15 (misma `asignatura_id`, mismos flags), porque grado 15 no tiene pensums del pestudio 2.
2. **Pevaluaciones:** crear/mapear las 86 pevs destino (mismo `asignatura` vía pensum, mismo `lapso_id`, mismo `profesor_id`, `seccion_id` mapeada por nombre A→A / B→B, mismas flags/escala/grupo_estable).
3. **Activities + relaciones:** copiar las 6 activities con sus relaciones (achievements y, según alcance elegido, árbol LMS completo).

La fuente NO se modifica (copia, no movimiento).

---

## 4. Estrategia de mapeo

| Elemento fuente | Mapeo a destino |
|---|---|
| `pensum` (pestudio 2, grado 10, asignatura X) | **crear** pensum (pestudio 2, grado 15, asignatura X) con mismos `status_component` / `status_active` / `status_active_diagnostic` / `observations` |
| `seccion` 19 "A", 20 "B" | 71 "A", 72 "B" (**por nombre**); si no hay match → warning + skip |
| `pevaluacion` (pensum, lapso, sección, profesor) | find-or-create: mismo pensum-mapeado + lapso + profesor + sección-mapeada; flags copiadas |
| `activity` | crear con mismas columnas (`finicial`, `ffinal`, `topic`, `thematic`, `references`, `teaching`, `learning`, `description`, `observations`, `comments`, `status`) |
| `achievements` | copiar filas con nuevo `activity_id` |
| `lms_activity_sections` + `contents` | copiar sección (incl. columna cache `content_type`) + re-mapear `section_id` en contenidos |
| `lms_activity_resources` | copiar fila con **mismo** `media_id` (la librería de media es compartida) |
| `lms_activity_links` / `lms_html_embeds` | copiar filas re-mapeando `activity_id` (y `section_id` si aplica) |
| `lms_activity_publications` | crear nueva publicación (única por activity) con `published_by` original |
| `lms_activity_logs` | copiar re-mapeando `activity_id` |

**Nota de esquema:** todas las tablas implicadas tienen solo `PRIMARY(id)` como unique, salvo `lms_activity_publications.activity_id` (única) — se genera una publicación nueva por activity copiada. No hay uuids ni constraints complejas.

---

## 5. Opciones

### Opción A — Copia integral con comando idempotente (RECOMENDADA)

Artisan command `activity:migrate-period` con **dry-run por defecto**, tabla de seguimiento y rollback acotado.

- Clona pensums + crea/mapas pevs + copia activities con **TODAS** las relaciones (achievements + LMS completo: secciones/contenidos, recursos, embeds, publicaciones, logs).
- **Idempotente:** tabla `activity_migration_logs` (source/target ids) → re-ejecutar salta lo ya copiado; `--rollback` borra solo lo copiado por el log.
- **--dry-run por defecto:** imprime el plan de ejecución sin escribir nada (convención del repo).
- Flags: `--from-grado=10 --to-grado=15 --pestudio=2`, `--with-lms` (default) / `--planning-only`, `--flip-status` (opcional: desactiva grado/secciones origen y activa las destino — ver §2), `--force`.
- **Ventajas:** seguro de re-ejecutar, verificable, reversible, no toca la fuente.
- **Coste:** migración nueva (tabla log) + comando ~300 líneas + tests.

### Opción B — Solo planificación (sin LMS)

Igual que A pero copia únicamente `activities` + `achievements` (los 6 + 17 registros). No se copian secciones de lección, embeds, recursos ni publicaciones.

- **Ventajas:** mínimo, rápido, sin riesgo sobre contenido LMS.
- **Coste:** las lecciones/embeds del grado 10 NO llegan al grado 15 (habría que regenerarlas o copiarlas después con otra herramienta).

### Opción C — Reapuntar en sitio (mover, no copiar)

Sin duplicar filas: `UPDATE pensums SET grado_id = 15` para los 12 pensums del pestudio 2 y re-mapear `seccion_id` de las 86 pevs (19→71, 20→72).

- **Ventajas:** cero duplicación, sin comando nuevo (un par de UPDATEs controlados).
- **Coste:** el pestudio 2 + grado 10 deja de existir como tal (el histórico del periodo anterior se pierde como estructura separada). Solo recomendable si el periodo anterior no debe conservarse.

### Variantes transversales (aplicables a A o B)

1. **Alcance de pevs:** crear las **86** pevs destino (estructura completa — recomendado: el grado 15 queda operativo) vs crear solo las **3** pevs que tienen activities (mínimo).
2. **--flip-status:** invertir `status_active` de grados/secciones (10→false, 15→true) como parte de la migración, o dejarlo a cargo del usuario.
3. **Mapeo de asignaturas:** por `asignatura_id` exacto (pestudio 2 → pestudio 2, recomendado) vs por nombre entre currículos distintos (pestudio 2 → pestudio 4, solo si se decide migrar al currículo nuevo).

---

## 6. Especificación del comando (Opción A)

```
php8.2 artisan activity:migrate-period \
    --from-grado=10 --to-grado=15 --pestudio=2 \
    [--planning-only] [--flip-status] [--force]
```

**Fases (cada una con resumen por consola y registro en el log):**

1. **Diagnóstico:** validar premisas (grados, secciones activas, pensums, conteos). Si el estado no coincide con lo esperado → abortar y mostrar el diagnóstico (con `--force` continúa).
2. **Pensums:** clonar los 12 de grado 10 → grado 15 (misma asignatura y flags). Skipear si el par (pestudio, grado, asignatura) ya existe.
3. **Pevaluaciones:** find-or-create por (pensum-mapeado, lapso, sección-mapeada, profesor); copiar flags (`status_baremo`, `status_official`, `status_note_report`, `nota_type`, `escala_id`, `objetivo`, `description`, `observations`, `category`, `grupo_estable_id`).
4. **Activities:** copiar las 6 con sus columnas; registrar en `activity_migration_logs`.
5. **Relaciones:** achievements; y si `--with-lms` (default): secciones→contenidos, recursos (mismo media), embeds, links, publicación (nueva), logs.
6. **Verificación:** conteos por tabla (origen == destino), spot-checks de topic/asignatura, reporte final.

**Rollback:** `--rollback` borra las filas destino registradas en el log (orden inverso: contenidos → secciones → publicaciones → … → activities → pevs → pensums creados).

**Tabla nueva (migración):** `activity_migration_logs` — `id, pestudio_id, from_grado_id, to_grado_id, pensum_source_id, pensum_target_id, pev_source_id, pev_target_id, activity_source_id, activity_target_id, copied_at` (index por from/to).

---

## 7. Verificación y rollback

- **Antes:** snapshot de conteos (`activities`, `achievements`, `pevaluacions`, `pensums` + `MAX(id)`), igual que la disciplina de fixtures del repo.
- **Después:** los conteos destino deben igualar a los fuente por tabla; spot-check de 2–3 activities (topic, achievements, secciones) en el grado 15; la fuente intacta (conteos idénticos).
- **Rollback:** `--rollback` con el log; re-verificar snapshot.

---

## 8. Riesgos y decisiones abiertas

| # | Riesgo / decisión | Mitigación / pregunta |
|---|---|---|
| 1 | Duplicar pensums/pevs al re-ejecutar | Tabla de seguimiento + find-or-create por par único |
| 2 | `lms_activity_publications.activity_id` única | Crear publicación nueva por activity copiada |
| 3 | Las 86 pevs destino requieren secciones mapeadas por nombre | A→A, B→B existe; si falta match → warning + skip (no abortar) |
| 4 | Los status de grados/secciones contradicen la premisa (§2) | **Pregunta:** ¿se invierten como parte de la tarea (`--flip-status`) o los ajusta el usuario? |
| 5 | Grado 15 pertenece a pestudio 4 (currículo nuevo) | **Pregunta:** ¿se clona el currículo pestudio 2 sobre grado 15 (como pide el pedido) o se mapea al currículo pestudio 4? |
| 6 | Crear 86 pevs vs solo las 3 con activities | Recomendado: 86 (estructura completa del grado) |
| 7 | Media de recursos (1) es compartida | Reutilizar `media_id` (no copiar archivos) |
| 8 | Profesor asignado a las pevs destino | Mantener el `profesor_id` original (mismo cuerpo docente) |
