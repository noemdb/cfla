<?php

namespace App\Livewire\Coordinacion;

use App\Services\Lms\CoordinacionScopeService;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    use Concerns\HasCoordinacionScope;

    public $selectedLapsoId;
    public $lapsos;
    public $lapsoActive;

    // KPI boxes
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;
    public $totalPevaluacions = 0;
    public $totalResources = 0;

    // Indicators per Peducativo
    public $peducativoIndicators = [];

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
        $service = $this->getCoordinacionService();

        $this->lapsos = Lapso::orderBy('id')->get();
        $this->lapsoActive = Lapso::current();
        $this->selectedLapsoId = $this->lapsoActive?->id ?? $this->lapsos->first()?->id;

        $this->loadIndicators();
    }

    public function updatedSelectedLapsoId(): void
    {
        $this->loadIndicators();
    }

    public function loadIndicators(): void
    {
        $service = $this->getCoordinacionService();
        $peducativos = $service->getPeducativos();

        $this->peducativoIndicators = $peducativos->map(function ($peducativo) use ($service) {
            $pestudios = $service->getPestudios()->where('peducativo_id', $peducativo->id);

            $totalActivities = 0;
            $totalProfesores = collect();

            foreach ($pestudios as $pestudio) {
                $totalActivities += $pestudio->getActivitiesCount($this->selectedLapsoId);
                $totalProfesores = $totalProfesores->merge(
                    $pestudio->getProfesors($this->selectedLapsoId)
                );
            }

            $pestudioIds = $pestudios->pluck('id');

            return (object) [
                'peducativo'        => $peducativo,
                'pestudios'         => $pestudios,
                'activities_count'  => $totalActivities,
                'profesores_count'  => $totalProfesores->unique('id')->count(),
                'lessons_count'     => Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                    ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                    ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                    ->whereIn('pensums.pestudio_id', $pestudioIds)
                    ->where('pevaluacions.lapso_id', $this->selectedLapsoId)
                    ->count(\Illuminate\Support\Facades\DB::raw('DISTINCT activities.id')),
                'grados_count'      => \Illuminate\Support\Facades\DB::table('grados')
                    ->whereIn('pestudio_id', $pestudioIds)
                    ->whereNull('deleted_at')
                    ->count(),
                'pensums_count'     => \Illuminate\Support\Facades\DB::table('pensums')
                    ->whereIn('pestudio_id', $pestudioIds)
                    ->whereNull('deleted_at')
                    ->count(),
            ];
        });

        $this->totalActivities = $this->peducativoIndicators->sum('activities_count');
        $this->totalProfesoresActivos = $service->getProfesoresCount();
        $this->totalPevaluacions = Pevaluacion::join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $service->getPestudioIds())
            ->count();
        $this->totalResources = \App\Models\app\Academy\Lms\LmsActivityResource::query()
            ->where('is_visible', true)
            ->whereHas('activity.pevaluacion.pensum', fn($q) => $q->whereIn('pestudio_id', $service->getPestudioIds()))
            ->count();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.coordinacion.dashboard')
            ->layout('coordinacion.layouts.app');
    }
}
