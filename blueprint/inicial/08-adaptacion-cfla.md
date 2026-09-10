# 08 — Adaptación a cfla (plan de migración)

> **Síntesis de los docs 01–07** aplicada al destino: cfla (Laravel 10 · Livewire 3 · Tailwind 3 · WireUI 2 · PHP 8.2 · BD `s2627`).
> ⚠️ Reglas del proyecto: **siempre `php8.2`** (el `php` del sistema es 7.4) · **JAMÁS dropear/vaciar la BD** (`migrate:fresh` prohibido incluso con `--path`; migraciones solo **aditivas** con `php8.2 artisan migrate`; tests con `DatabaseTransactions` tras `php8.2 artisan config:clear`).

## 1. Inventario de partida (verificado 2026-09-08)

### 1.1 Qué YA existe en cfla

| Activo | Estado |
|---|---|
| **19 tablas `ei*`** en `s2627` | Clonadas por SQL directo con **DDL idéntico** al legacy, **vacías** (no hay migraciones `ei*` en `s2627.migrations`) |
| Ecosistema de modelos | `Academy\{Pestudio, Grado, Seccion, Lapso, Profesor, Pensum, Pevaluacion, Peducativo, Pescolar, AreaConocimiento, Asignatura, Inscripcion}`, `Entity\{Institucion, Autoridad, Pescolar}`, `Learner\Estudiant` |
| **`pestudio` id 6 = "EDUCACION INICIAL"** | Mismo id en ambas BDs |
| Grados de Inicial | **22 (1ER GRUPO), 23 (2DO GRUPO), 24 (3ER GRUPO)** en `s2627` — los mismos `grado_id` que usa el `EILearningSeeder` del legacy |
| Conexión `s2526` | Ya definida en `config/database.php` de cfla |
| Users | `is_admin`, `is_planner`, `is_diagnostic`, `is_profesor` (boolean casts) — **NO existe `is_inicial`** |
| Profesor | `Academy\Profesor` con relación a `User` (118 users con `is_profesor=1`) |

### 1.2 Qué NO existe en cfla

Todo el código del módulo: modelos `Ei*` (18), rutas, middleware `is_inicial`, controladores, componentes Livewire, vistas, `EducationStatsService`. Escribirlo nuevo — con este blueprint como especificación.

### 1.3 Mapeo de namespaces legacy → cfla

| Legacy (`s2526`) | cfla (`s2627`) |
|---|---|
| `App\Models\app\Inicial\*` (18 Ei*) | `App\Models\app\Inicial\*` (misma familia — cfla ya usa carpetas-módulo: `Academy/`, `Entity/`, `Learner/`…; crear `app/Models/app/Inicial/`) |
| `App\Models\app\Pescolar\{Grado, Seccion, Lapso, Profesor, Pensum, Pevaluacion, Peducativo}` | `App\Models\app\Academy\{…}` |
| `App\Models\app\Institucion\{Institucion, Autoridad}` | `App\Models\app\Entity\{…}` |
| `App\Models\app\Estudiant` | `App\Models\app\Learner\Estudiant` |
| `App\Models\app\Pevaluacion` (puente) | `App\Models\app\Academy\Pevaluacion` (idéntico rol) |

## 2. Hallazgo clave de producción (condiciona las prioridades)

Conteos en la BD viva `s2526` (2026-09-08):

| Tabla | Filas | Tabla | Filas |
|---|---|---|---|
| eiplanningwks | **319** | eiprojectks | 19 |
| eiplanningwstrategies | **1,696** | eiprojectkstrategies | 322 |
| eiplanningwsummaries | **0** ⚠️ | eiprojectsummaries | 47 |
| eiplanningbwks | 6 | eiprojectreviews | 8 |
| eiplanningbwstrategies | 49 | eispecialks | 3 |
| eiplanningbwsummaries | 16 | eispecialstrategies / eispecialacts | 0 / 8 |
| eievaluationks | 24 | eievaluationps | 98 |
| **eifinalks / eifinalk_expectation** | **0 / 0** ⚠️ | **eilearningareas / eilearningexpectations** | **0 / 0** ⚠️ |

**Conclusiones:**

