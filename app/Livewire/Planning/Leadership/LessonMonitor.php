<?php

namespace App\Livewire\Planning\Leadership;

use App\Services\Planning\LeadershipService;
use App\Models\app\Academy\Activity;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class LessonMonitor extends Component
{
    use WithPagination;

    public $search = '';
    public $paginate = 15;
    public $area_id = '';
    public $lapso_id = '';
    public $filter_published = false;

    public function render()
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $query = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.lapso',
            'pevaluacion.seccion.grado',
            'pevaluacion.profesor',
            'lmsPublication',
            'lmsSections.contents',
        ])->whereHas('pevaluacion.pensum', function ($q) use ($service) {
            $asignaturaIds = $service->getAssignedAsignaturaIds();
            $q->whereIn('asignatura_id', $asignaturaIds);
        });

        if ($this->filter_published) {
            $query->whereHas('lmsPublication', fn($q) => $q->where('status', 'PUBLISHED'));
        }

        if ($this->search) {
            $query->where('topic', 'like', "%{$this->search}%");
        }

        if ($this->lapso_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapso_id));
        }

        $lessons = $query->orderBy('created_at', 'desc')
            ->paginate($this->paginate);

        $lapsos = \App\Models\app\Academy\Lapso::orderBy('name')->get();

        return view('livewire.planning.leadership.lesson-monitor', [
            'lessons' => $lessons,
            'lapsos' => $lapsos,
        ]);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
