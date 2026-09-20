<?php

namespace App\Livewire\Planning\Indicator;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Activity;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagSession;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class IndexComponent extends Component
{
    public $activeTab = 1;
    public $selectedLapsoId;

    // ─── Filters ─────────────────────────────────────────────────────
    public $selectedPeducativoId = null; // null = todos
    public $selectedPestudioId = null;   // null = todos
    public $selectedProfesorId = null;   // null = todos
    public $selectedGradoId = null;      // null = todos

    // Master data
    public $pestudios;
    public $peducativos;
    public $lapsos;
    public $lapsoActive;
    public $profesoresOptions = []; // for filter dropdown
    public $gradosOptions = [];     // for filter dropdown

    // ─── Tab 1: Indicadores Principales (per peducativo, filtered by selected lapso) ──
    public $peducativoMainIndicators = [];

    // ─── Tab 2: Profesores (nested by lapso → peducativo) ────────────────────────────
    public $tab2Data = [];

    // ─── Tab 3: Actividades (nested by lapso → peducativo) ────────────────────────────
    public $tab3Data = [];

    // ─── Tab 4: Lecciones (nested by lapso → peducativo) ──────────────────────────────
    public $tab4Data = [];

    // ─── Totals for global KPI boxes ───────────────────────────────────
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;
    public $totalDiagActive = 0;

    // ─── Charts ────────────────────────────────────────────────────────
    public $chartActivitiesByDay = [];
    public $chartLessonsByDay = [];
    public $chartScheduledByDay = [];

    // ─── Date range per chart (scoped by selected lapso) ───────────────
    public $chartActivitiesRange = '7d';
    public $chartLessonsRange = '7d';
    public $chartScheduledRange = '7d';

    // ─── Lesson stats (scoped by selected lapso) ───────────────────────
    public $lessonTotal = 0;
    public $lessonScheduled = 0;
    public $lessonPublished = 0;
    public $lessonPublishedPct = 0;
    public $lessonScheduledPct = 0;

    // ─── Registration flow charts (global, with date range) ────────────
    public $registrationRange = '7d';
    public $chartActivitiesFlow = [];
    public $chartLessonsFlow = [];
    public $chartDiagnosticsFlow = [];

    public function mount()
    {
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->where('planning_module', 1)
            ->orderBy('order')
            ->get();

        // Only keep peducativos that have at least one pestudio with planning_module = 1
        $planningPeducativoIds = $this->pestudios->pluck('peducativo_id')->unique()->values();
        $this->peducativos = Peducativo::where('status_active', 'true')
            ->whereIn('id', $planningPeducativoIds)
            ->orderBy('order')
            ->get();

        $this->lapsos = Lapso::orderBy('id')->get();
        $this->lapsoActive = Lapso::current();

        $this->selectedLapsoId = $this->lapsoActive?->id ?? $this->lapsos->first()?->id;

        $this->profesoresOptions = Profesor::where('status_active', 'true')
            ->orderBy('lastname')
            ->orderBy('name')
            ->get(['id', 'name', 'lastname']);

        $this->refreshGradosOptions();

        $this->loadAllData();
    }

    // ─── Filter change handlers ──────────────────────────────────────
    public function updatedSelectedLapsoId()
    {
        $this->loadAllData();
    }

    public function updatedSelectedPeducativoId()
    {
        $this->selectedPestudioId = null;
        $this->selectedGradoId = null;
        $this->selectedProfesorId = null;
        $this->refreshGradosOptions();
        $this->loadAllData();
    }

    public function updatedSelectedPestudioId()
    {
        $this->selectedGradoId = null;
        $this->selectedProfesorId = null;
        $this->refreshGradosOptions();
        $this->loadAllData();
    }

    public function updatedSelectedProfesorId()
    {
        $this->loadAllData();
    }

