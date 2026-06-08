<?php

namespace App\Http\Livewire\Administracion\Administrativa\Asistente;

use Livewire\Component;
use Livewire\WithPagination;

// Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Retiro;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;
use App\User;

class RetiroComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';

    public $search = '';
    public $per_page = 10;
    public $estudiantes = [];
    public $selectedEstudiante = null;
    public $observations = '';
    public $showModalRetiro = false; // Nueva propiedad para controlar el modal

    protected $queryString = [
        'search' => ['except' => ''],
        'per_page' => ['except' => 10]
    ];

    // Listener para el evento de confirmación
    protected $listeners = [
        'remove' => 'procesarRetiro'
    ];

    // Reglas de validación
    protected $rules = [
        'observations' => 'required|min:10|max:500'
    ];

    // Mensajes de validación personalizados
    protected $messages = [
        'observations.required' => 'Las observaciones son obligatorias para el retiro administrativo.',
        'observations.min' => 'Las observaciones deben tener al menos 10 caracteres.',
        'observations.max' => 'Las observaciones no pueden exceder los 500 caracteres.'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $estudiants = Estudiant::active('true')
            ->when($this->search, function($query) {
                $query->name(['search' => $this->search]);
            })
            ->paginate($this->per_page);

        return view('livewire.administracion.administrativa.asistente.retiro-component', [
            'estudiants' => $estudiants
        ]);
    }

    public function confirmarRetiro($estudiantId)
    {
        $this->selectedEstudiante = Estudiant::find($estudiantId);
        
        if (!$this->selectedEstudiante) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Estudiante no encontrado.',
                'icon' => 'error'
            ]);
            return;
        }

        $user = User::find(Auth::id());
        
        // Determinar si requiere observaciones (solo para retiro administrativo)
        if ($user->isAdmon()) {
            // Abrir modal para retiro administrativo con observaciones
            $this->showModalRetiro = true;
            $retiro = $this->selectedEstudiante?->retiro;
            $this->observations = $retiro?->observations;
        } else {
            // Para retiro control, mantener el flujo original con SweetAlert
            $this->mostrarConfirmacionControl($estudiantId);
        }
    }

    private function mostrarConfirmacionControl($estudiantId)
    {
        $nombreEstudiante = $this->selectedEstudiante->fullname;
        $tieneRetiroControl = $this->selectedEstudiante->rControl;
        $tieneRetiroAdmon = $this->selectedEstudiante->rAdmon;

        $mensaje = "¿Está seguro de realizar el retiro del estudiante {$nombreEstudiante}?";

        if ($tieneRetiroControl || $tieneRetiroAdmon) {
            $tipoRetiro = $tieneRetiroControl ? 'Control' : 'Administrativo';
            $mensaje .= "\n\nEste estudiante ya tiene un retiro {$tipoRetiro} registrado.";
        }

        $this->dispatchBrowserEvent('swal:confirm', [
            'message' => 'Confirmar Retiro',
            'text' => $mensaje,
            'type' => 'warning',
            'id' => $estudiantId
        ]);
    }

    // Nuevo método para procesar retiro desde el modal
    public function procesarRetiroDesdeModal()
    {
        // Validar observaciones antes de continuar
        $this->validate();

        DB::beginTransaction();

        try {
            $estudiant = $this->selectedEstudiante;
            $user = User::find(Auth::id());
            
            // Verificar que sea usuario administrativo
            if (!$user->isAdmon()) {
                throw new \Exception('Usuario no tiene permisos para realizar retiros administrativos.');
            }

            // Procesar retiro administrativo con observaciones
            $this->procesarRetiroAdministrativo($estudiant, $this->observations);

            DB::commit();

            $this->dispatchBrowserEvent('swal', [
                'title' => 'Retiro Exitoso',
                'text' => "El estudiante {$estudiant->fullname} ha sido retirado exitosamente.",
                'icon' => 'success'
            ]);

            // Cerrar modal y resetear propiedades
            $this->cerrarModal();

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error en el Retiro',
                'text' => "Ocurrió un error al procesar el retiro: " . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // Método para cerrar el modal
    public function cerrarModal()
    {
        $this->showModalRetiro = false;
        $this->reset(['observations', 'selectedEstudiante']);
        $this->resetErrorBag();
    }

    public function procesarRetiro($estudiantId)
    {
        DB::beginTransaction();

        try {
            $estudiant = Estudiant::findOrFail($estudiantId);
            $user = User::find(Auth::id());
            
            // Determinar tipo de retiro basado en el rol del usuario
            $tipo = null;
            if ($user->isControl()) {
                $tipo = 'control';
            } elseif ($user->isAdmon()) {
                $tipo = 'admon';
            }

            if (!$tipo) {
                throw new \Exception('Usuario no tiene permisos para realizar retiros.');
            }            

            // Procesar según el tipo de retiro
            if ($tipo == 'admon') {
                // Para retiro administrativo, ahora se usa el modal
                throw new \Exception('Use el proceso de retiro con observaciones para retiros administrativos.');
            } elseif ($tipo == 'control') {
                $this->procesarRetiroControl($estudiant);
            }

            DB::commit();

            $this->dispatchBrowserEvent('swal', [
                'title' => 'Retiro Exitoso',
                'text' => "El estudiante {$estudiant->fullname} ha sido retirado exitosamente.",
                'icon' => 'success'
            ]);

            // Resetear propiedades
            $this->selectedEstudiante = null;

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error en el Retiro',
                'text' => "Ocurrió un error al procesar el retiro: " . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    private function procesarRetiroAdministrativo($estudiant, $observations = null)
    {
        // Crear o actualizar el registro de retiro con observaciones
        $retiro = Retiro::updateOrCreate(
            [
                'estudiant_id' => $estudiant->id,
            ],
            [
                'user_id' => Auth::id(),
                'tipo' => 'admon',
                'status_admon' => 'true',
                'observations' => $observations // Guardar observaciones
            ]
        );

        // Generar deuda individual pendiente si existe
        $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;

        if ($exchange_ammount_expire_bill > 0) {
            $expire_bill = Cuentaxpagar::create([
                'planpago_id' => 2000, //D. RETIRO ADMINISTRATIVO
                'name' => 'D. INDIVIDUAL R.A.',
                'type' => 'INDIVIDUAL',
                'estudiant_id' => $estudiant->id,
                'date_expiration' => Carbon::now()->format('Y-m-d'),
                'description' => 'DEUDA INDIVIDUAL PENDIENTE GENERADA POR: ' . Auth::user()->username . ', DEL RETIRO DEL ESTUDIANTE.',
                'status_active' => 'true',
                'status_inscription' => 'true'
            ]);            

            $ammount_expire_bill = $estudiant->ammount_expire_bill;
            $concepto = ConceptoPago::create([
                'cuentaxpagar_id' => $expire_bill->id,
                'nom_concepto_pago_id' => 3,
                'concepto_description' => 'DEUDA INDIVIDUAL PENDIENTE, Retiro administrativo',
                'concepto_ammount' => $ammount_expire_bill,
                'exchange_ammount' => $exchange_ammount_expire_bill
            ]);
        }

        // Eliminar datos administrativos
        if (!empty($estudiant->administrativa->id)) {
            $administrativa = Administrativa::findOrFail($estudiant->administrativa->id);
            $administrativa->update(['planpago_id' => 2000]);
        }
    }

    private function procesarRetiroControl($estudiant)
    {
        // Crear o actualizar el registro de retiro (sin observaciones obligatorias)
        $retiro = Retiro::updateOrCreate(
            [
                'estudiant_id' => $estudiant->id,
            ],
            [
                'user_id' => Auth::id(),
                'tipo' => 'control',
                'status_control' => 'true',
            ]
        );

        // Eliminar inscripción académica
        if (!empty($estudiant->inscripcion->id)) {
            $inscripcion = Inscripcion::findOrFail($estudiant->inscripcion->id);
            $inscripcion->delete();
        }
    }
}