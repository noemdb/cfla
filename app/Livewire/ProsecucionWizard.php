<?php

namespace App\Livewire;

use App\Models\app\Learner\Estudiant;
use App\Models\app\Learner\Representant;
use Carbon\Carbon;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProsecucionWizard extends Component
{
    use Actions;

    public $step = 1;
    public $ci_representant = '';
    public $representant = null;
    public $estudiants = [];
    public $selectedEstudiants = [];
    public $downloadUrl = '';
    public $confirmedEstudiants = []; // Estudiantes ya confirmados
    public $qrCode = ''; // QR Code como string

    protected $rules = [
        'ci_representant' => 'required|string|min:6',
    ];

    protected $messages = [
        'ci_representant.required' => 'La cédula de identidad es requerida',
        'ci_representant.min' => 'La cédula debe tener al menos 6 caracteres',
    ];

    public function searchRepresentant()
    {
        $this->validate();

        // Buscar representante por CI
        $this->representant = Representant::where('ci_representant', $this->ci_representant)->first();

        if (!$this->representant) {
            $this->notification()->error(
                'Error',
                'No se encontró un representante con la cédula proporcionada'
            );
            return;
        }

        // Buscar estudiantes del representante que estén inscritos y activos
        $estudiants_collection = Estudiant::where('representant_id', $this->representant->id)
            ->where('status_active', true)
            ->whereHas('inscripcion', function($query) {
                $query->whereHas('seccion', function($subQuery) {
                    $subQuery->where('status_active', 'true')
                            ->where('status_inscription_affects', 'true')
                            ->whereNotIn('id', ['21','22','35','46','75','76','77','78']);
                });
            })
            ->with(['inscripcion.seccion.grado'])
            ->get();

        // Convertir Collection a array para Livewire
        $this->estudiants = $estudiants_collection->toArray();

        if (empty($this->estudiants)) {
            $this->notification()->error(
                'Error',
                'No se encontraron estudiantes inscritos y habilitados para la prosecución asociados a este representante'
            );
            return;
        }

        // Identificar estudiantes ya confirmados para prosecución
        $this->confirmedEstudiants = $estudiants_collection
            ->where('status_prosecution', true)
            ->pluck('id')
            ->toArray();

        // Inicializar selección con estudiantes que ya tienen prosecución confirmada
        $this->selectedEstudiants = $this->confirmedEstudiants;

        $this->step = 2;

        $this->notification()->success(
            'Éxito',
            'Representante encontrado. Seleccione los estudiantes que continuarán.'
        );
    }

    public function confirmProsecucion()
    {
        if (empty($this->selectedEstudiants)) {
            $this->notification()->error(
                'Error',
                'Debe seleccionar al menos un estudiante para continuar'
            );
            return;
        }

        // Verificar que no se intente desmarcar estudiantes ya confirmados
        $attemptingToUnconfirm = array_diff($this->confirmedEstudiants, $this->selectedEstudiants);

        if (!empty($attemptingToUnconfirm)) {
            $this->notification()->error(
                'Error',
                'No se puede desmarcar estudiantes que ya han sido confirmados para prosecución'
            );
            return;
        }

        // Actualizar estado de prosecución solo para estudiantes no confirmados previamente
        $newConfirmations = array_diff($this->selectedEstudiants, $this->confirmedEstudiants);

        if (!empty($newConfirmations)) {
            Estudiant::whereIn('id', $newConfirmations)
                ->update([
                    'status_prosecution' => true,
                    'date_prosecution' => now() // Agregar fecha y hora actual
                ]);

            $this->notification()->info(
                'Nuevas Confirmaciones',
                count($newConfirmations) . ' estudiante(s) confirmado(s) para prosecución'
            );
        }

        // Generar URL de descarga usando el ID del representante
        $this->downloadUrl = route('prosecucion.download.pdf', $this->representant->id);

        // Generar QR code
        try {
            $this->qrCode = 'data:image/png;base64,' . base64_encode(
                QrCode::format('png')->size(200)->generate($this->downloadUrl)
            );
        } catch (\Exception $e) {
            $this->notification()->warning(
                'Advertencia',
                'No se pudo generar el código QR, pero puede descargar la planilla directamente'
            );
        }

        $this->step = 3;

        $this->notification()->success(
            'Confirmación Exitosa',
            'La prosecución ha sido confirmada correctamente'
        );
    }

    // Método para verificar si un estudiante ya está confirmado
    public function isStudentConfirmed($studentId)
    {
        return in_array($studentId, $this->confirmedEstudiants);
    }

    // Método para obtener el conteo de estudiantes por estado
    public function getStudentCounts()
    {
        return [
            'total' => count($this->estudiants),
            'confirmed' => count($this->confirmedEstudiants),
            'selected' => count($this->selectedEstudiants),
            'new_confirmations' => count(array_diff($this->selectedEstudiants, $this->confirmedEstudiants))
        ];
    }

    public function resetWizard()
    {
        $this->step = 1;
        $this->ci_representant = '';
        $this->representant = null;
        $this->estudiants = [];
        $this->selectedEstudiants = [];
        $this->confirmedEstudiants = [];
        $this->downloadUrl = '';
        $this->qrCode = '';
    }

    public function render()
    {
        return view('livewire.prosecucion-wizard');
    }
}
