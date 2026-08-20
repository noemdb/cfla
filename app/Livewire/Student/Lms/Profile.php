<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->profileData = $service->getInscripcionData();

        // C4: misma base etaria que home/activity. Puede ser null (sin relación
        // estudiant), '-' (fecha no cargada) o int.
        $age = Auth::user()?->estudiant?->age;
        $this->showMascot = $age === null || $age === '-' || (int) $age <= 12;
        $this->mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;

        // Estadísticas académicas rápidas — MISMA semántica que StudentHome
        // (dashboard canónico): conteos reales, progreso como %, y comentarios
        // DEL PROPIO estudiante (antes se contaban los de todos y los conteos
        // crudos se mostraban como si fueran porcentajes — 2%, 0%).
        $seccionIds = $service->getSeccionIds();
        if ($seccionIds->isNotEmpty()) {
            $publishedActivityIds = LmsActivityPublication::query()
                ->visibleNow()
                ->pluck('activity_id');

            $visibleActivityIds = Activity::whereIn('id', $publishedActivityIds)
                ->whereHas('pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds))
                ->pluck('id');

            $totalActivities = $visibleActivityIds->count();

            $completedIds = LmsActivityLog::where('user_id', Auth::id())
                ->where('event', 'COMPLETE')
                ->whereIn('activity_id', $visibleActivityIds)
                ->pluck('activity_id')
                ->unique();

            // Comentarios: DOS indicadores — total dejados por el estudiante y
            // cuántos de esos pasaron la moderación (aprobados).
            $commentsCount = ActivityComment::where('user_id', Auth::id())->count();

            $commentsApprovedCount = ActivityComment::where('user_id', Auth::id())
                ->approved()
                ->count();

            $downloadsCount = LmsActivityLog::where('user_id', Auth::id())
                ->where('event', 'RESOURCE_DOWNLOAD')
                ->count();

            $this->stats = [
                'total' => $totalActivities,
                'completed' => $completedIds->count(),
                'comments' => $commentsCount,
                'comments_approved' => $commentsApprovedCount,
                'downloads' => $downloadsCount,
                'progress_pct' => $totalActivities > 0
                    ? round(($completedIds->count() / $totalActivities) * 100)
                    : 0,
            ];
        }
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::min(6), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->notification()->success(
            'Contraseña actualizada',
            'Tu contraseña se cambió correctamente.'
        );
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.profile')
            ->layout('student.layouts.app');
    }
}
