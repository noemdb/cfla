<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Entity\Institucion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ActivityPdfController extends Controller
{
    /**
     * Formato completo (9 columnas): todas las actividades de un plan de evaluación.
     */
    public function format($id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'activities' => fn($q) => $q->orderBy('finicial'),
            'activities.achievements',
        ])->findOrFail($id);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.planning.activities.format', [
            'pevaluacion' => $pevaluacion,
            'institucion'  => $institucion,
            'fecha'        => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');
        $this->optimizePdf($pdf);

        return $pdf->stream("plan-actividades-{$id}.pdf");
    }

    /**
     * Resumen ejecutivo (6 columnas): solo actividades con descripción evaluativa.
     */
    public function resume($id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'activities' => fn($q) => $q->whereNotNull('description')->orderBy('finicial'),
            'activities.achievements',
        ])->findOrFail($id);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.planning.activities.resume', [
            'pevaluacion' => $pevaluacion,
            'institucion'  => $institucion,
            'fecha'        => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');
        $this->optimizePdf($pdf);

        return $pdf->stream("resumen-actividades-{$id}.pdf");
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
