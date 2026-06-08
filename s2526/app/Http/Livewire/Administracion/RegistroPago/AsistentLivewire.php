<?php

namespace App\Http\Livewire\Administracion\RegistroPago;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

// Models
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Institucion;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Recurso;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\Pago;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\ExchangeRate;
use Exception;

class AsistentLivewire extends Component
{
    // Pasos del asistente
    public $currentStep = 1;
    public $totalSteps = 7;

    // Datos del pago reportado (Paso 1)
    public $representant_id;
    public $method_pay_id;
    public $banco_id;
    public $number_i_pay;
    public $date_transaction;
    public $date_payment;
    public $ingreso_ammount;
    public $exchange_ammount;
    public $ingreso_observations;
    public $person_bill_ci;
    public $person_bill_name;

    // Recursos disponibles (Paso 2)
    public $selectedResources = [];
    public $availableAbonos;
    public $availableCreditosAFavor;

    // Cuotas vencidas (Paso 3)
    public $selectedOverdueQuotas = [];
    public $overdueQuotasByStudent;

    // Cuotas no vencidas (Paso 4)
    public $selectedNotDueQuotas = [];
    public $notDueQuotasByStudent;

    // Vista previa (Paso 5)
    public $paymentSummary = [];

    // Datos auxiliares
    public $representants = [];
    public $metodosPago = [];
    public $bancos = [];
    public $estudiantes = [];
    public $representant;
    public $exchange_rate_current;
    public $ingresoId;
    public $institucion;
    public $registroPagoCombinadoId;
    public $registroPagoId;
    public $hideIngresoFields = false;
    public $registroPagoCombinado;

    // Nuevos: Manejo de crédito combinado
    public $firstRegistroPagoId = null;
    public $disabled;

    protected $rules = [
        'representant_id' => 'required|exists:representants,id',
        'method_pay_id' => 'required|exists:metodo_pagos,id',
        'banco_id' => 'nullable|exists:bancos,id',
        'number_i_pay' => 'required|max:32',
        'date_transaction' => 'required|date',
        'date_payment' => 'required|date',
        'ingreso_ammount' => 'required|numeric|min:0',
        'exchange_ammount' => 'nullable|numeric|min:0',
        'ingreso_observations' => 'nullable|string',
        'person_bill_ci' => 'nullable|string|max:20',
        'person_bill_name' => 'nullable|string|max:255',
    ];

    protected $listeners = ['showSwal'];

    public function mount($representant_id = null)
    {
        $this->institucion = Institucion::first();

        // Inicializar colecciones vacías
        $this->availableAbonos = collect();
        $this->availableCreditosAFavor = collect();
        $this->overdueQuotasByStudent = collect();
        $this->notDueQuotasByStudent = collect();

        // Si se pasa representant_id como parámetro, usarlo
        if ($representant_id) {
            $this->representant_id = $representant_id;
            $this->representant = Representant::find($representant_id);
            $this->person_bill_ci = $this->representant->ci_representant;
            $this->person_bill_name = $this->representant->name;

            // Si el representante existe, cargar sus datos automáticamente
            if ($this->representant) {
                $this->loadStudentsForRepresentant();
                $this->loadAvailableResources();
            }
        }

        $this->loadInitialData();

        $this->exchange_rate_current = ExchangeRate::whereDate('date', Carbon::now()->format('Y-m-d'))->first();

        if (!$this->exchange_rate_current) {
            session()->flash('error', 'No se encontró tasa de cambio configurada. El asistente de pagos no puede funcionar sin una tasa de cambio activa.');
            $this->disabled = true; // Nueva propiedad para deshabilitar el componente
            return;
        }

        $this->date_transaction = Carbon::now()->format('Y-m-d');
        $this->date_payment = Carbon::now()->format('Y-m-d');

        // Default values for step 1 fields
        // $this->method_pay_id = '3';
        // $this->banco_id = '3';
        // $this->number_i_pay = rand(100000, 10000000000000);
        // $this->date_transaction = Carbon::now()->format('Y-m-d');
        // $this->date_payment = Carbon::now()->format('Y-m-d');
        // $this->exchange_ammount = 200;
        // $this->ingreso_ammount = $this->exchange_ammount * ($this->exchange_rate_current->ammount ?? 1);
        // $this->ingreso_observations = 'OBS';
        // $this->person_bill_ci = '1234567890';
        // $this->person_bill_name = 'NOE DOMINGUEZ';
    }

    public function loadInitialData()
    {
        $this->representants = Representant::select('id', 'name', 'ci_representant')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($rep) {
                return [$rep->id => $rep->name . ' - ' . $rep->ci_representant];
            });

        $this->metodosPago = MetodoPago::list_metodo_pago();
        $this->bancos = Banco::pluck('name', 'id');

