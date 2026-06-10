<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Pevaluacion;
use Livewire\Component;

class StudentHome extends Component
{
    public string $search = '';
    public $pevaluacions;

    public function mount(): void
    {
        $publishedActivityIds = \App\Models\app\Academy\Lms\LmsActivityPublication::query()
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
