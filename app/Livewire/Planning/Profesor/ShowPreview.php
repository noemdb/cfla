<?php

namespace App\Livewire\Planning\Profesor;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use Livewire\Component;

class ShowPreview extends Component
{
    public $profesorId;

    public function mount($profesorId = null)
    {
        $this->profesorId = $profesorId;
    }

    public function getProfesorProperty()
    {
        if (!$this->profesorId) {
            return null;
        }

        return Profesor::with(['user', 'pevaluacions.pensum.asignatura', 'pevaluacions.lapso', 'pevaluacions.seccion'])
            ->withCount('pevaluacions')
            ->addSelect([
                'activities_count' => Pevaluacion::selectRaw('COUNT(activities.id)')
                    ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
                    ->whereColumn('pevaluacions.profesor_id', 'profesors.id')
            ])
            ->findOrFail($this->profesorId);
    }

    public function close()
    {
        $this->dispatch('closePreviewModal');
    }

    public function render()
    {
        return view('livewire.planning.profesor.show-preview');
    }
}
