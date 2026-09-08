# 07 — Perspectivas de revisión (Evaluación, Planning, Académico), roles y acceso

> **Fuente:** `saefl/s2526/app/Http/Livewire/Evaluacion/Inicial/` (7 componentes) · `resources/views/{evaluacions,plannings,academicos}/inicilas/` · `app/Http/Controllers/{Evaluacion,Planning,Academico}/Tab/InicialController.php` · `app/Http/Middleware/Admin/IsInicial.php` · `app/Models/sys/Rol.php` · `app/Services/EducationStatsService.php` · `app/User.php` · `resources/views/inicials/layouts/`.
> Rutas completas por perspectiva: ver [`01-rutas-controladores.md`](01-rutas-controladores.md). Este documento cubre **vistas, componentes, stats, sistema de roles y navegación**.
> Nomenclatura: el directorio de vistas se llama `inicilas` — **typo histórico de "iniciales"** — en las tres perspectivas.

## 1. Panorama: un mismo dato, 3 perspectivas de revisión + el docente

| Aspecto | **Evaluación** | **Planning** | **Académico** |
|---|---|---|---|
| Layout | `evaluacions.layouts.dashboard.app` | `plannings.layouts.dashboard.app` | `academicos.layouts.dashboard.app` |
| Pestañas activas | **6 de 6** | 5 de 6 (6ª = "Sin formato.") | **2 de 6** |
| Render de datos | **Componentes Livewire (6)** | Includes Blade server-side | Includes Blade server-side |
| Escritura | **SÍ**: observaciones + recomendaciones | No | No |
| Filtro sección | Leído y aplicado (cache) | Leído, **no aplicado** | Leído, no aplicado |
| Stats | Sí (3 cards + 6 indicadores + modal info) | No | No |
| Cache | 300 s `inicial_data_{p}_{g}_{s}` | No | No |
| Rutas de formato | 5 + printAll + use-cases | 5 | 2 |
| Título del botón en su navbar | "Planificación Educ. Primaria" **(erróneo)** | "Planificación Educ. Primaria" **(erróneo)** | "Educ. Inicial" (correcto) |

> **Hallazgo clave:** la perspectiva Evaluación **NO es de solo lectura total** — sus componentes Livewire persisten `observacion` (planes) y `recomendacion` (evaluaciones): es la función de **revisión del Coordinador de Evaluación**. Planning y Académico sí son solo lectura.

## 2. Vistas índice de las tres perspectivas

### 2.1 Evaluación — `resources/views/evaluacions/inicilas/index.blade.php` (159 líneas)

- Encabezado: "Educación Inicial, Formatos de Planificación" con icono `$icon_menus['inicials']`.
- **Filtros (form GET, NO Livewire):** `Form::open(['route' => 'evaluacions.inicials.index', 'method' => 'GET'])` con selects `profesor_id` (`$list_profesors` ← `Profesor::list_profesors_pestudio(6)`) y `grado_id` (`$list_grado` ← `Grado::list_pestudio_grado(6)`); botón "Buscar" + refresh a `url()->current()`. **No hay select de sección en el formulario** (el controlador sí lee `seccion_id` y lo aplica).
- **Estadísticas:** incluye `evaluacions.inicilas.stats.main` (solo esta perspectiva; §4).
- **6 pestañas** (`nav-tabs nav-fill`), todas habilitadas, cada una embebe un componente Livewire con los filtros como props: `<livewire:evaluacion.inicial.eiplanningwk-component :profesor_id="$profesor_id" :grado_id="$grado_id" />` · `eiplanningbwk-component` · `eiprojectk-component` · `eispecialk-component` · `eievaluationk-component` · `eifinalk-component`.
- `@section('sweetalert')`: listeners browser `swal`/`swal:confirm`/`swal:question` que emiten `window.livewire.emit('remove'/'method', id)`.

### 2.2 Planning — `resources/views/plannings/inicilas/index.blade.php` (102 líneas)

