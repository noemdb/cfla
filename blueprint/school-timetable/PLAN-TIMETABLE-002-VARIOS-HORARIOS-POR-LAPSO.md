# PLAN-TIMETABLE-002: Varios horarios (borradores/alternativas) por lapso — máximo UNO activo

| | |
|---|---|
| **Estado** | Plan aprobado — listo para ejecución (decisión de producto tomada) |
| **Stack** | Laravel 10 · Livewire 3 · MariaDB (db `s2627`, driver `mysql`) |
| **Documento base** | `blueprint/school-timetable/SPEC-TIMETABLE-001-v2.md` |
| **Relacionado** | `PLAN-TIMETABLE-002` describe el **cómo**; la spec se actualizará a v2.1 en la fase de docs |

---

## 1. Decisión de producto (tomada)

> Se permite **varios calendarios (horarios) por `lapso_id`**, tratados como
> **borradores / alternativas**, con **máximo UNO en estado `active`** por lapso.
> El resto viven en `draft`, `generating` o `archived`.

Esto **NO** habilita horarios vigentes simultáneos por ventanas de fecha (queda
fuera de alcance; ver §11). La regla dura la impone la base de datos, no solo la
aplicación.

---

## 2. Invariantes objetivo

| # | Invariante | Garantía |
|---|---|---|
| I-1 | Un lapso puede tener **0..N** calendarios | DB: se elimina `uq_calendar_lapso` |
| I-2 | Por lapso, **a lo sumo 1** calendario con `status='active'` | DB: columna generada + índice único (§4.1) |
| I-3 | `version` (bloqueo optimista §15) es **por calendario**, no por lapso | Aplica a la fila, ya es así; sin cambios |
| I-4 | Activar un calendario **archiva** al activo anterior del mismo lapso | App: `persist()` del job y `activate()` del modelo (§4.2) |
| I-5 | `dryRun` **no** desactiva el calendario activo vigente | App: el dryRun solo toca el calendario objetivo |
| I-6 | Los lectores (leadership/dirección/profesor/estudiante) resuelven **"el activo del lapso vigente"** | App: helper de resolución (§4.4) |
| I-7 | Solo se puede eliminar un calendario en `draft` | App: guard en `deleteCalendar()` |
| I-8 | `STATUS_ARCHIVED` deja de ser código muerto y se usa en democión | App + docs |

---

## 3. Estado actual (línea base, verificada)

- **DB**: `uq_calendar_lapso` único sobre `timetable_calendars.lapso_id`
  (migración `2026_08_15_000001_create_timetable_tables.php:47`).
- **Wizard** (`app/Livewire/Coordinacion/Timetable/TimetableWizard.php` y el gemelo
  `app/Livewire/Planning/Timetable/TimetableWizard.php`):
  - `createCalendar()` (línea 115) bloquea si `exists` para el lapso → guard a quitar.
  - `mount()` (línea 82) elige el último `draft|active` → debe pasar a selector.
- **Job** (`app/Jobs/Timetable/GenerateTimetableJob.php`): `persist()` (línea 224)
  sube `version`, pone `status='active'` y regenera slots/conflictos → debe además
  demover al activo anterior del lapso.
- **Lecturas** que resuelven "activo" globalmente (`->where('status','active')->latest('id')`):
  - `app/Livewire/Leadership/Timetable/SectionGrid.php:27`
  - `app/Livewire/Director/Timetable/SectionGrid.php` (gemelo)
  - `app/Livewire/Profesor/Timetable/MyTimetable.php:31`
  - `app/Livewire/Student/Lms/Timetable.php:32`
  - `TimetableViewService::activeCalendarOrFail()` es **por id explícito** → OK sin cambios.
- **Substitutes** (`TimetableSubstitutes`) ya muestra selector de calendarios → compatible.
- **Rutas**: `timetable.editor/{calendar?}` y `timetable.substitutes/{calendar?}` ya
  aceptan calendar opcional → compatibles.

---

## 4. Cambios por capa

### 4.1 Base de datos — integrada en la migración base (Fase 1)

**Consolidación de migraciones**: como los cambios aún no llegan a producción, no se
crea una migración nueva; el esquema final se integra **dentro de la migración base**
`database/migrations/2026_08_15_000001_create_timetable_tables.php` (tabla
`timetable_calendars`). En producción correrá directamente la base modificada y no
existirá ningún `drop_unique_lapso_allow_multi_calendars`.

