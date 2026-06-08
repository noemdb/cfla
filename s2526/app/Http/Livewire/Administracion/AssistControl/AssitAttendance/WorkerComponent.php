<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitAttendance;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\sys\Cargo;
use App\Models\sys\Profile;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;

class WorkerComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    // Propiedades para datos del usuario
    public $user_id,$ident,$work_id,$worker_order,$number_id,$firstname,$lastname,$cargo_id,$assit_schedule_id;
    public $worker_fullname,$worker_number_id,$worker_firstname,$worker_lastname,$updated;
    
    // Nuevas propiedades
    public $card_id,$status_schedule;
    
    // Propiedades de modo y listas
    public $editWorkerModeAssitSchedules;
    public $list_comment_user,$list_cargos,$list_assit_schedule;

    // Propiedades para búsqueda y filtros
    public $search = '';
    public $perPage = 10;

    protected function rules()
    {
        return [
            //'worker_order' => 'required|integer|min:1',
            'number_id' => 'unique:users,number_id,'.$this->user_id.'|integer|nullable',
            'firstname' => 'nullable|min:3|max:64',
            'lastname' => 'nullable|min:3|max:64',
            'cargo_id' => 'nullable|integer|nullable',
            'assit_schedule_id' => 'nullable|integer|nullable',
            'work_id' => 'nullable|max:10|unique:users,work_id,'.$this->user_id,
            'ident' => 'nullable|max:10|unique:users,ident,'.$this->user_id,
            'card_id' => 'nullable|max:10|unique:users,card_id,'.$this->user_id,
            'status_schedule' => 'nullable|boolean',
        ];
    }

    protected $messages = [
        'number_id.unique' => 'El número de cédula ingresado ya esta en uso, por favor indique otro.',
        'work_id.unique' => 'El Work ID ingresado ya esta en uso, por favor indique otro.',
        'ident.unique' => 'La identificación ingresada ya esta en uso, por favor indique otra.',
        'card_id.unique' => 'El Card ID ingresado ya esta en uso, por favor indique otro.',
    ];

    protected $validationAttributes = [
        'worker_order' => 'Orden del trabajador',
        'number_id' => 'Cédula de Identidad',
        'firstname' => 'Nombres',
        'lastname' => 'Apellidos',
        'cargo_id' => 'Cargo',
        'assit_schedule_id' => 'Horario',
        'work_id' => 'Work ID',
        'ident' => 'Identificación',
        'card_id' => 'Card ID',
        'status_schedule' => 'Estado Horario',
    ];

    public function mount()
    {
        $this->list_comment_user = User::COLUMN_COMMENTS;
        $this->list_cargos = Cargo::list_cargos();
        $this->list_assit_schedule = AssitSchedule::list_assit_schedule();
        $this->updated = false;
        
        // Inicializar nuevas propiedades
        $this->work_id = null;
        $this->ident = null;
        $this->card_id = null;
        $this->status_schedule = null;
    }

    public function render()
    {
        $workers = $this->getWorkers();
        return view('livewire.administracion.assist-control.assit-attendance.worker-component', compact('workers'));
    }

    private function getWorkers()
    {
        return User::query()
            ->select('users.*')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')

            ->where('users.is_active','enable')
            ->where('rols.group','imployeds')
            ->where('rols.status_schedule', true)

            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('profiles.firstname', 'like', '%' . $this->search . '%')
                      ->orWhere('profiles.lastname', 'like', '%' . $this->search . '%')
                      ->orWhere('users.number_id', 'like', '%' . $this->search . '%')
                      ->orWhere('users.ident', 'like', '%' . $this->search . '%')
                      ->orWhere('users.work_id', 'like', '%' . $this->search . '%');
                });
            })

            ->orderBy('users.username', 'asc')
            ->groupBy('users.id')
            ->paginate($this->perPage);
    }

    public function editWorker($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->user_id = $user->id;
            $this->ident = $user->ident;
            $this->work_id = $user->work_id;
            $this->worker_order = $user->worker_order;
            $this->number_id = $user->number_id;
            $this->card_id = $user->card_id;

            $this->firstname = $user->firstname;
            $this->lastname = $user->lastname;

            $this->worker_fullname = $user->fullname;
            $this->worker_number_id = $user->number_id;

            $this->cargo_id = $user->cargo_id;
            $this->assit_schedule_id = $user->assit_schedule_id;
            
            // Obtener el status_schedule del rol actual
            $this->status_schedule = $this->getCurrentStatusSchedule($user);

            $this->editWorkerModeAssitSchedules = true;
            $this->updated = false;
        }
    }

    public function updateWorker()
    {
        $this->validate();

        if ($this->user_id) {
            $user = User::findOrFail($this->user_id);
            
            // Actualizar datos del usuario
            $userArr = [ 
                'worker_order' => $this->worker_order, 
                'number_id' => $this->number_id,
                'work_id' => $this->work_id,
                'ident' => $this->ident,
                'card_id' => $this->card_id
            ];
            $user->update($userArr);

            // Actualizar perfil
            $profileArr = [ 
                'firstname' => $this->firstname, 
                'lastname' => $this->lastname
            ];
            $profile = $user->profile;
            if ($profile) {
                $profile->update($profileArr);
            }

            // Obtener el rol actual antes de actualizar
            $rol = $user->full_rol;
            
            // Actualizar rol si existe
            if ($rol) {
                $rolArr = [ 
                    'cargo_id' => $this->cargo_id, 
                    'assit_schedule_id' => $this->assit_schedule_id
                ];
                
                // Solo agregar status_schedule si no es nulo
                if (!is_null($this->status_schedule)) {
                    $rolArr['status_schedule'] = $this->status_schedule;
                }
                
                $rol->update($rolArr);
            }

            $this->editWorkerModeAssitSchedules = true;
            $this->updated = true;
            session()->flash('operp_ok', 'Guardado!!!.');
        }
    }

    public function closeEditMode()
    {
        $this->editWorkerModeAssitSchedules = false;
        $this->updated = false;
        
        // Limpiar propiedades al cerrar
        $this->reset([
            'work_id', 'ident', 'card_id', 'status_schedule'
        ]);
    }

    /**
     * Obtiene el status_schedule actual del usuario
     */
    private function getCurrentStatusSchedule($user)
    {
        $rol = $user->full_rol;
        if ($rol) {
            return $rol->status_schedule;
        }
        
        // Buscar en los roles del usuario
        $currentRol = $user->rols()
            ->where('status_schedule', true)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $currentRol ? $currentRol->status_schedule : null;
    }

    /**
     * Resetear propiedades cuando se actualiza el usuario
     */
    public function updatingUserId()
    {
        $this->resetValidation();
        $this->reset([
            'work_id', 'ident', 'card_id', 'status_schedule',
            'firstname', 'lastname', 'number_id', 'worker_order',
            'cargo_id', 'assit_schedule_id'
        ]);
    }

    /**
     * Resetear paginación cuando se realiza una búsqueda
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Resetear paginación cuando cambia el número de items por página
     */
    public function updatingPerPage()
    {
        $this->resetPage();
    }
}