1. **El núcleo usado en producción es la planificación semanal** (319 planes, 1,696 estrategias de 9 profesores en 3 secciones) + evaluaciones (24/98) + proyectos (19). Esas son las entidades **P0** de migración de datos y funcionalidad.
2. **El subsistema de informes finales (`Eifinalk` + `Eilearningarea/Eilearningexpectation` + pivote) NUNCA operó en producción**: el catálogo de expectativas está vacío porque **el `EILearningSeeder` del legacy (162 inserts DB::table, namespace `Database\Seeders`, grado_ids 22–24) nunca se ejecutó**, y sin catálogo no hay informes. En cfla es **greenfield**: ejecutar el seeder (los grado_ids coinciden), construir el flujo de informes finales completo (con las tabs de expectativas que en el legacy están comentadas) y **no migrar datos** de eifinalks.
3. `eiplanningwsummaries = 0`: el docente nunca completó los resúmenes semanales (sí los quincenales=16 y de proyecto=47). El formulario de summaries es funcional pero poco usado — mantenerlo, pero no es crítico.

### Verificación FK s2526 → s2627 (hecha)

Los ids referenciados por los datos vivos **existen tal cual en cfla**: profesores 9/9, secciones 3/3, lapsos 1,2,3 → 3/3. La BD cfla es continuación de la misma población ⇒ **INSERT…SELECT directo, sin remapping de FKs**. (Verificar de nuevo en el momento de la migración con el mismo script del §4.3.)

## 3. Decisiones de adaptación

### D1 — Modelos: familia `App\Models\app\Inicial\`

Portar los 18 modelos con su estructura del legacy (doc 02), ajustando solo los `use` de los modelos relacionados (Academy/Entity/Learner). Mantener: `COLUMN_COMMENTS` (cfla ya usa el patrón), scopes, accessors `getEstrategiaAttribute`/`getOrdered*`/`getStrategyByMomentAndDay`, `getPevaluacions(List)`. **Sin SoftDeletes** (fiel al legacy y al DDL clonado).

### D2 — Roles: añadir `is_inicial` + reutilizar los existentes

El legacy resuelve el acceso con `rols` (area/rol/vigencia) — con superposición excesiva (cualquier `SISTEMA/ADMINISTRADOR` pasa los 4 checks; doc 07 §7.3). cfla usa flags booleanos simples. Propuesta:

| Perspectiva legacy | cfla |
|---|---|
| Docente Inicial (`is_inicial`) | **nuevo flag `is_inicial`** en `users` (migración aditiva `add_is_inicial_to_users_table`) + middleware `IsInicial` (`abort(403)` explícito — NO el null silencioso del legacy) |
| Evaluación (`is_evaluacion`) | `is_diagnostic` (o `is_admin`) — mantiene su rol de **revisión** (escribir `observacion`/`recomendacion`) |
| Planning (`is_planning`) | `is_planner` |
| Académico (`is_academico`) | `is_admin` (perspectiva directivo) |

Los 4 checks Is{X} del User del legacy NO se portan; los flags de cfla ya existen salvo `is_inicial`.

### D3 — Schema: clon idéntico + quirk `lunes` documentado

Mantener las 19 tablas tal cual clonadas. El **quirk de persistencia del grid** (texto siempre en `lunes`, día real en `day_of_week`) debe preservarse en la 1ª migración de datos (los 1,696+49+322 registros existentes tienen esa forma) y los accessors lo compensan en lectura. Normalizar (columna única `estrategia`) solo como refactor posterior CON script de transformación de datos — nunca automático. Ídem `order` NULLS-LAST (`CASE WHEN`) y `day_of_week`/`momento_rutina_diaria`.

### D4 — Rutas: paridad de estructura

```
routes/app/inicials.php  (requerido por web.php)
  /app/inicials/*  → auth + is_inicial        (docente: home, use-cases, 6 entidades CRUD+format)
  /app/evaluacions/inicials/*  → auth + is_diagnostic   (revisión + stats + format)
  /app/plannings/inicials/*   → auth + is_planner       (solo lectura server-side)
  /app/academicos/inicials/*  → auth + is_admin          (solo lectura limitada)
```
Nombres de ruta `inicials.*` (plural) — evitando los typos del legacy (`eiplanningwbks` wb, `inicilas`, `Actividaes`). `pestudio_id = 6` → constante de clase `Inicial::PESTUDIO_ID` o `config('inicial.pestudio_id')` en vez de hardcode.