- Mismo patrón de filtros GET (route `plannings.inicials.index`), sin stats.
- **6 pestañas, paneles por include server-side (NO Livewire):** `@include('plannings.inicilas.table.eiplanningwks')` · `table.eiplanningbwks` · `table.eiprojectks` · `table.eispecialks` · `table.eievaluationks` · **Informe Pedagógico → solo texto "Sin formato."** (pestaña vacía).

### 2.3 Académico — `resources/views/academicos/inicilas/index.blade.php` (110 líneas)

- Include comentado `academicos.boletins.menus.index`; mismos filtros (route `academicos.inicials.index`).
- **6 pestañas, solo 2 habilitadas:** Plan Semanal (activa → `table.eiplanningwks`) · Plan Quincenal **disabled** · Proyecto de Aula (activa → `table.eiprojectks`) · Plan Especial **disabled** · Plan de Evaluación **disabled** · Informe Pedagógico **disabled**. Los disabled muestran placeholders "Content N".
- Es la perspectiva más limitada: solo consulta planes semanales y proyectos de aula.

## 3. Tablas server-side (includes Blade)

### 3.1 `plannings/inicilas/table/` (USADAS por el index de Planning)

Estructura idéntica entre tipos; rutas de acción PDF a `plannings.*.format.index`:

| Tabla | Columnas | Particularidades |
|---|---|---|
| `eiplanningwks` | N°, Inscripción, F.Inicial–F.Final, T.Resumen, Estrategias, Acciones | **Dos filas por registro con `rowspan=2`:** fila 1 Diagnóstico; fila 2 grado+sección / `profesor->fullname` / fechas `f_date` / `summaries`(objetivo) / `strategies`(momento_rutina_diaria) |
| `eiplanningbwks` | (sin col. Estrategias) | ⚠️ **BUG copy-paste: ambos botones apuntan a `plannings.eiplanningwks.format.index` (formato semanal, no quincenal)** |
| `eiprojectks` | Profesor, Inscripción, F.Inicial, F.Final, Tiempo, Diagnóstico, Revisión (`eleccion_tema_nombre`), T.Resumen (objetivo) | |
| `eispecialks` | …, Justificación, Observación, T.Actividades (`aprendizaje_esperado`+`indicadores`) | |
| `eievaluationks` | …, Momento (`lapso->name`), "Actividaes" (**typo**) = `eievaluationps` (`nombre_ninos`+`aprendizaje_alcanzado`) | Comentario Blade con la lista de campos |

### 3.2 `evaluacions/inicilas/table/` (HUÉRFANAS — grep confirmó que NADIE las incluye)

Mismas estructuras con rutas `evaluacions.*.format.index` (el index de Evaluación usa Livewire, no estas tablas). `eiplanningbwks` con el mismo bug del botón quincenal→semanal. `table/index.blade.php` es una **copia huérfana de la tabla "lessons" del LMS** ($lessons, autor, getAreaConocimientoLeaderId, pevaluacion, `wire:click showImagen`).

### 3.3 `academicos/inicilas/table/`

- Con contenido real: `eiplanningwks` (ruta `academicos.eiplanningwks.format.index`) y `eiprojectks` (ruta `academicos.eiprojectks.format.index`; ⚠️ **bug de sintaxis: comilla extra tras el href**; ⚠️ **título "Formato Planificación semanal" aunque es Proyecto de Aula**).
- `eiplanningbwks`, `eispecialks`, `eievaluationks`: **stubs de una palabra** (contenido literal "eiplanningbwks", etc.).
- `table/index.blade.php`: otra copia huérfana de lessons.

## 4. Estadísticas (exclusivas de la perspectiva Evaluación)

### 4.1 `evaluacions/inicilas/stats/main.blade.php` (278 líneas)

