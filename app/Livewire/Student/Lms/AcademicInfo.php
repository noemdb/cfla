<?php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AcademicInfo extends Component
{
    public ?array $inscripcionData = null;
    public $pensums;
    public $pevaluacions;
    public $currentLapsoId;

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->inscripcionData = $service->getInscripcionData();

        $this->currentLapsoId = Lapso::current()?->id;

        // Pensums del grado del estudiante
        $this->pensums = $service->getPensumsWithAsignatura();

        // Pevaluacions del estudiante (actividades de planificación)
        $this->pevaluacions = Pevaluacion::with([
            'pensum.asignatura',
            'lapso',
            'profesor',
        ])
        ->whereIn('seccion_id', $service->getSeccionIds())
        ->when($this->currentLapsoId, fn($q) => $q->where('lapso_id', $this->currentLapsoId))
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.academic-info')
            ->layout('student.layouts.app');
    }
}