### D5 — Stack frontend: traducción sistemática

| Legacy | cfla |
|---|---|
| Livewire 2.5 (`wire:model` sync, `dispatchBrowserEvent`, `emit`) | Livewire 3 (`wire:model.live`, `$this->dispatch()`, listeners nativos) |
| Bootstrap 4 cards/badges/dropdowns | Tailwind 3 + WireUI 2 |
| LaravelCollective `Form::select` | `<select>` nativo / WireUI `<x-select>` con `wire:model.live` |
| SweetAlert2 `dispatchBrowserEvent('swal')` + `window.livewire.emit` | WireUI `dialog()`/`notification()` + `$dispatch` |
| Modal `$modalType` switch + includes | WireUI Modals o componente propio; mantener la **anatomía de 5 bloques** (doc 04 §2) |
| Pagination `bootstrap-4` | Livewire 3 Tailwind |
| `confirm()` inline para deletes | WireUI confirmación uniforme (eifinalk no confirmaba NADA en el legacy) |
| Formatos A (CSS inline Arial uppercase) / B (Bootstrap 5.3 + `format.css` ausente) | componentes `x-formato.{membrete,seccion,firmas,footer}` + `@media print` determinista + `nl2br(e())` (doc 05 §10) |

### D6 — `Peducativo.max_number_*` (indicadores de stats)

cfla `Academy\Peducativo` **NO** tiene los campos `max_number_eiplanningwks/bwks/projectks/specialks/evaluationks/finalks`. Opciones: **(a) config** `config/inicial.php` con los 6 máximos (sin tocar schema — recomendado); (b) migración aditiva de 6 columnas nullable. En cualquier caso: NO portar los badges hardcodeados falsos (+12%, +5, 67%).

### D7 — Qué NO portar (lista consolidada)

- Capa muerta de UI: `table/`, `overlay/`, `forms/`, `partials/` vacío, `elements/` (doc 04 §8).
- `Evaluacion\Inicial\EifinalksComponent` (`dd()` + `save()` falso) y `Academico\Inicial\Eiplanningwk\IndexComponent` (doc 03 §B.7, §C).
- Código muerto de `Evaluacion\Tab\InicialController` (`getStats`, `clearCache`, `exportStats`, `getDashboardSummary`).
- 4 controladores huérfanos `p-*` del docente (doc 01).
- Vistas de perspectiva huérfanas (tablas de `evaluacions/inicilas/table/`, stubs de `academicos/`, copias lessons).
- Traits `$rules` muertos → **Form Requests** con las reglas INLINE del legacy como fuente de verdad (doc 03 §5.6).

## 4. Migración de datos (`s2526` → `s2627`)

### 4.1 Principios

1. **Solo INSERT aditivo** — nunca DROP/TRUNCATE/FRESH (regla absoluta del proyecto).
2. Tablas destino ya existen (DDL idéntico) — solo llenar.
3. **Orden padres → hijos** (las FK son CASCADE; insertar hijo antes que padre fallaría):
   1. `eilearningareas` → `eilearningexpectations` — **mejor vía `EILearningSeeder`** (portarlo a `database/seeders/` de cfla; grado_ids 22–24 ya válidos) en vez de copiar (las 2 tablas están vacías en s2526 — no hay nada que copiar).
   2. Cabeceras: `eiplanningwks`, `eiplanningbwks`, `eiprojectks`, `eispecialks`, `eievaluationks` (verificar previamente no-colisión de PKs).
   3. Hijos: `eiplanningwstrategies`, `eiplanningbwstrategies`, `eiprojectkstrategies`, `eispecialstrategies`, `eiplanningbwsummaries`, `eiprojectsummaries`, `eiprojectreviews`, `eispecialacts`, `eievaluationps`.
   4. `eifinalks`/`eifinalk_expectation`: **omitir** (vacías en origen; greenfield en destino).
4. Cada INSERT…SELECT **desde la conexión `s2526`** hacia la conexión default, con `DB::transaction` y log de filas insertadas.

### 4.2 Plantilla

