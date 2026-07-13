<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Entity\Institucion;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id', Auth::user()->id)->first();
            return $next($request);
        });
    }

    /**
     * Listado de Pevaluacions del profesor con filtros.
     */
    public function index(Request $request)
    {
        $profesor    = $this->profesor;
        $pestudio_id = $request->pestudio_id ?? null;
        $grado_id    = $request->grado_id ?? null;
        $seccion_id  = $request->seccion_id ?? null;
        $lapso_id    = $request->lapso_id ?? Lapso::current()?->id;
        $sort        = $request->sort ?? 'pevaluacions.created_at';
        $direction   = $request->direction ?? 'desc';

        if (!$profesor) {
            return redirect()->route('app.profesors.home')
                ->with('error', 'No se encontró el perfil de profesor.');
        }

        // ── Columnas permitidas para sort ──
        $allowedSorts = [
            'asignaturas.name', 'grados.name', 'lapsos.name',
            'pevaluacions.finicial', 'pevaluacions.created_at',
        ];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'pevaluacions.created_at';
            $direction = 'desc';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $pevaluacionsQuery = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('asignaturas', 'pensums.asignatura_id', '=', 'asignaturas.id')
            ->join('grados', 'pensums.grado_id', '=', 'grados.id')
            ->join('lapsos', 'pevaluacions.lapso_id', '=', 'lapsos.id')
            ->where('pevaluacions.profesor_id', $profesor->id)
            ->where('pestudios.planning_module', true)
            ->where('pestudios.status_active', 'true');

        $pevaluacionsQuery = ($pestudio_id) ? $pevaluacionsQuery->where('pensums.pestudio_id', $pestudio_id) : $pevaluacionsQuery;
        $pevaluacionsQuery = ($grado_id)    ? $pevaluacionsQuery->where('pensums.grado_id', $grado_id) : $pevaluacionsQuery;
        $pevaluacionsQuery = ($seccion_id)  ? $pevaluacionsQuery->where('pevaluacions.seccion_id', $seccion_id) : $pevaluacionsQuery;
        $pevaluacionsQuery = ($lapso_id)    ? $pevaluacionsQuery->where('pevaluacions.lapso_id', $lapso_id) : $pevaluacionsQuery;

        $pevaluacions = $pevaluacionsQuery->with([
            'activities.achievements', 'pensum.asignatura',
            'pensum.grado.pestudio', 'seccion', 'lapso', 'grupoEstable',
        ])->orderBy($sort, $direction)->paginate(10);

        // ── Listas para filtros ─────────────────────────────
        $list_pestudio = Pestudio::where('planning_module', true)
            ->where('status_active', 'true')
            ->whereHas('pensums.pevaluacions', function ($q) use ($profesor) {
                $q->where('profesor_id', $profesor->id);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        $grados = Grado::whereHas('pensums.pevaluacions', function ($q) use ($profesor) {
            $q->where('profesor_id', $profesor->id);
        })->when($pestudio_id, function ($q) use ($pestudio_id) {
            $q->where('pestudio_id', $pestudio_id);
        })->get();
        $list_grado   = $grados->pluck('name', 'id');
        $list_seccion = ($grado_id) ? Seccion::where('grado_id', $grado_id)->pluck('name', 'id') : collect();
        $list_lapso   = Lapso::orderBy('name', 'asc')->pluck('name', 'id');
        $lapsos       = Lapso::orderBy('name', 'asc')->get();
        $lapso_active = Lapso::find($lapso_id) ?? Lapso::current();

        return view('profesors.activities.index', compact(
            'pevaluacions', 'pestudio_id', 'list_pestudio',
            'grado_id', 'list_grado',
            'seccion_id', 'list_seccion', 'lapso_id', 'list_lapso',
            'lapsos', 'lapso_active', 'sort', 'direction'
        ));
    }

    /**
     * Endpoint AJAX: retorna grados activos del profesor filtrados por pestudio.
     */
    public function gradosByPestudio($pestudio_id)
    {
        $profesor = $this->profesor;

        if (!$profesor) {
            return response()->json([]);
        }

        $grados = Grado::where('pestudio_id', $pestudio_id)
            ->where('status_active', 'true')
            ->whereHas('pensums.pevaluacions', function ($q) use ($profesor) {
                $q->where('profesor_id', $profesor->id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($grados);
    }

    /**
     * Endpoint AJAX: retorna secciones activas del profesor filtradas por grado.
     */
    public function seccionesByGrado($grado_id)
    {
        $profesor = $this->profesor;

        if (!$profesor) {
            return response()->json([]);
        }

        $secciones = Seccion::where('grado_id', $grado_id)
            ->where('status_active', 'true')
            ->whereHas('pevaluacions', function ($q) use ($profesor) {
                $q->where('profesor_id', $profesor->id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($secciones);
    }

    /**
     * Detalle de Pevaluacion + Livewire CRUD de actividades.
     */
    public function create($pevaluacion_id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'pensum.grado',
            'pensum.pestudio',
            'seccion',
            'lapso',
            'profesor.user',
            'escala',
        ])->findOrFail($pevaluacion_id);

        return view('profesors.activities.create', compact('pevaluacion'));
    }

    /**
     * PDF: Plan de Actividades completo (9 columnas).
     */
    public function format($id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'pensum.grado',
            'seccion.grado',
            'lapso',
            'profesor',
            'activities' => fn($q) => $q->orderBy('finicial'),
            'activities.achievements',
        ])->findOrFail($id);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.planning.activities.format', [
            'pevaluacion'  => $pevaluacion,
            'institucion'   => $institucion,
            'fecha'         => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');
        $pdf->setOption('enable_font_subsetting', true);
        $pdf->setOption('dpi', 72);
        $pdf->setOption('default_font', 'Helvetica');

        return $pdf->stream("plan-actividades-{$id}.pdf");
    }

    /**
     * PDF: Resumen del Plan de Actividades (6 columnas).
     */
    public function resume($id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'pensum.grado',
            'seccion.grado',
            'lapso',
            'profesor',
            'activities' => fn($q) => $q->whereNotNull('description')->orderBy('finicial'),
            'activities.achievements',
        ])->findOrFail($id);

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $pdf = Pdf::loadView('pdfs.planning.activities.resume', [
            'pevaluacion'  => $pevaluacion,
            'institucion'   => $institucion,
            'fecha'         => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ]);

        $pdf->setPaper('letter', 'landscape');
        $pdf->setOption('enable_font_subsetting', true);
        $pdf->setOption('dpi', 72);
        $pdf->setOption('default_font', 'Helvetica');

        return $pdf->stream("resumen-actividades-{$id}.pdf");
    }
}
