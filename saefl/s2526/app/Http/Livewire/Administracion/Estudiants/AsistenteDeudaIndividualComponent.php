<?php

namespace App\Http\Livewire\Administracion\Estudiants;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// Models
use App\Models\app\Estudiant;
use App\Models\app\Planpago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\NomConceptoPago;
use App\Models\app\Estudiante\Administrativa;

class AsistenteDeudaIndividualComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';

    // Pasos del asistente
    public $currentStep = 1;
    public $totalSteps = 8;

    // Paso 1: Selección de Estudiante
    public $estudianteSearch = '';
    public $searchEstudiantes = [];
    public $estudianteSeleccionado = null;
    public $estudianteId = '';

    // Paso 2: Selección de Plan de Pago
    public $planpagoId = '';
    public $planpagosDisponibles = [];

    // Paso 3: Información del Estudiante (solo lectura)
    public $estudianteInfo = [];
    public $deudasExistentes = [];
    public $conceptosPendientes = [];

    // Paso 4: Creación de Conceptos
    public $cuentasIndividuales = [];
    public $conceptosDisponibles = [];
    public $nuevaCuenta = [
        'name' => '',
        'date_expiration' => '',
        'date_calendar_start' => '',
        'date_calendar_end' => '',
        'description' => '',
        'observations' => '',
        'status_bad' => 'false'
    ];
    public $nuevosConceptos = [];

    // Paso 5: Vista Previa
    public $resumen = [];
    public $deudaActualTotal = 0;
    public $nuevaDeudaTotal = 0;

    // Paso 6: Confirmación
    public $confirmacionData = [];

    // Paso 7: Éxito
    public $operacionExitosa = false;
    public $cuentaCreadaId = null;

    protected $queryString = ['currentStep'];

    protected $listeners = [
        'estudianteSeleccionado' => 'seleccionarEstudiante',
        'resetWizard' => 'resetearAsistente',
        'confirmarCambioPlanPago' => 'confirmarCambioPlanPago' // ← NUEVO LISTENER
    ];

    public function mount()
    {
        $this->cargarDatosIniciales();
        
        // Inicializar arrays vacíos con estructura correcta
        $this->estudianteSeleccionado = [
            'id' => null,
            'ci_estudiant' => '',
            'name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'representante' => null
        ];
        
        $this->estudianteInfo = [];
        $this->deudasExistentes = [];
        $this->conceptosPendientes = [];
        
        // Inicializar datos para el paso 4
        $this->nuevaCuenta = [
            'name' => '',
            'date_expiration' => '',
            'date_calendar_start' => '',
            'date_calendar_end' => '',
            'description' => '',
            'observations' => '',
            'status_bad' => 'false'
        ];
        
        $this->nuevosConceptos = [];
        $this->agregarConceptoVacio();
    }

    public function cargarDatosIniciales()
    {
        $this->planpagosDisponibles = Planpago::where('status_active', 'true')
            ->orderBy('name', 'asc')
            ->get()
            ->mapWithKeys(function ($plan) {
                return [$plan->id => $plan->name];
            })
            ->toArray();

        $this->conceptosDisponibles = NomConceptoPago::orderBy('name', 'asc')
            ->get()
            ->mapWithKeys(function ($concepto) {
                return [$concepto->id => $concepto->name];
            })
            ->toArray();

        // Inicializar array de nuevos conceptos
        $this->agregarConceptoVacio();
    }

    // ============ PASO 1: Selección de Estudiante ============
    public function updatedEstudianteSearch($value)
    {
        if (strlen($value) < 2) {
            $this->searchEstudiantes = [];
            return;
        }

        try {
            $this->searchEstudiantes = Estudiant::with(['representant'])
                ->where('status_active', 'true')
                ->where(function($query) use ($value) {
                    $query->where('ci_estudiant', 'like', '%' . $value . '%')
                          ->orWhere('name', 'like', '%' . $value . '%');
                })
                ->orderBy('ci_estudiant', 'asc')
                ->limit(10)
                ->get()
                ->map(function($estudiante) {
                    return [
                        'id' => $estudiante->id,
                        'ci_estudiant' => $estudiante->ci_estudiant,
                        'name' => $estudiante->name,
                        'representante' => $estudiante->representant ? 
                            $estudiante->representant->name . ' (' . $estudiante->representant->ci_representant . ')' : 
                            'Sin representante',
                        'display' => $estudiante->ci_estudiant . ' - ' . $estudiante->name
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            $this->searchEstudiantes = [];
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Error al buscar estudiantes: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // En el método seleccionarEstudiante - CORREGIR Y MEJORAR
    public function seleccionarEstudiante($id)
    {
        try {
            $estudiante = Estudiant::with(['representant'])->find($id);
            
            if (!$estudiante) {
                throw new \Exception('Estudiante no encontrado en la base de datos.');
            }

            // Inicializar el array correctamente con todas las claves necesarias
            $this->estudianteSeleccionado = [
                'id' => $estudiante->id,
                'ci_estudiant' => $estudiante->ci_estudiant ?? 'No registrado',
                'name' => $estudiante->name ?? 'No registrado',
                'email' => $estudiante->email ?? 'No registrado',
                'phone' => $estudiante->phone ?? 'No registrado',
                'address' => $estudiante->address ?? 'No registrado',
                'representante' => $estudiante->representant ? [
                    'name' => $estudiante->representant->name ?? 'No registrado',
                    'ci_representant' => $estudiante->representant->ci_representant ?? 'No registrado',
                    'email' => $estudiante->representant->email ?? 'No registrado',
                    'phone' => $estudiante->representant->phone ?? 'No registrado'
                ] : null
            ];
            
            $this->estudianteId = $estudiante->id;
            $this->estudianteSearch = '';
            $this->searchEstudiantes = [];
            
            // Cargar información para el paso 3
            $this->cargarInformacionEstudiante();
            
            $this->siguientePaso();
            
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo seleccionar el estudiante: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
            
            \Log::error('Error al seleccionar estudiante: ' . $e->getMessage(), [
                'estudiante_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // Agregar método para verificar si hay estudiante seleccionado
    public function getHasEstudianteSeleccionadoProperty()
    {
        return !empty($this->estudianteSeleccionado) && 
               isset($this->estudianteSeleccionado['ci_estudiant']) && 
               isset($this->estudianteSeleccionado['name']);
    }

    public function limpiarBusquedaEstudiante()
    {
        $this->reset(['estudianteSearch', 'searchEstudiantes']);
    }

    public function removerEstudianteSeleccionado()
    {
        $this->reset(['estudianteSeleccionado', 'estudianteId', 'estudianteInfo', 'deudasExistentes', 'conceptosPendientes']);
    }

    // ============ PASO 2: Selección de Plan de Pago ============
    public function seleccionarPlanPago($planpagoId)
    {
        $this->planpagoId = $planpagoId;
        $this->siguientePaso();
    }



    // ============ PASO 3: Información del Estudiante ============
    public function cargarInformacionEstudiante()
    {
        // Validar que tenemos un estudiante seleccionado
        if (!$this->estudianteId || empty($this->estudianteSeleccionado)) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No hay estudiante seleccionado para cargar información.',
                'icon' => 'error'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Cargar información completa del estudiante con relaciones
            $estudiante = Estudiant::with([
                'representant',
                'cuentaxpagars' => function($query) {
                    $query->where('type', 'INDIVIDUAL')
                          ->where('status_active', 'true')
                          ->with(['planpago', 'conceptopagos.nomconceptopago']);
                }
            ])->find($this->estudianteId);

            if (!$estudiante) {
                throw new \Exception('No se encontró el estudiante en la base de datos.');
            }

            // ============ INFORMACIÓN GENERAL DEL ESTUDIANTE ============
            $this->estudianteInfo = [
                'datos_generales' => [
                    'id' => $estudiante->id,
                    'ci_estudiant' => $estudiante->ci_estudiant ?? 'No registrado',
                    'name' => $estudiante->name ?? 'No registrado',
                    'email' => $estudiante->email ?? 'No registrado',
                    'phone' => $estudiante->phone ?? 'No registrado',
                    'address' => $estudiante->address ?? 'No registrado',
                    'birthdate' => $estudiante->birthdate ? 
                        \Carbon\Carbon::parse($estudiante->birthdate)->format('d/m/Y') : 'No registrado',
                    'gender' => $this->getGeneroTexto($estudiante->gender),
                    'status_active' => $estudiante->status_active == 'true' ? 'Activo' : 'Inactivo',
                    'created_at' => $estudiante->created_at ? 
                        \Carbon\Carbon::parse($estudiante->created_at)->format('d/m/Y H:i') : 'N/A',
                    'updated_at' => $estudiante->updated_at ? 
                        \Carbon\Carbon::parse($estudiante->updated_at)->format('d/m/Y H:i') : 'N/A'
                ],
                'representante' => $estudiante->representant ? [
                    'id' => $estudiante->representant->id,
                    'name' => $estudiante->representant->name ?? 'No registrado',
                    'ci_representant' => $estudiante->representant->ci_representant ?? 'No registrado',
                    'email' => $estudiante->representant->email ?? 'No registrado',
                    'phone' => $estudiante->representant->phone ?? 'No registrado',
                    'address' => $estudiante->representant->address ?? 'No registrado',
                    'relationship' => $estudiante->representant->relationship ?? 'No especificado',
                    'occupation' => $estudiante->representant->occupation ?? 'No especificado'
                ] : null
            ];

            // ============ DEUDAS EXISTENTES ============
            $this->deudasExistentes = [];
            $deudaActualTotal = 0;
            $totalConceptos = 0;
            $cuentasVencidas = 0;
            $montoVencido = 0;
            $montoPorVencer = 0;

            if ($estudiante->cuentaxpagars) {
                foreach ($estudiante->cuentaxpagars as $cuenta) {
                    // Calcular información de la cuenta
                    $fechaVencimiento = \Carbon\Carbon::parse($cuenta->date_expiration);
                    $estaVencida = now()->gt($fechaVencimiento);
                    $diasRestantes = now()->diffInDays($fechaVencimiento, false);
                    
                    // Procesar conceptos de la cuenta
                    $conceptosDetalle = [];
                    $montoCuenta = 0;

                    if ($cuenta->conceptopagos) {
                        foreach ($cuenta->conceptopagos as $concepto) {
                            $montoConcepto = $concepto->exchange_ammount ?? 0;
                            $conceptosDetalle[] = [
                                'id' => $concepto->id,
                                'nombre' => $concepto->nomconceptopago->name ?? 'Concepto sin nombre',
                                'monto' => $montoConcepto,
                                'descripcion' => $concepto->concepto_description ?? '',
                                'permite_descuento' => $concepto->status_discount == 'true',
                                'anualidad' => $concepto->status_annuity == 'true',
                                'status_active' => $concepto->status_active == 'true'
                            ];
                            $montoCuenta += $montoConcepto;
                            $totalConceptos++;
                        }
                    }

                    $deudaActualTotal += $montoCuenta;

                    if ($estaVencida) {
                        $cuentasVencidas++;
                        $montoVencido += $montoCuenta;
                    } else {
                        $montoPorVencer += $montoCuenta;
                    }

                    $this->deudasExistentes[] = [
                        'id' => $cuenta->id,
                        'name' => $cuenta->name ?? 'Cuenta sin nombre',
                        'planpago' => $cuenta->planpago->name ?? 'Plan no asignado',
                        'planpago_id' => $cuenta->planpago_id,
                        'date_expiration' => $fechaVencimiento->format('d/m/Y'),
                        'date_expiration_raw' => $cuenta->date_expiration,
                        'dias_restantes' => $diasRestantes,
                        'total_conceptos' => count($conceptosDetalle),
                        'monto_total' => $montoCuenta,
                        'conceptos' => $conceptosDetalle,
                        'status_bad' => $cuenta->status_bad,
                        'vencida' => $estaVencida,
                        'description' => $cuenta->description ?? '',
                        'observations' => $cuenta->observations ?? ''
                    ];
                }
            }

            // Ordenar deudas por fecha de vencimiento
            usort($this->deudasExistentes, function($a, $b) {
                return strtotime($a['date_expiration_raw']) - strtotime($b['date_expiration_raw']);
            });

            // ============ ESTADÍSTICAS Y RESUMEN ============
            $this->conceptosPendientes = [
                'total_cuentas' => count($this->deudasExistentes),
                'total_conceptos' => $totalConceptos,
                'cuentas_vencidas' => $cuentasVencidas,
                'cuentas_por_vencer' => count($this->deudasExistentes) - $cuentasVencidas,
                'monto_vencido' => $montoVencido,
                'monto_por_vencer' => $montoPorVencer,
                'cuenta_mas_antigua' => !empty($this->deudasExistentes) ? 
                    $this->deudasExistentes[0]['date_expiration'] : 'N/A',
                'cuenta_mas_reciente' => !empty($this->deudasExistentes) ? 
                    end($this->deudasExistentes)['date_expiration'] : 'N/A'
            ];

            $this->deudaActualTotal = $deudaActualTotal;

            // ============ ACTUALIZAR INFORMACIÓN EN ESTUDIANTE SELECCIONADO ============
            $this->estudianteSeleccionado['deuda_total'] = $deudaActualTotal;
            $this->estudianteSeleccionado['total_cuentas'] = count($this->deudasExistentes);

            DB::commit();

            // Log para debugging
            \Log::info("Información del estudiante cargada exitosamente", [
                'estudiante_id' => $this->estudianteId,
                'total_deudas' => count($this->deudasExistentes),
                'deuda_total' => $deudaActualTotal
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error("Error al cargar información del estudiante: " . $e->getMessage(), [
                'estudiante_id' => $this->estudianteId,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error al Cargar Información',
                'text' => 'No se pudo cargar la información del estudiante: ' . $e->getMessage(),
                'icon' => 'error'
            ]);

            // Inicializar arrays vacíos para evitar errores en la vista
            $this->estudianteInfo = [];
            $this->deudasExistentes = [];
            $this->conceptosPendientes = [
                'total_cuentas' => 0,
                'total_conceptos' => 0,
                'cuentas_vencidas' => 0,
                'cuentas_por_vencer' => 0,
                'monto_vencido' => 0,
                'monto_por_vencer' => 0
            ];
            $this->deudaActualTotal = 0;
        }
    }

    // ============ MÉTODO AUXILIAR PARA GÉNERO ============
    private function getGeneroTexto($gender)
    {
        $generos = [
            'M' => 'Masculino',
            'F' => 'Femenino',
            'male' => 'Masculino',
            'female' => 'Femenino',
            null => 'No especificado'
        ];

        return $generos[$gender] ?? 'No especificado';
    }

    // ============ MÉTODO PARA FORMATO DE MONEDA ============
    private function formatoMoneda($monto)
    {
        return number_format($monto, 2, ',', '.');
    }


    // ============ PASO 5: Vista Previa ============
    public function prepararVistaPrevia()
    {
        // Validar datos de la nueva cuenta
        $validacionCuenta = Validator::make($this->nuevaCuenta, [
            'name' => 'required|string|max:191',
            //'date_expiration' => 'required|date|after_or_equal:today',
            'date_expiration' => 'required|date',
        ], [
            'name.required' => 'El nombre de la cuenta es obligatorio',
            'date_expiration.required' => 'La fecha de vencimiento es obligatoria',
            'date_expiration.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a hoy'
        ]);

        if ($validacionCuenta->fails()) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error en Información de Cuenta',
                'text' => $validacionCuenta->errors()->first(),
                'icon' => 'error'
            ]);
            return;
        }

        // Validar conceptos
        $conceptosValidos = true;
        $conceptosConMonto = 0;
        $erroresConceptos = [];

        foreach ($this->nuevosConceptos as $index => $concepto) {
            $numeroConcepto = $index + 1;

            // Validar tipo de concepto
            if (empty($concepto['nom_concepto_pago_id'])) {
                $erroresConceptos[] = "El concepto #{$numeroConcepto} no tiene tipo seleccionado.";
                $conceptosValidos = false;
                continue;
            }

            // Validar monto
            $monto = floatval($concepto['exchange_ammount'] ?? 0);
            if ($monto <= 0) {
                $erroresConceptos[] = "El concepto #{$numeroConcepto} debe tener un monto mayor a 0.";
                $conceptosValidos = false;
                continue;
            }

            if ($monto > 0) {
                $conceptosConMonto++;
            }
        }

        if (!$conceptosValidos) {
            $mensajeError = implode('<br>', $erroresConceptos);
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error en Conceptos',
                'html' => $mensajeError,
                'icon' => 'error'
            ]);
            return;
        }

        if ($conceptosConMonto === 0) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error en Conceptos',
                'text' => 'Debe ingresar al menos un concepto con monto mayor a 0.',
                'icon' => 'error'
            ]);
            return;
        }

        // Preparar datos para vista previa
        $this->prepareResumen();
        $this->currentStep = 5;

        // Log para debugging
        \Log::info('Vista previa preparada exitosamente', [
            'estudiante_id' => $this->estudianteId,
            'total_conceptos' => count($this->nuevosConceptos),
            'nueva_deuda_total' => $this->nuevaDeudaTotal
        ]);
    }

    // Método para preparar resumen de vista previa - COMPLETO
    private function prepareResumen()
    {
        $conceptosDetallados = [];

        foreach ($this->nuevosConceptos as $concepto) {
            $nombreConcepto = $this->conceptosDisponibles[$concepto['nom_concepto_pago_id']] ?? 'Concepto no encontrado';
            $monto = floatval($concepto['exchange_ammount'] ?? 0);
            
            $conceptosDetallados[] = [
                'nombre' => $nombreConcepto,
                'descripcion' => $concepto['concepto_description'] ?: 'Sin descripción',
                'monto' => $monto,
                'permite_descuento' => $concepto['status_discount'] === 'true',
                'anualidad' => $concepto['status_annuity'] === 'true',
                'tipo_id' => $concepto['nom_concepto_pago_id']
            ];
        }

        $this->resumen = [
            'estudiante' => $this->estudianteSeleccionado,
            'planpago' => $this->planpagosDisponibles[$this->planpagoId] ?? 'No seleccionado',
            'planpago_id' => $this->planpagoId,
            'nuevaCuenta' => $this->nuevaCuenta,
            'nuevosConceptos' => $conceptosDetallados,
            'totalNuevaDeuda' => $this->nuevaDeudaTotal,
            'deudaActualTotal' => $this->deudaActualTotal ?? 0,
            'deudaTotalFinal' => ($this->deudaActualTotal ?? 0) + $this->nuevaDeudaTotal,
            'fecha_vencimiento_formateada' => $this->nuevaCuenta['date_expiration'] ? 
                \Carbon\Carbon::parse($this->nuevaCuenta['date_expiration'])->format('d/m/Y') : 'No definida',
            'total_conceptos' => count($conceptosDetallados),
            'conceptos_con_descuento' => collect($conceptosDetallados)->where('permite_descuento', true)->count(),
            'conceptos_anualidad' => collect($conceptosDetallados)->where('anualidad', true)->count()
        ];
    }

    // Método para volver al paso 4 y editar
    public function volverAEditarConceptos()
    {
        $this->currentStep = 4;
        
        $this->dispatchBrowserEvent('swal', [
            'title' => 'Puede Editar',
            'text' => 'Puede modificar los conceptos y luego volver a vista previa.',
            'icon' => 'info'
        ]);
    }




    // ============ NAVEGACIÓN ============
    public function siguientePaso()
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function pasoAnterior()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function irAPaso($paso)
    {
        if ($paso >= 1 && $paso <= $this->totalSteps) {
            $this->currentStep = $paso;
        }
    }

    public function render()
    {
        return view('livewire.administracion.estudiants.asistente-deuda-individual-component');
    }

    // Método para agregar un concepto vacío
    public function agregarConceptoVacio()
    {
        $this->nuevosConceptos[] = [
            'nom_concepto_pago_id' => '',
            'concepto_description' => $this->nuevaCuenta['name'] ?? '', // Auto-completar con nombre de cuenta
            'exchange_ammount' => 0,
            'status_discount' => 'false',
            'status_annuity' => 'false'
        ];
        
        // Recalcular la deuda
        $this->calcularNuevaDeuda();
    }

    // Método para remover un concepto - CORREGIDO
    public function removerConcepto($index)
    {
        if (count($this->nuevosConceptos) > 1) {
            array_splice($this->nuevosConceptos, $index, 1);
            $this->calcularNuevaDeuda();
        } else {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No se puede eliminar',
                'text' => 'Debe mantener al menos un concepto de pago.',
                'icon' => 'warning'
            ]);
        }
    }

    // Método específico para cambios en montos - NUEVO MÉTODO
    public function actualizarMonto($index, $value)
    {
        $monto = floatval($value);
        if ($monto >= 0) {
            $this->nuevosConceptos[$index]['exchange_ammount'] = $monto;
            $this->calcularNuevaDeuda();
        }
    }

    // Método para calcular la nueva deuda total - CORREGIDO
    public function calcularNuevaDeuda()
    {
        $total = 0;
        foreach ($this->nuevosConceptos as $concepto) {
            $total += floatval($concepto['exchange_ammount'] ?? 0);
        }
        $this->nuevaDeudaTotal = $total;
    }

    // Método cuando cambia el nombre de la cuenta - CORREGIDO
    public function updatedNuevaCuenta($value, $key)
    {
        if ($key === 'name' && !empty($value)) {
            // Auto-completar descripción de conceptos existentes
            foreach ($this->nuevosConceptos as $index => $concepto) {
                if (empty($concepto['concepto_description'])) {
                    $this->nuevosConceptos[$index]['concepto_description'] = $value;
                }
            }
        }
    }

    // Método para alternar checkboxes - NUEVO MÉTODO
    public function toggleCheckbox($index, $field)
    {
        if (isset($this->nuevosConceptos[$index][$field])) {
            $this->nuevosConceptos[$index][$field] = 
                $this->nuevosConceptos[$index][$field] === 'true' ? 'false' : 'true';
        }
    }

    // Método para limpiar todos los conceptos - NUEVO MÉTODO
    public function limpiarConceptos()
    {
        $this->nuevosConceptos = [];
        $this->agregarConceptoVacio();
        $this->calcularNuevaDeuda();
    }

    // Método para actualizar monto de concepto - CORREGIDO
    public function updatedNuevosConceptos($value, $path)
    {
        // Recalcular la deuda cuando cambie cualquier campo de conceptos
        $this->calcularNuevaDeuda();
        
        // Auto-completar descripción cuando se selecciona un tipo de concepto
        if (str_contains($path, 'nom_concepto_pago_id') && !empty($value)) {
            $parts = explode('.', $path);
            if (count($parts) === 3) {
                $index = $parts[1];
                $conceptoId = $value;
                
                // Si no hay descripción o está vacía, auto-completar
                if (empty($this->nuevosConceptos[$index]['concepto_description'])) {
                    $conceptoNombre = $this->conceptosDisponibles[$conceptoId] ?? '';
                    $this->nuevosConceptos[$index]['concepto_description'] = $conceptoNombre;
                }
            }
        }
    }

    // ============ PASO 6: Confirmación ============

    // Asegurar que confirmacionData esté disponible
    public function confirmarCreacionDeuda()
    {
        // Validar que todos los datos necesarios estén presentes
        if (empty($this->resumen) || !$this->validarDatosConfirmacion()) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Datos Incompletos',
                'text' => 'No hay datos suficientes para confirmar. Regrese al paso anterior.',
                'icon' => 'error'
            ]);
            return;
        }

        // Asegurar que confirmacionData tenga la estructura correcta
        $this->confirmacionData = array_merge($this->resumen, [
            'estadisticas' => $this->getEstadisticasConfirmacion()
        ]);
        
        $this->currentStep = 6;

        // Log para auditoría
        \Log::info('Confirmación de deuda preparada', [
            'estudiante_id' => $this->estudianteId,
            'planpago_id' => $this->planpagoId,
            'total_conceptos' => $this->resumen['total_conceptos'],
            'monto_total' => $this->resumen['totalNuevaDeuda'],
            'paso_actual' => $this->currentStep
        ]);
    }

    // Método para validar datos de confirmación
    private function validarDatosConfirmacion()
    {
        return !empty($this->resumen['estudiante']) &&
               !empty($this->resumen['planpago_id']) &&
               !empty($this->resumen['nuevaCuenta']['name']) &&
               !empty($this->resumen['nuevaCuenta']['date_expiration']) &&
               !empty($this->resumen['nuevosConceptos']) &&
               $this->resumen['totalNuevaDeuda'] > 0;
    }

    // ============ MÉTODO PARA CREAR DEUDA INDIVIDUAL CON VERIFICACIÓN ============
    public function crearDeudaIndividual()
    {
        // ============ VERIFICACIÓN DE CAMBIO DE PLAN DE PAGO ============
        try {
            // Obtener el plan de pago actual del estudiante desde la tabla administrativa
            $administrativaActual = Administrativa::where('estudiant_id', $this->estudianteId)->first();
            $planpagoActualId = $administrativaActual->planpago_id ?? null;
            
            // Verificar si hay un cambio de plan de pago
            if ($planpagoActualId && $planpagoActualId != $this->planpagoId) {
                $planpagoActual = Planpago::find($planpagoActualId);
                $planpagoNuevo = Planpago::find($this->planpagoId);
                
                // Renderizar el partial con los datos
                $htmlContent = view('livewire.administracion.estudiants.partials.confirmacion-cambio-planpago', [
                    'planActualNombre' => $planpagoActual->name ?? 'No asignado',
                    'planActualId' => $planpagoActualId,
                    'planNuevoNombre' => $planpagoNuevo->name ?? 'No asignado',
                    'planNuevoId' => $this->planpagoId
                ])->render();
                
                // Mostrar alerta de confirmación para el cambio
                $this->dispatchBrowserEvent('swal:confirmacion-cambio-planpago', [
                    'title' => 'Cambio de Plan de Pago Detectado',
                    'html' => $htmlContent,
                    'icon' => 'warning',
                    'confirmButtonText' => 'Sí, Cambiar Plan y Crear Deuda',
                    'cancelButtonText' => 'Cancelar',
                    'planpago_actual_id' => $planpagoActualId,
                    'planpago_nuevo_id' => $this->planpagoId
                ]);
                
                return; // Detener la ejecución hasta la confirmación del usuario
            }
        } catch (\Exception $e) {
            \Log::error('Error al verificar plan de pago: ' . $e->getMessage(), [
                'estudiante_id' => $this->estudianteId,
                'planpago_id' => $this->planpagoId
            ]);
        }

        // Si no hay cambio o el usuario confirmó, proceder con la creación normal
        $this->procederConCreacionDeuda();
    }

    // ============ MÉTODO PARA PROCEDER CON LA CREACIÓN (DESPUÉS DE CONFIRMACIÓN) ============
    public function procederConCreacionDeuda($confirmado = false, $planpagoActualId = null, $planpagoNuevoId = null)
    {
        // Si viene de una confirmación, registrar el cambio
        if ($confirmado && $planpagoActualId && $planpagoNuevoId) {
            \Log::info('Cambio de plan de pago confirmado por el usuario', [
                'estudiante_id' => $this->estudianteId,
                'planpago_anterior' => $planpagoActualId,
                'planpago_nuevo' => $planpagoNuevoId,
                'usuario' => auth()->user()->name ?? 'Sistema'
            ]);
        }

        // Validación final antes de crear
        if (!$this->validarDatosConfirmacion()) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error de Validación',
                'text' => 'Los datos no son válidos para crear la deuda. Regrese al paso 4.',
                'icon' => 'error'
            ]);
            $this->currentStep = 4;
            return;
        }

        DB::beginTransaction();

        try {
            // ============ CREAR LA CUENTA INDIVIDUAL ============
            $cuenta = Cuentaxpagar::create([
                'planpago_id' => $this->planpagoId,
                'estudiant_id' => $this->estudianteId,
                'name' => $this->resumen['nuevaCuenta']['name'],
                'type' => 'INDIVIDUAL',
                'date_expiration' => $this->resumen['nuevaCuenta']['date_expiration'],
                'date_calendar_start' => $this->resumen['nuevaCuenta']['date_calendar_start'] ?? null,
                'date_calendar_end' => $this->resumen['nuevaCuenta']['date_calendar_end'] ?? null,
                'description' => $this->resumen['nuevaCuenta']['description'] ?? $this->resumen['nuevaCuenta']['name'],
                'observations' => $this->resumen['nuevaCuenta']['observations'] ?? null,
                'status_active' => 'true',
                'status_delete' => 'true',
                'status_bad' => $this->resumen['nuevaCuenta']['status_bad'] ?? 'false',
                'status_inscription' => 'false',
                'status_exchange' => 1,
                'enable_late_payment' => false,
                'status_late_payment' => 0,
            ]);

            $this->cuentaCreadaId = $cuenta->id;

            // ============ CREAR LOS CONCEPTOS DE PAGO ============
            $conceptosCreados = 0;
            $totalCreado = 0;

            foreach ($this->resumen['nuevosConceptos'] as $conceptoData) {
                ConceptoPago::create([
                    'cuentaxpagar_id' => $cuenta->id,
                    'nom_concepto_pago_id' => $conceptoData['tipo_id'],
                    'concepto_description' => $conceptoData['descripcion'],
                    'concepto_observations' => null,
                    'concepto_ammount' => $conceptoData['monto'],
                    'exchange_ammount' => $conceptoData['monto'],
                    'status_modifiable' => 'true',
                    'status_discount' => $conceptoData['permite_descuento'] ? 'true' : 'false',
                    'status_active' => 'true',
                    'status_annuity' => $conceptoData['anualidad'] ? 'true' : 'false',
                ]);

                $conceptosCreados++;
                $totalCreado += $conceptoData['monto'];
            }

            // ============ ACTUALIZAR INFORMACIÓN ADMINISTRATIVA ============
            $observaciones = 'Asignacion de Deuda Individual';
            if ($confirmado) {
                $observaciones .= ' - Cambio de plan de pago confirmado (de ' . $planpagoActualId . ' a ' . $planpagoNuevoId . ')';
            }

            $administrativa = Administrativa::updateOrCreate(
                ['estudiant_id' => $this->estudianteId],
                [
                    'planpago_id' => $this->planpagoId,
                    'user_id' => auth()->id() ?? null,
                    'observations' => $observaciones,
                ]
            );

            DB::commit();

            // ============ PREPARAR DATOS PARA ÉXITO ============
            $this->operacionExitosa = true;
            $this->nuevaDeudaTotal = $totalCreado;

            // Log de éxito
            \Log::info('Deuda individual creada exitosamente', [
                'cuenta_id' => $this->cuentaCreadaId,
                'estudiante_id' => $this->estudianteId,
                'conceptos_creados' => $conceptosCreados,
                'total_creado' => $totalCreado,
                'planpago_id' => $this->planpagoId,
                'cambio_planpago' => $confirmado ? 'SÍ' : 'NO'
            ]);

            // Avanzar al paso de éxito
            $this->currentStep = 7;

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Excelente!',
                'text' => 'La deuda individual ha sido creada exitosamente.',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar',
                'confirmButtonColor' => '#28a745'
            ]);

            // Emitir evento para notificar a otros componentes
            $this->emit('deudaIndividualCreada', $this->cuentaCreadaId);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorMessage = 'Error al crear la deuda individual: ' . $e->getMessage();
            
            \Log::error($errorMessage, [
                'estudiante_id' => $this->estudianteId,
                'planpago_id' => $this->planpagoId,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error al Crear Deuda',
                'text' => $errorMessage,
                'icon' => 'error'
            ]);
            
            // Volver a vista previa en caso de error
            $this->currentStep = 5;
        }
    }

    // ============ MÉTODO PARA MANEJAR LA CONFIRMACIÓN DEL USUARIO ============
    public function confirmarCambioPlanPago($planpagoActualId, $planpagoNuevoId)
    {
        // Verificar que los IDs coincidan con los actuales
        if ($planpagoActualId && $planpagoNuevoId && $planpagoNuevoId == $this->planpagoId) {
            $this->procederConCreacionDeuda(true, $planpagoActualId, $planpagoNuevoId);
        } else {
            // Los datos han cambiado, mostrar error
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Datos Inconsistentes',
                'text' => 'Los datos del plan de pago han cambiado. Por favor, revise la información y vuelva a intentarlo.',
                'icon' => 'error'
            ]);
        }
    }

    // Método para cancelar y volver atrás
    public function cancelarCreacion()
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => 'Creación Cancelada',
            'text' => 'La creación de la deuda ha sido cancelada. Puede volver a intentarlo.',
            'icon' => 'info'
        ]);

        $this->currentStep = 5;
    }

    // ============ PASO 6: Confirmación ============

    // Reemplazar la propiedad computada por un método regular
    public function getEstadisticasConfirmacion()
    {
        if (empty($this->confirmacionData) || empty($this->confirmacionData['nuevosConceptos'])) {
            return [
                'total_conceptos' => 0,
                'conceptos_con_descuento' => 0,
                'conceptos_anualidad' => 0,
                'monto_promedio' => 0,
                'monto_maximo' => 0,
                'monto_minimo' => 0
            ];
        }

        $conceptos = $this->confirmacionData['nuevosConceptos'];
        $montos = collect($conceptos)->pluck('monto')->filter();
        
        return [
            'total_conceptos' => count($conceptos),
            'conceptos_con_descuento' => collect($conceptos)->where('permite_descuento', true)->count(),
            'conceptos_anualidad' => collect($conceptos)->where('anualidad', true)->count(),
            'monto_promedio' => $montos->isNotEmpty() ? $montos->avg() : 0,
            'monto_maximo' => $montos->isNotEmpty() ? $montos->max() : 0,
            'monto_minimo' => $montos->isNotEmpty() ? $montos->min() : 0
        ];
    }

    // Método para obtener estadísticas rápidas (alternativa)
    public function getEstadisticasRapidasProperty()
    {
        return $this->getEstadisticasConfirmacion();
    }


    // ============ PASO 7: Éxito ============

    // Método para obtener estadísticas del éxito
    public function getEstadisticasExitoProperty()
    {
        if (!$this->operacionExitosa) {
            return [
                'total_conceptos' => 0,
                'monto_total' => 0,
                'fecha_creacion' => now()->format('d/m/Y H:i'),
                'cuenta_id' => null
            ];
        }

        return [
            'total_conceptos' => count($this->nuevosConceptos),
            'monto_total' => $this->nuevaDeudaTotal,
            'fecha_creacion' => now()->format('d/m/Y H:i'),
            'cuenta_id' => $this->cuentaCreadaId,
            'estudiante_nombre' => $this->estudianteSeleccionado['name'] ?? 'N/A',
            'estudiante_ci' => $this->estudianteSeleccionado['ci_estudiant'] ?? 'N/A',
            'plan_pago' => $this->planpagosDisponibles[$this->planpagoId] ?? 'N/A'
        ];
    }

    // Método para ir a la gestión de estudiantes
    public function irAGestionEstudiantes()
    {
        // Emitir evento para redirección o navegación
        $this->emit('redireccionarAEstudiantes');
        
        // También se puede usar redirect si es necesario
        // return redirect()->route('administracion.estudiants.index');
    }

    // Método para ver la cuenta creada
    public function verCuentaCreada()
    {
        if ($this->cuentaCreadaId) {
            // Emitir evento para abrir modal o redireccionar
            $this->emit('mostrarCuentaCreada', $this->cuentaCreadaId);
            
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Redireccionando...',
                'text' => 'Será dirigido a los detalles de la cuenta creada.',
                'icon' => 'info'
            ]);
        }
    }

    // Método para obtener el resumen del éxito
    public function getResumenExitoProperty()
    {
        if (!$this->operacionExitosa) {
            return [
                'titulo' => 'Operación No Completada',
                'mensaje' => 'La operación no se completó exitosamente.',
                'icono' => 'error',
                'color' => 'danger'
            ];
        }

        return [
            'titulo' => '¡Deuda Individual Creada Exitosamente!',
            'mensaje' => 'La deuda individual ha sido registrada correctamente en el sistema.',
            'icono' => 'success',
            'color' => 'success',
            'detalles' => [
                'Cuenta ID' => $this->cuentaCreadaId ?? 'N/A',
                'Estudiante' => $this->estudianteSeleccionado['name'] ?? 'N/A',
                'Cédula' => $this->estudianteSeleccionado['ci_estudiant'] ?? 'N/A',
                'Total Conceptos' => count($this->nuevosConceptos),
                'Monto Total' => '$' . number_format($this->nuevaDeudaTotal, 2),
                'Fecha' => now()->format('d/m/Y H:i')
            ]
        ];
    }

    // Método simplificado para reiniciar el asistente
    public function reiniciarAsistente()
    {
        try {
            // Mantener solo los datos básicos del estudiante y plan de pago
            $estudianteTemp = $this->estudianteSeleccionado;
            $planpagoTemp = $this->planpagoId;

            // Reset de los datos de la deuda actual
            $this->reset([
                'nuevaCuenta', 'nuevosConceptos', 'nuevaDeudaTotal',
                'resumen', 'confirmacionData', 'operacionExitosa', 'cuentaCreadaId'
            ]);

            // Restaurar datos básicos
            $this->estudianteSeleccionado = $estudianteTemp;
            $this->planpagoId = $planpagoTemp;

            // Volver al paso 4 para crear nueva deuda
            $this->currentStep = 4;

            // Re-inicializar conceptos
            $this->nuevosConceptos = [];
            $this->agregarConceptoVacio();

            \Log::info('Asistente reiniciado para nueva deuda', [
                'estudiante_id' => $this->estudianteId,
                'paso_actual' => $this->currentStep
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al reiniciar asistente: ' . $e->getMessage());

            // Reset completo como fallback
            $this->reset();
            $this->currentStep = 1;
            $this->cargarDatosIniciales();
        }
    }

    // Método para reiniciar el asistente completamente al paso 1
    public function reiniciarAsistenteCompleto()
    {
        $this->reset();
        $this->mount();
        $this->currentStep = 1;
        
        $this->dispatchBrowserEvent('swal', [
            'title' => 'Asistente Reiniciado',
            'text' => 'Se ha vuelto al inicio del asistente.',
            'icon' => 'info'
        ]);
    }

    
}
