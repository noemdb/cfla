# PLAN-TIMETABLE-003 · Normalización de horas T/P por asignatura según legacy
**Fecha:** 2026-09-08 · **Estado:** PROPUESTA · **Dependencias:** SPEC-TIMETABLE-001-v2 §4 (ADR-TT-004), migraciones 2026_09_08 aplicadas

---

## 0. Contexto del problema

En el Paso 3 del wizard (`/app/planning/timetable`) las secciones de **EDUCACION INICIAL** muestran
"7 áreas · 28 bloques T · 28 bloques P · 56 totales" contra una capacidad real de 30 bloques/semana.
Causa verificada en BD (lapso 1, asignaturas 225–251): cada asignatura de INICIAL tiene
`hour_p_week = hour_t_week` (p. ej. 4/4), duplicando la carga. En MEDIA C&T también (41/41, 27/27),
pero en 4TO AÑO del mismo plan difieren (59/1) — inconsistencia global en
`asignaturas.hour_t_week/hour_p_week` (columnas `int(11) NULL`).

Origen del mal dato: carga manual de 2024 (verificado por `updated_at`: no vino del backfill, que
solo toca valores 0/null y siempre P=0; ni del import legacy, que solo lee). Pero el legacy **sí
contiene la verdad**: los horarios reales 2025-2026 (`legacy_horario_secciones.csv`, 575 slots)
muestran la distribución semanal real de cada materia por sección y grado.

**Objetivo:** artisan command `timetable:normalize-horas` que derive `hour_t_week`/`hour_p_week`
de la evidencia del legacy, audit-first (`--dry-run`/sin flags no escribe) y `--force` para aplicar.

**Caso de aceptación principal:** tras `--force`, la sección INICIAL "3ER GRUPO U" debe mostrar
~8 bloques/semana ≤ 30 de capacidad, y el warning "⚠ excede" del Paso 3 desaparecer.

---

## 1. Evidencia computada del legacy (fuente de verdad)

Minutos semanales por (nivel, materia) = mediana entre secciones de `sum(duración de franjas únicas)`;
franja identificada por `(dia, hora_inicio)` — los bloques combinados verticales (merges de 1-2
franjas del Excel) cuentan una vez. Cómputo verificable con el script ad-hoc sobre
`legacy_horario_secciones.csv` (ejecutado 2026-09-08; reproducible, ver §5).

### INICIAL (3 grupos × 1 sección U, 7 asignaturas)

El legacy solo registra 4 especialistas con franjas en la rejilla; las 3 áreas de formación las
cubre la docente de aula (40 h) integradas en la jornada — **no existen como bloques discretos**.

| Materia | Franjas/secc | Min/sem | Horas propuestas [T,P] |
|---|---|---|---|
| INGLÉS | 2–3 | 105–230 (mediana 150) | **[3, 0]** |
| ROBÓTICA | 1 | 80 | **[1, 0]** |
| MÚSICA | 1 | 70–80 | **[1, 0]** |
| EDUCACIÓN FÍSICA | 1 | 80 | **[1, 0]** |
| FORMACIÓN PERSONAL Y SOCIAL | 0 (integrada) | — | **[1, 0]** (D-1) |
| RELACIÓN CON EL AMBIENTE | 0 (integrada) | — | **[1, 0]** (D-1) |
| COMUNICACIÓN Y REPRESENTACIÓN | 0 (integrada) | — | **[1, 0]** (D-1) |

**D-1 (decisión):** para las 3 áreas integradas se fija 1 h T (mínimo razonable para que aparezcan
en la rejilla; el pensum real las distribuye como jornada de aula). P=0 siempre en INICIAL.

### PRIMARIA (grados 1°–6°, 12 secciones, 2 por grado)