```php
// En Schema::create('timetable_calendars', ...)
$table->string('active_lapso_key', 20)
    ->storedAs("IF(status = 'active', CONCAT('L', lapso_id), NULL)");
$table->index('lapso_id', 'idx_cal_lapso');          // backing de la FK
$table->unique('active_lapso_key', 'uq_active_lapso');
// NO existe uq_calendar_lapso (la FK queda respaldada por idx_cal_lapso)
```

Notas:
- `storedAs()` (STORED) está soportado por Blueprint en Laravel 10 + MariaDB; usar
  STORED (no VIRTUAL) para poder indexar de forma fiable.
- `idx_cal_lapso` (no único) respalda la FK `lapso_id`; antes lo hacía el único
  `uq_calendar_lapso`. Sin índice explícito MySQL lo crearía implícito.
- Datos existentes: no hay conflicto (máx. 1 por lapso).
- `active_lapso_key` **no** va a `$fillable` ni `$casts` (columna generada, lectura).

### 4.2 Modelo `TimetableCalendar` (Fase 1)

- Añadir scopes: `forLapso($lapsoId)`, `archived()`.
- Añadir métodos:
  - `activeForLapso($lapsoId): ?self` — activo del lapso (o null).
  - `activate(): void` — transacción: `UPDATE ... SET status='archived' WHERE
    lapso_id=? AND id<>? AND status='active'`; luego `status='active'` en esta fila.
    Usado por "promover borrador" cuando el borrador ya tiene slots generados.
  - `deleteDraft(): bool` — borra solo si `status='draft'` (cascada FK limpia
    periods/lessons/availability/slots/conflicts). Devuelve `false` si no es borrador.
- La invariante I-2 queda cubierta por DB; `activate()` además la respeta a nivel app
  (democión atómica previa) para evitar errores de índice.

### 4.3 Job `GenerateTimetableJob` (Fase 2)

En `persist()` (línea 224), reordenar la transacción:

1. `lockForUpdate()` + verificación de `version` (bloqueo optimista §15) sobre el
   calendario objetivo (mantener semántica actual, pero con row-lock para el paso 2).
2. **Democión**: `TimetableCalendar::where('lapso_id', $c->lapso_id)
   ->where('id', '!=', $c->id)->where('status', 'active')->update(['status' => 'archived'])`.
3. Activar objetivo: `status='active'`, `version+1`, `quality_score`, `preview_payload=null`.
4. Regenerar slots/conflictos (sin cambios respecto al código actual).

Manejo de carrera (dos confirmaciones simultáneas de distintos borradores del mismo
lapso): envolver en `try/catch (\Illuminate\Database\QueryException $e)` — si salta el
índice `uq_active_lapso`, loguear en canal `timetable` con `correlation_id` y
**revertir** (el objetivo vuelve a `draft`); es equivalente al warning de versión actual.

`dryRun` no cambia: el objetivo pasa a `draft` + `preview_payload`; el activo vigente
**permanece activo** durante la previsualización (mejora implícita vs. flujo actual,
donde dryRun dejaba sin activo a los lectores).

### 4.4 Lecturas — resolución "activo del lapso vigente" (Fase 4)

Introducir helper ligero (evitar consulta repetida):

```php
// app/Models/app/Timetable/TimetableCalendar.php (método estático)
public static function activeForCurrentLapso(): ?self
{
    $lapso = Lapso::query()
        ->where('finicial', '<=', now())
        ->where('ffinal', '>=', now())
        ->orderBy('finicial', 'desc')
        ->first();

    return $lapso ? self::query()->where('lapso_id', $lapso->id)
        ->where('status', self::STATUS_ACTIVE)->first() : null;
}
```

Reemplazar en los 4 puntos de §3 el patrón `where('status','active')->latest('id')`:
- `SectionGrid` (Leadership y Director), `MyTimetable` (Profesor), `Lms/Timetable`
  (Student): usar `activeForCurrentLapso()`.
- `TimetableViewService::activeCalendarOrFail()`: sin cambios (id explícito).
- Fallback si no hay lapso vigente pero sí un activo histórico: opcional — retornar el
  último activo (`latest('id')`) como degradación. Decisión abierta (§11, D-2).

### 4.5 Wizard — selector de calendarios (Fase 3, mayor esfuerzo UX)

En **ambos** gemelos (`Coordinacion` y `Planning`):

- **Paso 1 rediseñado**:
  - Selector de lapso (igual).
  - Al elegir lapso → listar sus calendarios (id, `name`, badge de `status`,
    `quality_score`) con acciones: **Continuar** (abre ese calendario), **Activar**
    (solo si tiene slots; usa `activate()`), **Eliminar** (solo `draft`).
  - Botón **Nuevo borrador** → `createCalendar()` sin el guard `$exists`.
