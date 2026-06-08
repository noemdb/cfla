<?php

namespace App\Jobs\Email\Collection;

use App\Http\Controllers\Administracion\Email\Collection\CollectionScheduleEmailRotationController;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNotifyCollectRotation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($dataEmail)
    {
        $this->dataEmail = $dataEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $controller = new CollectionScheduleEmailRotationController();
            $service = $this->dataEmail['service'] ?? 'brevo'; // Servicio por defecto

            $result = $controller->sendEmailWithService($service, $this->dataEmail);

            if ($result['success']) {
                Log::info("Email enviado exitosamente con {$service}", [
                    'email' => $this->dataEmail['address'],
                    'service' => $service
                ]);
            } else {
                Log::error("Error enviando email con {$service}", [
                    'email' => $this->dataEmail['address'],
                    'service' => $service,
                    'error' => $result['message'] ?? 'Error desconocido'
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error en ProcessNotifyCollectRotation: ' . $e->getMessage(), [
                'email' => $this->dataEmail['address'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-lanzar para que Laravel maneje el retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessNotifyCollectRotation job failed', [
            'email' => $this->dataEmail['address'] ?? 'unknown',
            'service' => $this->dataEmail['service'] ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}