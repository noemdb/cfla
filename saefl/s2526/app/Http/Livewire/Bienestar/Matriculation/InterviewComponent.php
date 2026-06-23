<?php

namespace App\Http\Livewire\Bienestar\Matriculation;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Services\ResendEmailService;
use App\Services\SendPulseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;

class InterviewComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Propiedades públicas
    public $representant_ci = '';
    public $search = '';
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Nuevos filtros de estado
    public $filterAccepted = '';
    public $filterStatusNotified = '';
    public $filterStatusStandby = '';

    // Propiedades para modales y estados
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedInterview = null;
    public $deleteInterviewId = null;
    public $deleteInterviewName = '';

    // Propiedades para edición
    public $interviewData = [];
    public $statusNotify = false;

    // Configuración de email
    protected const EMAIL_PROVIDER = 'sendpulse';
    protected $emailService;
    protected $cc_mail, $bcc_mail;

    // Listeners para eventos
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'deleteConfirmed' => 'deleteInterview'
    ];

    public function mount()
    {
        $this->emailService = $this->getEmailService();
        $this->cc_mail = env('MAIL_CC_ADDRESS_CONTROL', null);
        $this->bcc_mail = [
            env('MAIL_CC_ADDRESS', null),
            env('MAIL_CC_ADDRESS_BIENESTAR', null),
        ];
    }

    protected function getEmailService(?string $provider = null)
    {
        $provider = $provider ?? self::EMAIL_PROVIDER;

        return match ($provider) {
            'resend' => app(ResendEmailService::class),
            default => app(SendPulseService::class),
        };
    }

    public function render()
    {
        $catchment_interviews = $this->getInterviews();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;
        $list_grade = CatchmentInterview::list_grade();
        $list_religions = CatchmentInterview::list_religions();

        // Estadísticas para mostrar en la interfaz
        $statistics = $this->getStatistics();

        return view('livewire.bienestar.matriculation.interview-component', [
            'catchment_interviews' => $catchment_interviews,
            'list_comment' => $list_comment,
            'list_grade' => $list_grade,
            'list_religions' => $list_religions,
            'statistics' => $statistics
        ]);
    }

    protected function getInterviews()
    {
        $query = CatchmentInterview::query()
            ->select('catchment_interviews.*')
            ->with(['catchment.catchment_group.activities', 'grado.pestudio']);

        // Filtro por cédula del representante
        if (!empty($this->representant_ci)) {
            $query->where('identification_number', 'like', '%' . $this->representant_ci . '%');
        }

        // Búsqueda general
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('student_full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('identification_number', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por estado de aceptación
        if ($this->filterAccepted !== '') {
            $query->where('accepted', $this->filterAccepted);
        }

        // Filtro por estado de notificación
        if ($this->filterStatusNotified !== '') {
            $query->where('status_notified', $this->filterStatusNotified);
        }

        // Filtro por estado de espera
        if ($this->filterStatusStandby !== '') {
            $query->where('status_standby', $this->filterStatusStandby);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)
                    ->paginate($this->perPage);
    }

    protected function getStatistics()
    {
        $baseQuery = CatchmentInterview::query();

        // Aplicar filtros de búsqueda si existen
        if (!empty($this->representant_ci)) {
            $baseQuery->where('identification_number', 'like', '%' . $this->representant_ci . '%');
        }

        if (!empty($this->search)) {
            $baseQuery->where(function ($q) {
                $q->where('student_full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('identification_number', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'accepted' => (clone $baseQuery)->where('accepted', true)->count(),
            'standby' => (clone $baseQuery)->where('status_standby', true)->count(),
            'notified' => (clone $baseQuery)->where('status_notified', true)->count(),
            'pending' => (clone $baseQuery)->where('accepted', false)->where('status_standby', false)->count(),
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRepresentantCi()
    {
        $this->resetPage();
    }

    public function updatingFilterAccepted()
    {
        $this->resetPage();
    }

    public function updatingFilterStatusNotified()
    {
        $this->resetPage();
    }

    public function updatingFilterStatusStandby()
    {
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
        $this->representant_ci = '';
        $this->search = '';
        $this->filterAccepted = '';
        $this->filterStatusNotified = '';
        $this->filterStatusStandby = '';
        $this->resetPage();
    }

    // Métodos de filtro rápido
    public function filterByAccepted()
    {
        $this->filterAccepted = '1';
        $this->filterStatusStandby = '';
        $this->resetPage();
    }

    public function filterByStandby()
    {
        $this->filterStatusStandby = '1';
        $this->filterAccepted = '';
        $this->resetPage();
    }

    public function filterByNotified()
    {
        $this->filterStatusNotified = '1';
        $this->resetPage();
    }

    public function filterByPending()
    {
        $this->filterAccepted = '0';
        $this->filterStatusStandby = '0';
        $this->resetPage();
    }

    public function editInterview($id)
    {
        $this->selectedInterview = CatchmentInterview::findOrFail($id);
        $this->interviewData = $this->selectedInterview->toArray();
        $this->statusNotify = false;
        $this->showEditModal = true;
    }

    public function updateInterview()
    {
        $this->validate([
            'interviewData.accepted' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && $this->interviewData['status_standby']) {
                        $fail('No puede aceptar y poner en espera una solicitud de matrícula al mismo tiempo.');
                    }
                }
            ],
            'interviewData.status_standby' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && $this->interviewData['accepted']) {
                        $fail('No se puede poner en espera y aceptar una solicitud de matrícula al mismo tiempo.');
                    }
                }
            ],
        ]);

        DB::beginTransaction();
        try {
            $this->selectedInterview->fill($this->interviewData);
            $this->selectedInterview->save();

            $message = 'Entrevista actualizada correctamente.';

            // Procesar aceptación
            if ($this->selectedInterview->accepted == true) {
                $this->selectedInterview->generateToken();
                $this->selectedInterview->save();

                if ($this->statusNotify) {
                    $this->sendAcceptanceEmail($this->selectedInterview->id);
                }

                $response = $this->insertEstudiantRepresentanAccepted($this->selectedInterview->id);
                $responseData = json_decode($response->getContent(), true);

                if (isset($responseData['message'])) {
                    $message .= ' ' . $responseData['message'];
                } else {
                    $message .= ' ' . ($responseData['error'] ?? 'Error desconocido');
                }
            }

            // Procesar lista de espera
            if ($this->selectedInterview->status_standby == true && $this->statusNotify) {
                $this->sendStandbyEmail($this->selectedInterview->id);
            }

            // Actualizar estado de notificación
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
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al actualizar la entrevista: ' . $e->getMessage()]);
        }
    }

    public function confirmDelete($id, $name)
    {
        $interview = CatchmentInterview::find($id);

        if ($interview && $interview->accepted) {
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'No se puede eliminar una entrevista que ya ha sido aceptada.']);
            return;
        }

        $this->deleteInterviewId = $id;
        $this->deleteInterviewName = $name;
        $this->dispatchBrowserEvent('confirmDelete', ['id' => $id, 'name' => $name]);
    }

    public function deleteInterview()
    {
        if (!$this->deleteInterviewId) {
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'No se ha seleccionado ninguna entrevista para eliminar.']);
            return;
        }

        DB::beginTransaction();
        try {
            $interview = CatchmentInterview::findOrFail($this->deleteInterviewId);

            if ($interview->accepted) {
                throw new \Exception('No se puede eliminar una entrevista que ya ha sido aceptada.');
            }

            $studentName = $interview->student_full_name;
            $interview->delete();
            DB::commit();

            $this->dispatchBrowserEvent('showAlert', ['type' => 'success', 'message' => "Entrevista de {$studentName} eliminada correctamente."]);

            // Limpiar variables
            $this->deleteInterviewId = null;
            $this->deleteInterviewName = '';

            // Refrescar la lista
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al eliminar la entrevista: ' . $e->getMessage()]);
        }
    }

    protected function insertEstudiantRepresentanAccepted($id)
    {
        try {
            $interview = CatchmentInterview::findOrFail($id);

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

            // Crear Estudiante
            $full_name = preg_replace('/\s+/', ' ', trim($interview->student_full_name));
            [$est_name, $est_lastname] = $this->splitName($full_name);
            $est_name = mb_strtoupper($this->normalizeName($est_name), 'UTF-8');
            $est_lastname = mb_strtoupper($this->normalizeName($est_lastname), 'UTF-8');

            $estudiant = Estudiant::firstOrNew([
                'name' => $est_name,
                'lastname' => $est_lastname,
                'date_birth' => $interview->date_of_birth,
            ]);

            $estudiant->fill([
                'planpago_id' => 1,
                'grado_inicial_id' => $interview->grade_year_aspiring ?? 1,
                'seccion_inicial' => '1',
                'type_ci_id' => 1,
                'ci_estudiant' => 'TEMP-' . Str::random(6),
                'ci_estudiant_temp' => 'TEMP-' . Str::random(6),
                'gender' => 'Masculino',
                'email' => $interview->email,
                'representant_ci' => $representant->ci_representant,
                'representant_id' => $representant->id,
                'status_active' => 'true',
                'token' => Str::uuid(),
                'status_blacklist' => 'false',
                'status_notice' => 1,
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
            $interview = CatchmentInterview::findOrFail($interviewId);
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

            $subject = 'Aceptación de Solicitud - Matrícula Escolar';
            $response = $this->emailService->send(
                $interview->email,
                $subject,
                $htmlContent,
                null,
                false,
                $this->cc_mail,
                $this->bcc_mail
            );

            if (!$response['success']) {
                $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al enviar correo: ' . $response['message']]);
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al enviar correo de aceptación: ' . $e->getMessage()]);
        }
    }

    protected function sendStandbyEmail($interviewId)
    {
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);
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

            $subject = 'Lista de Espera - Matrícula Escolar';
            $response = $this->emailService->send(
                $interview->email,
                $subject,
                $htmlContent,
                null,
                false,
                $this->cc_mail,
                $this->bcc_mail
            );

            if (!$response['success']) {
                $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al enviar correo: ' . $response['message']]);
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('showAlert', ['type' => 'error', 'message' => 'Error al enviar correo de lista de espera: ' . $e->getMessage()]);
        }
    }

    private function splitName($fullName)
    {
        $parts = explode(' ', trim($fullName), 2);
        $first = $parts[0];
        $last = $parts[1] ?? '';
        return [$first, $last];
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
        $this->interviewData = [];
        $this->statusNotify = false;
    }
}
