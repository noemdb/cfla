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
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagResult;
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

    // ─── Tab 4: Diagnóstico (aggregate indicators) ────────────────────────────────────
    public $tab4DiagData = [];

    // ─── Tab 4: Question-level indicators ──────────────────────────────
    public $diagTotalQuestions = 0;
    public $diagQuestionsWithOptions = 0;
    public $diagPensumCoveragePct = 0;

    // ─── Totals for global KPI boxes ───────────────────────────────────
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;
    public $totalDiagActive = 0;

    // ─── Charts ────────────────────────────────────────────────────────
    public $chartActivitiesByDay = [];
    public $chartLessonsByDay = [];
    public $chartScheduledByDay = [];

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
        $this->peducativoMainIndicators = $filteredPeducativos->map(function ($peducativo) use ($lapsoId) {
            $pestudios = $this->getPestudiosForPeducativo($peducativo->id);

            $totalActivities = 0;
            $totalProfesores = collect();

            foreach ($pestudios as $pestudio) {
                $totalActivities += $pestudio->getActivitiesCount($lapsoId);
                $totalProfesores = $totalProfesores->merge(
                    $pestudio->getProfesors($lapsoId)
                );
            }

            return (object) [
                'peducativo' => $peducativo,
                'pestudios' => $pestudios,
                'activities_count' => $totalActivities,
                'profesores_count' => $totalProfesores->unique('id')->count(),
            ];
        });

        $this->totalActivities = $this->peducativoMainIndicators->sum('activities_count');
        $this->totalProfesoresActivos = Profesor::where('status_active', 'true')
            ->has('pevaluacions')
            ->count();

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

        // ══ TAB 4: Diagnóstico — aggregate indicators ══
        $this->tab4DiagData = collect();
        $diagLapso = $this->lapsos->firstWhere('id', $this->selectedLapsoId);
        if ($diagLapso) {
            $diagMains = DiagMain::where('active', true)->where('lapso_id', $diagLapso->id)->get();
            $this->totalDiagActive = $diagMains->count();

            $this->tab4DiagData = $diagMains->map(function ($dm) use ($diagLapso) {
                $sessions = DiagSession::where('diag_main_id', $dm->id)
                    ->where('lapso_id', $diagLapso->id);
                $totalSessions = (clone $sessions)->count();
                $completedSessions = (clone $sessions)->whereNotNull('completado_at')->count();
                $studentsEvaluated = (clone $sessions)->distinct('estudiant_id')->count('estudiant_id');

                $reportIds = DiagReport::where('diag_main_id', $dm->id)
                    ->where('lapso_id', $diagLapso->id)->pluck('id');
                $avgPrecision = $reportIds->isNotEmpty()
                    ? round(DiagResult::whereIn('report_id', $reportIds)->avg('precision') ?? 0, 1)
                    : 0;

                $totalAnswered = DiagResult::whereIn('report_id', $reportIds)->sum('total_answered_questions');

                return (object) [
                    'diag_main' => $dm,
                    'total_sessions' => $totalSessions,
                    'completed_sessions' => $completedSessions,
                    'students_evaluated' => $studentsEvaluated,
                    'avg_precision' => $avgPrecision,
                    'total_answered' => $totalAnswered,
                ];
            });

            // ── Question-level aggregate indicators ──
            $activeDiagMainIds = $diagMains->pluck('id');

            $this->diagTotalQuestions = DiagQuestion::whereIn('diag_main_id', $activeDiagMainIds)
                ->whereNotNull('pregunta')
                ->where('pregunta', '<>', '')
                ->count();

            $this->diagQuestionsWithOptions = DiagQuestion::whereIn('diag_main_id', $activeDiagMainIds)
                ->whereHas('options', function ($q) {
                    $q->whereNotNull('opcion')->where('opcion', '<>', '');
                })
                ->count();

            $pensumsWithQuestions = DiagQuestion::whereIn('diag_main_id', $activeDiagMainIds)
                ->whereNotNull('pensum_id')
                ->distinct()
                ->count('pensum_id');

            $totalPensumsInScope = \App\Models\app\Academy\Pensum::whereIn('pestudio_id', $diagMains->pluck('pestudio_id')->filter())
                ->count();

            $this->diagPensumCoveragePct = $totalPensumsInScope > 0
                ? round(($pensumsWithQuestions / $totalPensumsInScope) * 100, 1)
                : 0;
        }

        // ══ Chart: Activities per day ══
        $this->loadChartActivitiesByDay();

        // ══ Chart: Lessons per day ══
        $this->loadChartLessonsByDay();

        // ══ Chart: Scheduled publications per day ══
        $this->loadChartScheduledByDay();
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

        // ── Series 1: Published lessons ──
        $published = $this->applyLessonChartFilters(
            Activity::query()->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id'),
            $lapsoId
        )
            ->where('lms_activity_publications.status', 'PUBLISHED')
            ->selectRaw('DATE(lms_activity_publications.published_at) as date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.published_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // ── Series 2: Other lessons (non-published OR no publication at all) ──
        $other = $this->applyLessonChartFilters(
            Activity::query()->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id'),
            $lapsoId
        )
            ->where(function ($q) {
                $q->whereNull('lms_activity_publications.status')
                  ->orWhere('lms_activity_publications.status', '!=', 'PUBLISHED');
            })
            ->selectRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) as date, COUNT(*) as total')
            ->groupByRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at))')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // ── Merge all unique dates sorted ──
        $allDates = collect(array_merge(
            $published->keys()->toArray(),
            $other->keys()->toArray()
        ))->unique()->sort()->values();

        $this->chartLessonsByDay = [
            'categories' => $allDates->toArray(),
            'series'     => [
                [
                    'name' => 'Publicadas',
                    'data' => $allDates->map(fn($d) => (int) ($published[$d]->total ?? 0))->toArray(),
                ],
                [
                    'name' => 'Otras',
                    'data' => $allDates->map(fn($d) => (int) ($other[$d]->total ?? 0))->toArray(),
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

        $this->chartScheduledByDay = $query->get()->map(function ($row) {
            return [
                'x' => $row->pub_date,
                'y' => (int) $row->total,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.planning.indicator.index-component');
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
