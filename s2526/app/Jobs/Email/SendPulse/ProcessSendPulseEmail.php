<?php

namespace App\Jobs\Email\SendPulse;

use App\Services\SendPulseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSendPulseEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataEmail;
    public $tries = 2; // Aumentar a 3 intentos
    public $timeout = 30; // Aumentar el timeout
    public $backoff = 5; // Esperar 10 segundos entre intentos

    /**
     * Create a new job instance.
     */
    public function __construct(array $dataEmail)
    {
        $this->dataEmail = $dataEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(SendPulseService $sendPulseService): void
    {
        Log::info('1. Iniciando job', ['data' => $this->dataEmail]);

        try {
            Log::info('2. Verificando datos del email');
            if (empty($this->dataEmail['address']) || empty($this->dataEmail['subject']) || empty($this->dataEmail['html'])) {
                throw new \Exception('Datos de email incompletos');
            }

            Log::info('3. Verificando servicio SendPulse');
            if (!$sendPulseService) {
                throw new \Exception('Servicio SendPulse no disponible');
            }

            // Verificar la conexión primero
            Log::info('4. Verificando conexión con SendPulse');
            try {
                $addressBooks = $sendPulseService->getAddressBooks();
                Log::info('Conexión exitosa con SendPulse', ['result' => $addressBooks]);
            } catch (\Exception $e) {
                Log::error('Error al verificar conexión con SendPulse', [
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts()
                ]);
                throw $e;
            }

            Log::info('5. Intentando enviar email', [
                'to' => $this->dataEmail['address'],
                'subject' => $this->dataEmail['subject'],
                'cc' => $this->dataEmail['cc'] ?? null,
                'bcc' => $this->dataEmail['bcc'] ?? null,
                'attempt' => $this->attempts()
            ]);

            $result = $sendPulseService->send(
                $this->dataEmail['address'],
                $this->dataEmail['subject'],
                $this->dataEmail['html'],
                null,
                false,
                $this->dataEmail['cc'] ?? null,
                $this->dataEmail['bcc'] ?? null
            );

            Log::info('6. Resultado del envío', [
                'result' => $result,
                'attempt' => $this->attempts()
            ]);

            if (!$result['success']) {
                throw new \Exception($result['message']);
            }

            Log::info('7. Email enviado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en el job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts(),
                'max_attempts' => $this->tries
            ]);

            // Si es el último intento, registrar el fallo final
            if ($this->attempts() >= $this->tries) {
                Log::error('Falló el envío de email después de ' . $this->tries . ' intentos', [
                    'to' => $this->dataEmail['address'],
                    'subject' => $this->dataEmail['subject'],
                    'error' => $e->getMessage()
                ]);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Job falló definitivamente', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'data' => $this->dataEmail
        ]);
    }
}
