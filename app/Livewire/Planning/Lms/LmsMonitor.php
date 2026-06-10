<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Lms\LmsActivityPublication;
use Livewire\Component;
use Livewire\WithPagination;

class LmsMonitor extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public function render(): \Illuminate\View\View
    {
        $query = LmsActivityPublication::with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.profesor',
            'publisher',
        ])
        ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
        ->when($this->search, fn($q) => $q->whereHas('activity', fn($sq) => $sq->where('topic', 'like', '%' . $this->search . '%')));

        return view('livewire.planning.lms.monitor', [
            'publications' => $query->latest('updated_at')->paginate(20),
        ])->layout('planning.layouts.app');
    }
}
