<?php
namespace App\Http\Livewire\Administracion\Matriculation;

use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Services\ResendEmailService;
use App\Services\SendPulseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Livewire\Component;
use Livewire\WithPagination;

class CatchmentComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    public $representant_ci     = '';
    public $search              = '';
    public $showDeleteModal     = false;
    public $catchmentToDeleteId = null;
    public $deleteMessage       = '';

    public $filterStatusActive       = '1';
    public $filterPestudio           = '';
    public $filterGrado              = '';
    public $sortField                = 'created_at';
    public $sortDirection            = 'desc';
    public $catchmentToForceDeleteId = null;
    public $forceDeleteMessage       = '';
    public $catchmentToRestoreId     = null;
    public $restoreMessage           = '';
    public $perPage                  = 15;

    protected const EMAIL_PROVIDER = 'sendpulse';
    protected $emailService;
    protected $cc_mail, $bcc_mail;

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
        $this->cc_mail      = env('MAIL_CC_ADDRESS_CONTROL', null);
        $this->bcc_mail     = [
            env('MAIL_CC_ADDRESS', null),
            env('MAIL_CC_ADDRESS_BIENESTAR', null),
        ];
    }

    public function mount()
    {
        $this->representant_ci = request('representant_ci', '');
    }

    protected function getEmailService(?string $provider = null)
    {
        $provider = $provider ?? self::EMAIL_PROVIDER;
        return match ($provider) {
            'resend' => app(ResendEmailService::class),
            default  => app(SendPulseService::class),
        };
    }

    public function updatingRepresentantCi()
    {$this->resetPage();}
    public function updatingSearch()
    {$this->resetPage();}
    public function updatingFilterStatusActive()
    {$this->resetPage();}
    public function updatingFilterGrado()
    {$this->resetPage();}

    public function updatingFilterPestudio()
    {
        $this->filterGrado = '';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function clearFilters()
    {
        $this->representant_ci    = '';
        $this->search             = '';
        $this->filterStatusActive = '1';
        $this->filterPestudio     = '';
        $this->filterGrado        = '';
        $this->resetPage();
    }

    public function searchByCi()
    {$this->resetPage();}

    public function getCatchmentsProperty()
    {
        $query = Catchment::select('catchments.*')
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->filterStatusActive !== '') {
            $query->where('status_active', $this->filterStatusActive === '1');
        }

        if (! empty($this->representant_ci)) {
            $query->where('representant_ci', $this->representant_ci);
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('representant_name', 'like', '%' . $this->search . '%')
                    ->orWhere('representant_lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterPestudio !== '') {
            $query->whereHas('grado', fn($q) => $q->where('pestudio_id', $this->filterPestudio));
        }

        if ($this->filterGrado !== '') {
            $query->where('grade', $this->filterGrado);
        }

        return $query->paginate($this->perPage);
    }

    public function prepareDelete($catchmentId)
    {
        $catchment = Catchment::find($catchmentId);

        if (! $catchment) {
            $this->dispatchBrowserEvent('swal:error', ['title' => 'Error', 'text' => 'Registro no encontrado']);
            return;
        }

        if (! $catchment->status_delete) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'No se puede eliminar',
                'text'  => 'Este registro no puede ser eliminado porque tiene entrevistas asociadas.',
            ]);
            return;
        }

        $this->catchmentToDeleteId = $catchment->id;
        $this->deleteMessage       = "¿Está seguro de eliminar el registro de {$catchment->fullname}?";
        $this->dispatchBrowserEvent('swal:confirm-catchment', [
            'title'             => '¿Confirmar eliminación?',
            'text'              => $this->deleteMessage,
            'confirmButtonText' => 'Sí, eliminar',
            'cancelButtonText'  => 'Cancelar',
        ]);
    }

    public function confirmDelete()
    {
        if (! $this->catchmentToDeleteId) {
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

            $this->dispatchBrowserEvent('swal:success-catchment', ['title' => 'Eliminado', 'text' => 'El registro ha sido eliminado correctamente']);
            $this->catchmentToDeleteId = null;
            $this->deleteMessage       = '';
            $this->resetPage();
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    public function cancelDelete()
    {
        $this->catchmentToDeleteId = null;
        $this->deleteMessage       = '';
    }

    public function prepareForceDelete($catchmentId)
    {
        $catchment = Catchment::find($catchmentId);

        if (! $catchment) {
            $this->dispatchBrowserEvent('swal:error', ['title' => 'Error', 'text' => 'Registro no encontrado']);
            return;
        }

        $this->catchmentToForceDeleteId = $catchment->id;
        $this->forceDeleteMessage       = "¿Está completamente seguro de eliminar DEFINITIVAMENTE el registro de {$catchment->fullname}? Esta acción no se puede deshacer y borrará los datos de la base de datos.";
        $this->dispatchBrowserEvent('swal:confirm-catchment', [
            'title'             => '¿Confirmar Borrado Definitivo?',
            'text'              => $this->forceDeleteMessage,
            'confirmButtonText' => 'Sí, borrar definitivamente',
            'cancelButtonText'  => 'Cancelar',
            'action'            => 'forceDelete',
        ]);
    }

    public function confirmForceDelete()
    {
        if (! $this->catchmentToForceDeleteId) {
            return;
        }

        try {
            DB::beginTransaction();
            $catchment = Catchment::find($this->catchmentToForceDeleteId);
            if ($catchment) {
                // Primero eliminamos cualquier entrevista asociada si existe (por seguridad, aunque no debería dejar borrar el catchment si tiene entrevistas activas).
                foreach ($catchment->catchmentInterviews as $interview) {
                    $interview->delete();
                }
                $catchment->delete(); // Delete the record from the database definitively
            }
            DB::commit();

            $this->dispatchBrowserEvent('swal:success-catchment', ['title' => 'Eliminado Definitivamente', 'text' => 'El registro ha sido eliminado completamente de la base de datos']);
            $this->catchmentToForceDeleteId = null;
            $this->forceDeleteMessage       = '';
            $this->resetPage();
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al eliminar definitivamente: ' . $e->getMessage()]);
        }
    }

    public function cancelForceDelete()
    {
        $this->catchmentToForceDeleteId = null;
        $this->forceDeleteMessage       = '';
    }

    public function prepareRestore($catchmentId)
    {
        $catchment = Catchment::find($catchmentId);

        if (! $catchment) {
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Registro no encontrado']);
            return;
        }

        $this->catchmentToRestoreId = $catchment->id;
        $this->restoreMessage       = "¿Está seguro de que desea restaurar el registro de {$catchment->fullname}? Volverá a estar activo.";
        $this->dispatchBrowserEvent('swal:confirm-catchment', [
            'title'             => '¿Confirmar Restauración?',
            'text'              => $this->restoreMessage,
            'confirmButtonText' => 'Sí, restaurar',
            'cancelButtonText'  => 'Cancelar',
            'action'            => 'restore',
        ]);
    }

    public function confirmRestore()
    {
        if (! $this->catchmentToRestoreId) {
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
            $this->restoreMessage       = '';
            $this->resetPage();
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al restaurar: ' . $e->getMessage()]);
        }
    }

    public function cancelRestore()
    {
        $this->catchmentToRestoreId = null;
        $this->restoreMessage       = '';
    }

    public function sendAcceptanceEmail($interviewId)
    {
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            if (! $interview->accepted) {
                $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'La entrevista no está marcada como aceptada']);
                return;
            }

            $htmlContent = view('email.catchment.accepted', [
                'interview'    => $interview,
                'institucion'  => Institucion::orderBy('created_at', 'DESC')->first(),
                'autoridad'    => Autoridad::getTipoAuthority('1'),
                'director'     => Autoridad::getTipoAuthority('2'),
                'list_comment' => Catchment::COLUMN_COMMENTS,
                'toDate'       => Date::now()->format('d F Y'),
            ])->render();

            $response = $this->emailService->send($interview->email, 'Aceptación de Solicitud - Matrícula Escolar', $htmlContent, null, false, $this->cc_mail, $this->bcc_mail);

            if ($response['success']) {
                $this->dispatchBrowserEvent('swal:success-catchment', ['title' => 'Correo enviado', 'text' => 'Correo de aceptación enviado correctamente']);
            } else {
                $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al enviar: ' . $response['message']]);
            }
        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function sendStandbyEmail($interviewId)
    {
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            if (! $interview->status_standby) {
                $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'La entrevista no está en estado de espera']);
                return;
            }

            $htmlContent = view('email.catchment.status_standby', [
                'interview'    => $interview,
                'institucion'  => Institucion::orderBy('created_at', 'DESC')->first(),
                'autoridad'    => Autoridad::getTipoAuthority('1'),
                'director'     => Autoridad::getTipoAuthority('2'),
                'list_comment' => Catchment::COLUMN_COMMENTS,
                'toDate'       => Date::now()->format('d F Y'),
            ])->render();

            $response = $this->emailService->send($interview->email, 'Lista de Espera - Matrícula Escolar', $htmlContent, null, false, $this->cc_mail, $this->bcc_mail);

            if ($response['success']) {
                $this->dispatchBrowserEvent('swal:success-catchment', ['title' => 'Correo enviado', 'text' => 'Correo de espera enviado correctamente']);
            } else {
                $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al enviar: ' . $response['message']]);
            }
        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function insertEstudiantRepresentanAccepted($interviewId)
    {
        DB::beginTransaction();
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            $representant = Representant::firstOrCreate(
                ['ci_representant' => $interview->identification_number],
                [
                    'name'          => $interview->full_name,
                    'phone'         => $interview->phone_numbers,
                    'whatsapp'      => $interview->phone_numbers,
                    'cellphone'     => $interview->phone_numbers,
                    'email'         => $interview->email,
                    'status_active' => 'true',
                ]
            );
            $representant->save();

            $full_name                 = preg_replace('/\s+/', ' ', trim($interview->student_full_name));
            [$est_name, $est_lastname] = $this->splitName($full_name);
            $est_name                  = mb_strtoupper($this->normalizeName($est_name), 'UTF-8');
            $est_lastname              = mb_strtoupper($this->normalizeName($est_lastname), 'UTF-8');

            $estudiant = Estudiant::firstOrNew([
                'name'       => $est_name,
                'lastname'   => $est_lastname,
                'date_birth' => $interview->date_of_birth,
            ]);

            $estudiant->fill([
                'planpago_id'       => 1,
                'grado_inicial_id'  => $interview->grade_year_aspiring ?? 1,
                'seccion_inicial'   => '1',
                'type_ci_id'        => 1,
                'ci_estudiant'      => 'TEMP-' . Str::random(6),
                'ci_estudiant_temp' => 'TEMP-' . Str::random(6),
                'gender'            => 'Masculino',
                'email'             => $interview->email,
                'representant_ci'   => $representant->ci_representant,
                'representant_id'   => $representant->id,
                'status_active'     => 'true',
                'token'             => Str::uuid(),
                'status_blacklist'  => 'false',
                'status_notice'     => 1,
            ]);
            $estudiant->save();

            DB::commit();
            $this->dispatchBrowserEvent('swal:success-catchment', ['title' => 'Registro exitoso', 'text' => 'Representante y Estudiante registrado correctamente']);
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error-catchment', ['title' => 'Error', 'text' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }

    private function splitName($fullName)
    {
        $parts = explode(' ', trim($fullName), 2);
        return [$parts[0], $parts[1] ?? ''];
    }

    private function normalizeName(string $value): string
    {
        return ucwords(strtolower(trim($value)));
    }

    public function render()
    {
        $list_pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('order')
            ->pluck('name', 'id');

        $gradosQuery = Grado::where('status_active', 'true')->orderBy('order');
        if ($this->filterPestudio !== '') {
            $gradosQuery->where('pestudio_id', $this->filterPestudio);
        }
        $list_grados_filtered = $gradosQuery->pluck('name', 'id');

        return view('livewire.administracion.matriculation.catchment-component', [
            'catchments'           => $this->catchments,
            'list_comment'         => Catchment::COLUMN_COMMENTS,
            'list_pestudios'       => $list_pestudios,
            'list_grados_filtered' => $list_grados_filtered,
        ]);
    }
}
