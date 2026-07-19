<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Instrument\DiagSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // ── Indicadores por lapso ─────────────────────────────
        $indicadores = collect([]);

        // Pensums del profesor (para diagnósticos, no varía por lapso)
        $pensumIds = $profesor
            ? Pensum::whereHas('pevaluacions', fn($q) => $q->where('profesor_id', $profesor->id))
                ->pluck('id')
            : collect();

        if ($profesor) {
            foreach ($lapsos as $lapsoItem) {
                $pevaluacions = Pevaluacion::where('profesor_id', $profesor->id)
                    ->where('lapso_id', $lapsoItem->id)
                    ->get();

                // ── Diagnósticos ─────────────────────────────────
                if ($pensumIds->isNotEmpty()) {
                    $diagTotal = DiagSession::whereIn('pensum_id', $pensumIds)
                        ->where('lapso_id', $lapsoItem->id)
                        ->count();

                    $diagCompleted = DiagSession::whereIn('pensum_id', $pensumIds)
                        ->where('lapso_id', $lapsoItem->id)
                        ->whereNotNull('completado_at')
                        ->count();

                    $diagInProgress = DiagSession::whereIn('pensum_id', $pensumIds)
                        ->where('lapso_id', $lapsoItem->id)
                        ->where('activo', true)
                        ->whereNull('completado_at')
                        ->count();

                    $diagProgress = $diagTotal > 0
                        ? round(($diagCompleted / $diagTotal) * 100)
                        : 0;
                } else {
                    $diagTotal      = 0;
                    $diagCompleted  = 0;
                    $diagInProgress = 0;
                    $diagProgress   = 0;
                }

                // ── Actividades Registradas ─────────────────────
                $actTotal = Activity::whereHas('pevaluacion', fn($q) =>
                    $q->where('profesor_id', $profesor->id)
                        ->where('lapso_id', $lapsoItem->id)
                )->count();

                $actWithEval = Activity::whereHas('pevaluacion', fn($q) =>
                    $q->where('profesor_id', $profesor->id)
                        ->where('lapso_id', $lapsoItem->id)
                )->whereNotNull('description')
                 ->where('description', '!=', '')
                 ->count();

                $actApproved = Activity::whereHas('pevaluacion', fn($q) =>
                    $q->where('profesor_id', $profesor->id)
                        ->where('lapso_id', $lapsoItem->id)
                )->where('status', true)
                 ->count();

                // Actividades con enseñanza de calidad
                // (≥10 palabras significativas en el campo teaching)
                $activitiesTeaching = Activity::whereHas('pevaluacion', fn($q) =>
                    $q->where('profesor_id', $profesor->id)
                        ->where('lapso_id', $lapsoItem->id)
                )->get(['teaching']);

                $actCalidadEns = $activitiesTeaching->filter(
                    fn($a) => $a->teachingWordsMayorCount(3) >= 10
                )->count();

                // ── LMS / Lecciones ──────────────────────────────
                $lmsPublished = Activity::whereHas('pevaluacion', fn($q) =>
                    $q->where('profesor_id', $profesor->id)
                        ->where('lapso_id', $lapsoItem->id)
                )->whereHas('lmsPublication', fn($q) =>
                    (new LmsActivityPublication)->scopeVisibleNow($q)
                )->count();

                $lmsSections = LmsActivitySection::whereHas('activity.pevaluacion', fn($q) =>
                    $q->where('profesor_id', $profesor->id)
                        ->where('lapso_id', $lapsoItem->id)
                )->count();

                $lmsResources = LmsActivityResource::whereHas('activity.pevaluacion', fn($q) =>
                    $q->where('profesor_id', $profesor->id)
                        ->where('lapso_id', $lapsoItem->id)
                )->count();

                // ── Chart: Activities by day ────────────────────────
                $chartActivitiesByDay = Activity::selectRaw('activities.finicial, COUNT(*) as total')
                    ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                    ->where('pevaluacions.profesor_id', $profesor->id)
                    ->where('pevaluacions.lapso_id', $lapsoItem->id)
                    ->whereNull('pevaluacions.deleted_at')
                    ->groupBy('activities.finicial')
                    ->orderBy('activities.finicial')
                    ->get()
                    ->map(fn($row) => ['x' => $row->finicial, 'y' => (int) $row->total])
                    ->toArray();

                // ── Chart: Lessons by day ────────────────────────
                $chartLessonsByDay = DB::table('lms_activity_publications')
                    ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
                    ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                    ->where('pevaluacions.profesor_id', $profesor->id)
                    ->where('pevaluacions.lapso_id', $lapsoItem->id)
                    ->whereNull('pevaluacions.deleted_at')
                    ->selectRaw('COALESCE(lms_activity_publications.published_at, lms_activity_publications.created_at) as pub_date, COUNT(*) as total')
                    ->groupByRaw('DATE(COALESCE(lms_activity_publications.published_at, lms_activity_publications.created_at))')
                    ->orderBy('pub_date')
                    ->whereNotNull(DB::raw('COALESCE(lms_activity_publications.published_at, lms_activity_publications.created_at)'))
                    ->get()
                    ->map(function ($row) {
                        $date = $row->pub_date;
                        if ($date && strpos($date, ' ') !== false) $date = explode(' ', $date)[0];
                        return ['x' => $date, 'y' => (int) $row->total];
                    })
                    ->toArray();

                // ── Chart: Scheduled by day ──────────────────────
                $chartScheduledByDay = DB::table('lms_activity_publications')
                    ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
                    ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                    ->where('pevaluacions.profesor_id', $profesor->id)
                    ->where('pevaluacions.lapso_id', $lapsoItem->id)
                    ->whereNull('pevaluacions.deleted_at')
                    ->whereNotNull('lms_activity_publications.publish_at')
                    ->selectRaw('DATE(lms_activity_publications.publish_at) as pub_date, COUNT(*) as total')
                    ->groupByRaw('DATE(lms_activity_publications.publish_at)')
                    ->orderBy('pub_date')
                    ->get()
                    ->map(fn($row) => ['x' => $row->pub_date, 'y' => (int) $row->total])
                    ->toArray();

                $indicador = collect([
                    'id'                 => $lapsoItem->id,
                    'lapso'              => $lapsoItem,
                    'name'               => $lapsoItem->name,
                    'code'               => $lapsoItem->code,
                    'count_pevaluacions' => $pevaluacions->isNotEmpty() ? $pevaluacions->count() : 0,
                    // Actividades
                    'act_total'          => $actTotal,
                    'act_con_eval'       => $actWithEval,
                    'act_aprobadas'      => $actApproved,
                    'act_calidad_ens'    => $actCalidadEns,
                    // Diagnósticos
                    'diag_total'         => $diagTotal,
                    'diag_completed'     => $diagCompleted,
                    'diag_en_progreso'   => $diagInProgress,
                    'diag_progress'      => $diagProgress,
                    // LMS / Lecciones
                    'lms_published'      => $lmsPublished,
                    'lms_sections'       => $lmsSections,
                    'lms_resources'      => $lmsResources,
                    // Charts
                    'chart_activities'   => $chartActivitiesByDay,
                    'chart_lessons'      => $chartLessonsByDay,
                    'chart_scheduled'    => $chartScheduledByDay,
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
