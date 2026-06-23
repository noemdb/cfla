<?php

// namespace App\Http\Livewire\Bienestar\Matriculation;

// use Livewire\Component;

// class CatchmentComponent extends Component
// {
//     public function render()
//     {
//         return view('livewire.bienestar.matriculation.catchment-component');
//     }
// }


namespace App\Http\Livewire\Bienestar\Matriculation;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Services\ResendEmailService;
use App\Services\SendPulseService;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;

class CatchmentComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Propiedades públicas para el filtro
    public $representant_ci = '';
    public $search = '';

    // Propiedades para modales y estados
    public $showDeleteModal          = false;
    public $catchmentToDeleteId      = null;
    public $deleteMessage            = '';
    
    public $filterStatusActive       = '';
    public $catchmentToForceDeleteId = null;
    public $forceDeleteMessage       = '';
    public $catchmentToRestoreId     = null;
    public $restoreMessage           = '';

    // Configuración de email
    protected const EMAIL_PROVIDER = 'sendpulse';
    protected $emailService;
    protected $cc_mail, $bcc_mail;

    // Listeners para eventos
    protected $listeners = [
        'confirmDelete'      => 'confirmDelete',
        'cancelDelete'       => 'cancelDelete',
        'confirmForceDelete' => 'confirmForceDelete',
        'cancelForceDelete'  => 'cancelForceDelete',
        'confirmRestore'     => 'confirmRestore',
        'cancelRestore'      => 'cancelRestore',
    ];

    public function boot()
    {
        $this->emailService = $this->getEmailService();
        $this->cc_mail = env('MAIL_CC_ADDRESS_CONTROL', null);
        $this->bcc_mail = [
            env('MAIL_CC_ADDRESS', null),
            env('MAIL_CC_ADDRESS_BIENESTAR', null),
        ];
    }

    public function mount()
    {
        $this->representant_ci = request('representant_ci', '');
    }

    /**
     * Obtiene el servicio de email configurado
     */
    protected function getEmailService(?string $provider = null)
    {
        $provider = $provider ?? self::EMAIL_PROVIDER;

        return match ($provider) {
            'resend' => app(ResendEmailService::class),
            default => app(SendPulseService::class),
        };
    }

    /**
     * Actualiza la paginación cuando cambian los filtros
     */
    public function updatingRepresentantCi()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Limpia los filtros
     */
    public function clearFilters()
    {
        $this->representant_ci = '';
        $this->search = '';
        $this->filterStatusActive = '';
        $this->resetPage();
    }

    /**
     * Buscar por cédula
     */
    public function searchByCi()
    {
        $this->resetPage();
        // La búsqueda se ejecuta automáticamente en el render
    }

    /**
     * Obtiene los catchments filtrados
     */
    public function getCatchmentsProperty()
    {
        $query = Catchment::select('catchments.*')
            ->orderBy('created_at', 'desc');

        if ($this->filterStatusActive !== '') {
            $query->where('status_active', $this->filterStatusActive === '1');
        }

        if (!empty($this->representant_ci)) {
            $query->where('representant_ci', $this->representant_ci);
        }

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('representant_name', 'like', '%' . $this->search . '%')
                    ->orWhere('representant_lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(15);
    }

    /**
     * Prepara la eliminación de un catchment
     */
    public function prepareDelete($catchmentId)
    {
        $catchment = Catchment::find($catchmentId);

        if (!$catchment) {
            $this->dispatchBrowserEvent('swal:error-catchment', [
                'title' => 'Error',
                'text' => 'Registro no encontrado'
            ]);
            return;
        }

        // Verificar si se puede eliminar
        if (!$catchment->status_delete) {
            $this->dispatchBrowserEvent('swal:error-catchment', [
                'title' => 'No se puede eliminar',
                'text' => 'Este registro no puede ser eliminado porque tiene entrevistas asociadas.'
            ]);
            return;
        }

        $this->catchmentToDeleteId = $catchment->id;
        $this->deleteMessage = "¿Está seguro de eliminar el registro de {$catchment->fullname}?";

        $this->dispatchBrowserEvent('swal:confirm-catchment', [
            'title' => '¿Confirmar eliminación?',
            'text' => $this->deleteMessage,
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText' => 'Cancelar'
        ]);
    }

    /**
     * Confirma y ejecuta la eliminación
     */
    public function confirmDelete()
    {
        if (!$this->catchmentToDeleteId) {
            return;
        }

        try {
            DB::beginTransaction();
            $catchment = Catchment::find($this->catchmentToDeleteId);
            if ($catchment) {
                $catchment->status_active = false;
                $catchment->save();
            }
            DB::commit();

            $this->dispatchBrowserEvent('swal:success-catchment', [
                'title' => 'Eliminado',
                'text' => 'El registro ha sido eliminado correctamente'
            ]);

            $this->catchmentToDeleteId = null;
            $this->deleteMessage = '';
            $this->resetPage();

        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error-catchment', [
                'title' => 'Error',
                'text' => 'Error al eliminar el registro: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cancela la eliminación
     */
    public function cancelDelete()
    {
        $this->catchmentToDeleteId = null;
        $this->deleteMessage = '';
    }

    public function prepareForceDelete($catchmentId)
    {
        $catchment = Catchment::find($catchmentId);

        if (!$catchment) {
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Registro no encontrado']);
            return;
        }

        $this->catchmentToForceDeleteId = $catchment->id;
        $this->forceDeleteMessage = "¿Está completamente seguro de eliminar DEFINITIVAMENTE el registro de {$catchment->fullname}? Esta acción no se puede deshacer y borrará los datos de la base de datos.";
        $this->dispatchBrowserEvent('swal:confirm-catchment', [
            'title'             => '¿Confirmar Borrado Definitivo?',
            'text'              => $this->forceDeleteMessage,
            'confirmButtonText' => 'Sí, borrar definitivamente',
            'cancelButtonText'  => 'Cancelar',
            'action'            => 'forceDelete'
        ]);
    }

    public function confirmForceDelete()
    {
        if (!$this->catchmentToForceDeleteId) {
            return;
        }

        try {
            DB::beginTransaction();
            $catchment = Catchment::find($this->catchmentToForceDeleteId);
            if ($catchment) {
                foreach ($catchment->catchmentInterviews as $interview) {
                    $interview->delete();
                }
                $catchment->delete();
            }
            DB::commit();

            $this->dispatchBrowserEvent('swal:success-catchment', ['title' => 'Eliminado Definitivamente', 'text' => 'El registro ha sido eliminado completamente de la base de datos']);
            $this->catchmentToForceDeleteId = null;
            $this->forceDeleteMessage = '';
            $this->resetPage();
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al eliminar definitivamente: ' . $e->getMessage()]);
        }
    }

    public function cancelForceDelete()
    {
        $this->catchmentToForceDeleteId = null;
        $this->forceDeleteMessage = '';
    }

    public function prepareRestore($catchmentId)
    {
        $catchment = Catchment::find($catchmentId);

        if (!$catchment) {
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Registro no encontrado']);
            return;
        }

        $this->catchmentToRestoreId = $catchment->id;
        $this->restoreMessage = "¿Está seguro de que desea restaurar el registro de {$catchment->fullname}? Volverá a estar activo.";
        $this->dispatchBrowserEvent('swal:confirm-catchment', [
            'title'             => '¿Confirmar Restauración?',
            'text'              => $this->restoreMessage,
            'confirmButtonText' => 'Sí, restaurar',
            'cancelButtonText'  => 'Cancelar',
            'action'            => 'restore'
        ]);
    }

    public function confirmRestore()
    {
        if (!$this->catchmentToRestoreId) {
            return;
        }

        try {
            DB::beginTransaction();
            $catchment = Catchment::find($this->catchmentToRestoreId);
            if ($catchment) {
                $catchment->status_active = true;
                $catchment->save();
            }
            DB::commit();

            $this->dispatchBrowserEvent('swal:success-catchment', ['title' => 'Restaurado', 'text' => 'El registro ha sido restaurado y está activo nuevamente']);
            $this->catchmentToRestoreId = null;
            $this->restoreMessage = '';
            $this->resetPage();
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al restaurar: ' . $e->getMessage()]);
        }
    }

    public function cancelRestore()
    {
        $this->catchmentToRestoreId = null;
        $this->restoreMessage = '';
    }

    /**
     * Envía correo de aceptación
     */
    public function sendAcceptanceEmail($interviewId)
    {
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            if (!$interview->accepted) {
                $this->dispatchBrowserEvent('swal:error-catchment', [
                    'title' => 'Error',
                    'text' => 'La entrevista no está marcada como aceptada'
                ]);
                return;
            }

            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $autoridad = Autoridad::getTipoAuthority('1');
            $director = Autoridad::getTipoAuthority('2');
            $toDate = Date::now()->format('d F Y');
            $list_comment = Catchment::COLUMN_COMMENTS;

            $htmlContent = view('email.catchment.accepted', [
                'interview' => $interview,
                'institucion' => $institucion,
                'autoridad' => $autoridad,
                'director' => $director,
                'list_comment' => $list_comment,
                'toDate' => $toDate,
            ])->render();

            $cc = $this->cc_mail;
            $bcc = $this->bcc_mail;
            $subject = 'Aceptación de Solicitud - Matrícula Escolar';
            $email = $interview->email;

            $response = $this->emailService->send($email, $subject, $htmlContent, null, false, $cc, $bcc);

            if ($response['success']) {
                $this->dispatchBrowserEvent('swal:success-catchment', [
                    'title' => 'Correo enviado',
                    'text' => 'El correo de aceptación ha sido enviado correctamente'
                ]);
            } else {
                $this->dispatchBrowserEvent('swal:error-catchment', [
                    'title' => 'Error',
                    'text' => 'Error al enviar el correo: ' . $response['message']
                ]);
            }

        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal:error-catchment', [
                'title' => 'Error',
                'text' => 'Error al enviar correo de aceptación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Envía correo de lista de espera
     */
    public function sendStandbyEmail($interviewId)
    {
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            if (!$interview->status_standby) {
                $this->dispatchBrowserEvent('swal:error-catchment', [
                    'title' => 'Error',
                    'text' => 'La entrevista no está en estado de espera'
                ]);
                return;
            }

            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $autoridad = Autoridad::getTipoAuthority('1');
            $director = Autoridad::getTipoAuthority('2');
            $toDate = Date::now()->format('d F Y');
            $list_comment = Catchment::COLUMN_COMMENTS;

            $htmlContent = view('email.catchment.status_standby', [
                'interview' => $interview,
                'institucion' => $institucion,
                'autoridad' => $autoridad,
                'director' => $director,
                'list_comment' => $list_comment,
                'toDate' => $toDate,
            ])->render();

            $cc = $this->cc_mail;
            $bcc = $this->bcc_mail;
            $subject = 'Lista de Espera - Matrícula Escolar';
            $email = $interview->email;

            $response = $this->emailService->send($email, $subject, $htmlContent, null, false, $cc, $bcc);

            if ($response['success']) {
                $this->dispatchBrowserEvent('swal:success-catchment', [
                    'title' => 'Correo enviado',
                    'text' => 'El correo de lista de espera ha sido enviado correctamente'
                ]);
            } else {
                $this->dispatchBrowserEvent('swal:error-catchment', [
                    'title' => 'Error',
                    'text' => 'Error al enviar el correo: ' . $response['message']
                ]);
            }

        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal:error-catchment', [
                'title' => 'Error',
                'text' => 'Error al enviar correo de lista de espera: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Inserta estudiante y representante cuando es aceptado
     */
    public function insertEstudiantRepresentanAccepted($interviewId)
    {
        DB::beginTransaction();
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            // Crear o encontrar Representante
            $representant = Representant::firstOrCreate(
                ['ci_representant' => $interview->identification_number],
                [
                    'name' => $interview->full_name,
                    'phone' => $interview->phone_numbers,
                    'whatsapp' => $interview->phone_numbers,
                    'cellphone' => $interview->phone_numbers,
                    'email' => $interview->email,
                    'status_active' => 'true',
                ]
            );
            $representant->save();

            // Crear Estudiante
            $full_name = preg_replace('/\s+/', ' ', trim($interview->student_full_name));
            [$est_name, $est_lastname] = $this->splitName($full_name);
            $est_name = mb_strtoupper($this->normalizeName($est_name), 'UTF-8');
            $est_lastname = mb_strtoupper($this->normalizeName($est_lastname), 'UTF-8');
            $dob = $interview->date_of_birth;

            $estudiant = Estudiant::firstOrNew([
                'name' => $est_name,
                'lastname' => $est_lastname,
                'date_birth' => $dob,
            ]);

            $estudiant->planpago_id = 1;
            $estudiant->grado_inicial_id = $interview->grade_year_aspiring ?? 1;
            $estudiant->seccion_inicial = '1';
            $estudiant->type_ci_id = 1;
            $estudiant->ci_estudiant = 'TEMP-' . Str::random(6);
            $estudiant->ci_estudiant_temp = 'TEMP-' . Str::random(6);
            $estudiant->lastname = $est_lastname;
            $estudiant->name = $est_name;
            $estudiant->gender = 'Masculino';
            $estudiant->date_birth = $interview->date_of_birth;
            $estudiant->email = $interview->email;
            $estudiant->representant_ci = $representant->ci_representant;
            $estudiant->representant_id = $representant->id;
            $estudiant->status_active = 'true';
            $estudiant->token = Str::uuid();
            $estudiant->status_blacklist = 'false';
            $estudiant->status_notice = 1;
            $estudiant->save();

            DB::commit();

            $this->dispatchBrowserEvent('swal:success-catchment', [
                'title' => 'Registro exitoso',
                'text' => 'Representante y Estudiante agregado/actualizado correctamente'
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            $this->dispatchBrowserEvent('swal:error-catchment', [
                'title' => 'Error',
                'text' => 'Error al registrar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Divide el nombre completo
     */
    private function splitName($fullName)
    {
        $parts = explode(' ', trim($fullName), 2);
        $first = $parts[0];
        $last = $parts[1] ?? '';
        return [$first, $last];
    }

    /**
     * Normaliza el nombre
     */
    private function normalizeName(string $value): string
    {
        return ucwords(strtolower(trim($value)));
    }

    public function render()
    {
        $catchments = $this->catchments;
        $list_comment = Catchment::COLUMN_COMMENTS;

        return view('livewire.bienestar.matriculation.catchment-component', [
            'catchments' => $catchments,
            'list_comment' => $list_comment,
        ]);
    }
}