    public function updatedSelectedGradoId()
    {
        $this->selectedProfesorId = null;
        $this->loadAllData();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Refresh the grados dropdown options based on selected pestudio/peducativo.
     */
    private function refreshGradosOptions()
    {
        $query = Grado::where('status_active', 'true')->orderBy('order');

        if ($this->selectedPestudioId) {
            $query->where('pestudio_id', $this->selectedPestudioId);
        } elseif ($this->selectedPeducativoId) {
            $pestudioIds = $this->pestudios
                ->where('peducativo_id', $this->selectedPeducativoId)
                ->pluck('id');
            $query->whereIn('pestudio_id', $pestudioIds);
        }

        $this->gradosOptions = $query->get(['id', 'name', 'pestudio_id']);
    }

    /**
     * Base pestudios after applying pestudio-level filters (selectedPestudioId + selectedGradoId).
     */
    private function getBasePestudios()
    {
        $pestudios = $this->pestudios;

        if ($this->selectedPestudioId) {
            $pestudios = $pestudios->where('id', $this->selectedPestudioId);
        }

        if ($this->selectedGradoId) {
            $grado = Grado::find($this->selectedGradoId);
            if ($grado) {
                $pestudios = $pestudios->where('id', $grado->pestudio_id);
            }
        }

        return $pestudios;
    }

    /**
     * Get pestudios that belong to a given peducativo, respecting all filters.
     */
    private function getPestudiosForPeducativo($peducativoId)
    {
        return $this->getBasePestudios()->where('peducativo_id', $peducativoId);
    }

    /**
     * Get only the peducativos that match current filters.
     */
    private function getFilteredPeducativos()
    {
        $list = $this->peducativos;
        $activePestudioIds = $this->getBasePestudios()->pluck('peducativo_id')->unique()->values();
        $list = $list->whereIn('id', $activePestudioIds);

        if ($this->selectedPeducativoId) {
            $list = $list->where('id', $this->selectedPeducativoId);
        }

        return $list->values();
    }

    /**
     * Load all indicator data aggregated by Peducativo for all lapsos.
     */
    public function loadAllData()
    {
        $lapsoId = $this->selectedLapsoId;
        $filteredPeducativos = $this->getFilteredPeducativos();

        // ══ TAB 1: Main indicators per peducativo (selected lapso) ══
        $this->peducativoMainIndicators = collect();

        foreach ($filteredPeducativos as $peducativo) {
            $pestudios = $this->getPestudiosForPeducativo($peducativo->id);
            $pestudioIds = $pestudios->pluck('id');

            $totalActivities = 0;
            $totalProfesores = collect();
            foreach ($pestudios as $pestudio) {
                $totalActivities += $pestudio->getActivitiesCount($lapsoId);
                $totalProfesores = $totalProfesores->merge($pestudio->getProfesors($lapsoId));
            }

            // Solo lecciones con contenido LMS (al menos una sección o recurso),
            // misma lógica que el KPI global y el monitor LMS.
            $lessonsQuery = Activity::withLmsContent()
                ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                ->whereIn('pensums.pestudio_id', $pestudioIds)
                ->whereNull('pevaluacions.deleted_at');
            if ($lapsoId) {
                $lessonsQuery->where('pevaluacions.lapso_id', $lapsoId);
            }
            $lessonsCount = $lessonsQuery->distinct()->count('activities.id');

            $this->peducativoMainIndicators->push((object) [
                'peducativo' => $peducativo,
                'pestudios' => $pestudios,
                'activities_count' => $totalActivities,
                'profesores_count' => $totalProfesores->unique('id')->count(),
                'lessons_count' => $lessonsCount,
                'grados_count' => DB::table('grados')
                    ->whereIn('pestudio_id', $pestudioIds)
                    ->whereNull('deleted_at')
                    ->count(),
                'pensums_count' => DB::table('pensums')
                    ->whereIn('pestudio_id', $pestudioIds)
                    ->whereNull('deleted_at')
                    ->count(),
            ]);
        }

        // ══ Global KPI boxes (no dependen del lapso ni de los filtros) ══
        $this->loadGlobalKpis();

        // ══ TAB 2: Profesores data — only selected lapso ══
        $this->tab2Data = [];
        $tab2Lapso = $this->lapsos->firstWhere('id', $this->selectedLapsoId);
        if ($tab2Lapso) {
            foreach ($filteredPeducativos as $peducativo) {
                $pestudios = $this->getPestudiosForPeducativo($peducativo->id);
                $allProfesors = collect();
                $totalBoletinsPROM = 0;
                $profesorCount = 0;

                foreach ($pestudios as $pestudio) {
                    $profs = $pestudio->getProfesorsWithKPIs($tab2Lapso->id);
                    if ($this->selectedProfesorId) {
                        $profs = $profs->where('id', $this->selectedProfesorId);
                    }
                    $allProfesors = $allProfesors->merge($profs);
                    $pestIeeProm = $pestudio->getProfesorsIEEsPROM($tab2Lapso->id);
                    $totalBoletinsPROM += $pestIeeProm * $profs->count();
                    $profesorCount += $profs->count();
                }

                $allProfesors = $allProfesors->unique('id');
                $ieePROM = $profesorCount > 0 ? $totalBoletinsPROM / $profesorCount : 0;

                $this->tab2Data[$tab2Lapso->id][$peducativo->id] = [
                    'peducativo' => $peducativo,
                    'lapso' => $tab2Lapso,
                    'ieePROM' => $ieePROM,
                    'profesors' => $allProfesors,
                ];
            }
        }

        // ══ TAB 3: Activity indicators — only selected lapso, single-pass queries ══
        $this->tab3Data = [];
        $tab3Lapso = $this->lapsos->firstWhere('id', $this->selectedLapsoId);
        if ($tab3Lapso) {
            foreach ($filteredPeducativos as $peducativo) {
                $pestudios = $this->getPestudiosForPeducativo($peducativo->id);
                $pestudioIds = $pestudios->pluck('id');

                // Single query for all pevIds in this peducativo
                $pevIds = Pevaluacion::whereNull('pevaluacions.deleted_at')
                    ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                    ->whereIn('pensums.pestudio_id', $pestudioIds)
                    ->where('pevaluacions.lapso_id', $tab3Lapso->id)
                    ->pluck('pevaluacions.id');

                $totalPevCount = $pevIds->count();

                // Single query for all activities in this peducativo
                $allActivities = Activity::whereIn('pevaluacion_id', $pevIds)->get();
                $totalActivities = $allActivities->count();

                // Teacher counts (per pestudio — can't merge across pestudio FK)
                $totalTeachers = 0;
                $totalActiveTeachers = 0;
                foreach ($pestudios as $pestudio) {
                    $totalTeachers += $pestudio->getTeachersCount($tab3Lapso->id);
                    $totalActiveTeachers += $pestudio->getActiveTeachersCount($tab3Lapso->id);
                }

                // Aggregated indicators at peducativo level
                $cobertura = $totalPevCount > 0
                    ? round($totalActivities / $totalPevCount, 2) : 0;
                $participacion = $totalTeachers > 0
                    ? round(($totalActiveTeachers / $totalTeachers) * 100, 1) : 0;

                $withComments = $allActivities->filter(fn($a) => !empty($a->comments))->count();
                $approved = $allActivities->where('status', true)->count();
                $seguimiento = $totalActivities > 0
                    ? round(($withComments / $totalActivities) * 100, 1) : 0;
                $aprobacion = $totalActivities > 0
                    ? round(($approved / $totalActivities) * 100, 1) : 0;

                $pevWithObs = Pevaluacion::whereIn('id', $pevIds)
                    ->whereNotNull('observations')
                    ->where('observations', '<>', '')
                    ->count();
                $supervision = $totalPevCount > 0
                    ? round(($pevWithObs / $totalPevCount) * 100, 1) : 0;

                $this->tab3Data[$tab3Lapso->id][$peducativo->id] = (object) [
                    'peducativo' => $peducativo,
                    'lapso' => $tab3Lapso,
                    'indicators' => (object) [
                        'total_activities' => $totalActivities,
                        'cobertura_curricular' => $cobertura,
                        'participacion' => $participacion,
                        'seguimiento' => $seguimiento,
                        'aprobacion' => $aprobacion,
                        'supervision' => $supervision,
                    ],
                    'pevCount' => $totalPevCount,
                ];
            }
        }

        // ══ TAB 4: Lesson indicators — only selected lapso ══
        $this->tab4Data = [];
        $tab4Lapso = $this->lapsos->firstWhere('id', $this->selectedLapsoId);
        if ($tab4Lapso) {
            foreach ($filteredPeducativos as $peducativo) {
                $pestudios = $this->getPestudiosForPeducativo($peducativo->id);
                $pestudioIds = $pestudios->pluck('id');

                $pevIds = Pevaluacion::whereNull('pevaluacions.deleted_at')
                    ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                    ->whereIn('pensums.pestudio_id', $pestudioIds)
                    ->where('pevaluacions.lapso_id', $tab4Lapso->id)
                    ->pluck('pevaluacions.id');

                $totalPevCount = $pevIds->count();

                // Lessons (activities with LMS content) scoped to these pevIds
                $lessons = Activity::withLmsContent()
                    ->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                    ->whereIn('activities.pevaluacion_id', $pevIds)
                    ->select(
                        'activities.*',
                        'lms_activity_publications.status as pub_status',
                        'lms_activity_publications.publish_at',
                        'lms_activity_publications.published_at',
                        'lms_activity_publications.notes'
                    )
                    ->get();

                $totalLessons = $lessons->count();

                // Count by status
                $published = $lessons->filter(fn($l) => $l->pub_status === 'PUBLISHED')->count();
                $scheduled = $lessons->filter(fn($l) => !is_null($l->publish_at) && $l->pub_status !== 'PUBLISHED')->count();
                $drafts = $totalLessons - $published - $scheduled;

                // Percentages
                $publishedPct = $totalLessons > 0 ? round(($published / $totalLessons) * 100, 1) : 0;
                $scheduledPct = $totalLessons > 0 ? round(($scheduled / $totalLessons) * 100, 1) : 0;
                $draftPct = $totalLessons > 0 ? round(($drafts / $totalLessons) * 100, 1) : 0;

                // Avg lessons per plan
                $avgPerPev = $totalPevCount > 0 ? round($totalLessons / $totalPevCount, 2) : 0;

                // Teachers with lessons (distinct profesor_ids from pevs that have lessons)
                $pevIdsWithLessons = $lessons->pluck('pevaluacion_id')->unique();
                $profIdsWithLessons = Pevaluacion::whereIn('id', $pevIdsWithLessons)
                    ->whereNotNull('profesor_id')
                    ->distinct()
                    ->count('profesor_id');

                $totalTeachers = 0;
                foreach ($pestudios as $pestudio) {
                    $totalTeachers += $pestudio->getTeachersCount($tab4Lapso->id);
                }
                $teachersParticipation = $totalTeachers > 0 ? round(($profIdsWithLessons / $totalTeachers) * 100, 1) : 0;

                // Supervision: lessons with notes
                $withNotes = $lessons->filter(fn($l) => !empty($l->notes))->count();
                $supervision = $totalLessons > 0 ? round(($withNotes / $totalLessons) * 100, 1) : 0;

                $this->tab4Data[$tab4Lapso->id][$peducativo->id] = (object) [
                    'peducativo' => $peducativo,
                    'lapso' => $tab4Lapso,
                    'indicators' => (object) [
                        'total_lessons'          => $totalLessons,
                        'published_count'        => $published,
                        'scheduled_count'        => $scheduled,
                        'draft_count'            => $drafts,
                        'published_pct'          => $publishedPct,
                        'scheduled_pct'          => $scheduledPct,
                        'draft_pct'              => $draftPct,
                        'avg_lessons_per_pev'    => $avgPerPev,
                        'teachers_participation' => $teachersParticipation,
                        'supervision_rate'       => $supervision,
                    ],
                    'pevCount' => $totalPevCount,
                ];
            }
        }

        // ══ Chart: Activities per day ══
        $this->loadChartActivitiesByDay();

        // ══ Chart: Lessons per day ══
        $this->loadChartLessonsByDay();

        // ══ Chart: Scheduled publications per day ══
        $this->loadChartScheduledByDay();

        // ══ Registration flow charts (global, with date range) ══
        $this->loadRegistrationFlowCharts();
    }

    /**
     * Resolve a range key ('7d' | '30d' | '3m' | 'all') into a start date.
     * Returns null for 'all' (no lower bound).
     */
    private function rangeStart(?string $range)
    {
        return match ($range) {
            '7d'  => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m'  => now()->subMonths(3)->startOfDay(),
            default => null,
        };
    }

    /**
     * Query activities grouped by finicial date, applying all current filters.
     * Returns array of {date: string, total: int} for the ApexCharts bar chart.
     */
    private function loadChartActivitiesByDay()
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) {
            $this->chartActivitiesByDay = [];
            return;
        }

