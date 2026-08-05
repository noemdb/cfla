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
    public $totalPevaluacions = 0;
    public $totalResources = 0;

    // ─── KPIs por Peducativo ───
    public $peducativoIndicators = [];

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
        $service = $this->getDirectorService();

        $this->lapsos = \App\Models\app\Academy\Lapso::orderBy('id')->get();
        $this->lapsoActive = \App\Models\app\Academy\Lapso::current();
        $this->selectedLapsoId = $this->lapsoActive?->id ?? $this->lapsos->first()?->id;

        $this->totalPeducativos = $service->queryPeducativos()->count();

        $this->loadIndicators();
    }

    public function updatedSelectedLapsoId(): void
    {
        $this->loadIndicators();
    }

    public function loadIndicators(): void
    {
        $service = $this->getDirectorService();

        $this->totalPensums = $service->queryPensums()->count();
        $this->totalPevaluacions = $service->queryPevaluacions()
            ->when($this->selectedLapsoId, fn($q) => $q->where('lapso_id', $this->selectedLapsoId))
            ->count();
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
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.director.indicator-dashboard')
            ->layout('director.layouts.app');
    }
}