| Materia legacy | Franjas/secc | Min/sem (mediana) | Horas propuestas [T,P] | t/p actual BD |
|---|---|---|---|---|
| LENGUA | 3 | 195 | **[3, 0]** | 3/0 ✓ |
| MATEMÁTICA(S) | 3 | 185–195 | **[3, 0]** | 3/0 ✓ |
| CIENCIAS NATURALES | 2 | 150 | **[2, 0]** | 2/0 ✓ |
| CIENCIAS SOCIALES | 2 | 150 | **[2, 0]** | 2/0 ✓ |
| INGLÉS | 4–5 | 290–320 (mediana 310) | **[3, 0]** (OQ-1) | 2/0 |
| EDUCACIÓN ESTÉTICA | 1–2 | 70 | **[1, 0]** | 1/0 ✓ |
| EDUCACIÓN FÍSICA | 1 | 80 | **[1, 0]** | 1/0 ✓ |
| FORMACIÓN HUMANO CRISTIANA | 1 | 70–80 | **[1, 0]** | 1/0 ✓ |
| MÚSICA | 1 | 70–80 | **[1, 0]** | 1/0 ✓ |
| ROBÓTICA | 2 | 140–160 | **[2, 0]** | — |
| SOCIO EMOCIONAL | 1 | 35 | **[1, 0]** | — |

### MEDIA (años 1°–5°, 24 secciones; aplica a MEDIA GENERAL y MEDIA C&T)

| Materia legacy | Franjas/secc | Min/sem | Horas propuestas [T,P] |
|---|---|---|---|
| INGLÉS | 6 | 480 | **[8, 0]** |
| EDUCACIÓN FÍSICA | 3 | 240 | **[4, 0]** (OQ-2: hembras/varones en paralelo) |
| MATEMÁTICA(S) | 2–3 | 160–240 | **[3, 0]** |
| LENGUA Y LITERATURA | 2–3 | 160–240 | **[3, 0]** |
| CASTELLANO | 2 | 160 | **[3, 0]** |
| BIOLOGIA (4°–5°) | 2 | 160 | **[2, 1]** |
| BIOLOGIA, AMBIENTE Y TECNOLOGÍA (1°–3°) | 2 | 160 | **[2, 1]** |
| FÍSICA | 1–2 | 80–160 | **[2, 0]** |
| QUÍMICA | 1–2 | 80–160 | **[2, 0]** |
| GEOGRAFIA HISTORIA Y SOBERANIA NACIONAL (1°–3°) | 2 | 160 | **[2, 0]** |
| GEOGRAFIA HISTORIA Y CIUDADANIA (4°–5°) | 2 | 160 | **[2, 0]** |
| ORIENTACIÓN VOCACIONAL | 1–2 | 80–160 | **[2, 0]** |
| FORMACIÓN HUMANO CRISTIANA | 1 | 80 | **[1, 0]** |
| FORMACIÓN PARA LA SOBERANIA NACIONAL | 1 | 80 | **[2, 0]** |
| INFORMÁTICA (4°–5°) | 2 | 160 | **[2, 1]** |
| ROBÓTICA | 2 | 160 | **[2, 0]** |
| FINANZAS (solo 1°) | 2 | 160 | **[2, 0]** |
| SEMINARIO DE INVESTIGACIÓN (4°–5°) | 2 | 160 | **[2, 1]** |
| INNOVACIÓN TECNOLÓGICA Y PRODUCTIVA | 1 | 80 | **[1, 1]** |
| CIENCIAS DE LA TIERRA (solo 5°) | 1 | 80 | **[2, 1]** |

BD-only (sin franja propia en el grid): ORIENTACIÓN Y CONVIVENCIA **[3, 0]**, PARTICIPACIÓN EN GRUPOS
DE CREACIÓN/RECREACIÓN **[3, 0]** (OQ-3).

### Reglas

- **R-1 (redondeo):** `hour_t_week = ceil(min/sem ÷ 60)` con ajuste institucional donde la evidencia
  diverge (OQ-1: INGLÉS PRIMARIA). Máximo `hour_t + hour_p ≤ 30` (no caben en la semana).
- **R-2 (clasificación T/P):** P=0 por defecto (materia de aula). P>0 solo laboratorio/taller:
  BIOLOGÍA, INFORMÁTICA, SEMINARIO (trabajo de campo), INNOVACIÓN TEC., CIENCIAS DE LA TIERRA —
  fracción práctica acotada (≤1 h) para no inflar la rejilla.
- **R-3 (matching):** `norm()` idéntico a `TimetableBackfillHoras::norm` (iconv ASCII//TRANSLIT +
  colapsar espacios + upper) y matching `str_contains` del nombre normalizado contra las keys del
  mapa **del plan correspondiente** (PRIMARIA / MEDIA familia / INICIAL), igual que backfill `planMap()`.