        $this->loadQuotas();
    }

    public function updatedRepresentantId()
    {
        if ($this->representant_id) {
            $this->representant = Representant::find($this->representant_id);
            $this->loadStudentsForRepresentant();
            $this->loadAvailableResources();

            // Limpiar selecciones previas cuando cambia el representante
            $this->selectedResources = [];
            $this->selectedOverdueQuotas = [];
            $this->selectedNotDueQuotas = [];
            $this->overdueQuotasByStudent = collect();
            $this->notDueQuotasByStudent = collect();
        }
    }

    public function updatedExchangeAmmount()
    {
        $this->calculateIngresoAmmountFromExchange();
    }

    public function updatedDateTransaction()
    {
        if ($this->exchange_ammount) {
            $this->calculateIngresoAmmountFromExchange();
            $this->date_payment = $this->date_transaction;
        }
    }

    public function updatedIngresoAmmount()
    {
        $this->calculateExchangeAmmountFromIngreso();
    }

    private function calculateExchangeAmmountFromIngreso()
    {
        if ($this->ingreso_ammount && $this->date_transaction) {
            // Obtener la tasa de cambio para la fecha seleccionada
            $exchangeRate = ExchangeRate::whereDate('date', $this->date_transaction)->first();

            if (!$exchangeRate) {
                // Si no hay tasa para la fecha específica, usar la tasa actual
                $currentRate = ExchangeRate::getAmmountExchange();

                if ($currentRate) {
                    $this->exchange_ammount = round($this->ingreso_ammount / $currentRate, 2);
                } else {
                    session()->flash('warning', 'No se encontró tasa de cambio disponible para realizar el cálculo.');
                }
            } else {
                $this->exchange_ammount = round($this->ingreso_ammount / $exchangeRate->ammount, 2);
            }
        }
    }

    private function calculateIngresoAmmountFromExchange()
    {
        if (isset($this->exchange_ammount) && $this->exchange_ammount && $this->date_transaction) {
            // Obtener la tasa de cambio para la fecha seleccionada
            $exchangeRate = ExchangeRate::whereDate('date', $this->date_transaction)->first();

            if ($exchangeRate) {
                $calculatedAmount = round($this->exchange_ammount * $exchangeRate->ammount, 2);
                if ($this->ingreso_ammount != $calculatedAmount) {
                    $this->ingreso_ammount = $calculatedAmount;
                }
            } else {
                // Si no hay tasa para esa fecha, usar la tasa actual
                $currentRate = ExchangeRate::getAmmountExchange();
                if ($currentRate) {
                    $calculatedAmount = round($this->exchange_ammount * $currentRate, 2);
                    if ($this->ingreso_ammount != $calculatedAmount) {
                        $this->ingreso_ammount = $calculatedAmount;
                    }
                } else {
                    session()->flash('warning', 'No se encontró tasa de cambio para la fecha seleccionada: ' . $this->date_transaction);
                }
            }
        }
    }

    public function getCurrentExchangeRate()
    {
        if ($this->date_transaction) {
            $exchangeRate = ExchangeRate::whereDate('date', $this->date_transaction)
                ->whereNotNull('ammount')
                ->first();

            if ($exchangeRate) {
                return $exchangeRate->ammount;
            }

            return ExchangeRate::getAmmountExchange();
        }

        return ExchangeRate::getAmmountExchange();
    }

    public function loadStudentsForRepresentant()
    {
        $this->estudiantes = Estudiant::where('representant_id', $this->representant_id)
            ->select('id', 'name', 'lastname')
            ->get();
    }

    public function loadAvailableResources()
    {
        if ($this->representant_id && $this->representant) {
            $this->availableAbonos = $this->representant->abonos_disponibles_exchange;
            $this->availableCreditosAFavor = $this->representant->creditos_disponibles;
        } else {
            $this->availableAbonos = collect();
            $this->availableCreditosAFavor = collect();
        }
    }

    /**
     * Método temporal para pruebas - simular créditos a favor
     */
    public function createTestCredits()
    {
        if (!$this->representant_id) {
            return;
        }

        $existingCredits = $this->representant->creditos_disponibles;

        if ($existingCredits->count() == 0) {
            $estudiante = $this->representant->estudiants()->first();

            if ($estudiante) {
                CreditoAFavor::create([
                    'representant_id' => $this->representant_id,
                    'estudiant_id' => $estudiante->id,
                    'registro_pago_id' => null,
                    'ingreso_id' => null,
                    'credito_description' => 'CRÉDITO DE PRUEBA #1',
                    'credito_observations' => 'Crédito generado para pruebas del sistema - ' . Carbon::now()->format('Y-m-d H:i:s'),
                    'credito_ammount' => 500.00,
                    'exchange_ammount' => 10.00,
                    'status_omitted' => 'false',
                ]);

                CreditoAFavor::create([
                    'representant_id' => $this->representant_id,
                    'estudiant_id' => $estudiante->id,
                    'registro_pago_id' => null,
                    'ingreso_id' => null,
                    'credito_description' => 'CRÉDITO DE PRUEBA #2',
                    'credito_observations' => 'Segundo crédito generado para pruebas del sistema - ' . Carbon::now()->format('Y-m-d H:i:s'),
                    'credito_ammount' => 250.00,
                    'exchange_ammount' => 5.00,
                    'status_omitted' => 'false',
                ]);

                $this->loadAvailableResources();
                session()->flash('success', 'Se han creado créditos de prueba para testing');
            }
        }
    }

    /**
     * Método para limpiar créditos de prueba
     */
    public function cleanTestCredits()
    {
        if (!$this->representant_id) {
            return;
        }

        CreditoAFavor::where('representant_id', $this->representant_id)
            ->where('credito_description', 'LIKE', 'CRÉDITO DE PRUEBA%')
            ->delete();

        $this->loadAvailableResources();
        session()->flash('success', 'Créditos de prueba eliminados');
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate();
            $this->loadQuotas();
        }

        // Validation for Step 2: Total resources must be > 0.01 in reference currency
        if ($this->currentStep == 2) {
            $currentTotals = $this->calculateTotals();
            $totalAvailableExchangeFunds = $currentTotals['total_exchange_income'];

            if ($totalAvailableExchangeFunds <= 0.01) {
                $this->showSwal(
                    '¡Atención!',
                    'Los recursos no son suficientes para este procedimiento. El monto total de los recursos (ingreso principal + abonos + créditos seleccionados) debe ser mayor a 0.01 en moneda de referencia.',
                    'warning'
                );
                return;
            }
        }

        // Validation for Step 4: At least one quota must be selected
        if ($this->currentStep == 4) {
            if (empty($this->selectedOverdueQuotas) && empty($this->selectedNotDueQuotas)) {
                $this->showSwal(
                    '¡Atención!',
                    'Debe seleccionar al menos una cuota (vencida o no vencida) para continuar.',
                    'warning'
                );
                return;
            }
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }

        if ($this->currentStep == 5) {
            $this->generatePaymentSummary();
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function loadQuotas()
    {
        $this->overdueQuotasByStudent = collect();
        $this->notDueQuotasByStudent = collect();

        // Obtener la tasa de cambio actual para los cálculos
        $exchange_rate_current = ExchangeRate::getAmmountExchange();

        // Cargar cuotas por cada estudiante del representante
        foreach ($this->estudiantes as $estudiante) {
            $estudiant = Estudiant::find($estudiante->id);

            if ($estudiant) {
                // Vencidas
                $exchange_expire_bills = $estudiant->exchange_expire_bills;
                if ($exchange_expire_bills->count() > 0) {
                    $expiredQuotas = $exchange_expire_bills->map(function ($quota) use ($estudiant, $exchange_rate_current) {
                        $total_a_pagar = round($quota->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id),2);
                        // hardcode, en acuerdo con admon, se consideran montos superiores a USD 0.009

                        $is_paid = ($total_a_pagar <= 0);
                        $ammount_bs = ($exchange_rate_current) ? ($exchange_rate_current * $total_a_pagar) : null;

                        $quota->pending_amount = $ammount_bs ?: 0;
                        $quota->pending_exchange_amount = $total_a_pagar ?: 0;
                        $quota->is_paid = $is_paid;

                        $quota->total_amount = $quota->conceptopagos->sum('concepto_ammount');
                        $quota->total_exchange_amount = $quota->conceptopagos->sum('exchange_ammount');

                        return $quota;
                    });

                    $this->overdueQuotasByStudent->put($estudiante->id, [
                        'student' => $estudiant,
                        'quotas' => $expiredQuotas
                    ]);
                }

                // No vencidas
                $exchange_unexpired_bills = $estudiant->exchange_unexpired_bills;
                if ($exchange_unexpired_bills->count() > 0) {
                    $unexpiredQuotas = $exchange_unexpired_bills->map(function ($quota) use ($estudiant, $exchange_rate_current) {
                        $total_a_pagar = round($quota->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id),2); 
                        // hardcode, en acuerdo con admon, se consideran montos superiores a USD 0.009
                        $is_paid = ($total_a_pagar <= 0);
                        $ammount_bs = ($exchange_rate_current) ? ($exchange_rate_current * $total_a_pagar) : null;

                        $quota->pending_amount = $ammount_bs ?: 0;
                        $quota->pending_exchange_amount = $total_a_pagar ?: 0;
                        $quota->is_paid = $is_paid;

                        $quota->total_amount = $quota->conceptopagos->sum('concepto_ammount');
                        $quota->total_exchange_amount = $quota->conceptopagos->sum('exchange_ammount');

                        return $quota;
                    });

                    $this->notDueQuotasByStudent->put($estudiante->id, [
                        'student' => $estudiant,
                        'quotas' => $unexpiredQuotas
                    ]);
                }
            }
        }
    }

    public function generatePaymentSummary()
    {
        $this->paymentSummary = [
            'ingreso' => [
                'method_pay' => MetodoPago::find($this->method_pay_id)->name ?? '',
                'banco' => $this->banco_id ? Banco::find($this->banco_id)->name : '',
                'number_i_pay' => $this->number_i_pay,
                'date_transaction' => $this->date_transaction,
                'ingreso_ammount' => $this->ingreso_ammount,
                'exchange_ammount' => $this->exchange_ammount,
            ],
            'resources' => $this->getSelectedResourcesDetails(),
            'quotas' => $this->getSelectedQuotasDetails(),
            'totals' => $this->calculateTotals(),
        ];
    }

    public function getSelectedResourcesDetails()
    {
        $resources = [];

        foreach ($this->selectedResources as $resourceId) {
            $parts = explode('_', $resourceId);
            $type = $parts[0];
            $id = (int) $parts[1];

            if ($type === 'abono') {
                $abono = $this->availableAbonos->firstWhere('id', $id);
                if ($abono) {
                    $resources[] = [
                        'type' => 'Abono',
                        'description' => 'Abono disponible',
                        'amount' => $abono->abono_ammount,
                        'exchange_amount' => $abono->exchange_ammount,
                        'method' => $abono->metodo_pago,
                    ];
                }
            } elseif ($type === 'credito') {
                $credito = $this->availableCreditosAFavor->firstWhere('id', $id);
                if ($credito) {
                    $resources[] = [
                        'type' => 'Crédito a Favor',
                        'description' => $credito->credito_description,
                        'amount' => $credito->credito_ammount,
                        'exchange_amount' => $credito->exchange_ammount,
                    ];
                }
            }
        }

        return $resources;
    }

    public function getSelectedQuotasDetails()
    {
        $quotas = [];

        // Cuotas vencidas seleccionadas
        foreach ($this->selectedOverdueQuotas as $quotaSelection) {
            $parts = explode('_', $quotaSelection);
            $quotaId = (int)$parts[0];
            $estudiantId = (int)$parts[1];
            $studentSel = Estudiant::find($estudiantId);

            $quota = $this->findQuotaInOverdueByStudent($quotaId);

            if ($quota) {
                $pendingExchangeAmount = is_object($quota) ?
                    $quota->pending_exchange_amount : ($quota['pending_exchange_amount'] ?? 0);

                $pendingAmount = is_object($quota) ?
                    $quota->pending_amount : ($quota['pending_amount'] ?? 0);

                // Calcular lo que realmente se pagará (mínimo entre pendiente y recursos disponibles)
                $exchangeAmountToPay = $pendingExchangeAmount;
                $amountToPay = $pendingAmount;

                if (is_object($quota)) {
                    $quotas[] = [
                        'cuentaxpagar_id' => $quotaId,
                        'estudiant_id' => $estudiantId,
                        'ci_estudiant' => $studentSel?->ci_estudiant,
                        'name' => $quota->name,
                        'student' => $quota->estudiant->name . ' ' . $quota->estudiant->lastname,
                        'due_date' => $quota->date_expiration,
                        'amount' => $amountToPay,
                        'exchange_amount' => $exchangeAmountToPay,
                        'pending_amount' => $pendingAmount,
                        'pending_exchange_amount' => $pendingExchangeAmount,
                        'total_amount' => $quota->total_amount,
                        'total_exchange_amount' => $quota->total_exchange_amount ?? 0,
                        'status' => 'Vencida',
                        'will_be_partial' => false,
                    ];
                } elseif (is_array($quota)) {
                    $student = null;
                    foreach ($this->overdueQuotasByStudent as $studentData) {
                        foreach ($studentData['quotas'] as $q) {
                            if ((is_array($q) && isset($q['id']) && $q['id'] == $quotaId) ||
                                (is_object($q) && $q->id == $quotaId)
                            ) {
                                $student = $studentData['student'];
                                break 2;
                            }
                        }
                    }

                    if ($student) {
                        $quotas[] = [
                            'cuentaxpagar_id' => $quotaId,
                            'estudiant_id' => $estudiantId,
                            'ci_estudiant' => $studentSel?->ci_estudiant,
                            'name' => $quota['name'] ?? 'N/A',
                            'student' => (is_object($student) ? $student->name . ' ' . $student->lastname : (isset($student['name']) ? $student['name'] . ' ' . $student['lastname'] : 'N/A')),
                            'due_date' => $quota['date_expiration'] ?? 'N/A',
                            'amount' => $amountToPay,
                            'exchange_amount' => $exchangeAmountToPay,
                            'pending_amount' => $pendingAmount,
                            'pending_exchange_amount' => $pendingExchangeAmount,
                            'total_amount' => $quota['total_amount'] ?? 0,
                            'total_exchange_amount' => $quota['total_exchange_amount'] ?? 0,
                            'status' => 'Vencida',
                            'will_be_partial' => false,
                        ];
                    }
                }
            }
        }

        // Cuotas no vencidas seleccionadas (misma lógica)
        foreach ($this->selectedNotDueQuotas as $quotaSelection) {
            $parts = explode('_', $quotaSelection);
            $quotaId = (int)$parts[0];
            $estudiantId = (int)$parts[1];
            $studentSel = Estudiant::find($estudiantId);

            $quota = $this->findQuotaInNotDueByStudent($quotaId);

            if ($quota) {
                $pendingExchangeAmount = is_object($quota) ?
                    $quota->pending_exchange_amount : ($quota['pending_exchange_amount'] ?? 0);

                $pendingAmount = is_object($quota) ?
                    $quota->pending_amount : ($quota['pending_amount'] ?? 0);

                // Calcular lo que realmente se pagará
                $exchangeAmountToPay = $pendingExchangeAmount;
                $amountToPay = $pendingAmount;

                if (is_object($quota)) {
                    $quotas[] = [
                        'cuentaxpagar_id' => $quotaId,
                        'estudiant_id' => $estudiantId,
                        'ci_estudiant' => $studentSel?->ci_estudiant,
                        'name' => $quota->name,
                        'student' => $quota->estudiant->name . ' ' . $quota->estudiant->lastname,
                        'due_date' => $quota->date_expiration,
                        'amount' => $amountToPay,
                        'exchange_amount' => $exchangeAmountToPay,
                        'pending_amount' => $pendingAmount,
                        'pending_exchange_amount' => $pendingExchangeAmount,
                        'total_amount' => $quota->total_amount,
                        'total_exchange_amount' => $quota->total_exchange_amount ?? 0,
                        'status' => 'No Vencida',
                        'will_be_partial' => false,
                    ];
                } elseif (is_array($quota)) {
                    $student = null;
                    foreach ($this->notDueQuotasByStudent as $studentData) {
                        foreach ($studentData['quotas'] as $q) {
                            if ((is_array($q) && isset($q['id']) && $q['id'] == $quotaId) ||
                                (is_object($q) && $q->id == $quotaId)
                            ) {
                                $student = $studentData['student'];
                                break 2;
                            }
                        }
                    }
                    if ($student) {
                        $quotas[] = [
                            'cuentaxpagar_id' => $quotaId,
                            'estudiant_id' => $estudiantId,
                            'ci_estudiant' => $studentSel?->ci_estudiant,
                            'name' => $quota['name'] ?? 'N/A',
                            'student' => (is_object($student) ? $student->name . ' ' . $student->lastname : (isset($student['name']) ? $student['name'] . ' ' . $student['lastname'] : 'N/A')),
                            'due_date' => $quota['date_expiration'] ?? 'N/A',
                            'amount' => $amountToPay,
                            'exchange_amount' => $exchangeAmountToPay,
                            'pending_amount' => $pendingAmount,
                            'pending_exchange_amount' => $pendingExchangeAmount,
                            'total_amount' => $quota['total_amount'] ?? 0,
                            'total_exchange_amount' => $quota['total_exchange_amount'] ?? 0,
                            'status' => 'No Vencida',
                            'will_be_partial' => false,
                        ];
                    }
                }
            }
        }

        return $quotas;
    }

    private function findQuotaInOverdueByStudent($quotaId)
    {
        foreach ($this->overdueQuotasByStudent as $studentData) {
            foreach ($studentData['quotas'] as $quota) {
                if (is_array($quota) && isset($quota['id']) && $quota['id'] == $quotaId) {
                    return $quota;
                } elseif (is_object($quota) && $quota->id == $quotaId) {
                    return $quota;
                }
            }
        }
        return null;
    }

    private function findQuotaInNotDueByStudent($quotaId)
    {
        foreach ($this->notDueQuotasByStudent as $studentData) {
            foreach ($studentData['quotas'] as $quota) {
                if (is_array($quota) && isset($quota['id']) && $quota['id'] == $quotaId) {
                    return $quota;
                } elseif (is_object($quota) && $quota->id == $quotaId) {
                    return $quota;
                }
            }
        }
        return null;
    }

    public function getResourceTotals()
    {
        $total_abonos_ammount = $this->availableAbonos->sum('abono_ammount');
        $total_abonos_exchange_ammount = $this->availableAbonos->sum('exchange_ammount');

        $total_creditos_ammount = $this->availableCreditosAFavor->sum('credito_ammount');
        $total_creditos_exchange_ammount = $this->availableCreditosAFavor->sum('exchange_ammount');

        $ingreso_ammount = $this->ingreso_ammount ?? 0;
        $ingreso_exchange_ammount = $this->exchange_ammount ?? 0;

        $total_recursos_ammount = $ingreso_ammount + $total_abonos_ammount + $total_creditos_ammount;
        $total_recursos_exchange = $ingreso_exchange_ammount + $total_abonos_exchange_ammount + $total_creditos_exchange_ammount;

        return [
            'abonos' => [
                'ammount' => $total_abonos_ammount,
                'exchange_ammount' => $total_abonos_exchange_ammount,
            ],
            'creditos' => [
                'ammount' => $total_creditos_ammount,
                'exchange_ammount' => $total_creditos_exchange_ammount,
            ],
            'totals' => [
                'ammount' => $total_recursos_ammount,
                'exchange_ammount' => $total_recursos_exchange,
            ]
        ];
    }

    public function calculateTotals()
    {
        $totalIncome = $this->ingreso_ammount ?? 0;
        $totalExchangeIncome = $this->exchange_ammount ?? 0;

        // Sumar solo los recursos seleccionados
        foreach ($this->selectedResources as $resourceId) {
            $parts = explode('_', $resourceId);
            $type = $parts[0];
            $id = (int)$parts[1];

            if ($type === 'abono') {
                $abono = $this->availableAbonos->firstWhere('id', $id);
                if ($abono) {
                    $totalIncome += $abono->abono_ammount;
                    $totalExchangeIncome += $abono->exchange_ammount;
                }
            } elseif ($type === 'credito') {
                $credito = $this->availableCreditosAFavor->firstWhere('id', $id);
                if ($credito) {
                    $totalIncome += $credito->credito_ammount;
                    $totalExchangeIncome += $credito->exchange_ammount;
                }
            }
        }

        // Calcular total de cuotas seleccionadas
        $totalQuotas = 0;
        $totalExchangeQuotas = 0;
        foreach ($this->getSelectedQuotasDetails() as $quota) {
            $totalQuotas += $quota['amount'];
            $totalExchangeQuotas += $quota['exchange_amount'] ?? 0;
        }

        return [
            'total_income' => $totalIncome,
            'total_exchange_income' => $totalExchangeIncome,
            'total_quotas' => $totalQuotas,
            'total_income_exchange' => $totalExchangeIncome,
            'total_exchange_quotas' => $totalExchangeQuotas,
            'balance' => $totalIncome - $totalQuotas,
            'exchange_balance' => $totalExchangeIncome - $totalExchangeQuotas,
        ];
    }


    /**
     * CONFIRM PAYMENT - Refactored with Resource Pooling
     */
    public function confirmPayment()
    {

        // 1. Validar que hay montos o recursos seleccionados
        $totalIngreso = $this->ingreso_ammount ?? 0;
        $totalRecursos = $this->calculateSelectedResourcesTotal();
        $totalDisponible = $totalIngreso + $totalRecursos[0]; // Use the exchange amount from calculateSelectedResourcesTotal

        if ($totalDisponible <= 0) {
            $this->showSwal('Error', 'Debe ingresar un monto o seleccionar recursos para realizar el pago.', 'error');
            return;
        }

        // 2. Validar que hay cuotas seleccionadas
        $selectedQuotas = $this->getOrderedSelectedQuotas();
        if (empty($selectedQuotas)) { // Check if the array is empty
            $this->showSwal('Error', 'Debe seleccionar al menos una cuota para pagar.', 'error');
            return;
        }

        DB::beginTransaction();

        try {
            $representant = Representant::findOrFail($this->representant_id);
            $date_current = Carbon::now()->format('Y-m-d');

            // 3. Crear RegistroPagoCombinado
            $combinado = RegistroPagoCombinado::create([
                'representant_id' => $representant->id,
                'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: ' . $date_current,
            ]);

            // 4. Crear Ingreso Principal
            $ingreso = Ingreso::create([
                'estudiant_id' => $representant->estudiants()->first()->id, // Asignar al primer estudiante por defecto
                'representant_id' => $representant->id,
                'method_pay_id' => $this->method_pay_id,
                'banco_id' => $this->banco_id,
                'number_i_pay' => $this->number_i_pay,
                'date_transaction' => $this->date_transaction,
                'date_payment' => $this->date_payment,
                'ingreso_ammount' => $this->ingreso_ammount ?? 0,
                'exchange_rate_id' => ExchangeRate::whereDate('date', $this->date_payment)->first()->id ?? null,
                'exchange_ammount' => $this->exchange_ammount ?? 0,
                'ingreso_observations' => $this->ingreso_observations,
                'person_bill_ci' => $this->person_bill_ci,
                'person_bill_name' => $this->person_bill_name,
            ]);

            // Crear Recurso para el Ingreso
            Recurso::create([
                'registro_pago_combinado_id' => $combinado->id,
                'ingreso_id' => $ingreso->id,
                'status_ingreso' => 'true'
            ]);

            // 5. Procesar Recursos Adicionales (Abonos y Créditos)
            $usedCredits = [];
            $usedAbonos = [];
            $totalPoolExchange = $this->exchange_ammount ?? 0; // Iniciar pool con el ingreso en USD

            // Procesar Abonos
            foreach ($this->selectedResources as $resourceId) {
                if (strpos($resourceId, 'abono_') === 0) {
                    $id = substr($resourceId, 6);
                    $abono = Abono::findOrFail($id);
                    
                    Recurso::create([
                        'registro_pago_combinado_id' => $combinado->id,
                        'ingreso_id' => $abono->ingreso_id, // El abono apunta a un ingreso original
                        'status_abono' => 'true'
                    ]);

                    $totalPoolExchange += $abono->exchange_ammount;
                    $usedAbonos[] = $abono;
                    $abono->delete(); // SoftDelete
                }
            }

            // Procesar Créditos
            foreach ($this->selectedResources as $resourceId) {
                if (strpos($resourceId, 'credito_') === 0) {
                    $id = substr($resourceId, 8);
                    $credito = CreditoAFavor::findOrFail($id);

                    Recurso::create([
                        'registro_pago_combinado_id' => $combinado->id,
                        'credito_a_favor_id' => $credito->id,
                        'status_credito' => 'true'
                    ]);

                    $totalPoolExchange += $credito->exchange_ammount;
                    $usedCredits[] = $credito;
                    $credito->delete(); // SoftDelete
                }
            }

            // 6. Procesar Cuotas Secuencialmente
            $firstRegistroPago = null;

            foreach ($selectedQuotas as $quotaData) {
                if ($totalPoolExchange <= 0.009) break; // Si se acaba el dinero (con margen de error), parar

                $quota = Cuentaxpagar::findOrFail($quotaData['quota']->id); // Access the quota object
                $estudiant = Estudiant::findOrFail($quotaData['estudiant_id']); // Access the student ID
                
                // Calcular deuda pendiente en USD
                $debtExchange = $quotaData['pending_exchange_amount']; // Use the calculated pending amount
                
                // Determinar cuánto pagar para esta cuota
                $amountToPayExchange = min($debtExchange, $totalPoolExchange);
                
                if ($amountToPayExchange <= 0) continue;

                // Crear RegistroPago
                $registroPago = RegistroPago::create([
                    'estudiant_id' => $estudiant->id,
                    'representant_id' => $representant->id,
                    'registro_pago_combinado_id' => $combinado->id,
                    'cuentaxpagar_id' => $quota->id,
                    'status_unexpired' => !$quotaData['is_overdue'], // true si no está vencida
                    'user_id' => auth()->id()
                ]);

                if (!$firstRegistroPago) {
                    $firstRegistroPago = $registroPago;
                }

                // Distribuir pago en conceptos
                $this->applyFundsToQuotaConcepts($registroPago, $quota, $amountToPayExchange);

                // Crear Pago (Vinculación con Ingreso Principal)
                // Nota: El sistema legacy crea un Pago por cada RegistroPago, vinculado al Ingreso principal
                // Calculamos la proporción del pago que corresponde al Ingreso vs Recursos
                // Para simplificar y seguir el legacy, creamos el Pago con el monto total aplicado a esta cuota
                // convertido a Bs si es necesario, o simplemente registramos el monto en USD.
                // El modelo Pago tiene 'pagos_ammount' (Bs) y 'exchange_ammount' (USD).
                
                // Calcular monto en Bs aproximado para el registro (referencial)
                $rate = $this->getCurrentExchangeRate();
                $amountToPayBs = $amountToPayExchange * $rate;

                Pago::create([
                    'registro_pago_id' => $registroPago->id,
                    'ingreso_id' => $ingreso->id,
                    'pagos_ammount' => $amountToPayBs,
                    'exchange_ammount' => $amountToPayExchange
                ]);

                // Vincular Créditos y Abonos al PRIMER RegistroPago (como hace el legacy implícitamente)
                // O a todos? El legacy los borra, así que solo puede vincularlos una vez.
                // Lo haremos solo para el primer registro de pago para mantener la trazabilidad sin duplicar
                if ($registroPago->id === $firstRegistroPago->id) {
                    foreach ($usedCredits as $credito) {
                        CreditoAplicado::create([
                            'registro_pago_id' => $registroPago->id,
                            'credito_a_favor_id' => $credito->id,
                            'credito_aplicado_observations' => 'Aplicado en pago combinado ' . $combinado->id
                        ]);
                    }
                    foreach ($usedAbonos as $abono) {
                        AbonoAplicado::create([
                            'registro_pago_id' => $registroPago->id,
                            'abono_id' => $abono->id,
                            'abono_aplicado_observations' => 'Aplicado en pago combinado ' . $combinado->id
                        ]);
                    }
                }

                // Descontar del pool
                $totalPoolExchange -= $amountToPayExchange;
            }

            // 7. Generar Crédito a Favor (Remanente)
            if ($totalPoolExchange > 0.09) { // Margen mínimo para generar crédito
                $rate = $this->getCurrentExchangeRate();
                $remanenteBs = $totalPoolExchange * $rate;

                CreditoAFavor::create([
                    'representant_id' => $representant->id,
                    'estudiant_id' => $representant->estudiants()->first()->id,
                    'registro_pago_id' => $firstRegistroPago ? $firstRegistroPago->id : null, // Vincular al último pago o null
                    'ingreso_id' => $ingreso->id,
                    'credito_description' => 'CAF: Remanente Pago Combinado ' . $combinado->id,
                    'credito_observations' => 'Generado por sobrante de pago. Transacción: ' . $this->number_i_pay,
                    'credito_ammount' => $remanenteBs,
                    'exchange_ammount' => $totalPoolExchange,
                    'status_omitted' => 'false'
                ]);
            }

            DB::commit();

            // Preparar datos para el recibo
            $this->registroPagoCombinado = $combinado;
            $this->nextStep(); // Ir al paso de recibo

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error en confirmPayment: ' . $e->getMessage());
            $this->showSwal('Error', 'Ocurrió un error al procesar el pago: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Valida la integridad de la transacción
     */
    private function validateTransactionIntegrity($registroPagoCombinadoId, $ingresoId)
    {
        $ingreso = Ingreso::findOrFail($ingresoId);
        $combinado = RegistroPagoCombinado::with('recursos')->findOrFail($registroPagoCombinadoId);
        
        // Calcular total recursos disponibles (Ingreso + Abonos + Créditos)
        $totalResources = $ingreso->exchange_ammount;
        
        foreach ($combinado->recursos as $recurso) {
            if ($recurso->status_abono == 'true') {
                $abonoIngreso = Ingreso::find($recurso->ingreso_id);
                if ($abonoIngreso && $abonoIngreso->id != $ingresoId) {
                        $totalResources += $abonoIngreso->exchange_ammount;
                }
            } elseif ($recurso->status_credito == 'true') {
                $credito = CreditoAFavor::withTrashed()->find($recurso->credito_a_favor_id);
                if ($credito) {
                    $totalResources += $credito->exchange_ammount;
                }
            }
        }
        
        // Calcular total aplicado en Pagos
        $totalApplied = Pago::where('ingreso_id', $ingresoId)->sum('exchange_ammount');
        
        // Validar que lo aplicado no exceda lo disponible (con margen de error)
        if ($totalApplied > $totalResources + 0.05) {
                throw new Exception("Error de Integridad: El total aplicado ($totalApplied) excede los recursos disponibles ($totalResources)");
        }
    }

    /**
     * Calcula el total de recursos seleccionados
     */
    private function calculateSelectedResourcesTotal(): array
    {
        $totalExchange = 0;
        $totalLocal = 0;

        foreach ($this->selectedResources as $resourceId) {
            $parts = explode('_', $resourceId);
            $type = $parts[0];
            $id = (int)$parts[1];

            if ($type === 'abono') {
                $abono = $this->availableAbonos->firstWhere('id', $id);
                if ($abono) {
                    $totalExchange += (float) $abono->exchange_ammount;
                    $totalLocal += (float) $abono->abono_ammount;
                }
            } elseif ($type === 'credito') {
                $credito = $this->availableCreditosAFavor->firstWhere('id', $id);
                if ($credito) {
                    $totalExchange += (float) $credito->exchange_ammount;
                    $totalLocal += (float) $credito->credito_ammount;
                }
            }
        }

        return [$totalExchange, $totalLocal];
    }

    /**
     * Obtener cuotas seleccionadas ordenadas por prioridad
     */
    private function getOrderedSelectedQuotas()
    {
        $allSelectedQuotas = array_merge($this->selectedOverdueQuotas, $this->selectedNotDueQuotas);

        $quotasWithDetails = [];
        foreach ($allSelectedQuotas as $quotaSelection) {
            $parts = explode('_', $quotaSelection);
            $quotaId = (int)$parts[0];
            $estudiantId = (int)$parts[1];

            $quota = Cuentaxpagar::with(['estudiant', 'conceptopagos'])->find($quotaId);
            if (!$quota) {
                Log::warning('Quota not found for ID: ' . $quotaId);
                continue;
            }

            $pendingExchangeAmount = round($quota->TotalExchangeMontoCuentasXPagarAdeudado($estudiantId),2);
            // hardcode, en acuerdo con admon, se consideran montos superiores a USD 0.009

            $isOverdue = in_array($quotaSelection, $this->selectedOverdueQuotas);

            $quotasWithDetails[] = [
                'selection' => $quotaSelection,
                'quota' => $quota,
                'estudiant_id' => $estudiantId,
                'pending_exchange_amount' => $pendingExchangeAmount,
                'is_overdue' => $isOverdue,
                'priority' => $isOverdue ? 1 : 2,
            ];
        }

        // Ordenar: vencidas primero, luego por monto pendiente (ascendente)
        usort($quotasWithDetails, function ($a, $b) {
            if ($a['priority'] !== $b['priority']) {
                return $a['priority'] - $b['priority'];
            }
            return $a['pending_exchange_amount'] <=> $b['pending_exchange_amount'];
        });

        return $quotasWithDetails;
    }

    /**
     * Aplicar fondos a conceptos de una cuota
     */
    private function applyFundsToQuotaConcepts($registroPago, $quota, $availableExchange)
    {
        $totalAppliedExchange = 0;
        $totalAppliedLocal = 0;
        $conceptopagos = $quota->conceptopagos;
        $estudiant = Estudiant::findOrFail($registroPago->estudiant_id);
        $currentRate = $this->getCurrentExchangeRate();

        foreach ($conceptopagos as $conceptopago) {
            if ($availableExchange > 0) {
                $concepto_ammount = $conceptopago->concepto_ammount;
                $exchange_ammount = $conceptopago->exchange_ammount;

                // Aplicar descuento si corresponde
                if ($conceptopago->status_discount == 'true') {
                    $descuento_ammount = $estudiant->descuento_ammount($quota->id);
                    if ($descuento_ammount) {
                        $descuento_factor = 1 - ($descuento_ammount / 100);
                        $concepto_ammount *= $descuento_factor;
                        $exchange_ammount *= $descuento_factor;
                        
                        // Registrar descuento aplicado
                        $descuento = $estudiant->descuento($quota->id);
                        if ($descuento) {
                            DescuentoAplicado::create([
                                'registro_pago_id' => $registroPago->id,
                                'descuento_id' => $descuento->id,
                                'descuento_aplicado_observations' => 'Descuento aplicado automáticamente'
                            ]);
                        }
                    }
                }

                // Calcular cuánto falta por pagar de este concepto
                $count = $conceptopagos->count();
                // Nota: El legacy divide el pagado entre el count? Eso parece raro.
                // $concepto_cancelado_ammount_exchange = $quota->TotalExchangeMontoCuentasXPagarPagado($estudiant->id) / $count;
                // Vamos a usar una lógica más directa: verificar si ya existe un ConceptoCancelado para este concepto y registro?
                // No, estamos creando un nuevo pago.
                
                // Verificar pagos previos de este concepto específico sería ideal, pero el modelo es complejo.
                // Asumiremos que exchange_ammount es el total del concepto.
                // Si es un pago parcial, deberíamos saber cuánto falta.
                
                // Usaremos la lógica del legacy de "TotalExchangeMontoCuentasXPagarPagado" para estimar lo pagado.
                $totalPagadoEnQuota = $quota->TotalExchangeMontoCuentasXPagarPagado($estudiant->id);
                // Si hay 5 conceptos, y se pagaron 100, asume 20 por concepto?
                $pagadoPorConcepto = $count > 0 ? $totalPagadoEnQuota / $count : 0;
                
                $pendienteDelConcepto = max(0, $exchange_ammount - $pagadoPorConcepto);
                
                if ($pendienteDelConcepto > 0) {
                    // Cuánto podemos pagar de este concepto
                    $amountToPayForConcept = min($pendienteDelConcepto, $availableExchange);
                    
                    $status_partial = ($amountToPayForConcept < $pendienteDelConcepto) ? 'true' : 'false';
                    
                    ConceptoCancelado::create([
                        'registro_pago_id' => $registroPago->id,
                        'concepto_pago_id' => $conceptopago->id,
                        'status_partial' => $status_partial,
                        'concepto_ammount' => $amountToPayForConcept * $currentRate,
                        'exchange_ammount' => $amountToPayForConcept,
                    ]);
                    
                    $totalAppliedExchange += $amountToPayForConcept;
                    $totalAppliedLocal += ($amountToPayForConcept * $currentRate);
                    
                    $availableExchange -= $amountToPayForConcept;
                }
            }
        }

        return [
            'exchange' => $totalAppliedExchange,
            'local' => $totalAppliedLocal
        ];
    }

    /**
     * Procesar recursos originales (ABN + CAF) - soft delete y trazabilidad
     */
    private function processOriginalResources($combinedPaymentId, $abonosUsados, $creditosUsados)
    {
        // Procesar abonos
        foreach ($abonosUsados as $abono) {
            Recurso::create([
                'registro_pago_combinado_id' => $combinedPaymentId,
                'ingreso_id' => $abono->ingreso_id,
                'status_abono' => true,
            ]);
            $abono->delete();
            Log::info('Soft-deleted original ABN', ['abono_id' => $abono->id]);
        }

        // Procesar créditos
        foreach ($creditosUsados as $credito) {
            Recurso::create([
                'registro_pago_combinado_id' => $combinedPaymentId,
                'credito_a_favor_id' => $credito->id,
                'status_credito' => true,
            ]);
            $credito->delete();
            Log::info('Soft-deleted original CAF', ['credito_id' => $credito->id]);
        }
    }

    /**
     * Crear pago detallado - CORREGIDO para evitar duplicación
     */
    private function createDetailedPayment($registroPagoId, $totalPagado)
    {
        // VERIFICAR que no exista ya un registro de pago para este registro_pago_id
        $existingPago = Pago::where('registro_pago_id', $registroPagoId)->first();

        if ($existingPago) {
            Log::warning('Attempted to create duplicate Pago record', [
                'registro_pago_id' => $registroPagoId,
                'existing_pago_id' => $existingPago->id
            ]);
            return $existingPago;
        }

        // Crear NUEVO registro de pago
        return Pago::create([
            'registro_pago_id' => $registroPagoId,
            'pagos_ammount' => $totalPagado['ammount'],
            'exchange_ammount' => $totalPagado['exchange_ammount'],
        ]);
    }

    public function startNewPayment()
    {
        $this->resetAssistantProperties();
        $this->currentStep = 1;
    }

    private function resetAssistantProperties()
    {
        // Reset datos del pago
        $this->currentStep = 1;
        $this->method_pay_id = '3';
        $this->banco_id = '3';
        $this->number_i_pay = rand(100000, 10000000000000);
        $this->date_transaction = Carbon::now()->format('Y-m-d');
        $this->date_payment = Carbon::now()->format('Y-m-d');
        $this->ingreso_ammount = 0;
        $this->exchange_ammount = 0;
        $this->ingreso_observations = '';
        $this->person_bill_ci = $this->representant ? $this->representant->ci_representant : '';
        $this->person_bill_name = $this->representant ? $this->representant->name : '';

        $this->registroPagoId = null;
        $this->ingresoId = null;
        $this->registroPagoCombinadoId = null;
        $this->registroPagoCombinado = null;
        $this->firstRegistroPagoId = null;

        // Reset selecciones
        $this->selectedResources = [];
        $this->selectedOverdueQuotas = [];
        $this->selectedNotDueQuotas = [];

        // Reset datos auxiliares
        $this->paymentSummary = [];

        // Recargar datos si hay representante seleccionado
        if ($this->representant_id) {
            $this->loadAvailableResources();
            $this->loadQuotas();
        }

        $this->hideIngresoFields = ($this->banco_id == 1);
    }

    private function createCombinedPaymentRecord()
    {
        return RegistroPagoCombinado::create([
            'representant_id' => $this->representant_id,
            'description' => 'PAGO COMBINADO CORRESPONDIENTE A LA FECHA: ' . Carbon::now()->format('Y-m-d'),
        ]);
    }

    private function createMainIncome()
    {
        // Obtener tasa de cambio para la fecha de pago
        $exchangeRate = ExchangeRate::whereDate('date', $this->date_payment)->first();
        $exchangeRateId = $exchangeRate ? $exchangeRate->id : null;
        $exchangeAmount = $exchangeRate ? ($this->ingreso_ammount / $exchangeRate->ammount) : null;

        return Ingreso::create([
            'estudiant_id' => $this->representant->estudiants()->first()->id,
            'representant_id' => $this->representant_id,
            'method_pay_id' => $this->method_pay_id,
            'banco_id' => $this->banco_id,
            'number_i_pay' => $this->number_i_pay,
            'date_transaction' => $this->date_transaction,
            'date_payment' => $this->date_payment,
            'ingreso_ammount' => $this->ingreso_ammount,
            'exchange_rate_id' => $exchangeRateId,
            'exchange_ammount' => $exchangeAmount,
            'status_late_payment' => 'false',
            'exchange_ammount_late_payment' => 0,
            'ingreso_observations' => $this->ingreso_observations,
            'person_bill_ci' => $this->person_bill_ci ?: $this->representant->ci_representant,
            'person_bill_name' => $this->person_bill_name ?: $this->representant->name,
        ]);
    }

    private function createIncomeResource($combinedPaymentId, $ingresoId)
    {
        return Recurso::create([
            'registro_pago_combinado_id' => $combinedPaymentId,
            'ingreso_id' => $ingresoId,
            'status_ingreso' => true,
        ]);
    }

    private function createPaymentRecord($combinedPaymentId, $estudiantId, $quotaId, $isNotDue)
    {
        return RegistroPago::create([
            'estudiant_id' => $estudiantId,
            'representant_id' => $this->representant_id,
            'registro_pago_combinado_id' => $combinedPaymentId,
            'cuentaxpagar_id' => $quotaId,
            'status_unexpired' => $isNotDue,
            'user_id' => auth()->id(),
        ]);
    }

    private function applyDiscounts($registroPago, $estudiantId, $quotaId)
    {
        $estudiant = Estudiant::find($estudiantId);
        $descuento = $estudiant->descuento($quotaId);

        if ($descuento) {
            DescuentoAplicado::create([
                'registro_pago_id' => $registroPago->id,
                'descuento_id' => $descuento->id,
                'descuento_aplicado_observations' => 'Descuento aplicado automáticamente',
            ]);
        }
    }

    private function checkForAutomaticAdjustment()
    {
        $representant = Representant::find($this->representant_id);
        $exchangeAmountExpireBill = $representant->exchange_ammount_expire_bill;

        if ($exchangeAmountExpireBill > 0 && $exchangeAmountExpireBill < 0.1) {
            $now = Carbon::now()->format('Y-m-d');
            $exchangeRate = ExchangeRate::where('date', $now)->first();

            if ($exchangeRate) {
                session()->flash('adjustment_needed', [
                    'amount' => $exchangeAmountExpireBill,
                    'local_amount' => round($exchangeAmountExpireBill * $exchangeRate->ammount, 8),
                    'message' => 'Se requiere un ajuste automático por un monto menor.'
                ]);
            }
        }
    }

    public function debugDataStructure()
    {
        Log::info('Overdue Quotas Structure:', [
            'type' => gettype($this->overdueQuotasByStudent),
            'count' => $this->overdueQuotasByStudent->count(),
            'sample' => $this->overdueQuotasByStudent->take(1)->toArray()
        ]);

        Log::info('Not Due Quotas Structure:', [
            'type' => gettype($this->notDueQuotasByStudent),
            'count' => $this->notDueQuotasByStudent->count(),
            'sample' => $this->notDueQuotasByStudent->take(1)->toArray()
        ]);
    }

    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer' => 6000,
            'icon' => $icon,
            'toast' => false,
            'position' => 'center',
        ]);
    }

    /**
     * Calcular total de cuotas vencidas seleccionadas
     */
    public function calculateSelectedOverdueTotal()
    {
        $total = 0;
        foreach ($this->selectedOverdueQuotas as $quotaSelection) {
            $parts = explode('_', $quotaSelection);
            $quotaId = (int)$parts[0];
            $quota = $this->findQuotaInOverdueByStudent($quotaId);
            if ($quota) {
                $pendingAmount = is_object($quota) ?
                    $quota->pending_amount : ($quota['pending_amount'] ?? 0);
                $total += $pendingAmount;
            }
        }
        return $total;
    }

    /**
     * Calcular total de cuotas no vencidas seleccionadas
     */
    public function calculateSelectedNotDueTotal()
    {
        $total = 0;
        foreach ($this->selectedNotDueQuotas as $quotaSelection) {
            $parts = explode('_', $quotaSelection);
            $quotaId = (int)$parts[0];
            $quota = $this->findQuotaInNotDueByStudent($quotaId);
            if ($quota) {
                $pendingAmount = is_object($quota) ?
                    $quota->pending_amount : ($quota['pending_amount'] ?? 0);
                $total += $pendingAmount;
            }
        }
        return $total;
    }

    public function render()
    {
        return view('livewire.administracion.registro-pago.asistent-livewire', [
            'institucion' => $this->institucion,
            'registroPagoCombinado' => $this->registroPagoCombinado,
            'pescolar_name' => Session::get('pescolar_name'),
        ]);
    }

    //////////////////////////////////////////////////////////////////////
    /**
     * Método temporal para debug - verificar créditos generados
     */
    public function debugCreditGeneration()
    {
        try {
            $currentTotals = $this->calculateTotals();
            $orderedQuotas = $this->getOrderedSelectedQuotas();

            $debugInfo = [
                'total_exchange_income' => $currentTotals['total_exchange_income'],
                'total_exchange_quotas' => $currentTotals['total_exchange_quotas'],
                'exchange_balance' => $currentTotals['exchange_balance'],
                'ordered_quotas_count' => count($orderedQuotas),
                'selected_resources_count' => count($this->selectedResources),
                'should_generate_credit' => (!empty($orderedQuotas) &&
                    ($currentTotals['exchange_balance'] > 0.01 || !empty($this->selectedResources)))
            ];

            Log::debug('Credit Generation Debug', $debugInfo);

            return $debugInfo;
        } catch (Exception $e) {
            Log::error('Debug credit generation failed: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
