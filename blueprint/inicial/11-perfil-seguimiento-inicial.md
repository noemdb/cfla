# 11 — Perfil `is_inicial_manager` · Seguimiento de Educación Inicial

> **Estado:** implementado · 2026-10-29 · ampliado el 2026-11-02 con la escritura de la revisión (ADR-11.11)
> **Ámbito:** módulo Educación Inicial (pestudio 6), quinta perspectiva sobre los mismos seis documentos.
> **Depende de:** [`01-rutas-controladores.md`](01-rutas-controladores.md), [`02-modelos-schema.md`](02-modelos-schema.md), [`03-livewire-componentes.md`](03-livewire-componentes.md), [`04-vistas-ui.md`](04-vistas-ui.md), [`07-perspectivas-roles.md`](07-perspectivas-roles.md) y [`08-adaptacion-cfla.md`](08-adaptacion-cfla.md).
> **Alcance de escritura:** el perfil **consulta** los seis documentos y **solo escribe la revisión** (`observacion`/`recomendacion`). No crea, edita, elimina, clona ni importa documentos.

---

## 0. Ficha rápida

| Aspecto | Decisión propuesta |
|---|---|
| Flag | `users.is_inicial_manager` (`boolean`, `default false`) |
| Perfil | Seguimiento de Educación Inicial · **consulta + revisión** |
| Ruta base | `/app/inicials/seguimiento` |
| Nombre de rutas | `inicials.seguimiento.*` |
| Middleware | `auth` + `isInicialManager` (alias nuevo) |
| Layout | `seguimiento_inicial` (nuevo, dedicado) |
| Menú | Grupo `seguimiento_inicial` en `config/menus.php` |
| Documentos | Los **6** del catálogo `RegistroInicial` |
| Alcance de datos | **Todos** los docentes `is_inicial`, no solo el autenticado |
| Escritura | **Solo la revisión** (`observacion`, o `recomendacion` en el plan de evaluación) desde el modal «Ver»; sin crear, editar, eliminar, clonar ni importar documentos |
| Componente | `App\Livewire\Inicial\SeguimientoInicialComponent` (listado, filtros, tarjetas/tabla, modal de detalle y revisión) |
| Detalle / impresión | `PerspectivaInicialController::show()` y `format()` reutilizados |
| Servicio nuevo | `App\Services\Inicial\SeguimientoInicial` (consultas, filtros, completitud, KPI) |
| Migración | Una sola columna en `users`; sin tablas nuevas |
| Fase 1 | Los 6 documentos visibles; planificación semanal con indicadores ricos |
| Fase 2 | Enriquecer completitud por documento e índices, solo tras `EXPLAIN` y aprobación |

---

## 1. Problema y objetivo

El módulo de Inicial ya tiene cuatro perspectivas sobre los **mismos** seis documentos:

| Perspectiva | Flag | Escritura |
|---|---|---|
| Docente | `is_inicial` | CRUD completo |
| Evaluación | `is_diagnostic` | Escribe `observacion` / `recomendacion` |
| Planificación | `is_planner` | Solo lectura |
| Académico / Dirección | `is_admin` | Solo lectura limitada (2 documentos) |

Falta un perfil operativo que **no sea docente ni coordinador**: una persona que deba **revisar el avance de todos los docentes de Inicial**, detectar planes incompletos o sin revisión, abrir el detalle, descargar el formato y **registrar la revisión de la Coordinación**, pero que **no pueda crear, editar ni eliminar ningún documento**.

El objetivo es entregar una quinta perspectiva, `is_inicial_manager`, con una interfaz deliberadamente parecida a `/app/inicials/eiplanningwks`: filtros, tarjetas/tabla, paginación, indicadores de completitud, detalle y formato imprimible. La diferencia es que **no hay CRUD**: la única escritura es `observacion`/`recomendacion`, con la misma regla `min:5` que la Coordinación de Evaluación.

### 1.1 Criterios de éxito medibles

1. El manager ve registros de **dos docentes distintos** en la misma pantalla; no hay filtro implícito por `auth()->id()`.
2. Puede filtrar por docente, nivel (`grado_id`), sección, lapso (cuando aplica), rango de fechas, estado y búsqueda; los filtros viajan en la URL.
3. Puede abrir el detalle y el formato de los **seis** documentos.
4. No existe ninguna ruta `POST/PUT/PATCH/DELETE` bajo `inicials.seguimiento.*`; la revisión viaja por el endpoint de Livewire.
5. Desde el modal «Ver» puede registrar la revisión (`observacion`/`recomendacion`, `min:5`) y el badge y el KPI se actualizan en el mismo render.
6. Un docente `is_inicial` sin el flag recibe **403**; un `is_admin` sin el flag recibe **403**.
7. La suite `tests/Feature/Inicial` sigue verde y se añaden pruebas específicas del perfil.
8. La página índice no dispara N+1 ni carga las estrategias/resúmenes completos de cada fila.

---

## 2. Alcance

### 2.1 Dentro del alcance

- Columna `users.is_inicial_manager` y su administración desde `/admin/users`.
- Middleware, alias en `Kernel`, rutas, controlador, servicio, componente Livewire, vistas y parciales.
- Menú/layout propio y entrada en el navbar.
- Filtros, paginación, tarjetas/tabla, KPI, detalle y formato.
- Escritura de la revisión (`observacion`/`recomendacion`, `required|string|min:5`) desde el modal «Ver», con defensa de rol en el componente.
- Reutilización de `RegistroInicial`, `PerspectivaFiltros`, `PerspectivaInicialController` y las vistas de formato existentes.
- Pruebas de acceso, datos, filtros, detalle, formato, menú, administración de usuarios, revisión y no-escritura del resto de campos.
- Documentación de blueprint y actualización del `README`.

### 2.2 Fuera del alcance

- Escribir cualquier campo distinto de la revisión (`diagnostico`, rejilla de estrategias, resúmenes, etc.).
- Crear, editar, eliminar, clonar, importar o restaurar documentos.
- Exportaciones CSV/Excel, gráficos históricos, notificaciones o reportes automáticos.
- Cambiar el CRUD del docente, sus políticas o sus componentes.
- Cambiar las vistas de formato oficiales (el manager imprime **el mismo papel**, sin marcas de agua).
- Nuevas tablas, cambios de columnas existentes o migración de datos.
- Índices adicionales en fase 1; se evalúan en fase 2 con `EXPLAIN` y aprobación.

---

## 3. Decisiones de arquitectura (ADR)

### ADR-11.1 · Nombre del flag: `is_inicial_manager`

El requerimiento original decía `user.is_incial_manager`; **`incial` es un typo** de `inicial`.

**Decidido (2026-10-29):** el flag se llama `is_inicial_manager`. Se corrige ahora porque la columna es nueva, sin datos históricos ni integraciones externas; corregirla después obliga a renombrar columna, modelo, middleware, rutas, permisos, UI y pruebas.

| Elemento | Nombre decidido | Descartado (typo) |
|---|---|---|
| Columna | `is_inicial_manager` | `is_incial_manager` |
| Migración | `add_is_inicial_manager_to_users_table` | `add_is_incial_manager_to_users_table` |
| Método `User` | `isInicialManager()` | `isIncialManager()` |
| Middleware | `IsInicialManager` | `IsIncialManager` |
| Alias | `isInicialManager` | `isIncialManager` |
| Permiso de menú | `is_inicial_manager` | `is_incial_manager` |
| Layout | `seguimiento_inicial` | `seguimiento_incial` |

**Regla:** no crear las dos columnas ni aceptar ambos nombres. Un flag duplicado duplica la superficie de autorización y hace ambiguo el layout. Si alguna vez se optara por el literal, el renombrado debería ser global y consistente; no está previsto.

### ADR-11.2 · Perspectiva independiente, no una variante del docente

El manager **no hereda** `isInicial`, no usa `EiplanningwkComponent` y no comparte el `mount()` que resuelve `Auth::user()->profesor`. Un manager puede no tener ficha de `profesors`.

**Decidido (2026-10-29):** ambos flags son **excluyentes**. Un mismo usuario no puede ser docente con CRUD y manager de consulta y revisión; la UI de admin lo rechaza (§4.3). Es la forma más simple de garantizar que el perfil nuevo no herede el CRUD docente: no hay ningún usuario que acumule ambos poderes.

- Middleware propio: `IsInicialManager`.
- Controlador propio: `SeguimientoInicialController`.
- Componente propio: `SeguimientoInicialComponent`.
- Servicio propio: `SeguimientoInicial`.

La única reutilización es de **lectura**: catálogo, filtros, detalle y formatos.

### ADR-11.3 · Ruta `/app/inicials/seguimiento`

Se elige:

```text
/app/inicials/seguimiento
/app/inicials/seguimiento/eiplanningwks
/app/inicials/seguimiento/eiplanningwks/{id}
/app/inicials/seguimiento/eiplanningwks/{id}/format
```

Motivos:

- Queda dentro del módulo (`/app/inicials`), junto a las otras perspectivas.
- `seguimiento` describe la función, no el cargo (“manager” es inglés y el legacy ya usa `manager_registers` para otra cosa).
- Permite rutas por documento, con nombre y estado activo de menú propios.
- No colisiona con `/app/inicials/eiplanningwks` (docente) ni con `/app/plannings/inicials` (Planificación).

**Descartado:** `/app/seguimientos/inicials` (se confunde con el módulo de Seguimiento de Jefes de Área) y `/app/inicials/manager` (inglés y poco descriptivo).

### ADR-11.4 · Solo el flag; sin herencia de `is_admin`

**Decidido (2026-10-29):** el middleware exige `is_inicial_manager` y **no** deja pasar a `is_admin` por el hecho de serlo.

- Es coherente con `IsDiagnostic`, que tampoco hereda admin.
- Evita que un administrador vea datos pedagógicos sin una asignación explícita.
- El admin puede asignarse el flag temporalmente si necesita soporte; queda auditado en `users`.

Si operaciones pide “break-glass”, se añade después una excepción explícita y configurable, no implícita.

### ADR-11.5 · Los seis documentos desde el primer día

El manager debe ver **todos los registros de los profesores**, no solo la planificación semanal. `RegistroInicial` ya define los seis documentos, sus relaciones, columnas y formatos, así que mostrarlos todos no multiplica el código.

- **Fase 1:** los seis documentos con tarjeta/tabla genérica; la planificación semanal tiene indicadores ricos (estrategias/días/resúmenes) porque es el documento P0 y el que el usuario puso como referencia.
- **Fase 2:** reglas de completitud específicas para quincenal, proyecto, plan especial, evaluación e informe final.

### ADR-11.6 · Livewire para el índice; Blade servidor para detalle y formato

El índice necesita estado (filtros, paginación, tarjetas/tabla, modal) y debe parecerse a `/app/inicials/eiplanningwks`, que es Livewire. Por eso:

- `SeguimientoInicialComponent` (Livewire 3) para el listado.
- `PerspectivaInicialController::show()` y `format()` para detalle e impresión, renderizados en servidor.
- Las pestañas son enlaces GET por documento; así cada pestaña tiene URL propia, menú activo y no se cargan los seis listados a la vez.

**Descartado:** un componente Livewire único con las seis tablas embebidas (cargaría seis consultas para mostrar una) y un índice 100 % servidor (perdería el toggle tarjetas/tabla y el modal sin recargar).

### ADR-11.7 · Reutilizar catálogo y filtros; no crear modelos

No se crea ningún modelo Eloquent nuevo: los seis documentos ya existen.

| Necesidad | Reutilizar | Cambio |
|---|---|---|
| Catálogo de documentos, columnas, detalle, formato | `App\Services\Inicial\RegistroInicial` | Añadir `filaTabla()` |
| Docentes, niveles, secciones, lapsos | `App\Services\Inicial\PerspectivaFiltros` | Sin cambios; el manager amplía la lista de docentes en su servicio |
| Detalle e impresión | `App\Http\Controllers\Inicial\PerspectivaInicialController` | Hooks de migas/nota de revisión |
| Conteos globales | `App\Services\Inicial\EducationStatsService` | Solo como referencia; el manager usa su propio servicio para filtros finos |
| Tabla de solo lectura | `resources/views/inicial/shared/document-table.blade.php` | No se toca; el manager tiene tabla propia con estado/paginación |

### ADR-11.8 · Layout y menú dedicados

El manager es un perfil operativo dedicado. Su layout primario es `seguimiento_inicial`, con un grupo de menú propio. No se mezcla con `inicial` ni con `profesor`.

Orden de resolución propuesto en `MenuBuilder::resolveLayoutForUser()`:

```text
admin → inicial_manager → director → leadership → coordinacion → planner → profesor → inicial → student
```

Así un manager que también sea `is_profesor` sigue viendo su panel de seguimiento. Un admin que además tenga el flag sigue viendo el panel admin (el flag no le quita su rol principal).

### ADR-11.9 · “Nivel”, no “Grado”, en la UI

La base de datos conserva `grado_id` y las relaciones `grado`; **la interfaz del manager dice “Nivel”**. Aplica a filtros, tarjetas, tablas, detalle y textos de estado.

