<?php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Pensum;
use Livewire\Component;
use Livewire\WithPagination;

class PensumList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    public $paginate = 15;
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Pensum::with([
            'pestudio.peducativo',
            'asignatura',
            'grado',
        ]);

        $query = $service->scopePensums($query);

        if ($this->peducativoId) {
            $pestudioIds = $service->getPestudios()
                ->where('peducativo_id', $this->peducativoId)
                ->pluck('id');
            $query->whereIn('pestudio_id', $pestudioIds);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('grado', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('pestudio', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $pensums = $query->orderBy('pestudio_id')->paginate($this->paginate);

        $peducativos = $service->getPeducativos();

        return view('livewire.coordinacion.pensum-list', [
            'pensums'      => $pensums,
            'peducativos'  => $peducativos,
        ])->layout('coordinacion.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
