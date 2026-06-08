<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Entity\Institucion;
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
        $profesor   = $this->profesor;
        $grado_id   = $request->grado_id ?? null;
        $seccion_id = $request->seccion_id ?? null;
        $lapso_id   = $request->lapso_id ?? null;

        if (!$profesor) {
            return redirect()->route('app.profesors.home')
                ->with('error', 'No se encontró el perfil de profesor.');
        }

        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pevaluacions.profesor_id', $profesor->id)
            ->where('pestudios.planning_module', true)
            ->where('pestudios.status_active', 'true')
            ->orderBy('pevaluacions.created_at', 'desc');

        $pevaluacions = ($grado_id)   ? $pevaluacions->where('pensums.grado_id', $grado_id) : $pevaluacions;
        $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id', $seccion_id) : $pevaluacions;
        $pevaluacions = ($lapso_id)   ? $pevaluacions->where('pevaluacions.lapso_id', $lapso_id) : $pevaluacions;

        $pevaluacions = $pevaluacions->with(['activities.achievements', 'pensum.asignatura', 'pensum.grado.pestudio', 'seccion', 'lapso', 'grupoEstable'])->get();

        // ── Listas para filtros ─────────────────────────────
        $grados = Grado::whereHas('pensums.pevaluacions', function ($q) use ($profesor) {
            $q->where('profesor_id', $profesor->id);
        })->get();
        $list_grado   = $grados->pluck('name', 'id');
        $list_seccion = ($grado_id) ? Seccion::where('grado_id', $grado_id)->pluck('name', 'id') : collect();
        $list_lapso   = Lapso::orderBy('name', 'asc')->pluck('name', 'id');

        return view('profesors.activities.index', compact(
            'pevaluacions', 'grado_id', 'list_grado',
            'seccion_id', 'list_seccion', 'lapso_id', 'list_lapso'
        ));
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
     * Vista PDF: Plan de Actividades completo.
     */
    public function format($id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'pensum.grado',
            'pensum.pestudio',
            'seccion',
            'lapso',
            'profesor',
        ])->findOrFail($id);

        $activities = Activity::with('achievements')
            ->where('pevaluacion_id', $id)
            ->orderBy('finicial', 'asc')
            ->get();

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $fecha       = Carbon::now()->format('d-m-Y h:i A');

        return view('profesors.activities.format', compact(
            'pevaluacion', 'activities', 'institucion', 'fecha'
        ));
    }

    /**
     * Vista PDF: Resumen del Plan de Actividades.
     */
    public function resume($id)
    {
        $pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'pensum.grado',
            'pensum.pestudio',
            'seccion',
            'lapso',
            'profesor',
        ])->findOrFail($id);

        $activities = Activity::with('achievements')
            ->where('pevaluacion_id', $id)
            ->whereNotNull('description')
            ->orderBy('finicial', 'asc')
            ->get();

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();
        $fecha       = Carbon::now()->format('d-m-Y h:i A');

        return view('profesors.activities.resume', compact(
            'pevaluacion', 'activities', 'institucion', 'fecha'
        ));
    }
}
