<?php

namespace App\Http\Livewire\Evaluacion\Inicial;

use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Pescolar\Lapso;
use Livewire\Component;

class EifinalkComponent extends Component
{
    public $eifinalks;
    public $profesor_id;
    public $seccion_id;
    public $lapso_id;
    public $lapsos;

    public function mount($profesor_id = null, $seccion_id = null, $lapso_id = null)
    {
        $this->profesor_id = $profesor_id;
        $this->seccion_id = $seccion_id;
        $this->lapso_id = $lapso_id;
        $this->lapsos = Lapso::all(); // Cargar todos los lapsos disponibles
    }

   public function render()
    {
        $query = Eifinalk::query()
            ->with(['pevaluacion.profesor', 'pevaluacion.lapso', 'pevaluacion.seccion.grado', 'estudiant']);

        if ($this->profesor_id) {
            $query->byProfesor($this->profesor_id);
        }

        if ($this->lapso_id) {
            $query->whereHas('pevaluacion', function ($q) {
                $q->where('lapso_id', $this->lapso_id);
            });
        }

        if ($this->seccion_id) {
            $query->whereHas('pevaluacion', function ($q) {
                $q->where('seccion_id', $this->seccion_id);
            });
        }

        $this->eifinalks = $query
            ->orderBy('estudiant_id')
            ->orderBy('pevaluacion_id')
            ->groupBy('estudiant_id')
            ->get();

        return view('livewire.evaluacion.inicial.eifinalk-component');
    }




    public function downloadFormat($id)
    {
        // Este método será llamado para descargar/mostrar el formato.
        $this->dispatchBrowserEvent('show-format', ['id' => $id]);
    }
}
