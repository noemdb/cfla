<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\DidacticPlan;
use App\Models\app\Entity\Institucion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DidacticPlanPdfController extends Controller
{
    /**
     * Descarga el plan de clases aprobado en formato imprimible.
     */
    public function download(Request $request, DidacticPlan $didacticPlan)
    {
        abort_unless((bool) config('planning.didactic_plans_enabled'), 404);

        $this->authorize('downloadPdf', $didacticPlan);

        if (! $didacticPlan->isDownloadable()) {
            abort(403, 'El plan debe estar aprobado para generar el PDF oficial.');
        }

        $didacticPlan->load([
            'pestudio',
            'grado',
            'seccion.grado.pestudio',
            'lapso',
            'sessions.indicators',
            'indicators',
            'creator',
            'approver',
        ]);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.planning.didactic-plans.plan', [
            'plan' => $didacticPlan,
            'institucion' => $institucion,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'portrait');
        $this->optimizePdf($pdf);

        $seccion = $didacticPlan->seccion?->name ?? 'S'.$didacticPlan->seccion_id;
        $periodo = $didacticPlan->period_start->format('Ymd');
        $nombre = "PSD-{$seccion}-{$periodo}-v{$didacticPlan->version}.pdf";

        return $pdf->download($nombre);
    }

    private function optimizePdf($pdf): void
    {
        $pdf->setOption('enable_font_subsetting', true);
        $pdf->setOption('dpi', 72);
        $pdf->setOption('default_font', 'Helvetica');
    }
}