- **R-4 (integridad):** solo UPDATE de asignaturas **existentes** referenciadas por pevaluaciones del
  lapso (mismo alcance que backfill). Nunca crear asignaturas. Nunca tocar lecciones existentes.
- **R-5 (multi-plan):** si una misma asignatura aparece en >1 pestudio activo, saltarla con warning
  "multi-plan: resolver a mano" (el mapa por plan daría valores contradictorios).

---

## 2. Diseño del comando

### Firma
```bash
php8.2 artisan timetable:normalize-horas [--lapso=1] [--pestudio=""] [--force] [--dry-run]
```

- Sin flags → **audit-only** (tabla por consola, no persiste). Igual semántica que backfill
  (sin `--force` solo escribe valores 0/null; aquí además sin `--force` nunca escribe).
- `--force` → aplica TODAS las filas del audit (alinea a la norma legacy).
- `--dry-run` → alias explícito del default (paridad con backfill; mismo output).
- `--pestudio="EDUCACION INICIAL"` → limita el audit/aplicación a un plan.

### Fuente de datos
`config('timetable.legacy_csv_dir')` + `/legacy_horario_secciones.csv` (NO hardcodear path; el
comando es **determinista sin leer CSVs en runtime**: los valores ya están fijados en el MAPA de
constantes derivado de la evidencia §1. El CSV queda como referencia documental de dónde salieron).
Ventaja: sin dependencia de filesystem en producción (config `TIMETABLE_LEGACY_CSV_DIR` apunta al
blueprint en dev; el mapa vive en el comando).

### Estructura del comando (`app/Console/Commands/TimetableNormalizeHoras.php`)

```php
protected $signature = 'timetable:normalize-horas
    {--lapso=1 : Lapso base para localizar las asignaturas}
    {--pestudio= : Limitar a un plan de estudio (nombre exacto)}
    {--force : Aplicar los cambios (default: audit-only)}
    {--dry-run : Alias del default audit-only}';

protected $description = 'Normaliza hour_t_week/hour_p_week de las asignaturas según el horario legacy 2025-2026 (audit salvo --force)';

private const MAPAS = [
    'INICIAL' => [ /* tabla §1: 7 keys => [t,p] */ ],
    'PRIMARIA' => [ /* tabla §1: 11 keys */ ],
    'MEDIA' => [ /* tabla §1: 20 keys */ ],
];

// planMap(string $pestudioName): array — 'MEDIA GENERAL' familia comparte MEDIA
// norm(?string $s): string — copia exacta de TimetableBackfillHoras::norm
// horasFor(string $norm, array $mapa): ?array — str_contains, primera coincidencia
// asignaturas(int $lapsoId, string $pestudioName): Collection — igual que backfill
//   PERO: sin filtro de horas (audit cubre todas), sí filtro multi-plan (R-5)
```

`handle()` itera pestudios activos (o el de `--pestudio`), arma la tabla
`[plan, asig_id, asignatura, t_actual, p_actual, t_nuevo, p_nuevo, estado]` con estados
`cambia` / `ok` / `sin mapeo` / `multi-plan`, imprime con `$this->table()` y solo con `--force`
ejecuta `$asig->update()`. Sin `--force`, mensaje final: "audit-only: N filas cambiarían; re-ejecuta
con --force para aplicar".

### Casos de matching esperados (tests)

| Asignatura BD (norm) | Plan | Matchea | [T,P] |
|---|---|---|---|
| ÁREA COMPLEMENTARIA INGLES | PRIMARIA | 'INGLES' | [3, 0] |
| ÁREAS COMPLEMENTARIA INGLES (typo BD) | PRIMARIA | 'INGLES' | [3, 0] |
| ÁREAS COMPLEMENTARIA ROBÓTICA 1G | PRIMARIA | 'ROBOTICA' | [2, 0] |
| ÁREA COMPLEMENTARIA INTEGRAL 1G | PRIMARIA | — | sin mapeo (multi-sujeto) |
| INGLES Y OTRAS LENGUAS EXTRANJERAS | MEDIA | 'INGLES' | [8, 0] |
| IDIOMAS | MEDIA C&T | — | sin mapeo (OQ-3) |
| GEOGRAFÍA  HISTORIA Y CIUDADANIA (doble espacio) | MEDIA | colapsa → 'GEOGRAFIA HISTORIA Y CIUDADANIA' | [2, 0] |
| MATEMÁTICAS | cualquiera | 'MATEMATIC' | [3, 0] |
| BIOLOGÍA, AMBIENTE Y TECNOLOGÍA | MEDIA C&T | 'BIOLOGIA AMBIENTE Y TECNOLOGIA' (norm quita comas? NO — comas se conservan; key con coma) | [2, 1] |
| LENGUA | PRIMARIA | 'LENGUA' (orden: keys más largas primero para no pisar) | [3, 0] |

