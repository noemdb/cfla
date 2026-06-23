<?php

namespace App\Http\Livewire\Planning\Profesor;

use Livewire\Component;
use App\Models\app\Pescolar\Profesor;
use App\User;
use App\Models\sys\Profile;
use App\Models\sys\Rol;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfesorWizardComponent extends Component
{
    public $wizardStep = 1;
    public $profesor_id;
    
    // Profesor data
    public $ti_teacher, $ci_profesor, $name, $lastname, $gender, $date_birth;
    public $city_birth, $town_hall_birth, $state_birth, $country_birth;
    
    // Contact data
    public $dir_address, $phone, $cellphone, $whatsapp, $email, $gsemail;
    public $status_active = true;

    // User & Rol data
    public $user_username, $user_password;
    public $rol_area = 'PROFESORADO', $rol_rol = 'PROFESOR', $rol_descripcion = 'Profesor de la institución';
    public $rol_finicial, $rol_ffinal;

    protected $listeners = ['createProfesor', 'editProfesor'];

    public function createProfesor()
    {
        $this->resetForm();
        $this->wizardStep = 1;
        $this->dispatchBrowserEvent('show-profesor-modal');
    }

    public function editProfesor($id)
    {
        $this->resetForm();
        $profesor = Profesor::findOrFail($id);
        $this->profesor_id = $profesor->id;
        
        $this->ti_teacher = $profesor->ti_teacher;
        $this->ci_profesor = $profesor->ci_profesor;
        $this->name = $profesor->name;
        $this->lastname = $profesor->lastname;
        $this->gender = $profesor->gender;
        $this->date_birth = $profesor->date_birth;
        
        $this->city_birth = $profesor->city_birth;
        $this->town_hall_birth = $profesor->town_hall_birth;
        $this->state_birth = $profesor->state_birth;
        $this->country_birth = $profesor->country_birth;
        
        $this->dir_address = $profesor->dir_address;
        $this->phone = $profesor->phone;
        $this->cellphone = $profesor->cellphone;
        $this->whatsapp = $profesor->whatsapp;
        $this->email = $profesor->email;
        $this->gsemail = $profesor->gsemail;
        
        $this->status_active = filter_var($profesor->status_active, FILTER_VALIDATE_BOOLEAN);

        // Cargar datos de User y Rol
        $user = $profesor->user;
        if ($user) {
            $this->user_username = $user->username;
            
            $rol = Rol::where('user_id', $user->id)
                      ->where('area', 'PROFESORADO')
                      ->where('rol', 'PROFESOR')
                      ->first();
            if ($rol) {
                $this->rol_area = $rol->area;
                $this->rol_rol = $rol->rol;
                $this->rol_descripcion = $rol->descripcion;
                $this->rol_finicial = $rol->finicial ? Carbon::parse($rol->finicial)->format('Y-m-d') : null;
                $this->rol_ffinal = $rol->ffinal ? Carbon::parse($rol->ffinal)->format('Y-m-d') : null;
            }
        }

        $this->wizardStep = 1;
        $this->dispatchBrowserEvent('show-profesor-modal');
    }

    public function nextStep()
    {
        if ($this->wizardStep == 1) {
            $this->validate([
                'ci_profesor' => 'required|string|max:20',
                'name' => 'required|string|max:100',
                'lastname' => 'required|string|max:100',
                'gender' => 'required|in:M,F',
                'date_birth' => 'nullable|date',
                'ti_teacher' => 'nullable|string|max:50',
            ]);
        } elseif ($this->wizardStep == 2) {
            $this->validate([
                'email' => 'required|email|max:150',
                'phone' => 'nullable|string|max:20',
                'cellphone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'dir_address' => 'nullable|string|max:255',
            ]);

            // Generar valores por defecto para el Paso 3 si están vacíos
            if (empty($this->user_username) && $this->name && $this->lastname) {
                $usernameBase = strtolower(substr($this->name, 0, 1) . strtok($this->lastname, ' '));
                $str_ci = substr(preg_replace('/[^0-9]/', '', $this->ci_profesor), -2, 2);
                $this->user_username = $usernameBase . $str_ci;
            }
            if (empty($this->rol_finicial)) {
                $this->rol_finicial = Carbon::now()->year . '-09-01';
                $this->rol_ffinal = (Carbon::now()->year + 1) . '-08-31';
            }
        }

        if ($this->wizardStep < 3) {
            $this->wizardStep++;
        }
    }

    public function prevStep()
    {
        if ($this->wizardStep > 1) {
            $this->wizardStep--;
        }
    }

    public function submit()
    {
        // Validación final (por seguridad)
        $this->validate([
            'ci_profesor' => 'required|string|max:20',
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'gender' => 'required|in:M,F',
            'email' => 'required|email|max:150',
            'user_username' => 'required|string|max:150',
            'rol_area' => 'required|string',
            'rol_rol' => 'required|string',
            'rol_finicial' => 'required|date',
            'rol_ffinal' => 'required|date|after_or_equal:rol_finicial',
        ]);

        DB::beginTransaction();

        try {
            $profesor = $this->profesor_id ? Profesor::find($this->profesor_id) : new Profesor();
            
            // 1. Manejar User
            $user = $profesor->user;
            if (!$user) {
                // Verificar si el username ya existe (y no es el mismo usuario)
                $username = $this->user_username;
                $originalUsername = $username;
                $counter = 1;
                while(User::where('username', $username)->exists()){
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                $user = User::create([
                    'username' => $username,
                    'email' => $this->email,
                    'password' => empty($this->user_password) ? $this->ci_profesor : $this->user_password,
                    'is_active' => 'enable',
                    'number_id' => $this->ci_profesor
                ]);
            } else {
                $userUpdateData = [
                    'username' => $this->user_username,
                    'email' => $this->email,
                    'number_id' => $this->ci_profesor
                ];
                if (!empty($this->user_password)) {
                    $userUpdateData['password'] = $this->user_password;
                }
                $user->update($userUpdateData);
            }

            // 2. Manejar Profile
            $profile = Profile::where('user_id', $user->id)->first();
            if (!$profile) {
                Profile::create([
                    'user_id' => $user->id,
                    'firstname' => $this->name,
                    'lastname' => $this->lastname,
                    'card_number' => $this->ci_profesor,
                    'url_img' => 'images/avatar/user_default.png'
                ]);
            } else {
                $profile->update([
                    'firstname' => $this->name,
                    'lastname' => $this->lastname,
                    'card_number' => $this->ci_profesor
                ]);
            }

            // 3. Manejar Rol
            $rol = Rol::where('user_id', $user->id)
                      ->where('area', $this->rol_area)
                      ->where('rol', $this->rol_rol)
                      ->first();
                      
            if (!$rol) {
                Rol::create([
                    'user_id' => $user->id,
                    'area' => $this->rol_area,
                    'rol' => $this->rol_rol,
                    'descripcion' => $this->rol_descripcion,
                    'finicial' => $this->rol_finicial,
                    'ffinal' => $this->rol_ffinal
                ]);
            } else {
                $rol->update([
                    'descripcion' => $this->rol_descripcion,
                    'finicial' => $this->rol_finicial,
                    'ffinal' => $this->rol_ffinal
                ]);
            }

            // 4. Guardar Profesor
            $profesor->user_id = $user->id;
            $profesor->ti_teacher = $this->ti_teacher;
            $profesor->ci_profesor = $this->ci_profesor;
            $profesor->name = $this->name;
            $profesor->lastname = $this->lastname;
            $profesor->gender = $this->gender;
            $profesor->date_birth = $this->date_birth;
            $profesor->city_birth = $this->city_birth;
            $profesor->town_hall_birth = $this->town_hall_birth;
            $profesor->state_birth = $this->state_birth;
            $profesor->country_birth = $this->country_birth;
            $profesor->dir_address = $this->dir_address;
            $profesor->phone = $this->phone;
            $profesor->cellphone = $this->cellphone;
            $profesor->whatsapp = $this->whatsapp;
            $profesor->email = $this->email;
            $profesor->gsemail = $this->gsemail;
            $profesor->status_active = $this->status_active ? 'true' : 'false';
            
            $profesor->save();

            DB::commit();

            $this->dispatchBrowserEvent('hide-profesor-modal');
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Éxito',
                'text' => $this->profesor_id ? 'Profesor actualizado correctamente.' : 'Profesor registrado correctamente.',
                'type' => 'success',
                'icon' => 'success'
            ]);
            
            // Disparar evento para actualizar la lista (si fuera necesario)
            $this->emit('profesorSaved');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Ha ocurrido un error: ' . $e->getMessage(),
                'type' => 'error',
                'icon' => 'error'
            ]);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'profesor_id', 'ti_teacher', 'ci_profesor', 'name', 'lastname', 'gender', 'date_birth',
            'city_birth', 'town_hall_birth', 'state_birth', 'country_birth', 'dir_address',
            'phone', 'cellphone', 'whatsapp', 'email', 'gsemail', 'status_active',
            'user_username', 'user_password', 'rol_finicial', 'rol_ffinal'
        ]);
        $this->rol_area = 'PROFESORADO';
        $this->rol_rol = 'PROFESOR';
        $this->rol_descripcion = 'Profesor de la institución';
        $this->status_active = true;
        $this->wizardStep = 1;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.planning.profesor.profesor-wizard-component');
    }
}
