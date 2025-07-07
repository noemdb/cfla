<?php

namespace App\Livewire;

use App\Models\VotingPoll;
use App\Models\VotingSession;
use App\Models\VotingVote;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VotingPollAsistent extends Component
{
    public $polls = [];
    public $currentPollIndex = 0;
    public $currentPoll = null;
    public $totalPolls = 0;
    public $selectedOption = null;
    public $hasVoted = false;
    public $isVoting = false;
    public $isCompleted = false;
    public $errorMessage = '';
    public $successMessage = '';
    public $fingerprint = '';
    public $privateIp = '';
    public $completedSessions;
    public $votedCount = 0;
    public $isLoadingFingerprint = true;
    public $fingerprintAttempts = 0;
    public $maxFingerprintAttempts = 3;

    // Modal properties
    public $showParticipationModal = false;
    public $selectedParticipation = null;

    protected $listeners = [
        'setDeviceFingerprint' => 'handleFingerprintData'
    ];

    public function mount()
    {
        $this->loadPolls();
    }

    public function handleFingerprintData($fingerprint, $privateIp = null)
    {
        $this->fingerprint = $fingerprint;
        $this->privateIp = $privateIp ?? 'unknown';
        $this->isLoadingFingerprint = false;
        $this->fingerprintAttempts = 0;

        if ($this->fingerprint) {
            $this->checkCurrentPollStatus();
            $this->successMessage = '';
            $this->errorMessage = '';
        } else {
            $this->handleFingerprintError('No se pudo generar el fingerprint del dispositivo');
        }
    }

    public function handleFingerprintError($message = '')
    {
        $this->fingerprintAttempts++;
        $this->isLoadingFingerprint = false;

        if ($this->fingerprintAttempts >= $this->maxFingerprintAttempts) {
            $this->errorMessage = 'No se pudo generar la identificación del dispositivo después de varios intentos. Por favor, recarga la página.';
        } else {
            $this->errorMessage = 'Error generando identificación del dispositivo. Reintentando...';
        }
    }

    public function retryFingerprintGeneration()
    {
        if ($this->fingerprintAttempts < $this->maxFingerprintAttempts) {
            $this->isLoadingFingerprint = true;
            $this->errorMessage = '';
        }
    }

    public function forceSetFingerprint()
    {
        $this->fingerprint = 'fallback_' . md5(request()->ip() . request()->userAgent() . time());
        $this->privateIp = request()->ip();
        $this->isLoadingFingerprint = false;
        $this->checkCurrentPollStatus();
    }

    public function loadPolls()
    {
        $activePolls = VotingPoll::where('enable', true)
            ->with('options')
            ->orderBy('created_at')
            ->get();

        $this->polls = $activePolls->map(function ($poll) {
            return [
                'id' => $poll->id,
                'title' => $poll->title,
                'description' => $poll->description,
                'options' => $poll->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'label' => $option->label
                    ];
                })->toArray()
            ];
        })->toArray();

        $this->totalPolls = count($this->polls);

        if ($this->totalPolls > 0) {
            $this->currentPoll = $this->polls[0];
            $this->currentPollIndex = 0;
        } else {
            $this->errorMessage = 'No hay encuestas activas en este momento.';
            $this->isCompleted = true;
        }
    }

    public function checkCurrentPollStatus()
    {
        if (!$this->currentPoll || !$this->fingerprint) {
            return;
        }

        $session = VotingSession::where('poll_id', $this->currentPoll['id'])
            ->where('fingerprint', $this->fingerprint)
            ->where('private_ip', $this->privateIp)
            ->first();

        $this->hasVoted = $session !== null;

        if ($this->hasVoted) {
            $this->successMessage = '¡Ya has participado en esta encuesta!';
            $this->selectedOption = null;
        } else {
            $this->successMessage = '';
            $this->errorMessage = '';
        }
    }

    public function selectOption($optionId)
    {
        if ($this->hasVoted || $this->isVoting || $this->isLoadingFingerprint) {
            return;
        }

        $this->selectedOption = $optionId;
        $this->errorMessage = '';
    }

    public function submitVote()
    {
        if (!$this->selectedOption || $this->hasVoted || $this->isVoting) {
            return;
        }

        if ($this->isLoadingFingerprint) {
            $this->errorMessage = 'Espera mientras se genera la identificación del dispositivo...';
            return;
        }

        if (!$this->fingerprint) {
            $this->errorMessage = 'Error: No se pudo generar la identificación del dispositivo. Intenta recargar la página.';
            return;
        }

        $this->isVoting = true;
        $this->errorMessage = '';

        try {
            $existingSession = VotingSession::where('poll_id', $this->currentPoll['id'])
                ->where('fingerprint', $this->fingerprint)
                ->where('private_ip', $this->privateIp)
                ->first();

            if ($existingSession) {
                $this->errorMessage = 'Ya has participado en esta encuesta.';
                $this->hasVoted = true;
                $this->isVoting = false;
                return;
            }

            $session = VotingSession::create([
                'poll_id' => $this->currentPoll['id'],
                'fingerprint' => $this->fingerprint,
                'private_ip' => $this->privateIp,
                'uuid' => Str::uuid(),
                'expires_at' => now()->addHours(24)
            ]);

            VotingVote::create([
                'session_uuid' => $session->uuid,
                'option_id' => $this->selectedOption,
                'poll_id' => $this->currentPoll['id']
            ]);

            $this->hasVoted = true;
            $this->successMessage = '¡Tu voto ha sido registrado exitosamente!';
            $this->selectedOption = null;
            $this->votedCount++;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al registrar el voto: ' . $e->getMessage();
            Log::error('Error en votación: ' . $e->getMessage(), [
                'fingerprint' => $this->fingerprint,
                'poll_id' => $this->currentPoll['id'],
                'option_id' => $this->selectedOption
            ]);
        } finally {
            $this->isVoting = false;
        }
    }

    public function nextPoll()
    {
        if ($this->currentPollIndex < $this->totalPolls - 1) {
            $this->currentPollIndex++;
            $this->currentPoll = $this->polls[$this->currentPollIndex];
            $this->resetPollState();
            $this->checkCurrentPollStatus();
        } else {
            $this->completePollAssistant();
        }
    }

    public function previousPoll()
    {
        if ($this->currentPollIndex > 0) {
            $this->currentPollIndex--;
            $this->currentPoll = $this->polls[$this->currentPollIndex];
            $this->resetPollState();
            $this->checkCurrentPollStatus();
        }
    }

    public function skipPoll()
    {
        $this->nextPoll();
    }

    private function resetPollState()
    {
        $this->selectedOption = null;
        $this->hasVoted = false;
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->isVoting = false;
    }

    private function completePollAssistant()
    {
        $this->isCompleted = true;

        // Cargar sesiones completadas con relaciones para mostrar en el resumen
        if ($this->fingerprint) {
            $this->completedSessions = VotingSession::where('fingerprint', $this->fingerprint)
                ->where('private_ip', $this->privateIp)
                ->with(['poll', 'votes.option'])
                ->get();

            $this->votedCount = $this->completedSessions->count();
        }
    }

    /**
     * Mostrar modal con detalles de participación
     */
    public function showParticipationDetails($sessionId)
    {
        $this->selectedParticipation = $this->completedSessions->find($sessionId);

        if ($this->selectedParticipation) {
            $this->showParticipationModal = true;
        }
    }

    /**
     * Cerrar modal de participación
     */
    public function closeParticipationModal()
    {
        $this->showParticipationModal = false;
        $this->selectedParticipation = null;
    }

    /**
     * Generar código QR para una sesión de votación
     */
    public function generateQRCode($uuid)
    {
        try {
            $url = route('poll.participation.show', $uuid);
            return QrCode::size(144)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->generate($url);
        } catch (\Exception $e) {
            // Fallback si no se puede generar el QR
            return '<div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center border">
                        <div class="text-xs text-gray-800 font-mono">QR</div>
                    </div>';
        }
    }

    /**
     * Generar código QR grande para el modal
     */
    public function generateLargeQRCode($uuid)
    {
        try {
            $url = route('poll.participation.show', $uuid);
            return QrCode::size(200)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->generate($url);
        } catch (\Exception $e) {
            // Fallback si no se puede generar el QR
            return '<div class="w-48 h-48 bg-white rounded-lg flex items-center justify-center border">
                        <div class="text-lg text-gray-800 font-mono">QR Code</div>
                    </div>';
        }
    }

    public function render()
    {
        return view('livewire.voting-poll-asistent');
    }
}
