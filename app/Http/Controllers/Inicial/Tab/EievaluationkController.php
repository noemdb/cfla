<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Inicial\AbstractInicialController;
use App\Models\app\Entity\Institucion;
use App\Models\app\Inicial\Eievaluationk;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD y formato imprimible del: Plan de Evaluación (`eievaluationks`).
 *
 * Controlador delgado: `index()` devuelve la vista que embebe
 * `App\Livewire\Inicial\EievaluationkComponent`, `format()` la vista imprimible.
 * El CRUD real lo hace el componente Livewire, no el controlador.
 *
 * Rutas: `inicials.eievaluationks.*` (ver routes/app/inicials.php, F1).
 * Acceso: `auth` + `isInicial` en el grupo de rutas. Cualquier acción no
 * implementada cae en el `__call` de {@see AbstractInicialController} → 501.
 *
 * Modelo: `App\Models\app\Inicial\Eievaluationk`
 *
 * Blueprint: blueprint/inicial · fase F3 (CRUD docente resto).
 */
class EievaluationkController extends AbstractInicialController
{
    /**
     * Listado con filtros + CRUD. El componente es el que lista.
     */
    public function index()
    {
        return view('inicial.eievaluationk.index');
    }

    /**
     * Alta desde el botón "Nuevo plan de evaluación" (misma pantalla de listado,
     * el modal lo abre el propio componente).
     */
    public function create()
    {
        return view('inicial.eievaluationk.index');
    }

    public function store()
    {
        return $this->index();
    }

    public function show(Eievaluationk $eievaluationk)
    {
        return view('inicial.eievaluationk.index');
    }

    public function edit(Eievaluationk $eievaluationk)
    {
        return view('inicial.eievaluationk.index');
    }

    public function update(Eievaluationk $eievaluationk)
    {
        return $this->index();
    }

    public function destroy(Eievaluationk $eievaluationk)
    {
        return $this->index();
    }

    /**
     * Formato imprimible del plan de evaluación: UNA HOJA POR ÁREA.
     *
     * A diferencia de los otros formatos del módulo (una hoja por bloque), aquí
     * el legacy imprimía una página completa por área de aprendizaje, con su
     * membrete y su tabla de posiciones. Se mantiene: es lo que permite imprimir
     * y archivar cada área por separado.
     *
     * Dos correcciones deliberadas frente al legacy:
     *  · el alcance del docente se aplica EN LA QUERY → 404 en vez de un 403 que
     *    confirmaría que el id existe (el legacy hacía `findOrFail($id)`);
     *  · los textos se imprimen con `nl2br(e(...))` en lugar del helper
     *    `as_replace()` + `{!! !!}` del legacy, que no existe en cfla e
     *    interpretaba el contenido del docente como HTML crudo (XSS).
     */
    public function format(Eievaluationk $eievaluationk)
    {
        $profesor = Auth::user()->profesor;

        // El binding implícito de arriba solo resuelve el id.
        if ($profesor && ! Auth::user()->is_admin) {
            abort_unless(
                Eievaluationk::whereKey($eievaluationk->id)
                    ->where('profesor_id', $profesor->id)
                    ->exists(),
                404,
                'Este plan no existe o pertenece a otro docente.'
            );
        }

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $pescolar = $institucion?->pescolar;
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');

        return view('inicial.eievaluationk.format', compact(
            'profesor',
            'eievaluationk',
            'institucion',
            'pescolar',
            'fecha'
        ));
    }
}
