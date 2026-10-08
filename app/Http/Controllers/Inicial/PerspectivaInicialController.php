<?php

namespace App\Http\Controllers\Inicial;

use App\Models\app\Entity\Institucion;
use App\Services\Inicial\RegistroInicial;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Base de las tres perspectivas de revisión del módulo (Evaluación,
 * Planificación, Académico).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * QUÉ RESUELVE
 * ─────────────────────────────────────────────────────────────────────────────
 * Las tres exponen las mismas acciones sobre los mismos 6 documentos:
 * `index()` (cada una con su propia vista y sus filtros), `show()` (detalle de
 * solo lectura) y `format()` (impresión). El legacy resolvió `show` y `format`
 * tres veces, documento por documento: 630 + 114 + 69 líneas, con el bug ya
 * documentado de los botones quincenales que apuntaban al formato semanal.
 *
 * Aquí ambas acciones se escriben UNA vez y el catálogo
 * {@see RegistroInicial} aporta el modelo, las relaciones y la vista.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * DIFERENCIA ENTRE LAS TRES: SOLO `index()`
 * ─────────────────────────────────────────────────────────────────────────────
 *  · Evaluación: puede escribir `observacion`/`recomendacion` (min:5) → tiene
 *    componentes Livewire y vista propia ({@see \App\Http\Controllers\Evaluacion\InicialController}).
 *  · Planificación y Académico: solo lectura, renderizado en servidor.
 *
 * El detalle y la impresión NO se duplican: son el mismo papel y los mismos
 * datos, y quien los pide solo tiene que estar autorizado.
 */
abstract class PerspectivaInicialController extends AbstractInicialController
{
    /**
     * Prefijo de nombres de ruta de la perspectiva, p. ej.
     * `evaluacions.inicials.` — para construir los enlaces del formato.
     */
    protected string $prefijoRutas = '';

    /**
     * Detalle de un documento, en solo lectura.
     *
     * Las perspectivas ven el MÓDULO COMPLETO (no filtran por docente), así que
     * aquí no hay comprobación de propiedad: la autorización es el middleware
     * del grupo de rutas (`isDiagnostic` / `isPlanner` / `isAdmin`).
     *
     * La entidad NO es un parámetro del método: las rutas se declaran en bucle
     * (`eiplanningwks/{id}`, `eiprojectks/{id}`…) y la entidad viaja como valor
     * por defecto (`->defaults('entidad', …)`), de donde se lee aquí. Así los
     * tres controladores de perspectiva comparten un único `show()` en vez de
     * 30 métodos repetidos.
     */
    public function show(int $id): View
    {
        $entidad = $this->entidadDeLaRuta();

        $documento = $this->documento($entidad, $id);

        return view('inicial.shared.document-show', [
            'entidad' => $entidad,
            'titulo' => RegistroInicial::titulo($entidad),
            'documento' => $documento,
            'detalle' => RegistroInicial::parcialDetalle($entidad),
            'rutaFormato' => $this->rutaFormato($entidad, $documento->id),
            'campoRevision' => RegistroInicial::campoRevision($entidad),
        ]);
    }

    /**
     * Formato imprimible de un documento.
     *
     * Reutiliza la MISMA vista que el docente (F4): es el mismo papel. Lo único
     * que cambia es quién lo pide, y eso lo resuelve el middleware.
     */
    public function format(int $id): View
    {
        $entidad = $this->entidadDeLaRuta();

        $documento = $this->documento($entidad, $id);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        return view(RegistroInicial::vistaFormato($entidad), [
            // Cada vista de formato (F4) recibe el documento con el nombre en
            // SINGULAR (`$eiplanningwk`), declarado en el catálogo.
            RegistroInicial::variable($entidad) => $documento,
            'profesor' => $this->profesorDe($documento),
            'institucion' => $institucion,
            'pescolar' => $institucion?->pescolar,
            'fecha' => now()->translatedFormat('j \d\e\ F \d\e\ Y'),
        ]);
    }

    /**
     * Entidad `ei*` de la ruta actual.
     *
     * Vacía si se invoca el controlador sin la ruta (por ejemplo en un test
     * unitario): {@see documento()} la convierte en 404 en vez de dejar que
     * reviente un TypeError.
     */
    protected function entidadDeLaRuta(): string
    {
        // `Illuminate\Routing\Controller` no expone `route()`: se lee de la
        // petición. Vacía si no hay ruta (invocación directa en un test).
        $entidad = request()->route('entidad');

        return is_string($entidad) ? $entidad : '';
    }

    /**
     * Busca el documento o aborta con 404 si la entidad no está en el catálogo.
     *
     * Entidad desconocida y documento inexistente dan el MISMO 404: la ruta no
     * debe revelar qué claves existen en el catálogo.
     */
    protected function documento(string $entidad, int $id)
    {
        abort_unless(RegistroInicial::existe($entidad), 404);

        return RegistroInicial::buscar($entidad, $id);
    }

    /** URL del formato para un documento, en la perspectiva actual. */
    protected function rutaFormato(string $entidad, int $id): string
    {
        return route($this->prefijoRutas.$entidad.'.format', $id);
    }

    /**
     * Docente responsable del documento, para el pie de los formatos.
     *
     * El informe final no tiene `profesor_id`: se hereda de la carga académica.
     */
    protected function profesorDe($documento)
    {
        if (isset($documento->profesor)) {
            return $documento->profesor;
        }

        return $documento->pevaluacion?->profesor ?? Auth::user()->profesor;
    }
}
