<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\FlowDiagramService;

class FlowDiagramController extends Controller
{
    /**
     * Servicio de diagramas de flujo (descubrimiento, metadatos, servido).
     */
    public function __construct(private readonly FlowDiagramService $diagramService) {}

    /**
     * Hub: lista los diagramas de flujo disponibles.
     *
     * Cada archivo `flujo{Studly}.html` del directorio se publica como
     * `/app/planning/diagram/flow/{slug}`, donde {slug} = kebab(Studly).
     */
    public function index()
    {
        $diagrams = $this->diagramService->list();

        return view('planning.flow', compact('diagrams'));
    }

    /**
     * Sirve una infografía de flujo por slug.
     *
     * Ej.: /diagram/flow/activity-lesson → docs/infografia/flujoActivityLesson.html
     *
     * Se sirve con cabeceras anti-caché: los diagramas son documentos vivos que
     * pueden actualizarse (p. ej. autor, fechas) y el navegador no debe
     * mostrar versiones obsoletas (response()->file() emite Last-Modified/ETag
     * que el navegador puede reutilizar).
     */
    public function show(string $diagram)
    {
        $file = $this->diagramService->resolveFile($diagram);

        if ($file === null) {
            abort(404);
        }

        return response()->file($file, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }
}
