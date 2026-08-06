<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\Planning\FlowDiagramService;

class FlowDiagramController extends Controller
{
    /**
     * Servicio de diagramas de flujo (descubrimiento, metadatos, servido).
     */
    public function __construct(private readonly FlowDiagramService $diagramService)
    {
    }

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
     */
    public function show(string $diagram)
    {
        $file = $this->diagramService->resolveFile($diagram);

        if ($file === null) {
            abort(404);
        }

        return response()->file($file);
    }
}
