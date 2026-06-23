<?php

namespace App\Http\Livewire\Administracion\Cuentaxpagar\Asistente;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;

// Models
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago;
use App\Models\app\Estudiant;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';

    // Properties for filtering
    public $planpago_id;
    public $type;
    public $finicial;
    public $ffinal;
    public $status_bad;
    public $filter_ci;

    // Properties for data
    public $planpagos;
    public $list_estudiants;
    public $list_planpagos;

    // Search properties
    public $search = '';
    public $perPage = 10;

    // Modal properties
    public $showInfoModal = false;
    public $showEditModal = false;
    public $showCreateModal = false;
    public $selectedCuentaxpagar;
    public $cuentaxpagarDetails;

    // Agregar estas propiedades
    public $estudianteSearch = '';
    public $searchEstudiantes = [];
    public $selectedEstudianteName = '';

    // Edit form properties
    public $editForm = [
        'name' => '',
        'planpago_id' => '',
        'type' => '',
        'estudiant_id' => '',
        'date_expiration' => '',
        'date_calendar_start' => '',
        'date_calendar_end' => '',
        'description' => '',
        'observations' => '',
        'status_bad' => ''
    ];

    // Create form properties
    public $createForm = [
        'name' => '',
        'planpago_id' => '',
        'type' => '',
        'estudiant_id' => '',
        'date_expiration' => '',
        'date_calendar_start' => '',
        'date_calendar_end' => '',
        'description' => '',
        'observations' => '',
        'status_bad' => 'false'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'finicial' => ['except' => ''],
        'ffinal' => ['except' => ''],
        'status_bad' => ['except' => ''],
    ];

    protected $listeners = [
        'cuentaxpagarDeleted' => 'refreshData',
        'cuentaxpagarUpdated' => 'refreshData',
        'cuentaxpagarCreated' => 'refreshData',
        'remove' => 'deleteCuentaxpagar' // ← CAMBIAR 'confirmDelete' por 'remove'
    ];

    // protected $queryString = [
    //     'filter_status_bad' => ['except' => ''],
    //     // ...otros filtros
    // ];

    public function mount()
    {
        $this->initializeData();
    }

    public function initializeData()
    {
        $this->planpagos = Planpago::where('status_active', 'true')->get();
        $this->list_planpagos = Planpago::select('name', 'id')
            ->orderBy('name', 'asc')
            ->where('status_active', 'true')
            ->pluck('name', 'id')
            ->toArray();

        $this->list_estudiants = Estudiant::list_active();
    }

    public function updated($property)
    {
        $filterProperties = ['planpago_id', 'type', 'finicial', 'ffinal', 'search'];

        if (in_array($property, $filterProperties)) {
            $this->resetPage();
        }

        // Mostrar/ocultar campo estudiante en creación
        if ($property === 'createForm.type') {
            if ($this->createForm['type'] !== 'INDIVIDUAL') {
                $this->createForm['estudiant_id'] = '';
            }
        }
    }

    // Método para buscar estudiantes
    public function updatedEstudianteSearch($value)
    {
        if (strlen($value) < 2) {
            $this->searchEstudiantes = [];
            return;
        }

        try {
            $estudiantes = Estudiant::where('status_active', 'true')
                ->where(function($query) use ($value) {
                    $query->where('ci_estudiant', 'like', '%' . $value . '%')
                          ->orWhere('name', 'like', '%' . $value . '%');
                })
                ->orderBy('ci_estudiant', 'asc')
                ->limit(20)
                ->get()
                ->map(function($estudiante) {
                    return [
                        'id' => $estudiante->id,
                        'ci_estudiant' => $estudiante->ci_estudiant,
                        'name' => $estudiante->name,
                        'display' => $estudiante->ci_estudiant . ' - ' . $estudiante->name
                    ];
                })
                ->toArray();

            $this->searchEstudiantes = $estudiantes;
        } catch (\Exception $e) {
            $this->searchEstudiantes = [];
            $this->emit('showNotification', [
                'type' => 'error',
                'message' => 'Error al buscar estudiantes: ' . $e->getMessage()
            ]);
        }
    }

    // Método para seleccionar estudiante
    public function selectEstudiante($id, $displayName)
    {
        $this->createForm['estudiant_id'] = $id;
        $this->selectedEstudianteName = $displayName;
        $this->estudianteSearch = '';
        $this->searchEstudiantes = [];
    }

    // Método para limpiar búsqueda
    public function clearEstudianteSearch()
    {
        $this->estudianteSearch = '';
        $this->searchEstudiantes = [];
    }

    // Método para limpiar estudiante seleccionado
    public function clearSelectedEstudiante()
    {
        $this->createForm['estudiant_id'] = '';
        $this->selectedEstudianteName = '';
        $this->estudianteSearch = '';
        $this->searchEstudiantes = [];
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'type',
            'finicial',
            'ffinal',
            'status_bad',
        ]);
        $this->resetPage();
    }

    // Create Methods
    public function showCreate()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function resetCreateForm()
    {
        $this->createForm = [
            'name' => '',
            'planpago_id' => '',
            'type' => '',
            'estudiant_id' => '',
            'date_expiration' => '',
            'date_calendar_start' => '',
            'date_calendar_end' => '',
            'description' => '',
            'observations' => '',
            'status_bad' => 'false'
        ];
        $this->estudianteSearch = '';
        $this->searchEstudiantes = [];
        $this->selectedEstudianteName = '';
        $this->resetErrorBag();
    }

    public function createCuentaxpagar()
    {
        $this->validate([
            'createForm.name' => 'required|string|max:255',
            'createForm.planpago_id' => 'required|exists:planpagos,id',
            'createForm.type' => 'required|in:GENERAL,INDIVIDUAL',
            'createForm.date_expiration' => 'required|date',
            'createForm.date_calendar_start' => 'required|date',
            'createForm.date_calendar_end' => 'required|date|after_or_equal:createForm.date_calendar_start',
            'createForm.description' => 'required|string',
            'createForm.status_bad' => 'required|in:true,false',
        ]);

        if ($this->createForm['type'] === 'INDIVIDUAL') {
            $this->validate([
                'createForm.estudiant_id' => 'required|exists:estudiants,id',
            ]);
        }

        try {
            // Agregar campos de estado por defecto
            $cuentaxpagarData = array_merge($this->createForm, [
                'status_active' => 'true',
                'status_delete' => 'true'
            ]);

            Cuentaxpagar::create($cuentaxpagarData);

            $this->emit('showNotification', [
                'type' => 'success',
                'message' => 'Cuenta por pagar creada exitosamente'
            ]);

            $this->closeModals();
            $this->emit('cuentaxpagarCreated');
        } catch (\Exception $e) {
            $this->emit('showNotification', [
                'type' => 'error',
                'message' => 'Error al crear la cuenta por pagar: ' . $e->getMessage()
            ]);
        }
    }

    // Modal Methods (existing - manteniendo la funcionalidad original)
    public function showInfo($id)
    {
        $this->selectedCuentaxpagar = Cuentaxpagar::with([
            'planpago',
            'estudiant.representant',
            'conceptopagos',
            'conceptopagos.nomconceptopago'
        ])->findOrFail($id);

        $this->cuentaxpagarDetails = $this->prepareDetailsData($this->selectedCuentaxpagar);
        $this->showInfoModal = true;
    }

    public function showEdit($id)
    {
        $this->selectedCuentaxpagar = Cuentaxpagar::findOrFail($id);

        // Fill the edit form with current data
        $this->editForm = [
            'name' => $this->selectedCuentaxpagar->name,
            'planpago_id' => $this->selectedCuentaxpagar->planpago_id,
            'type' => $this->selectedCuentaxpagar->type,
            'estudiant_id' => $this->selectedCuentaxpagar->estudiant_id,
            'date_expiration' => $this->selectedCuentaxpagar->date_expiration,
            'date_calendar_start' => $this->selectedCuentaxpagar->date_calendar_start,
            'date_calendar_end' => $this->selectedCuentaxpagar->date_calendar_end,
            'description' => $this->selectedCuentaxpagar->description,
            'observations' => $this->selectedCuentaxpagar->observations,
            'status_bad' => $this->selectedCuentaxpagar->status_bad,
        ];

        $this->showEditModal = true;
    }

    public function updateCuentaxpagar()
    {
        $this->validate([
            'editForm.name' => 'required|string|max:255',
            'editForm.planpago_id' => 'required|exists:planpagos,id',
            'editForm.type' => 'required|in:GENERAL,INDIVIDUAL',
            'editForm.date_expiration' => 'required|date',
            'editForm.date_calendar_start' => 'required|date',
            'editForm.date_calendar_end' => 'required|date',
            'editForm.description' => 'required|string',
            'editForm.status_bad' => 'required|in:true,false',
        ]);

        if ($this->editForm['type'] === 'INDIVIDUAL') {
            $this->validate([
                'editForm.estudiant_id' => 'required|exists:estudiants,id',
            ]);
        }

        try {
            $this->selectedCuentaxpagar->update($this->editForm);

            $this->emit('showNotification', [
                'type' => 'success',
                'message' => 'Cuenta por pagar actualizada exitosamente'
            ]);

            $this->closeModals();
            $this->emit('cuentaxpagarUpdated');
        } catch (\Exception $e) {
            $this->emit('showNotification', [
                'type' => 'error',
                'message' => 'Error al actualizar la cuenta por pagar: ' . $e->getMessage()
            ]);
        }
    }

    public function closeModals()
    {
        $this->reset([
            'showInfoModal',
            'showEditModal',
            'showCreateModal',
            'selectedCuentaxpagar',
            'cuentaxpagarDetails',
            'editForm',
            'estudianteSearch',
            'searchEstudiantes',
            'selectedEstudianteName'
        ]);
        $this->resetCreateForm();
    }

    private function prepareDetailsData($cuentaxpagar)
    {
        $conceptopagos = $cuentaxpagar->conceptopagos;
        $totalAmount = $conceptopagos ? $conceptopagos->sum('exchange_ammount') : 0;

        return [
            'id' => $cuentaxpagar->id,
            'name' => $cuentaxpagar->name,
            'planpago' => $cuentaxpagar->planpago->name ?? 'N/A',
            'type' => $cuentaxpagar->type,
            'date_expiration' => f_date($cuentaxpagar->date_expiration),
            'date_calendar_start' => f_date($cuentaxpagar->date_calendar_start),
            'date_calendar_end' => f_date($cuentaxpagar->date_calendar_end),
            'description' => $cuentaxpagar->description,
            'observations' => $cuentaxpagar->observations,
            'status_bad' => $cuentaxpagar->status_bad == 'true' ? 'Sí' : 'No',
            'estudiant' => $cuentaxpagar->estudiant ? [
                'ci_estudiant' => $cuentaxpagar->estudiant->ci_estudiant,
                'name' => $cuentaxpagar->estudiant->name,
            ] : null,
            'representant' => $cuentaxpagar->estudiant && $cuentaxpagar->estudiant->representant ? [
                'ci_representant' => $cuentaxpagar->estudiant->representant->ci_representant,
                'name' => $cuentaxpagar->estudiant->representant->name,
            ] : null,
            'conceptopagos_count' => $conceptopagos ? $conceptopagos->count() : 0,
            'total_amount' => number_format($totalAmount, 2),
            'conceptopagos' => $conceptopagos ? $conceptopagos->map(function ($concepto) {
                return [
                    'name' => $concepto->nomconceptopago->name ?? 'N/A',
                    'amount' => number_format($concepto->exchange_ammount, 2),
                    'description' => $concepto->concepto_description,
                ];
            })->toArray() : [],
        ];
    }

    public function getCuentaxpagarsQuery()
    {
        return Cuentaxpagar::with([
            'planpago',
            'estudiant.representant',
            'conceptopagos'
        ])
            ->where('status_active', 'true')

            // 🔍 Tipo GENERAL / INDIVIDUAL
            ->when($this->planpago_id, function ($query) {
                return $query->where('planpago_id', $this->planpago_id);
            })

            // 🔍 Tipo GENERAL / INDIVIDUAL
            ->when($this->type, function ($query) {
                return $query->where('type', $this->type);
            })

            // 📅 Fecha inicial
            ->when($this->finicial, function ($query) {
                return $query->whereDate('date_expiration', '>=', $this->finicial);
            })

            // 📅 Fecha final
            ->when($this->ffinal, function ($query) {
                return $query->whereDate('date_expiration', '<=', $this->ffinal);
            })

            // ⚠ Cuenta incobrable (status_bad)
            ->when($this->status_bad, function ($query) {
                if ($this->status_bad === 'incobrable') {
                    return $query->where('status_bad', 'true');
                }
                if ($this->status_bad === 'normal') {
                    return $query->where('status_bad', 'false');
                }
            })

            // 🔍 Búsqueda CI / nombre / descripción
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {

                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('estudiant', function ($q2) {
                          $q2->where('ci_estudiant', 'like', '%' . $this->search . '%')
                             ->orWhere('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('estudiant.representant', function ($q3) {
                          $q3->where('ci_representant', 'like', '%' . $this->search . '%')
                             ->orWhere('name', 'like', '%' . $this->search . '%');
                      });

                });
            })

            ->orderBy('created_at', 'desc');
    }


    // Método para obtener estadísticas corregido
    public function getStatistics()
    {
        $baseQuery = $this->getCuentaxpagarsQuery();

        return [
            'total' => $baseQuery->count(),
            'individual' => $baseQuery->clone()->where('type', 'INDIVIDUAL')->count(),
            'general' => $baseQuery->clone()->where('type', 'GENERAL')->count(),
            'incobrable' => $baseQuery->clone()->where('status_bad', 'true')->count(),
        ];
    }

    public function render()
    {
        $cuentaxpagars = $this->getCuentaxpagarsQuery()->paginate($this->perPage);
        $statistics = $this->getStatistics();

        return view('livewire.administracion.cuentaxpagar.asistente.index-component', compact('cuentaxpagars', 'statistics'));
    }

    public function refreshData()
    {
        $this->initializeData();
        $this->resetPage();
    }

    public function deleteCuentaxpagar($id)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);

        if ($cuentaxpagar->status_delete) {
            $cuentaxpagar->delete();

            $this->emit('showNotification', [
                'type' => 'success',
                'message' => trans('db_oper_result.delete_ok')
            ]);

            $this->emit('cuentaxpagarDeleted');
        } else {
            $this->emit('showNotification', [
                'type' => 'error',
                'message' => 'No se puede eliminar esta cuenta por pagar'
            ]);
        }
    }

    public function confirmDelete($id)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);

        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',
            'message' => '¿Está seguro?',
            'text' => "Esta acción eliminará la cuenta por pagar: {$cuentaxpagar->name}. ¡Esta acción no se puede deshacer!",
            'id' => $id
        ]);
    }

    public function toggleStatusBad($id)
    {
        try {
            $cuentaxpagar = Cuentaxpagar::findOrFail($id);
            $newStatus = $cuentaxpagar->status_bad === 'true' ? 'false' : 'true';
            $cuentaxpagar->update(['status_bad' => $newStatus]);

            $this->emit('showNotification', [
                'type' => 'success',
                'message' => 'Estado de cuenta incobrable actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            $this->emit('showNotification', [
                'type' => 'error',
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ]);
        }
    }
}