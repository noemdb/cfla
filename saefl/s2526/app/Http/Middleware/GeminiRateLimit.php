<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class GeminiRateLimit
{
    public function handle($request, Closure $next)
    {
        $key = 'gemini_rate_limit_' . $request->ip();
        $maxAttempts = 60; // 60 peticiones
        $decayMinutes = 1; // por minuto

        if (Cache::has($key) && Cache::get($key) >= $maxAttempts) {
            return response()->json([
                'success' => false,
                'error' => 'Demasiadas peticiones. Intenta más tarde.'
            ], 429);
        }

        Cache::put($key, Cache::get($key, 0) + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}