- Header "Indicadores de Planificación" con "Actualizado: `{{$last_updated ?? now()->format('H:i')}}`" + botón info → modal `evaluacions/inicilas/partials/info.blade.php` (`#indicadoresModal`, modal-lg scrollable) con descripciones textuales de los 9 indicadores.
- **3 cards principales** desde `$education_stats['totalRecords']`, `activeProjects`, `completedEvaluations`, con **badges decorativos hardcodeados** (+12%, +5, 67%) — **dato falso, solo estético**.
- **6 indicadores detallados** (`$detailIndicators`) comparando el conteo de cada tipo contra máximos configurados en el período educativo:

| Indicador | Máximo (`Peducativo`) |
|---|---|
| Planificaciones semanales | `max_number_eiplanningwks ?? 50` |
| Planificaciones quincenales | `max_number_eiplanningbwks ?? 30` |
| Proyectos de aula | `max_number_eiprojectks ?? 20` |
| Planes especiales | `max_number_eispecialks ?? 15` |
| Planes de evaluación | `max_number_eievaluationks ?? 25` |
| Informes pedagógicos | `max_number_eifinalks ?? 40` |

  `$peducativo` se resuelve con fallback en cascada: `$peducativo ?? ($eiplanningwks->first()->peducativo ?? ($eiprojectks->first()->peducativo ?? null))`. Progress bars de % de cumplimiento; check-circle si ≥ máximo, warning triangle si ≥ 80 %. Alert de configuración con `$peducativo->name`; CSS hover-lift/fadeInUp.

- `livewire/evaluacion/inicial/partials/info.blade.php` (116 líneas): "Configuración de Límites Máximos para Indicadores de Gestión" — cards describiendo cada tipo de plan, con ejemplo de cálculo "6 áreas × 40 estudiantes = 240 informes parciales + 40 finales".
- Los `max_number_*` viven en el modelo **`Peducativo`** (fillable + `COLUMN_COMMENTS` con labels en español).

### 4.2 `EducationStatsService` (resumen; detalle de queries en [`02-modelos-schema.md`](02-modelos-schema.md) §B.8)

`getEducationStats(profesor_id, grado_id)` → counts por cada tipo (eifinalks vía `whereHas(pevaluacion→pensum.grado_id/profesor_id)`); `totalRecords = array_sum(...)`; `activeProjects = Eiprojectk::whereNotNull('finicial')->whereNull('ffinal')`; `completedEvaluations = Eievaluationk::whereNotNull('ffinal')`; `getStatsAsJson()`.

## 5. Los 7 componentes Livewire de Evaluación (`app/Http/Livewire/Evaluacion/Inicial/`)

### 5.1 Patrón común

- **Props + queries propias:** el controlador `index()` pasa los filtros a la vista; la vista los inyecta al componente (`:profesor_id` `:grado_id`); `mount()` los asigna; `render()` ejecuta **su propia query** filtrada por esas props. Las colecciones cacheadas del controlador **NO alimentan** a los componentes → **redundancia de queries** (el controlador ya cargó los datos para stats; cada componente re-consulta).
- **Escritura limitada a dos campos:** `observacion` (planes) y `recomendacion` (evaluaciones) — la "revisión" del Coordinador. Todo lo demás (estrategias, informes) es solo visualización/impresión.

### 5.2 Componente por componente

