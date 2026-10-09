<?php

namespace App\Exceptions;

use App\Services\Binnacle;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
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
        // Observabilidad de CorruptComponentPayloadException (checksum HMAC del
        // snapshot con APP_KEY): el log original no trae componente/URL/usuario
        // y sin eso no se puede distinguir entre APP_KEY inconsistente,
        // payload mutado/truncado o snapshot viejo. Solo agrega contexto al
        // log; no cambia el comportamiento ni el flujo de la excepción.
        // Nunca lanza: va envuelto en try/catch.
        $this->reportable(function (Throwable $e) {
            if (! $e instanceof \Livewire\Mechanisms\HandleComponents\CorruptComponentPayloadException) {
                return;
            }

            try {
                $components = request()->input('components', []);
                $first = is_array($components) ? ($components[0] ?? []) : [];
                $snapshot = (is_array($first) ? ($first['snapshot'] ?? []) : []);
                $memo = (is_array($snapshot) ? ($snapshot['memo'] ?? []) : []);
                $fingerprint = (is_array($snapshot) ? ($snapshot['fingerprint'] ?? []) : []);

                Log::warning('Livewire corrupt payload', [
                    'component' => $memo['name'] ?? $fingerprint['name'] ?? null,
                    'memo_id' => $memo['id'] ?? null,
                    'path' => $fingerprint['path'] ?? request()->path(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'user_id' => auth()->id(),
                    'content_length' => request()->server('CONTENT_LENGTH'),
                    'has_checksum' => is_array($snapshot) && array_key_exists('checksum', $snapshot),
                    'calls' => array_map(
                        fn ($c) => is_array($c) ? ($c['method'] ?? null) : null,
                        is_array($first['calls'] ?? null) ? $first['calls'] : []
                    ),
                ]);
            } catch (Throwable) {
                // La observabilidad nunca debe romper el manejo del error.
            }
        });

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
