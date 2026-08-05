<?php
// app/Livewire/Director/ActivityList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $peducativoId = '';
    public $lapsoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = $service->queryActivities()->with([
            'pevaluacion' => fn($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
        ]);

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }

        $activities = $query->orderBy('activities.created_at', 'desc')->paginate(15);
        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.activity-list', [
            'activities' => $activities,
            'lapsos'     => $lapsos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
}
