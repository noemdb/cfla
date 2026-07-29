<?php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceList extends Component
{
    use WithPagination;

    public string $search = '';
    public $lapsoId = '';
    public bool $showPreviewModal = false;
    public ?array $previewResource = null;
    protected $paginationTheme = 'tailwind';

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
                  ->orWhereHas('activity', fn($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        if ($this->lapsoId) {
            $query->whereHas('activity.pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(15);

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.student.lms.resource-list', [
            'resources' => $resources,
            'lapsos'    => $lapsos,
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

        if (!$belongsToStudent) {
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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
}
