<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Profile extends Component
{
    use WireUiActions;

    public ?array $profileData = null;

    public ?array $stats = null;

    /** ¿Mostrar la mascota? (C4) — oculta para 13–15 años. */
    public bool $showMascot = false;

    /** ¿Mascota con énfasis (ojos de estrella)? (C4) — solo 5–8 años. */
    public bool $mascotEmphasis = false;

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->profileData = $service->getInscripcionData();

        // C4: misma base etaria que home/activity. Puede ser null (sin relación
        // estudiant), '-' (fecha no cargada) o int.
        $age = Auth::user()?->estudiant?->age;
        $this->showMascot = $age === null || $age === '-' || (int) $age <= 12;
        $this->mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;

        // Estadísticas académicas rápidas
        $seccionIds = $service->getSeccionIds();
        if ($seccionIds->isNotEmpty()) {
            $publishedActivityIds = LmsActivityPublication::query()
                ->visibleNow()
                ->pluck('activity_id');

            $activities = Activity::whereIn('id', $publishedActivityIds)
                ->whereHas('pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds))
                ->get();

            $this->stats = [
                'total_activities' => $activities->count(),
                'total_lessons' => $activities->filter(fn ($a) => $a->lmsPublication?->isVisibleToStudents())->count(),
                'total_comments' => ActivityComment::whereIn('activity_id', $activities->pluck('id'))
                    ->approved()
                    ->count(),
            ];
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.profile')
            ->layout('student.layouts.app');
    }
}
