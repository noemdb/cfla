<?php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class AcademicInfo extends Component
{
    use WireUiActions;
    public ?array $inscripcionData = null;
    public $pensums;
    public $pevaluacions;
    public $currentLapsoId;
    public Collection $areaStats;

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->inscripcionData = $service->getInscripcionData();

        $this->currentLapsoId = Lapso::current()?->id;

        // Pensums del grado del estudiante
        $this->pensums = $service->getPensumsWithAsignatura();

        // Pevaluacions del estudiante (actividades de planificación)
        $this->pevaluacions = Pevaluacion::with([
            'pensum.asignatura',
            'lapso',
            'profesor',
        ])
        ->whereIn('seccion_id', $service->getSeccionIds())
        ->when($this->currentLapsoId, fn($q) => $q->where('lapso_id', $this->currentLapsoId))
        ->orderBy('created_at', 'desc')
        ->get();

        // Stats por área de formación
        $this->areaStats = $this->computeAreaStats($service);
    }

    protected function computeAreaStats(StudentScopeService $service): Collection
    {
        $seccionIds = $service->getSeccionIds();
        if ($seccionIds->isEmpty() || $this->pensums->isEmpty()) {
            return collect();
        }

        $stats = [];

        foreach ($this->pensums as $pensum) {
            $activityIds = Activity::whereHas('pevaluacion', fn($q) =>
                $q->whereIn('seccion_id', $seccionIds)
                  ->where('pensum_id', $pensum->id)
            )->pluck('id');

            $stats[$pensum->id] = [
                'activities' => $activityIds->count(),
                'lessons'    => Activity::whereIn('id', $activityIds)
                    ->whereHas('lmsPublication', fn($q) => $q->visibleNow())
                    ->count(),
                'comments'   => ActivityComment::where('is_approved', true)
                    ->whereIn('activity_id', $activityIds)
                    ->count(),
            ];
        }

        return collect($stats);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.academic-info')
            ->layout('student.layouts.app');
    }
}