No se renombra la columna ni la relación: sería un cambio de esquema y de todo el módulo sin beneficio.

### ADR-11.10 · Cambio de base de datos mínimo

Fase 1: **una sola migración**, `users.is_inicial_manager`.

- No se añaden índices en fase 1: el volumen actual de Inicial es pequeño y la planificación semanal ya tiene índices (`eiplanningwks_profesor_finicial_index`, `eiplanningwks_grado_seccion_finicial_index`).
- Fase 2: índices para los otros documentos **solo** después de medir con `EXPLAIN` y obtener aprobación. Se documentan en §12.3, no se incluyen en la migración inicial.

### ADR-11.11 · La revisión se escribe desde el Seguimiento (2026-11-02)

El perfil nació como solo lectura, pero el uso real exige que la persona que **detecta** un plan incompleto o sin revisión pueda **dejar constancia** de la revisión sin abrir otra perspectiva ni pedir otra cuenta.

**Decidido:** la única escritura del perfil es el campo de revisión del documento, y ocurre **solo dentro del modal del listado**. El modal se abre con «Ver» o directamente con el botón «Revisión» que está junto a «Imprimir» en cada tarjeta o fila de la tabla:

- Campo: `observacion` en planificación semanal, quincenal, proyecto y plan especial; `recomendacion` en el plan de evaluación. El informe final no tiene campo revisable y `abrirRevision()` responde **404**.
- Regla: `required|string|min:5`, con los mismos mensajes que la Coordinación de Evaluación.
- Defensa: `abort_unless(auth()->user()?->isInicialManager(), 403)` al inicio de `guardarRevision()`, porque una actualización de Livewire no vuelve a ejecutar `mount()`.
- El botón «Revisión» llama a `revisarDetalle($id)`: abre el mismo modal y entra en modo escritura. No crea una ruta ni un formulario paralelo.
- La ficha completa (`show`) y el formato siguen siendo de solo lectura; el formulario **no** se renderiza fuera del modal del listado.
- No se añade ruta de escritura: la llamada viaja por el endpoint de Livewire y las rutas del grupo siguen siendo solo `GET`.

**Descartado:** limitarse a mostrar la revisión existente (el perfil quedaría incompleto para su propósito) y derivar a `/app/evaluacions/inicials` (duplica el flujo y exige otro flag, `is_diagnostic`).

**Consecuencia:** el perfil deja de ser «solo lectura» en su totalidad. El blueprint, los textos de la UI y las pruebas distinguen explícitamente **documento de solo lectura** de **revisión escribible**.

---

## 4. Modelo de datos y migración

### 4.1 Migración

Archivo nuevo:

```text
database/migrations/2026_10_29_000001_add_is_inicial_manager_to_users_table.php
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perfil de seguimiento de Educación Inicial (consulta y revisión).
 *
 * Blueprint: blueprint/inicial · doc 11 · ADR-11.1.
 *
 * Una sola columna booleana. No crea tablas ni modifica datos existentes:
 * todos los usuarios quedan con `false` y conservan su comportamiento actual.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_inicial_manager')
                ->default(false)
                ->after('is_inicial');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_inicial_manager');
        });
    }
};
```

Notas:

- `after('is_inicial')` es una comodidad de MySQL; Laravel lo ignora en SQLite.
- No se añade índice: la tabla `users` es pequeña y el filtro por rol no es crítico.
- **Rollback:** elimina la columna y, con ella, la asignación del flag. Antes de revertir en producción, exportar `SELECT id, username, is_inicial_manager FROM users WHERE is_inicial_manager = 1;`.
- **Aprobación:** cualquier `migrate` en producción requiere aprobación explícita. No se toca `.env`.

### 4.2 Cambios en `App\Models\User`

| Ubicación | Cambio |
|---|---|
| Docblock de la clase | `@method bool isInicialManager()` |
| `auditableAttributes()` | Añadir `is_inicial_manager` |
| `$fillable` | Añadir `is_inicial_manager` |
| `$casts` | Añadir `'is_inicial_manager' => 'boolean'` |
| Método nuevo | `isInicialManager(): bool` |
| `getRoleLabelAttribute()` | Etiqueta `Seguimiento Inicial` |
| `isInicial()` | Sin cambios (sigue leyendo el atributo crudo) |

```php
/**
 * Acceso al seguimiento de Educación Inicial (consulta y revisión).
 *
 * Lee el atributo crudo, igual que isInicial(), para no heredar el fallback
 * de otros flags. Devuelve false mientras la migración no se haya corrido.
 */
public function isInicialManager(): bool
{
    return (bool) ($this->attributes['is_inicial_manager'] ?? false);
}
```

En `getRoleLabelAttribute()`, insertar **después** de `is_admin` y antes de `is_planner`:

```php
if (! empty($a['is_inicial_manager'])) {
    return 'Seguimiento Inicial';
}
```

Motivo: el manager es un perfil dedicado; si además tiene otro flag, la etiqueta debe decir qué está usando. El orden propuesto no altera a ningún usuario existente porque la columna nace en `false`.

### 4.3 Administración de usuarios (`/admin/users`)

Archivos:

- `app/Livewire/Admin/Users/IndexComponent.php`
- `resources/views/livewire/admin/users/index-component.blade.php`

Cambios en el componente:

1. Propiedad `public $is_inicial_manager = false;`.
2. `rules()`: `'is_inicial_manager' => 'boolean'`.
3. `$validationAttributes`: `'is_inicial_manager' => 'seguimiento inicial'`.
4. `render()`:
   - Filtro `filter_role === 'inicial_manager'` → `where('is_inicial_manager', true)`.
   - Filtro `standard` → añadir `where('is_inicial_manager', false)`.
   - `$roleOptions['inicial_manager'] = 'Seguimiento Inicial'`.
5. `edit()`: `$this->is_inicial_manager = (bool) $user->isInicialManager();`.
6. `save()`: escribir el flag **solo si la columna existe**, con el mismo patrón defensivo que `is_inicial`:

```php
if (Schema::hasColumn('users', 'is_inicial_manager')) {
    $userData['is_inicial_manager'] = $this->is_inicial_manager ? 1 : 0;
}
```

7. `resetForm()`: incluir `is_inicial_manager` en `$this->reset([...])`.
8. Validación de separación de funciones: si `is_inicial` y `is_inicial_manager` están ambos activos, rechazar el guardado con un error en `is_inicial_manager`:

```php
if ($this->is_inicial && $this->is_inicial_manager) {
    $this->addError(
        'is_inicial_manager',
        'Un usuario no puede ser docente de Inicial y gestor de seguimiento a la vez.'
    );

    return;
}
```

Se valida en `save()` después de `$this->validate()` y antes de la transacción. Es una regla de producto: el perfil consulta y revisa, pero no debe heredar el CRUD docente. Si más adelante se necesita un usuario con ambos roles, se revisa la decisión y se documenta.

> Si un usuario heredado ya tiene ambos flags, el formulario los mostrará marcados
> y el guardado fallará hasta desmarcar uno: es el comportamiento deseado
> (fuerza la corrección), pero quien edite debe ver el mensaje, no un error
> genérico. Por eso la regla va en `$this->addError('is_inicial_manager', …)` y
> no en `abort()`.

Cambios en la vista:

- Checkbox `is_inicial_manager` junto a `is_inicial`, con etiqueta **“Seguimiento Inicial (consulta y revisión)”** y texto de ayuda “No crea ni edita documentos; solo registra la revisión”.
- Badge en la columna Roles: `Seguim.` (color cian).
- La condición de “Usuario Estándar” debe incluir `! $user->isInicialManager()`.
- El selector de filtro por rol usa `$roleOptions`; no hace falta tocar el Blade si ya itera.

### 4.4 Checklist de despliegue de la migración

```bash
php artisan migrate --pretend
# revisar que solo aparece el ALTER TABLE users ADD is_inicial_manager
php artisan migrate --force
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

- Backup previo de `users` (o al menos de `id, username, is_inicial, is_admin`).
- Ventana de mantenimiento si la política del colegio lo exige.
- No modificar `.env` ni assets compilados.
- Verificación posterior:

```sql
SHOW COLUMNS FROM users LIKE 'is_inicial_manager';
SELECT COUNT(*) FROM users WHERE is_inicial_manager = 1;
```

---

## 5. RBAC y middleware

### 5.1 Middleware `App\Http\Middleware\IsInicialManager`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Acceso al seguimiento de Educación Inicial (consulta y revisión).
 *
 * Blueprint: blueprint/inicial · doc 11 · ADR-11.4.
 *
 * A diferencia de IsInicial, NO deja pasar a is_admin: el acceso a los datos
 * pedagógicos de todos los docentes exige una asignación explícita. Si soporte
 * necesita entrar, se marca el flag temporalmente y queda auditado.
 */
class IsInicialManager
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->isInicialManager()) {
            abort(403, 'No tienes permiso para acceder al seguimiento de Educación Inicial.');
        }

        return $next($request);
    }

    /**
     * Diagnóstico de despliegue: la columna aún no existe.
     *
     * Mismo patrón que IsInicial::migracionPendiente(): permite que las
     * pruebas y los comandos de diagnóstico den un mensaje accionable en vez
     * de un 403 silencioso.
     */
    public static function migracionPendiente(): bool
    {
        return ! Schema::hasColumn('users', 'is_inicial_manager');
    }
}
```

### 5.2 Alias en `app/Http/Kernel.php`

Añadir en `$middlewareAliases` (no en `$routeMiddleware`, que solo conserva `isAdmin`):

```php
'isInicialManager' => \App\Http\Middleware\IsInicialManager::class,
```

### 5.3 Matriz de acceso

| Usuario | `/app/inicials/seguimiento*` | Menú “Seguimiento Inicial” |
|---|---|---|
| Invitado | 302 a login | No |
| `is_inicial` (docente) sin manager | **403** | No |
| `is_inicial_manager` | **200** | Sí |
| `is_admin` sin manager | **403** | No |
| `is_inicial_manager` + `is_profesor` | **200** (layout manager) | Sí |
| `is_inicial_manager` + `is_inicial` | **200** por middleware, pero **bloqueado en admin UI** | Sí |

La combinación `is_inicial` + `is_inicial_manager` no se permite desde la UI. Si existiera data heredada, el middleware deja pasar porque el flag manager está activo; el layout manager gana. Se recomienda auditar:

```sql
SELECT id, username
FROM users
WHERE is_inicial = 1 AND is_inicial_manager = 1;
```

No se corrige automáticamente: es una decisión de datos y requiere aprobación.

### 5.4 Defensa en profundidad

- Rutas: solo `GET`/`HEAD` (§6).
- Middleware: `isInicialManager`.
- Componente: `abort_unless(auth()->user()?->isInicialManager(), 403)` en `mount()` y de nuevo en `guardarRevision()`, por si se invoca fuera de la ruta o con un snapshot manipulado.
- Pruebas: acceso, métodos, revisión y ausencia de escritura del resto de campos (§11).

Opcional, si se quiere centralizar la regla para Blade:

```php
// app/Providers/AuthServiceProvider.php · boot()
Gate::define('inicial.seguimiento', fn (User $user) => $user->isInicialManager());
```

No es obligatorio porque el middleware ya protege el grupo. Si se añade, usarlo solo como refuerzo, nunca como sustituto del middleware.

---

## 6. Rutas

Archivo: `routes/app/inicials.php`. Insertar el grupo **antes** del grupo del docente, para que la ruta de seguimiento quede separada visualmente y no dependa del orden de los recursos.

```php
use App\Http\Controllers\Inicial\SeguimientoInicialController;

/*
|--------------------------------------------------------------------------
| 0. SEGUIMIENTO DE EDUCACIÓN INICIAL  ·  auth + is_inicial_manager
|--------------------------------------------------------------------------
| Quinta perspectiva: consulta los 6 documentos de TODOS los docentes de
| Inicial y solo escribe la revisión (`observacion`/`recomendacion`) desde
| el modal «Ver». No hay POST/PUT/PATCH/DELETE en el grupo: la escritura
| viaja por el endpoint de Livewire.
|
| Rutas por documento para que cada pestaña tenga URL y estado de menú
| propios. El detalle y el formato los hereda de PerspectivaInicialController.
*/
Route::middleware(['auth', 'isInicialManager'])
    ->prefix('app/inicials/seguimiento')
    ->name('inicials.seguimiento.')
    ->group(function () {
        // Panel: redirige a la pestaña semanal.
        Route::get('/', [SeguimientoInicialController::class, 'index'])->name('index');

        foreach ([
            'eiplanningwks',
            'eiplanningbwks',
            'eiprojectks',
            'eispecialks',
            'eievaluationks',
            'eifinalks',
        ] as $entidad) {
            Route::get($entidad, [SeguimientoInicialController::class, 'listado'])
                ->defaults('entidad', $entidad)
                ->name($entidad.'.index');

            Route::get($entidad.'/{id}', [SeguimientoInicialController::class, 'show'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.show');

            Route::get($entidad.'/{id}/format', [SeguimientoInicialController::class, 'format'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.format');
        }
    });
```

### 6.1 Tabla de rutas

