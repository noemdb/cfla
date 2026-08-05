<?php
// app/Livewire/Director/IndicatorDashboard.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IndicatorDashboard extends Component
{
    use Concerns\HasDirectorScope;

    public $selectedLapsoId;
    public $lapsos;
    public $lapsoActive;

    // ─── KPI globales (toda la institución) ───
    public $totalPeducativos = 0;
    public $totalPensums = 0;
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;
    public $totalLessons = 0;
    public $totalResources = 0;

    // ─── KPIs por Peducativo ───
    public $peducativoIndicators = [];

    // ─── Registración flow charts (global, con rango de fechas) ───
    public $registrationRange = '7d';
    public $chartActivitiesFlow = [];
    public $chartLessonsFlow = [];
    public $chartDiagnosticsFlow = [];

    // ─── Charts por día (filtrados por lapso, alcance institucional) ───
    public $chartActivitiesByDay = [];
    public $chartLessonsByDay = [];
    public $chartScheduledByDay = [];

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
        $service = $this->getDirectorService();

        $this->lapsos = \App\Models\app\Academy\Lapso::orderBy('id')->get();
        $this->lapsoActive = \App\Models\app\Academy\Lapso::current();
        $this->selectedLapsoId = $this->lapsoActive?->id ?? $this->lapsos->first()?->id;

        $this->totalPeducativos = $service->queryPeducativos()->count();

        $this->loadIndicators();
        $this->loadRegistrationFlowCharts();
    }

    public function updatedSelectedLapsoId(): void
    {
        $this->loadIndicators();
    }

    public function loadIndicators(): void
    {
        $service = $this->getDirectorService();

        $this->totalPensums = $service->queryPensums()->count();

        // Lecciones registradas (actividades) del lapso seleccionado, en toda
        // la institución. Mismo criterio que los dashboards de coordinación y
        // liderazgo: cada actividad cuenta una vez aunque tenga publicación LMS.
        $this->totalLessons = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->when($this->selectedLapsoId, fn($q) => $q->where('pevaluacions.lapso_id', $this->selectedLapsoId))
            ->whereNull('pevaluacions.deleted_at')
            ->count(DB::raw('DISTINCT activities.id'));
        $this->totalActivities = $service->queryActivities()->count();
        $this->totalProfesoresActivos = $service->queryProfesores()->count();
        $this->totalResources = $service->queryResources()->count();

        // Indicadores por Peducativo (seguimiento institucional)
        $this->peducativoIndicators = $service->queryPeducativos()->get()
            ->map(function ($peducativo) use ($service) {
                $pestudioIds = $service->queryPestudios()
                    ->where('peducativo_id', $peducativo->id)
                    ->pluck('id');

                return (object) [
                    'peducativo'       => $peducativo,
                    'pensums_count'    => Pensum::whereIn('pestudio_id', $pestudioIds)->count(),
                    'activities_count' => Activity::whereHas('pevaluacion.pensum', fn($q) => $q->whereIn('pestudio_id', $pestudioIds))->count(),
                    'profesores_count' => DB::table('profesors')
                        ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
                        ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                        ->whereIn('pensums.pestudio_id', $pestudioIds)
                        ->whereNull('pevaluacions.deleted_at')
                        ->distinct('profesors.id')
                        ->count('profesors.id'),
                ];
            });

        // Charts por día (filtrados por lapso, alcance institucional)
        $this->loadChartActivitiesByDay();
        $this->loadChartLessonsByDay();
        $this->loadChartScheduledByDay();
    }

    /**
     * Actividades registradas por día (agrupadas por activities.finicial),
     * para el lapso seleccionado y toda la institución (scope global del rol).
     */
    private function loadChartActivitiesByDay(): void
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) {
            $this->chartActivitiesByDay = [];
            return;
        }

        $this->chartActivitiesByDay = Activity::selectRaw('activities.finicial, COUNT(*) as total')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at')
            ->groupBy('activities.finicial')
            ->orderBy('activities.finicial')
            ->get()
            ->map(fn ($row) => [
                'x' => $row->finicial,
                'y' => (int) $row->total,
            ])
            ->toArray();
    }

    /**
     * Lecciones registradas por día. Tres series: Publicadas (por published_at),
     * Programadas (por publish_at, no publicadas) y Borradores (sin publicación).
     * Filtrado por lapso y toda la institución.
     */
    private function loadChartLessonsByDay(): void
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) {
            $this->chartLessonsByDay = [];
            return;
        }

        $scope = function ($query) use ($lapsoId) {
            return $query
                ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                ->where('pevaluacions.lapso_id', $lapsoId)
                ->whereNull('pevaluacions.deleted_at');
        };

        // Serie 1: Publicadas (status = 'PUBLISHED', por published_at)
        $published = $scope(
            Activity::query()->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
        )
            ->where('lms_activity_publications.status', 'PUBLISHED')
            ->selectRaw('DATE(lms_activity_publications.published_at) as date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.published_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Serie 2: Programadas (publish_at NOT NULL, status != 'PUBLISHED')
        $scheduled = $scope(
            Activity::query()->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
        )
            ->whereNotNull('lms_activity_publications.publish_at')
            ->where('lms_activity_publications.status', '!=', 'PUBLISHED')
            ->selectRaw('DATE(lms_activity_publications.publish_at) as date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.publish_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Serie 3: Borradores (publish_at NULL, status != 'PUBLISHED' O null)
        $drafts = $scope(
            Activity::query()->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
        )
            ->whereNull('lms_activity_publications.publish_at')
            ->where(function ($q) {
                $q->whereNull('lms_activity_publications.status')
                  ->orWhere('lms_activity_publications.status', '!=', 'PUBLISHED');
            })
            ->selectRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) as date, COUNT(*) as total')
            ->groupByRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at))')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $allDates = collect(array_merge(
            $published->keys()->toArray(),
            $scheduled->keys()->toArray(),
            $drafts->keys()->toArray()
        ))->unique()->sort()->values();

        $this->chartLessonsByDay = [
            'categories' => $allDates->toArray(),
            'series'     => [
                ['name' => 'Publicadas', 'data' => $allDates->map(fn ($d) => (int) ($published[$d]->total ?? 0))->toArray()],
                ['name' => 'Programadas', 'data' => $allDates->map(fn ($d) => (int) ($scheduled[$d]->total ?? 0))->toArray()],
                ['name' => 'Borradores', 'data' => $allDates->map(fn ($d) => (int) ($drafts[$d]->total ?? 0))->toArray()],
            ],
        ];
    }

    /**
     * Publicaciones programadas por día (por publish_at), para el lapso
     * seleccionado y toda la institución (scope global del rol).
     */
    private function loadChartScheduledByDay(): void
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) {
            $this->chartScheduledByDay = [];
            return;
        }

        $this->chartScheduledByDay = DB::query()
            ->from('lms_activity_publications')
            ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNotNull('lms_activity_publications.publish_at')
            ->selectRaw('DATE(lms_activity_publications.publish_at) as pub_date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.publish_at)')
            ->orderBy('pub_date')
            ->get()
            ->map(fn ($row) => [
                'x' => $row->pub_date,
                'y' => (int) $row->total,
            ])
            ->toArray();
    }

    /**
     * Carga los charts de flujo de registros (actividades/lecciones/diagnósticos).
     * Alcance global de toda la institución (igual que el rol planificación);
     * no se filtra por lapso. Usa el rango seleccionado en $this->registrationRange.
     */
    public function updatedRegistrationRange(): void
    {
        $this->loadRegistrationFlowCharts();
    }

    private function loadRegistrationFlowCharts(): void
    {
        $since = match ($this->registrationRange) {
            '7d'  => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m'  => now()->subMonths(3)->startOfDay(),
            'all' => null,
            default => now()->subDays(7)->startOfDay(),
        };

        // ── Actividades por fecha de creación ──
        $query = Activity::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date');
        if ($since) {
            $query->where('created_at', '>=', $since);
        }
        $this->chartActivitiesFlow = $query->get()->map(fn ($r) => [
            'x' => $r->date,
            'y' => (int) $r->total,
        ])->toArray();

        // ── Lecciones (publicadas / programadas / borradores, por fecha) ──
        $merged = collect();

        // Publicadas (por published_at)
        $pubQuery = DB::table('lms_activity_publications')
            ->where('status', 'PUBLISHED')
            ->whereNotNull('published_at')
            ->selectRaw('DATE(published_at) as date, COUNT(*) as total')
            ->groupBy('date');
        if ($since) $pubQuery->where('published_at', '>=', $since);
        foreach ($pubQuery->get() as $r) {
            $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
        }

        // Programadas (por publish_at, no publicadas)
        $schQuery = DB::table('lms_activity_publications')
            ->whereNotNull('publish_at')
            ->where('status', '!=', 'PUBLISHED')
            ->selectRaw('DATE(publish_at) as date, COUNT(*) as total')
            ->groupBy('date');
        if ($since) $schQuery->where('publish_at', '>=', $since);
        foreach ($schQuery->get() as $r) {
            $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
        }

        // Borradores (actividades sin registro de publicación, por fecha de creación)
        $drfQuery = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
            ->whereNull('lms_activity_publications.publish_at')
            ->where(function ($q) {
                $q->whereNull('lms_activity_publications.status')
                  ->orWhere('lms_activity_publications.status', '!=', 'PUBLISHED');
            })
            ->selectRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) as date, COUNT(*) as total')
            ->groupByRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at))')
            ->orderBy('date');
        if ($since) {
            $drfQuery->whereRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) >= ?', [$since]);
        }
        foreach ($drfQuery->get() as $r) {
            $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
        }

        $this->chartLessonsFlow = $merged
            ->groupBy('date')
            ->map(fn ($items, $date) => ['x' => $date, 'y' => $items->sum('total')])
            ->sortBy('x')
            ->values()
            ->toArray();

        // ── Diagnósticos por fecha de creación (sesiones) ──
        $query = \App\Models\app\Instrument\DiagSession::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date');
        if ($since) {
            $query->where('created_at', '>=', $since);
        }
        $this->chartDiagnosticsFlow = $query->get()->map(fn ($r) => [
            'x' => $r->date,
            'y' => (int) $r->total,
        ])->toArray();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.director.indicator-dashboard')
            ->layout('director.layouts.app');
    }
}