```php
// php8.2 artisan tinker o un comando de migración dedicado (add-only)
$tablas = [ /* orden del §4.1 */ ];
foreach ($tablas as $t) {
    $n = DB::connection('s2526')->table($t)->count();
    $ids = DB::connection('s2526')->table($t)->pluck('id');
    $ya = DB::table($t)->whereIn('id', $ids)->count();
    if ($ya > 0) { Log::info("saltar $t: $ya ids ya existen"); continue; }
    // copiar en chunks con insertUsing:
    DB::table($t)->insertUsing(
        (array) DB::getSchemaBuilder()->getColumnListing($t),
        DB::connection('s2526')->table($t)->select(...)
    );
}
```

### 4.3 Pre-verificación FK (script de guardián)

Antes de insertar: para cada columna FK usada (`profesor_id`, `grado_id`, `seccion_id`, `lapso_id`, `pevaluacion_id`, `eiprojectk_id`), contar ids referenciados en s2526 que NO existan en s2627 (verificado hoy: 0 faltantes para profesores/secciones/lapsos; repetir para `pevaluacion_id` — 958 pevaluacions en s2526, verificar los ~usados por summaries/acts/evaluationps). Si hay faltantes, detenerse y construir el mapeo (profesor por `user_id`/CI, pevaluacion por clave natural profesor+pensum+seccion+lapso) antes de continuar.

## 5. Plan de fases

