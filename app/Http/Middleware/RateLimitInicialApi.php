<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitInicialApi
{
    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Cache\RateLimiter  $limiter
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return mixed
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        // Límites más estrictos para operaciones masivas
        $path = $request->path();
        if (str_contains($path, 'batch') || str_contains($path, 'clone')) {
            $maxAttempts = 10;
        }

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier() . '|' . $request->ip());
        }

        return sha1($request->ip() . '|' . $request->userAgent());
    }

    /**
     * Create a 'too many attempts' response.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildResponse($key, $maxAttempts)
    {
        $retryAfter = $this->limiter->availableIn($key);

        $response = response()->json([
            'error' => 'rate_limit_exceeded',
            'message' => 'Demasiadas solicitudes. Por favor, espere antes de intentar nuevamente.',
            'retry_after' => $retryAfter,
            'retry_after_human' => $this->formatSeconds($retryAfter),
            'max_attempts' => $maxAttempts,
            'documentation' => route('api.documentation') . '#rate-limiting'
        ], 429);

        $response->headers->add([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->getTimestamp(),
        ]);

        return $response;
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  int  $maxAttempts
     * @param  int  $remainingAttempts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts)
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
            'X-RateLimit-Reset' => now()->addMinutes(1)->getTimestamp(),
        ]);

        return $response;
    }

    /**
     * Calculate the number of remaining attempts.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return int
     */
    protected function calculateRemainingAttempts($key, $maxAttempts)
    {
        return max(0, $maxAttempts - $this->limiter->attempts($key));
    }

    /**
     * Format seconds to human readable time.
     *
     * @param  int  $seconds
     * @return string
     */
    protected function formatSeconds($seconds)
    {
        if ($seconds < 60) {
            return "{$seconds} segundos";
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($remainingSeconds > 0) {
            return "{$minutes} minuto(s) y {$remainingSeconds} segundo(s)";
        }

        return "{$minutes} minuto(s)";
    }
}