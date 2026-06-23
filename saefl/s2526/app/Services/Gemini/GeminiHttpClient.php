<?php

namespace App\Services\Gemini;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GeminiHttpClient
{
    public static function make(): Client
    {
        $stack = HandlerStack::create();

        // Retry middleware
        $stack->push(
            Middleware::retry(
                self::retryDecider(),
                self::retryDelay()
            )
        );

        return new Client([
            'base_uri' => rtrim(config('services.gemini.api_url'), '/') . '/',
            'handler'  => $stack,

            'timeout'         => config('services.gemini.timeout', 60),
            'connect_timeout' => config('services.gemini.connect_timeout', 10),

            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],

            'http_errors' => true,
        ]);
    }

    /**
     * Decide si se debe reintentar la petición
     */
    private static function retryDecider(): callable
    {
        return function (
            int $retries,
            RequestInterface $request,
            ?ResponseInterface $response = null,
            ?\Throwable $exception = null
        ) {
            $maxRetries = config('services.gemini.retry_max', 3);

            if ($retries >= $maxRetries) {
                return false;
            }

            // Excepciones de red (timeout, conexión)
            if ($exception !== null) {
                return true;
            }

            if ($response) {
                $status = $response->getStatusCode();

                // Reintentar solo errores transitorios
                return in_array($status, [429, 500, 502, 503, 504], true);
            }

            return false;
        };
    }

    /**
     * Backoff exponencial con jitter
     */
    private static function retryDelay(): callable
    {
        return function (int $retries) {
            $baseDelayMs = config('services.gemini.retry_base_delay', 200);

            // Exponential backoff: base * 2^n + jitter
            $delay = $baseDelayMs * (2 ** $retries);

            // Jitter (0–100 ms)
            $jitter = random_int(0, 100);

            return $delay + $jitter;
        };
    }
}
