<?php

namespace App\Http\Livewire\Administracion\Pase;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Permission\Pase;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Lapso;

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
    public $paginate = '';
    public $selectedPestudio = '';
    public $selectedStatus = '';
    public $perPage = 10;

    // Variables para modales y formularios
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingPase = null;
    public $showResumenModal = false;

    // Campos del formulario
    public $estudiant_id;
    public $profesor_id;
    public $pensum_id;
    public $pestudio_id;
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
    public $list_pestudio = [];
    public $list_profesor = [];
    public $list_pensum = [];
    public $list_type = [];
    public $list_motive = [];
    public $list_status = [];
    public $list_grado = [];
    public $list_seccion = [];
    public $list_lapso = [];    

    public $grado_id = '';
    public $seccion_id = '';

    public $search_estudiant = '';

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
        $this->list_pestudio = Pestudio::list_pestudio();
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');

        $this->list_grado = [];
        $this->list_seccion = [];

        $this->list_comment = Pase::COLUMN_COMMENTS;
        $this->loadSelectLists();
        $this->resetForm();
    }

    public function updatedSelectedPestudio($value)
    {
        $this->resetPage();

        if ($this->selectedPestudio) {
            $this->list_grado = Grado::list_pestudio_grado($value);
        } else {
            $this->list_grado = [];
        }

        // Reset dependientes
        $this->grado_id = '';
        $this->seccion_id = '';
        $this->list_seccion = [];
    }

    public function updatedGradoId()
    {
        $this->resetPage();

        if ($this->grado_id) {
            // Cargar secciones del grado seleccionado
            $this->list_seccion = Seccion::list_seccion_grado($this->grado_id);
        } else {
            $this->list_seccion = [];
        }

        // Reset dependiente
        $this->seccion_id = '';
    }

    

    public function updatedSearchEstudiant()
    {
        $this->loadSelectLists();
    }

    private function loadSelectLists()
    {
        // Cargar listas asegurando que sean arrays
        $list_estudiants_raw = Estudiant::list_pestudio_grado();

        if ($this->search_estudiant) {
            $term = strtolower($this->search_estudiant);

            // Filtrar estructura anidada: Pestudio -> Grado -> Estudiantes
            $list_estudiants_raw = $list_estudiants_raw->map(function ($grados) use ($term) {
                // $grados es una colección de grados
                return $grados->map(function ($estudiantes) use ($term) {
                    // $estudiantes es una colección o array de usuarios [id => nombre]
                    return collect($estudiantes)->filter(function ($name) use ($term) {
                        return strpos(strtolower($name), $term) !== false;
                    });
                })->filter(function ($estudiantes) {
                    return $estudiantes->isNotEmpty();
                });
            })->filter(function ($grados) {
                return $grados->isNotEmpty();
            });
        }

        $this->list_estudiants = $this->ensureArray($list_estudiants_raw);
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
        // Filtros principales
        $this->search = '';
        $this->selectedPestudio = '';
        $this->selectedStatus = '';

        // Filtros dependientes
        $this->profesor_id = '';
        $this->grado_id = '';
        $this->seccion_id = '';

        // Reset de selects dependientes del Plan de Estudio
        $this->list_grado = [];
        $this->list_seccion = [];

        // Paginación
        $this->perPage = 10;

        // Reinicio de página actual
        $this->resetPage();
    }

    public function getPases()
    {
        // Eager loading profundo para evitar N+1
        $pases = Pase::with([
            'estudiant.inscripcion.seccion.grado',
            'profesor',
            'pensum.grado.pestudio',
        ])->get();

        // FILTROS SOBRE LA COLECCIÓN
        $filteredPases = $pases->filter(function ($pase) {

            $matchesSearch    = true;
            $matchesPestudio  = true;
            $matchesStatus    = true;
            $matchesGrado     = true;
            $matchesSeccion   = true;
            $matchesProfesor  = true;

            /* --------------------------------------------------
             * 🔍 1) Filtro de búsqueda global
             * -------------------------------------------------- */
            if ($this->search) {
                $searchTerm    = strtolower($this->search);
                $matchesSearch = false;

                // Estudiante
                if ($pase->estudiant) {
                    if (
                        str_contains(strtolower($pase->estudiant->name), $searchTerm) ||
                        str_contains(strtolower($pase->estudiant->lastname), $searchTerm) ||
                        str_contains(strtolower($pase->estudiant->ci), $searchTerm)
                    ) {
                        $matchesSearch = true;
                    }
                }

                // Profesor
                if (!$matchesSearch && $pase->profesor) {
                    if (
                        str_contains(strtolower($pase->profesor->name), $searchTerm) ||
                        str_contains(strtolower($pase->profesor->lastname), $searchTerm)
                    ) {
                        $matchesSearch = true;
                    }
                }

                // Campos del Pase
                if (!$matchesSearch) {
                    if (
                        str_contains(strtolower((string)$pase->description), $searchTerm) ||
                        str_contains(strtolower((string)$pase->destination), $searchTerm) ||
                        str_contains(strtolower((string)$pase->type), $searchTerm) ||
                        str_contains(strtolower((string)$pase->motive), $searchTerm)
                    ) {
                        $matchesSearch = true;
                    }
                }
            }

            /* --------------------------------------------------
             * 🎓 2) Filtro por Plan de Estudio
             * -------------------------------------------------- */
            if ($this->selectedPestudio) {
                $matchesPestudio = false;

                if (
                    $pase->pensum &&
                    $pase->pensum->grado &&
                    $pase->pensum->grado->pestudio
                ) {
                    $matchesPestudio =
                        $pase->pensum->grado->pestudio->id == $this->selectedPestudio;
                }
            }

            /* --------------------------------------------------
             * 🧑‍🏫 3) Filtro por Profesor
             * -------------------------------------------------- */
            if ($this->profesor_id) {
                $matchesProfesor = false;

                if ($pase->profesor) {
                    $matchesProfesor = $pase->profesor->id == $this->profesor_id;
                }
            }

            /* --------------------------------------------------
             * 🧷 4) Filtro por Grado (desde inscripcion)
             * -------------------------------------------------- */
            if ($this->grado_id) {
                $matchesGrado = false;

                if (
                    $pase->estudiant &&
                    $pase->estudiant->inscripcion &&
                    $pase->estudiant->inscripcion->seccion &&
                    $pase->estudiant->inscripcion->seccion->grado
                ) {
                    $matchesGrado =
                        $pase->estudiant->inscripcion->seccion->grado->id == $this->grado_id;
                }
            }

            /* --------------------------------------------------
             * 🧷 5) Filtro por Sección
             * -------------------------------------------------- */
            if ($this->seccion_id) {
                $matchesSeccion = false;

                if (
                    $pase->estudiant &&
                    $pase->estudiant->inscripcion &&
                    $pase->estudiant->inscripcion->seccion
                ) {
                    $matchesSeccion =
                        $pase->estudiant->inscripcion->seccion->id == $this->seccion_id;
                }
            }

            /* --------------------------------------------------
             * 🏷️ 6) Filtro por Estado
             * -------------------------------------------------- */
            if ($this->selectedStatus) {
                $matchesStatus = ($pase->status == $this->selectedStatus);
            }

            /* --------------------------------------------------
             * ✔️ 7) Resultado final del cierre (todos los filtros AND)
             * -------------------------------------------------- */
            return $matchesSearch
                && $matchesPestudio
                && $matchesStatus
                && $matchesGrado
                && $matchesSeccion
                && $matchesProfesor;
        });

        /* ------------------------------------------------------
         * 📌 Ordenamiento por fecha reciente
         * ------------------------------------------------------ */
        $sortedPases = $filteredPases->sortByDesc(function ($pase) {
            return $pase->date_time ?? $pase->created_at;
        });

        /* ------------------------------------------------------
         * 📌 Paginación manual
         * ------------------------------------------------------ */
        $page    = $this->page ?: 1;
        $perPage = $this->perPage;
        $sliced  = $sortedPases->slice(($page - 1) * $perPage, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
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
        return view('livewire.administracion.pase.index-component', [
            'pases' => $this->getPases(),
            'resumenEstudiantes' => $this->getResumenPasesPorEstudiante(),
            'statusOptions' => $this->list_status,
            'typeOptions' => $this->list_type,
            'motiveOptions' => $this->list_motive
        ]);
    }

    public function updatingGradoId()
    {
        $this->resetPage();
    }

    public function updatingSeccionId()
    {
        $this->resetPage();
    }

    public function openResumenModal()
    {
        $this->showResumenModal = true;
    }

    public function closeResumenModal()
    {
        $this->showResumenModal = false;
    }

    public function getResumenPasesPorEstudiante()
    {
        $query = \DB::table('estudiants')
            ->leftjoin('pases', 'estudiants.id', '=', 'pases.estudiant_id')
            ->leftjoin('profesors', 'profesors.id', '=', 'pases.profesor_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('grados', 'seccions.grado_id', '=', 'grados.id')
            ->join('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')

            ->select(
                'estudiants.id as estudiant_id',
                \DB::raw("CONCAT(estudiants.lastname, ' ', estudiants.name) as estudiante"),
                'pestudios.name as pestudio',
                'grados.name as grado',
                'seccions.name as seccion',

                // 👇 TOTAL precalculado
                'estudiants.count_passes as total_pases'
            )
            ->whereNotNull('estudiants.count_passes')
            ;

        /* ==============================
           Filtros dinámicos
        ============================== */

        if ($this->selectedPestudio) {
            $query->where('pestudios.id', $this->selectedPestudio);
        }

        if ($this->grado_id) {
            $query->where('grados.id', $this->grado_id);
        }

        if ($this->seccion_id) {
            $query->where('seccions.id', $this->seccion_id);
        }

        if ($this->profesor_id) {
            $query->where('profesors.id', $this->profesor_id);
        }

        if ($this->search) {
            $term = '%' . strtolower($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(estudiants.name) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(estudiants.lastname) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(estudiants.ci) LIKE ?', [$term]);
            });
        }

        return $query
            ->orderBy('estudiante')
            ->get()
            // ->paginate($this->perPage)
            ;
    }


}
