<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Inicial\AbstractInicialController;
use App\Models\app\Entity\Institucion;
use App\Models\app\Inicial\Eispecialk;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD y formato imprimible del: Plan Especial (`eispecialks`).
 *
 * Controlador delgado: `index()` devuelve la vista que embebe
 * `App\Livewire\Inicial\EispecialkComponent`, `format()` la vista imprimible.
 * El CRUD real lo hace el componente Livewire, no el controlador.
 *
 * Rutas: `inicials.eispecialks.*` (ver routes/app/inicials.php, F1).
 * Acceso: `auth` + `isInicial` en el grupo de rutas. Cualquier acción no
 * implementada cae en el `__call` de {@see AbstractInicialController} → 501.
 *
 * Modelo: `App\Models\app\Inicial\Eispecialk`
 *
 * Blueprint: blueprint/inicial · fase F3 (CRUD docente resto).
 */
class EispecialkController extends AbstractInicialController
{
    /**
     * Listado con filtros + CRUD. El componente es el que lista.
     */
    public function index()
    {
        return view('inicial.eispecialk.index');
    }

    /**
     * Alta desde el botón "Nuevo plan especial" (misma pantalla de listado, el
     * modal lo abre el propio componente).
     */
    public function create()
    {
        return view('inicial.eispecialk.index');
    }

    public function store()
    {
        return $this->index();
    }

    public function show(Eispecialk $eispecialk)
    {
        return view('inicial.eispecialk.index');
    }

    public function edit(Eispecialk $eispecialk)
    {
        return view('inicial.eispecialk.index');
    }

    public function update(Eispecialk $eispecialk)
    {
        return $this->index();
    }

    public function destroy(Eispecialk $eispecialk)
    {
        return $this->index();
    }

    /**
     * Formato imprimible del plan especial.
     *
     * Tres bloques: cabecera · tabla resumen por actividad · rejilla de
     * estrategias. Sin el bloque de revisión del proyecto de aula, que es lo
     * único que diferencia aquel formato de este.
     *
     * Mismas dos correcciones que en los demás documentos:
     *  · el alcance del docente se aplica EN LA QUERY → 404 en vez de un 403
     *    que confirmaría que el id existe (el legacy hacía `findOrFail($id)` y
     *    cualquiera podía imprimir el plan de otro docente);
     *  · los textos se imprimen con `nl2br(e(...))` en lugar del helper
     *    `as_replace()` + `{!! !!}` del legacy, que no existe en cfla e
     *    interpretaba el contenido del docente como HTML crudo (XSS).
     */
    public function format(Eispecialk $eispecialk)
    {
        $profesor = Auth::user()->profesor;

        // El binding implícito de arriba solo resuelve el id.
        if ($profesor && ! Auth::user()->is_admin) {
            abort_unless(
                Eispecialk::whereKey($eispecialk->id)
                    ->where('profesor_id', $profesor->id)
                    ->exists(),
                404,
                'Este plan no existe o pertenece a otro docente.'
            );
        }

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $pescolar = $institucion?->pescolar;
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');

        return view('inicial.eispecialk.format', compact(
            'profesor',
            'eispecialk',
            'institucion',
            'pescolar',
            'fecha'
        ));
    }
}
