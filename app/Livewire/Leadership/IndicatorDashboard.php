<?php

namespace App\Livewire\Leadership;

use App\Models\User;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Activity;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagSession;
use App\Services\Leadership\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndicatorDashboard extends Component
{
    public $activeTab = 1;
    public $selectedLapsoId;

    // ─── Filters ─────────────────────────────────────────────────────
    public $selectedPeducativoId = null;
    public $selectedPestudioId = null;
    public $selectedGradoId = null;
    public $selectedSeccionId = null;

    // Master data (scoped to leadership areas)
    public $pestudios;
    public $filteredPestudios;
    public $peducativos;
    public $lapsos;
    public $lapsoActive;
    public $seccionesOptions = [];
    public $gradosOptions = [];

    // Tab 1: Main indicators per peducativo
    public $peducativoMainIndicators = [];

    // Tab 2: Profesores data
    public $tab2Data = [];

    // Tab 3: Activity indicators
    public $tab3Data = [];

    // Tab 4: Lesson indicators
    public $tab4Data = [];

    // Global KPI boxes
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;
    public $totalDiagActive = 0;

    // Charts
    public $chartActivitiesByDay = [];
    public $chartLessonsByDay = [];
    public $chartScheduledByDay = [];

    // Lesson stats (scoped by selected lapso)
    public $lessonTotal = 0;
    public $lessonScheduled = 0;
    public $lessonPublished = 0;
    public $lessonPublishedPct = 0;
    public $lessonScheduledPct = 0;

    // Registration flow charts
    public $registrationRange = '7d';
    public $chartActivitiesFlow = [];
    public $chartLessonsFlow = [];
    public $chartDiagnosticsFlow = [];

    // ─── Areas/Pensums Modal ──────────────────────────────────────────
    public $showAreasPensumsModal = false;
    public $areasPensumsData = [];

    private LeadershipService $leadershipService;

    /** Cached asignatura_ids for scoping (public so Livewire persists across requests) */
    public $asignaturaIds = [];

    /** Admin bypass flag — admins see everything */
    public $isAdmin = false;

    public function mount()
    {
        $this->isAdmin = Auth::user()->is_admin;
        $this->leadershipService = app(LeadershipService::class, [
            'user' => Auth::user()
        ]);

        if ($this->isAdmin) {
            // Admin sees ALL planning-related asignatura IDs
            $this->asignaturaIds = \App\Models\app\Academy\Asignatura::whereHas(
                'pensums.pestudio', fn($q) => $q->where('planning_module', true)
            )->pluck('id')->toArray();
        } else {
            $this->asignaturaIds = $this->leadershipService->getAssignedAsignaturaIds();
            if ($this->asignaturaIds->isEmpty()) {
                // No areas assigned — show empty state
                return;
            }
        }

        // Scoped pestudios: only those with pensums under the leader's asignaturas
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->where('planning_module', 1)
            ->whereHas('grados.pensums', function ($q) {
                $q->whereIn('asignatura_id', $this->asignaturaIds);
            })
            ->orderBy('order')
            ->get();

        $planningPeducativoIds = $this->pestudios->pluck('peducativo_id')->unique()->values();
        $this->peducativos = Peducativo::where('status_active', 'true')
            ->whereIn('id', $planningPeducativoIds)
            ->orderBy('order')
            ->get();

        $this->lapsos = Lapso::orderBy('id')->get();
        $this->lapsoActive = Lapso::current();
        $this->selectedLapsoId = $this->lapsoActive?->id ?? $this->lapsos->first()?->id;

        // Init cascading filter options
        $this->refreshFilteredPestudios();
        $this->refreshGradosOptions();
        $this->refreshSeccionesOptions();
        $this->loadAreasPensumsData();
        $this->loadAllData();
    }

    /**
     * Recarga datos del modal de áreas/pensums.
     */
    public function loadAreasPensumsData()
    {
        $query = AreaConocimiento::with([
            'peducativo',
            'campo_conocimientos.asignatura' => function ($q) {
                $q->orderBy('name');
            },
            'campo_conocimientos.asignatura.pensums.grado',
            'campo_conocimientos.asignatura.pensums.pestudio',
        ])->orderBy('name');

        if ($this->isAdmin) {
            // Admin sees all areas that have planning-related asignaturas
            $query->whereHas('campo_conocimientos.asignatura.pensums.pestudio', fn($q) => $q->where('planning_module', true));
        } else {
            $query->where('leader_id', Auth::id());
        }

        $this->areasPensumsData = $query->get()->toArray();
    }

    /**
     * Abre el modal de áreas/pensums y refresca los datos.
     */
    public function openAreasPensumsModal()
    {
        $this->loadAreasPensumsData();
        $this->showAreasPensumsModal = true;
    }

    // ─── Filter change handlers ──────────────────────────────────────
    public function updatedSelectedLapsoId() { $this->loadAllData(); }
    public function updatedSelectedPeducativoId()
    {
        $this->selectedPestudioId = null;
        $this->selectedGradoId = null;
        $this->selectedSeccionId = null;
        $this->refreshFilteredPestudios();
        $this->refreshGradosOptions();
        $this->refreshSeccionesOptions();
        $this->loadAllData();
    }
    public function updatedSelectedPestudioId()
    {
        $this->selectedGradoId = null;
        $this->selectedSeccionId = null;
        $this->refreshGradosOptions();
        $this->refreshSeccionesOptions();
        $this->loadAllData();
    }
    public function updatedSelectedSeccionId() { $this->loadAllData(); }
    public function updatedSelectedGradoId()
    {
        $this->selectedSeccionId = null;
        $this->refreshSeccionesOptions();
        $this->loadAllData();
    }
    public function switchTab($tab) { $this->activeTab = $tab; }

    private function refreshGradosOptions()
    {
        if (!$this->isAdmin && empty($this->asignaturaIds)) {
            $this->gradosOptions = [];
            return;
        }
        $query = Grado::where('status_active', 'true')
            ->whereHas('pensums', function ($q) {
                $q->whereIn('asignatura_id', $this->asignaturaIds);
            })
            ->orderBy('order');

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
     * Filtra pestudios según el peducativo seleccionado (cascading).
     * Si no hay peducativo seleccionado, muestra todos los pestudios scoped.
     */
    private function refreshFilteredPestudios()
    {
        $pestudios = $this->pestudios;
        if ($this->selectedPeducativoId) {
            $pestudios = $pestudios->where('peducativo_id', $this->selectedPeducativoId);
        }
        $this->filteredPestudios = $pestudios->values();
    }

    /**
     * Secciones activas del grado seleccionado (cascading).
     */
    private function refreshSeccionesOptions()
    {
        if ($this->selectedGradoId) {
            $this->seccionesOptions = Seccion::where('status_active', true)
                ->where('grado_id', $this->selectedGradoId)
                ->orderBy('name')
                ->get(['id', 'name', 'grado_id']);
        } else {
            $this->seccionesOptions = [];
        }
    }

    /** Scope a pevaluacion query by the leader's asignatura_ids */
    private function scopePevaQuery($query)
    {
        if ($this->isAdmin) return $query;
        return $query->whereHas('pensum', function ($q) {
            $q->whereIn('asignatura_id', $this->asignaturaIds);
        });
    }

    /** Scope a query that already joins pensums by the leader's asignatura_ids */
    private function scopePensumJoin($query)
    {
        if ($this->isAdmin) return $query;
        return $query->whereIn('pensums.asignatura_id', $this->asignaturaIds);
    }

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

    private function getPestudiosForPeducativo($peducativoId)
    {
        return $this->getBasePestudios()->where('peducativo_id', $peducativoId);
    }

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

    public function loadAllData()
    {
        if (!$this->isAdmin && empty($this->asignaturaIds)) {
            return; // No areas — keep empty state
        }
        $lapsoId = $this->selectedLapsoId;
        $filteredPeducativos = $this->getFilteredPeducativos();

        // ══ TAB 1: Main indicators per peducativo ══
        $this->peducativoMainIndicators = $filteredPeducativos->map(function ($peducativo) use ($lapsoId) {
            $pestudios = $this->getPestudiosForPeducativo($peducativo->id);

            $totalActivities = 0;
            $totalProfesores = collect();

            foreach ($pestudios as $pestudio) {
                $totalActivities += $this->getScopedActivitiesCount($pestudio->id, $lapsoId);
                $totalProfesores = $totalProfesores->merge(
                    $this->getScopedProfesores($pestudio->id, $lapsoId)
                );
            }

            $pestudioIds = $pestudios->pluck('id');

            $lessonsCount = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                ->whereIn('pensums.pestudio_id', $pestudioIds)
                ->where('pevaluacions.lapso_id', $lapsoId)
                ->whereNull('pevaluacions.deleted_at')
                ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
                ->count(DB::raw('DISTINCT activities.id'));

            $gradosCount = DB::table('grados')
                ->whereIn('pestudio_id', $pestudioIds)
                ->whereNull('deleted_at')
                ->count();

            $pensumsCount = DB::table('pensums')
                ->whereIn('pestudio_id', $pestudioIds)
                ->whereIn('asignatura_id', $this->asignaturaIds)
                ->whereNull('deleted_at')
                ->count();

            return (object) [
                'peducativo' => $peducativo,
                'pestudios' => $pestudios,
                'activities_count' => $totalActivities,
                'profesores_count' => $totalProfesores->unique('id')->count(),
                'lessons_count' => $lessonsCount,
                'grados_count' => $gradosCount,
                'pensums_count' => $pensumsCount,
            ];
        });

        $this->totalActivities = $this->peducativoMainIndicators->sum('activities_count');
        $this->totalProfesoresActivos = $this->getScopedProfesoresConCarga();

        // ══ TAB 2: Profesores data ══
        $this->tab2Data = [];
        $tab2Lapso = $this->lapsos->firstWhere('id', $this->selectedLapsoId);
        if ($tab2Lapso) {
            foreach ($filteredPeducativos as $peducativo) {
                $pestudios = $this->getPestudiosForPeducativo($peducativo->id);
                $allProfesors = collect();
                $totalBoletinsPROM = 0;
                $profesorCount = 0;

                foreach ($pestudios as $pestudio) {
                    $profs = $this->getScopedProfesorsWithKPIs($pestudio->id, $tab2Lapso->id, $this->selectedSeccionId);
                    $allProfesors = $allProfesors->merge($profs);
                    $pestIeeProm = $this->getScopedProfesoresIEEsPROM($pestudio->id, $tab2Lapso->id, $this->selectedSeccionId);
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

        // ══ TAB 3: Activity indicators ══
        $this->tab3Data = [];
        $tab3Lapso = $this->lapsos->firstWhere('id', $this->selectedLapsoId);
        if ($tab3Lapso) {
            foreach ($filteredPeducativos as $peducativo) {
                $pestudios = $this->getPestudiosForPeducativo($peducativo->id);
                $pestudioIds = $pestudios->pluck('id');

                $pevIds = Pevaluacion::whereNull('pevaluacions.deleted_at')
                    ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                    ->whereIn('pensums.pestudio_id', $pestudioIds)
                    ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
                    ->where('pevaluacions.lapso_id', $tab3Lapso->id)
                    ->pluck('pevaluacions.id');

                $totalPevCount = $pevIds->count();
                $allActivities = Activity::whereIn('pevaluacion_id', $pevIds)->get();
                $totalActivities = $allActivities->count();

                $totalTeachers = 0;
                $totalActiveTeachers = 0;
                foreach ($pestudios as $pestudio) {
                    $totalTeachers += $this->getScopedTeachersCount($pestudio->id, $tab3Lapso->id);
                    $totalActiveTeachers += $this->getScopedActiveTeachersCount($pestudio->id, $tab3Lapso->id);
                }

                $cobertura = $totalPevCount > 0 ? round($totalActivities / $totalPevCount, 2) : 0;
                $participacion = $totalTeachers > 0 ? round(($totalActiveTeachers / $totalTeachers) * 100, 1) : 0;

                $withComments = $allActivities->filter(fn($a) => !empty($a->comments))->count();
                $approved = $allActivities->where('status', true)->count();
                $seguimiento = $totalActivities > 0 ? round(($withComments / $totalActivities) * 100, 1) : 0;
                $aprobacion = $totalActivities > 0 ? round(($approved / $totalActivities) * 100, 1) : 0;

                $pevWithObs = Pevaluacion::whereIn('id', $pevIds)
                    ->whereNotNull('observations')
                    ->where('observations', '<>', '')
                    ->count();
                $supervision = $totalPevCount > 0 ? round(($pevWithObs / $totalPevCount) * 100, 1) : 0;

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

        // ══ TAB 4: Lesson indicators (scoped to leader's asignaturas) ══
        $this->tab4Data = [];
        $tab4Lapso = $this->lapsos->firstWhere('id', $this->selectedLapsoId);
        if ($tab4Lapso) {
            foreach ($filteredPeducativos as $peducativo) {
                $pestudios = $this->getPestudiosForPeducativo($peducativo->id);
                $pestudioIds = $pestudios->pluck('id');

                $pevIds = Pevaluacion::whereNull('pevaluacions.deleted_at')
                    ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                    ->whereIn('pensums.pestudio_id', $pestudioIds)
                    ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
                    ->where('pevaluacions.lapso_id', $tab4Lapso->id)
                    ->pluck('pevaluacions.id');

                $totalPevCount = $pevIds->count();

                $lessons = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
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
                $published = $lessons->filter(fn($l) => $l->pub_status === 'PUBLISHED')->count();
                $scheduled = $lessons->filter(fn($l) => !is_null($l->publish_at) && $l->pub_status !== 'PUBLISHED')->count();
                $drafts = $totalLessons - $published - $scheduled;

                $publishedPct = $totalLessons > 0 ? round(($published / $totalLessons) * 100, 1) : 0;
                $scheduledPct = $totalLessons > 0 ? round(($scheduled / $totalLessons) * 100, 1) : 0;
                $draftPct = $totalLessons > 0 ? round(($drafts / $totalLessons) * 100, 1) : 0;
                $avgPerPev = $totalPevCount > 0 ? round($totalLessons / $totalPevCount, 2) : 0;

                $pevIdsWithLessons = $lessons->pluck('pevaluacion_id')->unique();
                $profIdsWithLessons = Pevaluacion::whereIn('id', $pevIdsWithLessons)
                    ->whereNotNull('profesor_id')
                    ->distinct()
                    ->count('profesor_id');

                $totalTeachers = 0;
                foreach ($pestudios as $pestudio) {
                    $totalTeachers += $this->getScopedTeachersCount($pestudio->id, $tab4Lapso->id);
                }
                $teachersParticipation = $totalTeachers > 0 ? round(($profIdsWithLessons / $totalTeachers) * 100, 1) : 0;

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

        // ══ Global: Diagnósticos activos (scoped to leader's pestudios) ══
        $pestudioIds = $this->getBasePestudios()->pluck('id');
        $this->totalDiagActive = DiagMain::where('active', true)
            ->where('lapso_id', $this->selectedLapsoId)
            ->whereHas('pestudio', fn($q) => $q->whereIn('id', $pestudioIds))
            ->count();

        // ══ Charts y lesson stats ══
        $this->loadChartActivitiesByDay();
        $this->loadChartLessonsByDay();
        $this->loadChartScheduledByDay();
        $this->loadLessonStats($this->selectedLapsoId);
        $this->loadRegistrationFlowCharts();
    }

    // ─── Scoped helper queries ───────────────────────────────────────

    private function getScopedActivitiesCount(int $pestudioId, ?int $lapsoId): int
    {
        $query = Pevaluacion::whereNull('pevaluacions.deleted_at')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
            ->where('pensums.pestudio_id', $pestudioId)
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds);

        if ($lapsoId) {
            $query->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $query->count();
    }

    private function getScopedProfesores(int $pestudioId, ?int $lapsoId, ?int $seccionId = null)
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $pestudioId)
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->when($seccionId, fn($q) => $q->where('pevaluacions.seccion_id', $seccionId))
            ->distinct();

        if ($lapsoId) {
            $profesors = $profesors->where('pevaluacions.lapso_id', $lapsoId);
        }

        return $profesors->get();
    }

    private function getScopedProfesoresIEEsPROM(int $pestudioId, ?int $lapsoId, ?int $seccionId = null): float
    {
        $profesors = $this->getScopedProfesores($pestudioId, $lapsoId, $seccionId);
        if ($profesors->isEmpty()) return 0;

        $totalBoletins = 0;
        foreach ($profesors as $profesor) {
            $totalBoletins += $profesor->getBoletins($lapsoId, $pestudioId)->count();
        }
        return $totalBoletins / $profesors->count();
    }

    private function getScopedProfesorsWithKPIs(int $pestudioId, ?int $lapsoId, ?int $seccionId = null)
    {
        $profesors = Profesor::where('profesors.status_active', 'true')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $pestudioId)
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
            ->whereNull('pevaluacions.deleted_at')
            ->when($lapsoId, fn($q) => $q->where('pevaluacions.lapso_id', $lapsoId))
            ->when($seccionId, fn($q) => $q->where('pevaluacions.seccion_id', $seccionId))
            ->select('profesors.id', 'profesors.name', 'profesors.lastname', 'profesors.ci_profesor')
            ->distinct()
            ->get();

        $ieePROM = $this->getScopedProfesoresIEEsPROM($pestudioId, $lapsoId, $seccionId);

        return $profesors->map(function ($profesor) use ($lapsoId, $pestudioId, $ieePROM) {
            $fullProfesor = Profesor::find($profesor->id);

            $pevIds = Pevaluacion::where('profesor_id', $profesor->id)
                ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                ->where('pensums.pestudio_id', $pestudioId)
                ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
                ->when($lapsoId, fn($q) => $q->where('pevaluacions.lapso_id', $lapsoId))
                ->pluck('pevaluacions.id');

            $activitiesCount = Activity::whereIn('pevaluacion_id', $pevIds)->count();
            $approvedActivities = Activity::whereIn('pevaluacion_id', $pevIds)
                ->where('status', true)
                ->count();
            $approvalRate = $activitiesCount > 0 ? round(($approvedActivities / $activitiesCount) * 100, 1) : 0;

            $lessonsCount = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                ->whereIn('activities.pevaluacion_id', $pevIds)
                ->count(DB::raw('DISTINCT activities.id'));

            return (object) [
                'id' => $profesor->id,
                'full_name' => ($profesor->lastname ?? '') . ' ' . ($profesor->name ?? ''),
                'ci_profesor' => $profesor->ci_profesor ?? '',
                'activities_count' => $activitiesCount,
                'approval_rate' => $approvalRate,
                'lessons_count' => $lessonsCount,
            ];
        });
    }

    private function getScopedTeachersCount(int $pestudioId, ?int $lapsoId): int
    {
        $query = Profesor::select('profesors.id')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $pestudioId)
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
            ->whereNull('pevaluacions.deleted_at');
        if ($lapsoId) $query->where('pevaluacions.lapso_id', $lapsoId);
        return $query->distinct('profesors.id')->count('profesors.id');
    }

    private function getScopedActiveTeachersCount(int $pestudioId, ?int $lapsoId): int
    {
        $query = Profesor::select('profesors.id')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pensums.pestudio_id', $pestudioId)
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
            ->whereNull('pevaluacions.deleted_at')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('activities')
                    ->whereColumn('activities.pevaluacion_id', 'pevaluacions.id');
            });
        if ($lapsoId) $query->where('pevaluacions.lapso_id', $lapsoId);
        return $query->distinct('profesors.id')->count('profesors.id');
    }

    private function getScopedProfesoresConCarga(): int
    {
        return Profesor::where('status_active', 'true')
            ->whereHas('pevaluacions.pensum', function ($q) {
                $q->whereIn('asignatura_id', $this->asignaturaIds);
            })
            ->count();
    }

    // ─── Chart queries (all scoped) ──────────────────────────────────

    private function applyLessonChartFilters($query, $lapsoId)
    {
        $query
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at')
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds);

        if ($this->selectedSeccionId) {
            $query->where('pevaluacions.seccion_id', $this->selectedSeccionId);
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

    private function loadChartActivitiesByDay()
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) { $this->chartActivitiesByDay = []; return; }

        $query = Activity::selectRaw('activities.finicial, COUNT(*) as total')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at')
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
            ->groupBy('activities.finicial')
            ->orderBy('activities.finicial');

        if ($this->selectedSeccionId) {
            $query->where('pevaluacions.seccion_id', $this->selectedSeccionId);
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

        $this->chartActivitiesByDay = $query->get()->map(fn($r) => [
            'x' => $r->finicial,
            'y' => (int) $r->total,
        ])->toArray();
    }

    private function loadChartLessonsByDay()
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) { $this->chartLessonsByDay = []; return; }

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

        $scheduled = $this->applyLessonChartFilters(
            Activity::query()->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id'),
            $lapsoId
        )
            ->whereNotNull('lms_activity_publications.publish_at')
            ->where('lms_activity_publications.status', '!=', 'PUBLISHED')
            ->selectRaw('DATE(lms_activity_publications.publish_at) as date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.publish_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $drafts = $this->applyLessonChartFilters(
            Activity::query()->leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id'),
            $lapsoId
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
            'series' => [
                ['name' => 'Publicadas', 'data' => $allDates->map(fn($d) => (int) ($published[$d]->total ?? 0))->toArray()],
                ['name' => 'Programadas', 'data' => $allDates->map(fn($d) => (int) ($scheduled[$d]->total ?? 0))->toArray()],
                ['name' => 'Borradores', 'data' => $allDates->map(fn($d) => (int) ($drafts[$d]->total ?? 0))->toArray()],
            ],
        ];
    }

    private function loadChartScheduledByDay()
    {
        $lapsoId = $this->selectedLapsoId;
        if (!$lapsoId) { $this->chartScheduledByDay = []; return; }

        $query = DB::query()
            ->from('lms_activity_publications')
            ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->whereNull('pevaluacions.deleted_at')
            ->whereIn('pensums.asignatura_id', $this->asignaturaIds)
            ->whereNotNull('lms_activity_publications.publish_at')
            ->selectRaw('DATE(lms_activity_publications.publish_at) as pub_date, COUNT(*) as total')
            ->groupByRaw('DATE(lms_activity_publications.publish_at)')
            ->orderBy('pub_date');

        if ($this->selectedSeccionId) $query->where('pevaluacions.seccion_id', $this->selectedSeccionId);
        if ($this->selectedPestudioId) $query->where('pensums.pestudio_id', $this->selectedPestudioId);
        elseif ($this->selectedPeducativoId) {
            $pestudioIds = $this->pestudios->where('peducativo_id', $this->selectedPeducativoId)->pluck('id');
            $query->whereIn('pensums.pestudio_id', $pestudioIds);
        }
        if ($this->selectedGradoId) {
            $query->join('seccions', 'pevaluacions.seccion_id', '=', 'seccions.id')
                  ->where('seccions.grado_id', $this->selectedGradoId);
        }

        $this->chartScheduledByDay = $query->get()->map(fn($r) => [
            'x' => $r->pub_date,
            'y' => (int) $r->total,
        ])->toArray();
    }

    private function loadLessonStats(?int $lapsoId = null)
    {
        $asignaturaIds = $this->asignaturaIds;

        $lapsoScope = function ($q) use ($lapsoId) {
            if ($lapsoId) {
                $q->whereHas('activity.pevaluacion', fn ($pq) => $pq->where('lapso_id', $lapsoId));
            }
        };

        $this->lessonPublished = \App\Models\app\Academy\Lms\LmsActivityPublication::where('status', 'PUBLISHED')
            ->whereNotNull('published_at')
            ->tap($lapsoScope)
            ->whereHas('activity.pevaluacion.pensum', fn($q) => $q->whereIn('asignatura_id', $asignaturaIds))
            ->count();

        $this->lessonScheduled = \App\Models\app\Academy\Lms\LmsActivityPublication::whereNotNull('publish_at')
            ->where('status', '!=', 'PUBLISHED')
            ->tap($lapsoScope)
            ->whereHas('activity.pevaluacion.pensum', fn($q) => $q->whereIn('asignatura_id', $asignaturaIds))
            ->count();

        $draftsQuery = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereNull('lms_activity_publications.publish_at')
            ->where(function ($q) {
                $q->whereNull('lms_activity_publications.status')
                  ->orWhere('lms_activity_publications.status', '!=', 'PUBLISHED');
            })
            ->whereIn('pensums.asignatura_id', $asignaturaIds)
            ->whereNull('pevaluacions.deleted_at');

        if ($lapsoId) {
            $draftsQuery->where('pevaluacions.lapso_id', $lapsoId);
        }

        $draftsCount = $draftsQuery->count(DB::raw('DISTINCT activities.id'));

        $this->lessonTotal = $this->lessonPublished + $this->lessonScheduled + $draftsCount;
        $this->lessonPublishedPct = $this->lessonTotal > 0 ? round(($this->lessonPublished / $this->lessonTotal) * 100, 1) : 0;
        $this->lessonScheduledPct = $this->lessonTotal > 0 ? round(($this->lessonScheduled / $this->lessonTotal) * 100, 1) : 0;
    }

    public function updatedRegistrationRange() { $this->loadRegistrationFlowCharts(); }

    private function loadRegistrationFlowCharts()
    {
        $since = match ($this->registrationRange) {
            '7d'  => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m'  => now()->subMonths(3)->startOfDay(),
            'all' => null,
            default => now()->subDays(7)->startOfDay(),
        };
        $asignaturaIds = $this->asignaturaIds;

        // Activities flow
        $query = Activity::selectRaw('DATE(activities.created_at) as date, COUNT(*) as total')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.asignatura_id', $asignaturaIds)
            ->whereNull('pevaluacions.deleted_at')
            ->groupBy('date')
            ->orderBy('date');
        if ($since) $query->where('activities.created_at', '>=', $since);
        $this->chartActivitiesFlow = $query->get()->map(fn($r) => [
            'x' => $r->date,
            'y' => (int) $r->total,
        ])->toArray();

        // Lessons flow
        $merged = collect();

        $pubQuery = DB::table('lms_activity_publications')
            ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('lms_activity_publications.status', 'PUBLISHED')
            ->whereNotNull('lms_activity_publications.published_at')
            ->whereIn('pensums.asignatura_id', $asignaturaIds)
            ->whereNull('pevaluacions.deleted_at')
            ->selectRaw('DATE(lms_activity_publications.published_at) as date, COUNT(*) as total')
            ->groupBy('date');
        if ($since) $pubQuery->where('lms_activity_publications.published_at', '>=', $since);
        foreach ($pubQuery->get() as $r) $merged->push(['date' => $r->date, 'total' => (int) $r->total]);

        $schQuery = DB::table('lms_activity_publications')
            ->join('activities', 'lms_activity_publications.activity_id', '=', 'activities.id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereNotNull('lms_activity_publications.publish_at')
            ->where('lms_activity_publications.status', '!=', 'PUBLISHED')
            ->whereIn('pensums.asignatura_id', $asignaturaIds)
            ->whereNull('pevaluacions.deleted_at')
            ->selectRaw('DATE(lms_activity_publications.publish_at) as date, COUNT(*) as total')
            ->groupBy('date');
        if ($since) $schQuery->where('lms_activity_publications.publish_at', '>=', $since);
        foreach ($schQuery->get() as $r) $merged->push(['date' => $r->date, 'total' => (int) $r->total]);

        $drfQuery = Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereNull('lms_activity_publications.publish_at')
            ->where(function ($q) {
                $q->whereNull('lms_activity_publications.status')
                  ->orWhere('lms_activity_publications.status', '!=', 'PUBLISHED');
            })
            ->whereIn('pensums.asignatura_id', $asignaturaIds)
            ->whereNull('pevaluacions.deleted_at')
            ->selectRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) as date, COUNT(*) as total')
            ->groupByRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at))')
            ->orderBy('date');
        if ($since) $drfQuery->whereRaw('DATE(COALESCE(lms_activity_publications.created_at, activities.created_at)) >= ?', [$since]);
        foreach ($drfQuery->get() as $r) $merged->push(['date' => $r->date, 'total' => (int) $r->total]);

        $this->chartLessonsFlow = $merged
            ->groupBy('date')
            ->map(fn($items, $date) => ['x' => $date, 'y' => $items->sum('total')])
            ->sortBy('x')
            ->values()
            ->toArray();

        // Diagnostics flow (same scoping via pestudio chain)
        $diagQuery = DiagSession::selectRaw('DATE(diag_sessions.created_at) as date, COUNT(*) as total')
            ->join('diag_mains', 'diag_sessions.diag_main_id', '=', 'diag_mains.id')
            ->where('diag_mains.lapso_id', $this->selectedLapsoId)
            ->whereHas('diagMain.pestudio', fn($q) => $q->whereIn('id', $this->getBasePestudios()->pluck('id')))
            ->groupBy('date')
            ->orderBy('date');
        if ($since) $diagQuery->where('diag_sessions.created_at', '>=', $since);
        $this->chartDiagnosticsFlow = $diagQuery->get()->map(fn($r) => [
            'x' => $r->date,
            'y' => (int) $r->total,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.leadership.indicator-dashboard', [
            'asignaturaIds' => $this->asignaturaIds,
            'isAdmin' => $this->isAdmin,
        ]);
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
