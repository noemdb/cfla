# SPEC-ELEVATION-DIRECTOR-001 — Elevación del Blueprint `is_director`
## Sobre: `Plan de Implementación: Rol is_director (Dirección / Supervisión Ejecutiva)`

**Estado:** Addendum obligatorio — el blueprint original no debe implementarse sin aplicar las secciones 1 y 2 de este documento (son bugs, no mejoras opcionales).

---

## 0. Qué se eleva y por qué

El blueprint original está bien estructurado (ADRs, fases, rollback, principio read-only claro), pero al revisar el código propuesto **método por método** aparecen dos problemas reales que un "elevar genéricamente" no habría encontrado, y dos vacíos entre lo que el documento promete y lo que el código entrega. Esta elevación no reescribe el enfoque — lo corrige donde el código tal como está escrito no haría lo que el documento dice que hace.

---

## 1. 🔴 Bug transversal: el eager loading se descarta en los 6 componentes de listado

En **`PensumList`**, **`CargaAcademicaList`**, **`ActivityList`**, **`LessonList`**, **`ResourceList`** y **`ProfesorIndicators`**, el patrón es siempre:

```php
$query = Pensum::with(['pestudio.peducativo', 'asignatura', 'grado']);
$query = $service->queryPensums();   // ← esto PISA la línea anterior
```

La segunda asignación **reemplaza completamente** `$query`, no lo filtra. El resultado es que `->with(...)` nunca se aplica — cada componente termina haciendo N+1 queries reales en producción (por cada fila de la tabla/paginación, una query adicional por cada relación accedida en la vista: `$activity->pevaluacion?->profesor?->lastname`, `$pensum->pestudio->peducativo`, etc.).

Esto es particularmente grave en este rol específico porque el director **ve toda la institución sin filtro** (ADR-002) — es el componente con mayor volumen de filas de los tres roles de seguimiento (`coordinacion`, `leadership`, `director`), así que es el que más va a notar el N+1 en producción.

**Corrección (aplica el mismo patrón a los 6 componentes):**

```php
// ANTES (bug — el with() se descarta)
$query = Pensum::with(['pestudio.peducativo', 'asignatura', 'grado']);
$query = $service->queryPensums();

// DESPUÉS — el eager loading se aplica sobre el query base del service
$query = $service->queryPensums()->with(['pestudio.peducativo', 'asignatura', 'grado']);
```

**Consecuencia para `DirectorScopeService`:** para que esto funcione limpio, cada método `query*()` del servicio debe devolver un `Builder` (no una colección ya resuelta) — lo cual ya es el caso en el código mostrado, así que la corrección es mecánica: mover el `->with([...])` a continuación del `query*()`, no antes. Aplica igual a `CargaAcademicaList`, `ActivityList`, `LessonList`, `ResourceList`, y a `withCount` en `ProfesorIndicators` (mismo patrón: `$query = Profesor::withCount([...]); $query = $service->queryProfesores();` tiene el mismo bug).

**Nuevo ítem obligatorio para la Fase 8 (Testing):**
```php
// tests/Feature/Director/DirectorEagerLoadingTest.php
test('activity list no genera N+1 al listar con relaciones', function () {
    // Sembrar 15+ activities con pevaluacion/profesor/pensum/asignatura
    DB::enableQueryLog();
    Livewire::actingAs($director)->test(ActivityList::class);
    $queryCount = count(DB::getQueryLog());
    // Un query base + un query por relación eager-loaded, NO uno por fila
    $this->assertLessThan(15, $queryCount);
});
```

---

## 2. 🔴 Bug en el test de enforcement de rutas GET-only

En la sección 8.4, el test de rutas usa:

```php
->reject(fn (Route $r) => ! str_starts_with((string) ($r->uri ?? ''), 'app/director'));
```

`Illuminate\Routing\Route` no expone `uri` como propiedad pública accesible así — el URI se obtiene con el método `$route->uri()`. Tal como está escrito, `$r->uri` devuelve `null` (o lanza un error según la versión), el `reject` filtra **todas** las rutas, la colección queda vacía, y el test **pasa trivialmente sin verificar nada** — el peor tipo de bug en un test de seguridad: da falsa confianza.

**Corrección:**

```php
test('todas las rutas de director son de solo lectura (GET)', function () {
    $directorRoutes = collect(RouteFacade::getRoutes())
        ->filter(fn (Route $r) => str_starts_with($r->uri(), 'app/director'));

    $this->assertNotEmpty($directorRoutes, 'El filtro no debe devolver una colección vacía — si esto falla, el prefijo real de las rutas cambió y el test no está verificando nada.');

    foreach ($directorRoutes as $route) {
        $this->assertEqualsCanonicalizing(['GET', 'HEAD'], $route->methods(),
            "Ruta {$route->uri()} expone métodos distintos de GET/HEAD.");
    }
});
```

