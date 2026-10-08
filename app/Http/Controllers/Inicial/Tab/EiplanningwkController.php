<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Inicial\AbstractInicialController;
use App\Livewire\Inicial\EiplanningwkComponent;
use App\Models\app\Entity\Institucion;
use App\Models\app\Inicial\Eiplanningwk;
use Illuminate\Support\Facades\Auth;

/**
 * Planificación semanal de Educación Inicial (entidad P0).
 *
 * Controlador delgado, como en el legacy: `index()` devuelve la vista que
 * embebe el componente Livewire y `format()` la vista imprimible. El CRUD real
 * —validación, wizard de 50 celdas, resúmenes— vive en
 * {@see EiplanningwkComponent}, no aquí.
 *
 * Rutas: `inicials.eiplanningwks.*` (ver routes/app/inicials.php, F1).
 * Acceso: `auth` + `isInicial` en el grupo de rutas, y `isInicial` de nuevo en
 * el propio componente (defensa en profundidad). Cualquier acción no
 * implementada cae en el `__call` de {@see AbstractInicialController} → 501.
 */
class EiplanningwkController extends AbstractInicialController
{
    /**
     * Listado con filtros + CRUD. El componente es el que lista.
     */
    public function index()
    {
        return view('inicial.eiplanningwk.index');
    }

    /**
     * Alta desde el botón "Nueva planificación" (misma pantalla de listado, el
     * modal lo abre el propio componente).
     */
    public function create()
    {
        return view('inicial.eiplanningwk.index');
    }

    public function store()
    {
        return $this->index();
    }

    public function show(Eiplanningwk $eiplanningwk)
    {
        return view('inicial.eiplanningwk.index');
    }

    public function edit(Eiplanningwk $eiplanningwk)
    {
        return view('inicial.eiplanningwk.index');
    }

    public function update(Eiplanningwk $eiplanningwk)
    {
        return $this->index();
    }

    public function destroy(Eiplanningwk $eiplanningwk)
    {
        return $this->index();
    }

    /**
     * Formato imprimible del plan: cabecera, tabla resumen por área y rejilla
     * de estrategias 10 momentos × 5 días.
     *
     * Port del legacy `format($id, Request)` con dos cambios deliberados:
     *  · el plan se busca filtrando por `profesor_id` del usuario autenticado
     *    (el legacy hacía `findOrFail($id)` y cualquiera podía imprimir el plan
     *    de otro docente solo con conocer el id);
     *  · los textos se imprimen con `nl2br(e(...))` en lugar del helper
     *    `as_replace()` + `{!! !!}` del legacy, que no existe en cfla e
     *    interpretaba el contenido del docente como HTML crudo (XSS).
     */
    public function format(Eiplanningwk $eiplanningwk)
    {
        $profesor = Auth::user()->profesor;

        // El alcance del docente se aplica EN LA QUERY, no como chequeo
        // posterior: así el resultado es 404 ("no existe un plan tuyo con ese
        // id"), igual que en el listado, en vez de un 403 que confirmaría que
        // el id existe. El binding implícito de arriba solo resuelve el id.
        if ($profesor && ! Auth::user()->is_admin) {
            abort_unless(
                Eiplanningwk::whereKey($eiplanningwk->id)
                    ->where('profesor_id', $profesor->id)
                    ->exists(),
                404,
                'Este plan no existe o pertenece a otro docente.'
            );
        }

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $pescolar = $institucion?->pescolar;
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');

        return view('inicial.eiplanningwk.format', compact(
            'profesor',
            'eiplanningwk',
            'institucion',
            'pescolar',
            'fecha'
        ));
    }
}
