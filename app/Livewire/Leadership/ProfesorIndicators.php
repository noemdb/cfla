<?php

namespace App\Livewire\Leadership;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Profesor;
use App\Services\Leadership\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProfesorIndicators extends Component
{
    public $selectedProfesorId = null;
    public $selectedLapsoId = null;
    public $profesores = [];
    public $searchProfesor = '';

    // Charts
    public $activityRange = '7d';
    public $chartActivitiesFlow = [];
    public $chartLessonsFlow = [];
    public $chartStatusFlow = [];

    public function mount()
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $this->profesores = $service->getAssignedProfesores();
    }

    public function render()
    {
        $profesor = null;
        $kpi = null;
        $profesorInfo = null;
        $cargaAcademica = [];
        $resumenActividades = null;
        $resumenLecciones = collect();

        if ($this->selectedProfesorId) {
            $profesor = Profesor::find($this->selectedProfesorId);
            if ($profesor) {
                // KPIs existentes
                $kpi = [
                    'iee' => $profesor->getProfesorIEE($this->selectedLapsoId),
                    'ire' => $profesor->getProfesorIRE(
                        request()->input('pestudio_id'),
                        $this->selectedLapsoId
                    ),
                    'goal_notas' => $profesor->goal_notas_load($this->selectedLapsoId),
                    'real_notas' => $profesor->real_notas_load($this->selectedLapsoId),
                    'total_pevas' => $profesor->pevaluacions()
                        ->when($this->selectedLapsoId, fn($q) => $q->where('lapso_id', $this->selectedLapsoId))
                        ->count(),
                ];

                // D) Información general
                $profesorInfo = [
                    'full_name' => $profesor->full_name,
                    'ci' => $profesor->ti_teacher . ' ' . $profesor->ci_profesor,
                    'email' => $profesor->email,
                    'phone' => $profesor->phone,
                    'cellphone' => $profesor->cellphone,
                    'status_active' => $profesor->status_active,
                ];

                // B) Carga académica
                $pensums = $profesor->pensums()->get();
                $cargaAcademica['asignaturas'] = $pensums->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->asignatura_name,
                ]);

                // Secciones guía
                $seccionGuias = $profesor->getSeccionGuiasAttribute();
                $cargaAcademica['seccion_guias'] = $seccionGuias;

                // C) Resumen de actividades
                $actividadesQuery = Activity::whereHas('pevaluacion', function ($q) use ($profesor) {
                    $q->where('profesor_id', $profesor->id);
                    if ($this->selectedLapsoId) {
                        $q->where('lapso_id', $this->selectedLapsoId);
                    }
                });

                $totalAct = (clone $actividadesQuery)->count();
                $pendingAct = (clone $actividadesQuery)->where('status', 0)->count();
                $approvedAct = (clone $actividadesQuery)->where('status', 1)->count();

                $resumenActividades = [
                    'total' => $totalAct,
                    'pending' => $pendingAct,
                    'approved' => $approvedAct,
                    'progress_pct' => $totalAct > 0 ? round(100 * $approvedAct / $totalAct) : 0,
                ];

                // E) Resumen de lecciones LMS
                $resumenLecciones = Activity::whereHas('pevaluacion', function ($q) use ($profesor) {
                    $q->where('profesor_id', $profesor->id);
                    if ($this->selectedLapsoId) {
                        $q->where('lapso_id', $this->selectedLapsoId);
                    }
                })
                    ->whereHas('lmsPublication')
                    ->join('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                    ->selectRaw("lms_activity_publications.status, COUNT(*) as count")
                    ->groupBy('lms_activity_publications.status')
                    ->pluck('count', 'status');

                // Charts
                $this->loadProfesorFlowCharts($profesor);
            }
        }

        $lapsos = Lapso::orderBy('name')->get();

        // Filtrar profesores por búsqueda
        $filteredProfesores = $this->profesores;
        if ($this->searchProfesor) {
            $needle = mb_strtolower($this->searchProfesor);
            $filteredProfesores = $filteredProfesores->filter(fn($p) =>
                mb_strpos(mb_strtolower($p->name ?? ''), $needle) !== false
                || mb_strpos(mb_strtolower($p->lastname ?? ''), $needle) !== false
            )->values();
        }

        return view('livewire.leadership.profesor-indicators', [
            'profesor' => $profesor,
            'kpi' => $kpi,
            'profesorInfo' => $profesorInfo,
            'cargaAcademica' => $cargaAcademica,
            'resumenActividades' => $resumenActividades,
            'resumenLecciones' => $resumenLecciones,
            'lapsos' => $lapsos,
            'filteredProfesores' => $filteredProfesores,
        ]);
    }

    public function selectProfesor($id)
    {
        $this->selectedProfesorId = $id;
        $this->loadProfesorFlowCharts($id ? Profesor::find($id) : null);
    }

    public function updatedActivityRange()
    {
        if ($this->selectedProfesorId) {
            $profesor = Profesor::find($this->selectedProfesorId);
            $this->loadProfesorFlowCharts($profesor);
        }
    }

    private function loadProfesorFlowCharts(?Profesor $profesor): void
    {
        if (!$profesor) {
            $this->chartActivitiesFlow = [];
            $this->chartLessonsFlow = [];
            $this->chartStatusFlow = [];
            return;
        }

        $since = match ($this->activityRange) {
            '7d'  => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '3m'  => now()->subMonths(3)->startOfDay(),
            'all' => null,
            default => now()->subDays(7)->startOfDay(),
        };

        // ── Chart 1: Actividades flow ──
        $actQuery = Activity::selectRaw('DATE(activities.created_at) as date, COUNT(*) as total')
            ->whereHas('pevaluacion', function ($q) use ($profesor) {
                $q->where('profesor_id', $profesor->id);
                if ($this->selectedLapsoId) {
                    $q->where('lapso_id', $this->selectedLapsoId);
                }
            })
            ->groupBy('date')
            ->orderBy('date');
        if ($since) $actQuery->where('activities.created_at', '>=', $since);
        $this->chartActivitiesFlow = $actQuery->get()->map(fn($r) => [
            'x' => $r->date,
            'y' => (int) $r->total,
        ])->toArray();

        // ── Chart 2: Lecciones flow (todas las que tengan lmsPublication) ──
        $lesQuery = Activity::selectRaw('DATE(activities.created_at) as date, COUNT(*) as total')
            ->whereHas('pevaluacion', function ($q) use ($profesor) {
                $q->where('profesor_id', $profesor->id);
                if ($this->selectedLapsoId) {
                    $q->where('lapso_id', $this->selectedLapsoId);
                }
            })
            ->whereHas('lmsPublication')
            ->groupBy('date')
            ->orderBy('date');
        if ($since) $lesQuery->where('activities.created_at', '>=', $since);
        $this->chartLessonsFlow = $lesQuery->get()->map(fn($r) => [
            'x' => $r->date,
            'y' => (int) $r->total,
        ])->toArray();

        // ── Chart 3: Status flow (aprobadas vs pendientes por día) ──
        $statusQuery = Activity::selectRaw('DATE(activities.created_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN activities.status = 1 THEN 1 ELSE 0 END) as approved')
            ->selectRaw('SUM(CASE WHEN activities.status = 0 THEN 1 ELSE 0 END) as pending')
            ->whereHas('pevaluacion', function ($q) use ($profesor) {
                $q->where('profesor_id', $profesor->id);
                if ($this->selectedLapsoId) {
                    $q->where('lapso_id', $this->selectedLapsoId);
                }
            })
            ->groupBy('date')
            ->orderBy('date');
        if ($since) $statusQuery->where('activities.created_at', '>=', $since);
        $statusData = $statusQuery->get();

        $this->chartStatusFlow = [
            'categories' => $statusData->pluck('date')->toArray(),
            'approved'   => $statusData->pluck('approved')->map(fn($v) => (int) $v)->toArray(),
            'pending'    => $statusData->pluck('pending')->map(fn($v) => (int) $v)->toArray(),
        ];
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
