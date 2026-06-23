<?php

namespace App\Http\Livewire\Administracion\Configuraciones;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;

// Models
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\NomConceptoPago;
use App\Models\app\Planpago;
use App\Models\app\Estudiant;

class ConceptoPagoCrud extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';

    // Properties for filtering
    public $filter_planpago_id;
    public $filter_type;
    public $filter_asociado;
    public $filter_ci;
    public $perPage = 10;
    public $finicial;
    public $ffinal;
    public $filter_status_bad;

    // Modal properties
    public $showEditModal = false;
    public $showCreateModal = false;
    public $isEditMode = false;
    public $selectedConcepto;

    // Edit form properties
    public $editForm = [
        'nom_concepto_pago_id' => '',
        'exchange_ammount' => '',
        'concepto_description' => '',
        'concepto_observations' => '',
        'status_active' => 'true'
    ];

    // Create form properties (PROPIEDADES SEPARADAS)
    public $create_type = 'GENERAL';
    public $create_cuentaxpagar_id = '';
    public $create_nom_concepto_pago_id = '';
    public $create_exchange_ammount = '';
    public $create_concepto_description = '';
    public $create_concepto_observations = '';
    public $create_status_active = 'true';
    public $create_status_discount = 'false';
    public $create_status_annual = 'false';

    public $currentCuentaxpagarId;
    public $selectedCuentaInfo = [
        'name' => '',
        'planpago_name' => '',
        'date_expiration' => '',
        'type' => ''
    ];

    // Lists for dropdowns
    public $list_nom_concepto_pago = [];
    public $list_cuentaxpagar_general = [];
    public $list_cuentaxpagar_individual = [];
    public $list_estudiants = [];
    public $list_planpagos = [];

    protected $queryString = [
        'filter_planpago_id' => ['except' => ''],
        'filter_type' => ['except' => ''],
        'filter_asociado' => ['except' => ''],
        'filter_ci' => ['except' => ''],
        'filter_status_bad' => ['except' => ''],
        'finicial' => ['except' => ''],
        'ffinal' => ['except' => ''],
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'showEdit' => 'showEditModal',
        'alertQuestion',
        'concepto_delete'
    ];

    public function mount()
    {
        $this->initializeDropdowns();
    }

    public function initializeDropdowns()
    {
        $this->list_nom_concepto_pago = NomConceptoPago::select('name', 'id')
            ->orderBy('name', 'asc')
            //->where('status_active', 'true')
            ->pluck('name', 'id')
            ->toArray();

        // Lista de cuentas GENERALES
        $this->list_cuentaxpagar_general = Cuentaxpagar::select('cuentaxpagars.*')
            ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->where('cuentaxpagars.type', 'GENERAL')
            ->where('cuentaxpagars.status_active', 'true')
            ->orderBy('planpagos.name', 'asc')
            ->get()
            ->mapWithKeys(function ($cuenta) {
                return [$cuenta->id => $cuenta->planpago->name . ' - ' . $cuenta->name];
            })
            ->toArray();

        // Lista de cuentas INDIVIDUALES
        $this->list_cuentaxpagar_individual = Cuentaxpagar::select('cuentaxpagars.*')
            ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->join('estudiants', 'estudiants.id', '=', 'cuentaxpagars.estudiant_id')
            ->where('cuentaxpagars.type', 'INDIVIDUAL')
            ->where('cuentaxpagars.status_active', 'true')
            ->orderBy('planpagos.name', 'asc')
            ->get()
            ->mapWithKeys(function ($cuenta) {
                $estudianteInfo = $cuenta->estudiant ? 
                    '(' . $cuenta->estudiant->ci_estudiant . ') ' . $cuenta->estudiant->name : 
                    'Estudiante no asignado';
                return [$cuenta->id => $cuenta->planpago->name . ' - ' . $cuenta->name . ' - ' . $estudianteInfo];
            })
            ->toArray();

        $this->list_planpagos = Planpago::select('name', 'id')
            ->orderBy('name', 'asc')
            ->where('status_active', 'true')
            ->pluck('name', 'id')
            ->toArray();
    }

    // ✅ MÉTODO QUE DEBERÍA EJECUTARSE CON SELECT
    public function updatedCreateType($value)
    {
        \Log::info('updatedCreateType ejecutado', ['value' => $value]);
        
        // Resetear la selección de cuenta cuando cambia el tipo
        $this->create_cuentaxpagar_id = '';
        $this->resetErrorBag(['create_cuentaxpagar_id']);
        
        // Forzar actualización del componente
        $this->dispatchBrowserEvent('type-changed', ['type' => $value]);
    }

    // Create functionality
    public function showCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function resetCreateForm()
    {
        // Resetear todas las propiedades de creación
        $this->create_type = 'GENERAL';
        $this->create_cuentaxpagar_id = '';
        $this->create_nom_concepto_pago_id = '';
        $this->create_exchange_ammount = '';
        $this->create_concepto_description = '';
        $this->create_concepto_observations = '';
        $this->create_status_active = 'true';
        $this->create_status_discount = 'false';
        $this->create_status_annual = 'false';
        
        $this->resetErrorBag();
    }

    public function store()
    {
        // Reglas de validación
        $validationRules = [
            'create_type' => 'required|in:GENERAL,INDIVIDUAL',
            'create_cuentaxpagar_id' => 'required|exists:cuentaxpagars,id',
            'create_nom_concepto_pago_id' => 'required|exists:nom_concepto_pagos,id',
            'create_exchange_ammount' => 'required|numeric|min:0.01',
            'create_concepto_description' => 'required|string|max:500',
            'create_concepto_observations' => 'nullable|string|max:500',
            'create_status_active' => 'required|in:true,false',
            'create_status_discount' => 'required|in:true,false',
            'create_status_annual' => 'required|in:true,false',
        ];

        $this->validate($validationRules);

        try {
            // Verificar que la cuenta seleccionada existe y coincide con el tipo
            $cuenta = Cuentaxpagar::where('id', $this->create_cuentaxpagar_id)
                                ->where('type', $this->create_type)
                                ->where('status_active', 'true')
                                ->first();

            if (!$cuenta) {
                throw new \Exception('La cuenta seleccionada no coincide con el tipo especificado o no está disponible.');
            }

            // Crear el nuevo concepto de pago
            ConceptoPago::create([
                'nom_concepto_pago_id' => $this->create_nom_concepto_pago_id,
                'cuentaxpagar_id' => $this->create_cuentaxpagar_id,
                'exchange_ammount' => $this->create_exchange_ammount,
                'concepto_description' => $this->create_concepto_description,
                'concepto_observations' => $this->create_concepto_observations,
                'status_active' => $this->create_status_active,
                'status_discount' => $this->create_status_discount,
                'status_annual' => $this->create_status_annual,
            ]);

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Creado Exitosamente!',
                'text' => 'El concepto de pago ha sido creado exitosamente.',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar'
            ]);

            $this->closeCreateModal();
            $this->emit('refreshComponent');

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo crear el concepto: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Entendido'
            ]);
        }
    }

    public function closeCreateModal()
    {
        $this->reset(['showCreateModal']);
        $this->resetCreateForm();
    }


    // Método para cargar información de la cuenta
    private function loadCuentaInfo($cuentaxpagarId)
    {
        $cuenta = Cuentaxpagar::with(['planpago', 'estudiant'])->find($cuentaxpagarId);
        
        if ($cuenta) {
            $this->selectedCuentaInfo = [
                'name' => $cuenta->name,
                'planpago_name' => $cuenta->planpago->name ?? 'N/A',
                'date_expiration' => $cuenta->date_expiration ? \Carbon\Carbon::parse($cuenta->date_expiration)->format('d/m/Y') : 'N/A',
                'type' => $cuenta->type
            ];
        } else {
            $this->selectedCuentaInfo = [
                'name' => 'Cuenta no encontrada',
                'planpago_name' => 'N/A',
                'date_expiration' => 'N/A',
                'type' => 'N/A'
            ];
        }
    }

    // Edit functionality (se mantiene igual)
    public function showEditModal($id)
    {
        $this->selectedConcepto = ConceptoPago::with(['cuentaxpagar.planpago', 'nomconceptopago'])->findOrFail($id);

        // Check if concept can be edited
        if (!$this->selectedConcepto->status_edit) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No se puede editar',
                'text' => 'Este concepto de pago no puede ser editado porque está marcado como no editable.',
                'icon' => 'warning',
                'confirmButtonText' => 'Entendido'
            ]);
            return;
        }

        // Guardar el ID de la cuenta actual
        $this->currentCuentaxpagarId = $this->selectedConcepto->cuentaxpagar_id;

        // Cargar información de la cuenta para mostrar
        $this->loadCuentaInfo($this->currentCuentaxpagarId);

        // Fill the edit form
        $this->editForm = [
            'nom_concepto_pago_id' => $this->selectedConcepto->nom_concepto_pago_id,
            'exchange_ammount' => $this->selectedConcepto->exchange_ammount,
            'concepto_description' => $this->selectedConcepto->concepto_description,
            'concepto_observations' => $this->selectedConcepto->concepto_observations,
            'status_active' => $this->selectedConcepto->status_active,
        ];

        $this->isEditMode = true;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'editForm.nom_concepto_pago_id' => 'required|exists:nom_concepto_pagos,id',
            'editForm.exchange_ammount' => 'required|numeric|min:0.01',
            'editForm.concepto_description' => 'required|string|max:500',
            'editForm.concepto_observations' => 'nullable|string|max:500',
            'editForm.status_active' => 'required|in:true,false',
        ]);

        try {
            // Verify the concept can still be edited
            if (!$this->selectedConcepto->status_edit) {
                throw new \Exception('Este concepto ya no puede ser editado.');
            }

            $this->selectedConcepto->update($this->editForm);

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Actualizado!',
                'text' => 'El concepto de pago ha sido actualizado exitosamente.',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar'
            ]);

            $this->closeModal();
            $this->emit('refreshComponent');

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo actualizar el concepto: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Entendido'
            ]);
        }
    }

    public function closeModal()
    {
        $this->reset(['showEditModal', 'isEditMode', 'selectedConcepto', 'editForm', 'selectedCuentaInfo']);
    }

    // Existing delete methods remain the same
    public function alertQuestion($id, $method)
    {
        $concepto = ConceptoPago::with('cuentaxpagar')->findOrFail($id);

        // Check if concept can be deleted
        if ($concepto->conceptocancelados->count() ) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No se puede eliminar',
                'text' => 'Este concepto no puede ser eliminado porque está asociado a pagos o pertenece a una cuenta GENERAL.',
                'icon' => 'warning',
                'confirmButtonText' => 'Entendido'
            ]);
            return;
        }

        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => '¿Estás seguro de realizar esta acción?',
            'text' => 'Si la realizas, no la podrás revertir.',
            'id' => $concepto->id,
            'method' => $method
        ]);
    }

    public function concepto_delete($id)
    {
        $concepto = ConceptoPago::findOrFail($id);
        $concepto->delete();
        
        $this->showSwal('¡Excelente, buen trabajo!', 'Operación realizada exitosamente.');
        $this->emit('refreshComponent');
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

    public function getConceptoPagosQuery()
    {
        return ConceptoPago::with([
            'cuentaxpagar.planpago',
            'cuentaxpagar.estudiant',
            'nomconceptopago',
            'conceptocancelados'
        ])
            ->select('concepto_pagos.*')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('concepto_pagos.status_active', 'true')
            ->where('cuentaxpagars.status_active', 'true')

            // 📌 Filtro plan de pago
            ->when($this->filter_planpago_id, function ($query) {
                return $query->where('cuentaxpagars.planpago_id', $this->filter_planpago_id);
            })

            // 📌 Filtro tipo GENERAL / INDIVIDUAL
            ->when($this->filter_type, function ($query) {
                return $query->where('cuentaxpagars.type', $this->filter_type);
            })

            // 🔗 Asociado / No asociado
            ->when($this->filter_asociado, function ($query) {
                if ($this->filter_asociado === 'asociado') {
                    return $query->whereHas('conceptocancelados');
                }
                if ($this->filter_asociado === 'no_asociado') {
                    return $query->whereDoesntHave('conceptocancelados');
                }
            })

            // 📅 Fecha inicial
            ->when($this->finicial, function ($query) {
                return $query->whereDate('cuentaxpagars.date_expiration', '>=', $this->finicial);
            })

            // 📅 Fecha final
            ->when($this->ffinal, function ($query) {
                return $query->whereDate('cuentaxpagars.date_expiration', '<=', $this->ffinal);
            })

            // ⚠ Incobrable / Normal
            ->when($this->filter_status_bad, function ($query) {
                if ($this->filter_status_bad === 'incobrable') {
                    return $query->where('cuentaxpagars.status_bad', 'true');
                }
                if ($this->filter_status_bad === 'normal') {
                    return $query->where('cuentaxpagars.status_bad', 'false');
                }
            })

            // 🔍 Filtro CI estudiante / CI representante
            ->when($this->filter_ci, function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('cuentaxpagar.estudiant', function ($q2) {
                        $q2->where('ci_estudiant', 'like', '%' . $this->filter_ci . '%')
                           ->orWhere('name', 'like', '%' . $this->filter_ci . '%');
                    })
                    ->orWhereHas('cuentaxpagar.estudiant.representant', function ($q3) {
                        $q3->where('ci_representant', 'like', '%' . $this->filter_ci . '%')
                           ->orWhere('name', 'like', '%' . $this->filter_ci . '%');
                    });
                });
            })

            ->orderBy('concepto_pagos.created_at', 'desc');
    }


    public function getStatsProperty()
    {
        $baseQuery = $this->getConceptoPagosQuery();

        return [
            'total' => $baseQuery->count(),
            'general' => $baseQuery->clone()->where('cuentaxpagars.type', 'GENERAL')->count(),
            'individual' => $baseQuery->clone()->where('cuentaxpagars.type', 'INDIVIDUAL')->count(),
            'total_monto' => $baseQuery->clone()->sum('concepto_pagos.exchange_ammount'),
        ];
    }

    public function render()
    {
        $concepto_pagos = $this->getConceptoPagosQuery()->paginate($this->perPage);
        $stats = $this->getStatsProperty();

        return view('livewire.administracion.configuraciones.concepto-pago-crud', compact('concepto_pagos', 'stats'));
    }
}