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

        $pensums = $query->orderBy('pestudio_id')->paginate(20);
        $peducativos = $service->queryPeducativos()->get();

        return view('livewire.director.pensum-list', [
            'pensums'      => $pensums,
            'peducativos'  => $peducativos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
}