**Orden de keys:** iterar el mapa con `strlen($key)` DESC para que 'BIOLOGIA AMBIENTE Y TECNOLOGIA'
gane antes que 'BIOLOGIA', y 'LENGUA Y LITERATURA' antes que 'LENGUA' (en MEDIA; en PRIMARIA solo
existe 'LENGUA'). Detalle crítico: PRIMARIA tiene key 'LENGUA' y MEDIA 'LENGUA Y LITERATURA' —
como el matching es por plan, no colisionan.

### Interacción con artefactos existentes

- **TimetableLesson** (lecciones ya persistidas por import-legacy con las horas viejas): el comando
  NO las toca. Las lecciones nuevas derivarán bloques correctos; las existentes se re-derivan
  editando el Paso 3 o duplicando calendario. Documentar en `--help`.
- **timetable:backfill-horas**: queda funcionalmente obsoleto para PRIMARIA/MEDIA (su mapa P=0
  siempre es incorrecto para laboratorios). Añadir en su `protected $description`:
  "Ver también timetable:normalize-horas (mapa con horas prácticas del legacy)".
- **BD (REGLA ABSOLUTA CLAUDE.md):** solo UPDATE de 2 columnas int de `asignaturas`. Sin DROP,
  sin TRUNCATE, sin fresh. Tests con `DatabaseTransactions` sobre la BD real.

---

## 3. Tareas (TDD)

### Task 1 — Test feature del audit (RED)
**Files:** Create `tests/Feature/Timetable/NormalizeHorasCommandTest.php`

Test 1 `test_audit_only_no_escribe`:
- Fixture: pestudio INICIAL + pevaluación del lapso 1 + asignatura 'FORMACIÓN PERSONAL Y SOCIAL' T=4 P=4.
- `$this->artisan('timetable:normalize-horas')->assertExitCode(0)`.
- Assert BD: T sigue 4, P sigue 4 (sin --force no escribe).
- Assert output contiene `t_nuevo` = 1 y `p_nuevo` = 0 (audit muestra el cambio propuesto).

Run: `php8.2 artisan test --filter=NormalizeHorasCommandTest` → FAIL (command no definido).

### Task 2 — Comando mínimo (GREEN)
**Files:** Create `app/Console/Commands/TimetableNormalizeHoras.php`

Esqueleto: signature, `MAPAS` (las 3 tablas de §1), `planMap()`, `norm()` (copia de backfill),
`horasFor()` con keys ordenadas por longitud DESC, `handle()` audit-only con `$this->table()`.

Run: `php8.2 artisan test --filter=NormalizeHorasCommandTest` → PASS.
Commit: `feat(timetable): comando timetable:normalize-horas audit-only con mapa legacy`

### Task 3 — Test --force y matching de alias (RED→GREEN)
En el mismo test file:

- `test_force_aplica_y_deja_p_en_cero_inicial`: mismo fixture, `--force`, assert T=1 P=0 en BD.
- `test_alias_area_complementaria`: asignatura 'ÁREA COMPLEMENTARIA INGLES' (PRIMARIA) T=2 → --force → T=3.
- `test_integral_queda_sin_mapeo`: 'ÁREA COMPLEMENTARIA INTEGRAL 1G' → --force → BD intacta + output "sin mapeo".
- `test_mult plan_saltado`: misma asignatura con pevaluaciones en 2 pestudios → output "multi-plan" + BD intacta.
- `test_doble_espacio_geografia`: 'GEOGRAFÍA  HISTORIA Y CIUDADANIA' (dos espacios) → --force → T=2.

