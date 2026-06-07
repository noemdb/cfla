<?php

namespace App\Livewire\Planning\Indicator;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Boletin;
use App\Models\app\Academy\Evaluacion;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Learner\Estudiant;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

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

    // ─── Tab 4: Planes de Evaluación (per peducativo summary) ────────────────────────
    public $tab4Data = [];

    // ─── Totals for global KPI boxes ───────────────────────────────────
    public $totalInscritos = 0;
    public $totalPevaluacions = 0;
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;

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

            $totalInscritos = 0;
            $totalPevaluacions = 0;
            $totalActivities = 0;
            $totalProfesores = collect();

            foreach ($pestudios as $pestudio) {
                $totalInscritos += $pestudio->getInscritosCount($lapsoId);
                $totalPevaluacions += $pestudio->getPevaluacionsCount($lapsoId);
                $totalActivities += $pestudio->getActivitiesCount($lapsoId);
                $totalProfesores = $totalProfesores->merge(
                    $pestudio->getProfesors($lapsoId)
                );
            }

            return (object) [
                'peducativo' => $peducativo,
                'pestudios' => $pestudios,
                'inscritos' => $totalInscritos,
                'pevaluacions_count' => $totalPevaluacions,
                'activities_count' => $totalActivities,
                'profesores_count' => $totalProfesores->unique('id')->count(),
            ];
        });

        $this->totalInscritos = $this->peducativoMainIndicators->sum('inscritos');
        $this->totalPevaluacions = $this->peducativoMainIndicators->sum('pevaluacions_count');
        $this->totalActivities = $this->peducativoMainIndicators->sum('activities_count');
        $this->totalProfesoresActivos = Profesor::where('status_active', 'true')->count();

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

        // ══ TAB 4: Evaluation plans summary per peducativo ══
        $this->tab4Data = $filteredPeducativos->map(function ($peducativo) use ($lapsoId) {
            $pestudios = $this->getPestudiosForPeducativo($peducativo->id);

            $pevCount = 0;
            $actCount = 0;
            $boletinsCount = 0;

            foreach ($pestudios as $pestudio) {
                $pevCount += $pestudio->getPevaluacionsCount($lapsoId);
                $actCount += $pestudio->getActivitiesCount($lapsoId);
                try {
                    $boletinsCount += $pestudio->getBoletins($lapsoId)->count();
                } catch (\Exception $e) {
                    // Silently handle missing boletin data
                }
            }

            $avgPerPlan = $pevCount > 0 ? round($actCount / $pevCount, 1) : 0;

            return (object) [
                'peducativo' => $peducativo,
                'pev_count' => $pevCount,
                'act_count' => $actCount,
                'avg_per_plan' => $avgPerPlan,
                'total' => $pevCount + $actCount,
                'boletins_count' => $boletinsCount,
            ];
        });
    }

    public function render()
    {
        return view('livewire.planning.indicator.index-component');
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