Nota el `assertNotEmpty` agregado: cualquier test que filtra una colección y luego verifica una propiedad sobre el resultado filtrado debe primero afirmar que el filtro no vació la colección — si no, un cambio futuro en el prefijo de rutas (ej. si `director` deja de estar bajo `app/`) hace que el test vuelva a pasar en falso sin que nadie lo note.

---

## 3. 🟠 Gap: KPIs docentes (IEE/IRE) prometidos en el resumen, no implementados en el código

La tabla de responsabilidades (sección 1) promete explícitamente:

> **Seguimiento docente (KPIs)** — `Profesor` → KPIs (IEE, IRE) — Visualizar métricas de desempeño docente

Pero `ProfesorIndicators.php` (sección 5.7) solo calcula `peva_count` (conteo de cargas académicas) — no hay ningún cálculo de IEE ni IRE en el componente mostrado. Dos caminos, y el blueprint debe decidir explícitamente cuál antes de la Fase 5, no dejarlo implícito:

- **(a)** Si `IEE`/`IRE` ya existen como métodos/accessors en el modelo `Profesor` o en otro servicio (ej. reusados de `LeadershipService`, que según la sección 1 del blueprint original sí calcula IEE/IRE para su propio dashboard), `ProfesorIndicators::render()` debe llamarlos explícitamente y pasarlos a la vista.
- **(b)** Si no existen todavía, la tabla de responsabilidades de la sección 1 debe corregirse para no prometer "IEE, IRE" en esta fase, y agregarse como ítem de roadmap futuro con su propio ADR.

**Recomendación:** dado que el blueprint dice explícitamente que reusa lógica de scope de Planning/Lms (principio de diseño, sección 1), lo más probable es que `LeadershipService` ya tenga el cálculo — en ese caso la corrección es agregar en `ProfesorIndicators::render()`:

```php
$profesores = $query->orderBy('lastname')->paginate(20)
    ->through(fn ($profesor) => tap($profesor, function ($p) {
        // Reusar el cálculo existente de LeadershipService si aplica —
        // NO reimplementar la fórmula de IEE/IRE en Director.
        $p->iee = app(\App\Services\Leadership\LeadershipService::class)->calculateIee($p);
        $p->ire = app(\App\Services\Leadership\LeadershipService::class)->calculateIre($p);
    }));
```

Esto es una decisión que **debe tomarse antes de escribir el código de Fase 5**, no descubrirse en QA — por eso se documenta acá como gap explícito y no como nota al margen.

---

## 4. 🟠 Gap: el toggle de admin para asignar el rol nunca se agrega en el plan forward

El checklist de rollback (sección 9.4) dice:

> La vista `admin/users` ya no muestra el toggle/estado "Dirección".

Esto implica que ese toggle **debe existir** para que el rollback tenga algo que verificar — pero ninguna de las 8 fases del plan agrega ese toggle a la vista de administración de usuarios. Sin él, la única forma de asignar `is_director = true` a un usuario es por tinker/SQL directo, lo cual no es sostenible para un rol operativo.

**Corrección — agregar a la Fase 1 (Base de Datos y Modelo) un ítem 1.3:**

```
Fase 1.3: Toggle "Dirección" en la vista de administración de usuarios
  - Ubicación: el mismo componente/vista donde hoy se asignan is_leadership /
    is_coordinacion / is_planner (buscar el componente Livewire de gestión de
    usuarios existente — el blueprint no lo nombra explícitamente, así que el
    agente debe localizarlo antes de tocar nada, con grep de "is_leadership"
    en resources/views/livewire/admin/**).
  - Mismo patrón visual/de guardado que los toggles de rol existentes —
    no inventar un componente nuevo para esto.
```

Este ítem se agrega explícitamente porque el propio documento lo da por sentado en la sección 9.4 sin haberlo planificado en la sección 6 — es el tipo de inconsistencia que solo aparece al leer el documento de punta a punta contra sí mismo, no al revisarlo por partes.

---

