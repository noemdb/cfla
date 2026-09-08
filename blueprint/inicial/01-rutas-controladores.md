# 01 — Rutas y Controladores · Módulo Inicial (legacy s2526)

> Fuente verificada: `saefl/s2526/routes/web.php`, `routes/app/inicials.php`, `routes/app/iniciales.php`, `routes/app/tab/inicials/*.php`, `routes/app/tab/evaluacions/inicials.php`, `routes/app/tab/plannings/inicials.php`, `routes/app/tab/academicos/inicials.php`, `app/Http/Controllers/Inicial/Tab/*.php`, `app/Http/Controllers/{Evaluacion,Planning,Academico}/Tab/InicialController.php`.

---

## 1. Montaje en `routes/web.php`

Cuatro grupos de rol montan el módulo. Todos cuelgan del prefijo **`/app`**:

```php
// Docente Inicial (web.php ~línea 60)
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Inicial'], function () {
    Route::group(['prefix' => 'inicials', 'middleware' => ['is_inicial']], function () {
        require (__DIR__ . '/app/inicials.php');
    });
});

// Evaluación (web.php ~línea 253)
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Evaluacion'], function () {
    Route::group(['prefix' => 'evaluacions', 'middleware' => ['is_evaluacion']], function () {
        Route::get('/home', 'HomeController@home')->name('evaluacions.home');
        require (__DIR__ . '/app/evaluacions.php');   // línea 48: require tab/evaluacions/inicials.php
    });
});

// Planning (web.php ~línea 47)
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Planning'], function () {
    Route::group(['prefix' => 'plannings', 'middleware' => ['is_planning']], function () {
        require (__DIR__ . '/app/plannings.php');       // require tab/plannings/inicials.php
    });
});

// Académico (web.php ~línea 237)
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Academico'], function () {
    Route::group(['prefix' => 'academicos', 'middleware' => ['is_academico']], function () {
        Route::get('/home', 'HomeController@home')->name('academicos.home');
        require (__DIR__ . '/app/academicos.php');      // línea 39: require tab/academicos/inicials.php
    });
});
```

`routes/app/inicials.php` (docente) es solo un loader de pestañas:

```php
require (__DIR__ . '/tab/inicials/home.php');
require (__DIR__ . '/tab/inicials/eiplanningwks.php');
require (__DIR__ . '/tab/inicials/eiplanningbwks.php');
require (__DIR__ . '/tab/inicials/eiprojectks.php');
require (__DIR__ . '/tab/inicials/eispecialks.php');
require (__DIR__ . '/tab/inicials/eievaluationks.php');
require (__DIR__ . '/tab/inicials/eifinalks.php');
```

> Nota: `routes/app/iniciales.php` (13 líneas) NO pertenece a este módulo — es la ruta raíz `/home` de Inscripciones (`Tab\InscripcionController@book`). Coincide por nombre, no confundir.

## 2. Tabla completa de rutas

### 2.1 Docente Inicial — prefijo `/app/inicials` (auth + is_inicial)

