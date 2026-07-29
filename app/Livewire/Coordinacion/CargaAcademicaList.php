<?php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Pevaluacion;
use Livewire\Component;
use Livewire\WithPagination;

class CargaAcademicaList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    public $lapsoId = '';
    public $pensumId = '';
    public $paginate = 15;
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Pevaluacion::with([
            'profesor:id,name,lastname,ci_profesor',
            'seccion:id,name,grado_id',
            'seccion.grado:id,name',
            'pensum.asignatura',
            'pensum.pestudio.peducativo',
            'lapso',
        ]);

        $query = $service->scopePevaluacions($query);

        if ($this->lapsoId) {
            $query->where('pevaluacions.lapso_id', $this->lapsoId);
        }
        if ($this->pensumId) {
            $query->where('pevaluacions.pensum_id', $this->pensumId);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('profesor', fn($q) => $q->where('lastname', 'like', "%{$this->search}%"))
                  ->orWhereHas('pensum.asignatura', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('seccion', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            });
        }

        $pevaluacions = $query->orderBy('pevaluacions.created_at', 'desc')
            ->paginate($this->paginate);

        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');
        $peducativos = $service->getPeducativos();

        return view('livewire.coordinacion.carga-academica-list', [
            'pevaluacions' => $pevaluacions,
            'lapsos'       => $lapsos,
            'peducativos'  => $peducativos,
        ])->layout('coordinacion.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
