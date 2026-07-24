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
use Illuminate\Support\Collection;
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
        $lapsos       = Lapso::all()->reject(fn($l) => str_contains(strtolower($l->code ?? ''), 'debug') || str_contains(strtolower($l->name ?? ''), 'debug'));
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
                $chartActivitiesByDay = Activity::selectRaw('activities.finicial as date, COUNT(*) as total')
                    ->whereHas('pevaluacion', fn($q) =>
                        $q->where('profesor_id', $profesor->id)
                            ->where('lapso_id', $lapsoItem->id)
                    )
                    ->groupBy('activities.finicial')
                    ->orderBy('activities.finicial')
                    ->get()
                    ->map(fn($row) => ['x' => $row->date, 'y' => (int) $row->total])
                    ->toArray();

                // ── Chart: Lessons by day ────────────────────────
                $chartLessonsByDay = LmsActivityPublication::selectRaw('COALESCE(lms_activity_publications.published_at, lms_activity_publications.created_at) as pub_date, COUNT(*) as total')
                    ->whereHas('activity.pevaluacion', fn($q) =>
                        $q->where('profesor_id', $profesor->id)
                            ->where('lapso_id', $lapsoItem->id)
                    )
                    ->groupByRaw('DATE(COALESCE(lms_activity_publications.published_at, lms_activity_publications.created_at))')
                    ->orderBy('pub_date')
                    ->get()
                    ->map(function ($row) {
                        $date = $row->pub_date;
                        if ($date && strpos($date, ' ') !== false) $date = explode(' ', $date)[0];
                        return ['x' => $date, 'y' => (int) $row->total];
                    })
                    ->toArray();

                // ── Chart: Scheduled by day ──────────────────────
                $chartScheduledByDay = LmsActivityPublication::selectRaw('DATE(lms_activity_publications.publish_at) as pub_date, COUNT(*) as total')
                    ->whereHas('activity.pevaluacion', fn($q) =>
                        $q->where('profesor_id', $profesor->id)
                            ->where('lapso_id', $lapsoItem->id)
                    )
                    ->whereNotNull('lms_activity_publications.publish_at')
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

        // ── Registration Flow Charts (Flujo de Registros) ──
        $registrationFlow = $profesor
            ? $this->loadRegistrationFlowData($profesor->id, $pensumIds)
            : [];

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
            'mostrarModalNotificacion',
            'registrationFlow'
        ));
    }

    /**
     * Compute registration flow chart data for all date ranges.
     * Three series: activities, lessons (merged), diagnostics.
     * All filtered by profesor ID.
     */
    private function loadRegistrationFlowData(int $profesorId, Collection $pensumIds): array
    {
        $ranges = [
            '7d'  => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m'  => now()->subMonths(3)->startOfDay(),
            'all' => null,
        ];

        $flow = [];

        foreach ($ranges as $key => $since) {
            // ── Activities ─────────────────────────────────────────
            $activitiesQuery = Activity::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereHas('pevaluacion', fn($q) => $q->where('profesor_id', $profesorId))
                ->groupBy('date')
                ->orderBy('date');
            if ($since) {
                $activitiesQuery->where('created_at', '>=', $since);
            }

            $flow[$key]['activities'] = $activitiesQuery->get()
                ->map(fn($r) => ['x' => $r->date, 'y' => (int) $r->total])
                ->toArray();

            // ── Lessons (merged: published + scheduled + drafts) ──
            $merged = collect();

            // Published lessons (published_at)
            $pubQuery = DB::table('lms_activity_publications')
                ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
                ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                ->where('pevaluacions.profesor_id', $profesorId)
                ->where('lms_activity_publications.status', 'PUBLISHED')
                ->whereNotNull('lms_activity_publications.published_at')
                ->selectRaw('DATE(lms_activity_publications.published_at) as date, COUNT(*) as total')
                ->groupBy('date');
            if ($since) $pubQuery->where('lms_activity_publications.published_at', '>=', $since);
            foreach ($pubQuery->get() as $r) {
                $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
            }

            // Scheduled lessons (publish_at, not yet published)
            $schQuery = DB::table('lms_activity_publications')
                ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
                ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                ->where('pevaluacions.profesor_id', $profesorId)
                ->whereNotNull('lms_activity_publications.publish_at')
                ->where('lms_activity_publications.status', '!=', 'PUBLISHED')
                ->selectRaw('DATE(lms_activity_publications.publish_at) as date, COUNT(*) as total')
                ->groupBy('date');
            if ($since) $schQuery->where('lms_activity_publications.publish_at', '>=', $since);
            foreach ($schQuery->get() as $r) {
                $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
            }

            // Draft lessons (no publication record → use activity created_at)
            $drfQuery = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                ->where('pevaluacions.profesor_id', $profesorId)
                ->whereNull('lms_activity_publications.publish_at')
                ->where(function ($q) {
                    $q->whereNull('lms_activity_publications.status')
                      ->orWhere('lms_activity_publications.status', '!=', 'PUBLISHED');
                })
                ->selectRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) as date, COUNT(*) as total')
                ->groupByRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at))');
            if ($since) {
                $drfQuery->where(function ($q) use ($since) {
                    $q->where('lms_activity_publications.created_at', '>=', $since)
                      ->orWhere('activities.created_at', '>=', $since);
                });
            }
            foreach ($drfQuery->get() as $r) {
                $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
            }

            // Merge all lesson sub-queries by date
            $flow[$key]['lessons'] = $merged
                ->groupBy('date')
                ->map(fn($items, $date) => ['x' => $date, 'y' => $items->sum('total')])
                ->sortBy('x')
                ->values()
                ->toArray();

            // ── Diagnostics ───────────────────────────────────────
            if ($pensumIds->isNotEmpty()) {
                $diagQuery = DiagSession::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                    ->whereIn('pensum_id', $pensumIds)
                    ->groupBy('date')
                    ->orderBy('date');
                if ($since) $diagQuery->where('created_at', '>=', $since);
                $flow[$key]['diagnostics'] = $diagQuery->get()
                    ->map(fn($r) => ['x' => $r->date, 'y' => (int) $r->total])
                    ->toArray();
            } else {
                $flow[$key]['diagnostics'] = [];
            }
        }

        return $flow;
    }
}
