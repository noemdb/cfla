<?php
// app/Http/Livewire/Administracion/Users/IndexComponent.php

namespace App\Http\Livewire\Administracion\Users;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\sys\Cargo;
use App\Models\sys\Profile;
use App\Models\sys\Rol;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IndexComponent extends Component
{
    use WithPagination;
    use UserTrait;

    protected $paginationTheme = 'bootstrap-4';
    public $paginate, $search = null;

    protected $listeners = [
        'showSwal',
        'alertConfirm',
        'alertQuestion',
        'remove',
        'edit',
        'preview',
        'show'
    ];

    public User $user;
    public Profile $profile;
    public Rol $rol;

    public $rols;
    public $user_id, $profile_id, $rol_id;
    public $list_status, $list_comment, $list_comment_profile, $list_comment_rol, $list_area, $list_rol, $list_cargos, $list_rols_group, $list_assit_schedule;
    public $modeIndex, $modeCreate, $modeEdit, $modePreview, $modeShow, $modeView, $modeFilter;
    public $modeProfile, $modeProfileEdit, $modeRol, $modeRolEdit;

    // Propiedades para creación
    public $is_active = '';
    public $password, $password_confirmation;

    // Agregar los siguientes métodos al componente
    public function applyFilters()
    {
        $this->resetPage(); // Resetear a la primera página al aplicar filtros
        $this->render();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->is_active = '';
        $this->resetPage();
        $this->render();
    }

    public function mount()
    {
        $this->list_comment = User::COLUMN_COMMENTS;
        $this->list_comment_profile = Profile::COLUMN_COMMENTS;
        $this->list_comment_rol = Rol::COLUMN_COMMENTS;
        $this->list_area = Rol::list_area();
        $this->list_rol = Rol::list_rol();
        $this->list_cargos = Cargo::list_cargos();
        $this->list_status = ['enable' => 'Activo', 'disable' => 'Inactivo'];
        $this->list_rols_group = Rol::list_rols_group();
        $this->list_assit_schedule = AssitSchedule::list_assit_schedule();
        $this->close();
        $this->initializeModels();
    }

    protected function initializeModels()
    {
        $this->user = new User();
        $this->profile = new Profile();
        $this->rol = new Rol();
    }

    public function render()
    {
        $search = $this->search;
        $users = User::select('users.*')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('users.username', '<>', 'admin');

        if (!empty($this->is_active)) {
            $users = $users->where('users.is_active', $this->is_active);
        } else {
            $users = $users->where('users.is_active', 'enable');
        }

        $users = (!empty($search)) ? $users->where(
            function ($query) use ($search) {
                $query->orWhere('users.username', 'like', '%' . $search . '%')
                    ->orWhere('profiles.firstname', 'like', '%' . $search . '%')
                    ->orWhere('profiles.lastname', 'like', '%' . $search . '%');
            }
        ) : $users;

        $users = $users->paginate($this->paginate);

        return view('livewire.administracion.users.index-component', [
            'users' => $users,
        ]);
    }

    // Método para abrir modal de creación
    public function create()
    {
        $this->close();
        $this->clearAllValidation();
        $this->initializeModels();
        $this->password = '';
        $this->password_confirmation = '';
        $this->modeCreate = true;
    }

    // Método para guardar nuevo usuario con perfil y rol
    public function store()
    {
        $this->validate([
            'user.username' => 'required|string|min:3|unique:users,username',
            'user.email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'user.is_active' => 'required|in:enable,disable',
            'user.is_diagnostic' => 'nullable|boolean',

            // Nuevos campos de identificación
            'user.card_id' => 'nullable|string|unique:users,card_id',
            'user.work_id' => 'nullable|integer|unique:users,work_id',
            'user.ident' => 'nullable|string|unique:users,ident',

            'profile.firstname' => 'required|string|min:2',
            'profile.lastname' => 'required|string|min:2',
            'profile.card_number' => 'nullable|string',

            'rol.area' => 'required|string',
            'rol.rol' => 'required|string',
            'rol.cargo_id' => 'nullable|exists:cargos,id',
            'rol.assit_schedule_id' => 'nullable|exists:assit_schedules,id',
            'rol.finicial' => 'required|date',
            'rol.ffinal' => 'required|date|after_or_equal:rol.finicial',
            'rol.group' => 'nullable|string',
            'rol.status_schedule' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () {
                // Crear usuario
                $user = User::create([
                    'username' => $this->user->username,
                    'email' => $this->user->email,
                    'password' => $this->password,
                    'is_active' => $this->user->is_active,
                    'work_id' => $this->user->work_id,
                    'card_id' => $this->user->card_id,
                    'ident' => $this->user->ident,
                    'number_id' => $this->user->number_id,
                    'is_diagnostic' => $this->user->is_diagnostic ?? false,
                ]);

                // Crear perfil
                $profile = Profile::create([
                    'user_id' => $user->id,
                    'firstname' => $this->profile->firstname,
                    'lastname' => $this->profile->lastname,
                    'card_number' => $this->profile->card_number,
                ]);

                // Crear rol
                $rol = Rol::create([
                    'user_id' => $user->id,
                    'area' => $this->rol->area,
                    'rol' => $this->rol->rol,
                    'cargo_id' => $this->rol->cargo_id,
                    'assit_schedule_id' => $this->rol->assit_schedule_id,
                    'finicial' => $this->rol->finicial,
                    'ffinal' => $this->rol->ffinal,
                    'group' => $this->rol->group,
                    'status_schedule' => $this->rol->status_schedule ?? true,
                    'descripcion' => "Rol asignado automáticamente al crear usuario",
                ]);

                $this->user_id = $user->id;
            });

            $this->close();
            $this->clearAllValidation();
            $this->showSwal('¡Usuario creado exitosamente!', 'El usuario ha sido registrado con perfil y rol asignados.', 'success');
        } catch (\Exception $e) {
            $this->showSwal('Error al crear usuario', 'Ha ocurrido un error: ' . $e->getMessage(), 'error');
        }
    }

    // Métodos para limpiar validación - CORREGIDOS
    public function clearAllValidation()
    {
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function clearUserValidation()
    {
        $this->resetValidation(['user.*', 'password']);
        $this->resetErrorBag(['user.*', 'password']);
    }

    public function clearProfileValidation()
    {
        $this->resetValidation(['profile.*']);
        $this->resetErrorBag(['profile.*']);
    }

    public function clearRolValidation()
    {
        $this->resetValidation(['rol.*']);
        $this->resetErrorBag(['rol.*']);
    }

    public function setModeRol($id)
    {
        $this->close();
        $this->clearAllValidation();
        $user = User::findOrFail($id);
        $this->user = $user;
        $this->rols = $user->rols;
        $this->user_id = $user->id;
        $this->modeRol = true;
    }

    public function editRol($id)
    {
        $this->clearRolValidation();
        $rol = Rol::findOrFail($id);
        $this->rol = $rol;
        $this->rol_id = $rol->id;
        $this->modeRolEdit = true;
    }

    public function saveRol()
    {
        $this->validate([
            'rol.area' => 'required|string',
            'rol.rol' => 'required|string',
            'rol.finicial' => 'required|date',
            'rol.ffinal' => 'required|date',
        ]);

        $this->rol->save();
        $this->closeRolEdit();
        $this->clearRolValidation();

        $user = User::findOrFail($this->user_id);
        $this->rols = $user->rols;

        $this->showSwal('¡Excelente, buen trabajo!', 'Rol actualizado exitosamente');
    }

    public function setModeProfile($id)
    {
        $this->close();
        $this->clearAllValidation();
        $user = User::findOrFail($id);
        $this->user = $user;
        $this->profile = $user->profile;
        $this->user_id = $user->id;
        $this->modeProfile = true;
    }

    public function editProfile($id)
    {
        $this->clearProfileValidation();
        $profile = Profile::findOrFail($id);
        $this->profile = $profile;
        $this->profile_id = $profile->id;
        $this->modeProfileEdit = true;
    }

    public function saveProfile()
    {
        $this->validate([
            'profile.firstname' => 'required|string',
            'profile.lastname' => 'required|string',
        ]);

        $this->profile->save();
        $this->closeProfileEdit();
        $this->clearProfileValidation();
        $this->showSwal('¡Excelente, buen trabajo!', 'Perfil actualizado exitosamente');
    }

    public function edit($id)
    {
        $this->close();
        $this->clearUserValidation();
        $user = User::findOrFail($id);
        $this->user = $user;
        $this->user_id = $user->id;
        $this->modeEdit = true;
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function save()
    {
        $this->validate([
            'user.username' => 'required|string',
            'user.password' => 'nullable',
            'user.email' => 'required|email|unique:users,email,' . $this->user->id,
            'user.is_active' => 'nullable',
            'user.status_update' => 'nullable',
            'user.work_id' => 'nullable|unique:users,work_id,' . $this->user->id,
            'user.card_id' => 'nullable|unique:users,card_id,' . $this->user->id,
            'user.ident' => 'nullable|unique:users,ident,' . $this->user->id,
            'user.number_id' => 'nullable|unique:users,number_id,' . $this->user->id,
            'user.is_diagnostic' => 'nullable|boolean',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if (!empty($this->password)) {
            $this->user->password = $this->password;
        }

        $this->user->save();

        if ($this->user->IsRepresentant()) {
            $representant = $this->user->representant;
            if ($representant) {
                $representant->email = $this->user->email;
                $representant->save();
            }
        }

        if ($this->user->IsProfesor()) {
            $profesor = $this->user->profesor;
            if ($profesor) {
                $profesor->email = $this->user->email;
                $profesor->save();
            }
        }

        $this->close();
        //$this->search = null;
        $this->clearUserValidation();
        $this->showSwal('¡Excelente, buen trabajo!', 'Usuario actualizado exitosamente');
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeEdit = false;
        $this->modeCreate = false;
        $this->modePreview = false;
        $this->modeView = false;
        $this->modeShow = false;
        $this->modeFilter = false;
        $this->modeProfile = false;
        $this->modeProfileEdit = false;
        $this->modeRol = false;
        $this->modeRolEdit = false;

        $this->clearAllValidation();
    }

    public function closeProfileEdit()
    {
        $this->modeProfileEdit = false;
        $this->clearProfileValidation();
    }

    public function closeRolEdit()
    {
        $this->modeRolEdit = false;
        $this->clearRolValidation();
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
}
