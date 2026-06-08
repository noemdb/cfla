<?php

namespace App\Http\Livewire\Evaluacion\Pase;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Permission\Pase;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';

    public $pestudios;
    public $manager_id;
    public $list_comment;
    public $search = '';
    public $selectedPestudio = '';
    public $selectedStatus = '';
    public $perPage = 10;

    // Variables para modales y formularios
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingPase = null;

    // Campos del formulario
    public $estudiant_id;
    public $profesor_id;
    public $pensum_id;
    public $type;
    public $motive;
    public $description;
    public $destination;
    public $date;
    public $time;
    public $require_auhtorize_guardian = '';
    public $require_auhtorize_teacher = '';
    public $require_auhtorize_manager = '';
    public $status;
    public $status_emergency = '';

    // Listas para selects
    public $list_estudiants = [];
    public $list_profesor = [];
    public $list_pensum = [];
    public $list_type = [];
    public $list_motive = [];
    public $list_status = [];

    // En protected $listeners, mantener solo esto:
    protected $listeners = [
        'paseUpdated' => '$refresh',
        'remove' => 'destroy',
        'sendNotification' => 'send',
        'alertConfirm' => 'remove',
        'alertQuestion' => 'executeAction',
        'changeStatusDirect' => 'changeStatusDirect', // Nuevo listener específico
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedPestudio' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'perPage' => ['except' => 10]
    ];

    protected $rules = [
        'estudiant_id' => 'required|exists:estudiants,id',
        'profesor_id' => 'required|exists:profesors,id',
        'pensum_id' => 'required|exists:pensums,id',
        'type' => 'required|string',
        'motive' => 'required|string',
        'description' => 'nullable|string|max:500',
        'destination' => 'required|string|max:255',
        'date' => 'required|date',
        'time' => 'required',
        'require_auhtorize_guardian' => 'required|in:0,1',
        'require_auhtorize_teacher' => 'required|in:0,1',
        'require_auhtorize_manager' => 'required|in:0,1',
        'status' => 'required|string',
        'status_emergency' => 'required|in:0,1',
    ];

    protected $messages = [
        'estudiant_id.required' => 'El estudiante es obligatorio.',
        'profesor_id.required' => 'El profesor es obligatorio.',
        'pensum_id.required' => 'El pensum es obligatorio.',
        'type.required' => 'El tipo de pase es obligatorio.',
        'motive.required' => 'El motivo es obligatorio.',
        'destination.required' => 'El destino es obligatorio.',
        'date.required' => 'La fecha es obligatoria.',
        'time.required' => 'La hora es obligatoria.',
        'require_auhtorize_guardian.required' => 'La autorización del guardián es obligatoria.',
        'require_auhtorize_teacher.required' => 'La autorización del profesor es obligatoria.',
        'require_auhtorize_manager.required' => 'La autorización del coordinador es obligatoria.',
        'status.required' => 'El estado es obligatorio.',
        'status_emergency.required' => 'El estado de emergencia es obligatorio.',
    ];

    public $showStatusModal = false;
    public $paseForStatus = null;
    public $new_status;

    public function mount()
    {
        $user = User::find(Auth::id());
        $this->manager_id = $user->id;
        $user_id = $user->id;

        $this->pestudios = Pestudio::select('pestudios.*')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where(
                function ($query) use ($user_id) {
                    $query->orWhere('peducativos.manager_id', $user_id)
                        ->orWhere('peducativos.assistant_id', $user_id)
                        ->orWhere('peducativos.deputy_id', $user_id);
                }
            )
            ->orderBy('pestudios.order', 'asc')
            ->where('pestudios.status_active', 'true')
            ->get();

        $this->list_comment = Pase::COLUMN_COMMENTS;
        $this->loadSelectLists();
        $this->resetForm();
    }

    private function loadSelectLists()
    {
        // Cargar listas asegurando que sean arrays
        $this->list_estudiants = $this->ensureArray(Estudiant::list_pestudio_grado());
        $this->list_profesor = $this->ensureArray(Profesor::list_profesors());
        $this->list_pensum = $this->ensureArray(Pensum::list_pestudio_pensum());
        $this->list_type = $this->ensureArray(Pase::list_type());
        $this->list_motive = $this->ensureArray(Pase::list_motive());
        $this->list_status = $this->ensureArray(Pase::list_status());
    }

    private function ensureArray($data)
    {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof \Illuminate\Support\Collection) {
            return $data->toArray();
        }

        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        return [];
    }

    private function resetForm()
    {
        $this->reset([
            'estudiant_id',
            'profesor_id',
            'pensum_id',
            'type',
            'motive',
            'description',
            'destination',
            'date',
            'time',
            'require_auhtorize_guardian',
            'require_auhtorize_teacher',
            'require_auhtorize_manager',
            'status',
            'status_emergency',
            'editingPase'
        ]);

        // Valores por defecto
        $this->require_auhtorize_guardian = '';
        $this->require_auhtorize_teacher = '';
        $this->require_auhtorize_manager = '';
        $this->status_emergency = '';
        $this->date = now()->format('Y-m-d');
        $this->time = now()->format('H:i');

        $this->resetErrorBag();
    }

    // Métodos para modales
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($paseId)
    {
        $this->editingPase = Pase::findOrFail($paseId);

        if ($this->editingPase->status_notifications) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No se puede editar',
                'text' => 'Este pase ya ha sido notificado y no puede ser editado.',
                'icon' => 'warning',
                'timer' => 3000
            ]);
            return;
        }

        // Cargar datos del pase en el formulario
        $this->estudiant_id = $this->editingPase->estudiant_id;
        $this->profesor_id = $this->editingPase->profesor_id;
        $this->pensum_id = $this->editingPase->pensum_id;
        $this->type = $this->editingPase->type;
        $this->motive = $this->editingPase->motive;
        $this->description = $this->editingPase->description;
        $this->destination = $this->editingPase->destination;
        $this->date = $this->editingPase->date;
        $this->time = $this->editingPase->time;
        $this->require_auhtorize_guardian = (string)$this->editingPase->require_auhtorize_guardian;
        $this->require_auhtorize_teacher = (string)$this->editingPase->require_auhtorize_teacher;
        $this->require_auhtorize_manager = (string)$this->editingPase->require_auhtorize_manager;
        $this->status = $this->editingPase->status;
        $this->status_emergency = (string)$this->editingPase->status_emergency;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    // Métodos CRUD
    public function create()
    {
        $this->validate();

        try {
            Pase::create([
                'user_id' => Auth::id(),
                'estudiant_id' => $this->estudiant_id,
                'profesor_id' => $this->profesor_id,
                'pensum_id' => $this->pensum_id,
                'type' => $this->type,
                'motive' => $this->motive,
                'description' => $this->description,
                'destination' => $this->destination,
                'date' => $this->date,
                'time' => $this->time,
                'require_auhtorize_guardian' => (bool)$this->require_auhtorize_guardian,
                'require_auhtorize_teacher' => (bool)$this->require_auhtorize_teacher,
                'require_auhtorize_manager' => (bool)$this->require_auhtorize_manager,
                'status' => $this->status,
                'status_emergency' => (bool)$this->status_emergency,
            ]);

            $this->closeCreateModal();

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Éxito!',
                'text' => 'Pase escolar creado correctamente.',
                'icon' => 'success',
                'timer' => 2000
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo crear el pase escolar: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000
            ]);
        }
    }

    public function update()
    {
        $this->validate();

        if (!$this->editingPase) {
            return;
        }

        // Verificar si el pase ya fue notificado
        if ($this->editingPase->status_notifications) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No se puede editar',
                'text' => 'Este pase ya ha sido notificado y no puede ser editado.',
                'icon' => 'warning',
                'timer' => 3000
            ]);
            return;
        }

        try {
            $this->editingPase->update([
                'estudiant_id' => $this->estudiant_id,
                'profesor_id' => $this->profesor_id,
                'pensum_id' => $this->pensum_id,
                'type' => $this->type,
                'motive' => $this->motive,
                'description' => $this->description,
                'destination' => $this->destination,
                'date' => $this->date,
                'time' => $this->time,
                'require_auhtorize_guardian' => (bool)$this->require_auhtorize_guardian,
                'require_auhtorize_teacher' => (bool)$this->require_auhtorize_teacher,
                'require_auhtorize_manager' => (bool)$this->require_auhtorize_manager,
                'status' => $this->status,
                'status_emergency' => (bool)$this->status_emergency,
            ]);

            $this->closeEditModal();

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Éxito!',
                'text' => 'Pase escolar actualizado correctamente.',
                'icon' => 'success',
                'timer' => 2000
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo actualizar el pase escolar: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000
            ]);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedPestudio()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedPestudio = '';
        $this->selectedStatus = '';
        $this->resetPage();
    }

    public function getPasesProperty()
    {
        // Obtener la colección base
        $pases = Pase::getPasesForManagerId($this->manager_id);

        // Aplicar filtros
        $filteredPases = $pases->filter(function ($pase) {
            $matchesSearch = true;
            $matchesPestudio = true;
            $matchesStatus = true;

            // Filtro de búsqueda
            if ($this->search) {
                $searchTerm = strtolower($this->search);
                $matchesSearch = false;

                // Buscar en estudiante
                if ($pase->estudiant) {
                    if (
                        str_contains(strtolower($pase->estudiant->name), $searchTerm) ||
                        str_contains(strtolower($pase->estudiant->lastname), $searchTerm) ||
                        str_contains(strtolower($pase->estudiant->ci), $searchTerm)
                    ) {
                        $matchesSearch = true;
                    }
                }

                // Buscar en profesor
                if (!$matchesSearch && $pase->profesor) {
                    if (
                        str_contains(strtolower($pase->profesor->name), $searchTerm) ||
                        str_contains(strtolower($pase->profesor->lastname), $searchTerm)
                    ) {
                        $matchesSearch = true;
                    }
                }

                // Buscar en campos directos del pase
                if (!$matchesSearch) {
                    if (
                        str_contains(strtolower($pase->description), $searchTerm) ||
                        str_contains(strtolower($pase->destination), $searchTerm) ||
                        str_contains(strtolower($pase->type), $searchTerm) ||
                        str_contains(strtolower($pase->motive), $searchTerm)
                    ) {
                        $matchesSearch = true;
                    }
                }
            }

            // Filtro de plan de estudio
            if ($this->selectedPestudio) {
                $matchesPestudio = false;
                // Buscar a través de las relaciones
                if ($pase->pensum && $pase->pensum->grado && $pase->pensum->grado->pestudio) {
                    $matchesPestudio = $pase->pensum->grado->pestudio->id == $this->selectedPestudio;
                }
            }

            // Filtro de estado
            if ($this->selectedStatus) {
                $matchesStatus = $pase->status == $this->selectedStatus;
            }

            return $matchesSearch && $matchesPestudio && $matchesStatus;
        });

        // Ordenar por fecha más reciente primero
        $sortedPases = $filteredPases->sortByDesc(function ($pase) {
            return $pase->date_time ?? $pase->created_at;
        });

        // Paginar manualmente
        $page = $this->page ?: 1;
        $perPage = $this->perPage;
        $sliced = $sortedPases->slice(($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator(
            $sliced->values(),
            $sortedPases->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    public function destroy($id)
    {
        try {
            $pase = Pase::findOrFail($id);

            if ($pase->status_notifications) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'No se puede eliminar',
                    'text' => 'Este pase ya ha sido notificado y no puede ser eliminado.',
                    'icon' => 'warning',
                    'timer' => 3000
                ]);
                return;
            }

            $pase->delete();

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Eliminado!',
                'text' => 'El pase escolar ha sido eliminado correctamente.',
                'icon' => 'success',
                'timer' => 2000
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo eliminar el pase escolar: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000
            ]);
        }
    }

    public function confirmDelete($id)
    {
        $pase = Pase::findOrFail($id);

        if ($pase->status_notifications) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No se puede eliminar',
                'text' => 'Este pase ya ha sido notificado y no puede ser eliminado.',
                'icon' => 'warning',
                'timer' => 3000
            ]);
            return;
        }

        $this->dispatchBrowserEvent('swal:confirm', [
            'id' => $id,
            'message' => '¿Estás seguro?',
            'text' => 'Esta acción eliminará permanentemente el pase escolar.',
            'type' => 'warning'
        ]);
    }

    public function send($id)
    {
        try {
            $pase = Pase::findOrFail($id);

            if ($pase->status_notifications) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Ya notificado',
                    'text' => 'Este pase ya ha sido notificado anteriormente.',
                    'icon' => 'info',
                    'timer' => 3000
                ]);
                return;
            }

            // Simular envío de notificación
            $pase->update(['status_notifications' => true]);

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Notificación enviada!',
                'text' => 'La notificación del pase ha sido enviada correctamente.',
                'icon' => 'success',
                'timer' => 2000
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo enviar la notificación: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000
            ]);
        }
    }

    public function confirmSend($id)
    {
        $pase = Pase::findOrFail($id);

        if ($pase->status_notifications) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Ya notificado',
                'text' => 'Este pase ya ha sido notificado anteriormente.',
                'icon' => 'info',
                'timer' => 3000
            ]);
            return;
        }

        $this->dispatchBrowserEvent('swal:question', [
            'id' => $id,
            'method' => 'sendNotification',
            'message' => '¿Enviar notificación?',
            'text' => 'Se enviará la notificación del pase escolar a los destinatarios correspondientes.',
            'type' => 'question'
        ]);
    }

    // Métodos para confirmaciones (igual que en tu proyecto)
    public function alertConfirm($id)
    {
        $pase = Pase::findOrFail($id);

        if ($pase->status_notifications) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No se puede eliminar',
                'text' => 'Este pase ya ha sido notificado y no puede ser eliminado.',
                'icon' => 'warning',
                'timer' => 3000
            ]);
            return;
        }

        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',
            'message' => '¿Estás seguro?',
            'text' => 'Esta acción eliminará permanentemente el pase escolar.',
            'id' => $id
        ]);
    }

    public function alertQuestion($id, $method)
    {
        $pase = Pase::findOrFail($id);

        if ($method === 'sendNotification') {
            if ($pase->status_notifications) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Ya notificado',
                    'text' => 'Este pase ya ha sido notificado anteriormente.',
                    'icon' => 'info',
                    'timer' => 3000
                ]);
                return;
            }

            $this->dispatchBrowserEvent('swal:question', [
                'type' => 'question',
                'message' => '¿Enviar notificación?',
                'text' => 'Se enviará la notificación del pase escolar a los destinatarios correspondientes.',
                'id' => $id,
                'method' => $method
            ]);
        }
    }

    public function executeAction($id, $method)
    {
        if ($method === 'sendNotification') {
            $this->send($id);
        }
    }

    // Método único para cambiar estado con confirmación
    public function confirmChangeStatus($paseId, $newStatus)
    {
        $pase = Pase::findOrFail($paseId);
        $statusName = $this->list_status[$newStatus] ?? $newStatus;
        $currentStatusName = $this->list_status[$pase->status] ?? $pase->status;

        $this->dispatchBrowserEvent('swal:question-status', [
            'type' => 'question',
            'message' => '¿Cambiar estado?',
            'text' => '¿Estás seguro de cambiar el estado del pase de <strong>' . $currentStatusName . '</strong> a <strong>' . $statusName . '</strong>?',
            'id' => $paseId,
            'newStatus' => $newStatus
        ]);
    }

    // Método único para cambiar estado
    public function changeStatusDirect($id, $newStatus)
    {
        try {
            $pase = Pase::findOrFail($id);

            if (!array_key_exists($newStatus, $this->list_status)) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Error',
                    'text' => 'El estado seleccionado no es válido.',
                    'icon' => 'error',
                    'timer' => 3000
                ]);
                return;
            }

            $oldStatus = $pase->status;
            $pase->update(['status' => $newStatus]);

            $this->dispatchBrowserEvent('swal', [
                'title' => 'Estado actualizado',
                'text' => 'Cambiado de ' . ($this->list_status[$oldStatus] ?? $oldStatus) . ' a ' . ($this->list_status[$newStatus] ?? $newStatus),
                'icon' => 'success',
                'timer' => 2000
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000
            ]);
        }
    }

    // Mantener los métodos existentes para el modal de estado
    public function openStatusModal($paseId)
    {
        $this->paseForStatus = Pase::findOrFail($paseId);
        $this->new_status = $this->paseForStatus->status;
        $this->showStatusModal = true;
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->paseForStatus = null;
        $this->new_status = '';
        $this->resetErrorBag();
    }

    public function updateStatus()
    {
        $this->validate([
            'new_status' => 'required|in:' . implode(',', array_keys($this->list_status)),
        ]);

        if (!$this->paseForStatus) {
            return;
        }

        try {
            $this->paseForStatus->update([
                'status' => $this->new_status,
            ]);

            $this->closeStatusModal();

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Éxito!',
                'text' => 'Estado del pase actualizado correctamente.',
                'icon' => 'success',
                'timer' => 2000
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000
            ]);
        }
    }

    public function render()
    {
        return view('livewire.evaluacion.pase.index-component', [
            'pases' => $this->pases,
            'statusOptions' => $this->list_status,
            'typeOptions' => $this->list_type,
            'motiveOptions' => $this->list_motive
        ]);
    }
}
