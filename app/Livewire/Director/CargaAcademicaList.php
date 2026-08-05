<?php
// app/Livewire/Director/CargaAcademicaList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Pevaluacion;
use Livewire\Component;
use Livewire\WithPagination;

class CargaAcademicaList extends Component
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

        $query = $service->queryPevaluacions()->with([
            'profesor:id,name,lastname',
            'seccion:id,name,grado_id',
            'seccion.grado:id,name',
            'pensum.asignatura',
            'pensum.pestudio.peducativo',
            'lapso',
        ]);

        if ($this->lapsoId) $query->where('pevaluacions.lapso_id', $this->lapsoId);
        if ($this->peducativoId) {
            $query->whereHas('pensum.pestudio', fn($q) => $q->where('peducativo_id', $this->peducativoId));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('profesor', fn($sq) => $sq->where('lastname', 'like', "%{$this->search}%"))
                  ->orWhereHas('pensum.asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('seccion', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $pevaluacions = $query->orderBy('pevaluacions.created_at', 'desc')->paginate(20);
        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');
        $peducativos = $service->queryPeducativos()->get();

        return view('livewire.director.carga-academica-list', [
            'pevaluacions' => $pevaluacions,
            'lapsos'       => $lapsos,
            'peducativos'  => $peducativos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
}
