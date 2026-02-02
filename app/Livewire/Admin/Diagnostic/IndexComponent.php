<?php

namespace App\Livewire\Admin\Diagnostic;

use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pensum;
use Livewire\Component;
use Livewire\Attributes\Layout;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WireUiActions;

    public function toggleAllPestudio($pestudioId, $activate)
    {
        Pensum::where('pestudio_id', $pestudioId)
            ->update(['status_active_diagnostic' => $activate]);

        $this->notification()->success(
            $title = 'Actualización Masiva',
            $description = "Se han " . ($activate ? 'activado' : 'desactivado') . " todas las asignaturas del plan de estudio."
        );
    }

    public function toggleAllGrado($pestudioId, $gradoId, $activate)
    {
        Pensum::where('pestudio_id', $pestudioId)
            ->where('grado_id', $gradoId)
            ->update(['status_active_diagnostic' => $activate]);

        $this->notification()->success(
            $title = 'Actualización Masiva',
            $description = "Se han " . ($activate ? 'activado' : 'desactivado') . " todas las asignaturas del grado."
        );
    }

    public function toggleStatus($pensumId)
    {
        $pensum = Pensum::findOrFail($pensumId);
        $pensum->status_active_diagnostic = !$pensum->status_active_diagnostic;
        $pensum->save();

        $this->notification()->success(
            $title = 'Estado Actualizado',
            $description = "El pensum ha sido " . ($pensum->status_active_diagnostic ? 'activado' : 'desactivado') . " para el diagnóstico."
        ); 
    }

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $pestudios = Pestudio::with(['grados.seccions', 'grados' => function($query) {
            $query->where('status_active', 'true')->has('pensums');
        }])
        ->where('status_active', 'true')
        ->orderBy('order', 'asc')
        ->get();

        // Get all pensums grouped by pestudio and grado
        $pensums = Pensum::whereHas('pevaluacions')
            ->with(['asignatura', 'grado', 'pestudio'])
            ->withCount('diagQuestions')
            ->get()
            ->groupBy(['pestudio_id', 'grado_id']);

        return view('livewire.admin.diagnostic.index-component', [
            'pestudios' => $pestudios,
            'groupedPensums' => $pensums
        ]);
    }
}