| Componente | Props | Escritura | Observaciones |
|---|---|---|---|
| **EiplanningwkComponent** | `$eiplanningwks`, `$selectedEiplanningwkId`, `$showForm`, `$profesor_id`, `$grado_id`, `$seccion_id`, `$observacion`, `$showStrategiesModal`, `$selectedEiplanningwk` | `saveObservacion()` persiste `observacion` (regla `required\|string\|min:5`) + flash + `showSwal()` | `showForm($id)` carga el plan y su observación; `viewStrategies($id)` = `Eiplanningwk::with([grado,seccion,profesor])->find($id)` + abre modal; `render()` filtra por grado/profesor; `showSwal()` = `dispatchBrowserEvent('swal', [...,'timer'=>6000])` |
| **EiplanningbwkComponent** | idem sobre `Eiplanningbwk` | `saveObservacion()` escribe `observacion` | `viewStrategies` guarda en `$selectedEiplanningwk` — **nombre prestado del semanal** |
| **EiprojectkComponent** | idem | `saveObservacion()` → `observacion` | Query propia en `render()` filtrada por profesor/grado |
| **EispecialkComponent** | idem | `saveObservacion()` → `observacion` | Idem |
| **EievaluationkComponent** | `profesor_id`/`grado_id`/**`lapso_id`**/`recomendacion` | **`saveRecomendacion()` escribe `recomendacion`** (regla `required\|string\|min:5`) | El filtro diferencial es el **lapso**, no la sección; `mount($profesor_id=null, $grado_id=null, $lapso_id=null)`; `render()` filtra por los 3 |
| **EifinalkComponent** (el que usa el index) | `profesor_id`/`seccion_id`/`lapso_id`/`lapsos` | — (solo lectura) | `mount()` carga `$lapsos = Lapso::all()`. `render()`: `Eifinalk::query()->with([pevaluacion.profesor, pevaluacion.lapso, pevaluacion.seccion.grado, estudiant])`, scope `byProfesor`, `whereHas('pevaluacion')` para lapso/sección, `orderBy('estudiant_id')->orderBy('pevaluacion_id')->groupBy('estudiant_id')->get()` — ⚠️ **`groupBy` sin función de agregado** (frágil según `only_full_group_by`). `downloadFormat($id)` = `dispatchBrowserEvent('show-format')` → la vista abre `window.open('/evaluacions/eifinalks/format/${id}','_blank')` |
| **EifinalksComponent** (variante rota, aparentemente sin uso) | — | ⚠️ **`saveRecommendations()` asigna `recommendations` pero NUNCA llama `save()`** (éxito falso) | ⚠️ **`render()` termina con `dd()`** — mata cualquier página que lo invoque. Usa scopes `byProfesor`/`byLapsoYSeccion`. Retorna la **misma vista que EifinalkComponent** (no existe vista `eifinalks` propia) |

### 5.3 Vistas Livewire — `resources/views/livewire/evaluacion/inicial/`

| Vista | Contenido |
|---|---|
| `eiplanningwk-component.blade.php` (187) | Tabla (dos filas por registro). "Observación:" inline. Acciones: **Ver Estrategias** (`viewStrategies`), **PDF** (`route('evaluacions.eiplanningwks.format.index')`, `target=_BLANK`), **Gestionar Obs.** Modal estrategias (modal-xl): matriz Momento × días con `getStrategyByMomentAndDay($momento_key,$day_key)` y helper `as_replace()` renderizado con `{!! !!}` (**XSS potencial**). Modal observación: header "Gestionar Observación **[Coord. de Evaluación]**", textarea `wire:model.defer="observacion"` |
| `eiplanningbwk-component.blade.php` (178) | Ídem quincenal; **PDF a `route('evaluacions.eiplanningwbks.format.index')` (typo `wb`≠`bw` — coincide con el nombre de ruta typo del tab)** |
| `eiprojectk-component.blade.php` (117) | Tabla simple + modal observación |
| `eispecialk-component.blade.php` (101) | (Justificación, Observación, T.Actividades) + modal observación |
| `eievaluationk-component.blade.php` (117) | Fila extra "Recomendaciones: {{$item->recomendacion}}" o "Sin recomendación" + modal recomendación (`saveRecomendacion`) |
| `eifinalk-component.blade.php` (61) | Tabla Estudiante/Profesor/Acciones; por cada lapso un botón `route('evaluacions.eifinalks.print-all-for-lapso', [estudiant, lapso])`, clase `btn`/`btn-outline` según `$estudiant->hasEifinalkForLapso($lapso->id)`, color `btn-{{$lapso->class}}`, label `{{$lapso->id}}`, "Ver informe" |
| `index-component.blade.php` (133) | **HUÉRFANA** — variante de tabla semanal; no existe clase `IndexComponent` |
| `observation-component.blade.php` | Stub: `<div>Be like water.</div>` |
| `partials/info.blade.php` (116) | Modal de límites máximos (§4) |

