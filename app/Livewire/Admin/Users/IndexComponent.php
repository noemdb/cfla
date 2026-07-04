<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\sys\Profile;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    public $modeIndex = true;
    public $modeForm = false;

    public $isEditing = false;
    public $user_id;

    // ─── Form fields ───────────────────────────────────────────
    public $username;
    public $email;
    public $password;
    public $is_active = true;

    // Role flags
    public $is_admin = false;
    public $is_diagnostic = false;
    public $is_planner = false;
    public $is_profesor = false;

    // Profile fields
    public $firstname;
    public $lastname;
    public $card_number;

    // ─── Filters & sorting ─────────────────────────────────────
    public $search = '';
    public $filter_role = '';

    public $sortField = 'id';
    public $sortDirection = 'desc';

    // ─── Delete confirmation ───────────────────────────────────
    public $confirmDeleteId = null;

    protected function rules()
    {
        $rules = [
            'username' => 'required|string|max:150',
            'email'     => [
                'required',
                'email',
                'max:255',
                $this->isEditing ? "unique:users,email,{$this->user_id}" : 'unique:users,email',
            ],
            'password'  => $this->isEditing ? 'nullable|string|min:6' : 'required|string|min:6',
            'is_active' => 'required|boolean',
            'is_admin'  => 'boolean',
            'is_diagnostic' => 'boolean',
            'is_planner'    => 'boolean',
            'is_profesor'   => 'boolean',
            'firstname'     => 'nullable|string|max:100',
            'lastname'      => 'nullable|string|max:100',
            'card_number'   => 'nullable|string|max:20',
        ];

        return $rules;
    }

    protected $validationAttributes = [
        'username'  => 'nombre de usuario',
        'email'     => 'correo electrónico',
        'password'  => 'contraseña',
        'is_active' => 'activo',
        'is_admin'  => 'administrador',
        'is_diagnostic' => 'diagnóstico',
        'is_planner'    => 'planificador',
        'is_profesor'   => 'profesor',
        'firstname'     => 'nombre',
        'lastname'      => 'apellido',
        'card_number'   => 'cédula',
    ];

    public function render()
    {
        $query = User::query()->with('profile');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('username', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('number_id', 'like', "%{$this->search}%")
                  ->orWhereHas('profile', function ($p) {
                      $p->where('firstname', 'like', "%{$this->search}%")
                        ->orWhere('lastname', 'like', "%{$this->search}%");
                  });
            });
        }

        // Filter by role
        if ($this->filter_role === 'admin') {
            $query->where('is_admin', true);
        } elseif ($this->filter_role === 'diagnostic') {
            $query->where('is_diagnostic', true);
        } elseif ($this->filter_role === 'planner') {
            $query->where('is_planner', true);
        } elseif ($this->filter_role === 'profesor') {
            $query->where('is_profesor', true);
        } elseif ($this->filter_role === 'standard') {
            $query->where('is_admin', false)
                  ->where('is_diagnostic', false)
                  ->where('is_planner', false)
                  ->where('is_profesor', false);
        }

        if (in_array($this->sortField, ['id', 'username', 'email', 'is_active'])) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('id', $this->sortDirection);
        }

        $users = $query->paginate(10);

        $roleOptions = [
            ''           => 'Todos los roles',
            'admin'      => 'Administrador',
            'diagnostic' => 'Diagnóstico',
            'planner'    => 'Planificación',
            'profesor'   => 'Profesor',
            'standard'   => 'Usuario Estándar',
        ];

        return view('livewire.admin.users.index-component', [
            'users'        => $users,
            'roleOptions'  => $roleOptions,
        ]);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRole() { $this->resetPage(); }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->user_id = null;
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);

        $this->user_id      = $user->id;
        $this->username     = $user->username;
        $this->email        = $user->email;
        $this->is_active    = $user->is_active === 'enable' || $user->is_active === true;
        $this->is_admin     = (bool) $user->is_admin;
        $this->is_diagnostic = (bool) $user->is_diagnostic;
        $this->is_planner   = (bool) $user->is_planner;
        $this->is_profesor  = (bool) $user->is_profesor;

        if ($user->profile) {
            $this->firstname   = $user->profile->firstname;
            $this->lastname    = $user->profile->lastname;
            $this->card_number = $user->profile->card_number;
        }

        $this->isEditing = true;
        $this->modeForm = true;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $userData = [
                'username'      => $this->username,
                'email'         => $this->email,
                'is_admin'      => $this->is_admin ? 1 : 0,
                'is_diagnostic' => $this->is_diagnostic ? 1 : 0,
                'is_planner'    => $this->is_planner ? 1 : 0,
                'is_profesor'   => $this->is_profesor ? 1 : 0,
                'is_active'     => $this->is_active ? 'enable' : 'disable',
            ];

            if ($this->isEditing) {
                $user = User::findOrFail($this->user_id);
                $user->update($userData);

                if (!empty($this->password)) {
                    $user->update(['password' => bcrypt($this->password)]);
                }

                $this->notification()->success(
                    title: 'Usuario Actualizado',
                    description: "El usuario {$user->username} se actualizó correctamente."
                );
            } else {
                $userData['password'] = bcrypt($this->password);
                $user = User::create($userData);

                $this->notification()->success(
                    title: 'Usuario Creado',
                    description: "El usuario {$user->username} se creó correctamente."
                );
            }

            // Create or update profile
            if ($this->firstname || $this->lastname || $this->card_number) {
                Profile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'firstname'   => mb_strtoupper($this->firstname ?? ''),
                        'lastname'    => mb_strtoupper($this->lastname ?? ''),
                        'card_number' => $this->card_number,
                    ]
                );
            }

            DB::commit();
            $this->modeForm = false;
            $this->modeIndex = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error(
                title: 'Error',
                description: 'Ocurrió un error al guardar: ' . $e->getMessage()
            );
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmDeleteId = null;
    }

    public function destroy()
    {
        $user = User::findOrFail($this->confirmDeleteId);

        if ($user->id === auth()->id()) {
            $this->notification()->error(
                title: 'No puedes eliminarte',
                description: 'No puedes eliminar tu propio usuario.'
            );
            $this->cancelDelete();
            return;
        }

        $user->delete();

        $this->cancelDelete();

        $this->notification()->success(
            title: 'Usuario Eliminado',
            description: 'El usuario se eliminó correctamente.'
        );
    }

    public function resetForm()
    {
        $this->reset([
            'username', 'email', 'password',
            'is_admin', 'is_diagnostic', 'is_planner', 'is_profesor',
            'firstname', 'lastname', 'card_number',
        ]);
        $this->is_active = true;
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
