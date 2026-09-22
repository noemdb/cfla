<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Entity\Institucion;
use App\Models\app\Learner\Estudiant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InscripcionPdfController extends Controller
{
    /**
     * Exporta el listado de inscripciones de un grado/sección a PDF.
     *
     * Requiere `grado_id` y `seccion_id` (la sección debe pertenecer al grado).
     * Opcionalmente respeta `search` y `tipo_id` para reflejar los filtros del
     * listado en pantalla.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'grado_id' => ['required', 'integer', 'exists:grados,id'],
            'seccion_id' => ['required', 'integer', 'exists:seccions,id'],
            'search' => ['nullable', 'string', 'max:100'],
            'tipo_id' => ['nullable', 'integer', 'exists:tinscripcions,id'],
        ]);

        $seccion = Seccion::with('grado.pestudio')->findOrFail($data['seccion_id']);

        if ((int) $seccion->grado_id !== (int) $data['grado_id']) {
            abort(422, 'La sección seleccionada no pertenece al grado indicado.');
        }

        $query = Inscripcion::with('estudiant')
            ->where('seccion_id', $seccion->id);

        if (! empty($data['search'])) {
            $search = $data['search'];
            $query->whereHas('estudiant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('ci_estudiant', 'like', "%{$search}%");
            });
        }

        if (! empty($data['tipo_id'])) {
            $query->where('tipo_id', $data['tipo_id']);
        }

        $inscripcions = $query
            ->orderBy(Estudiant::select('lastname')->whereColumn('estudiants.id', 'inscripcions.estudiant_id'))
            ->orderBy(Estudiant::select('name')->whereColumn('estudiants.id', 'inscripcions.estudiant_id'))
            ->get();

        $institucion = Institucion::orderByDesc('created_at')->first();
        $grado = $seccion->grado;

        $pdf = Pdf::loadView('pdfs.planning.inscripcions.listado', [
            'institucion' => $institucion,
            'seccion' => $seccion,
            'grado' => $grado,
            'pestudio' => $grado?->pestudio,
            'inscripcions' => $inscripcions,
            'total' => $inscripcions->count(),
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('enable_font_subsetting', true);
        $pdf->setOption('dpi', 72);
        $pdf->setOption('default_font', 'Helvetica');

        $filename = 'inscripciones-'
            .Str::slug(($grado?->name ?? 'grado').'-'.($seccion->name ?? 'seccion'))
            .'-'.now()->format('Ymd').'.pdf';

        return $pdf->stream($filename);
    }
}