- **Switcher global** (arriba del wizard, visible cuando hay `calendarId`): select con
  todos los calendarios del lapso activo; al cambiar → `calendarId` nuevo +
  `loadCalendar()` + recargar `periodsList`/`lessons`/`availability` (usar
  `updatedCalendarId()` existente).
- `mount()`: intentar `activeForCurrentLapso()`, luego cualquier `draft` del mismo;
  si hay varios, mostrar el selector sin auto-elegir (o elegir el primero y dejar que
  el usuario cambie — mantener el comportamiento actual como fallback).
- Validación opcional: `name` único dentro del lapso a nivel app (no DB) para evitar
  confusión de nombres duplicados. Dejar como mejora menor.

### 4.6 Editor y suplencias (Fase 4)

- `TimetableEditor`: la ruta ya recibe `{calendar?}`. Cuando no llega `calendarId`
  **y** hay varios calendarios, mostrar selector de calendario (similar al del wizard)
  en vez del estado vacío actual.
- `TimetableSubstitutes`: ya lista calendarios; confirmar que el listado filtra
  `draft|active` (no `archived`) para no mostrar alternativas archivadas como
  seleccionables. Ajustar consulta si aplica.

### 4.7 Docs — actualizar spec (Fase 6)

En `SPEC-TIMETABLE-001-v2.md`:
- Flujo paso 1: quitar "único por lapso" → flujo multi-borrador + activación + democión.
- Tabla de `status`: documentar uso real de `archived` (democión).
- Añadir **ADR-TT-014: multi-calendario por lapso con único activo** (columna
  generada `active_lapso_key` + `uq_active_lapso`). Nota: ADR-TT-003 ya está
  tomado por la heurística del solver en la spec, por eso se usa TT-014.
- §15: aclarar que `version` es por-calendario; la carrera de "dos activos" la blinda
  `uq_active_lapso` + democión en `persist()`.
- TT-007 / TT-001: reflejar que el calendario ya no es 1:1 con el lapso y que los
  lectores resuelven "activo del lapso vigente".
- Bump del doc a v2.1 y enlazar este plan.

---

## 5. Plan de ejecución por fases (orden de implementación)

| Fase | Entregable | Archivos | Verificación |
|---|---|---|---|
| **F0** | Línea base verde | — | `php8.2 artisan test --filter=Timetable` (registrar baseline) |
| **F1** | Migración + modelo | esquema multi-calendario integrado en migración base, `TimetableCalendar.php` | migrar en local; `php8.2 artisan test --filter="Timetable"` |
| **F2** | Job: democión + carrera | `GenerateTimetableJob.php` | tests de democión y de índice DB |
| **F3** | Wizard: selector + nuevo borrador + activar/eliminar | `TimetableWizard.php` (Coordinacion y Planning) + blades | tests de flujo multi |
| **F4** | Lecturas + editor + suplencias | `SectionGrid` (x2), `MyTimetable`, `Student/Lms/Timetable`, `TimetableEditor` (x2), `TimetableSubstitutes` (x2) | tests de resolución activo |
| **F5** | Tests integrales | `tests/Feature/Timetable/*` | suite completa `php8.2 artisan test` |
| **F6** | Docs | `SPEC-TIMETABLE-001-v2.md` (v2.1, ADR-TT-014), este plan enlazado | revisión |
| **F7** | Seeders/smoke | `TimetableTestSeeder` (verificar no rompe) | `php8.2 artisan db:seed --class=TimetableTestSeeder` |

Cada fase termina con Pint (`./vendor/bin/pint`) y tests del módulo verdes antes de
pasar a la siguiente.

---

## 6. Pruebas a crear / modificar

**Modificar** (`tests/Feature/Timetable/TimetableWizardTest.php`):
- `test_calendar_is_unique_per_lapso` → **eliminar/reemplazar** por los nuevos.

**Crear** (nuevo archivo `tests/Feature/Timetable/MultiCalendarTest.php`):
- `test_multiple_drafts_allowed_per_lapso` — crear 2+ calendarios con `createCalendar()` sin error.
- `test_wizard_lists_calendars_of_lapso` — el listado del paso 1 muestra los del lapso.
- `test_switch_calendar_changes_context` — cambiar `calendarId` recarga lessons/availability.
- `test_persist_demotes_previous_active` — publicar B con A activo → A pasa a `archived`, B activo.
- `test_db_forbids_two_active_per_lapso` — `expectException(QueryException)` al forzar 2 activos (columna generada).
- `test_dry_run_keeps_active_calendar_intact` — dryRun de B no toca a A (sigue activo).
- `test_delete_draft_only` — borrar `draft` OK; borrar `active`/`archived` rechazado.
- `test_activate_promotes_draft_with_slots` — `activate()` demueve al activo previo.
- `test_readers_resolve_active_of_current_lapso` — `SectionGrid`/`MyTimetable` devuelven el activo correcto con varios calendarios (incl. `archived`) presentes.