## 6. Controladores de las tres perspectivas

### 6.1 `Evaluacion/Tab/InicialController.php` (630 líneas)

- Constructor: middleware `['auth','is_evaluacion']`, DI `EducationStatsService`, carga user/autoridad/COLUMN_COMMENTS.
- `index()`: lee `profesor_id`/`grado_id`/`seccion_id`; **cache 5 min** `Cache::remember("inicial_data_{p}_{g}_{s}", 300, ...)` → `getFilteredData()` arma 5 colecciones con eager loading (`with`) + `orderBy('created_at','desc')` + `applyFilters` (grado/profesor/sección); stats vía `EducationStatsService`; `Grado::list_pestudio_grado(6)` y `Profesor::list_profesors_pestudio(6)` — **pestudio_id 6 hardcodeado**; retorna `view('evaluacions.inicilas.index')` + `stats_json` + `last_updated`; try/catch con `Log::error`.
- **Código muerto (grep negativo, sin rutas):** `getStats()`, `clearCache()`, `exportStats()`, `getDashboardSummary()`.
- `format_*`: vistas `livewire.inicial.formats.{eiplanningwk|eiplanningbwk|eiprojectks|eispecialk|eievaluationk}.index` (nombres inconsistentes: eiprojectks con s, eievaluationk sin s) + profesor + institucion + `fecha` (`Carbon::now()->format('d-m-Y h:i A')`).
- `printAllforLapso(Estudiant, Lapso)`: carga `estudiant->eifinalks` con `pevaluacion.pensum.grado/seccion/lapso/profesor` + `expectations.area`, `whereHas` por lapso_id; arma **3 colecciones**: `$eifinalks` (todos), `$eifinalks_oficial` (`status_official=true`), `$eifinalks_component` (`status_official=false`) → `livewire.inicial.formats.eifinalks.index-all`.
- `useCases()` → `view('inicials.use-cases')`.

### 6.2 `Planning/Tab/InicialController.php` (114 líneas)

Middleware `auth+is_planning`; `index()` con 5 colecciones inline — ⚠️ **`seccion_id` se lee del request pero NO se aplica al filtro**; `format_*` sin `with()` ni try/catch; ⚠️ **fecha con bug `format('d-m-Y h:m A')`** (`h:m` = hora:minuto-del-año, debería ser `h:i`).

### 6.3 `Academico/Tab/InicialController.php` (69 líneas)

Middleware `auth+is_academico`; `index()` solo carga eiplanningwks + eiprojectks → `academicos.inicilas.index`; solo `format_eiplanningwk` + `format_eiprojectks`.

## 7. Sistema de acceso y roles

### 7.1 Middleware `app/Http/Middleware/Admin/IsInicial.php` (transcripción)

```php
public function handle($request, Closure $next, Guard $auth)
{
    $user = User::find(Auth::id());
    if(!$user->IsInicial()){
        Session::flash('operp_ok', trans('db_oper_result.you_not_are_admin'));
        return redirect('/home');
    }
    return $next($request);
}
```

`IsEvaluacion` e `IsPlanning` son idénticos llamando a sus métodos; `IsAcademico` usa `$this->auth->user()->IsAcademico()` con firma sin `Guard`. Todos redirigen a `/home` con flash `operp_ok` si el check falla.

### 7.2 `App\User::IsInicial()` — líneas 506–521 (transcripción)

