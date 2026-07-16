<?php

namespace App\Livewire\Planning\Profesor;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\sys\Profile;
use App\Models\sys\Rol;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    // Modal modes
    public $modeIndex = true;
    public $modeForm = false;

    // Wizard step
    public $wizardStep = 1;

    // Editing flag
    public $isEditing = false;
    public $profesor_id;

    // ─── Paso 1: Datos Personales ────────────────────────────────
    public $ci_profesor;
    public $ti_teacher = 'Titular';
    public $name;
    public $lastname;
    public $gender = 'M';
    public $date_birth;

    // ─── Paso 2: Contacto ────────────────────────────────────────
    public $email;
    public $phone;
    public $cellphone;
    public $whatsapp;
    public $gsemail;
    public $dir_address;

    // ─── Paso 3: Cuenta y Rol ────────────────────────────────────
    public $user_username;
    public $user_password;
    public $rol_finicial;
    public $rol_ffinal;
    public $status_active = true;

    // Select lists
    public $peducativos;
    public $lapsos;

    // Filters
    public $search = '';
    public $filter_peducativo = '';
    public $filter_pevaluacions = '';
    public $filter_activities = '';

    // Sorting
    public $sortField = 'profesors.id';
    public $sortDirection = 'asc';

    // Pagination
    public $paginate = 15;

    // Confirm delete
    public $confirmDeleteId = null;

    // Toggle active
    public $confirmToggleActiveId = null;
    public $toggleActiveProfesorId = null;
    public $toggleActiveName = '';
    public $toggleActiveCurrentStatus = false;

    // Preview
    public $previewMode = false;
    public $previewProfesorId = null;

    protected function rules()
    {
        $rules = [];

        if ($this->wizardStep === 1) {
            $rules = [
                'ci_profesor' => 'required|string|max:20',
                'name' => 'required|string|max:100',
                'lastname' => 'required|string|max:100',
                'gender' => 'required|in:M,F',
                'date_birth' => 'nullable|date',
                'ti_teacher' => 'nullable|string|max:50',
            ];
        } elseif ($this->wizardStep === 2) {
            $rules = [
                'email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:20',
                'cellphone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'gsemail' => 'nullable|email|max:150',
                'dir_address' => 'nullable|string|max:255',
            ];
        } else {
            $rules = [
                'user_username' => 'required|string|max:150',
                'user_password' => 'nullable|string|min:4',
                'rol_finicial' => 'nullable|date',
                'rol_ffinal' => 'nullable|date|after_or_equal:rol_finicial',
                'status_active' => 'required|boolean',
            ];
        }

        return $rules;
    }

    protected $validationAttributes = [
        'ci_profesor' => 'cédula',
        'name' => 'nombre',
        'lastname' => 'apellido',
        'gender' => 'género',
        'date_birth' => 'fecha de nacimiento',
        'ti_teacher' => 'tipo de facilitador',
        'email' => 'correo electrónico',
        'phone' => 'teléfono',
        'cellphone' => 'celular',
        'whatsapp' => 'WhatsApp',
        'gsemail' => 'correo GSuite',
        'dir_address' => 'dirección',
        'user_username' => 'nombre de usuario',
        'user_password' => 'contraseña',
        'rol_finicial' => 'fecha inicial del rol',
        'rol_ffinal' => 'fecha final del rol',
    ];

    public function mount()
    {
        $this->peducativos = Peducativo::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $this->lapsos = Lapso::orderBy('id')
            ->get();

        $this->close();
    }

    public function render()
    {
        $query = Profesor::where('status_active', 'true')
            ->with('user')
            ->withCount('pevaluacions')
            ->leftJoin('users', 'profesors.user_id', '=', 'users.id')
            ->addSelect([
                'activities_count' => Pevaluacion::selectRaw('COUNT(activities.id)')
                    ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
                    ->whereColumn('pevaluacions.profesor_id', 'profesors.id')
            ]);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('profesors.name', 'like', "%{$this->search}%")
                  ->orWhere('profesors.lastname', 'like', "%{$this->search}%")
                  ->orWhere('profesors.ci_profesor', 'like', "%{$this->search}%")
                  ->orWhere('profesors.email', 'like', "%{$this->search}%")
                  ->orWhere('users.username', 'like', "%{$this->search}%");
            });
        }

        // Filter by peducativo
        if ($this->filter_peducativo) {
            $query->whereHas('pevaluacions.pensum.pestudio', function ($q) {
                $q->where('peducativo_id', $this->filter_peducativo);
            });
        }

        // Filter by pevaluacions
        if ($this->filter_pevaluacions === 'SI') {
            $query->has('pevaluacions');
        } elseif ($this->filter_pevaluacions === 'NO') {
            $query->doesntHave('pevaluacions');
        }

        // Filter by activities
        if ($this->filter_activities === 'SI') {
            $query->having('activities_count', '>', 0);
        } elseif ($this->filter_activities === 'NO') {
            $query->having('activities_count', '=', 0);
        }

        // Profesores sin carga académica al final (sort primario)
        $query->orderByRaw('pevaluacions_count > 0 DESC');

        // Sorting
        if (in_array($this->sortField, ['profesors.id', 'profesors.name', 'profesors.lastname', 'profesors.ci_profesor', 'users.username'])) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } elseif ($this->sortField === 'pevaluacions_count') {
            $query->orderBy('pevaluacions_count', $this->sortDirection);
        } elseif ($this->sortField === 'activities_count') {
            $query->orderBy('activities_count', $this->sortDirection);
        } else {
            $query->orderBy('profesors.id', $this->sortDirection);
        }

        $profesors = $query->paginate($this->paginate);

        // Cargar pevaluacions en lote (1 query en vez de N)
        $ids = $profesors->pluck('id');
        $allPevaluacions = Pevaluacion::whereIn('profesor_id', $ids)->get();
        foreach ($profesors as $profesor) {
            $profesor->setRelation('pevaluacions',
                $allPevaluacions->where('profesor_id', $profesor->id)
            );
        }

        return view('livewire.planning.profesor.index-component', [
            'profesors' => $profesors,
            'previewProfesorId' => $this->previewProfesorId,
        ]);
    }

    // ─── FILTERS RESET PAGE ──────────────────────────────────────

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPeducativo() { $this->resetPage(); }
    public function updatingFilterPevaluacions() { $this->resetPage(); }
    public function updatingFilterActivities() { $this->resetPage(); }

    // ─── SORTING ────────────────────────────────────────────────

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ─── WIZARD NAVIGATION ──────────────────────────────────────

    public function nextStep()
    {
        $this->validate();
        $this->wizardStep++;

        // Auto-generate username when entering step 3
        if ($this->wizardStep === 3) {
            if (empty($this->user_username)) {
                $this->user_username = $this->generateUsername();
            }
            if (empty($this->rol_finicial)) {
                $year = now()->year;
                $this->rol_finicial = "{$year}-09-01";
                $this->rol_ffinal = ($year + 1) . "-08-31";
            }
        }
    }

    public function prevStep()
    {
        $this->wizardStep--;
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->profesor_id = null;
        $this->wizardStep = 1;
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $profesor = Profesor::with('user')->findOrFail($id);

        $this->profesor_id = $profesor->id;
        $this->ci_profesor = $profesor->ci_profesor;
        $this->ti_teacher = $profesor->ti_teacher;
        $this->name = $profesor->name;
        $this->lastname = $profesor->lastname;
        $this->gender = $profesor->gender;
        $this->date_birth = $profesor->date_birth ? \Carbon\Carbon::parse($profesor->date_birth)->format('Y-m-d') : null;
        $this->email = $profesor->email;
        $this->phone = $profesor->phone;
        $this->cellphone = $profesor->cellphone;
        $this->whatsapp = $profesor->whatsapp;
        $this->gsemail = $profesor->gsemail;
        $this->dir_address = $profesor->dir_address;
        $this->status_active = $profesor->status_active === 'true' || $profesor->status_active === true;

        // Load user data if exists
        if ($profesor->user) {
            $this->user_username = $profesor->user->username;
        }

        // Load rol data if exists
        $rol = Rol::where('user_id', $profesor->user_id)
            ->where('area', 'PROFESORADO')
            ->latest()
            ->first();
        if ($rol) {
            $this->rol_finicial = $rol->finicial?->format('Y-m-d');
            $this->rol_ffinal = $rol->ffinal?->format('Y-m-d');
        }

        $this->isEditing = true;
        $this->wizardStep = 1;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Validate all steps
        $rules = [
            'ci_profesor' => 'required|string|max:20',
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'gender' => 'required|in:M,F',
            'date_birth' => 'nullable|date',
            'ti_teacher' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'gsemail' => 'nullable|email|max:150',
            'dir_address' => 'nullable|string|max:255',
            'user_username' => 'required|string|max:150',
            'rol_finicial' => 'nullable|date',
            'rol_ffinal' => 'nullable|date|after_or_equal:rol_finicial',
            'status_active' => 'required|boolean',
        ];

        $this->validate($rules);

        try {
            DB::beginTransaction();

            // ─── 1. Buscar o crear User ──────────────────────────
            $user = null;
            if ($this->isEditing) {
                $profesor = Profesor::findOrFail($this->profesor_id);
                if ($profesor->user_id) {
                    $user = User::find($profesor->user_id);
                }
            }

            if (!$user) {
                // Ensure username is unique
                $username = $this->user_username;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $this->user_username . $counter;
                    $counter++;
                }

                $userData = [
                    'username' => $username,
                    'email' => $this->email ?: ($this->ci_profesor . '@temp.com'),
                    'password' => bcrypt($this->user_password ?: $this->ci_profesor),
                    'number_id' => $this->ci_profesor,
                    'is_active' => $this->status_active ? 'enable' : 'disable',
                ];

                $user = User::create($userData);
            } else {
                $user->update([
                    'username' => $this->user_username,
                    'email' => $this->email ?: $user->email,
                    'number_id' => $this->ci_profesor,
                    'is_active' => $this->status_active ? 'enable' : 'disable',
                ]);

                if (!empty($this->user_password)) {
                    $user->update(['password' => bcrypt($this->user_password)]);
                }
            }

            // ─── 2. Buscar o crear Profile ───────────────────────
            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'firstname' => strtoupper($this->name),
                    'lastname' => strtoupper($this->lastname),
                    'card_number' => $this->ci_profesor,
                    'dir_address' => $this->dir_address,
                ]
            );

            // ─── 3. Buscar o crear Rol ───────────────────────────
            Rol::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'area' => 'PROFESORADO',
                    'rol' => 'PROFESOR',
                ],
                [
                    'descripcion' => 'Profesor de la institución',
                    'finicial' => $this->rol_finicial ?: now()->year . '-09-01',
                    'ffinal' => $this->rol_ffinal ?: (now()->year + 1) . '-08-31',
                ]
            );

            // ─── 4. Crear o actualizar Profesor ─────────────────
            $profesorData = [
                'user_id' => $user->id,
                'ci_profesor' => $this->ci_profesor,
                'ti_teacher' => $this->ti_teacher,
                'name' => strtoupper($this->name),
                'lastname' => strtoupper($this->lastname),
                'gender' => $this->gender,
                'date_birth' => $this->date_birth,
                'email' => $this->email,
                'phone' => $this->phone,
                'cellphone' => $this->cellphone,
                'whatsapp' => $this->whatsapp,
                'gsemail' => $this->gsemail,
                'dir_address' => $this->dir_address,
                'status_active' => $this->status_active ? 'true' : 'false',
            ];

            if ($this->isEditing) {
                $profesor->update($profesorData);
                $this->notification()->success(
                    title: 'Profesor Actualizado',
                    description: 'El profesor se actualizó correctamente.'
                );
            } else {
                Profesor::create($profesorData);
                $this->notification()->success(
                    title: 'Profesor Creado',
                    description: 'El profesor se creó correctamente con usuario y rol.'
                );
            }

            DB::commit();
            $this->close();
            $this->modeIndex = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error(
                title: 'Error',
                description: 'Ocurrió un error al guardar: ' . $e->getMessage()
            );
        }
    }

    // ─── DELETE ──────────────────────────────────────────────────

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
        $profesor = Profesor::withCount('pevaluacions')
            ->findOrFail($this->confirmDeleteId);

        if ($profesor->pevaluacions_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "El profesor tiene {$profesor->pevaluacions_count} carga(s) académica(s) asociada(s)."
            );
            $this->cancelDelete();
            return;
        }

        // Soft-delete profesor
        $profesor->delete();

        $this->cancelDelete();

        $this->notification()->success(
            title: 'Profesor Eliminado',
            description: 'El profesor se eliminó correctamente.'
        );
    }

    // ─── TOGGLE ACTIVE ─────────────────────────────────────────

    public function confirmToggleActive($profesorId)
    {
        $profesor = Profesor::with('user')->findOrFail($profesorId);

        if (!$profesor->user) {
            $this->notification()->error(
                title: 'Sin usuario',
                description: 'Este profesor no tiene un usuario asociado.'
            );
            return;
        }

        $this->toggleActiveProfesorId = $profesor->id;
        $this->toggleActiveName = $profesor->full_name;
        $this->toggleActiveCurrentStatus = $profesor->user->is_active === 'enable';
        $this->confirmToggleActiveId = $profesor->user->id;
    }

    public function cancelToggleActive()
    {
        $this->confirmToggleActiveId = null;
        $this->toggleActiveProfesorId = null;
        $this->toggleActiveName = '';
    }

    public function toggleActive()
    {
        $user = User::findOrFail($this->confirmToggleActiveId);
        $newStatus = $user->is_active === 'enable' ? 'disable' : 'enable';
        $user->update(['is_active' => $newStatus]);

        $label = $newStatus === 'enable' ? 'activado' : 'desactivado';
        $this->notification()->success(
            title: 'Usuario ' . ($newStatus === 'enable' ? 'Activado' : 'Desactivado'),
            description: "El usuario de {$this->toggleActiveName} fue {$label} correctamente."
        );

        $this->cancelToggleActive();
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewProfesorId = $id;
        $this->previewMode = true;
    }

    #[On('closePreviewModal')]
    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewProfesorId = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function autoGenerateUsername()
    {
        if (!$this->isEditing && !empty($this->name) && !empty($this->lastname) && !empty($this->ci_profesor)) {
            $this->user_username = $this->generateUsername();
        }
    }

    private function generateUsername(): string
    {
        $nameParts = explode(' ', trim($this->name));
        $lastnameParts = explode(' ', trim($this->lastname));
        $base = strtolower(
            substr($nameParts[0] ?? '', 0, 1) . ($lastnameParts[0] ?? '')
        );
        $ciDigits = substr(preg_replace('/[^0-9]/', '', $this->ci_profesor), -2, 2);
        $username = $base . $ciDigits;

        $original = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $original . $counter;
            $counter++;
        }

        return $username;
    }

    public function resetForm()
    {
        $this->reset([
            'ci_profesor', 'ti_teacher', 'name', 'lastname', 'gender',
            'date_birth', 'email', 'phone', 'cellphone', 'whatsapp',
            'gsemail', 'dir_address', 'user_username', 'user_password',
            'rol_finicial', 'rol_ffinal',
        ]);
        $this->status_active = true;
        $this->ti_teacher = 'Titular';
        $this->gender = 'M';
        $this->wizardStep = 1;
    }

    public function resetFilters()
    {
        $this->reset([
            'search', 'filter_peducativo', 'filter_pevaluacions', 'filter_activities',
        ]);
    }

    public function close()
    {
        $this->modeForm = false;
        $this->previewMode = false;
        $this->wizardStep = 1;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
