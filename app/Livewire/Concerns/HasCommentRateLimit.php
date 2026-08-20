<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Anti-spam en acciones de comentarios del LMS (mejora #9).
 *
 * Limitador por usuario (+ IP) con ventana fija, aplicado dentro de los
 * métodos de Livewire (no hay middleware para las peticiones /livewire).
 * Patrón análogo al de LoginRequest::ensureIsNotRateLimited.
 */
trait HasCommentRateLimit
{
    protected function commentRateLimitPassed(string $action, int $maxAttempts, int $decaySeconds = 60): bool
    {
        $key = $this->commentRateLimitKey($action);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return false;
        }

        RateLimiter::hit($key, $decaySeconds);

        return true;
    }

    protected function commentRateLimitWaitSeconds(string $action): int
    {
        return RateLimiter::availableIn($this->commentRateLimitKey($action));
    }

    protected function commentRateLimitKey(string $action): string
    {
        return 'comments:'.$action.':'.auth()->id().'|'.request()->ip();
    }
}
