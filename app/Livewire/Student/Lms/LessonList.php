<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class LessonList extends Component
{
    use WireUiActions;
    use WithPagination;

    public string $search = '';

    public $lapsoId = '';

    public $asignaturaId = '';

    protected $paginationTheme = 'tailwind';

    /** ¿Mostrar la mascota? (C4) — oculta para 13–15 años. */
    public bool $showMascot = false;

    /** ¿Mascota con énfasis (ojos de estrella)? (C4) — solo 5–8 años. */
    public bool $mascotEmphasis = false;

    public function render(): \Illuminate\View\View
    {
        // C4: misma base etaria que home/activity/perfil/académica.
        $age = Auth::user()?->estudiant?->age;
        $this->showMascot = $age === null || $age === '-' || (int) $age <= 12;
        $this->mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;

        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $seccionIds = $service->getSeccionIds();

        $query = Activity::query()
            ->with([
                'pevaluacion.pensum.asignatura',
                'pevaluacion.profesor',
                'pevaluacion.lapso',
                'lmsPublication',
            ])->where('status', true)
            ->whereHas('pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds))
            ->whereHas('lmsPublication', fn ($q) => $q->visibleNow());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                    ->orWhere('thematic', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('lapso_id', $this->lapsoId));
        }

        if ($this->asignaturaId) {
            $query->whereHas('pevaluacion.pensum', fn ($q) => $q->where('asignatura_id', $this->asignaturaId));
        }

        $orderBy = function ($q, $column) {
            return $q->orderBy(
                LmsActivityPublication::select($column)
                    ->whereColumn('activity_id', 'activities.id')
                    ->limit(1),
                'desc'
            );
        };

        // ─── Agrupación por lapso: primero el lapso actual (Lapso::current()).
        $currentLapso = Lapso::current();
        $currentLapsoId = $currentLapso?->id;

        // Grupo 1: lecciones del lapso actual (siempre primero, en su propio bloque).
        $currentLapsoActivities = null;
        if ($currentLapsoId !== null) {
            $currentLapsoActivities = $orderBy(
                (clone $query)->whereHas('pevaluacion', fn ($q) => $q->where('lapso_id', $currentLapsoId)),
                'published_at'
            )->paginate(12, ['*'], 'currentPage');
        }

        // Grupo 2: el resto de les lecciones (otros lapsos / sin lapso).
        $otherLapsoQuery = (clone $query)->whereHas('pevaluacion', function ($q) use ($currentLapsoId) {
            $q->where('lapso_id', '!=', $currentLapsoId)->orWhereNull('lapso_id');
        });
        $otherLapsoActivities = $orderBy($otherLapsoQuery, 'published_at')->paginate(12, ['*'], 'otherPage');

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        $asignaturas = \App\Models\app\Academy\Asignatura::whereHas('pensums.pevaluacions', function ($q) use ($seccionIds) {
            $q->whereIn('seccion_id', $seccionIds);
        })->whereHas('pensums.pevaluacions.activities.lmsPublication', function ($q) {
            $q->visibleNow();
        })->orderBy('name')->pluck('name', 'id');

        // Solo se renderiza un grupo si tiene resultados, y siempre en este orden.
        $groups = [];
        if ($currentLapsoActivities !== null && $currentLapsoActivities->isNotEmpty()) {
            $groups[] = [
                'key' => 'current',
                'title' => 'Lapso actual',
                'subtitle' => $currentLapso?->name,
                'accent' => 'emerald',
                'activities' => $currentLapsoActivities,
            ];
        }
        if ($otherLapsoActivities->isNotEmpty()) {
            $groups[] = [
                'key' => 'others',
                'title' => 'Lecciones anteriores',
                'subtitle' => 'De otros lapsos',
                'accent' => 'slate',
                'activities' => $otherLapsoActivities,
            ];
        }

        return view('livewire.student.lms.lesson-list', [
            'groups' => $groups,
            'lapsos' => $lapsos,
            'asignaturas' => $asignaturas,
        ])->layout('student.layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetBothPages();
    }

    public function updatingLapsoId()
    {
        $this->resetBothPages();
    }

    public function updatingAsignaturaId()
    {
        $this->resetBothPages();
    }

    /** Reinicia las dos paginaciones con nombre propio al cambiar un filtro. */
    private function resetBothPages(): void
    {
        $this->resetPage('currentPage');
        $this->resetPage('otherPage');
    }
}
