<?php

namespace App\Http\Livewire\Administracion\RegistroPago\Asistente;

use App\Models\app\Planpago;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\NomConceptoPago;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Estructura extends Component
{
    // Pasos del asistente
    public $currentStep = 1;
    public $totalSteps = 6;

    // Modo de operación
    public $modo = 'create'; // 'create' o 'edit'

    // Para modo edición
    public $planpago_seleccionado_id;
    public $planpagos_existentes = [];

    // Datos del Plan de Pago
    public $planpago_id;
    public $planpago_name;
    public $planpago_description;
    public $planpago_observations;
    public $status_active = 'true';
    public $enabled_for_administrative = 'true';
    public $status_cancel = 'true';
    public $status_inscription_affects = 'true';
    public $status_inscriptions = 'true';
    public $status_foreign_currency = 'false';
    public $referential_currencie_id = 1;
    public $currency_id = 1;

    // Datos de Cuentas por Pagar
    public $cuentas = [];
    public $cuenta_counter = 0;

    // Datos de Conceptos
    public $conceptos_disponibles = [];

    // Resumen
    public $resumen = [];

    // Confirmación
    public $planpago_creado_id;
    public $operacion_realizada = 'creada'; // 'creada' o 'actualizada'

    protected $rules = [
        'planpago_name' => 'required|string|max:191',
        'planpago_description' => 'required|string|max:191',
        'cuentas.*.name' => 'required|string|max:191',
        'cuentas.*.date_expiration' => 'required|date',
        'cuentas.*.conceptos.*.nom_concepto_pago_id' => 'required|integer',
        'cuentas.*.conceptos.*.concepto_ammount' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->loadConceptosDisponibles();
        $this->loadPlanpagosExistentes();
    }

    public function loadConceptosDisponibles()
    {
        $this->conceptos_disponibles = NomConceptoPago::query()
            ->orderBy('name')
            ->get();
    }

    public function loadPlanpagosExistentes()
    {
        $this->planpagos_existentes = Planpago::query()
            ->where('id', '!=', 1) // Excluir .NINGUNO.
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get();
    }

    // ============ PASO 1: Seleccionar Modo ============
    public function selectModo($modo)
    {
        $this->modo = $modo;

        if ($modo === 'create') {
            $this->resetPlanpagoData();
            $this->currentStep = 2;
        }
    }

    public function selectPlanpagoExistente()
    {
        $this->validate([
            'planpago_seleccionado_id' => 'required|exists:planpagos,id'
        ]);

        $this->cargarPlanpagoExistente($this->planpago_seleccionado_id);
        $this->currentStep = 2;
    }

    public function cargarPlanpagoExistente($planpago_id)
    {
        $planpago = Planpago::with(['cuentaxpagars.conceptopagos'])->find($planpago_id);

        if ($planpago) {
            $this->planpago_id = $planpago->id;
            $this->planpago_name = $planpago->name;
            $this->planpago_description = $planpago->description;
            $this->planpago_observations = $planpago->observations;
            $this->status_active = $planpago->status_active;
            $this->enabled_for_administrative = $planpago->enabled_for_administrative;
            $this->status_cancel = $planpago->status_cancel;
            $this->status_inscription_affects = $planpago->status_inscription_affects;
            $this->status_inscriptions = $planpago->status_inscriptions;
            $this->status_foreign_currency = $planpago->status_foreign_currency;
            $this->referential_currencie_id = $planpago->referential_currencie_id;
            $this->currency_id = $planpago->currency_id;

            // Cargar cuentas existentes
            $this->cuentas = [];
            $this->cuenta_counter = 0;

            $cuentaxpagars = $planpago->cuentaxpagars->where('type', 'GENERAL');

            foreach ($cuentaxpagars as $cuenta) {
                $this->cuentas[] = [
                    'id' => $this->cuenta_counter++,
                    'cuenta_id' => $cuenta->id, // ID existente para updates
                    'name' => $cuenta->name,
                    'date_expiration' => $cuenta->date_expiration,
                    'date_late_payment' => $cuenta->date_late_payment,
                    'date_calendar_start' => $cuenta->date_calendar_start,
                    'date_calendar_end' => $cuenta->date_calendar_end,
                    'description' => $cuenta->description,
                    'observations' => $cuenta->observations,
                    'status_inscription' => $cuenta->status_inscription,
                    'enable_late_payment' => $cuenta->enable_late_payment,
                    'conceptos' => $cuenta->conceptopagos->map(function ($concepto) {
                        return [
                            'concepto_id' => $concepto->id, // ID existente para updates
                            'nom_concepto_pago_id' => $concepto->nom_concepto_pago_id,
                            'concepto_description' => $concepto->concepto_description,
                            'concepto_ammount' => $concepto->concepto_ammount,
                            'exchange_ammount' => $concepto->exchange_ammount,
                            'status_discount' => $concepto->status_discount,
                            'status_annuity' => $concepto->status_annuity
                        ];
                    })->toArray()
                ];
            }
        }
    }

    public function resetPlanpagoData()
    {
        $this->reset([
            'planpago_id',
            'planpago_name',
            'planpago_description',
            'planpago_observations',
            'status_active',
            'enabled_for_administrative',
            'status_cancel',
            'status_inscription_affects',
            'status_inscriptions',
            'status_foreign_currency',
            'cuentas',
            'cuenta_counter'
        ]);

        $this->status_active = 'true';
        $this->enabled_for_administrative = 'true';
        $this->status_cancel = 'true';
        $this->status_inscription_affects = 'true';
        $this->status_inscriptions = 'true';
        $this->status_foreign_currency = 'false';
    }

    // ============ PASO 2: Plan de Pago ============
    public function nextStepTwo()
    {
        $this->validate([
            'planpago_name' => 'required|string|max:191',
            'planpago_description' => 'required|string|max:191',
        ]);

        $this->currentStep = 3;

        // En modo CREATE, generar cuentas automáticamente
        if ($this->modo === 'create' && empty($this->cuentas)) {
            $this->generarCuentasAutomaticas();
        }
    }

    public function generarCuentasAutomaticas()
    {
        $meses = [
            'SEPTIEMBRE' => 9,
            'OCTUBRE' => 10,
            'NOVIEMBRE' => 11,
            'DICIEMBRE' => 12,
            'ENERO' => 1,
            'FEBRERO' => 2,
            'MARZO' => 3,
            'ABRIL' => 4,
            'MAYO' => 5,
            'JUNIO' => 6,
            'JULIO' => 7,
            'AGOSTO' => 8
        ];

        $añoActual = now()->year;
        $añoSiguiente = $añoActual + 1;

        // Cuenta de INSCRIPCIÓN (Septiembre)
        $this->addCuentaAutomatica('INSCRIPCIÓN', $añoActual, 9, true);

        // 12 cuentas mensuales
        foreach ($meses as $mes => $mesNumero) {
            $año = $mesNumero >= 9 ? $añoActual : $añoSiguiente;
            $this->addCuentaAutomatica($mes, $año, $mesNumero, false);
        }
    }

    public function addCuentaAutomatica($nombre, $año, $mes, $esInscripcion)
    {
        // Primer día del mes
        $fechaInicioMes = Carbon::create($año, $mes, 1);

        // Último día del mes
        $fechaFinMes = $fechaInicioMes->copy()->endOfMonth();

        $this->cuentas[] = [
            'id' => $this->cuenta_counter++,
            'name' => $nombre,
            'date_expiration' => $fechaInicioMes->format('Y-m-d'),  // Primer día del mes
            'date_late_payment' => $fechaInicioMes->format('Y-m-d'), // Igual que date_expiration
            'date_calendar_start' => $fechaInicioMes->format('Y-m-d'), // Igual que date_expiration
            'date_calendar_end' => $fechaFinMes->format('Y-m-d'),    // Último día del mes
            'description' => $nombre,
            'observations' => null,
            'status_inscription' => $esInscripcion ? 'true' : 'false',
            'enable_late_payment' => !$esInscripcion,
            'conceptos' => [
                [
                    'nom_concepto_pago_id' => '', // Se seleccionará manualmente
                    'concepto_description' => $nombre, // Mismo nombre que la cuenta
                    'concepto_ammount' => 0, // Monto en 0
                    'exchange_ammount' => 120.00, // Monto cambiario 120.00
                    'status_discount' => 'true', // Permite descuento
                    'status_annuity' => 'true' // Es anualidad
                ]
            ]
        ];
    }

    // ============ PASO 3: Cuentas por Pagar ============
    public function addCuenta()
    {
        $this->cuentas[] = [
            'id' => $this->cuenta_counter++,
            'name' => '',
            'date_expiration' => '',
            'date_late_payment' => '',
            'date_calendar_start' => '',
            'date_calendar_end' => '',
            'description' => '',
            'observations' => '',
            'status_inscription' => 'false',
            'enable_late_payment' => false,
            'conceptos' => [
                [
                    'nom_concepto_pago_id' => '',
                    'concepto_description' => '', // Se llenará con el nombre de la cuenta
                    'concepto_ammount' => 0,
                    'exchange_ammount' => 120.00,
                    'status_discount' => 'true',
                    'status_annuity' => 'true'
                ]
            ]
        ];
    }

    public function removeCuenta($index)
    {
        if (count($this->cuentas) > 1) {
            unset($this->cuentas[$index]);
            $this->cuentas = array_values($this->cuentas);
        } else {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Debe mantener al menos una cuenta por pagar',
                'icon' => 'error'
            ]);
        }
    }

    public function nextStepThree()
    {
        $this->validate([
            'cuentas' => 'required|array|min:1',
            'cuentas.*.name' => 'required|string|max:191',
            'cuentas.*.date_expiration' => 'required|date',
        ]);

        $this->currentStep = 4;
    }

    // ============ PASO 4: Gestión de Conceptos ============
    public function addConcepto($cuentaIndex)
    {
        if (!isset($this->cuentas[$cuentaIndex]['conceptos'])) {
            $this->cuentas[$cuentaIndex]['conceptos'] = [];
        }

        $this->cuentas[$cuentaIndex]['conceptos'][] = [
            'nom_concepto_pago_id' => '',
            'concepto_description' => $this->cuentas[$cuentaIndex]['name'], // Mismo nombre que la cuenta
            'concepto_ammount' => 0,
            'exchange_ammount' => 120.00,
            'status_discount' => 'true',
            'status_annuity' => 'true'
        ];
    }

    public function removeConcepto($cuentaIndex, $conceptoIndex)
    {
        if (isset($this->cuentas[$cuentaIndex]['conceptos']) && count($this->cuentas[$cuentaIndex]['conceptos']) > 1) {
            unset($this->cuentas[$cuentaIndex]['conceptos'][$conceptoIndex]);
            $this->cuentas[$cuentaIndex]['conceptos'] = array_values($this->cuentas[$cuentaIndex]['conceptos']);
        } else {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Cada cuenta debe tener al menos un concepto de pago',
                'icon' => 'error'
            ]);
        }
    }

    public function updateConceptoAmmount($cuentaIndex, $conceptoIndex)
    {
        $ammount = $this->cuentas[$cuentaIndex]['conceptos'][$conceptoIndex]['exchange_ammount'];
        $this->cuentas[$cuentaIndex]['conceptos'][$conceptoIndex]['exchange_ammount'] = $ammount;
    }

    public function nextStepFour()
    {
        // Validar que cada cuenta tenga al menos un concepto
        foreach ($this->cuentas as $index => $cuenta) {
            if (empty($cuenta['conceptos'])) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Error',
                    'text' => "La cuenta '{$cuenta['name']}' debe tener al menos un concepto de pago",
                    'icon' => 'error'
                ]);
                return;
            }
        }

        $this->validate([
            'cuentas.*.conceptos.*.nom_concepto_pago_id' => 'required|integer',
            'cuentas.*.conceptos.*.exchange_ammount' => 'required|numeric|min:0',
        ]);

        $this->prepareResumen();
        $this->currentStep = 5;
    }

    // ============ PASO 5: Vista Previa ============
    public function prepareResumen()
    {
        $totalGeneral = 0;
        $this->resumen = [
            'planpago' => [
                'name' => $this->planpago_name,
                'description' => $this->planpago_description,
                'observations' => $this->planpago_observations,
                'configuraciones' => [
                    'status_active' => $this->status_active,
                    'enabled_for_administrative' => $this->enabled_for_administrative,
                    'status_cancel' => $this->status_cancel,
                    'status_inscription_affects' => $this->status_inscription_affects,
                    'status_inscriptions' => $this->status_inscriptions,
                    'status_foreign_currency' => $this->status_foreign_currency,
                ]
            ],
            'cuentas' => [],
            'total_cuentas' => count($this->cuentas),
            'total_conceptos' => 0,
            'total_general' => 0
        ];

        foreach ($this->cuentas as $cuenta) {
            $totalCuenta = 0;
            $conceptosDetalle = [];

            foreach ($cuenta['conceptos'] as $concepto) {
                $nombreConcepto = NomConceptoPago::find($concepto['nom_concepto_pago_id']);
                $conceptosDetalle[] = [
                    'nombre' => $nombreConcepto->name ?? 'N/A',
                    'monto' => $concepto['exchange_ammount'],
                    'permite_descuento' => $concepto['status_discount'] === 'true',
                    'anualidad' => $concepto['status_annuity'] === 'true'
                ];
                $totalCuenta += $concepto['exchange_ammount'];
                $this->resumen['total_conceptos']++;
            }

            $this->resumen['cuentas'][] = [
                'name' => $cuenta['name'],
                'date_expiration' => $cuenta['date_expiration'],
                'status_inscription' => $cuenta['status_inscription'],
                'enable_late_payment' => $cuenta['enable_late_payment'],
                'conceptos' => $conceptosDetalle,
                'total' => $totalCuenta
            ];

            $totalGeneral += $totalCuenta;
        }

        $this->resumen['total_general'] = $totalGeneral;
    }

    public function nextStepFive()
    {
        $this->currentStep = 6;
        $this->saveEstructura();
    }

    // ============ PASO 6: Guardar Estructura ============
    public function saveEstructura()
    {
        DB::beginTransaction();

        try {
            if ($this->modo === 'create') {
                $this->crearNuevaEstructura();
                $this->operacion_realizada = 'creada';
            } else {
                $this->actualizarEstructuraExistente();
                $this->operacion_realizada = 'actualizada';
            }

            DB::commit();

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Éxito!',
                'text' => "La estructura de cobranza ha sido {$this->operacion_realizada} correctamente",
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Hubo un error al procesar la estructura: ' . $e->getMessage(),
                'icon' => 'error'
            ]);

            $this->currentStep = 5;
        }
    }

    public function crearNuevaEstructura()
    {
        // Crear el Plan de Pago
        $planpago = Planpago::create([
            'name' => $this->planpago_name,
            'description' => $this->planpago_description,
            'observations' => $this->planpago_observations ?? $this->planpago_description,
            'status_active' => $this->status_active,
            'enabled_for_administrative' => $this->enabled_for_administrative,
            'status_cancel' => $this->status_cancel,
            'status_inscription_affects' => $this->status_inscription_affects,
            'status_inscriptions' => $this->status_inscriptions,
            'referential_currencie_id' => $this->referential_currencie_id,
            'currency_id' => $this->currency_id,
            'status_foreign_currency' => $this->status_foreign_currency,
        ]);

        $this->planpago_creado_id = $planpago->id;

        // Crear las Cuentas por Pagar
        foreach ($this->cuentas as $cuentaData) {
            $cuenta = Cuentaxpagar::create([
                'planpago_id' => $planpago->id,
                'name' => $cuentaData['name'],
                'type' => 'GENERAL',
                'date_expiration' => $cuentaData['date_expiration'],
                'date_late_payment' => $cuentaData['date_late_payment'] ?? null,
                'date_calendar_start' => $cuentaData['date_calendar_start'] ?? null,
                'date_calendar_end' => $cuentaData['date_calendar_end'] ?? null,
                'description' => $cuentaData['description'] ?? $cuentaData['name'],
                'observations' => $cuentaData['observations'] ?? null,
                'status_active' => 'true',
                'status_inscription' => $cuentaData['status_inscription'] ?? 'false',
                'status_exchange' => 1,
                'status_bad' => 'false',
                'enable_late_payment' => $cuentaData['enable_late_payment'] ?? 0,
                'status_late_payment' => 0,
            ]);

            // Crear los Conceptos de Pago
            foreach ($cuentaData['conceptos'] as $conceptoData) {
                ConceptoPago::create([
                    'cuentaxpagar_id' => $cuenta->id,
                    'nom_concepto_pago_id' => $conceptoData['nom_concepto_pago_id'],
                    'concepto_description' => $conceptoData['concepto_description'] ?? null,
                    'concepto_observations' => null,
                    'concepto_ammount' => $conceptoData['concepto_ammount'],
                    'exchange_ammount' => $conceptoData['exchange_ammount'] ?? $conceptoData['concepto_ammount'],
                    'status_modifiable' => 'true',
                    'status_discount' => $conceptoData['status_discount'] ?? 'true',
                    'status_active' => 'true',
                    'status_annuity' => $conceptoData['status_annuity'] ?? 'false',
                ]);
            }
        }
    }

    public function actualizarEstructuraExistente()
    {
        // Actualizar el Plan de Pago
        $planpago = Planpago::find($this->planpago_id);
        $planpago->update([
            'name' => $this->planpago_name,
            'description' => $this->planpago_description,
            'observations' => $this->planpago_observations ?? $this->planpago_description,
            'status_active' => $this->status_active,
            'enabled_for_administrative' => $this->enabled_for_administrative,
            'status_cancel' => $this->status_cancel,
            'status_inscription_affects' => $this->status_inscription_affects,
            'status_inscriptions' => $this->status_inscriptions,
            'status_foreign_currency' => $this->status_foreign_currency,
        ]);

        $this->planpago_creado_id = $planpago->id;

        // Actualizar/Eliminar/Crear cuentas
        $cuentasExistentesIds = [];

        foreach ($this->cuentas as $cuentaData) {
            if (isset($cuentaData['cuenta_id'])) {
                // Actualizar cuenta existente
                $cuenta = Cuentaxpagar::find($cuentaData['cuenta_id']);
                $cuenta->update([
                    'name' => $cuentaData['name'],
                    'date_expiration' => $cuentaData['date_expiration'],
                    'date_late_payment' => $cuentaData['date_late_payment'] ?? null,
                    'date_calendar_start' => $cuentaData['date_calendar_start'] ?? null,
                    'date_calendar_end' => $cuentaData['date_calendar_end'] ?? null,
                    'description' => $cuentaData['description'] ?? $cuentaData['name'],
                    'observations' => $cuentaData['observations'] ?? null,
                    'status_inscription' => $cuentaData['status_inscription'] ?? 'false',
                    'enable_late_payment' => $cuentaData['enable_late_payment'] ?? 0,
                ]);

                $cuentasExistentesIds[] = $cuentaData['cuenta_id'];

                // Actualizar conceptos de esta cuenta
                $this->actualizarConceptosCuenta($cuenta, $cuentaData['conceptos']);
            } else {
                // Crear nueva cuenta
                $cuenta = Cuentaxpagar::create([
                    'planpago_id' => $planpago->id,
                    'name' => $cuentaData['name'],
                    'type' => 'GENERAL',
                    'date_expiration' => $cuentaData['date_expiration'],
                    'date_late_payment' => $cuentaData['date_late_payment'] ?? null,
                    'date_calendar_start' => $cuentaData['date_calendar_start'] ?? null,
                    'date_calendar_end' => $cuentaData['date_calendar_end'] ?? null,
                    'description' => $cuentaData['description'] ?? $cuentaData['name'],
                    'observations' => $cuentaData['observations'] ?? null,
                    'status_active' => 'true',
                    'status_inscription' => $cuentaData['status_inscription'] ?? 'false',
                    'status_exchange' => 1,
                    'status_bad' => 'false',
                    'enable_late_payment' => $cuentaData['enable_late_payment'] ?? 0,
                    'status_late_payment' => 0,
                ]);

                // Crear conceptos para la nueva cuenta
                foreach ($cuentaData['conceptos'] as $conceptoData) {
                    ConceptoPago::create([
                        'cuentaxpagar_id' => $cuenta->id,
                        'nom_concepto_pago_id' => $conceptoData['nom_concepto_pago_id'],
                        'concepto_description' => $conceptoData['concepto_description'] ?? null,
                        'concepto_observations' => null,
                        'concepto_ammount' => $conceptoData['concepto_ammount'],
                        'exchange_ammount' => $conceptoData['exchange_ammount'] ?? $conceptoData['concepto_ammount'],
                        'status_modifiable' => 'true',
                        'status_discount' => $conceptoData['status_discount'] ?? 'true',
                        'status_active' => 'true',
                        'status_annuity' => $conceptoData['status_annuity'] ?? 'false',
                    ]);
                }
            }
        }

        // Eliminar cuentas que ya no existen
        Cuentaxpagar::where('planpago_id', $planpago->id)
            ->whereNotIn('id', $cuentasExistentesIds)
            ->delete();
    }

    public function actualizarConceptosCuenta($cuenta, $conceptosData)
    {
        $conceptosExistentesIds = [];

        foreach ($conceptosData as $conceptoData) {
            if (isset($conceptoData['concepto_id'])) {
                // Actualizar concepto existente
                $concepto = ConceptoPago::find($conceptoData['concepto_id']);
                $concepto->update([
                    'nom_concepto_pago_id' => $conceptoData['nom_concepto_pago_id'],
                    'concepto_description' => $conceptoData['concepto_description'] ?? null,
                    'concepto_ammount' => $conceptoData['concepto_ammount'],
                    'exchange_ammount' => $conceptoData['exchange_ammount'] ?? $conceptoData['concepto_ammount'],
                    'status_discount' => $conceptoData['status_discount'] ?? 'true',
                    'status_annuity' => $conceptoData['status_annuity'] ?? 'false',
                ]);

                $conceptosExistentesIds[] = $conceptoData['concepto_id'];
            } else {
                // Crear nuevo concepto
                ConceptoPago::create([
                    'cuentaxpagar_id' => $cuenta->id,
                    'nom_concepto_pago_id' => $conceptoData['nom_concepto_pago_id'],
                    'concepto_description' => $conceptoData['concepto_description'] ?? null,
                    'concepto_observations' => null,
                    'concepto_ammount' => $conceptoData['concepto_ammount'],
                    'exchange_ammount' => $conceptoData['exchange_ammount'] ?? $conceptoData['concepto_ammount'],
                    'status_modifiable' => 'true',
                    'status_discount' => $conceptoData['status_discount'] ?? 'true',
                    'status_active' => 'true',
                    'status_annuity' => $conceptoData['status_annuity'] ?? 'false',
                ]);
            }
        }

        // Eliminar conceptos que ya no existen
        ConceptoPago::where('cuentaxpagar_id', $cuenta->id)
            ->whereNotIn('id', $conceptosExistentesIds)
            ->delete();
    }

    // ============ NAVEGACIÓN ============
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function resetWizard()
    {
        $this->reset();
        $this->currentStep = 1;
        $this->modo = 'create';
        $this->loadConceptosDisponibles();
        $this->loadPlanpagosExistentes();

        // Resetear valores por defecto
        $this->status_active = 'true';
        $this->enabled_for_administrative = 'true';
        $this->status_cancel = 'true';
        $this->status_inscription_affects = 'true';
        $this->status_inscriptions = 'true';
        $this->status_foreign_currency = 'false';
    }

    public function render()
    {
        return view('livewire.administracion.registro-pago.asistente.estructura');
    }
}
