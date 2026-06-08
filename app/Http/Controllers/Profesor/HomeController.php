<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Boletin;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isProfesor']);
    }

    /**
     * Profile / user page.
     */
    public function users()
    {
        $profesor = Profesor::where('user_id', Auth::user()->id)->first();

        return view('profesors.users.index', compact('profesor'));
    }

    /**
     * Dashboard principal del profesor.
     */
    public function home()
    {
        $profesor = Profesor::where('user_id', Auth::user()->id)->first();

        // ── Profesor guía ───────────────────────────────────────
        $esProfesorGuia = false;
        $seccionesGuia  = collect();

        if ($profesor && $profesor->seccion_guias && $profesor->seccion_guias->count() > 0) {
            $esProfesorGuia = true;
            $seccionesGuia  = $profesor->seccion_guias;
        }

        // ── Reportes de diagnóstico ─────────────────────────────
        $tieneReportesDiagnosticos = false;
        $ultimoReporte             = null;

        if ($esProfesorGuia && $seccionesGuia->count() > 0) {
            $seccionIds = $seccionesGuia->pluck('id')->toArray();

            if (class_exists(\App\Models\app\Instrument\SectionDiagnosticReport::class)) {
                $tieneReportesDiagnosticos = \App\Models\app\Instrument\SectionDiagnosticReport::whereIn('section_id', $seccionIds)->exists();

                if ($tieneReportesDiagnosticos) {
                    $ultimoReporte = \App\Models\app\Instrument\SectionDiagnosticReport::whereIn('section_id', $seccionIds)
                        ->with(['section' => function ($query) {
                            $query->with(['grado']);
                        }, 'diagMain'])
                        ->latest('created_at')
                        ->first();
                }
            }
        }

        // ── Lapsos ──────────────────────────────────────────────
        $lapso       = Lapso::current();
        $lapsos       = Lapso::all();
        $lapso_active = $lapso;

        // ── Modal de notificación ──────────────────────────────
        $mostrarModalNotificacion = false;

        $lapsoModal = Lapso::find(2);
        if ($esProfesorGuia && $tieneReportesDiagnosticos && $lapsoModal) {
            $fechaActual = Carbon::now();
            $fechaInicio = Carbon::parse($lapsoModal->finicial);
            $fechaFin    = Carbon::parse($lapsoModal->ffinal);

            if ($fechaActual->between($fechaInicio, $fechaFin)) {
                $mostrarModalNotificacion = true;
            }
        }

        // ── Indicadores por lapso ──────────────────────────────
        $indicadores = collect([]);

        if ($profesor) {
            foreach ($lapsos as $lapsoItem) {
                $pevaluacions       = Pevaluacion::where('profesor_id', $profesor->id)
                    ->where('lapso_id', $lapsoItem->id)
                    ->get();

                $count_pevaluacions = $pevaluacions->isNotEmpty() ? $pevaluacions->count() : 0;
                $count_evaluacions  = Pevaluacion::count_evaluacion_prof_lapso($profesor->id, $lapsoItem->id);

                $goal_notas = $profesor->goal_notas_load($lapsoItem->id);
                $real_notas = $profesor->real_notas_load($lapsoItem->id);

                $porc_notas_load = ($goal_notas > 0) ? 100 * ($real_notas / $goal_notas) : 0;

                if ($porc_notas_load > 99.85) {
                    $porc_notas_load = 100.00;
                }

                $porc_notas_load = round($porc_notas_load, 1);

                if ($goal_notas - $real_notas > 0 && $goal_notas - $real_notas < 6) {
                    $porc_notas_load = 100;
                }

                $promedio       = $profesor->getPromedio($lapsoItem->id, 2);
                $porc_aprobados = $profesor->getPorcAprobados($lapsoItem->id, 1);

                $indicador = collect([
                    'id'                 => $lapsoItem->id,
                    'lapso'              => $lapsoItem,
                    'name'               => $lapsoItem->name,
                    'code'               => $lapsoItem->code,
                    'count_pevaluacions' => $count_pevaluacions,
                    'count_evaluacions'  => $count_evaluacions,
                    'porc_notas_load'    => $porc_notas_load,
                    'promedio'           => $promedio,
                    'porc_aprobados'     => $porc_aprobados,
                ]);

                $indicadores->push($indicador);
            }
        }

        return view('profesors.home', compact(
            'profesor',
            'indicadores',
            'lapsos',
            'lapso_active',
            'lapso',
            'esProfesorGuia',
            'seccionesGuia',
            'tieneReportesDiagnosticos',
            'ultimoReporte',
            'mostrarModalNotificacion'
        ));
    }
}