| Método | URI | Nombre | Acción |
|---|---|---|---|
| GET | `/app/inicials/seguimiento` | `inicials.seguimiento.index` | `SeguimientoInicialController@index` (redirige a semanal) |
| GET | `/app/inicials/seguimiento/eiplanningwks` | `inicials.seguimiento.eiplanningwks.index` | `@listado` |
| GET | `/app/inicials/seguimiento/eiplanningwks/{id}` | `inicials.seguimiento.eiplanningwks.show` | `@show` (heredado) |
| GET | `/app/inicials/seguimiento/eiplanningwks/{id}/format` | `inicials.seguimiento.eiplanningwks.format` | `@format` (heredado) |
| GET | `/app/inicials/seguimiento/eiplanningbwks` | `…eiplanningbwks.index` | `@listado` |
| GET | `/app/inicials/seguimiento/eiplanningbwks/{id}` | `…eiplanningbwks.show` | `@show` |
| GET | `/app/inicials/seguimiento/eiplanningbwks/{id}/format` | `…eiplanningbwks.format` | `@format` |
| GET | `/app/inicials/seguimiento/eiprojectks` | `…eiprojectks.index` | `@listado` |
| GET | `/app/inicials/seguimiento/eiprojectks/{id}` | `…eiprojectks.show` | `@show` |
| GET | `/app/inicials/seguimiento/eiprojectks/{id}/format` | `…eiprojectks.format` | `@format` |
| GET | `/app/inicials/seguimiento/eispecialks` | `…eispecialks.index` | `@listado` |
| GET | `/app/inicials/seguimiento/eispecialks/{id}` | `…eispecialks.show` | `@show` |
| GET | `/app/inicials/seguimiento/eispecialks/{id}/format` | `…eispecialks.format` | `@format` |
| GET | `/app/inicials/seguimiento/eievaluationks` | `…eievaluationks.index` | `@listado` |
| GET | `/app/inicials/seguimiento/eievaluationks/{id}` | `…eievaluationks.show` | `@show` |
| GET | `/app/inicials/seguimiento/eievaluationks/{id}/format` | `…eievaluationks.format` | `@format` |
| GET | `/app/inicials/seguimiento/eifinalks` | `…eifinalks.index` | `@listado` |
| GET | `/app/inicials/seguimiento/eifinalks/{id}` | `…eifinalks.show` | `@show` |
| GET | `/app/inicials/seguimiento/eifinalks/{id}/format` | `…eifinalks.format` | `@format` |

### 6.2 Verificaciones

```bash
php artisan route:list --name=inicials.seguimiento
php artisan route:list --path=app/inicials/seguimiento --method=POST
# el segundo comando no debe devolver rutas
```

- **Sin conflictos:** el grupo del docente declara `/app/inicials/` y `/app/inicials/eiplanningwks/{id}`, no un wildcard raíz. `/app/inicials/seguimiento` no coincide con ningún recurso existente.
- **Sin closures:** todas las rutas apuntan a métodos de controlador, así que `route:cache` funciona.
- **Sin rutas de escritura:** no se declara `Route::resource`, `Route::post`, `put`, `patch` ni `delete`. La revisión se persiste desde `guardarRevision()` de Livewire, no desde el router.

---

## 7. Backend: controlador y servicios

### 7.1 `App\Http\Controllers\Inicial\SeguimientoInicialController`

```php
<?php

namespace App\Http\Controllers\Inicial;

use App\Services\Inicial\RegistroInicial;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Perspectiva de SEGUIMIENTO de Educación Inicial (consulta y revisión).
 *
 * Blueprint: blueprint/inicial · doc 11.
 *
 * `show()` y `format()` se heredan de PerspectivaInicialController: son los
 * mismos documentos y el mismo papel que ven las otras perspectivas. Lo único
 * que cambia es el prefijo de rutas y el marco de navegación.
 */
class SeguimientoInicialController extends PerspectivaInicialController
{
    protected string $prefijoRutas = 'inicials.seguimiento.';

    /** El panel entra por la planificación semanal. */
    public function index(): RedirectResponse
    {
        return redirect()->route('inicials.seguimiento.eiplanningwks.index');
    }

    /** Pestaña de un documento: valida la entidad y renderiza el listado. */
    public function listado(): View
    {
        return $this->vista($this->entidadDeLaRuta());
    }

    private function vista(string $entidad): View
    {
        // Entidad desconocida y documento inexistente comparten 404: la ruta
        // no debe revelar qué claves existen en el catálogo.
        abort_unless(RegistroInicial::existe($entidad), 404);

        return view('inicial.seguimiento.index', [
            'entidad' => $entidad,
        ]);
    }

    /**
     * Hooks de navegación del detalle compartido.
     *
     * El detalle base (inicial.shared.document-show) usa estos métodos para
     * que un manager no acabe en la portada del docente, que le daría 403.
     */
    protected function rutaInicio(): string
    {
        return route('inicials.seguimiento.index');
    }

    protected function etiquetaInicio(): string
    {
        return 'Seguimiento Inicial';
    }

    protected function notaRevision(): string
    {
        return 'La revisión se registra desde el listado (modal «Ver»). Esta ficha es de solo lectura.';
    }
}
```

#### Cambio pequeño y retrocompatible en `PerspectivaInicialController`

En `show()`, añadir al array de la vista:

```php
'rutaInicio' => $this->rutaInicio(),
'etiquetaInicio' => $this->etiquetaInicio(),
'notaRevision' => $this->notaRevision(),
```

Y los métodos por defecto:

```php
protected function rutaInicio(): string
{
    return route('inicials.home');
}

protected function etiquetaInicio(): string
{
    return 'Educación Inicial';
}

protected function notaRevision(): string
{
    return 'La revisión la escribe la Coordinación de Evaluación desde su propia perspectiva.';
}
```

> No hace falta un método `tituloRevision()`: el texto es genérico y el
> parcial de detalle ya muestra el rótulo del campo de revisión con
> `RegistroInicial::rotuloRevision($entidad)`, que el controlador pasa a la vista.

En `resources/views/inicial/shared/document-show.blade.php`, usar variables opcionales:

```blade
<a href="{{ $rutaInicio ?? route('inicials.home') }}" ...>
    {{ $etiquetaInicio ?? 'Educación Inicial' }}
</a>
...
<p class="mt-2 text-[11px] text-gray-600">
    {{ $notaRevision ?? 'Para escribirla, use la pestaña de la perspectiva correspondiente.' }}
</p>
```

Así las tres perspectivas actuales no cambian y el manager obtiene migas y nota correctas. Se prueba la regresión en §11.

### 7.2 `App\Services\Inicial\SeguimientoInicial`

Servicio de lectura. **No tiene métodos de escritura.** Su responsabilidad es convertir filtros de UI en consultas, calcular el estado de cada documento y resumir KPI.

#### API pública

```php
namespace App\Services\Inicial;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Inicial\Eifinalk;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class SeguimientoInicial
{
    public function __construct(
        private readonly PerspectivaFiltros $filtros
    ) {}

    /** @return array<int, string> */
    public function pestanas(): array;

    /** Docentes de Inicial: flag + los que ya tienen registros. @return Collection<int,string> */
    public function profesores(): Collection;

    /** @return Collection<int,string> */
    public function grados(): Collection;

    /** @return Collection<int,string> */
    public function secciones(?int $gradoId): Collection;

    /** @return Collection<int,string> */
    public function lapsos(): Collection;

    /** @param array<string,mixed> $filtros */
    public function paginar(string $entidad, array $filtros, int $porPagina = 15): LengthAwarePaginator;

    /** @param array<string,mixed> $filtros @return array<string,mixed> */
    public function resumen(string $entidad, array $filtros): array;

    /** @return array{clave:string, etiqueta:string, variante:string, detalle:string} */
    public function estado(string $entidad, Model $documento): array;

    /** @return array<int, array{0:string,1:callable}> */
    public function columnas(string $entidad): array;

    /** @return array<string,string> */
    public function opcionesEstado(string $entidad): array;

    /** Admite el filtro de fechas/lapso según el documento. */
    public function soportaFechas(string $entidad): bool;
    public function soportaLapso(string $entidad): bool;
}
```

#### Catálogos, pestañas y soporte de filtros

```php
/** @return array<int, string> */
public function pestanas(): array
{
    return RegistroInicial::claves();
}

public function grados(): Collection
{
    return $this->filtros->grados();
}

public function secciones(?int $gradoId): Collection
{
    return $this->filtros->secciones($gradoId);
}

public function lapsos(): Collection
{
    return $this->filtros->lapsos();
}

public function soportaFechas(string $entidad): bool
{
    return $entidad !== 'eifinalks';
}

public function soportaLapso(string $entidad): bool
{
    return in_array($entidad, ['eievaluationks', 'eifinalks'], true);
}

/**
 * Docentes del filtro: los que tienen el flag MÁS los que ya tienen
 * documentos (un flag retirado no debe ocultar el histórico a revisar).
 *
 * No se usa PerspectivaFiltros::profesores() porque cruza `pevaluacions` y
 * dejaría fuera a un docente con planes pero sin carga registrada.
 */
public function profesores(): Collection
{
    $conFlag = User::query()
        ->where('is_inicial', true)
        ->with('profesor:id,user_id,name,lastname')
        ->get()
        ->pluck('profesor')
        ->filter()
        ->mapWithKeys(fn ($p) => [$p->id => trim("{$p->lastname}, {$p->name}")]);

    $conDocumentos = Profesor::query()
        ->whereIn('id', $this->idsConDocumentos())
        ->get()
        ->mapWithKeys(fn ($p) => [$p->id => trim("{$p->lastname}, {$p->name}")]);

    // union() conserva las claves de la izquierda y añade las que falten;
    // sort() ordena por etiqueta sin perder el id.
    return $conFlag->union($conDocumentos)->sort();
}

/** @return array<int, int> */
private function idsConDocumentos(): array
{
    return collect(RegistroInicial::claves())
        ->flatMap(function (string $entidad) {
            if ($entidad === 'eifinalks') {
                return Pevaluacion::query()
                    ->whereIn('id', $this->consultaBase($entidad)->select('pevaluacion_id'))
                    ->pluck('profesor_id');
            }

            return $this->consultaBase($entidad)->distinct()->pluck('profesor_id');
        })
        ->filter()
        ->unique()
        ->values()
        ->all();
}

public function opcionesEstado(string $entidad): array
{
    if ($entidad === 'eifinalks') {
        return []; // no tiene revisión ni estado de contenido en fase 1
    }

    $revision = [
        'sin_revision' => 'Sin revisión',
        'revisado' => 'Revisado',
    ];

    return $entidad === 'eiplanningwks'
        ? [
            'sin_iniciar' => 'Sin iniciar',
            'en_progreso' => 'En progreso',
            'completo' => 'Completo',
        ] + $revision
        : $revision;
}

// La tabla no recibe columnas: la fila la arma el catálogo con
// RegistroInicial::filaTabla(), que conoce el documento y agrupa sus campos.
// Ver §9.6.
```

> `idsConDocumentos()` son seis consultas `distinct` sobre columnas ya
> indexadas o de bajo cardinalidad. Se ejecutan **una vez por carga de
> página**: los catálogos se cargan en `mount()`, no en cada re-render de
> filtros. Si el catálogo se vuelve caro, se cachea 5 minutos; no se cachea el
> listado.

#### Consulta de listado: no usar `RegistroInicial::consulta()`

`RegistroInicial::consulta()` carga **todas** las relaciones, incluidas estrategias y resúmenes. Es correcto para `show()`/`format()`, pero inaceptable para una página de 15 filas: multiplicaría los modelos y dispararía el tiempo de respuesta.

El servicio define su propia consulta de listado:

```php
private const RELACIONES_LISTADO = [
    'eiplanningwks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura'],
    'eiplanningbwks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura', 'eiprojectk:id'],
    'eiprojectks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura'],
    'eispecialks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura'],
    'eievaluationks' => ['profesor', 'grado', 'seccion', 'lapso', 'pensum.asignatura'],
    'eifinalks' => [
        'pevaluacion.profesor',
        'pevaluacion.seccion',
        'pevaluacion.lapso',
        'pevaluacion.pensum.asignatura',
        'expectant',
    ],
];

/**
 * Contadores por documento: alias => [relación, columna de contenido].
 *
 * `null` como columna significa contar todas las filas de la relación. En la
 * planificación semanal se cuenta solo `lunes` no vacío, igual que el listado
 * del docente: el texto vive SIEMPRE en `lunes` (quirk de persistencia, D3) y
 * las filas legadas vacías no deben inflar el progreso.
 */
private const CONTEO = [
    'eiplanningwks' => [
        'estrategias_count' => ['eiplanningwstrategies', 'lunes'],
        'resumenes_count' => ['eiplanningwsummaries', null],
    ],
    'eiplanningbwks' => [
        'estrategias_count' => ['eiplanningbwstrategies', 'lunes'],
        'resumenes_count' => ['eiplanningbwsummaries', null],
    ],
    'eiprojectks' => [
        'estrategias_count' => ['eiprojectkstrategies', 'lunes'],
        'resumenes_count' => ['eiprojectsummaries', null],
        'revisiones_count' => ['eiprojectreviews', null],
    ],
    'eispecialks' => [
        'estrategias_count' => ['eispecialstrategies', 'lunes'],
        'actividades_count' => ['activities', null],
    ],
    'eievaluationks' => [
        'posiciones_count' => ['eievaluationps', null],
    ],
    'eifinalks' => [
        'expectativas_count' => ['expectations', null],
    ],
];

private function consultaBase(string $entidad): Builder
{
    /** @var class-string<Model> $modelo */
    $modelo = RegistroInicial::modelo($entidad);

    return $modelo::query();
}

private function consultaListado(string $entidad): Builder
{
    return $this->aplicarConteos(
        $this->consultaBase($entidad)->with(self::RELACIONES_LISTADO[$entidad]),
        $entidad
    );
}

private function aplicarConteos(Builder $query, string $entidad): Builder
{
    foreach (self::CONTEO[$entidad] ?? [] as $alias => [$relacion, $columna]) {
        $query->withCount([
            $relacion.' as '.$alias => fn ($q) => $columna === null
                ? $q
                : $q->whereNotNull($columna)->where($columna, '<>', ''),
        ]);
    }

    return $query;
}
```