| # | Método | URI | Nombre | Acción (`App\Http\Controllers\Inicial\Tab\…`) |
|---|---|---|---|---|
| 1 | GET | `/app/inicials/home` | `inicials.home` | `HomeInicialController@home` |
| 2 | GET | `/app/inicials/use-cases` | `inicials.use-cases` | `HomeInicialController@useCases` |
| 3 | GET | `/app/inicials/eiplanningwks` | `inicials.eiplanningwks.index` | `EiplanningwkController@index` |
| 4 | GET | `/app/inicials/eiplanningwks/format/index/{id}` | `inicials.eiplanningwks.format.index` | `EiplanningwkController@format` |
| 5 | GET | `/app/inicials/eiplanningbwks` | `inicials.eiplanningbwks.index` | `EiplanningbwkController@index` |
| 6 | GET | `/app/inicials/eiplanningbwks/format/index/{id}` | `inicials.eiplanningbwks.format.index` | `EiplanningbwkController@format` |
| 7 | GET | `/app/inicials/eiprojectks` | `inicials.eiprojectks.index` | `EiprojectkController@index` |
| 8 | GET | `/app/inicials/eiprojectks/format/{id}` ⚠️ | `inicials.eiprojectks.format.index` | `EiprojectkController@format` |
| 9 | GET | `/app/inicials/eispecialks` | `inicials.eispecialks.index` | `EispecialkController@index` |
| 10 | GET | `/app/inicials/eispecialks/format/index/{id}` | `inicials.eispecialks.format.index` | `EispecialkController@format` |
| 11 | GET | `/app/inicials/eievaluationks` | `inicials.eievaluationks.index` | `EievaluationkController@index` |
| 12 | GET | `/app/inicials/eievaluationks/format/index/{id}` | `inicials.eievaluationks.format.index` | `EievaluationkController@format` |
| 13 | GET | `/app/inicials/eifinalks` | `inicials.eifinalks.index` | `EifinalkController@index` |
| 14 | GET | `/app/inicials/eifinalks/{eifinalk}/print` | `inicials.eifinalks.print` | `EifinalkController@print` |
| 15 | GET | `/app/inicials/eifinalks/estudiant/{estudiant}/lapso/{lapso}/print-all` | `inicials.eifinalks.print-all-for-lapso` | `EifinalkController@printAllforLapso` |

⚠️ **Patrón inconsistente**: la ruta 8 usa `/format/{id}` mientras el resto usa `/format/index/{id}` (bug de consistencia del legacy; unificar en la migración).

### 2.2 Evaluación — prefijo `/app/evaluacions` (auth + is_evaluacion)

Archivo: `routes/app/tab/evaluacions/inicials.php`. Controlador: `App\Http\Controllers\Evaluacion\Tab\InicialController`.

| # | Método | URI | Nombre | Acción |
|---|---|---|---|---|
| 1 | GET | `/app/evaluacions/inicials/index` | `evaluacions.inicials.index` | `index` — dashboard con filtros + estadísticas |
| 2 | GET | `/app/evaluacions/inicials/eiplanningwks/format/index/{id}` | `evaluacions.eiplanningwks.format.index` | `format_eiplanningwk` |
| 3 | GET | `/app/evaluacions/inicials/eiplanningbwks/format/index/{id}` | `evaluacions.eiplanningwbks.format.index` ⚠️ | `format_eiplanningbwk` |
| 4 | GET | `/app/evaluacions/inicials/eiprojectks/format/index/{id}` | `evaluacions.eiprojectks.format.index` | `format_eiprojectks` |
| 5 | GET | `/app/evaluacions/inicials/eispecialks/format/index/{id}` | `evaluacions.eispecialks.format.index` | `format_eispecialks` |
| 6 | GET | `/app/evaluacions/inicials/eievaluationks/format/index/{id}` | `evaluacions.eievaluationks.format.index` | `format_eievaluationks` |
| 7 | GET | `/app/evaluacions/eifinalks/estudiant/{estudiant}/lapso/{lapso}/print-all` | `evaluacions.eifinalks.print-all-for-lapso` | `printAllforLapso` |
| 8 | GET | `/app/evaluacions/use-cases` | `evaluacions.inicials.use-cases` | `useCases` |

⚠️ Nombre con typo `eiplanningwbks` (bw→wb) en la ruta 3 — mantener mapeo al corregir en cfla.
ℹ️ El archivo importa `HomeInicialController` pero nunca lo usa (import huérfano).
ℹ️ Métodos del controlador **sin ruta**: `getStats`, `clearCache`, `exportStats`, `getDashboardSummary` (endpoints JSON preparados, no cableados en este archivo de rutas).

### 2.3 Planning — prefijo `/app/plannings` (auth + is_planning)

Archivo: `routes/app/tab/plannings/inicials.php`. Controlador: `App\Http\Controllers\Planning\Tab\InicialController`.

