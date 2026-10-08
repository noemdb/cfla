<?php

namespace App\Http\Controllers\Inicial;

use App\Http\Controllers\Controller;

/**
 * Base de los controladores del módulo de Educación Inicial.
 *
 * Blueprint: blueprint/inicial · fases F1 (rutas) → F2/F3 (CRUD docente) →
 * F5 (perspectivas de solo lectura).
 *
 * ─────────────────────────────────────────────────────────────────
 * POR QUÉ EXISTE ESTA CLASE
 * ─────────────────────────────────────────────────────────────────
 * F1 entrega el mapa de rutas y el acceso, pero los controladores de verdad
 * llegan con F2/F3/F5. Sin embargo, `php artisan route:list` hace
 * `new ReflectionClass($route->getControllerClass())` sobre CADA ruta: si las
 * clases no existen, el comando revienta con `ReflectionException` y se cae
 * una herramienta base del proyecto para todo el equipo.
 *
 * Por eso los controladores existen desde F1 como esqueletos, y este `__call`
 * convierte cualquier acción todavía no implementada en un 501 explícito en
 * lugar de un 500 por "método inexistente".
 *
 * No hay enlace de navegación que apunte a estas rutas todavía, así que el
 * 501 no es alcanzable desde la UI: solo por URL directa.
 */
abstract class AbstractInicialController extends Controller
{
    /**
     * Acción aún no implementada (la implementa su fase).
     */
    public function __call($method, $parameters)
    {
        abort(501, sprintf(
            'La acción [%s] de %s todavía no está implementada. '
            .'Módulo de Educación Inicial — ver blueprint/inicial (fases F2, F3 y F5).',
            $method,
            static::class
        ));
    }
}
