<?php
// app/Livewire/Director/LessonList.php

namespace App\Livewire\Director;

use App\Models\app\Academy\Activity;
use Livewire\Component;
use Livewire\WithPagination;

class LessonList extends Component
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
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.pestudio.peducativo',
            'pevaluacion.profesor',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections.contents',
        ])->whereHas('lmsPublication');

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }

        $lessons = $query->orderBy('activities.created_at', 'desc')->paginate(15);
        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.director.lesson-list', [
            'lessons' => $lessons,
            'lapsos'  => $lapsos,
        ])->layout('director.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
}