| # | Método | URI | Nombre | Acción |
|---|---|---|---|---|
| 1 | GET | `/app/plannings/inicials/index` | `plannings.inicials.index` | `index` — listados con filtros |
| 2 | GET | `/app/plannings/inicials/eiplanningwks/format/index/{id}` | `plannings.eiplanningwks.format.index` | `format_eiplanningwk` |
| 3 | GET | `/app/plannings/inicials/eiplanningbwks/format/index/{id}` | `plannings.eiplanningbwks.format.index` | `format_eiplanningbwk` |
| 4 | GET | `/app/plannings/inicials/eiprojectks/format/index/{id}` | `plannings.eiprojectks.format.index` | `format_eiprojectks` |

ℹ️ El controlador también define `format_eispecialks` y `format_eievaluationks` pero **no tienen rutas** (métodos muertos en esta perspectiva).

### 2.4 Académico — prefijo `/app/academicos` (auth + is_academico)

Archivo: `routes/app/tab/academicos/inicials.php`. Controlador: `App\Http\Controllers\Academico\Tab\InicialController`.

| # | Método | URI | Nombre | Acción |
|---|---|---|---|---|
| 1 | GET | `/app/academicos/inicials/index` | `academicos.inicials.index` | `index` — solo planes semanales y proyectos |
| 2 | GET | `/app/academicos/inicials/eiplanningwks/format/index/{id}` | `academicos.eiplanningwks.format.index` | `format_eiplanningwk` |
| 3 | GET | `/app/academicos/inicials/eiprojectks/format/index/{id}` | `academicos.eiprojectks.format.index` | `format_eiprojectks` |

## 3. Controladores

### 3.1 `Inicial\Tab\HomeInicialController` (105 líneas)

- Middleware: `auth`, `is_inicial` + closure inyecta `User`, `Autoridad` (del usuario) y `Autoridad::COLUMN_COMMENTS`.
- `home()` → vista `inicials.home` con `user`, `autoridad`, `list_comment_autoridad`, `lapsos = Lapso::all()`, `lapso_active = Lapso::current()`.
- `useCases()` → vista `inicials.use-cases` con el arreglo `getUseCasesData()` (7 casos de uso: authentication, weekly-planning, classroom-projects, evaluations, pedagogical-reports, special-reports, export-print — solo título/descripción/icono/color).

### 3.2 Controladores de pestaña del docente (`Inicial\Tab\*`)

Patrón uniforme — controladores **delgados**, sin lógica de negocio (el CRUD lo hace Livewire):

| Controlador | `index()` pasa a la vista | Vista índice | Componente Livewire embebido | `format($id)` |
|---|---|---|---|---|
| `EiplanningwkController` (37 lín.) | `profesor` (del user autenticado) | `inicials.eiplanningwks.index` | `<livewire:inicial.eiplanningwk-component />` | `livewire.inicial.formats.eiplanningwk.index` |
| `EiplanningbwkController` (60 lín.) | `user`, `autoridad`, `list_comment_autoridad`, `lapsos`, `lapso_active` | `inicials.eiplanningbwks.index` | `<livewire:inicial.eiplanningbwk-component />` | `livewire.inicial.formats.eiplanningbwk.index` |
| `EiprojectkController` (37 lín.) | `profesor` | `inicials.eiprojectks.index` | `<livewire:inicial.eiprojectk-component />` | `livewire.inicial.formats.eiprojectks.index` |
| `EispecialkController` (34 lín.) | `profesor` | `inicials.eispecialks.index` | `<livewire:inicial.eispecialk-component />` | `livewire.inicial.formats.eispecialks.index` |
| `EievaluationkController` (52 lín.) | `user`, `autoridad`, `list_comment_autoridad`, `lapsos`, `lapso_active` | `inicials.eievaluationks.index` | `<livewire:inicial.eievaluationk-component />` | `livewire.inicial.formats.eievaluationk.index` |
| `EifinalkController` (128 lín.) | `user`, `autoridad`, `list_comment_autoridad`, `lapsos`, `lapso_active` | `inicials.eifinalks.index` | `<livewire:inicial.eifinalk-component />` | `print(Eifinalk)` + `printAllforLapso(Estudiant, Lapso)` (ver §3.3) |

