<?php

namespace App\Services;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsAdminOrDiagnostic;
use App\Http\Middleware\IsCoordinacion;
use App\Http\Middleware\IsDiagnostic;
use App\Http\Middleware\IsDirector;
use App\Http\Middleware\IsLeadership;
use App\Http\Middleware\IsPlanner;
use App\Http\Middleware\IsProfesor;
use App\Http\Middleware\IsStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Resuelve el destino de una notificación según el rol del usuario
 * (blueprint/notifications, hallazgo N3).
 *
 * Dos familias de notificación se dirigen por rol, porque cada rol tiene su
 * propio listado y el mismo dato se ve distinto en cada uno:
 *
 * - `lesson_scheduled`: la URL almacenada apunta al monitor de planificación,
 *   pero coordinación, liderazgo y dirección tienen sus propios listados.
 * - `activity_created`: se almacena con URL neutra y cada rol va al suyo
 *   (jefatura con scope por áreas, planificación global, coordinación en
 *   ámbito, dirección y profesorado(read-only)).
 *
 * Para el resto se respeta la URL almacenada (`url`, o `action_url` de las
 * filas históricas), con el índice de notificaciones como último recurso.
 *
 * Orden de prioridad de roles (el primero que el usuario tenga gana):
 * is_admin → is_planner → is_director → is_diagnostic → is_coordinacion →
 * is_leadership → is_profesor → is_student.
 *
 * Los roles se leen del atributo CRUD, no de los accesores: `is_planner`,
 * `is_leadership` e `is_director` tienen accesores que además incluyen
 * `is_admin`, y usarlos rompería el orden (un admin+planner se iría al
 * destino de liderazgo en lugar del de planificación).
 */
class NotificationTargetResolver
{
    /**
     * @var array<int, string>
     */
    public const ROLE_PRIORITY = [
        'is_admin',
        'is_planner',
        'is_director',
        'is_diagnostic',
        'is_coordinacion',
        'is_leadership',
        'is_profesor',
        'is_student',
    ];