Implementar lo mínimo para cada uno. Run tras cada test → PASS. 
Commit: `feat(timetable): normalize-horas aplica con --force, alias y guard multi-plan`

### Task 4 — Deprecación suave de backfill
**Files:** Modify `app/Console/Commands/TimetableBackfillHoras.php` (solo la línea `$description`).

Run: `php8.2 artisan list | grep timetable` → muestra ambas descripciones.
Commit: `chore(timetable): nota de coexistencia backfill vs normalize-horas`

### Task 5 — Documentación
**Files:** Modify `docs/timetable/README.md`:
- §10.1: añadir fila "Normalización de horas T/P (normalize-horas)" con el comando.
- §12: comandos de validación nuevos.
- §13 FAQ: entrada "Paso 3 muestra '⚠ excede'" → referencia al comando.
- Actualizar la cifra de tests si cambia.

Run: leer el diff. Commit: `docs(timetable): normalize-horas en README`

### Task 6 — Regresión completa
```bash
php8.2 artisan config:clear
php8.2 artisan test --filter=Timetable        # 115 + N nuevos en verde
php8.2 artisan timetable:normalize-horas      # audit real de producción (sin escribir)
php8.2 artisan timetable:normalize-horas --pestudio="EDUCACION INICIAL"   # audit dirigido
```
El `--force` sobre producción lo ejecuta SOLO el usuario (no el agente) tras revisar el audit.

---

## 4. Archivos a crear/modificar

| Acción | Path |
|---|---|
| Create | `app/Console/Commands/TimetableNormalizeHoras.php` |
| Create | `tests/Feature/Timetable/NormalizeHorasCommandTest.php` |
| Modify | `app/Console/Commands/TimetableBackfillHoras.php` (nota en $description) |
| Modify | `docs/timetable/README.md` (§10.1, §12, §13) |

---

## 5. Verificación

- Script de evidencia (ya ejecutado; re-ejecutable): parsear `legacy_horario_secciones.csv`
  agrupando franjas únicas `(dia, hora_inicio)` por `(nivel_efectivo, grado, seccion, materia)`,
  mediana por (nivel, materia). Debe reproducir las tablas §1.
- `php8.2 artisan test --filter=Timetable` → todo verde (115 + ~6 nuevos).
- Audit de producción: la tabla debe mostrar las asignaturas INICIAL 225–251 pasando de 4/4 a
  [3|1|1|1|1|1|1]/0, y PRIMARIA/MEDIA mayormente `ok` o deltas pequeños.
- Tras `--force` (ejecuta el usuario): en `/app/planning/timetable` Paso 3, sección INICIAL:
  "7 áreas · 8 bloques T · 0 bloques P · 8 totales/semana · Capacidad 30 ✓".

## 6. Riesgos

- **Lecciones ya importadas** no se recalculan (documentado; regenerar calendario si se quiere).
- **INGLÉS PRIMARIA** y otras divergencias evidencia-vs-norma: el mapa es una constante editable;
  el audit hace visible cualquier ajuste antes de aplicar.
- **Asignaturas homónimas entre planes**: guard R-5 las salta con warning.

## 7. Open questions (decidir antes/durante implementación)

1. **INGLÉS PRIMARIA**: franjas legacy ~5/semana (310 min ≈ 5.2 h) vs carga docente abril (16–24 h
   por docente para 6 secciones ≈ 2.7–4 h/sección). Propuesta: **3 h**. ¿Confirmar?
2. **EDUCACIÓN FÍSICA MEDIA**: 3 franjas = 4 h, pero el legacy divide HEMBRAS/VARONES en paralelo
   (grupo_paralelo). ¿Confirmar 4 h T?
3. **ORIENTACIÓN Y CONVIVENCIA / PARTICIPACIÓN EN GRUPOS / IDIOMAS**: en BD sin franja propia en
   el grid. Propuesta: mantener los valores del backfill ([3,0]) y no tocarlas (estado `ok`).
   ¿Confirmar?
4. **¿Crear también sección U de INGLÉS PRIMARIA vs el resto?** No: una sola asignatura por plan;
   las variantes por grado (4to A vs B con 4 vs 5 franjas) se promedian en la mediana del mapa.