## 5. ADR-006: Cacheo de indicadores globales del dashboard

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `IndicatorDashboard::loadIndicators()` cachea los totales institucionales (`totalPensums`, `totalActivities`, `totalProfesoresActivos`, `totalResources`, `peducativoIndicators`) con `Cache::remember("director.indicators.{$lapsoId}", now()->addMinutes(15), fn () => ...)` | Sin caché (recalcular en cada `mount`/`updatedSelectedLapsoId`) |
| **Razón** | A diferencia de `coordinacion`/`leadership`, donde el resultado depende del usuario (`manager_id`, áreas), el resultado de Director **no depende de qué director lo consulta** — es el mismo dato agregado para todos los directores. Es el caso de uso perfecto para caché compartida: múltiples directores viendo el mismo dashboard no deberían recalcular la misma agregación institucional completa cada uno. |
| **Consecuencia** | Los indicadores pueden tener hasta 15 minutos de desfase respecto a cambios recién guardados por profesores/coordinación. Aceptable para un dashboard de supervisión ejecutiva (no es un panel operativo en tiempo real). Se documenta un botón "Actualizar ahora" opcional que invalida la clave de caché específica, si el negocio lo requiere — no es parte de esta fase, se deja como nota para no sobre-construir. |

---

## 6. Definition of Done por fase (faltaba — mismo patrón que el resto de las specs de este proyecto)

El blueprint original tiene fases y un roadmap con estimación de días, pero no un criterio de cierre verificable por fase (a diferencia de las otras specs de este proyecto, que sí lo tienen). Se agrega:

- [ ] **Fase 1:** migración corre en limpio y con `migrate:rollback` sin errores; toggle de admin (1.3) visible y funcional; `getRoleLabelAttribute` devuelve "Dirección" para un usuario con `is_director = true` y sin otros roles.
- [ ] **Fase 2:** request de un usuario sin `is_director` a `/app/director/` devuelve 403; con `is_director` devuelve 200; admin sin `is_director` explícito también devuelve 200 (bypass heredado).
- [ ] **Fase 3:** `DirectorScopeService::query*()` devuelve conteos iguales al total real de cada tabla (sin filtro) en un entorno de prueba con datos de más de un `Peducativo`.
- [ ] **Fase 4:** `php artisan route:list --name=director` muestra únicamente métodos GET/HEAD.
- [ ] **Fase 5:** los 7 componentes renderizan sin error N+1 (test de la sección 1) y sin eager loading descartado.
- [ ] **Fase 6:** navbar muestra "Dirección" solo para usuarios con el rol; mobile nav en paridad con desktop (ver ADR-004 de ese spec de UI mobile, si aplica a este módulo).
- [ ] **Fase 7:** test de reflexión (sección 8.4 corregida) y test de rutas GET-only (sección 2 de este documento) en verde.
- [ ] **Fase 8:** suite completa `php artisan test --filter=Director` en verde, incluido el nuevo test de N+1.

**Gate humano:** igual que en las otras specs de este proyecto, no se avanza de fase sin que la anterior cumpla su DoD — particularmente crítico aquí porque Fase 5 (componentes) depende de que Fase 3 (servicio) ya tenga el eager loading corregido, o el bug se replica 7 veces antes de detectarse.

---

## 7. Test adicional: los componentes Livewire de Director tampoco exponen escritura

El test de reflexión de la sección 8.4 original solo audita `DirectorScopeService`. Pero el invariante real que le importa al negocio es "el módulo Director no escribe nada" — y eso incluye los propios componentes Livewire, no solo el servicio. Se agrega:

```php
test('ningun componente Livewire de Director expone metodos de escritura', function () {
    $forbidden = ['save', 'update', 'store', 'create', 'delete', 'destroy',
                  'approve', 'reject', 'comment', 'observe'];
    // Excepciones legítimas: 'updatingSearch', 'updatingLapsoId', 'updatedSelectedLapsoId'
    // son hooks de ciclo de vida de Livewire para *leer* el input del filtro, no escritura de datos.
    $allowlist = ['updatingSearch', 'updatingLapsoId', 'updatingPeducativoId', 'updatedSelectedLapsoId'];

    $componentClasses = [
        \App\Livewire\Director\IndicatorDashboard::class,
        \App\Livewire\Director\PensumList::class,
        \App\Livewire\Director\CargaAcademicaList::class,
        \App\Livewire\Director\ActivityList::class,
        \App\Livewire\Director\LessonList::class,
        \App\Livewire\Director\ResourceList::class,
        \App\Livewire\Director\ProfesorIndicators::class,
    ];

    foreach ($componentClasses as $class) {
        $methods = array_map(
            fn ($m) => $m->name,
            (new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC)
        );
        foreach ($methods as $method) {
            if (in_array($method, $allowlist, true)) continue;
            foreach ($forbidden as $f) {
                $this->assertFalse(
                    str_contains(strtolower($method), $f),
                    "$class::$method parece un método de escritura — el módulo Director debe ser 100% read-only."
                );
            }
        }
    }
});
```