> El alias (`estrategias_count`, `posiciones_count`, …) es el que leen
> `filaTabla()` y `estado()`. Un test comprueba que cada alias del mapa
> aparece como atributo en el modelo paginado, para que un rename de relación
> no rompa el listado en silencio.

#### Filtros

| Filtro UI | Clave | Semanal | Quincenal | Proyecto | Especial | Evaluación | Informe final |
|---|---|---|---|---|---|---|---|
| Docente | `profesor_id` | `profesor_id` | `profesor_id` | `profesor_id` | `profesor_id` | `profesor_id` | `whereHas('pevaluacion')` |
| Nivel | `grado_id` | `grado_id` | `grado_id` | `grado_id` | `grado_id` | `grado_id` | `whereHas('pevaluacion.pensum')` |
| Sección | `seccion_id` | `seccion_id` | `seccion_id` | `seccion_id` | `seccion_id` | `seccion_id` | `whereHas('pevaluacion')` |
| Lapso | `lapso_id` | — | — | — | — | `lapso_id` | `whereHas('pevaluacion')` |
| Desde | `desde` | `finicial >=` | `finicial >=` | `finicial >=` | `finicial >=` | `finicial >=` | `created_at >=` |
| Hasta | `hasta` | `ffinal <=` | `ffinal <=` | `ffinal <=` | `ffinal <=` | `ffinal <=` | `created_at <=` |
| Estado | `estado` | sin_iniciar/en_progreso/completo + revisión | revisión | revisión | revisión | revisión | — |
| Búsqueda | `search` | `diagnostico`, `observacion` | idem | `diagnostico`, `observacion` | `justificacion`, `observacion` | `observaciones`, `recomendacion`, `asistencia` | `title`, estudiante |

Reglas:

- **Fechas:** se interpreta “solapa el rango”: `finicial <= hasta` y `ffinal >= desde`. Es lo que espera un supervisor que busca “lo trabajado en octubre”, no solo lo que empieza y termina dentro.
- **Lapso:** solo se ofrece para evaluación e informe final; en los demás la UI lo oculta y el servicio lo ignora.
- **Estado:** dos ejes que comparten el parámetro `estado`. **Contenido** solo en semanal: `sin_iniciar` (0 estrategias), `en_progreso` (1..49) y `completo` (≥ 50); los tres tramos son disjuntos y suman el total. **Revisión** en los cinco documentos con campo: `sin_revision` / `revisado`. Al ser un único `<select>`, no se pueden combinar ambos ejes. El informe final no tiene revisión: su estado es “emitido”.
- **Búsqueda:** `LIKE %texto%` sobre las columnas declaradas; para el informe final se añade `whereHas('expectant', ...)` por nombre/apellido.
- **Docente:** la lista del filtro no usa `PerspectivaFiltros::profesores()` porque esa cruza `pevaluacions` y dejaría fuera a un docente con planes pero sin carga registrada. El servicio usa el flag `is_inicial` **más** los `profesor_id` distintos que ya existen en los seis documentos.

```php
public function paginar(string $entidad, array $filtros, int $porPagina = 15): LengthAwarePaginator
{
    return $this->aplicarFiltros($this->consultaListado($entidad), $entidad, $filtros)
        ->latest('id')
        ->paginate($porPagina)
        ->withQueryString();
}

private const BUSQUEDA = [
    'eiplanningwks' => ['diagnostico', 'observacion'],
    'eiplanningbwks' => ['diagnostico', 'observacion'],
    'eiprojectks' => ['diagnostico', 'observacion'],
    'eispecialks' => ['justificacion', 'observacion'],
    'eievaluationks' => ['observaciones', 'recomendacion', 'asistencia'],
    'eifinalks' => ['title'],
];

private function aplicarFiltros(Builder $query, string $entidad, array $filtros): Builder
{
    $final = $entidad === 'eifinalks';

    // Docente y sección: el informe final los hereda de la carga académica.
    foreach (['profesor_id', 'seccion_id'] as $columna) {
        if (! empty($filtros[$columna])) {
            $final
                ? $query->whereHas('pevaluacion', fn ($q) => $q->where($columna, $filtros[$columna]))
                : $query->where($columna, $filtros[$columna]);
        }
    }

    if (! empty($filtros['grado_id'])) {
        $final
            ? $query->whereHas('pevaluacion.pensum', fn ($q) => $q->where('grado_id', $filtros['grado_id']))
            : $query->where('grado_id', $filtros['grado_id']);
    }

    // Lapso: solo evaluación (columna propia) e informe final (vía carga).
    if (! empty($filtros['lapso_id'])) {
        if ($entidad === 'eievaluationks') {
            $query->where('lapso_id', $filtros['lapso_id']);
        } elseif ($final) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('lapso_id', $filtros['lapso_id']));
        }
    }

    // Fechas: solapamiento con el rango, no contención. El informe final no
    // tiene período propio: se usa created_at (fase 2 puede cambiarlo).
    $columnaInicio = $final ? 'created_at' : 'finicial';
    $columnaFin = $final ? 'created_at' : 'ffinal';

    if (! empty($filtros['desde'])) {
        $query->whereDate($columnaFin, '>=', $filtros['desde']);
    }

    if (! empty($filtros['hasta'])) {
        $query->whereDate($columnaInicio, '<=', $filtros['hasta']);
    }

    if (! empty($filtros['search'])) {
        $texto = '%'.$filtros['search'].'%';

        $query->where(function (Builder $q) use ($entidad, $texto) {
            foreach (self::BUSQUEDA[$entidad] as $columna) {
                $q->orWhere($columna, 'like', $texto);
            }

            if ($entidad === 'eifinalks') {
                $q->orWhereHas('expectant', fn ($e) => $e
                    ->where('firstname', 'like', $texto)
                    ->orWhere('lastname', 'like', $texto));
            }
        });
    }

    if (! empty($filtros['estado'])) {
        $this->aplicarEstado($query, $entidad, $filtros['estado']);
    }

    return $query;
}

private function aplicarEstado(Builder $query, string $entidad, string $estado): void
{
    $campoRevision = RegistroInicial::campoRevision($entidad);

    if ($campoRevision && $estado === 'sin_revision') {
        $query->where(fn ($q) => $q->whereNull($campoRevision)->orWhere($campoRevision, ''));

        return;
    }

    if ($campoRevision && $estado === 'revisado') {
        $query->whereNotNull($campoRevision)->where($campoRevision, '<>', '');

        return;
    }

    // Estados de CONTENIDO. Se resuelven con whereHas + umbral, no con
    // having() sobre el alias de withCount(): paginate() ejecuta un count(*)
    // que se rompe con un HAVING sin GROUP BY.
    //
    // Se miran TODAS las relaciones de contenido del documento (estrategias,
    // actividades, posiciones), no solo la primera: un plan especial puede
    // tener actividades sin estrategias y no por eso está "sin iniciar".
    $relevantes = array_values(array_intersect_key(
        self::CONTEO[$entidad] ?? [],
        array_flip(['estrategias_count', 'actividades_count', 'posiciones_count'])
    ));

    if ($relevantes === []) {
        return; // el informe final no ofrece filtro de estado
    }

    // Devuelve el closure de condición para cada relación de contenido.
    $conContenido = fn (?string $columna) => fn ($q) => $columna === null
        ? $q
        : $q->whereNotNull($columna)->where($columna, '<>', '');

    // Umbral de la rejilla semanal: 5 días × 10 momentos = 50.
    $total = count(config('inicial.dias_semana')) * count(config('inicial.momentos_rutina_diaria'));

    match ($estado) {
        // `completo` solo se ofrece en semanal: es el único documento con una
        // rejilla de celdas acotada (5 días × 10 momentos) en fase 1.
        'completo' => $query->whereHas(
            'eiplanningwstrategies',
            $conContenido('lunes'),
            '>=',
            $total
        ),
        // En semanal, `en_progreso` es el tramo 1..49: tiene contenido pero no
        // llega a la rejilla completa. Sin este techo, el filtro devolvía
        // también las semanas cuyo badge dice «Completo».
        'en_progreso' => $query
            ->where(function ($q) use ($relevantes, $conContenido) {
                foreach ($relevantes as [$relacion, $columna]) {
                    $q->orWhereHas($relacion, $conContenido($columna));
                }
            })
            ->when(
                $entidad === 'eiplanningwks',
                fn (Builder $q) => $q->whereHas(
                    'eiplanningwstrategies',
                    $conContenido('lunes'),
                    '<',
                    $total
                )
            ),
        'sin_iniciar' => $query->where(function ($q) use ($relevantes, $conContenido) {
            foreach ($relevantes as [$relacion, $columna]) {
                $q->whereDoesntHave($relacion, $conContenido($columna));
            }
        }),
        default => null,
    };
}
```

#### Estado y completitud (fase 1)

```php
public function estado(string $entidad, Model $documento): array
{
    if ($entidad === 'eiplanningwks') {
        $total = count(config('inicial.dias_semana')) * count(config('inicial.momentos_rutina_diaria')); // 50
        $escritas = (int) ($documento->estrategias_count ?? 0);

        return match (true) {
            $escritas >= $total => [
                'clave' => 'completo',
                'etiqueta' => 'Completo',
                'variante' => 'emerald',
                'detalle' => "{$escritas}/{$total} estrategias",
            ],
            $escritas > 0 => [
                'clave' => 'en_progreso',
                'etiqueta' => 'En progreso',
                'variante' => 'amber',
                'detalle' => "{$escritas}/{$total} estrategias",
            ],
            default => [
                'clave' => 'sin_iniciar',
                'etiqueta' => 'Sin iniciar',
                'variante' => 'gray',
                'detalle' => "0/{$total} estrategias",
            ],
        };
    }

    if ($entidad === 'eifinalks') {
        return [
            'clave' => 'emitido',
            'etiqueta' => 'Emitido',
            'variante' => 'emerald',
            'detalle' => 'Informe final',
        ];
    }

    // El máximo de los contadores: `??` no sirve porque withCount devuelve 0
    // (no null) y un plan especial puede tener actividades sin estrategias.
    $contenido = max(
        (int) ($documento->estrategias_count ?? 0),
        (int) ($documento->actividades_count ?? 0),
        (int) ($documento->posiciones_count ?? 0),
        (int) ($documento->expectativas_count ?? 0),
    );

    return [
        'clave' => $contenido > 0 ? 'en_progreso' : 'sin_iniciar',
        'etiqueta' => $contenido > 0 ? 'Con contenido' : 'Sin contenido',
        'variante' => $contenido > 0 ? 'cyan' : 'gray',
        'detalle' => $contenido.' ítem(s)',
    ];
}
```

La revisión se muestra como **badge independiente**, no se mezcla con la completitud:

- `observacion` / `recomendacion` vacíos → `Sin revisión` (ámbar).
- Con texto → `Revisado` (esmeralda).
- Informe final → sin badge de revisión.

> El filtro de contenido replica estos mismos tramos: `sin_iniciar` = 0,
> `en_progreso` = 1..49 y `completo` = 50. Así el badge de cada fila siempre
> coincide con el filtro que la muestra: un plan completo no aparece bajo
> “En progreso”. Un test comprueba que los tres tramos son disjuntos y suman
> el total.

> **Por qué no se reutiliza `EiplanningwkComponent::attachCompletion()`:** ese
> método calcula días con contenido y resúmenes esperados **por sección del
> docente autenticado** (`$this->profesor_id`), y el manager no tiene profesor.
> En fase 1 el seguimiento mide celdas escritas de la rejilla (50) y muestra
> resúmenes como dato absoluto; en fase 2 se puede portar el cálculo de
> resúmenes esperados por sección, agrupado para toda la página y sin depender
> del usuario. Ambas métricas deben llamarse distinto en la UI (“Estrategias”
> vs. “Resúmenes esperados”) para que no parezcan la misma cifra.

#### KPI (`resumen`)

Para la entidad activa y los mismos filtros:

