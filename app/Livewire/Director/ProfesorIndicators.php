<?php
// app/Livewire/Director/ProfesorIndicators.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Profesor;
use Livewire\Component;
use Livewire\WithPagination;

class ProfesorIndicators extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $lapsoId = '';
    public $paginate = 15;
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        // Todos los profesores activos con su carga (seguimiento global)
        $query = $service->queryProfesores()->withCount([
            'pevaluacions as peva_count' => fn($q) => $q->when($this->lapsoId, fn($qq) => $qq->where('lapso_id', $this->lapsoId)),
        ]);

        if ($this->search) {
            $query->where(fn($q) => $q->where('lastname', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%"));
        }

        $profesores = $query->orderBy('lastname')->paginate($this->paginate);
        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.profesor-indicators', [
            'profesores' => $profesores,
            'lapsos'     => $lapsos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