| Fase | Entregable | Contenido |
|---|---|---|
| **F0** — Fundaciones | Modelos `Ei*` (18) + `EILearningSeeder` portado | Familia `App\Models\app\Inicial\` con relaciones a Academy/Entity/Learner, COLUMN_COMMENTS, accessors (`getOrdered*`, `getStrategyByMomentAndDay`), scopes `byProfesor`/`byLapsoYSeccion`. Ejecutar seeder (áreas+expectativas por grados 22–24). |
| **F1** — Acceso | Flag `is_inicial` + middleware + rutas vacías | Migración aditiva `add_is_inicial_to_users_table`; `IsInicial` con `abort(403)`; `routes/app/inicials.php` con los 4 grupos (D4); home + navbar (doc 07 §8). |
| **F2** — CRUD docente P0 | `EiplanningwkComponent` (Livewire 3) | Anatomía 5 bloques (doc 04 §2): tarjetas + filtros + modal único + **wizard de estrategias** (nav-tabs días + pills momentos, "Guardar y Continuar"/"Guardar Todas", progreso por día). Form Request con reglas inline del legacy (doc 03 §A.1). Confirmación de borrado uniforme. |
| **F3** — CRUD docente resto | bwk, projectk (reviews+summaries+strategies), especialk (activities), evaluationk (positions) | Reutilizar el andamiaje de F2; corregir en ruta los bugs: `edit-strategy` roto, `deleteStrategy(day,momento)`, fecha sin datepicker, labels. `EifinalkComponent` completo CON tabs de expectativas activadas + acordeón + sync de pivote + condicionales `status_official` (doc 03 §A.6). |
| **F4** — Formatos de impresión | 6 formatos + membrete único | Componentes `x-formato.*` con `@media print`, `nl2br(e())`, membrete por página configurable, botón `window.print()` en el contenedor (doc 05 §10). Recuperar `css/einicial/format.css` del entorno desplegado legacy (ausente del checkout). |
| **F5** — Perspectivas | Evaluación (revisión) + Planning + Académico | Evaluación: 6 tabs con Livewire + escritura limitada `observacion`/`recomendacion` (regla `min:5`) + stats (`EducationStatsService` portado, máximos por config D6, badges reales o sin badges). Planning/Académico: solo lectura server-side (sin Livewire). Corregir: mount desalineado (grado→sección), `groupBy` sin agregado, filtros de sección uniformes. |
| **F6** — Migración de datos | Comando `inicial:migrate-legacy` | INSERT…SELECT del §4 con verificación FK previa, chunks, log, idempotencia (skip si ids ya existen). Nunca destructivo. |
| **F7** — Tests y validación | Suite PHPUnit | `php8.2 artisan config:clear` antes de testear; `DatabaseTransactions` sobre BD real; feature tests de: CRUD semanal (flujo wizard completo), validaciones inline, revisión del coordinador (observación/recomendación), informes finales (create+sync de expectativas por status_official), formatos (200 + estructura), permisos (403 sin is_inicial). |

**Orden de dependencia:** F0→F1→F2→{F3, F4, F5}→F6→F7. F3/F4/F5 pueden avanzar en paralelo tras F2.

## 6. Bugs del legacy a corregir en cfla (checklist "must-fix")

Consolidado desde los docs 01/02/03/04/05/07 — se corrigen por diseño en las fases:

1. `dd()` en `EifinalksComponent` y `save()` falso → componente no se porta (F5).
2. `Eipedagogicalk` (formato informe pedagógico) reutiliza `$eievaluationk` + filtrado por área anulado → decidir: rehacer como vista real de Eifinalk o descartar (F4).
3. Quirk `lunes` documentado y accessors portados; cualquier UI nueva escribe vía `setEstrategiaAttribute` (F0/F2).
4. `Eiplanningbwk::getOrderedViews()` no existe / relación `eiprojectk` de bwk rota en detalles → usar `getOrderedViews()` de projectk solo donde existe (F3).
5. `deleteStrategy` firma `(day, momento)` consistente en toda la UI nueva (F2).
6. Mount desalineado grado→sección en perspectiva Evaluación → props nombradas (F5).
7. `as_replace()` con `{!! !!}` → `nl2br(e())` en todo (F4).
8. Fechas `h:m` → `h:i` (F4); `fecha` type text → datepicker (F3).
9. `EifinalkComponent (Evaluación)` `groupBy` sin agregado → `get()->groupBy()` colección (F5).
10. `Eifinalk` casts con `'estudiantes' => 'array'` huérfano y accessors delegados colisionando con relaciones — limpiar en el port (F0).
11. 403 explícito en middleware + componente (no null silencioso) (F1/F2).
12. Títulos "Planificación Educ. Primaria" → "Educación Inicial" (F5).
13. `Lapso::current()` con fallback no determinista → portar con fallback explícito `status_active` (F0).
14. `Eilearningarea::scopeActive` usa `whereHas` sobre expectations (área sin expectativas desaparece) — decisión consciente al portar (F0).

## 7. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| `migrate:fresh`/DROP accidental | Regla absoluta CLAUDE.md; migraciones aditivas; migración de datos por comando propio idempotente |
| `css/einicial/format.css` y assets `vendor/*` ausentes (no hay `public/` en el checkout) | Recuperar del entorno desplegado legacy antes de F4; o rediseñar los formatos B con Tailwind |
| FKs de s2526 no presentes en s2627 a futuro (datos nuevos) | Script guardián §4.3 obligatorio antes de F6; abortar si hay faltantes |
| Quirk `lunes` mal entendido por código nuevo | Centralizar lectura/escritura en `get/setEstrategiaAttribute`; prohibir escribir columnas de día directamente |
| Superposición de roles del legacy heredada a cfla | Flags cfla granulares + revisión de quién recibe `is_inicial` |
| Loss del flujo de expectativas (tabs comentadas en el legacy) | F3 lo construye completo; el seeder de F0 es prerrequisito |
| `orderBy` de `order` NULLS-LAST en MySQL | Portar el `CASE WHEN` tal cual (ya probado en producción) |

## 8. Checklist de validación final

- [ ] 19 tablas `ei*` intactas en `s2627` (schema sin cambios destructivos).
- [ ] Seeder ejecutado: áreas × 3 grados (22–24) con expectativas visibles en el UI.
- [ ] CRUD semanal: crear plan → wizard 50 celdas → guardar → reabrir → estrategia visible en la celda correcta (día/momento) → PDF.
- [ ] Coordinador (is_diagnostic): ver estrategias read-only, escribir observación (min:5), ver stats con máximos por config.
- [ ] Informe final: seleccionar pevaluación → estudiantes del lapso → expectativas por acordeón → save con sync → `print-all-for-lapso` separa oficial/componente.
- [ ] Migración de datos idempotente re-ejecutable (skip por id) con log de filas.
- [ ] Suite de tests verde (`php8.2 artisan test` tras `config:clear`).
- [ ] Ningún uso de `dd()`, `{!! !!}` sin escapar, ni `migrate:fresh` en el código entregado.

---

*Con este documento el blueprint del módulo Inicial queda completo: 01 rutas/controladores · 02 modelos/schema · 03 Livewire · 04 vistas/UI · 05 formatos · 06 casos de uso funcionales · 07 perspectivas/roles · 08 adaptación cfla.*
