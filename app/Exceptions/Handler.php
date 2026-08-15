<?php

namespace App\Exceptions;

use App\Services\Binnacle;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Bitácora de auditoría (Spec BINNACLE-001, Fase 2): excepciones no
        // manejadas explícitamente. ValidationException tiene su propio flujo
        // de UX y se omite para no generar ruido.
        $this->reportable(function (Throwable $e) {
            if ($e instanceof ValidationException) {
                return;
            }

            Binnacle::log('exception_thrown', [
                'title' => 'Excepción no manejada',
                'description' => $e->getMessage(),
                'category' => 'error',
                'severity' => $this->severityFor($e),
                // El actor de la excepción es el usuario autenticado (si lo hay),
                // para que aparezca en su línea de actividad.
                'subject' => auth()->user(),
                'metadata' => [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ]);
        });
    }

    /**
     * 500 → critical; 4xx no manejados → warning.
     */
    private function severityFor(Throwable $e): string
    {
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
            return $e->getStatusCode() >= 500 ? 'critical' : 'warning';
        }

        return 'critical';
    }
}