```php
public function resumen(string $entidad, array $filtros): array
{
    $query = $this->aplicarFiltros($this->consultaBase($entidad), $entidad, $filtros);
    $campoRevision = RegistroInicial::campoRevision($entidad);

    return [
        'total' => (clone $query)->count(),
        'docentes' => $this->contarDocentes(clone $query, $entidad),
        'sin_revision' => $campoRevision
            ? (clone $query)
                ->where(fn ($q) => $q->whereNull($campoRevision)->orWhere($campoRevision, ''))
                ->count()
            : null,
        'revisados' => $campoRevision
            ? (clone $query)->whereNotNull($campoRevision)->where($campoRevision, '<>', '')->count()
            : null,
        'completos' => $entidad === 'eiplanningwks'
            ? $this->contarCompletos(clone $query)
            : null,
        'ultima_actualizacion' => (clone $query)->max('updated_at'),
    ];
}

private function contarDocentes(Builder $query, string $entidad): int
{
    // El informe final no tiene profesor_id: el docente se hereda de la carga.
    $columna = $entidad === 'eifinalks' ? 'pevaluacion_id' : 'profesor_id';

    return (int) $query->distinct()->count($columna);
}

private function contarCompletos(Builder $query): int
{
    $total = count(config('inicial.dias_semana')) * count(config('inicial.momentos_rutina_diaria'));

    // `whereHas` con umbral y no `having()` sobre el alias de withCount():
    // paginate() ejecuta un count(*) que se rompe con un HAVING sin GROUP BY.
    return $query->whereHas(
        'eiplanningwstrategies',
        fn ($q) => $q->whereNotNull('lunes')->where('lunes', '<>', ''),
        '>=',
        $total
    )->count();
}
```

- `completos` solo se promete en semanal. Para el resto de documentos el KPI
  muestra “—”; no se inventa un umbral que el documento no tiene.
- El informe final no tiene campo de revisión: `sin_revision` y `revisados`
  llegan como `null` y la vista oculta esas dos tarjetas.
- No se cachea en fase 1. Si el volumen crece, cachear `resumen` 60 s con una clave derivada de entidad + filtros.
- `aplicarFiltros()` es el único sitio donde se traducen los filtros de UI a
  SQL (§7.2, tabla de filtros). `resumen()` y `paginar()` lo comparten, de modo
  que las cifras siempre cuadran con la tabla que tienen debajo.

#### Filas de la tabla: agrupadas, no una columna por dato

La primera versión reutilizaba una variante plana del catálogo (una columna
por fecha, nivel, docente, área, contador y texto). En pantalla obligaba a
`nowrap` y a `overflow-x-auto`, y desbordaba incluso en escritorio.

La tabla usa ahora `RegistroInicial::filaTabla()`, que devuelve la fila ya
agrupada:

```php
[
    'principal' => '3 años · Sección A · Matemáticas',
    'secundario' => 'Docente …',
    'meta' => 'N° 12 · 15/10/2026 → 22/10/2026 · 1 semana',
    'contenidoEtiqueta' => 'Diagnóstico',
    'contenido' => 'Texto del diagnóstico…',
    'contadores' => ['Estrategias' => 12, 'Resúmenes' => 3],
]
```

- La celda **Documento** concentra nivel · sección · área, docente y metadatos.
- La celda **Contenido** muestra el texto propio de cada documento
  (diagnóstico, justificación, observaciones o título) y los contadores.
- Los contadores siguen leyendo los alias de `withCount()`, no las colecciones.
- El detalle y el formato siguen usando `columnas()`.

---

## 8. Componente Livewire

Archivo: `app/Livewire/Inicial/SeguimientoInicialComponent.php`
Vista raíz: `resources/views/livewire/inicial/seguimiento/index.blade.php`

### 8.1 Propiedades y estado

```php
namespace App\Livewire\Inicial;

use App\Services\Inicial\RegistroInicial;
use App\Services\Inicial\SeguimientoInicial;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado de seguimiento de Educación Inicial.
 *
 * No crea, actualiza ni elimina documentos. La ÚNICA escritura permitida es
 * `guardarRevision()`: el manager registra `observacion` (o `recomendacion` en
 * el plan de evaluación) desde el modal «Ver», con la misma regla `min:5` que
 * la Coordinación de Evaluación. El resto del documento sigue siendo de solo
 * lectura y las rutas del grupo solo declaran GET.
 */
class SeguimientoInicialComponent extends Component
{
    use WithPagination;

    /** Documento activo; lo fija la ruta y no se puede cambiar desde el cliente. */
    #[Locked]
    public string $entidad;

    #[Url(as: 'docente', except: '')]
    public $profesor_id = '';

    #[Url(as: 'nivel', except: '')]
    public $grado_id = '';

    #[Url(as: 'seccion', except: '')]
    public $seccion_id = '';

    #[Url(as: 'lapso', except: '')]
    public $lapso_id = '';

    #[Url(as: 'desde', except: '')]
    public $desde = '';

    #[Url(as: 'hasta', except: '')]
    public $hasta = '';

    #[Url(as: 'estado', except: '')]
    public $estado = '';

    #[Url(as: 'buscar', except: '')]
    public string $search = '';

    #[Url(as: 'vista', except: 'grid')]
    public string $viewMode = 'grid';

    /** Página y tamaño. */
    public int $paginate = 15;

    /** Documento abierto en el modal de detalle. */
    #[Locked]
    public ?int $detalleId = null;

    /** El modal muestra el formulario de revisión en lugar del detalle. */
    public bool $revisionAbierta = false;

    /** Texto del campo revisable (`observacion` o `recomendacion`). */
    public string $revision = '';

    /** Aviso de éxito que se muestra al volver al detalle. */
    public ?string $avisoRevision = null;

    /** Catálogos de filtros. */
    public Collection $listProfesores;
    public Collection $listGrados;
    public Collection $listSecciones;
    public Collection $listLapsos;

    public function mount(string $entidad): void
    {
        // Defensa en profundidad: la ruta ya exige el middleware, pero el
        // componente no debe ser usable si alguien lo invoca de otra forma.
        abort_unless(auth()->user()?->isInicialManager(), 403);
        abort_unless(RegistroInicial::existe($entidad), 404);

        $this->entidad = $entidad;
        $this->cargarCatalogos();
    }

    public function render()
    {
        $servicio = app(SeguimientoInicial::class);
        $filtros = $this->filtros();

        $documentos = $servicio->paginar($this->entidad, $filtros, $this->paginate);

        return view('livewire.inicial.seguimiento.index', [
            'documentos' => $documentos,
            'resumen' => $servicio->resumen($this->entidad, $filtros),
            'opcionesEstado' => $servicio->opcionesEstado($this->entidad),
            // `consulta()->first()` y no `buscar()`: dentro de Livewire un 404
            // por un registro borrado mientras el modal estaba abierto rompe
            // la respuesta; aquí el modal simplemente se queda sin cuerpo.
            'detalle' => $this->detalleId
                ? RegistroInicial::consulta($this->entidad)->whereKey($this->detalleId)->first()
                : null,
            'detalleParcial' => RegistroInicial::parcialDetalle($this->entidad),
            'pestanas' => $servicio->pestanas(),
        ]);
    }
}
```

### 8.2 Métodos (consulta, navegación y revisión)

| Método | Responsabilidad |
|---|---|
| `cargarCatalogos()` | Carga docentes, niveles, secciones y lapsos desde `SeguimientoInicial`. |
| `updatedGradoId()` | Al cambiar el nivel, limpia la sección y recarga sus opciones. |
| `updated(string $name)` | Resetea la página cuando cambia un filtro. |
| `limpiarFiltros()` | Limpia filtros y página; conserva la entidad. |
| `alternarVista()` | Alterna `grid`/`table`; se refleja en la URL (`vista`). |
| `abrirDetalle(int $id)` | Valida que el id exista para la entidad, abre el modal y limpia cualquier revisión pendiente. |
| `cerrarDetalle()` | Cierra el modal, limpia `detalleId` y el estado de revisión. |
| `abrirRevision()` | Precarga el texto actual y muestra el formulario. 404 si la entidad no tiene campo revisable (informe final). |
| `cerrarRevision()` | Descarta el texto no guardado y vuelve al detalle. |
| `guardarRevision()` | **Única escritura.** Valida `required\|string\|min:5`, escribe `observacion`/`recomendacion` y deja `avisoRevision`. |
| `queryFiltros(): array` | Devuelve los filtros no vacíos para construir los enlaces de pestañas. |

```php
/** Catálogos del panel de filtros. Una sola vez por request. */
private function cargarCatalogos(): void
{
    $servicio = app(SeguimientoInicial::class);

    $this->listProfesores = $servicio->profesores();
    $this->listGrados = $servicio->grados();
    $this->listLapsos = $servicio->lapsos();
    $this->listSecciones = $servicio->secciones(
        $this->grado_id === '' ? null : (int) $this->grado_id
    );
}

/** Filtros no vacíos que consumen el servicio y la query string. */
private function filtros(): array
{
    return array_filter([
        'profesor_id' => $this->profesor_id,
        'grado_id' => $this->grado_id,
        'seccion_id' => $this->seccion_id,
        'lapso_id' => $this->lapso_id,
        'desde' => $this->desde,
        'hasta' => $this->hasta,
        'estado' => $this->estado,
        'search' => $this->search,
    ], fn ($valor) => $valor !== null && $valor !== '');
}

public function updatedGradoId(): void
{
    // Al cambiar (o limpiar) el nivel, la sección deja de ser válida: se
    // limpia y sus opciones se recalculan para el nivel nuevo. Sin nivel, el
    // selector de sección queda vacío y deshabilitado en la vista.
    $this->seccion_id = '';
    $this->listSecciones = app(SeguimientoInicial::class)
        ->secciones($this->grado_id === '' ? null : (int) $this->grado_id);

    $this->resetPage();
}

public function limpiarFiltros(): void
{
    $this->reset([
        'profesor_id', 'grado_id', 'seccion_id', 'lapso_id',
        'desde', 'hasta', 'estado', 'search',
    ]);

    $this->listSecciones = collect();
    $this->resetPage();
}

public function updated(string $name): void
{
    if (in_array($name, [
        'profesor_id', 'grado_id', 'seccion_id', 'lapso_id',
        'desde', 'hasta', 'estado', 'search',
    ], true)) {
        $this->resetPage();
    }
}

public function abrirDetalle(int $id): void
{
    $existe = RegistroInicial::modelo($this->entidad)::query()->whereKey($id)->exists();

    if (! $existe) {
        $this->cerrarDetalle();

        return;
    }

    $this->detalleId = $id;
    $this->limpiarRevision();
    $this->avisoRevision = null;
}

public function cerrarDetalle(): void
{
    $this->detalleId = null;
    $this->limpiarRevision();
    $this->avisoRevision = null;
}

public function abrirRevision(): void
{
    $documento = $this->documentoDetalle();
    $campo = RegistroInicial::campoRevision($this->entidad);

    abort_if(! $campo || ! $documento, 404);

    $this->revision = (string) $documento->{$campo};
    $this->revisionAbierta = true;
    $this->avisoRevision = null;

    $this->resetValidation('revision');
}

public function guardarRevision(): void
{
    // Una actualización de Livewire no vuelve a ejecutar `mount()`.
    abort_unless(auth()->user()?->isInicialManager(), 403);

    $campo = RegistroInicial::campoRevision($this->entidad);
    abort_if(! $campo, 404);

    $rotulo = RegistroInicial::rotuloRevision($this->entidad);
    $minuscula = mb_strtolower($rotulo);

    $this->validate([
        'revision' => ['required', 'string', 'min:5'],
    ], [
        'revision.required' => "Escribe la {$minuscula} de la Coordinación.",
        'revision.min' => "La {$minuscula} debe tener al menos 5 caracteres.",
    ]);

    $documento = $this->documentoDetalle();
    abort_if(! $documento, 404);

    $documento->{$campo} = trim($this->revision);
    $documento->save();

    $this->limpiarRevision();
    $this->avisoRevision = "{$rotulo} guardada.";
}

public function alternarVista(): void
{
    $this->viewMode = $this->viewMode === 'grid' ? 'table' : 'grid';
}

public function queryFiltros(): array
{
    return array_filter([
        'docente' => $this->profesor_id,
        'nivel' => $this->grado_id,
        'seccion' => $this->seccion_id,
        'lapso' => $this->lapso_id,
        'desde' => $this->desde,
        'hasta' => $this->hasta,
        'estado' => $this->estado,
        'buscar' => $this->search,
        'vista' => $this->viewMode === 'grid' ? null : $this->viewMode,
    ], fn ($valor) => $valor !== null && $valor !== '');
}
```

Notas de implementación:

