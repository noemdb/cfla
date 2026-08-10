<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Entity\Institucion;
use App\Services\Leadership\LeadershipService;
use App\Services\Lms\CoordinacionScopeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ActivityPdfController extends Controller
{
    /**
     * Formato completo (9 columnas): todas las actividades de un plan de evaluación.
     */
    public function format(Request $request, $id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'activities' => fn ($q) => $q->orderBy('finicial'),
            'activities.achievements',
        ])->findOrFail($id);

        $this->assertPevaluacionScope($request, $pevaluacion->id);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.planning.activities.format', [
            'pevaluacion' => $pevaluacion,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');
        $this->optimizePdf($pdf);

        return $pdf->stream("plan-actividades-{$id}.pdf");
    }

    /**
     * Resumen ejecutivo (6 columnas): solo actividades con descripción evaluativa.
     */
    public function resume(Request $request, $id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'activities' => fn ($q) => $q->whereNotNull('description')->orderBy('finicial'),
            'activities.achievements',
        ])->findOrFail($id);

        $this->assertPevaluacionScope($request, $pevaluacion->id);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.planning.activities.resume', [
            'pevaluacion' => $pevaluacion,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');
        $this->optimizePdf($pdf);

        return $pdf->stream("resumen-actividades-{$id}.pdf");
    }

    /**
     * Guarda de autorización por scope para los módulos con alcance acotado.
     *
     * El controlador es compartido entre cuatro módulos read-only y deduce el
     * scope del nombre de ruta (patrón ADR-006):
     *   - Dirección y Planificación supervisan TODA la institución (sin scope).
     *   - Liderazgo: solo pevaluacions de sus áreas asignadas
     *     (LeadershipService::scopePevaluacions).
     *   - Coordinación: solo pevaluacions de sus peducativos gestionados
     *     (CoordinacionScopeService::pevaluacionIsInScope).
     *
     * Lo que antes era un IDOR: sin esta guarda, un coordinador o jefe de área
     * podía descargar el PDF de cualquier pevaluacion abriendo la URL con el
     * id ajeno (routes coordinacion/leadership apuntan a este controlador).
     */
    private function assertPevaluacionScope(Request $request, int $pevaluacionId): void
    {
        $routeName = $request->route()?->getName() ?? '';

        if (str_contains($routeName, 'leadership')) {
            $inScope = app(LeadershipService::class, ['user' => $request->user()])
                ->scopePevaluacions(Pevaluacion::query()->whereKey($pevaluacionId))
                ->exists();

            if (! $inScope) {
                abort(403, 'No tienes permiso para ver actividades fuera de tus áreas asignadas.');
            }

            return;
        }

        if (str_contains($routeName, 'coordinacion')) {
            if (! app(CoordinacionScopeService::class, ['user' => $request->user()])
                ->pevaluacionIsInScope($pevaluacionId)) {
                abort(403, 'No tienes permiso para ver actividades fuera de tus programas educativos.');
            }
        }
    }

    /**
     * Aplica optimizaciones al PDF: subsetting de fuentes, DPI reducido.
     */
    private function optimizePdf($pdf): void
    {
        $pdf->setOption('enable_font_subsetting', true);
        $pdf->setOption('dpi', 72);
        $pdf->setOption('default_font', 'Helvetica');
    }
}
