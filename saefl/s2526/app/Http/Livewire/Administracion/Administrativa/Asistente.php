<?php

namespace App\Http\Livewire\Administracion\Administrativa;

use Livewire\Component;
use App\Models\app\Estudiant;
use App\Models\app\Planpago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Estudiante\Administrativa as Administrativa;
use App\Models\app\Estudiante\Inscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class Asistente extends Component
{
    // Paso 1
    public $search = '';
    public $students = [];

    // Selección estudiante
    public $selectedEstudiant = null;
    public $selectedEstudiantId = null;
    public $isAcademicInscribed = false;
    public $selectedPlanName = '';

    // Paso 2 (planes)
    public $plans = [];
    public $selectedPlanId = null;

    // Paso 3 (cuotas del plan)
    public $planQuotas = [];
    public $selectedQuotas = [];

    // UI helpers
    public $step = 1;
    public $loadingQuotas = false;

    protected $listeners = [
        'saveConfirmed' => 'saveConfirmed'
    ];

    public function mount()
    {
        try {
            $this->plans = Planpago::list_planpago_inscription();
        } catch (Exception $e) {
            $this->plans = [];
        }
    }

    public function render()
    {
        return view('livewire.administracion.administrativa.asistente');
    }

    /* ---------------------------
       - Paso 1: búsqueda de estudiantes
       --------------------------- */
    public function updatedSearch()
    {
        $q = trim($this->search);
        if (strlen($q) < 2) {
            $this->students = [];
            return;
        }

        $found = Estudiant::query()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('lastname', 'like', "%{$q}%")
            ->orWhere('ci_estudiant', 'like', "%{$q}%")
            ->limit(12)
            ->get();

        $this->students = $found->map(function ($s) {

            $s->is_solvent = (round($s->exchange_ammount_expire_bill ?? 0, 2) == 0);

            // Inscripción académica
            $s->is_academic_inscribed = Inscripcion::where('estudiant_id', $s->id)->exists();

            // Inscripción administrativa que sí afecta inscripción
            $s->is_admin_inscribed = Administrativa::where('estudiant_id', $s->id)
                ->whereHas('planpago', function ($q) {
                    $q->where('status_inscription_affects', Planpago::INSCRIPTION_AFFECTS_TRUE);
                })
                ->exists();

            // Inscripción administrativa que NO afecta inscripción
            $s->has_admin_non_affecting = Administrativa::where('estudiant_id', $s->id)
                ->whereHas('planpago', function ($q) {
                    $q->where('status_inscription_affects', Planpago::INSCRIPTION_AFFECTS_FALSE);
                })
                ->exists();

            return $s;
        })->toArray();
    }



    public function selectStudent($id)
    {
        $student = Estudiant::find($id);

        if (! $student) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => "Estudiante no encontrado.",
                'icon' => 'error'
            ]);
            return;
        }

        // ✅ NUEVA VALIDACIÓN: verificar solvencia
        $montoVencido = round($student->exchange_ammount_expire_bill ?? 0, 2);
        if ($montoVencido > 0) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Estudiante no está solvente',
                'text' => "El estudiante tiene un saldo vencido de USD " . number_format($montoVencido, 2) . ". No puede realizar inscripción extemporánea hasta regularizar su situación.",
                'icon' => 'warning'
            ]);
            return;
        }

        // Validar que NO esté inscrito administrativamente (afecta inscripción)
        $yaAdmin = Administrativa::where('estudiant_id', $student->id)
            ->whereHas('planpago', function ($q) {
                $q->where('status_inscription_affects', Planpago::INSCRIPTION_AFFECTS_TRUE);
            })
            ->exists();

        if ($yaAdmin) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Atención',
                'text' => "El estudiante ya está inscrito administrativamente en un plan que afecta inscripción.",
                'icon' => 'warning'
            ]);
            return;
        }

        $this->selectedEstudiant = $student;
        $this->selectedEstudiantId = $student->id;
        $this->isAcademicInscribed = Inscripcion::where('estudiant_id', $student->id)->exists();

        $this->selectedPlanId = null;
        $this->planQuotas = [];
        $this->selectedQuotas = [];
        $this->step = 2;

        $this->students = [];
        $this->search = '';
    }


    /* ---------------------------
       - Paso 2: seleccionar plan de pago
       --------------------------- */
    /**
     * Detecta cuando cambia el plan seleccionado y carga cuotas.
     */
    public function updatedSelectedPlanId($value)
    {
        if ($value) {
            $this->loadPlanQuotas();
        } else {
            $this->planQuotas = [];
        }
    }

    public function selectPlan($planId)
    {
        $this->selectedPlanId = $planId;

        // Obtener el nombre del plan
        $plan = Planpago::find($planId);
        $this->selectedPlanName = $plan ? $plan->name : 'Plan no encontrado';

        $this->loadPlanQuotas();
        $this->step = 3;
    }

    public function loadPlanQuotas()
    {
        $this->loadingQuotas = true;
        $this->planQuotas = [];
        $this->selectedQuotas = [];

        try {
            $plan = Planpago::find($this->selectedPlanId);
            if (! $plan) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Error',
                    'text' => 'Plan de pago no encontrado.',
                    'icon' => 'error'
                ]);
                $this->loadingQuotas = false;
                return;
            }

            //$cuotas = $plan->cuentaxpagars()->get();

            $cuotas = $plan->cuentaxpagars()
                ->where('type', 'GENERAL')
                ->where('status_late_payment', false)
                ->where('enable_late_payment', true)
                ->get();

            $now = Carbon::now();
            foreach ($cuotas as $q) {
                $monto = (float) $q->conceptopagos()->sum('exchange_ammount');
                $dateExpiration = $q->date_expiration ? Carbon::parse($q->date_expiration) : null;
                $expired = $dateExpiration ? $dateExpiration->lt($now) : false;

                $this->planQuotas[] = [
                    'id' => $q->id,
                    'name' => $q->name,
                    'date_expiration' => $q->date_expiration,
                    'date_late_payment' => $q->date_late_payment,
                    'monto' => round($monto, 2),
                    'expired' => $expired,
                ];
            }
        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Error al cargar cuotas del plan: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        } finally {
            $this->loadingQuotas = false;
        }
    }

    /* ---------------------------
        - Paso 3: seleccionar cuotas (checkbox)
       --------------------------- */
    public function toggleQuotaSelection($quotaId)
    {
        $pq = collect($this->planQuotas)->firstWhere('id', $quotaId);
        if (! $pq) return;

        if (! $pq['expired']) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No permitido',
                'text' => "La cuota '{$pq['name']}' no está vencida. No se puede aplicar recargo.",
                'icon' => 'info'
            ]);
            return;
        }

        if (isset($this->selectedQuotas[$quotaId])) {
            unset($this->selectedQuotas[$quotaId]);
        } else {
            $meses = $this->calcularMesesMoraByDates($pq['date_late_payment'] ?? $pq['date_expiration']);
            $recargo = round(min($meses, 12) * ($pq['monto'] * 0.01), 2);

            $this->selectedQuotas[$quotaId] = [
                'id' => $quotaId,
                'name' => $pq['name'],
                'monto' => $pq['monto'],
                'meses' => $meses,
                'recargo' => $recargo,
            ];
        }
    }

    public function calcularMesesMoraByDates($dateString): int
    {
        if (empty($dateString)) return 0;
        try {
            $fechaInicioMora = Carbon::parse($dateString);
        } catch (Exception $e) {
            return 0;
        }
        $hoy = Carbon::now();
        if ($hoy->lt($fechaInicioMora)) return 0;
        $meses = $fechaInicioMora->diffInMonths($hoy);
        return ($meses <= 0) ? 1 : $meses;
    }

    public function goToSummary()
    {
        if (empty($this->selectedQuotas)) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Atención',
                'text' => 'Debe seleccionar al menos una cuota vencida para aplicar recargo, o continuar para crear la inscripción sin recargos.',
                'icon' => 'warning'
            ]);
            return;
        }
        $this->step = 4;
    }

    public function backToStep($n)
    {
        $this->step = $n;
    }

    /* ---------------------------
       - Paso 4: confirmar y guardar
       --------------------------- */
    public function askSave()
    {
        $totalRecargo = collect($this->selectedQuotas)->sum('recargo');
        $count = count($this->selectedQuotas);

        $this->dispatchBrowserEvent('swal:question', [
            'message' => 'Confirmar Inscripción extemporánea',
            'text' => "Se aplicarán {$count} recargos por un total de " . number_format($totalRecargo, 2) . ". ¿Desea continuar?",
            'type' => 'warning',
            'method' => 'saveConfirmed',
            'id' => null
        ]);
    }

    public function saveConfirmed($dummy = null)
    {
        if (! $this->selectedEstudiantId || ! $this->selectedPlanId) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Estudiante y plan deben estar seleccionados.',
                'icon' => 'error'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $administrativa = Administrativa::updateOrCreate(
                ['estudiant_id' => $this->selectedEstudiantId],
                [
                    'planpago_id' => $this->selectedPlanId,
                    'user_id' => auth()->id() ?? null,
                    'observations' => 'Inscripción extemporánea por asistente',
                ]
            );

            foreach ($this->selectedQuotas as $sq) {
                $quotaTemplate = Cuentaxpagar::find($sq['id']);
                if (! $quotaTemplate) continue;

                $conceptoOriginal = $quotaTemplate->conceptopagos()->first();
                if (! $conceptoOriginal) continue;

                $montoOriginal = $conceptoOriginal->exchange_ammount;
                if (! $montoOriginal || $montoOriginal <= 0) continue;

                $fechaMora = $quotaTemplate->date_late_payment ?? $quotaTemplate->date_expiration;
                $mesesMora = $this->calcularMesesMoraByDates($fechaMora);
                $mesesMora = ($mesesMora <= 0) ? 1 : $mesesMora;

                $recargoTotal = min($mesesMora, 12) * ($montoOriginal * 0.01);

                $quotaObj = Cuentaxpagar::firstOrCreate(
                    [
                        'estudiant_id' => $this->selectedEstudiantId,
                        'name' => $quotaTemplate->name . ' RMA1',
                        'planpago_id' => $this->selectedPlanId,
                        'quota_original_id'  => $quotaTemplate->id, // 👈 clave para unicidad
                    ],
                    [
                        'type' => 'INDIVIDUAL',
                        'date_expiration' => $quotaTemplate->date_expiration,
                        'date_calendar_start' => $quotaTemplate->date_expiration,
                        'date_calendar_end' => $quotaTemplate->date_expiration,
                        'description' => 'Recargo por Morosidad',
                        'observations' => 'Recargo por Morosidad',
                        'status_inscription' => false,
                        'status_bad' => $quotaTemplate->status_bad,
                        'status_late_payment' => true,
                        'enable_late_payment' => false,
                    ]
                );

                ConceptoPago::updateOrCreate(
                    [
                        'cuentaxpagar_id' => $quotaObj->id,
                        'quota_id' => $quotaTemplate->id,
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
            }

            DB::commit();

            $this->dispatchBrowserEvent('swal', [
                'title' => 'Éxito',
                'text' => 'Inscripción extemporánea y recargos aplicados correctamente.',
                'icon' => 'success'
            ]);

            $this->resetStateAfterSave();
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Fallo al guardar: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    protected function resetStateAfterSave()
    {
        $this->search = '';
        $this->students = [];
        $this->selectedEstudiant = null;
        $this->selectedEstudiantId = null;
        $this->isAcademicInscribed = false;
        $this->selectedPlanId = null;
        $this->planQuotas = [];
        $this->selectedQuotas = [];
        $this->step = 1;
    }
}
