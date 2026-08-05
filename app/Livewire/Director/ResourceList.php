<?php
// app/Livewire/Director/ResourceList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Lms\LmsActivityResource;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $peducativoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = $service->queryResources()->with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.pensum.pestudio.peducativo',
            'activity.pevaluacion.profesor',
            'media',
        ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('display_name', 'like', "%{$this->search}%")
                  ->orWhereHas('activity', fn($sq) => $sq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        $resources = $query->orderBy('lms_activity_resources.created_at', 'desc')->paginate(20);

        return view('livewire.director.resource-list', [
            'resources' => $resources,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
}
