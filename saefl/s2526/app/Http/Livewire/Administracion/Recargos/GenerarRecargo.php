<?php

namespace App\Http\Livewire\Administracion\Recargos;


use Livewire\Component;
use App\Models\app\Estudiant;
use Carbon\Carbon;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;


class GenerarRecargo extends Component
{
    public $search = '';
    public $students = [];
    public $selectedEstudiant;
    public $selectedEstudiantId;
    public $quotas = [];
    public $confirming = false;

    public $montoOriginal = 0;
    public $recargoTotal = 0;
    public $mesesMora = 0;
    public $selectedQuota;
    public $selectedQuotaId;

    protected $rules = [
        'selectedEstudiantId' => 'required|integer|exists:estudiants,id',
        'selectedQuotaId'       => 'required|integer|exists:cuentaxpagars,id',
    ];

    /**
     * Renderiza la vista
     */
    public function render()
    {
        return view('livewire.administracion.recargos.generar-recargo');
    }

    /**
     * Busca estudiantes
     */
    public function updatedSearch()
    {
        if (strlen($this->search) > 2) {
            $this->students = Estudiant::where('name', 'like', "%{$this->search}%")
                ->orWhere('lastname', 'like', "%{$this->search}%")
                ->orWhere('ci_estudiant', 'like', "%{$this->search}%")
                ->limit(10)
                ->get();
        } else {
            $this->students = [];
        }
    }

    /**
     * Selecciona estudiante
     */
    public function selectStudent($id)
    {
        $this->selectedEstudiant = Estudiant::find($id);
        $this->selectedEstudiantId = $id;

        // buscar cuotas vencidas con fecha de expiración pasada y sin estar canceladas completamente
        $this->quotas = $this->selectedEstudiant->exchange_expire_bills;

        $this->students = [];
        $this->search = '';
    }

    /**
     * Selecciona cuota y calcula recargo
     */
    public function selectQuota($quotaId)
    {
        $this->selectedQuota = Cuentaxpagar::findOrFail($quotaId);
        $this->selectedQuotaId = $this->selectedQuota->id;

        $montoAdeudado = round($this->selectedQuota->TotalExchangeMontoCuentasXPagarAdeudado($this->selectedEstudiantId ), 2);


        $this->montoOriginal = $montoAdeudado;

        // Calcular meses de mora
        $fechaMora = $this->selectedQuota->date_late_payment ?? $this->selectedQuota->date_expiration;
        $this->mesesMora = Carbon::parse($fechaMora)->diffInMonths(Carbon::now());

        // Fórmula de recargo (ejemplo: 1% mensual sobre lo adeudado)
        $this->recargoTotal = $montoAdeudado * 0.01;

        $this->confirming = true;
    }

    /**
     * Genera el recargo en BD
     */
    public function generarRecargo()
    {
        $this->validate();

        $quotaRM = $this->selectedQuota; // cuota seleccionada
        $estudiant = $this->selectedEstudiant;

        // Concepto original de la cuota
        $conceptoOriginal = $quotaRM->conceptopagos()->first();
        if (!$conceptoOriginal) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => "La cuota {$quotaRM->id} no tiene concepto asociado.",
                'icon' => 'error',
            ]);
            return;
        }

        $montoOriginal = $conceptoOriginal->exchange_ammount;
        if (!$montoOriginal || $montoOriginal <= 0) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => "La cuota {$quotaRM->id} tiene monto inválido.",
                'icon' => 'error',
            ]);
            return;
        }

        // Calcular meses de mora
        $fechaMora = $quotaRM->date_late_payment ?? $quotaRM->date_expiration;
        $mesesMora = Carbon::parse($fechaMora)->diffInMonths(Carbon::now());
        $mesesMora = ($mesesMora <= 0) ? 1 : $mesesMora;

        // Recargo acumulado (máx. 12 meses, 1% mensual sobre monto original)
        $recargoTotal = min($mesesMora, 12) * ($montoOriginal * 0.01);

        // Crear/actualizar cuota de recargo
        $quotaObj = Cuentaxpagar::firstOrCreate(
            [
                'estudiant_id' => $estudiant->id,
                'name' => $quotaRM->name . ' RM1',
                'planpago_id' => $quotaRM->planpago_id,
            ],
            [
                'type' => 'INDIVIDUAL',
                'date_expiration' => $quotaRM->date_expiration,
                'date_calendar_start' => $quotaRM->date_expiration,
                'date_calendar_end' => $quotaRM->date_expiration,
                'description' => 'Recargo por Morosidad',
                'observations' => 'Recargo por Morosidad',
                'status_inscription' => false,
                'status_bad' => $quotaRM->status_bad,
                'status_late_payment' => true,
                'enable_late_payment' => false,
            ]
        );

        // Crear/actualizar concepto de recargo
        ConceptoPago::updateOrCreate(
            [
                'cuentaxpagar_id' => $quotaObj->id,
                'quota_id' => $quotaRM->id,
            ],
            [
                'nom_concepto_pago_id' => $conceptoOriginal->nom_concepto_pago_id,
                'concepto_description' => $conceptoOriginal->concepto_description . ' Recargo por Morosidad',
                'concepto_observations' => $conceptoOriginal->concepto_observations . ' Recargo por Morosidad',
                'concepto_ammount' => $recargoTotal,
                'exchange_ammount' => $recargoTotal,
                'status_modifiable' => 'false',
                'status_discount' => 'false',
                'status_active' => 'true',
            ]
        );

        $this->quotas = $this->selectedEstudiant->exchange_expire_bills;

        // Reset del estado
        $this->reset(['confirming', 'montoOriginal', 'recargoTotal', 'mesesMora', 'selectedQuota']);

        // Mensaje SweetAlert de éxito
        $this->dispatchBrowserEvent('swal', [
            'title' => 'Recargo generado',
            'text' => "Recargo aplicado correctamente al estudiante {$estudiant->fullname}.",
            'icon' => 'success',
        ]);
    }


}