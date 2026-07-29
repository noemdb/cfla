<?php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Lms\LmsActivityResource;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = LmsActivityResource::with([
            'activity',
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.pensum.pestudio.peducativo',
            'activity.pevaluacion.profesor',
            'media',
        ])->where('is_visible', true);

        $query = $service->scopeResources($query);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('display_name', 'like', "%{$this->search}%")
                  ->orWhereHas('activity', fn($sq) => $sq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        $resources = $query->orderBy('lms_activity_resources.created_at', 'desc')
            ->paginate(20);

        return view('livewire.coordinacion.resource-list', [
            'resources' => $resources,
        ])->layout('coordinacion.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
}
