<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Pevaluacion;
use App\Services\Estudiant\StudentScopeService;
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

    public ?int $selectedPevId = null;

    public $selectedPev = null;

    public $selectedActivities = null;

    public $pevLessons = null;

    public $pevResources = null;

    /** ¿Mostrar la mascota? (C4) — oculta para 13–15 años. */
    public bool $showMascot = false;

    /** ¿Mascota con énfasis (ojos de estrella)? (C4) — solo 5–8 años. */
    public bool $mascotEmphasis = false;

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->inscripcionData = $service->getInscripcionData();

        // C4: misma base etaria que home/activity/perfil. Puede ser null,
        // '-' (fecha no cargada) o int.
        $age = Auth::user()?->estudiant?->age;
        $this->showMascot = $age === null || $age === '-' || (int) $age <= 12;
        $this->mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;

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
            ->when($this->currentLapsoId, fn ($q) => $q->where('lapso_id', $this->currentLapsoId))
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
            $activityIds = Activity::whereHas('pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds)
                ->where('pensum_id', $pensum->id)
            )->pluck('id');

            $stats[$pensum->id] = [
                'activities' => $activityIds->count(),
                'lessons' => Activity::whereIn('id', $activityIds)
                    ->whereHas('lmsPublication', fn ($q) => $q->visibleNow())
                    ->count(),
                'comments' => ActivityComment::where('is_approved', true)
                    ->whereIn('activity_id', $activityIds)
                    ->count(),
            ];
        }

        return collect($stats);
    }

    public function showDetail(int $pevId): void
    {
        $this->selectedPevId = $pevId;

        $this->selectedPev = Pevaluacion::with([
            'pensum.asignatura',
            'lapso',
            'profesor.user.profile',
            'activities.lmsPublication',
            'activities.lmsResources',
        ])->find($pevId);

        $this->selectedActivities = $this->selectedPev?->activities;

        $this->pevLessons = $this->selectedActivities
            ? $this->selectedActivities->filter(fn ($a) => $a->relationLoaded('lmsPublication') && $a->lmsPublication && $a->lmsPublication->is_visible)
            : collect();

        $this->pevResources = $this->selectedActivities
            ? $this->selectedActivities->pluck('lmsResources')->flatten()->filter(fn ($r) => $r && $r->is_visible)
            : collect();
    }

    public function closeDetail(): void
    {
        $this->selectedPevId = null;
        $this->selectedPev = null;
        $this->selectedActivities = null;
        $this->pevLessons = null;
        $this->pevResources = null;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.academic-info')
            ->layout('student.layouts.app');
    }
}