        $query = Activity::selectRaw('activities.finicial, COUNT(*) as total')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at')
            ->groupBy('activities.finicial')
            ->orderBy('activities.finicial');

        // Filter by profesor
        if ($this->selectedProfesorId) {
            $query->where('pevaluacions.profesor_id', $this->selectedProfesorId);
        }

        // Filter by pestudio (and indirectly by peducativo via pestudio)
        if ($this->selectedPestudioId) {
            $query->where('pensums.pestudio_id', $this->selectedPestudioId);
        } elseif ($this->selectedPeducativoId) {
            $pestudioIds = $this->pestudios
                ->where('peducativo_id', $this->selectedPeducativoId)
                ->pluck('id');
            $query->whereIn('pensums.pestudio_id', $pestudioIds);
        }

        // Filter by grado (via pevaluacion → seccion → grado)
        if ($this->selectedGradoId) {
            $query->join('seccions', 'pevaluacions.seccion_id', '=', 'seccions.id')
                  ->where('seccions.grado_id', $this->selectedGradoId);
        }

        // Filter by date range
        if ($since = $this->rangeStart($this->chartActivitiesRange)) {
            $query->where('activities.finicial', '>=', $since->toDateString());
        }

        $this->chartActivitiesByDay = $query->get()->map(function ($row) {
            return [
                'x' => $row->finicial,
                'y' => (int) $row->total,
            ];
        })->toArray();
    }

    /**
     * Apply shared lapso + academic filters to a lesson chart query.
     * Works with both Eloquent Builder and Query Builder.
     */
    private function applyLessonChartFilters($query, $lapsoId)
    {
        $query
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at');

        if ($this->selectedProfesorId) {
            $query->where('pevaluacions.profesor_id', $this->selectedProfesorId);
        }
        if ($this->selectedPestudioId) {
            $query->where('pensums.pestudio_id', $this->selectedPestudioId);
        } elseif ($this->selectedPeducativoId) {
            $pestudioIds = $this->pestudios
                ->where('peducativo_id', $this->selectedPeducativoId)
                ->pluck('id');
            $query->whereIn('pensums.pestudio_id', $pestudioIds);
        }
        if ($this->selectedGradoId) {
            $query->join('seccions', 'pevaluacions.seccion_id', '=', 'seccions.id')
                  ->where('seccions.grado_id', $this->selectedGradoId);
        }

        return $query;
    }

    /**
     * Query lessons grouped by day, split into two series:
     *  1) Published lessons (status = 'PUBLISHED', grouped by published_at)
     *  2) Others (non-published OR no publication record, grouped by created_at)
     *
     * Uses Activity model (same as monitor) + LEFT JOIN so activities
     * without an lms_activity_publications row are included in "Otras".
     */
    private function loadChartLessonsByDay()
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) {
            $this->chartLessonsByDay = [];
            return;
        }

        $since = $this->rangeStart($this->chartLessonsRange);

        // ── Series 1: Published lessons (status = 'PUBLISHED') ──
        $published = $this->applyLessonChartFilters(
            Activity::query()->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')->withLmsContent(),
            $lapsoId
        )
            ->where('lms_activity_publications.status', 'PUBLISHED')
            ->when($since, fn ($q) => $q->where('lms_activity_publications.published_at', '>=', $since))
            ->selectRaw('DATE(lms_activity_publications.published_at) as date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.published_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // ── Series 2: Scheduled lessons (status != 'PUBLISHED', publish_at IS NOT NULL) ──
        $scheduled = $this->applyLessonChartFilters(
            Activity::query()->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')->withLmsContent(),
            $lapsoId
        )
            ->whereNotNull('lms_activity_publications.publish_at')
            ->where('lms_activity_publications.status', '!=', 'PUBLISHED')
            ->when($since, fn ($q) => $q->where('lms_activity_publications.publish_at', '>=', $since))
            ->selectRaw('DATE(lms_activity_publications.publish_at) as date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.publish_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // ── Series 3: Drafts (publish_at IS NULL, status != 'PUBLISHED' OR null) ──
        $drafts = $this->applyLessonChartFilters(
            Activity::query()->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')->withLmsContent(),
            $lapsoId
        )
            ->whereNull('lms_activity_publications.publish_at')
            ->where(function ($q) {
                $q->whereNull('lms_activity_publications.status')
                  ->orWhere('lms_activity_publications.status', '!=', 'PUBLISHED');
            })
            ->when($since, fn ($q) => $q->whereRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) >= ?', [$since->toDateString()]))
            ->selectRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) as date, COUNT(*) as total')
            ->groupByRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at))')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // ── Merge all unique dates sorted ──
        $allDates = collect(array_merge(
            $published->keys()->toArray(),
            $scheduled->keys()->toArray(),
            $drafts->keys()->toArray()
        ))->unique()->sort()->values();

        $this->chartLessonsByDay = [
            'categories' => $allDates->toArray(),
            'series'     => [
                [
                    'name' => 'Publicadas',
                    'data' => $allDates->map(fn($d) => (int) ($published[$d]->total ?? 0))->toArray(),
                ],
                [
                    'name' => 'Programadas',
                    'data' => $allDates->map(fn($d) => (int) ($scheduled[$d]->total ?? 0))->toArray(),
                ],
                [
                    'name' => 'Borradores',
                    'data' => $allDates->map(fn($d) => (int) ($drafts[$d]->total ?? 0))->toArray(),
                ],
            ],
        ];
    }

    /**
     * Query lms_activity_publications grouped by publish_at (scheduled date),
     * joining through activities → pevaluacions → pensums for filters.
     */
    private function loadChartScheduledByDay()
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) {
            $this->chartScheduledByDay = [];
            return;
        }

        $query = DB::query()
            ->from('lms_activity_publications')
            ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNotNull('lms_activity_publications.publish_at')
            ->selectRaw('DATE(lms_activity_publications.publish_at) as pub_date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.publish_at)')
            ->orderBy('pub_date');

        // Apply filters
        if ($this->selectedProfesorId) {
            $query->where('pevaluacions.profesor_id', $this->selectedProfesorId);
        }
        if ($this->selectedPestudioId) {
            $query->where('pensums.pestudio_id', $this->selectedPestudioId);
        } elseif ($this->selectedPeducativoId) {
            $pestudioIds = $this->pestudios
                ->where('peducativo_id', $this->selectedPeducativoId)
                ->pluck('id');
            $query->whereIn('pensums.pestudio_id', $pestudioIds);
        }
        if ($this->selectedGradoId) {
            $query->join('seccions', 'pevaluacions.seccion_id', '=', 'seccions.id')
                  ->where('seccions.grado_id', $this->selectedGradoId);
        }

        // Filter by date range
        if ($since = $this->rangeStart($this->chartScheduledRange)) {
            $query->where('lms_activity_publications.publish_at', '>=', $since);
        }

        $this->chartScheduledByDay = $query->get()->map(function ($row) {
            return [
                'x' => $row->pub_date,
                'y' => (int) $row->total,
            ];
        })->toArray();
    }

    /**
     * KPIs globales del dashboard (no dependen del lapso ni de los filtros).
     */
    private function loadGlobalKpis(): void
    {
        $this->totalActivities = Activity::count();
        $this->totalProfesoresActivos = Profesor::where('status_active', 'true')
            ->has('pevaluacions')
            ->count();
        $this->totalDiagActive = DiagMain::where('active', true)->count();

        $this->loadLessonStats();
    }

    /**
     * Lecciones globales (sin filtro de lapso).
     *
     * Total = actividades con al menos una sección o algún recurso asociado
     * (`Activity::withLmsContent()`), la misma lógica del monitor LMS.
     * Publicadas/Programadas = publicaciones por estado, sin scope de lapso.
     */
    private function loadLessonStats(): void
    {
        $this->lessonTotal = Activity::withLmsContent()->count();

        $this->lessonPublished = \App\Models\app\Academy\Lms\LmsActivityPublication::query()
            ->where('status', 'PUBLISHED')
            ->whereNotNull('published_at')
            ->count();
        $this->lessonScheduled = \App\Models\app\Academy\Lms\LmsActivityPublication::query()
            ->whereNotNull('publish_at')
            ->where('status', '!=', 'PUBLISHED')
            ->count();

        $this->lessonPublishedPct = $this->lessonTotal > 0 ? round(($this->lessonPublished / $this->lessonTotal) * 100, 1) : 0;
        $this->lessonScheduledPct = $this->lessonTotal > 0 ? round(($this->lessonScheduled / $this->lessonTotal) * 100, 1) : 0;
    }

    /**
     * Date-range handlers for the per-chart filters (Tab 1 bento charts).
     */
    public function updatedChartActivitiesRange()
    {
        $this->loadChartActivitiesByDay();
    }

    public function updatedChartLessonsRange()
    {
        $this->loadChartLessonsByDay();
    }

    public function updatedChartScheduledRange()
    {
        $this->loadChartScheduledByDay();
    }

    /**
     * Load registration flow charts (activities/lessons/diagnostics by created_at).
     * Global scope — not filtered by lapso; uses $this->registrationRange for date window.
     */
    public function updatedRegistrationRange()
    {
        $this->loadRegistrationFlowCharts();
    }

    private function loadRegistrationFlowCharts()
    {
        $since = match ($this->registrationRange) {
            '7d'  => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m'  => now()->subMonths(3)->startOfDay(),
            'all' => null,
            default => now()->subDays(7)->startOfDay(),
        };

        // ── Activities flow ──
        $query = \App\Models\app\Academy\Activity::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date');
        if ($since) {
            $query->where('created_at', '>=', $since);
        }
        $this->chartActivitiesFlow = $query->get()->map(fn ($r) => [
            'x' => $r->date,
            'y' => (int) $r->total,
        ])->toArray();

        // ── Lessons flow (same logic as loadChartLessonsByDay — published/scheduled/drafts — global scope) ──
        // Solo se cuentan lecciones con contenido LMS (al menos una sección o recurso
        // asociado), igual que el KPI de "Total de Lecciones" (Activity::withLmsContent()).
        $merged = collect();

        // Published (by published_at)
        $pubQuery = Activity::query()
            ->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
            ->withLmsContent()
            ->where('lms_activity_publications.status', 'PUBLISHED')
            ->whereNotNull('lms_activity_publications.published_at')
            ->selectRaw('DATE(lms_activity_publications.published_at) as date, COUNT(*) as total')
            ->groupBy('date');
        if ($since) $pubQuery->where('lms_activity_publications.published_at', '>=', $since);
        foreach ($pubQuery->get() as $r) {
            $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
        }

        // Scheduled (by publish_at, not published)
        $schQuery = Activity::query()
            ->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
            ->withLmsContent()
            ->whereNotNull('lms_activity_publications.publish_at')
            ->where('lms_activity_publications.status', '!=', 'PUBLISHED')
            ->selectRaw('DATE(lms_activity_publications.publish_at) as date, COUNT(*) as total')
            ->groupBy('date');
        if ($since) $schQuery->where('lms_activity_publications.publish_at', '>=', $since);
        foreach ($schQuery->get() as $r) {
            $merged->push(['date' => $r->date, 'total' => (int) $r->total]);
        }

        // Drafts — use Activity + LEFT JOIN (matching loadChartLessonsByDay's Borradores logic),
        // to catch activities WITHOUT any publication record that the DB::table approach misses.
        $drfQuery = Activity::query()
            ->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
            ->withLmsContent()
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

        // ── Diagnostics flow ──
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

    public function render()
    {
        return view('livewire.planning.indicator.index-component');
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