Nota: los tests usan MySQL real con `DatabaseTransactions`; la columna generada es
reversible vía `down()` en la migración — los tests que crean 2 activos deben esperar
`QueryException` y gestionar la transacción (el error dentro de `DatabaseTransactions`
se revierte solo).

---

## 7. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Carrera en activación simultánea de 2 borradores | Violación de `uq_active_lapso` | `try/catch QueryException` + revert a `draft` + log (Fase 2) |
| Columnas generadas + `storedAs` en Laravel 10/MariaDB | Migración falla | Verificar versión MariaDB; usar SQL crudo `DB::statement()` como fallback |
| `mount()` del wizard con varios borradores | Contexto ambiguo al entrar | Preferir `activeForCurrentLapso()`; si hay varios drafts, mostrar selector |
| Calendarios archivados con ausencias/suplencias | Datos históricos "invisibles" | No se borran (FK solo cascade al eliminar draft); suplencias quedan auditables por `calendar_id` |
| Lectores `latest('id')` con 2 activos imposibles por DB | Ninguno (I-2 blinda) | Igual se migra a `activeForCurrentLapso()` por claridad semántica |
| `TimetableTestSeeder` asume 1 por lapso | Tests/smoke rotos | Verificar en F7; el seeder crea 1 por lapso → sigue válido |

---

## 8. Definición de listo (DoD)

- [ ] Migración aplicada y reversible; `uq_calendar_lapso` fuera, `uq_active_lapso` activo.
- [ ] `createCalendar()` permite N borradores por lapso; el guard `$exists` eliminado.
- [ ] `persist()` demueve al activo anterior; carrera controlada sin excepciones 500.
- [ ] Wizard y editor ofrecen selector de calendarios; eliminar solo borradores.
- [ ] 4 lectores migrados a `activeForCurrentLapso()`.
- [ ] Tests nuevos en verde; `test_calendar_is_unique_per_lapso` retirado.
- [ ] Spec actualizada a v2.1 (ADR-TT-014 + flujo + status).
- [ ] `./vendor/bin/pint` limpio.

---

## 9. Cambios de alcance (los que NO se hacen aquí)

| Fuera de alcance | Por qué |
|---|---|
| Horarios vigentes simultáneos por ventana de fecha en un mismo lapso | Cambia conflictos, solapamientos y todo el read-side (rediseño de fondo) |
| Duplicar calendario (copiar estructura completa) | Mejora menor, fase opcional post-v1 |
| `archived` con retención/borrado programado | Sin requerimiento; los datos se conservan por FK |
| Comparación visual de dos alternativas lado a lado | UX adicional, fase opcional post-v1 |

---

## 10. Decisiones abiertas (bloquean solo tareas opcionales)

- **D-1**: ¿Se permite **promover un borrador a activo sin regenerar** (solo si ya tiene
  slots), o la única vía a `active` es `persist()` del job? Recomendado: permitirlo vía
  `activate()` con guard "tiene slots" (flexibilidad para "volver a un plan anterior").
- **D-2**: ¿Degradación de lecturas si no hay lapso vigente pero sí un activo histórico?
  Recomendado: `latest('id')` como fallback.
- **D-3**: ¿Nombre de borrador único dentro del lapso (app-level)? Recomendado: sí, para
  evitar confusión; sin índice DB.

---

## 11. Referencias

- Spec normativa: `blueprint/school-timetable/SPEC-TIMETABLE-001-v2.md`
- Spec semilla: `blueprint/school-timetable/specDrive01.md`
- Migración base: `database/migrations/2026_08_15_000001_create_timetable_tables.php`
- Modelo: `app/Models/app/Timetable/TimetableCalendar.php`
- Job: `app/Jobs/Timetable/GenerateTimetableJob.php`
- Wizard: `app/Livewire/Coordinacion/Timetable/TimetableWizard.php` y `app/Livewire/Planning/Timetable/TimetableWizard.php`
- Lecturas: `SectionGrid` (Leadership/Director), `MyTimetable`, `Student\Lms\Timetable`, `TimetableViewService`