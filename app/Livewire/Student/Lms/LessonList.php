<?php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class LessonList extends Component
{
    use WireUiActions;
    use WithPagination;

    public string $search = '';
    public $lapsoId = '';
    public $asignaturaId = '';
    protected $paginationTheme = 'tailwind';

    public function render(): \Illuminate\View\View
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $seccionIds = $service->getSeccionIds();

        $query = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.profesor',
            'pevaluacion.lapso',
            'lmsPublication',
        ])->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
          ->whereHas('lmsPublication', fn($q) => $q->visibleNow());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }

        if ($this->asignaturaId) {
            $query->whereHas('pevaluacion.pensum', fn($q) => $q->where('asignatura_id', $this->asignaturaId));
        }

        $activities = $query->orderBy(
            LmsActivityPublication::select('published_at')
                ->whereColumn('activity_id', 'activities.id')
                ->limit(1),
            'desc'
        )->paginate(12);

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.student.lms.lesson-list', [
            'activities' => $activities,
            'lapsos'     => $lapsos,
        ])->layout('student.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingAsignaturaId() { $this->resetPage(); }
}