    /**
     * Destino por tipo de notificación y rol: [route, parámetros].
     * `null` = ese rol no tiene pantalla propia para ese tipo, así que se
     * sigue al siguiente rol de la lista; si ninguno encaja, se usa la URL
     * almacenada en la notificación.
     *
     * `is_admin` y `is_diagnostic` apuntan al módulo de planificación porque
     * el middleware `IsPlanner` los acepta (admin || planner || diagnostic).
     *
     * Un parámetro con el prefijo `@` se resuelve contra el payload de la
     * notificación (`@activity_id` → `$data['activity_id']`): hace falta para
     * las rutas con parámetro, como la previsualización de una lección. Si el
     * payload no trae el valor, ese destino se descarta y se prueba el
     * siguiente rol.
     *
     * @var array<string, array<string, array{0: string, 1: array<string, mixed>}|null>>
     */
    private const DESTINATIONS = [
        'lesson_scheduled' => [
            'is_admin' => ['app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']],
            'is_planner' => ['app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']],
            'is_director' => ['app.director.lessons', []],
            'is_diagnostic' => ['app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']],
            'is_coordinacion' => ['app.coordinacion.lessons', []],
            'is_leadership' => ['app.leadership.lessons', []],
            // El profesorado no tiene un listado de lecciones que aprobar.
            'is_profesor' => null,
            'is_student' => ['student.lms.lessons', []],
        ],
        'activity_created' => [
            'is_admin' => ['app.planning.activities.index', []],
            'is_planner' => ['app.planning.activities.index', []],
            'is_director' => ['app.director.activities', []],
            'is_diagnostic' => ['app.planning.activities.index', []],
            'is_coordinacion' => ['app.coordinacion.activities', []],
            'is_leadership' => ['app.leadership.activities', []],
            'is_profesor' => ['app.profesors.activities.index', []],
            // El alumnado no gestiona actividades.
            'is_student' => null,
        ],
        // Ciclo de publicación de la lección: cada rol va a donde puede ver el
        // resultado. Planificación y jefatura a la previsualización de la
        // lección, el profesorado a su editor, el resto a su listado de
        // lecciones.
        'lms_activity_published' => [
            'is_admin' => ['app.planning.lms.preview', ['activity' => '@activity_id']],
            'is_planner' => ['app.planning.lms.preview', ['activity' => '@activity_id']],
            'is_director' => ['app.director.lessons', []],
            'is_diagnostic' => ['app.planning.lms.preview', ['activity' => '@activity_id']],
            'is_coordinacion' => ['app.coordinacion.lessons', []],
            'is_leadership' => ['app.leadership.lms.preview', ['activity' => '@activity_id']],
            'is_profesor' => ['app.profesors.lms.editor', ['activity' => '@activity_id']],
            'is_student' => null,
        ],
        // Al eliminarse la publicación ya no hay nada que previsualizar: cada
        // rol va a su listado de lecciones/actividades.
        'lms_publication_deleted' => [
            'is_admin' => ['app.planning.lms.monitor', []],
            'is_planner' => ['app.planning.lms.monitor', []],
            'is_director' => ['app.director.lessons', []],
            'is_diagnostic' => ['app.planning.lms.monitor', []],
            'is_coordinacion' => ['app.coordinacion.lessons', []],
            'is_leadership' => ['app.leadership.lessons', []],
            'is_profesor' => ['app.profesors.activities.index', []],
            'is_student' => null,
        ],
    ];

    public function resolveFor(User $user, array $data): string
    {
        // URL almacenada: `url` en las actuales, `action_url` en las
        // históricas (TimetableChanged/SubstituteAssigned y filas previas).
        $stored = $data['url'] ?? $data['action_url'] ?? null;

        // `type` es la clave canónica; `event_type` la de las históricas.
        $type = $data['type'] ?? $data['event_type'] ?? null;

        if (is_string($type) && isset(self::DESTINATIONS[$type])) {
            foreach (self::ROLE_PRIORITY as $flag) {
                $destination = self::DESTINATIONS[$type][$flag] ?? null;

                if ($destination === null || ! $this->hasRole($user, $flag)) {
                    continue;
                }

                $params = $this->resolveParams($destination[1], $data);

                // Parámetro obligatorio que el payload no trae (p. ej. una fila
                // antigua sin activity_id): este destino no sirve, se prueba el
                // siguiente rol.
                if ($params === null) {
                    continue;
                }

                $url = route($destination[0], $params);

                // El enlace solo se usa si el usuario puede abrirlo: si perdió
                // el rol entre la emisión y el clic, un 403 no ayuda a nadie.
                if ($this->canAccess($user, $url)) {
                    return $url;
                }
            }
        }

        // Respaldo: la URL almacenada, si el usuario puede abrirla. Si no (o no
        // había), el índice de notificaciones, que es accesible para cualquiera
        // autenticado.
        if (is_string($stored) && $stored !== '' && $this->canAccess($user, $stored)) {
            return $stored;
        }

        return route('app.notifications.index');
    }

    /**
     * Resuelve los parámetros de la ruta: un valor `@clave` se toma del
     * payload de la notificación. `null` si falta ese dato.
     *
     * @param  array<string, mixed>  $params
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    private function resolveParams(array $params, array $data): ?array
    {
        $resolved = [];

        foreach ($params as $key => $value) {
            if (is_string($value) && str_starts_with($value, '@')) {
                $value = $data[substr($value, 1)] ?? null;

                if ($value === null || $value === '') {
                    return null;
                }
            }

            $resolved[$key] = $value;
        }

        return $resolved;
    }

    /**
     * Comprueba que el usuario pasaría los middlewares de rol de la ruta del
     * enlace, sin ejecutarla: `Route::match()` resuelve la ruta y
     * `gatherMiddleware()` devuelve las clases (los alias ya están resueltos).
     *
     * Se apoya en que `admin` es superusuario y atraviesa los módulos de rol.
     * `IsDiagnostic` es la excepción (no contempla admin) y se replica tal cual:
     * el chequeo nunca debe ser más permisivo que el middleware, para no
     * entregar un enlace que acabaría en 403.
     *
     * Rutas no registradas (URLs antiguas de otra instalación) o con
     * middlewares que no son de rol → se consideran accesibles: no se filtran
     * avisos por un enlace que no somos capaces de interpretar.
     */
    private function canAccess(User $user, string $url): bool
    {
        $path = $this->pathOf($url);

        if ($path === null) {
            return true;
        }

        foreach ($this->roleRequirements($path) as $alternatives) {
            if (! $this->satisfies($user, $alternatives)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Requisitos de rol de la ruta que corresponde a una URL, o lista vacía si
     * no se puede resolver. Cada requisito es el conjunto de flags que el
     * middleware acepta (OR). Memoizado por path: la campana resuelve varios
     * enlaces en cada render.
     *
     * @return array<int, array<int, string>>
     */
    private function roleRequirements(string $path): array
    {
        if (array_key_exists($path, $this->requirementCache)) {
            return $this->requirementCache[$path];
        }

        $requirements = [];
        $roles = $this->middlewareRoles();

        try {
            /** @var RoutingRoute $route */
            $route = Route::getRoutes()->match(Request::create($path, 'GET'));

            foreach ($route->gatherMiddleware() as $middleware) {
                // Puede haber closures u objetos (no son middlewares de rol).
                if (is_string($middleware) && isset($roles[$middleware])) {
                    $requirements[] = $roles[$middleware];
                }
            }
        } catch (NotFoundHttpException|MethodNotAllowedHttpException) {
            // Ruta desconocida: sin requisitos que evaluar.
        }

        return $this->requirementCache[$path] = $requirements;
    }

    /**
     * `Route::gatherMiddleware()` devuelve los ALIAS ('isLeadership'), no las
     * clases, así que se indexa el mapa por ambos: así el chequeo funciona
     * tanto si la ruta declara el alias como si declara la clase.
     *
     * @return array<string, array<int, string>>
     */
    private function middlewareRoles(): array
    {
        if ($this->middlewareRoleCache !== null) {
            return $this->middlewareRoleCache;
        }

        $roles = self::MIDDLEWARE_ROLES;

        foreach (app('router')->getMiddleware() as $alias => $class) {
            if (isset($roles[$class])) {
                $roles[$alias] = $roles[$class];
            }
        }

        return $this->middlewareRoleCache = $roles;
    }

    /**
     * Path de una URL (o de una ruta relativa interna). `null` si no es
     * interpretable.
     */
    private function pathOf(string $url): ?string
    {
        if (str_starts_with($url, '/')) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : null;
    }

    /**
     * ¿El usuario cumple el requisito? Basta con cumplir UNO de los flags
     * aceptados por el middleware.
     *
     * @param  array<int, string>  $alternatives
     */
    private function satisfies(User $user, array $alternatives): bool
    {
        foreach ($alternatives as $flag) {
            if ($this->hasRole($user, $flag)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Middleware de rol → flags que acepta, replicando literalmente lo que
     * hace cada middleware (`App\Http\Middleware\*`). Se leen del atributo
     * crudo, no de los accesores de `User`, para no sumar `is_admin` dos veces.
     *
     * @var array<class-string, array<int, string>>
     */
    private const MIDDLEWARE_ROLES = [
        IsAdmin::class => ['is_admin'],
        IsAdminOrDiagnostic::class => ['is_admin', 'is_diagnostic'],
        // No contempla al administrador a propósito: se replica tal cual para
        // no entregar un enlace que el middleware acabaría rechazando con 403.
        IsDiagnostic::class => ['is_diagnostic'],
        IsPlanner::class => ['is_admin', 'is_planner', 'is_diagnostic'],
        IsLeadership::class => ['is_admin', 'is_leadership'],
        IsDirector::class => ['is_admin', 'is_director'],
        IsCoordinacion::class => ['is_admin', 'is_coordinacion'],
        IsProfesor::class => ['is_admin', 'is_profesor'],
        IsStudent::class => ['is_admin', 'is_student'],
    ];

    /**
     * Requisitos ya calculados por path.
     *
     * @var array<string, array<int, array<int, string>>>
     */
    private array $requirementCache = [];

    /**
     * Mapa middleware (alias y clase) → flags aceptados.
     *
     * @var array<string, array<int, string>>|null
     */
    private ?array $middlewareRoleCache = null;

    /**
     * Lee el flag crudo del modelo: los accesores de is_planner/is_leadership/
     * is_director suman `is_admin` y falsearían la prioridad.
     */
    private function hasRole(User $user, string $flag): bool
    {
        return (bool) ($user->getAttributes()[$flag] ?? false);
    }
}
