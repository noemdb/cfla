<?php
namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;

//Helpers
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Instrument\SectionDiagnosticReport;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'is_profesor']);
    }

    public function users()
    {
        $profesor = Profesor::where('user_id', Auth::user()->id)->first();

        return view('profesors.users.index', compact('profesor'));
    }

    public function home()
    {
        $profesor = Profesor::where('user_id', Auth::user()->id)->first();

        // Verificar si el profesor es guía de alguna sección usando el accessor
        $esProfesorGuia = false;
        $seccionesGuia  = collect();

        if ($profesor && $profesor->seccion_guias && $profesor->seccion_guias->count() > 0) {
            $esProfesorGuia = true;
            $seccionesGuia  = $profesor->seccion_guias;
        }

        // Verificar si hay reportes de diagnóstico disponibles
        $tieneReportesDiagnosticos = false;
        $ultimoReporte             = null;

        if ($esProfesorGuia && $seccionesGuia->count() > 0) {
            $seccionIds = $seccionesGuia->pluck('id')->toArray();

            // Buscar si hay reportes de diagnóstico para sus secciones
            $tieneReportesDiagnosticos = SectionDiagnosticReport::whereIn('section_id', $seccionIds)
                ->exists();

            // Obtener el último reporte generado para mostrar información
            if ($tieneReportesDiagnosticos) {
                $ultimoReporte = SectionDiagnosticReport::whereIn('section_id', $seccionIds)
                    ->with(['section' => function ($query) {
                        $query->with(['grado']);
                    }, 'diagMain'])
                    ->latest('created_at')
                    ->first();
            }
        }

        // Obtener el lapso activo
        $lapso = Lapso::getCurrentOrFirst();

        // Si no se encuentra el lapso activo, obtener el lapso con ID 2 como respaldo
        if (! $lapso) {
            $lapso = Lapso::where('id', 2)->first();
        }

        $lapsos = Lapso::all();

        // Para compatibilidad con código existente
        $lapso_active = $lapso;

        // Determinar si debemos mostrar el modal de notificación
        // Solo mostrar si:
        // 1. Es profesor guía
        // 2. Tiene reportes disponibles
        // 3. Estamos dentro del rango de fechas del lapso activo
        $mostrarModalNotificacion = false;

        $lapsoModal = Lapso::where('id', 2)->first();
        if ($esProfesorGuia && $tieneReportesDiagnosticos && $lapsoModal) {
            $fechaActual = Carbon::now();
            $fechaInicio = Carbon::parse($lapsoModal->finicial);
            $fechaFin    = Carbon::parse($lapsoModal->ffinal);

            // Verificar si la fecha actual está dentro del rango del lapso
            if ($fechaActual->between($fechaInicio, $fechaFin)) {
                $mostrarModalNotificacion = true;
            } else {
                // Si estamos fuera del rango, podemos mostrar un mensaje diferente
                // o simplemente no mostrar el modal
                $mostrarModalNotificacion = false;
            }
        }

        $indicadores = collect([]);
        if ($profesor) {
            foreach ($lapsos as $lapsoItem) {
                $obj_pevaluacion = new Pevaluacion;
                $obj_boletin     = new Boletin;
                unset($pevaluacions, $count_pevaluacions, $count_evaluacions);

                $pevaluacions       = Pevaluacion::where('profesor_id', $profesor->id)->where('lapso_id', $lapsoItem->id)->get();
                $count_pevaluacions = ($pevaluacions->IsNotEmpty()) ? $pevaluacions->count() : 0;
                $count_evaluacions  = $obj_pevaluacion->count_evaluacion_prof_lapso($profesor->id, $lapsoItem->id);

                $goal_notas = $profesor->goal_notas_load($lapsoItem->id);
                $real_notas = $profesor->real_notas_load($lapsoItem->id);

                //CCORTEZ
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
            'lapso', // Nueva variable para el lapso activo
            'esProfesorGuia',
            'seccionesGuia',
            'tieneReportesDiagnosticos',
            'ultimoReporte',
            'mostrarModalNotificacion'
        )
        );
    }
}
