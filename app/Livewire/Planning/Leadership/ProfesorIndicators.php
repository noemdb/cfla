<?php

namespace App\Livewire\Planning\Leadership;

use App\Services\Planning\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProfesorIndicators extends Component
{
    public $selectedProfesorId = null;
    public $selectedLapsoId = null;
    public $profesores = [];

    public function mount()
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $this->profesores = $service->getAssignedProfesores();
    }

    public function render()
    {
        $profesor = null;
        $kpi = null;

        if ($this->selectedProfesorId) {
            $profesor = \App\Models\app\Academy\Profesor::find($this->selectedProfesorId);
            if ($profesor) {
                $kpi = [
                    'iee' => $profesor->getProfesorIEE($this->selectedLapsoId),
                    'ire' => $profesor->getProfesorIRE(
                        request()->input('pestudio_id'),
                        $this->selectedLapsoId
                    ),
                    'goal_notas' => $profesor->goal_notas_load($this->selectedLapsoId),
                    'real_notas' => $profesor->real_notas_load($this->selectedLapsoId),
                    'total_pevas' => $profesor->pevaluacions()
                        ->when($this->selectedLapsoId, fn($q) => $q->where('lapso_id', $this->selectedLapsoId))
                        ->count(),
                ];
            }
        }

        $lapsos = \App\Models\app\Academy\Lapso::orderBy('name')->get();

        return view('livewire.planning.leadership.profesor-indicators', [
            'profesor' => $profesor,
            'kpi' => $kpi,
            'lapsos' => $lapsos,
        ]);
    }

    public function selectProfesor($id)
    {
        $this->selectedProfesorId = $id;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
