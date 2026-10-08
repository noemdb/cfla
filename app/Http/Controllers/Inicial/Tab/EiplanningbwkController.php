<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Inicial\AbstractInicialController;
use App\Models\app\Entity\Institucion;
use App\Models\app\Inicial\Eiplanningbwk;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD y formato imprimible de: Planificación quincenal (`eiplanningbwks`).
 *
 * Controlador delgado: `index()` devuelve la vista que embebe
 * `App\Livewire\Inicial\EiplanningbwkComponent`, `format()` la vista imprimible.
 * El CRUD real lo hace el componente Livewire, no el controlador.
 *
 * Rutas: `inicials.eiplanningbwks.*` (ver routes/app/inicials.php, F1).
 * Acceso: `auth` + `isInicial` en el grupo de rutas.
 * Cualquier acción no implementada cae en el `__call` de
 * {@see AbstractInicialController} → 501.
 *
 * Modelo: `App\Models\app\Inicial\Eiplanningbwk`
 *
 * Blueprint: blueprint/inicial · fase F3.
 */
class EiplanningbwkController extends AbstractInicialController
{
    /**
     * Listado con filtros + CRUD. El componente es el que lista.
     */
    public function index()
    {
        return view('inicial.eiplanningbwk.index');
    }

    /**
     * Alta desde el botón "Nueva planificación" (misma pantalla de listado, el
     * modal lo abre el propio componente).
     */
    public function create()
    {
        return view('inicial.eiplanningbwk.index');
    }

    public function store()
    {
        return $this->index();
    }

    public function show(Eiplanningbwk $eiplanningbwk)
    {
        return view('inicial.eiplanningbwk.index');
    }

    public function edit(Eiplanningbwk $eiplanningbwk)
    {
        return view('inicial.eiplanningbwk.index');
    }

    public function update(Eiplanningbwk $eiplanningbwk)
    {
        return $this->index();
    }

    public function destroy(Eiplanningbwk $eiplanningbwk)
    {
        return $this->index();
    }

    /**
     * Formato imprimible del plan quincenal.
     *
     * Idéntico al de la semanal en estructura (cabecera · tabla resumen por área ·
     * rejilla de estrategias) y por las dos correcciones que se hicieron allí:
     *
     *  · el alcance del docente se aplica EN LA QUERY, no como chequeo
     *    posterior: el resultado es 404 ("no existe un plan tuyo con ese id") en
     *    vez de un 403 que confirmaría que el id existe. El legacy hacía
     *    `findOrFail($id)` y cualquiera podía imprimir el plan de otro docente
     *    solo con conocer el id;
     *  · los textos se imprimen con `nl2br(e(...))` en lugar del helper
     *    `as_replace()` + `{!! !!}` del legacy, que no existe en cfla e
     *    interpretaba el contenido del docente como HTML crudo (XSS).
     */
    public function format(Eiplanningbwk $eiplanningbwk)
    {
        $profesor = Auth::user()->profesor;

        // El binding implícito de arriba solo resuelve el id.
        if ($profesor && ! Auth::user()->is_admin) {
            abort_unless(
                Eiplanningbwk::whereKey($eiplanningbwk->id)
                    ->where('profesor_id', $profesor->id)
                    ->exists(),
                404,
                'Este plan no existe o pertenece a otro docente.'
            );
        }

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $pescolar = $institucion?->pescolar;
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');

        return view('inicial.eiplanningbwk.format', compact(
            'profesor',
            'eiplanningbwk',
            'institucion',
            'pescolar',
            'fecha'
        ));
    }
}