- `updatedGradoId()` se dispara antes que `updated('grado_id')`; el `resetPage()` duplicado es inocuo.
- `abrirDetalle()` no usa `findOrFail` dentro de Livewire: un 404 de Livewire rompe el modal. Se comprueba existencia y, si no, se cierra sin notificación (mismo criterio de no revelar ids).
- `guardarRevision()` es la única llamada que persiste. No toca otros campos ni crea registros; `documentoDetalle()` relee el registro por id para no confiar en el snapshot del cliente.
- `abrirRevision()` responde 404 cuando `RegistroInicial::campoRevision()` es `null` (informe final): no se ofrece un formulario que al guardar no escribiría nada.
- La autorización sigue siendo el middleware + el `abort_unless` del `mount()`; la escritura añade su propio `abort_unless` como defensa en profundidad.
- No se usa `WireUiActions`: el aviso de éxito es un bloque `avisoRevision` que se muestra al volver al detalle.

### 8.3 Pestañas dentro del componente

Las pestañas deben vivir **dentro** de la vista Livewire, no en el shell del controlador. Motivo: cuando el usuario cambia un filtro, Livewire actualiza la URL; si las pestañas estuvieran fuera, conservarían los filtros viejos. Dentro, se re-renderizan con `queryFiltros()`:

```blade
@foreach ($pestanas as $clave)
    <a href="{{ route('inicials.seguimiento.'.$clave.'.index', $this->queryFiltros()) }}"
       role="tab"
       aria-selected="{{ $entidad === $clave ? 'true' : 'false' }}"
       @class([...])>
        {{ \App\Services\Inicial\RegistroInicial::titulo($clave) }}
    </a>
@endforeach
```

Cada pestaña es una navegación completa (GET). Es el mismo patrón de las perspectivas de Planificación y Académico: URL compartible, sin cargar seis listados a la vez.

### 8.4 Filtros y query string

- El formulario usa `wire:model.live` para los selects y `wire:model.live.debounce.400ms` para la búsqueda.
- “Limpiar” llama `limpiarFiltros()`.
- Los nombres visibles en la URL son `docente`, `nivel`, `seccion`, `lapso`, `desde`, `hasta`, `estado`, `buscar`, `vista`. No se exponen los nombres internos (`profesor_id`, `grado_id`).
- La paginación usa `WithPagination`; `paginate()` conserva el query string.
- No se usa `wire:model` sobre campos del documento. Esa es la garantía visual de que no hay edición.

---

## 9. Vistas y parciales

### 9.1 Árbol de archivos

```text
resources/views/inicial/seguimiento/
  index.blade.php                       ← shell: layout, header, <livewire>
resources/views/livewire/inicial/seguimiento/
  index.blade.php                       ← raíz del componente
  partials/
    kpis.blade.php                      ← 4 tarjetas de resumen
    filters.blade.php                   ← formulario de filtros
    tabs.blade.php                      ← pestañas por documento
    grid.blade.php                      ← tarjetas
    table.blade.php                     ← tabla de solo lectura
    card-eiplanningwk.blade.php         ← tarjeta rica de planificación semanal
    card-documento.blade.php            ← tarjeta genérica para los otros 5
    detalle-modal.blade.php             ← modal de detalle
    empty.blade.php                     ← estado vacío
```

No se crea un `show.blade.php` nuevo: el detalle reutiliza `inicial.shared.document-show` con los hooks de §7.1. Tampoco se duplican las vistas de formato: se reutilizan las de `RegistroInicial::vistaFormato()`.

### 9.2 Shell `inicial/seguimiento/index.blade.php`

```blade
<x-layouts.role>
    <div class="py-6">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <header class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <nav class="text-xs text-gray-500 mb-1" aria-label="Migas de pan">
                        <ol class="flex items-center gap-2">
                            <li class="text-gray-400">Educación Inicial</li>
                            <li aria-hidden="true">/</li>
                            <li class="text-gray-300" aria-current="page">Seguimiento</li>
                        </ol>
                    </nav>

                    <h1 class="text-lg font-semibold text-gray-100">
                        Seguimiento de Educación Inicial
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Consulta de todos los docentes. Los documentos no se crean ni se editan;
                        solo se registra la revisión.
                    </p>
                </div>

                <div class="flex items-center gap-2" x-data="{ leyenda: false }">
                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-cyan-500/20 bg-cyan-500/10 px-3 py-1.5 text-xs font-medium text-cyan-300"
                        title="Los documentos son de solo lectura; la revisión sí se registra.">
                        Solo lectura
                    </span>

                    <button type="button" x-on:click="leyenda = true" aria-haspopup="dialog">
                        Leyenda de estados
                    </button>

                    @include('inicial.seguimiento.partials.leyenda-estados')
                </div>
            </header>

            <livewire:inicial.seguimiento-inicial-component
                :entidad="$entidad"
                :key="$entidad" />
        </div>
    </div>
</x-layouts.role>
```

### 9.3 KPI

Cuatro tarjetas para la entidad activa:

| Tarjeta | Valor | Nota |
|---|---|---|
| Registros | `$resumen['total']` | Con los filtros actuales |
| Docentes | `$resumen['docentes']` | Distinct de profesor (o carga en informe final) |
| Sin revisión | `$resumen['sin_revision']` | Ámbar; oculta en informe final |
| Completos / Revisados | `$resumen['completos'] ?? $resumen['revisados']` | Semanal: completos; otros: revisados; final: “Emitidos” |

Usar `role="status"` o `aria-live="polite"` en la fila para que el cambio de filtros se anuncie. No usar gráficos en fase 1.

### 9.4 Filtros

Disposición en dos filas:

- **Fila 1:** Docente, Nivel, Sección y el selector de lapso o el par de fechas.
- **Fila 2:** Buscar, Estado y las acciones (Limpiar, Tabla/Tarjetas), con las acciones alineadas a la derecha.

Campos visibles según la entidad:

- **Siempre:** Docente, Nivel, Sección y Buscar.
- **Estado:** a la derecha de Buscar, en la misma segunda fila; en los cinco documentos que tienen estado, el informe final no lo ofrece (no se inventa un estado que el documento no tiene).
- **Semanal, quincenal, proyecto, especial:** Desde, Hasta.
- **Evaluación:** Desde, Hasta, Lapso.
- **Informe final:** Lapso (sin fechas; se usa `created_at` si se habilita en fase 2).

Detalles:

- Etiqueta **Nivel**, nunca “Grado”.
- Sección deshabilitada si no hay nivel: “Elija primero un nivel”.
- El selector de estado se genera con `opcionesEstado()`; no se ofrecen opciones que el servicio ignoraría.
- Botón **«Leyenda de estados»** junto a la insignia de solo lectura: abre un modal con la definición, la regla y un ejemplo de cada estado (contenido y revisión), incluido «Emitido» del informe final. Explica que son dos ejes independientes y que el filtro admite una sola opción.
- El botón “Limpiar” no recarga la página.
- `wire:loading` muestra un skeleton o un texto “Consultando…” en la tabla/tarjetas.

### 9.5 Tarjetas

**Planificación semanal (`card-eiplanningwk.blade.php`):** mismo lenguaje visual que `/app/inicials/eiplanningwks`, pero sin acciones de escritura.

- Cabecera: `Nivel · Sección`, fechas, badge de semanas.
- Docente y área.
- Diagnóstico truncado (3 líneas).
- Progreso: `estrategias_count / 50` con barra.
- Badges: estado de completitud + revisión.
- Acciones: **Ver** (modal), **Ficha** (show), **Imprimir** (format).

**Otros documentos (`card-documento.blade.php`):**

- Título del documento y número.
- Docente, `Nivel · Sección`, fechas o lapso.
- Contador principal según entidad (estrategias, actividades, posiciones, expectativas).
- Badge de estado + revisión (si aplica).
- Acciones: Ver, Ficha, Imprimir.

No hay botones de editar, clonar, eliminar, importar ni “Nuevo”.

### 9.6 Tabla

- **Cinco columnas fijas:** Documento, Contenido, Estado, Revisión y Acciones.
  - Documento: `Nivel · Sección · Área`, docente y `N° · período`.
  - Contenido: diagnóstico, justificación, observaciones o título, más los
    contadores de la entidad. La etiqueta cambia con el documento.
  - Revisión se omite en el informe final, que no tiene campo.
- La fila la arma `RegistroInicial::filaTabla()`; no se pasa `$columnas` al
  componente.
- **Sin scroll horizontal:** `table-fixed` + `colgroup` con porcentajes que
  suman 100. Nada de `min-w-*` ni `overflow-x-auto` en la tabla.
- **Responsive:** en `md+` es una tabla de 5 columnas; por debajo de `md` cada
  `tr` se apila como tarjeta (`block` / `md:table-row`) y las celdas muestran
  su etiqueta. El texto largo se limita con `line-clamp-3`, nunca con `nowrap`.
- Acciones: Ver (modal), Ficha (show), Imprimir (format).
- `wire:key="doc-{{ $entidad }}-{{ $documento->id }}"` en cada fila.
- `scope="col"`, `caption` accesible, `tabular-nums` en contadores.
- Paginación Tailwind: `{{ $documentos->links() }}`.
- Estado vacío: “No hay registros de {documento} para estos filtros.” con enlace para limpiar.

### 9.7 Modal de detalle y revisión

- Cabecera: título, docente, `Nivel · Sección`, fechas/lapso, badges y botón **«Registrar revisión»** / **«Editar revisión»** (solo si la entidad tiene campo revisable).
- Cuerpo:
  - Si `$detalleParcial` existe: `@include($detalleParcial, ['plan' => $detalle])`.
  - Si no (informe final): resumen de estudiante, carga, título y enlace al formato.
  - Si `$revisionAbierta`: formulario `wire:submit="guardarRevision"` con `<textarea wire:model="revision">`, `min:5`, aviso de que solo se modifica ese campo, botones Cancelar/Guardar. El pie del modal se oculta durante la revisión.
- Tras guardar, el modal vuelve al detalle, muestra `avisoRevision` y el badge pasa a «Revisado» en el mismo render.
- Pie: “Abrir ficha completa” (`show`), “Formato imprimible” (`format`), “Cerrar”.
- Sin CRUD: no hay campos del documento editables, ni botón de crear/eliminar/clonar.
- Cierre con `wire:click="cerrarDetalle"`, `@keydown.escape.window`, clic en backdrop o en la X: si el formulario está abierto, se cancela la revisión y se vuelve al detalle; no se descarta el modal completo.
- Si el documento ya no existe, `abrirDetalle()` cierra el modal sin revelar el id.
- El informe final no muestra el botón y `abrirRevision()` responde 404.

### 9.8 Detalle completo

Se reutiliza `inicial.shared.document-show`. Con los hooks de §7.1:

- Migas: “Seguimiento Inicial / {documento} / N° id”.
- Nota de revisión: explica que la revisión se registra desde el listado (modal «Ver») y que esta ficha es de solo lectura.
- Botón “Formato imprimible” apunta a `inicials.seguimiento.{entidad}.format`.
- El cuerpo incluye el parcial de detalle existente; para el informe final, el resumen que ya trae la vista compartida.

### 9.9 Impresión

- Se reutiliza `RegistroInicial::vistaFormato($entidad)`.
- **No** se añade marca de agua, pie “copia de seguimiento” ni firma adicional: el documento oficial debe ser idéntico al que imprime el docente.
- El manager solo cambia **quién** puede pedirlo; el papel es el mismo.
- Prueba: `format` responde 200 para manager y 403 para docente/admin sin flag.

### 9.10 Copy y accesibilidad

- “Seguimiento”, “Documento de solo lectura”, “Registrar revisión”, “Editar revisión”, “Leyenda de estados”, “Nivel”, “Sección”, “Docente”, “Período”, “Estado”, “Revisión”.
- No usar “Grado” en la UI.
- Contraste y tamaños coherentes con el módulo (Tailwind, dark mode).
- Foco visible en pestañas, filtros y acciones.
- El modal cierra con Escape y devuelve el foco al botón que lo abrió (mejora progresiva con Alpine).

---

## 10. Menú, layout y administración

### 10.1 `App\Services\MenuBuilder`

Tres cambios:

1. `$roleMeta`:

```php
'seguimiento_inicial' => 'Seguimiento · Educación Inicial',
```

2. `resolveLayoutForUser()`, después de `is_admin`:

```php
(bool) $user->isInicialManager() => 'seguimiento_inicial',
```

3. `hasPermission()`:

```php
'is_inicial_manager' => (bool) $user->isInicialManager(),
```

No hace falta tocar `resolveHref()` ni el render del navbar: las rutas de menú no llevan parámetros; cada documento tiene su propia ruta.

### 10.2 `config/menus.php`

Grupo nuevo:

```php
'seguimiento_inicial' => [
    'label' => 'Seguimiento Inicial',
    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.05-1.083-3.22A12 12 0 0112 2.944z',
    'permission' => 'is_inicial_manager',
    'color' => 'cyan',
    'items' => [
        [
            'label' => 'Panel',
            'route' => 'inicials.seguimiento.index',
            'active' => 'inicials.seguimiento.index',
        ],
        [
            'label' => 'Planificación semanal',
            'route' => 'inicials.seguimiento.eiplanningwks.index',
            'active' => 'inicials.seguimiento.eiplanningwks*',
        ],
        [
            'label' => 'Planificación quincenal',
            'route' => 'inicials.seguimiento.eiplanningbwks.index',
            'active' => 'inicials.seguimiento.eiplanningbwks*',
        ],
        [
            'label' => 'Proyecto de aula',
            'route' => 'inicials.seguimiento.eiprojectks.index',
            'active' => 'inicials.seguimiento.eiprojectks*',
        ],
        [
            'label' => 'Plan especial',
            'route' => 'inicials.seguimiento.eispecialks.index',
            'active' => 'inicials.seguimiento.eispecialks*',
        ],
        [
            'label' => 'Plan de evaluación',
            'route' => 'inicials.seguimiento.eievaluationks.index',
            'active' => 'inicials.seguimiento.eievaluationks*',
        ],
        [
            'label' => 'Informe final',
            'route' => 'inicials.seguimiento.eifinalks.index',
            'active' => 'inicials.seguimiento.eifinalks*',
        ],
    ],
],
```

