<?php

namespace App\Livewire;

use App\Models\VotingPoll as Poll;
use App\Models\VotingSession;
use App\Models\VotingVote;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use WireUi\Traits\Actions;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VotingPoll extends Component
{
    use Actions;

    // Propiedades públicas simples (solo tipos básicos)
    public $poll;
    public string $accessToken = '';
    public ?int $selectedOption = null;
    public bool $hasVoted = false;
    public bool $isLoading = false;
    public ?string $timeRemaining = null;
    public ?string $errorState = null;
    public bool $canVote = true;
    public bool $showQRCode = false;
    public ?string $qrCodeSvg = null;
    public ?string $participationUrl = null;
    public ?string $sessionUuid = null;

    // Estados de error posibles
    const ERROR_POLL_NOT_FOUND = 'poll_not_found';
    const ERROR_POLL_INACTIVE = 'poll_inactive';
    const ERROR_POLL_EXPIRED = 'poll_expired';
    const ERROR_ALREADY_VOTED = 'already_voted';
    const ERROR_SESSION_INVALID = 'session_invalid';
    const ERROR_NO_OPTIONS = 'no_options';
    const ERROR_POLL_DELETED = 'poll_deleted';
    const ERROR_NETWORK = 'network_error';

    protected $rules = [
        'selectedOption' => 'required|exists:voting_options,id',
    ];

    protected $messages = [
        'selectedOption.required' => 'Debes seleccionar una opción para votar.',
        'selectedOption.exists' => 'La opción seleccionada no es válida.',
    ];

    public function mount($accessToken)
    {
        try {
            // Validar que el token no esté vacío
            if (empty($accessToken)) {
                throw new Exception('Token de acceso vacío');
            }

            $this->accessToken = (string) $accessToken;

            Log::info('VotingPoll component mounted', [
                'token' => $this->accessToken,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $this->initializePoll();

        } catch (Exception $e) {
            Log::error('Error mounting VotingPoll component: ' . $e->getMessage(), [
                'token' => $accessToken ?? 'unknown',
                'error_trace' => $e->getTraceAsString()
            ]);

            $this->handleCriticalError($e);
        }
    }

    private function initializePoll()
    {
        try {
            // Cargar la encuesta
            if (!$this->loadPoll()) {
                return;
            }

            // Verificar estado de la encuesta
            if (!$this->validatePollState()) {
                return;
            }

            // Verificar sesión existente
            $this->checkExistingSession();

            // Generar fingerprint si es necesario
            if (!$this->hasVoted && $this->canVote) {
                $this->generateFingerprint();
            }

            // Actualizar tiempo restante
            $this->updateTimeRemaining();

        } catch (Exception $e) {
            Log::error('Error initializing poll: ' . $e->getMessage(), [
                'token' => $this->accessToken,
                'poll_id' => $this->getPoll()?->id ?? 'unknown'
            ]);

            $this->handleCriticalError($e);
        }
    }

    // Método para obtener la encuesta (no como propiedad pública)
    private function getPoll()
    {
        return Poll::with('options')
            ->where('access_token', $this->accessToken)
            ->first();
    }

    public function loadPoll()
    {
        try {
            $poll = $this->getPoll();

            if (!$poll) {
                $this->setErrorState(self::ERROR_POLL_NOT_FOUND);
                $this->notification()->error(
                    'Encuesta no encontrada',
                    'La encuesta que buscas no existe, ha sido eliminada o el enlace es incorrecto.'
                );

                Log::warning('Poll not found', ['token' => $this->accessToken]);
                return false;
            }

            // Verificar si la encuesta tiene opciones
            if ($poll->options->isEmpty()) {
                $this->setErrorState(self::ERROR_NO_OPTIONS);
                $this->notification()->error(
                    'Encuesta sin opciones',
                    'Esta encuesta no tiene opciones disponibles para votar.'
                );

                Log::warning('Poll has no options', [
                    'poll_id' => $poll->id,
                    'token' => $this->accessToken
                ]);
                return false;
            }

            Log::info('Poll loaded successfully', [
                'poll_id' => $poll->id,
                'poll_title' => $poll->title,
                'options_count' => $poll->options->count()
            ]);

            $this->poll = $poll;

            return true;

        } catch (Exception $e) {
            Log::error('Error loading poll: ' . $e->getMessage(), [
                'token' => $this->accessToken,
                'error_trace' => $e->getTraceAsString()
            ]);

            $this->setErrorState(self::ERROR_NETWORK);
            $this->notification()->error(
                'Error de conexión',
                'No se pudo cargar la encuesta. Por favor, verifica tu conexión e intenta nuevamente.'
            );
            return false;
        }
    }

    private function validatePollState()
    {
        try {
            $poll = $this->getPoll();
            if (!$poll) return false;

            // Verificar si la encuesta está activa
            if (!$poll->enable) {
                $this->setErrorState(self::ERROR_POLL_INACTIVE);
                $this->notification()->warning(
                    'Encuesta inactiva',
                    'Esta encuesta no está activa en este momento. Contacta al administrador para más información.'
                );

                Log::info('Poll is inactive', [
                    'poll_id' => $poll->id,
                    'poll_title' => $poll->title
                ]);
                return false;
            }

            // Verificar si la encuesta ha expirado
            if ($poll->isExpired()) {
                $poll->update(['enable' => false]);
                $this->setErrorState(self::ERROR_POLL_EXPIRED);
                $this->notification()->error(
                    'Encuesta expirada',
                    'El tiempo para participar en esta encuesta ha terminado.'
                );

                Log::info('Poll expired', [
                    'poll_id' => $poll->id,
                    'poll_title' => $poll->title
                ]);
                return false;
            }

            return true;

        } catch (Exception $e) {
            Log::error('Error validating poll state: ' . $e->getMessage(), [
                'poll_id' => $this->getPoll()?->id ?? 'unknown'
            ]);

            $this->setErrorState(self::ERROR_NETWORK);
            return false;
        }
    }

    public function checkExistingSession()
    {
        try {
            $poll = $this->getPoll();
            if (!$poll) return;

            $sessionId = session('vote_session_id');

            if (!$sessionId) {
                return;
            }

            $decryptedSessionId = Crypt::decryptString($sessionId);
            $session = VotingSession::where('uuid', $decryptedSessionId)
                ->where('poll_id', $poll->id)
                ->first();

            if (!$session) {
                // Sesión no encontrada, limpiar
                session()->forget('vote_session_id');
                Log::info('Invalid session cleared', ['session_id' => $decryptedSessionId]);
                return;
            }

            // Verificar si la sesión ha expirado
            if ($session->isExpired()) {
                $this->setErrorState(self::ERROR_SESSION_INVALID);
                $this->notification()->warning(
                    'Sesión expirada',
                    'Tu sesión ha expirado. La página se recargará automáticamente.'
                );
                session()->forget('vote_session_id');
                $this->dispatch('refresh-page');

                Log::info('Expired session detected', [
                    'session_uuid' => $session->uuid,
                    'poll_id' => $poll->id
                ]);
                return;
            }

            if ($session->voted) {
                $this->hasVoted = true;
                $this->canVote = false;
                $this->setErrorState(self::ERROR_ALREADY_VOTED);

                // Configurar QR Code para mostrar
                $this->sessionUuid = $session->uuid;
                $this->generateQRCode($session->uuid);

                $this->notification()->info(
                    'Ya has participado',
                    '¡Gracias! Ya has votado en esta encuesta. Tu voto fue registrado exitosamente.'
                );

                Log::info('User already voted', [
                    'session_uuid' => $session->uuid,
                    'poll_id' => $poll->id
                ]);
            }

            // Guardar solo el UUID como string en sesión
            session(['current_session_uuid' => $session->uuid]);

        } catch (Exception $e) {
            Log::error('Error checking existing session: ' . $e->getMessage(), [
                'poll_id' => $this->getPoll()?->id ?? 'unknown',
                'error_trace' => $e->getTraceAsString()
            ]);

            // Error al desencriptar o verificar sesión
            session()->forget('vote_session_id');
            $this->notification()->warning(
                'Sesión inválida',
                'Se detectó una sesión inválida. Se creará una nueva sesión automáticamente.'
            );
        }
    }

    public function generateFingerprint()
    {
        try {
            $poll = $this->getPoll();
            if (!$poll || $this->hasVoted || !$this->canVote) {
                return;
            }

            $ip = request()->ip();
            $userAgent = request()->userAgent();

            // Verificar si ya existe una sesión para esta IP
            $existingSession = VotingSession::where('poll_id', $poll->id)
                ->where('ip', $ip)
                ->first();

            if ($existingSession) {
                if ($existingSession->voted) {
                    $this->hasVoted = true;
                    $this->canVote = false;
                    $this->setErrorState(self::ERROR_ALREADY_VOTED);

                    // Configurar QR Code para mostrar
                    $this->sessionUuid = $existingSession->uuid;
                    $this->generateQRCode($existingSession->uuid);

                    $this->notification()->info(
                        'Ya has participado',
                        'Desde esta conexión ya se ha votado en esta encuesta.'
                    );

                    Log::info('IP already voted', [
                        'ip' => $ip,
                        'poll_id' => $poll->id,
                        'session_uuid' => $existingSession->uuid
                    ]);
                    return;
                }

                $encryptedSessionId = Crypt::encryptString($existingSession->uuid);
                session(['vote_session_id' => $encryptedSessionId]);
                session(['current_session_uuid' => $existingSession->uuid]);

                Log::info('Existing session found', [
                    'session_uuid' => $existingSession->uuid,
                    'poll_id' => $poll->id
                ]);
                return;
            }

            // Crear nueva sesión
            $session = VotingSession::create([
                'ip' => $ip,
                'fingerprint' => $this->generateBrowserFingerprint(),
                'user_agent' => $userAgent,
                'poll_id' => $poll->id,
            ]);

            $encryptedSessionId = Crypt::encryptString($session->uuid);
            session(['vote_session_id' => $encryptedSessionId]);
            session(['current_session_uuid' => $session->uuid]);

            Log::info('New session created', [
                'session_uuid' => $session->uuid,
                'poll_id' => $poll->id,
                'ip' => $ip
            ]);

        } catch (Exception $e) {
            Log::error('Error generating fingerprint: ' . $e->getMessage(), [
                'poll_id' => $this->getPoll()?->id ?? 'unknown',
                'ip' => request()->ip(),
                'error_trace' => $e->getTraceAsString()
            ]);

            $this->setErrorState(self::ERROR_NETWORK);
            $this->notification()->error(
                'Error de sesión',
                'No se pudo crear la sesión de votación. Intenta recargar la página.'
            );
        }
    }

    private function generateBrowserFingerprint()
    {
        return hash('sha256',
            request()->ip() .
            request()->userAgent() .
            request()->header('Accept-Language', '') .
            date('Y-m-d')
        );
    }

    private function generateQRCode($sessionUuid)
    {
        try {
            $this->participationUrl = route('poll.participation.show', ['uuid' => $sessionUuid]);

            $this->qrCodeSvg = QrCode::size(200)
                ->margin(2)
                ->generate($this->participationUrl);

            $this->showQRCode = true;

        } catch (Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage(), [
                'session_uuid' => $sessionUuid
            ]);
        }
    }

    public function vote()
    {
        try {
            // Verificaciones previas
            if (!$this->preVoteValidation()) {
                return;
            }

            $this->validate();
            $this->isLoading = true;

            $poll = $this->getPoll();
            if (!$poll) {
                throw new Exception('Encuesta no encontrada');
            }

            // Recargar la encuesta para verificar estado actual
            $poll->refresh();

            // Verificar que la encuesta sigue activa
            if (!$poll->enable) {
                throw new Exception('La encuesta ha sido desactivada durante el proceso de votación.');
            }

            // Verificar expiración nuevamente
            if ($poll->isExpired()) {
                $poll->update(['enable' => false]);
                throw new Exception('La encuesta ha expirado durante el proceso de votación.');
            }

            // Verificar que la opción existe y pertenece a esta encuesta
            $option = $poll->options()->find($this->selectedOption);
            if (!$option) {
                throw new Exception('La opción seleccionada ya no está disponible.');
            }

            // Obtener UUID de sesión desde session
            $sessionUuid = session('current_session_uuid');
            if (!$sessionUuid) {
                throw new Exception('Sesión inválida. Recarga la página e intenta nuevamente.');
            }

            $session = VotingSession::where('uuid', $sessionUuid)
                ->where('poll_id', $poll->id)
                ->first();

            if (!$session) {
                throw new Exception('Tu sesión no fue encontrada. Recarga la página.');
            }

            if ($session->voted) {
                throw new Exception('Ya has votado en esta encuesta.');
            }

            if ($session->isExpired()) {
                throw new Exception('Tu sesión ha expirado. Recarga la página.');
            }

            // Verificar que no exista ya un voto para esta sesión
            $existingVote = VotingVote::where('session_uuid', $session->uuid)->first();
            if ($existingVote) {
                throw new Exception('Ya existe un voto registrado para esta sesión.');
            }

            // Registrar el voto
            VotingVote::create([
                'session_uuid' => $session->uuid,
                'option_id' => $option->id,
            ]);

            // Marcar sesión como votada
            $session->update(['voted' => true]);

            $this->hasVoted = true;
            $this->canVote = false;
            $this->selectedOption = null;

            // Generar QR Code
            $this->sessionUuid = $session->uuid;
            $this->generateQRCode($session->uuid);

            Log::info('Vote registered successfully', [
                'session_uuid' => $session->uuid,
                'poll_id' => $poll->id,
                'option_id' => $option->id,
                'ip' => request()->ip()
            ]);

            $this->notification()->success(
                '¡Voto registrado exitosamente!',
                'Tu voto ha sido registrado de forma segura y anónima. Aquí tienes tu código QR de participación.'
            );

        } catch (ModelNotFoundException $e) {
            Log::error('Model not found during voting: ' . $e->getMessage(), [
                'poll_id' => $this->getPoll()?->id ?? 'unknown',
                'selected_option' => $this->selectedOption,
                'session_uuid' => session('current_session_uuid')
            ]);

            $this->notification()->error(
                'Elemento no encontrado',
                'La opción seleccionada o la encuesta ya no están disponibles.'
            );
        } catch (Exception $e) {
            Log::error('Error during voting: ' . $e->getMessage(), [
                'poll_id' => $this->getPoll()?->id ?? 'unknown',
                'selected_option' => $this->selectedOption,
                'session_uuid' => session('current_session_uuid'),
                'error_trace' => $e->getTraceAsString()
            ]);

            $this->notification()->error(
                'Error al registrar el voto',
                $e->getMessage()
            );
        } finally {
            $this->isLoading = false;
        }
    }

    private function preVoteValidation()
    {
        if ($this->hasVoted) {
            $this->notification()->warning(
                'Ya has votado',
                'Ya has participado en esta encuesta.'
            );
            return false;
        }

        if (!$this->canVote) {
            $this->notification()->error(
                'No puedes votar',
                'No tienes permisos para votar en esta encuesta.'
            );
            return false;
        }

        $poll = $this->getPoll();
        if (!$poll) {
            $this->notification()->error(
                'Encuesta no disponible',
                'La encuesta no está disponible en este momento.'
            );
            return false;
        }

        if (!$poll->enable) {
            $this->notification()->error(
                'Encuesta inactiva',
                'Esta encuesta no está activa en este momento.'
            );
            return false;
        }

        return true;
    }

    public function selectOption($optionId)
    {
        try {
            if ($this->hasVoted || !$this->canVote) {
                return;
            }

            $poll = $this->getPoll();
            // Verificar que la opción existe
            if (!$poll || !$poll->options->contains('id', $optionId)) {
                $this->notification()->error(
                    'Opción inválida',
                    'La opción seleccionada no es válida.'
                );
                return;
            }

            $this->selectedOption = $optionId;
            $this->resetValidation();

        } catch (Exception $e) {
            Log::error('Error selecting option: ' . $e->getMessage(), [
                'option_id' => $optionId,
                'poll_id' => $this->getPoll()?->id ?? 'unknown'
            ]);
        }
    }

    public function updateTimeRemaining()
    {
        $poll = $this->getPoll();
        if (!$poll || !$poll->enable || !$poll->date) {
            $this->timeRemaining = null;
            return;
        }

        try {
            $startTime = $poll->date;
            $endTime = $startTime->copy()->addMinutes($poll->time_active);
            $now = now();

            if ($now->greaterThan($endTime)) {
                $this->timeRemaining = 'Expirada';

                if ($poll->enable) {
                    $poll->update(['enable' => false]);
                    $this->setErrorState(self::ERROR_POLL_EXPIRED);
                    $this->canVote = false;

                    $this->notification()->warning(
                        'Encuesta expirada',
                        'El tiempo para votar ha terminado.'
                    );
                }
                return;
            }

            $remaining = $now->diffInMinutes($endTime, false);

            if ($remaining <= 0) {
                $this->timeRemaining = 'Expirada';
                return;
            }

            $hours = intval($remaining / 60);
            $minutes = $remaining % 60;

            if ($hours > 0) {
                $this->timeRemaining = "{$hours}h {$minutes}m restantes";
            } else {
                $this->timeRemaining = "{$minutes}m restantes";
            }

        } catch (Exception $e) {
            Log::error('Error updating time remaining: ' . $e->getMessage(), [
                'poll_id' => $poll->id ?? 'unknown'
            ]);
            $this->timeRemaining = 'Error al calcular tiempo';
        }
    }

    private function setErrorState($errorType)
    {
        $this->errorState = $errorType;
        $this->canVote = false;
    }

    private function handleCriticalError(Exception $e)
    {
        $this->setErrorState(self::ERROR_NETWORK);
        $this->notification()->error(
            'Error crítico',
            'Ocurrió un error inesperado. Por favor, recarga la página o contacta al administrador.'
        );
    }

    public function refreshPoll()
    {
        try {
            $this->errorState = null;
            $this->canVote = true;
            $this->initializePoll();

            if (!$this->errorState) {
                $this->notification()->success(
                    'Encuesta actualizada',
                    'La información de la encuesta ha sido actualizada.'
                );
            }
        } catch (Exception $e) {
            Log::error('Error refreshing poll: ' . $e->getMessage(), [
                'poll_id' => $this->getPoll()?->id ?? 'unknown'
            ]);
            $this->handleCriticalError($e);
        }
    }

    public function copyParticipationUrl()
    {
        $this->dispatch('copy-to-clipboard', text: $this->participationUrl);
        $this->notification()->success(
            'Enlace copiado',
            'El enlace de participación ha sido copiado al portapapeles.'
        );
    }

    // Computed property para la vista - ESTA ES LA CLAVE
    public function getPollProperty()
    {
        return $this->getPoll();
    }

    public function render()
    {
        // Solo actualizar tiempo si no hay errores críticos
        if (!in_array($this->errorState, [
            self::ERROR_POLL_NOT_FOUND,
            self::ERROR_POLL_DELETED,
            self::ERROR_NETWORK
        ])) {
            $this->updateTimeRemaining();
        }

        return view('livewire.voting-poll');
    }
}