```php
public function IsInicial()
{
    if ($this->is_active == 'disable') return false;
    $fecha = Carbon::now();
    $data = $this->rols
        ->whereIn('area', ['SISTEMA', 'PROFESORADO'])
        ->whereIn('rol', ['ADMINISTRADOR', 'INICIAL'])
        ->Where('finicial', '<=', $fecha)
        ->Where('ffinal', '>=', $fecha)
        ->count();
    if ($data > 0) { return true; } else { return false; }
}
```

El rol **NO** es una columna de `users`: se resuelve por la relación `rols()` (hasMany `App\Models\sys\Rol`). Un usuario es "inicial" si tiene **al menos una fila** en `rols` con `area IN (...)` AND `rol IN (...)` AND vigencia temporal (`finicial <= hoy <= ffinal`).

### 7.3 Valores que identifican cada rol (métodos Is{X} de `User.php`)

| Método | `area` | `rol` |
|---|---|---|
| `IsInicial()` (l.506) | SISTEMA, PROFESORADO | ADMINISTRADOR, INICIAL |
| `IsEvaluacion()` (l.455) | SISTEMA, EVALUACION | ADMINISTRADOR, COORDINADOR |
| `IsPlanning()` (l.523) | SISTEMA, ACADEMICO | ADMINISTRADOR, COORDINADOR |
| `IsAcademico()` (l.371) | SISTEMA, ACADEMICO | ADMINISTRADOR, **DIRECTOR** |

Otros métodos leídos: `IsBienestar`, `IsProyecto`, `IsLeader`, `IsAudit` (SISTEMA/AUTORIDAD + ADMIN/SUPERVISOR), `getRolAttribute`/`getRolNameAttribute`, relación `profesor()` (hasOne). **El docente de inicial es además un `Profesor`** (relación `user_id`).

> ⚠️ **Superposición de permisos:** cualquier fila `SISTEMA/ADMINISTRADOR` vigente satisface los cuatro checks Is{X} — los `whereIn` de área/rol son demasiado amplios; un administrador accede a todas las perspectivas. A tener en cuenta en la migración de roles.

### 7.4 Modelo `app/Models/sys/Rol.php`

- `fillable`: `user_id, area, rol, cargo_id, group, assit_schedule_id, descripcion, finicial, ffinal, status_census_taker, status_schedule`.
- `list_area()` incluye PROFESORADO, EVALUACION, ACADEMICO…; `list_rol()` incluye **`'INICIAL' => 'INICIAL'`**.
- SQL comentado al final con los ENUMs de la tabla `rols`; el ENUM comentado de `rol` **NO incluye INICIAL** (fue añadido a posteriori).
- **No existen seeds ni migraciones que referencien el rol 'inicial'** (greps negativos): los roles se gestionan por **datos en BD, no por código**.

### 7.5 Kernel — aliases de middleware

`'is_academico'` (l.75), `'is_evaluacion'` (l.77), `'is_inicial'` (l.80), `'is_planning'` (l.82).

### 7.6 Montaje de los grupos de rutas en `routes/web.php` (patrón común)

```php
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Inicial'], function () {
    Route::group(['prefix' => 'inicials', 'middleware' => ['is_inicial']], function () {
        require (__DIR__ . '/app/inicials.php');   // l.59-64
    });
});
```

Mismo patrón para `evaluacions` (l.253-258, con `HomeController@home` adicional), `plannings` (l.45-50) y `academicos` (l.237-242). `routes/app/inicials.php` requiere a su vez un archivo por formato en `routes/app/tab/inicials/{home,eiplanningwks,eiplanningbwks,eiprojectks,eispecialks,eievaluationks,eifinalks}.php`.

> ⚠️ **Typo de ruta `eiplanningwbks` (wb) vs modelo `eiplanningbwk` (bw):** consistente entre ruta, vista del componente y tab de rutas de Evaluación y Planning — funciona, pero es una **trampa de rename en migración**.

## 8. Navegación del docente (perspectiva Inicial)

### 8.1 Layout `inicials/layouts/dashboard/app.blade.php`