Layout nuevo:

```php
'seguimiento_inicial' => [
    'groups' => ['seguimiento_inicial'],
],
```

Notas:

- El grupo incluye los seis documentos, igual que el menú del docente, para que el manager no dependa de las pestañas para descubrirlos.
- `active` con `*` marca también `show` y `format`.
- `'Panel'` apunta a `/seguimiento` y redirige a semanal; su patrón activo es exacto para no marcar dos ítems a la vez.
- No se añade el grupo a los layouts existentes: el manager es un perfil dedicado (ADR-11.8). Si se quiere que un manager con otro rol vea ambos menús, se revisa en una fase posterior.
- Los ítems sin `icon` son válidos (`components/menu/item.blade.php` lo trata como opcional). Si se busca paridad visual con el grupo `inicial`, se reutilizan sus mismos paths; no se inventan iconos nuevos.

### 10.3 Administración de usuarios

Además de §4.3:

- En la tabla de usuarios, el badge `Seguim.` debe ser visible para `isInicialManager()`.
- La condición de “Usuario Estándar” incluye el nuevo flag.
- El filtro `filter_role = 'inicial_manager'` debe funcionar aunque el usuario no sea `is_profesor`.
- El texto de ayuda del checkbox: “Acceso de consulta a todos los documentos de los docentes de Inicial; solo registra la revisión”.

### 10.4 `config/inicial.php`

Añadir a `perspectivas`:

```php
'seguimiento' => 'isInicialManager',
```

Es documentación de configuración; no hay código que lo consuma todavía. Sirve para que la quinta perspectiva quede declarada junto a las otras cuatro.

---

## 11. Pruebas

Archivo nuevo: `tests/Feature/Inicial/SeguimientoInicialTest.php`
Grupo: `@group inicial`, `@group inicial-seguimiento`.
Base: `DatabaseTransactions` (mismo patrón que `PerspectivasInicialTest`).

### 11.1 Fixtures

Extraer de `PerspectivasInicialTest` un trait reutilizable, por ejemplo `tests/Feature/Inicial/Concerns/CreaDocumentosInicial.php`, con:

- `makeDocente(bool $isAdmin = false): array` (usuario, profesor, pevaluación, grado, sección, lapso).
- `makeManager(): User` (usuario con `is_inicial_manager = true`, sin `is_inicial`).
- `makeUsuario(string $flag): User`.
- `planAttributes(int $profesorId, int $gradoId, int $seccionId): array`.
- `saltarSiFaltaLaMigracion(): void` (columna `is_inicial_manager`).
- `seccionActiva(int $gradoId): int`.

Si no se quiere refactorizar el test existente, duplicar solo los helpers mínimos en el nuevo archivo. La extracción es preferible para no mantener dos fixtures divergentes.

### 11.2 Casos de acceso

| # | Caso | Resultado |
|---|---|---|
| 1 | Invitado a `inicials.seguimiento.eiplanningwks.index` | 302 a login |
| 2 | Docente `is_inicial` sin manager | 403 |
| 3 | Admin sin manager | 403 |
| 4 | Manager | 200 |
| 5 | Manager + `is_profesor` | 200 y layout `seguimiento_inicial` |
| 6 | Manager + `is_inicial` (data heredada) | 200; la UI de admin bloquea combinarlos |
| 7 | Manager abre `show` y `format` de los 6 documentos | 200 |
| 8 | Docente/admin abren `show` o `format` | 403 |

### 11.3 Casos de datos y filtros

| # | Caso | Resultado |
|---|---|---|
| 9 | Dos docentes con un plan cada uno | El índice muestra ambos |
| 10 | Filtro `docente` | Solo los registros de ese docente |
| 11 | Filtro `nivel` | Solo los del nivel; la UI dice “Nivel” |
| 12 | Filtro `nivel` + `seccion` | Sección dependiente del nivel |
| 13 | Filtro `desde`/`hasta` | Solo documentos que solapan el rango |
| 14 | Filtro `lapso` en evaluación | Solo el lapso elegido |
| 15 | Filtro `lapso` en semanal | La UI no lo ofrece; el servicio lo ignora |
| 16 | Búsqueda por diagnóstico | Devuelve el plan correspondiente |
| 17 | Búsqueda por docente en informe final | Devuelve los informes del docente |
| 18 | Estado `sin_revision` / `revisado` | Coincide con `observacion`/`recomendacion` |
| 19 | Estado `completo` en semanal | Solo planes con 50 estrategias no vacías |
| 20 | Paginación | 15 por página, conserva filtros |
| 21 | Pestaña `eiprojectks` | Solo carga proyectos, no los otros cinco |

### 11.4 Casos de lectura

| # | Caso | Resultado |
|---|---|---|
| 22 | `Route::getRoutes()` filtra `inicials.seguimiento.*` | Todos los métodos son `GET`/`HEAD` |
| 23 | `POST /app/inicials/seguimiento/eiplanningwks` | 405 (no existe ruta) |
| 24 | Reflexión sobre `SeguimientoInicialComponent` | No existen `save`, `update`, `delete`, `create`, `store`, `destroy`; sí existe `guardarRevision` |
| 25 | El HTML del índice (sin modal abierto) | No contiene `wire:model` sobre `observacion`/`recomendacion` |
| 26 | El HTML del detalle | No contiene “Editar”, “Eliminar”, “Clonar”, “Nuevo” |

La prueba 24 es simbólica pero barata; la 22 es la garantía real de que no hay rutas de escritura.

### 11.5 Casos de revisión (única escritura)

| # | Caso | Resultado |
|---|---|---|
| 27 | Manager abre «Ver» → «Registrar revisión» en plan semanal | El formulario aparece con `wire:model="revision"` y «Guardar» |
| 28 | Guarda una observación con espacios | Se guarda con `trim`; `diagnostico` y demás campos no cambian; aviso `Observación guardada.`; badge «Revisado» |
| 29 | Guarda una recomendación en `eievaluationks` | Escribe `recomendacion`, no `observacion`; aviso `Recomendación guardada.` |
| 30 | Texto de 2 caracteres | Error `min:5`; el formulario sigue abierto y la BD no cambia |
| 31 | Abre «Editar revisión» y cancela | El textarea precarga el texto actual; al cancelar se descarta y la BD conserva el original |
| 32 | Informe final → `abrirRevision()` | 404: no hay campo revisable ni formulario |

### 11.6 Casos de menú y administración

| # | Caso | Resultado |
|---|---|---|
| 33 | Manager | Ve el grupo “Seguimiento Inicial” y sus 7 ítems |
| 34 | Docente | No ve el grupo |
| 35 | Admin sin flag | No ve el grupo |
| 36 | `MenuBuilder::resolveLayoutForUser()` con manager | `seguimiento_inicial` |
| 37 | `subtitleForLayout('seguimiento_inicial')` | “Seguimiento · Educación Inicial” |
| 38 | `User::isInicialManager()` | `true` solo con el flag |
| 39 | `getRoleLabelAttribute()` | “Seguimiento Inicial” |
| 40 | `auditableAttributes()` | Contiene `is_inicial_manager` |
| 41 | Admin crea usuario manager | Se guarda el flag y aparece el badge |
| 42 | Admin edita usuario manager | El checkbox queda marcado |
| 43 | Admin filtra por `inicial_manager` | Solo managers |
| 44 | Admin intenta guardar `is_inicial` + `is_inicial_manager` | Error de validación |
| 45 | Admin filtra “Usuario Estándar” | No incluye managers |

### 11.7 Casos de regresión

| # | Caso | Resultado |
|---|---|---|
| 46 | `inicials.home` y CRUD docente | Siguen funcionando |
| 47 | `evaluacions.inicials.index`, `plannings.inicials.index`, `academicos.inicials.index` | Siguen funcionando |
| 48 | `document-show` de las tres perspectivas | Sigue mostrando su miga “Educación Inicial” y su nota de revisión original |
| 49 | Formatos de los seis documentos | Sin cambios |
| 50 | `MenuInicialTest` | Sigue verde |
| 51 | Suite `tests/Feature/Inicial` (por archivo, nunca la carpeta completa) | Verde |

### 11.8 Rendimiento

| # | Caso | Resultado |
|---|---|---|
| 52 | Índice con 15 filas | Menos de 30 consultas (medir con `DB::listen` o `DB::getQueryLog`) |
| 53 | Índice semanal | No carga `eiplanningwstrategies` completas; usa `withCount` |
| 54 | Detalle | Usa `RegistroInicial::buscar()` con relaciones completas |

No se hacen pruebas de carga en fase 1. El presupuesto de 30 consultas es una guía; si se supera, revisar primero `profesores()` (seis `distinct`) y luego `withCount`/`with`, antes de añadir caché.

### 11.9 Comandos

```bash
php artisan test tests/Feature/Inicial/SeguimientoInicialTest.php
php artisan test --filter=SeguimientoInicial
vendor/bin/pint --test
git diff --check
```

Nunca ejecutar la carpeta `tests/Feature/Inicial` completa sobre la base real: un test con `RefreshDatabase` haría `migrate:fresh` sobre `s2627`. Los tests del módulo usan `DatabaseTransactions` y los que no tienen factory se marcan `skipped`.

Si se toca `MenuBuilder` o `document-show`, ejecutar además:

```bash
php artisan test --filter=MenuInicial
php artisan test --filter=PerspectivasInicial
```

---

## 12. Rendimiento

### 12.1 Presupuesto de consultas

Para una página de 15 registros:

| Bloque | Consultas aproximadas |
|---|---|
| Paginador (incluye `count(*)`) | 2 |
| `with` de relaciones de listado | 1 por relación (5–6) |
| `withCount` | 1 por contador (1–3) |
| Catálogos de filtros | 4 (niveles, secciones, lapsos) + 7 (`profesores()`: flag + seis `distinct`) |
| KPI | 4–5 |
| **Total** | **~21–28** |

No se carga el detalle de estrategias/resúmenes en el listado. Esa es la diferencia principal con `RegistroInicial::documentos()`, que está pensado para tablas pequeñas de perspectivas y no para paginación.

### 12.2 Evitar N+1

- `with(RELACIONES_LISTADO)` cubre docente, nivel, sección, área y lapso.
- `withCount` cubre los contadores de tarjeta/tabla.
- `filaTabla()` lee los `*_count`, no las colecciones.
- El detalle sí usa `RegistroInicial::buscar()` porque necesita el documento completo.

### 12.3 Índices (fase 2, solo con aprobación)

La planificación semanal ya tiene:

- `eiplanningwks_profesor_finicial_index` (`profesor_id`, `finicial`)
- `eiplanningwks_grado_seccion_finicial_index` (`grado_id`, `seccion_id`, `finicial`)
- `eiplanningwks_semana_unique`

Para los otros documentos, antes de crear índices:

```sql
EXPLAIN SELECT ...
```

Candidatos, solo si el plan lo justifica:

```php
// Migración separada, NO incluir en la fase 1
Schema::table('eiplanningbwks', function (Blueprint $table) {
    $table->index(['profesor_id', 'finicial'], 'eiplanningbwks_profesor_finicial_index');
    $table->index(['grado_id', 'seccion_id', 'finicial'], 'eiplanningbwks_grado_seccion_finicial_index');
});
// Análogo para eiprojectks y eispecialks.
// eievaluationks: (profesor_id, lapso_id), (grado_id, seccion_id, lapso_id).
// eifinalks: (pevaluacion_id), (estudiant_id) si no existen ya por FK.
```

Cada índice es un cambio de base de datos: requiere aprobación explícita y una migración propia con `down()`.

### 12.4 Caché

No en fase 1. Si los KPI se vuelven costosos:

- Cachear `resumen()` 60 s con clave `inicial.seguimiento.resumen.{entidad}.{hash(filtros)}`.
- Invalidar con `updated_at` del documento o TTL corto.
- No cachear el listado: el manager espera ver el estado actual.

---

## 13. Plan de ejecución por fases