Todos los `format()` hacen lo mismo: `Model::findOrFail($id)`, `Profesor::where('user_id', auth()->id())`, `Institucion::orderBy('created_at','DESC')->first()`, `fecha = Carbon::now()->format('d-m-Y h:m A')` y renderizan la vista de formato imprimible.

`Lapso::current()` — lapso académico vigente (definido en `App\Models\app\Pescolar\Lapso`).

### 3.3 `EifinalkController` — impresión de informes finales

- `print(Eifinalk $eifinalk)` → carga `pevaluacion.pensum.grado`, `pevaluacion.seccion`, `pevaluacion.lapso`, `pevaluacion.profesor`, `estudiant`, `expectations.area` → vista `livewire.inicial.formats.eifinalks.index`.
- `printAllforLapso(Estudiant, Lapso)` → busca los `eifinalks` del estudiante cuyo `pevaluacion.lapso_id` sea el lapso dado y los separa en **3 colecciones**:
  - `eifinalks` — todos, ordenados por `order ASC`
  - `eifinalks_oficial` — solo `pevaluacion.status_official = true`
  - `eifinalks_component` — solo `status_official = false`
  → vista `livewire.inicial.formats.eifinalks.index-all`. Si no hay informes, redirige con warning.

### 3.4 `Evaluacion\Tab\InicialController` (630 líneas) — el más rico

Middleware `auth + is_evaluacion`; inyecta `EducationStatsService`.

| Método | Qué hace |
|---|---|
| `index(Request)` | Lee filtros `profesor_id`, `grado_id`, `seccion_id` del query string. Caché de 5 min por combinación (`inicial_data_{p}_{g}_{s}`). `getFilteredData()` trae las 5 colecciones (planes semanales/bisemanales, proyectos, planes especiales, evaluaciones) con eager loading `profesor, grado, seccion, eiprojectk / lapso` + `applyFilters()`. Stats vía `EducationStatsService::getEducationStats()`. Listas: `Grado::list_pestudio_grado(6)`, `Profesor::list_profesors_pestudio(6)`. Vista `evaluacions.inicilas.index`. |
| `getFilteredData()` | Colecciones de las 5 entidades, `orderBy created_at desc`, `applyFilters` por grado/profesor/sección. |
| `applyFilters($query,…)` | Where condicionales por `grado_id`, `profesor_id`, `seccion_id`. |
| `getStats()` | JSON: `educationStatsService->getEducationStats(profesor_id, grado_id)`. |
| `clearCache()` | `Cache::forget` del caché de datos + `education_stats_{p}_{g}`. |
| `format_eiplanningwk/bwk/eiprojectks/eispecialks/eievaluationks($id)` | Igual que los del docente pero con `findOrFail` + try/catch + log; la variante `format_eifinalks($id)` carga el informe final con `pevaluacion.*` completo. |
| `printAllforLapso(Estudiant, Lapso)` | Mismo flujo que el del docente (3 colecciones oficial/componente). |
| `exportStats()` | JSON — stub ("lógica de exportación a Excel" pendiente en el legacy). |
| `getDashboardSummary()` | JSON — `total_records`, `recent_activity` (planes semanales de últimos 7 días, max 10), `pending_tasks` (proyectos con `ffinal` nula), `completion_rate` (activeProjects/eiprojectks). |
| `useCases()` + `getUseCasesData()` | **Duplicado literal** de `HomeInicialController` (mismos 7 casos de uso) — vista `inicials.use-cases`. |

### 3.5 `Planning\Tab\InicialController` (114 líneas) y `Academico\Tab\InicialController` (73 líneas)

Ambos replican el patrón `index` con filtros **sin caché y sin eager loading** (queries planas + where condicional):

