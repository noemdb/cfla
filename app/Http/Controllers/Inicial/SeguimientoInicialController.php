<?php

namespace App\Http\Controllers\Inicial;

use App\Services\Inicial\RegistroInicial;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Perspectiva de SEGUIMIENTO de Educación Inicial (solo lectura).
 *
 * Blueprint: blueprint/inicial · doc 11 · ADR-11.2 / ADR-11.6.
 *
 * `show()` y `format()` se heredan de PerspectivaInicialController: son los
 * mismos documentos y el mismo papel que ven las otras perspectivas. Lo único
 * que cambia es el prefijo de rutas y el marco de navegación.
 *
 * No hay ningún método de escritura, y las rutas del grupo solo declaran GET.
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
     * El detalle base (`inicial.shared.document-show`) los usa para que un
     * manager no acabe en la portada del docente, que le daría 403.
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
        return 'La revisión la realiza la Coordinación de Evaluación. Este perfil es de solo lectura.';
    }
}