| Fase | Entregable | DoD |
|---|---|---|
| **F0 · Decisión** | Este documento revisado y aprobado; naming confirmado | ✅ Confirmado 2026-10-29: `is_inicial_manager`, sin atajo para admin, roles excluyentes (§17). Pendiente el visto bueno de alcance 4–7 |
| **F1 · Flag y acceso** | Migración, `User`, `IsInicialManager`, alias en `Kernel`, admin UI | Flag asignable; middleware probado; suite de usuarios verde |
| **F2 · Backend de lectura** | Rutas, controlador, servicio, `filaTabla`, `show`/`format`, hooks de `document-show` | Manager ve detalle/formato de los 6; docente/admin 403; regresión de perspectivas verde |
| **F3 · Interfaz** | Componente Livewire, vistas, parciales, filtros, KPI, tarjetas/tabla, modal y formulario de revisión | Índice funcional, filtros en URL, paginación, revisión `min:5`, prueba de consultas |
| **F4 · Navegación** | `MenuBuilder`, `config/menus.php`, layout, badges de admin, docs | Menú visible solo para manager; layout correcto; README y docs actualizados |
| **F5 · Rendimiento** | `EXPLAIN` y, si aplica, migración de índices | Aprobación explícita; migración separada; medición antes/después |
| **F6 · QA y despliegue** | Checklist, backup, migración, smoke test | Pruebas de Inicial por archivo verdes; `route:list` correcto; manager real ve datos |

### 13.1 Orden de PRs sugerido

1. **PR 1:** migración + `User` + middleware + `Kernel` + admin UI + pruebas. No hay rutas todavía; el flag se puede asignar sin exponer nada.
2. **PR 2:** rutas + controlador + servicio + `filaTabla` + `show`/`format` + hooks de `document-show` + pruebas de acceso.
3. **PR 3:** componente + vistas + parciales + pruebas de interfaz, revisión y no-escritura del resto de campos.
4. **PR 4:** menú/layout + badges/filtros de admin + documentación.
5. **PR 5 (opcional):** índices de fase 2, solo con aprobación.

Cada PR debe pasar `pint --test` y `git diff --check`. No se hace commit sin solicitud expresa.

---

## 14. Criterios de aceptación

1. Existe `users.is_inicial_manager` con `default false`; la migración tiene `down()`.
2. El admin puede asignar, quitar y filtrar el flag desde `/admin/users`.
3. Un usuario con el flag entra a `/app/inicials/seguimiento`.
4. Un docente `is_inicial` sin el flag recibe 403.
5. Un `is_admin` sin el flag recibe 403.
6. El manager ve registros de **todos** los docentes, no solo los suyos.
7. El índice ofrece los seis documentos con pestañas y menú propio.
8. Los filtros son: docente, nivel, sección, lapso (cuando aplica), fechas, estado y búsqueda.
9. Los filtros viajan en la URL y sobreviven a F5 y al cambio de pestaña.
10. La vista permite alternar tarjetas/tabla y pagina de 15 en 15.
11. La planificación semanal muestra estrategias, días, resúmenes y estado de revisión.
12. El detalle y el formato funcionan para los seis documentos.
13. No existe ninguna ruta de escritura bajo `inicials.seguimiento.*`; la revisión viaja por el endpoint de Livewire.
14. El componente no tiene métodos de CRUD (`save`, `update`, `delete`, `create`, `store`, `destroy`); su única persistencia es `guardarRevision()` sobre el campo revisable.
15. El manager registra `observacion`/`recomendacion` (`required|string|min:5`) desde el modal «Ver»; el informe final responde 404 y no muestra el botón.
16. El detalle del manager no enlaza a `inicials.home` (que le daría 403); enlaza a `inicials.seguimiento.index`.
17. El menú “Seguimiento Inicial” aparece solo para el flag y tiene estado activo correcto.
18. El layout `seguimiento_inicial` tiene subtítulo “Seguimiento · Educación Inicial”.
19. La UI dice **Nivel**, no “Grado”.
20. La combinación `is_inicial` + `is_inicial_manager` se rechaza en la UI de admin.
21. Las pruebas de Inicial (por archivo, nunca la carpeta completa) están verdes, incluidos los nuevos casos y la regresión de las cuatro perspectivas actuales.
22. `pint --test` y `git diff --check` pasan.
23. No se modificaron `.env`, assets compilados, formatos oficiales ni el CRUD docente.

---

## 15. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Se implementa el typo `is_incial_manager` | Deuda y confusión permanentes | Descartado en ADR-11.1 (decisión 2026-10-29); si reapareciera, renombrar global antes de F1 |
| El manager hereda el CRUD docente | Falla de separación de funciones | Middleware propio, componente propio, sin rutas de escritura, pruebas 22–32 |
| `is_admin` entra sin flag | Acceso no autorizado a datos pedagógicos | `IsInicialManager` sin herencia; prueba 3 y 8 |
| Fuga de datos entre docentes | Privacidad | El perfil es de seguimiento global por diseño; restringir el flag a personal autorizado; auditar asignaciones |
| N+1 o listado lento | Mala experiencia | `with` acotado, `withCount`, `filaTabla`, presupuesto de consultas |
| `document-show` se rompe para las otras perspectivas | Regresión | Variables opcionales + pruebas 46–48 |
| Conflictos de rutas | 404/403 inesperados | Prefijo distinto, `route:list`, prueba de rutas |
| Migración en producción | Bloqueo o pérdida del flag | `--pretend`, backup, ventana, `down()` documentado |
| El manager escribe más allá de la revisión | Pérdida de control sobre el documento | `guardarRevision()` solo toca el campo revisable; `abort_unless` de rol; pruebas 27–32 |
| Combinación de flags | Layout/permisos ambiguos | Validación en admin; auditoría SQL; orden de layout documentado |
| Índices prematuros | Bloqueos de tabla | Fase 2 separada, `EXPLAIN`, aprobación |

---

## 16. Rollback

### 16.1 Código

- Revertir los PRs en orden inverso (menú → interfaz → backend → flag).
- Si se revierte solo la interfaz, las rutas y el servicio pueden quedar sin uso; no afectan al docente.
- Si se revierte el middleware antes que las rutas, las rutas quedarían sin protección: revertir primero las rutas o desplegar todo el bloque junto.

### 16.2 Base de datos

```bash
php artisan migrate:rollback --step=1
```

Antes:

```sql
SELECT id, username, is_inicial_manager
FROM users
WHERE is_inicial_manager = 1;
```

El `down()` elimina la columna y el flag. No hay otras tablas ni datos afectados.

### 16.3 Configuración

- `config:clear`, `route:clear`, `view:clear` después de revertir.
- No hay variables de `.env` nuevas.

---

## 17. Decisiones del usuario

**Confirmadas el 2026-10-29:**

| # | Decisión | Resuelto |
|---|---|---|
| 1 | Nombre del flag | **`is_inicial_manager`** (typo corregido). No se crea `is_incial_manager`. |
| 2 | Acceso de `is_admin` | **Solo con el flag.** `IsInicialManager` no hereda `is_admin`. |
| 3 | Manager + docente en el mismo usuario | **Excluyentes.** La UI de admin rechaza ambos flags; el middleware, si existiera data heredada, da prioridad a manager. |

**Confirmadas el 2026-11-02:**

| # | Decisión | Resuelto |
|---|---|---|
| 8 | ¿El manager puede escribir la revisión? | **Sí, solo la revisión** (`observacion`/`recomendacion`, `min:5`) dentro del modal del listado, que se abre con «Ver» o con «Revisión» junto a «Imprimir». El resto del documento sigue de solo lectura (ADR-11.11). |

**Adoptadas según la recomendación del plan** (implementadas; no cambian la arquitectura):

| # | Decisión | Resuelto |
|---|---|---|
| 4 | ¿Los 6 documentos desde el inicio? | **Sí**, con fase 1 genérica y semanal rica |
| 5 | ¿El formato impreso lleva marca de “seguimiento”? | **No**, mismo papel oficial |
| 6 | ¿Quién asigna el flag? | **Solo admin** (`/admin/users`) |
| 7 | ¿Se añaden índices en fase 1? | **No**, fase 2 con `EXPLAIN` |

> Cualquier cambio en 1–3 obliga a revisar ADR-11.1, ADR-11.4 y §4.3/§5
> antes de escribir código. Las decisiones 4–7 no cambian la arquitectura,
> solo el alcance de la fase 1.

---

## 18. Anexo: mapa de archivos

### 18.1 Archivos nuevos

| Archivo | Tipo | Propósito |
|---|---|---|
| `database/migrations/2026_10_29_000001_add_is_inicial_manager_to_users_table.php` | Migración | Columna del flag |
| `app/Http/Middleware/IsInicialManager.php` | Middleware | Autorización del perfil |
| `app/Http/Controllers/Inicial/SeguimientoInicialController.php` | Controlador | Índice, detalle y formato heredado |
| `app/Services/Inicial/SeguimientoInicial.php` | Servicio | Consultas, filtros, estado, KPI |
| `app/Livewire/Inicial/SeguimientoInicialComponent.php` | Livewire | Listado interactivo con detalle y revisión |
| `resources/views/inicial/seguimiento/index.blade.php` | Vista | Shell con layout y encabezado |
| `resources/views/livewire/inicial/seguimiento/index.blade.php` | Vista | Raíz del componente |
| `resources/views/livewire/inicial/seguimiento/partials/kpis.blade.php` | Parcial | Tarjetas de resumen |
| `resources/views/livewire/inicial/seguimiento/partials/filters.blade.php` | Parcial | Filtros |
| `resources/views/livewire/inicial/seguimiento/partials/tabs.blade.php` | Parcial | Pestañas por documento |
| `resources/views/livewire/inicial/seguimiento/partials/grid.blade.php` | Parcial | Tarjetas |
| `resources/views/livewire/inicial/seguimiento/partials/table.blade.php` | Parcial | Tabla |
| `resources/views/livewire/inicial/seguimiento/partials/card-eiplanningwk.blade.php` | Parcial | Tarjeta semanal |
| `resources/views/livewire/inicial/seguimiento/partials/card-documento.blade.php` | Parcial | Tarjeta genérica |
| `resources/views/livewire/inicial/seguimiento/partials/detalle-modal.blade.php` | Parcial | Modal de detalle y formulario de revisión |
| `resources/views/inicial/seguimiento/partials/leyenda-estados.blade.php` | Parcial | Leyenda de estados (modal Alpine) |
| `resources/views/livewire/inicial/seguimiento/partials/empty.blade.php` | Parcial | Estado vacío |
| `tests/Feature/Inicial/SeguimientoInicialTest.php` | Prueba | Acceso, datos, filtros, revisión y no-escritura del resto |
| `tests/Feature/Inicial/Concerns/CreaDocumentosInicial.php` | Trait (opcional) | Fixtures compartidos |

### 18.2 Archivos modificados

| Archivo | Cambio |
|---|---|
| `app/Models/User.php` | Flag, cast, fillable, audit, `isInicialManager()`, etiqueta |
| `app/Http/Kernel.php` | Alias `isInicialManager` |
| `routes/app/inicials.php` | Grupo de rutas de seguimiento |
| `app/Services/MenuBuilder.php` | `$roleMeta`, layout, permiso |
| `config/menus.php` | Grupo `seguimiento_inicial`, layout |
| `config/inicial.php` | Perspectiva `seguimiento` |
| `app/Livewire/Admin/Users/IndexComponent.php` | Propiedad, reglas, filtro, guardado, reset |
| `resources/views/livewire/admin/users/index-component.blade.php` | Checkbox, badge, filtro |
| `app/Http/Controllers/Inicial/PerspectivaInicialController.php` | Hooks de migas/nota de revisión |
| `resources/views/inicial/shared/document-show.blade.php` | Variables opcionales de navegación/nota |
| `app/Services/Inicial/RegistroInicial.php` | `filaTabla()` |
| `blueprint/inicial/README.md` | Enlace al doc 11 y nota de la quinta perspectiva |
| `blueprint/inicial/07-perspectivas-roles.md` | Nota de la quinta perspectiva en cfla (opcional) |

### 18.3 Archivos que **no** se tocan

- `app/Livewire/Inicial/EiplanningwkComponent.php` y demás componentes CRUD.
- `app/Policies/*` del docente.
- `resources/views/livewire/inicial/**` del docente.
- `resources/views/inicial/*/format.blade.php` (formatos oficiales).
- `app/Services/Inicial/EducationStatsService.php` (se usa como referencia, no se modifica).
- `.env`, `public/build/**`, assets compilados.

### 18.4 Comandos de verificación final

```bash
php artisan route:list --name=inicials.seguimiento
php artisan test --filter=SeguimientoInicial
php artisan test tests/Feature/Inicial
vendor/bin/pint --test
git diff --check
```

---

## 19. Resumen ejecutivo

La quinta perspectiva es **barata en código** porque el módulo ya tiene el catálogo, los filtros, el detalle y los formatos. El trabajo real está en tres fronteras:

1. **Autorización:** un flag nuevo, un middleware propio y ningún atajo por `is_admin`.
2. **Frontera de escritura:** rutas GET, CRUD ausente y una única escritura acotada a la revisión (`observacion`/`recomendacion`, `min:5`), con pruebas que lo demuestren.
3. **Seguimiento:** consultas paginadas y acotadas, estado de completitud/revisión y una UI que se parezca al listado del docente sin heredar su CRUD.

Si se respetan las decisiones de este documento, el perfil se puede implementar en cuatro PRs pequeños, con la migración aislada y la suite de Inicial como red de seguridad.

