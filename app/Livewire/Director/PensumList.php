<?php
// app/Livewire/Director/PensumList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Pensum;
use Livewire\Component;
use Livewire\WithPagination;

class PensumList extends Component
{
    use WithPagination, Concerns\HasDirectorScope;

    public string $search = '';
    public $peducativoId = '';
    public $paginate = 10;
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasDirectorScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getDirectorService();

        $query = $service->queryPensums()->with([
            'pestudio.peducativo',
            'asignatura',
            'grado',
        ]);

        if ($this->peducativoId) {
            $query->whereHas('pestudio', fn($q) => $q->where('peducativo_id', $this->peducativoId));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('grado', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('pestudio', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $query->orderBy('pestudio_id');

        if ((int) $this->paginate === 9999) {
            // "Todos": entrega todos los registros en una sola página
            // (mismo comportamiento que el módulo de planificación).
            $all = $query->get();
            $pensums = new \Illuminate\Pagination\LengthAwarePaginator(
                $all,
                $all->count(),
                max($all->count(), 1),
                1,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        } else {
            $pensums = $query->paginate($this->paginate);
        }
        $peducativos = $service->queryPeducativos()->get();

        return view('livewire.director.pensum-list', [
            'pensums'      => $pensums,
            'peducativos'  => $peducativos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
    public function updatedPaginate() { $this->resetPage(); }
}