- **Planning**: las 5 colecciones (semanal, bisemanal, proyectos, especiales, evaluaciones) + `list_grado`/`list_profesors` (pestudio 6) → vista `plannings.inicilas.index`.
- **Académico**: solo `eiplanningwks` y `eiprojectks` → vista `academicos.inicilas.index`.

### 3.6 Controladores huérfanos (⚠️ no cableados a rutas — no migrar)

| Controlador | Líneas | Evidencia |
|---|---|---|
| `Inicial\Tab\EievaluationpController` | 45 | Sin rutas; `home()` duplica `EievaluationkController@index` |
| `Inicial\Tab\EifinalpController` | 45 | Sin rutas; `home()` duplica `EifinalkController@index` |
| `Inicial\Tab\EiprojectpController` | 45 | Sin rutas; `home()` duplica `EiprojectkController@index` |
| `Inicial\Tab\EispecialpController` | 45 | Sin rutas; `home()` duplica `EispecialkController@index` |

*(Verificado con grep: ninguna referencia en `routes/` ni en `app/Http/Livewire/`. Son residuos de una refactorización: los 4 tienen `home()` en vez de `index()` y middleware idéntico.)*

## 4. Bugs y anomalías detectados en rutas/controladores

| # | Bug | Archivo | Detalle |
|---|---|---|---|
| 1 | **Fecha mal formateada en formatos impresos** | Todos los `format()` del docente, Planning y Académico | `Carbon::now()->format('d-m-Y h:m A')` — `m` es **mes**, no minutos (minutos es `i`). Imprime p.ej. "08" en vez de los minutos. Correcto solo en `Evaluacion\Tab\InicialController` (`h:i A`). |
| 2 | URI inconsistente eiprojectks | `tab/inicials/eiprojectks.php` | `/format/{id}` vs `/format/index/{id}` del resto. |
| 3 | Typo en nombre de ruta | `tab/evaluacions/inicials.php:12` | `evaluacions.eiplanningwbks.format.index` (wb↔bw). |
| 4 | Typo en nombre de vistas | `evaluacions/inicilas`, `plannings/inicilas`, `academicos/inicilas` | "inicilas" en vez de "iniciales" (3 directorios). |
| 5 | Import sin uso | `tab/evaluacions/inicials.php` | `HomeInicialController` importado, nunca referenciado. |
| 6 | Código duplicado | `HomeInicialController` vs `Evaluacion InicialController` | `getUseCasesData()` idéntico en ambos. |
| 7 | Endpoints JSON sin rutas | Evaluación | `getStats`, `clearCache`, `exportStats`, `getDashboardSummary` existen pero no están cableados. |
| 8 | Sin paginación | Todas las perspectivas | `->get()` sin paginar (heredan el patrón "DataTables client-side" del legacy). |

## 5. Notas para la migración a cfla

1. **Un solo controlador por perspectiva** o directamente Livewire full-page components en cfla; el CRUD ya es 100 % Livewire, los controladores solo son "page shells".
2. Unificar el patrón de URI de formatos: `inicial/formats/{entity}/{id}` con un nombre `inicial.{entity}.format`.
3. Corregir `h:m` → `h:i` en la fecha de los formatos.
4. Los formatos imprimibles no deben depender de `auth()->user()` para el `profesor` cuando el que imprime es otro rol (Evaluación/Planning imprimen planes de otros profesores: en el legacy el `profesor` mostrado en el membrete es el **autenticado**, no el dueño del plan — comportamiento a revisar; el docente imprime los suyos, pero el evaluador imprimiría "su" nombre en el membrete del plan de otro docente — bug conceptual del legacy).
5. `use-cases` es documentación embebida (7 casos de uso) — en cfla conviene un solo endpoint/rol dueño del contenido.
6. Las perspectivas Planning/Académico/Evaluación pueden consolidarse en una vista parametrizada por rol con filtros, en lugar de 3 controladores casi idénticos.
