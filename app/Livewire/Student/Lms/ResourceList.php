<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class ResourceList extends Component
{
    use WireUiActions;
    use WithPagination;

    public string $search = '';

    public $lapsoId = '';

    public bool $showPreviewModal = false;

    public ?array $previewResource = null;

    /** ¿Mostrar la mascota? (C4) — oculta para 13–15 años. */
    public bool $showMascot = false;

    /** ¿Mascota con énfasis (ojos de estrella)? (C4) — solo 5–8 años. */
    public bool $mascotEmphasis = false;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        // Misma base etaria que la mascota del home/detalle (C4): puede ser
        // null (sin relación estudiant), '-' (fecha no cargada) o int.
        $age = null;
        if (auth()->user() && auth()->user()->estudiant) {
            $age = auth()->user()->estudiant->age;
        }
        $this->showMascot = $age === null || $age === '-' || (int) $age <= 12;
        $this->mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->lapsoId = '';
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);

        $query = LmsActivityResource::with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.profesor',
            'activity.pevaluacion.lapso',
            'media',
            'section',
        ]);

        $query = $service->scopeResources($query);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('display_name', 'like', "%{$this->search}%")
                    ->orWhereHas('activity', fn ($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        if ($this->lapsoId) {
            $query->whereHas('activity.pevaluacion', fn ($q) => $q->where('lapso_id', $this->lapsoId));
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(15);

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.student.lms.resource-list', [
            'resources' => $resources,
            'lapsos' => $lapsos,
        ])->layout('student.layouts.app');
    }

    public function preview(int $resourceId): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);

        $resource = LmsActivityResource::with([
            'activity',
            'media',
            'section',
        ])->findOrFail($resourceId);

        // Security check: ensure resource belongs to student's section
        $seccionIds = $service->getSeccionIds();
        $belongsToStudent = $resource->activity?->pevaluacion
            && $seccionIds->contains($resource->activity->pevaluacion->seccion_id);

        if (! $belongsToStudent) {
            $this->notification()->error(
                title: 'Acceso denegado',
                description: 'Este recurso no está disponible para tu sección.'
            );

            return;
        }

        $this->previewResource = $resource->toArray();
        $this->showPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewResource = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLapsoId()
    {
        $this->resetPage();
    }
}
