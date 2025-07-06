<?php

namespace App\Livewire;

use App\Models\VotingPoll;
use App\Models\VotingSession;
use App\Models\VotingVote;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VotingPollAsistent extends Component
{
    public $polls;
    public $currentPollIndex = 0;
    public $selectedOption = null;
    public $isVoting = false;
    public $hasVoted = false;
    public $voteMessage = '';
    public $voteMessageType = 'success';
    public $isCompleted = false;
    public $completedSessions = [];
    public $currentPoll = null;
    public $deviceFingerprint = null;
    public $deviceIp = null;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount($polls)
    {
        $this->polls = $polls;
        $this->initializeDevice();
        $this->loadCurrentPoll();
    }

    private function initializeDevice()
    {
        $this->deviceIp = request()->ip();
        $this->deviceFingerprint = $this->generateSecureFingerprint();

        Log::info('Device initialized', [
            'ip' => $this->deviceIp,
            'fingerprint' => substr($this->deviceFingerprint, 0, 8) . '...',
            'user_agent' => request()->userAgent()
        ]);
    }

    public function loadCurrentPoll()
    {
        if ($this->currentPollIndex < $this->polls->count()) {
            $this->currentPoll = $this->polls[$this->currentPollIndex];
            $this->checkExistingVote();
        } else {
            $this->isCompleted = true;
        }
    }

    public function checkExistingVote()
    {
        if (!$this->currentPoll || !$this->deviceFingerprint) {
            return;
        }

        try {
            // Verificar si el dispositivo ya votó en esta encuesta específica
            $hasVoted = VotingSession::hasVotedInPoll(
                $this->currentPoll->id,
                $this->deviceFingerprint,
                $this->deviceIp
            );

            if ($hasVoted) {
                $this->hasVoted = true;
                $this->voteMessage = 'Ya has participado en esta encuesta desde este dispositivo.';
                $this->voteMessageType = 'info';

                // Obtener la sesión existente para estadísticas
                $existingSession = VotingSession::where('poll_id', $this->currentPoll->id)
                    ->where(function ($query) {
                        $query->where('fingerprint', $this->deviceFingerprint)
                            ->orWhere('ip', $this->deviceIp);
                    })
                    ->where('voted', true)
                    ->first();

                if ($existingSession) {
                    $this->completedSessions[] = $existingSession;
                }
            } else {
                // Verificar si la encuesta está activa
                if (!$this->currentPoll->isActive()) {
                    $this->hasVoted = true;
                    $this->voteMessage = 'Esta encuesta no está activa actualmente.';
                    $this->voteMessageType = 'warning';
                } else {
                    $this->hasVoted = false;
                    $this->voteMessage = '';
                }
            }

            Log::info('Vote check completed', [
                'poll_id' => $this->currentPoll->id,
                'has_voted' => $this->hasVoted,
                'is_active' => $this->currentPoll->isActive()
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking existing vote', [
                'poll_id' => $this->currentPoll->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->voteMessage = 'Error al verificar el estado de votación.';
            $this->voteMessageType = 'error';
        }
    }

    public function selectOption($optionId)
    {
        if ($this->hasVoted || $this->isVoting || !$this->currentPoll) {
            return;
        }

        // Verificar que la opción pertenece a la encuesta actual
        $option = $this->currentPoll->options()->find($optionId);
        if (!$option) {
            $this->voteMessage = 'Opción de voto inválida.';
            $this->voteMessageType = 'error';
            return;
        }

        $this->selectedOption = $optionId;
        $this->voteMessage = '';
    }

    public function submitVote()
    {
        if (!$this->selectedOption || $this->hasVoted || $this->isVoting || !$this->currentPoll) {
            return;
        }

        $this->isVoting = true;
        $this->voteMessage = 'Procesando voto...';
        $this->voteMessageType = 'info';

        try {
            // Usar transacción para asegurar consistencia
            DB::transaction(function () {
                // Verificación final de unicidad antes de votar
                $hasVoted = VotingSession::hasVotedInPoll(
                    $this->currentPoll->id,
                    $this->deviceFingerprint,
                    $this->deviceIp
                );

                if ($hasVoted) {
                    throw new \Exception('Ya has participado en esta encuesta desde este dispositivo.');
                }

                // Verificar que la encuesta sigue activa
                if (!$this->currentPoll->isActive()) {
                    throw new \Exception('La encuesta ya no está activa.');
                }

                // Verificar que la opción es válida
                $option = $this->currentPoll->options()->find($this->selectedOption);
                if (!$option) {
                    throw new \Exception('Opción de voto inválida.');
                }

                // Crear o recuperar sesión para este dispositivo
                $session = VotingSession::createOrRetrieveForDevice(
                    $this->currentPoll->id,
                    $this->deviceFingerprint,
                    $this->deviceIp,
                    request()->userAgent()
                );

                // Verificar una vez más que no haya votado (por si acaso)
                if ($session->voted) {
                    throw new \Exception('Esta sesión ya ha sido utilizada para votar.');
                }

                // Registrar el voto
                VotingVote::create([
                    'session_uuid' => $session->uuid,
                    'option_id' => $this->selectedOption,
                ]);

                // Marcar la sesión como votada
                $session->update(['voted' => true]);

                // Guardar la sesión completada
                $this->completedSessions[] = $session;

                Log::info('Vote submitted successfully', [
                    'poll_id' => $this->currentPoll->id,
                    'option_id' => $this->selectedOption,
                    'session_uuid' => $session->uuid,
                    'device_fingerprint' => substr($this->deviceFingerprint, 0, 8) . '...'
                ]);
            });

            $this->hasVoted = true;
            $this->voteMessage = '¡Voto registrado exitosamente!';
            $this->voteMessageType = 'success';
            $this->selectedOption = null;
        } catch (\Exception $e) {
            $this->voteMessage = $e->getMessage();
            $this->voteMessageType = 'error';

            Log::error('Error submitting vote', [
                'poll_id' => $this->currentPoll->id ?? null,
                'option_id' => $this->selectedOption,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->isVoting = false;
        }
    }

    public function nextPoll()
    {
        $this->currentPollIndex++;
        $this->resetPollState();
        $this->loadCurrentPoll();
    }

    public function previousPoll()
    {
        if ($this->currentPollIndex > 0) {
            $this->currentPollIndex--;
            $this->resetPollState();
            $this->loadCurrentPoll();
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
        $this->voteMessage = '';
        $this->isVoting = false;
    }

    public function generateQRCode($sessionUuid)
    {
        try {
            $participationUrl = route('poll.participation.show', ['uuid' => $sessionUuid]);

            return QrCode::size(150)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->margin(1)
                ->generate($participationUrl);
        } catch (\Exception $e) {
            Log::error('Error generating QR code', [
                'session_uuid' => $sessionUuid,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generar fingerprint seguro y único para el dispositivo
     */
    private function generateSecureFingerprint()
    {
        $userAgent = request()->userAgent() ?? '';
        $acceptLanguage = request()->header('Accept-Language', '');
        $acceptEncoding = request()->header('Accept-Encoding', '');
        $ip = request()->ip() ?? '';

        // Agregar timestamp del día para permitir un voto por día si es necesario
        // $dailySalt = date('Y-m-d');

        // Crear fingerprint único basado en características del dispositivo
        $fingerprintData = implode('|', [
            $userAgent,
            $acceptLanguage,
            $acceptEncoding,
            $ip,
            // $dailySalt // Descomentar si se quiere permitir un voto por día
        ]);

        return hash('sha256', $fingerprintData);
    }

    /**
     * Obtener estadísticas de la encuesta actual
     */
    public function getCurrentPollStats()
    {
        if (!$this->currentPoll) {
            return null;
        }

        return [
            'total_votes' => $this->currentPoll->votes_count ?? 0,
            'unique_participants' => $this->currentPoll->getUniqueParticipantsCount(),
            'is_active' => $this->currentPoll->isActive(),
            'time_remaining' => $this->currentPoll->time_remaining,
        ];
    }

    public function render()
    {
        return view('livewire.voting-poll-asistent', [
            'pollStats' => $this->getCurrentPollStats()
        ]);
    }
}