Extiende `inicials.layouts.app`; navbar dark sticky (color por `Session::get('pescolar_color')`: #004000 SAEFL / #FF0000 SAEFL.DEV) que incluye `inicials.layouts.dashboard.navbar.app`; body en 2 columnas: `col-md-2` con `@includeif('inicials.card.profesor')` + `col-md-10` con `@yield('main')`; scripts `accordion.js` + reloj() cada 60 s.

### 8.2 Navbar del docente (`inicials/layouts/dashboard/navbar/app.blade.php`)

Brand → `route('home')`; primer link → `route('profesors.home')`; botones activables por `Request::is()`:

| Etiqueta | Ruta (nombre) |
|---|---|
| Inicio | `inicials.home` |
| Casos de Usos | `inicials.use-cases` (icon crud) |
| Plan Semanal | `inicials.eiplanningwks.index` |
| Plan de Quincenal | `inicials.eiplanningbwks.index` |
| Proyecto de Aula | `inicials.eiprojectks.index` |
| Plan Especial | `inicials.eispecialks.index` |
| Plan de Evaluación | `inicials.eievaluationks.index` |
| Informe Final | `inicials.eifinalks.index` |

Más `usermenu` (dropdown con card user + logout POST `route('logout')`), "PE: `Session::get('pescolar_name')`" y `#reloj`. ⚠️ **HTML malformado: `</li>` duplicados.**

### 8.3 Tarjeta de perfil (NO es un menú)

`inicials/card/profesor.blade.php` — tarjeta de perfil del docente (avatar, username, email, fullname, CI, `ti_teacher`, nacimiento, dirección, teléfonos) usando `Auth::user()->profesor`. Incluye un SQL `CREATE TABLE` comentado (86 líneas).

### 8.4 Vistas huérfanas de navegación (verificadas con grep, no referenciadas)

- `inicials/layouts/dashboard/sidebar/app.blade.php`: sidebar genérico con `includeWhen` condicionados por URL a parciales de **administracion**.
- `inicials/layouts/dashboard/sidebar/partials/*` (inicio, evaluacions, pevaluacions, profesors, estudiants, representants, configuraciones, planpagos, libros/admon): 0 referencias fuera de su directorio; internamente incluyen los de administracion.
- `inicials/sidebar/access/` (admon, control comentados; common solo "Inicio"→`administracion.home`) · `inicials/navbar/access/` (admin, admon, common, control, profesors, system): parciales condicionales por rol que incluyen menús de administracion/profesors.

### 8.5 Controlador del docente `Inicial/Tab/HomeInicialController.php`

Middleware `['auth','is_inicial', closure]` (closure setea user/autoridad/comments); `home()` con `Lapso::all()` + `Lapso::current()`; `useCases()` con 7 casos hardcodeados (`getUseCasesData()`). `home.blade.php`: jumbotron "Educación Inicial — Formatos para la Planificación y Evaluación." + scripts Chart.bundle. `use-cases.blade.php`: nav-pills + `.mermaid` (ver [`06-funcional-use-cases.md`](06-funcional-use-cases.md)).

> Controladores docente por formato (ej. `Inicial/Tab/EiplanningwkController.php`): middleware `auth+is_inicial`; `index()` = `Profesor::where('user_id', Auth::id())->first()` + `view('inicials.eiplanningwks.index')` — la vista delega el CRUD al componente Livewire del docente (§ [`03-livewire-componentes.md`](03-livewire-componentes.md)); `format($id)` = `findOrFail` + profesor + `Institucion::OrderBy('created_at','DESC')->first()` + `fecha` con **bug `format('d-m-Y h:m A')`** + `view('livewire.inicial.formats.eiplanningwk.index')`.

## 9. Entradas de menú hacia el módulo desde las perspectivas

- **`evaluacions/layouts/dashboard/navbar/app.blade.php` l.58-61:** botón title **"Planificación Educ. Primaria"** (erróneo — el módulo es Inicial) → `route('evaluacions.inicials.index')`, icono `$icon_menus['inicials']`, activo en `*/evaluacions/inicials/index*`. Link a use-cases COMENTADO. Resto del navbar: evaluacions.home, diagnostics, peducativos, profesors, pevaluacions, activities, estudiants, pases, evaluaciones, attendances, competitions.
- **`plannings/layouts/dashboard/navbar/app.blade.php` l.70-74:** mismo patrón → `route('plannings.inicials.index')`, mismo título erróneo; link COMENTADO a `evaluacions.inicials.index`. Navbar: plannings.home, indicators, profesors, pevaluacions, activities, diagnostics, inicials, estudiants, competitions.
- **`academicos/layouts/dashboard/navbar/app.blade.php` l.63-67:** title **"Educ. Inicial"** (correcto) → `route('academicos.inicials.index')`, activo en `*inicials*`. Navbar: academicos.home, manager_registers, control.performance, pollmains, diagnostics, mailers, lessons, activities, inicials, audits.usages + badge TDC exchange_rate.

## 10. Anexo consolidado de bugs/hallazgos de perspectivas y roles

| # | Hallazgo | Impacto migración |
|---|---|---|
| 1 | `dd()` en `EifinalksComponent::render()` | Descartar componente roto |
| 2 | `saveRecommendations()` sin `save()` (éxito falso) | No portar |
| 3 | Typo de ruta `eiplanningwbks` (wb) — y en tablas Blade de Planning/Evaluación el botón quincenal enlaza al formato **semanal** | Normalizar nombre en rutas y corregir enlaces |
| 4 | Títulos erróneos "Planificación Educ. Primaria" en navbars de Evaluación y Planning | Corregir texto |
| 5 | Fecha `h:m` en vez de `h:i` (`Planning\Tab` y `Inicial\Tab\EiplanningwkController::format`) | Corregir |
| 6 | HTML malformado (`</li>` duplicados) en navbar del docente | Corregir |
| 7 | Vistas huérfanas: `index-component`, `observation-component` (stub), tablas de `evaluacions/inicilas/table/`, copias de lessons LMS, sidebar/access, navbar/access | No portar |
| 8 | Stubs de una palabra en `academicos/inicilas/table/` + comilla extra en `eiprojectks` + título "semanal" en tabla de proyectos | No portar; rehacer tablas |
| 9 | Typos de contenido: "inicilas" (directorios), "Actividaes", "Plan de Quincenal" | Normalizar |
| 10 | **pestudio 6 hardcodeado** en todos los controladores de perspectiva | Mantener constante, pero extraer a config |
| 11 | Código muerto no enrutado: `getStats`, `clearCache`, `exportStats`, `getDashboardSummary` | No portar |
| 12 | Badges de stats hardcodeados (+12%, +5, 67%) — muestran datos falsos | No portar; calcular deltas reales o quitar |
| 13 | `groupBy` sin agregado en `EifinalkComponent::render()` (frágil según sql_mode) | Reescribir query |
| 14 | Superposición de roles: cualquier `SISTEMA/ADMINISTRADOR` vigente satisface los 4 checks | Rediseñar RBAC en cfla |
| 15 | Redundancia de queries: controlador cachea 5 colecciones y cada componente re-consulta | Pasar colecciones o unificar origen |
| 16 | Filtro de sección inconsistente (aplicado en Evaluación; leído sin aplicar en Planning; ausente del formulario GET en todas) | Unificar filtros |
| 17 | `{!! as_replace() !!}` sin escapar en modal de estrategias | `nl2br(e())` |
| 18 | Nombres de vistas de formatos inconsistentes (`eiprojectks` con s / `eievaluationk` sin s) | Normalizar |
| 19 | Evaluación tiene capacidad de escritura (observaciones/recomendaciones) — no es pura lectura | Decidir en cfla: mantener revisión del coordinador con permisos explícitos |
