<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class StudentHome extends Component
{
    use WireUiActions;
    use Concerns\HasStudentScope;

    public string $search = '';
    public $pevaluacions;

    public function mount(): void
    {
        $this->initializeHasStudentScope();

        $service = $this->getStudentService();
        $seccionIds = $service->getSeccionIds();

        if ($seccionIds->isEmpty()) {
            $this->pevaluacions = collect();
            return;
        }

        $publishedActivityIds = LmsActivityPublication::query()
            ->visibleNow()
            ->pluck('activity_id');

        $this->pevaluacions = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'activities' => function ($q) use ($publishedActivityIds) {
                $q->whereIn('id', $publishedActivityIds)
                  ->whereHas('lmsPublication', fn($sq) => $sq->visibleNow())
                  ->with('lmsPublication');
            },
        ])
        ->whereIn('seccion_id', $seccionIds)
        ->whereHas('activities', fn($q) => $q->whereIn('id', $publishedActivityIds))
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.student-home')
            ->layout('student.layouts.app');
    }
}
