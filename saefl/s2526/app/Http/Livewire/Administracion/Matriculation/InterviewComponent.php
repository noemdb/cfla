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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Livewire\Component;
use Livewire\WithPagination;

class InterviewComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    public $representant_ci = '';
    public $search          = '';
    public $perPage         = 15;
    public $sortField       = 'created_at';
    public $sortDirection   = 'desc';

    public $filterAccepted       = '';
    public $filterStatusNotified = '';
    public $filterStatusStandby  = '';
    public $filterPestudio       = '';
    public $filterGrado          = '';

    public $showEditModal       = false;
    public $showDeleteModal     = false;
    public $selectedInterview   = null;
    public $deleteInterviewId   = null;
    public $deleteInterviewName = '';

    public $interviewData = [];
    public $statusNotify  = false;

    protected const EMAIL_PROVIDER = 'sendpulse';
    protected $emailService;
    protected $cc_mail, $bcc_mail;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'deleteConfirmed'  => 'deleteInterview',
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

    protected function getEmailService(?string $provider = null)
    {
        $provider = $provider ?? self::EMAIL_PROVIDER;
        return match ($provider) {
            'resend' => app(ResendEmailService::class),
            default  => app(SendPulseService::class),
        };
    }

    public function render()
    {
        // Lista de planes de estudio activos
        $list_pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('order')
            ->pluck('name', 'id');

        // Grados filtrados según el pestudio seleccionado
        $gradosQuery = Grado::where('status_active', 'true')->orderBy('order');
        if ($this->filterPestudio !== '') {
            $gradosQuery->where('pestudio_id', $this->filterPestudio);
        }
        $list_grados_filtered = $gradosQuery->pluck('name', 'id');

        return view('livewire.administracion.matriculation.interview-component', [
            'catchment_interviews' => $this->getInterviews(),
            'list_comment'         => CatchmentInterview::COLUMN_COMMENTS,
            'list_grade'           => CatchmentInterview::list_grade(),
            'list_religions'       => CatchmentInterview::list_religions(),
            'statistics'           => $this->getStatistics(),
            'list_pestudios'       => $list_pestudios,
            'list_grados_filtered' => $list_grados_filtered,
        ]);
    }

    protected function getInterviews()
    {
        $query = CatchmentInterview::query()
            ->select('catchment_interviews.*')
            ->with(['catchment.catchment_group.activities', 'grado.pestudio']);

        if (! empty($this->representant_ci)) {
            $query->where('identification_number', 'like', '%' . $this->representant_ci . '%');
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('student_full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterAccepted !== '') {
            $query->where('accepted', $this->filterAccepted);
        }

        if ($this->filterStatusNotified !== '') {
            $query->where('status_notified', $this->filterStatusNotified);
        }

        if ($this->filterStatusStandby !== '') {
            $query->where('status_standby', $this->filterStatusStandby);
        }

        // Filtro por plan de estudio (via grado)
        if ($this->filterPestudio !== '') {
            $query->whereHas('grado', fn($q) => $q->where('pestudio_id', $this->filterPestudio));
        }

        // Filtro por grado
        if ($this->filterGrado !== '') {
            $query->where('grade_year_aspiring', $this->filterGrado);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }

    protected function getStatistics()
    {
        $base = CatchmentInterview::query();

        if (! empty($this->representant_ci)) {
            $base->where('identification_number', 'like', '%' . $this->representant_ci . '%');
        }

        if (! empty($this->search)) {
            $base->where(function ($q) {
                $q->where('student_full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterPestudio !== '') {
            $base->whereHas('grado', fn($q) => $q->where('pestudio_id', $this->filterPestudio));
        }

        if ($this->filterGrado !== '') {
            $base->where('grade_year_aspiring', $this->filterGrado);
        }

        return [
            'total'    => (clone $base)->count(),
            'accepted' => (clone $base)->where('accepted', true)->count(),
            'standby'  => (clone $base)->where('status_standby', true)->count(),
            'notified' => (clone $base)->where('status_notified', true)->count(),
            'pending'  => (clone $base)->where('accepted', false)->where('status_standby', false)->count(),
        ];
    }

    public function updatingSearch()
    {$this->resetPage();}
    public function updatingRepresentantCi()
    {$this->resetPage();}
    public function updatingFilterAccepted()
    {$this->resetPage();}
    public function updatingFilterStatusNotified()
    {$this->resetPage();}
    public function updatingFilterStatusStandby()
    {$this->resetPage();}
    public function updatingFilterGrado()
    {$this->resetPage();}

    /** Al cambiar el plan de estudio se limpia el grado seleccionado */
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
        $this->representant_ci      = '';
        $this->search               = '';
        $this->filterAccepted       = '';
        $this->filterStatusNotified = '';
        $this->filterStatusStandby  = '';
        $this->filterPestudio       = '';
        $this->filterGrado          = '';
        $this->resetPage();
    }

    public function filterByAccepted()
    {$this->filterAccepted = '1';
        $this->filterStatusStandby                       = '';
        $this->resetPage();}
    public function filterByStandby()
    {$this->filterStatusStandby = '1';
        $this->filterAccepted                                 = '';
        $this->resetPage();}
    public function filterByNotified()
    {$this->filterStatusNotified = '1';
        $this->resetPage();}
    public function filterByPending()
    {$this->filterAccepted = '0';
        $this->filterStatusStandby                       = '0';
        $this->resetPage();}

    public function editInterview($id)
    {
        $this->selectedInterview = CatchmentInterview::findOrFail($id);
        $this->interviewData     = $this->selectedInterview->toArray();
        $this->statusNotify      = false;
        $this->showEditModal     = true;
    }

    public function updateInterview()
    {
        $this->validate([
            'interviewData.accepted'       => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && $this->interviewData['status_standby']) {
                        $fail('No puede aceptar y poner en espera al mismo tiempo.');
                    }
                },
            ],
            'interviewData.status_standby' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && $this->interviewData['accepted']) {
                        $fail('No se puede poner en espera y aceptar al mismo tiempo.');
                    }
                },
            ],
        ]);

        DB::beginTransaction();
        try {
            $this->selectedInterview->fill($this->interviewData);
            $this->selectedInterview->save();

            $message = 'Entrevista actualizada correctamente.';

            if ($this->selectedInterview->accepted == true) {
                $this->selectedInterview->generateToken();
                $this->selectedInterview->save();

                if ($this->statusNotify) {
                    $this->sendAcceptanceEmail($this->selectedInterview->id);
                }

                $response      = $this->insertEstudiantRepresentanAccepted($this->selectedInterview->id);
                $responseData  = json_decode($response->getContent(), true);
                $message      .= ' ' . ($responseData['message'] ?? $responseData['error'] ?? 'Error desconocido');
            }

            if ($this->selectedInterview->status_standby == true && $this->statusNotify) {
                $this->sendStandbyEmail($this->selectedInterview->id);
            }

            if ($this->statusNotify) {
                $this->selectedInterview->status_notified = true;
                $this->selectedInterview->save();
            }

            DB::commit();
            $this->showEditModal = false;
            $this->dispatchBrowserEvent('showAlert', ['type' => 'success', 'message' => $message]);
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    public function confirmDelete($id, $name)
    {
        $interview = CatchmentInterview::find($id);

        if ($interview && $interview->accepted) {
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'No se puede eliminar una entrevista aceptada.']);
            return;
        }

        $this->deleteInterviewId   = $id;
        $this->deleteInterviewName = $name;
        $this->dispatchBrowserEvent('confirmDelete', ['id' => $id, 'name' => $name]);
    }

    public function deleteInterview()
    {
        if (! $this->deleteInterviewId) {
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'No se ha seleccionado ninguna entrevista.']);
            return;
        }

        DB::beginTransaction();
        try {
            $interview = CatchmentInterview::findOrFail($this->deleteInterviewId);

            if ($interview->accepted) {
                throw new \Exception('No se puede eliminar una entrevista aceptada.');
            }

            $studentName = $interview->student_full_name;
            $interview->delete();
            DB::commit();

            $this->dispatchBrowserEvent('showAlert', ['type' => 'success', 'message' => "Entrevista de {$studentName} eliminada correctamente."]);
            $this->deleteInterviewId   = null;
            $this->deleteInterviewName = '';
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    protected function insertEstudiantRepresentanAccepted($id)
    {
        try {
            $interview = CatchmentInterview::findOrFail($id);

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

            return response()->json(['message' => 'Registro exitoso [Representante y Estudiante agregado/actualizado]'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    protected function sendAcceptanceEmail($interviewId)
    {
        try {
            $interview   = CatchmentInterview::findOrFail($interviewId);
            $htmlContent = view('email.catchment.accepted', [
                'interview'    => $interview,
                'institucion'  => Institucion::orderBy('created_at', 'DESC')->first(),
                'autoridad'    => Autoridad::getTipoAuthority('1'),
                'director'     => Autoridad::getTipoAuthority('2'),
                'list_comment' => Catchment::COLUMN_COMMENTS,
                'toDate'       => Date::now()->format('d F Y'),
            ])->render();

            // 1. Envío principal al destinatario (SIN CC/BCC en parámetros)
            $response = $this->emailService->send(
                $interview->email, 
                'Aceptación de Solicitud - Matrícula Escolar', 
                $htmlContent, 
                null, 
                false, 
                null,  // CC omitido intencionalmente
                null   // BCC omitido intencionalmente
            );

            if (! $response['success']) {
                $this->dispatchBrowserEvent('showAlert', [
                    'type' => 'error', 
                    'message' => 'Error al enviar correo principal: ' . $response['message']
                ]);
                return; // Detener si falla el envío principal
            }

            // 2. Envío independiente para CC (proceso separado)
            if (! empty($this->cc_mail) && filter_var($this->cc_mail, FILTER_VALIDATE_EMAIL)) {
                try {
                    $response_cc = $this->emailService->send(
                        $this->cc_mail,
                        'Aceptación de Solicitud - Matrícula Escolar [CC]',
                        $htmlContent,
                        null,
                        false,
                        null,
                        null
                    );
                    
                    if (! $response_cc['success']) {
                        Log::warning('SendPulseService: Fallo en envío CC', [
                            'interview_id' => $interviewId,
                            'cc_email' => $this->cc_mail,
                            'error' => $response_cc['message']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('SendPulseService: Excepción en envío CC', [
                        'interview_id' => $interviewId,
                        'cc_email' => $this->cc_mail,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // 3. Envío independiente para BCC (proceso separado por cada dirección)
            if (! empty($this->bcc_mail) && is_array($this->bcc_mail)) {
                foreach ($this->bcc_mail as $bcc_address) {
                    if (! empty($bcc_address) && filter_var($bcc_address, FILTER_VALIDATE_EMAIL)) {
                        try {
                            $response_bcc = $this->emailService->send(
                                $bcc_address,
                                'Aceptación de Solicitud - Matrícula Escolar [BCC]',
                                $htmlContent,
                                null,
                                false,
                                null,
                                null
                            );
                            
                            if (! $response_bcc['success']) {
                                Log::warning('SendPulseService: Fallo en envío BCC', [
                                    'interview_id' => $interviewId,
                                    'bcc_email' => $bcc_address,
                                    'error' => $response_bcc['message']
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('SendPulseService: Excepción en envío BCC', [
                                'interview_id' => $interviewId,
                                'bcc_email' => $bcc_address,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('showAlert', [
                'type' => 'error', 
                'message' => 'Error correo aceptación: ' . $e->getMessage()
            ]);
            Log::error('SendPulseService: Excepción crítica en sendAcceptanceEmail', [
                'interview_id' => $interviewId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function sendStandbyEmail($interviewId)
    {
        try {
            $interview   = CatchmentInterview::findOrFail($interviewId);
            $htmlContent = view('email.catchment.status_standby', [
                'interview'    => $interview,
                'institucion'  => Institucion::orderBy('created_at', 'DESC')->first(),
                'autoridad'    => Autoridad::getTipoAuthority('1'),
                'director'     => Autoridad::getTipoAuthority('2'),
                'list_comment' => Catchment::COLUMN_COMMENTS,
                'toDate'       => Date::now()->format('d F Y'),
            ])->render();

            // 1. Envío principal al destinatario (SIN CC/BCC en parámetros)
            $response = $this->emailService->send(
                $interview->email, 
                'Lista de Espera - Matrícula Escolar', 
                $htmlContent, 
                null, 
                false, 
                null,  // CC omitido intencionalmente
                null   // BCC omitido intencionalmente
            );

            if (! $response['success']) {
                $this->dispatchBrowserEvent('showAlert', [
                    'type' => 'error', 
                    'message' => 'Error al enviar correo principal: ' . $response['message']
                ]);
                return; // Detener si falla el envío principal
            }

            // 2. Envío independiente para CC (proceso separado)
            if (! empty($this->cc_mail) && filter_var($this->cc_mail, FILTER_VALIDATE_EMAIL)) {
                try {
                    $response_cc = $this->emailService->send(
                        $this->cc_mail,
                        'Lista de Espera - Matrícula Escolar [CC]',
                        $htmlContent,
                        null,
                        false,
                        null,
                        null
                    );
                    
                    if (! $response_cc['success']) {
                        Log::warning('SendPulseService: Fallo en envío CC (Standby)', [
                            'interview_id' => $interviewId,
                            'cc_email' => $this->cc_mail,
                            'error' => $response_cc['message']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('SendPulseService: Excepción en envío CC (Standby)', [
                        'interview_id' => $interviewId,
                        'cc_email' => $this->cc_mail,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // 3. Envío independiente para BCC (proceso separado por cada dirección)
            if (! empty($this->bcc_mail) && is_array($this->bcc_mail)) {
                foreach ($this->bcc_mail as $bcc_address) {
                    if (! empty($bcc_address) && filter_var($bcc_address, FILTER_VALIDATE_EMAIL)) {
                        try {
                            $response_bcc = $this->emailService->send(
                                $bcc_address,
                                'Lista de Espera - Matrícula Escolar [BCC]',
                                $htmlContent,
                                null,
                                false,
                                null,
                                null
                            );
                            
                            if (! $response_bcc['success']) {
                                Log::warning('SendPulseService: Fallo en envío BCC (Standby)', [
                                    'interview_id' => $interviewId,
                                    'bcc_email' => $bcc_address,
                                    'error' => $response_bcc['message']
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('SendPulseService: Excepción en envío BCC (Standby)', [
                                'interview_id' => $interviewId,
                                'bcc_email' => $bcc_address,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('showAlert', [
                'type' => 'error', 
                'message' => 'Error correo espera: ' . $e->getMessage()
            ]);
            Log::error('SendPulseService: Excepción crítica en sendStandbyEmail', [
                'interview_id' => $interviewId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->selectedInterview = null;
        $this->interviewData     = [];
        $this->statusNotify      = false;
    }
}
