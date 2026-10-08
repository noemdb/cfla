<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Inicial\AbstractInicialController;
use App\Models\app\Entity\Institucion;
use App\Models\app\Inicial\Eifinalk;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD y formato imprimible del: Informe Final por estudiante (`eifinalks`).
 *
 * Controlador delgado: `index()` devuelve la vista que embebe
 * `App\Livewire\Inicial\EifinalkComponent`, `format()` la vista imprimible.
 * El CRUD real lo hace el componente Livewire, no el controlador.
 *
 * Rutas: `inicials.eifinalks.*` (ver routes/app/inicials.php, F1).
 * Acceso: `auth` + `isInicial` en el grupo de rutas. Cualquier acción no
 * implementada cae en el `__call` de {@see AbstractInicialController} → 501.
 *
 * Modelo: `App\Models\app\Inicial\Eifinalk`
 *
 * Blueprint: blueprint/inicial · fase F3 (patrón A.6, el último documento).
 */
class EifinalkController extends AbstractInicialController
{
    /**
     * Listado de informes + pestaña de estudiantes. El componente es el que
     * lista.
     */
    public function index()
    {
        return view('inicial.eifinalk.index');
    }

    /**
     * Alta. El componente abre su propio modal, así que la pantalla es la misma
     * que el índice.
     */
    public function create()
    {
        return view('inicial.eifinalk.index');
    }

    public function store()
    {
        return $this->index();
    }

    public function show(Eifinalk $eifinalk)
    {
        return view('inicial.eifinalk.index');
    }

    public function edit(Eifinalk $eifinalk)
    {
        return view('inicial.eifinalk.index');
    }

    public function update(Eifinalk $eifinalk)
    {
        return $this->index();
    }

    public function destroy(Eifinalk $eifinalk)
    {
        return $this->index();
    }

    /**
     * Formato imprimible del informe final de UN estudiante.
     *
     * ─────────────────────────────────────────────────────────────────────────────
     * ALCANCE RESPECTO AL LEGACY
     * ─────────────────────────────────────────────────────────────────────────────
     * El legacy tenía además `printAllforLapso(Estudiant, Lapso)`: el boletín
     * COMPLETO del niño (todos sus informes del periodo, separados en oficial y
     * de componente). Eso no se porta aquí a propósito:
     *
     *  · necesita su propia ruta (`eifinalks/print-all-for-lapso`), que F1 no
     *    declara — F1 registró un único `ei*.{id}/format` por documento;
     *  · es una impresión AGREGADA de varias filas del pivote, no el formato de
     *    un documento: encaja con F4/F5 (formatos y perspectivas) y no con el
     *    CRUD del docente;
     *  · el separation oficial/componente es exactamente la lógica que la
     *    Coordinación necesita en la perspectiva de evaluación (F5).
     *
     * Lo que sí se mantiene es el alcance por docente, que el legacy no tenía:
     * hacía `findOrFail($id)` y cualquiera imprimía el informe de otro docente
     * —de otro niño— solo con conocer el id. Aquí se aplica EN LA QUERY, así que
     * la respuesta es 404 en vez de un 403 que confirmaría que el id existe.
     */
    public function format(Eifinalk $eifinalk)
    {
        $profesor = Auth::user()->profesor;

        // El binding implícito de arriba solo resuelve el id.
        if ($profesor && ! Auth::user()->is_admin) {
            abort_unless(
                Eifinalk::whereKey($eifinalk->id)
                    ->whereHas('pevaluacion', fn ($q) => $q->where('profesor_id', $profesor->id))
                    ->exists(),
                404,
                'Este informe no existe o pertenece a otro docente.'
            );
        }

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $pescolar = $institucion?->pescolar;
        $fecha = now()->translatedFormat('j \d\e\ F \d\e\ Y');

        return view('inicial.eifinalk.format', compact(
            'profesor',
            'eifinalk',
            'institucion',
            'pescolar',
            'fecha'
        ));
    }
}
