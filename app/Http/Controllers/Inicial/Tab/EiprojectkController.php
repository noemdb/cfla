<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Inicial\AbstractInicialController;
use App\Models\app\Entity\Institucion;
use App\Models\app\Inicial\Eiprojectk;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD y formato imprimible del: Proyecto de Aula (`eiprojectks`).
 *
 * Controlador delgado: `index()` devuelve la vista que embebe
 * `App\Livewire\Inicial\EiprojectkComponent`, `format()` la vista imprimible.
 * El CRUD real lo hace el componente Livewire, no el controlador.
 *
 * Rutas: `inicials.eiprojectks.*` (ver routes/app/inicials.php, F1).
 * Acceso: `auth` + `isInicial` en el grupo de rutas. Cualquier acción no
 * implementada cae en el `__call` de {@see AbstractInicialController} → 501.
 *
 * Modelo: `App\Models\app\Inicial\Eiprojectk`
 *
 * Blueprint: blueprint/inicial · fase F4.
 */
class EiprojectkController extends AbstractInicialController
{
    /**
     * Listado con filtros + CRUD. El componente es el que lista.
     */
    public function index()
    {
        return view('inicial.eiprojectk.index');
    }

    /**
     * Alta desde el botón "Nuevo proyecto" (misma pantalla de listado, el modal
     * lo abre el propio componente).
     */
    public function create()
    {
        return view('inicial.eiprojectk.index');
    }

    public function store()
    {
        return $this->index();
    }

    public function show(Eiprojectk $eiprojectk)
    {
        return view('inicial.eiprojectk.index');
    }

    public function edit(Eiprojectk $eiprojectk)
    {
        return view('inicial.eiprojectk.index');
    }

    public function update(Eiprojectk $eiprojectk)
    {
        return $this->index();
    }

    public function destroy(Eiprojectk $eiprojectk)
    {
        return $this->index();
    }

    /**
     * Formato imprimible del proyecto de aula.
     *
     * Cuatro bloques: cabecera · revisión · tabla resumen por área · rejilla de
     * estrategias. La revisión es lo que diferencia este formato de los de la
     * semanal y la quincenal.
     *
     * Mismas dos correcciones que en los otros documentos:
     *  · el alcance del docente se aplica EN LA QUERY → 404 en vez de un 403
     *    que confirmaría que el id existe (el legacy hacía `findOrFail($id)` y
     *    cualquiera podía imprimir el proyecto de otro docente);
     *  · los textos se imprimen con `nl2br(e(...))` en lugar del helper
     *    `as_replace()` + `{!! !!}` del legacy, que no existe en cfla e
     *    interpretaba el contenido del docente como HTML crudo (XSS).
     */
    public function format(Eiprojectk $eiprojectk)
    {
        $profesor = Auth::user()->profesor;

        // El binding implícito de arriba solo resuelve el id.
        if ($profesor && ! Auth::user()->is_admin) {
            abort_unless(
                Eiprojectk::whereKey($eiprojectk->id)
                    ->where('profesor_id', $profesor->id)
                    ->exists(),
                404,
                'Este proyecto no existe o pertenece a otro docente.'
            );
        }

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $pescolar = $institucion?->pescolar;
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');

        return view('inicial.eiprojectk.format', compact(
            'profesor',
            'eiprojectk',
            'institucion',
            'pescolar',
            'fecha'
        ));
    }
}
