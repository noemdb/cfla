<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integración con TokenRouter (https://www.tokenrouter.com) — gateway
 * multi-modelo con formato OpenAI-compatible y un solo API key.
 *
 * Características del gateway que se aprovechan:
 *  - Unified API Key: una sola clave para múltiples modelos.
 *  - Dynamic Global Routing / High Availability: si un modelo o canal
 *    falla, el servicio intenta la cadena de modelos configurada
 *    (model_primary → fallback1 → fallback2).
 *
 * Respuesta esperada (formato OpenAI):
 *  { choices: [ { message: { content: "..." } } ], model, usage }
 */
class TokenRouterService
{
    /**
     * Consulta one-shot con la cadena de modelos (primary + fallbacks).
     *
     * @param  string  $systemPrompt  Instrucción del sistema (contexto).
     * @param  string|array  $message  Mensaje del usuario (texto o multimodal).
     * @param  array<string,mixed>  $overrides  model, max_tokens, temperature, timeout
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    public function ask(string $systemPrompt, string|array $message, array $overrides = []): array
    {
        $errors = [];

        foreach ($this->models($overrides['model'] ?? null) as $model) {
            $result = $this->requestOne($systemPrompt, $message, $overrides + ['model' => $model]);

            if ($result['success']) {
                return $result;
            }

            $errors[] = "{$model}: ".($result['error'] ?? 'respuesta vacía');
            Log::info('TokenRouter: fallback de modelo', ['model' => $model, 'error' => $result['error']]);
        }

        return $this->errorResult('Todos los modelos de TokenRouter fallaron. '.implode(' | ', array_slice($errors, 0, 5)));
    }

    /**
     * Consulta con soporte de imágenes (modelos vision).
     *
     * @param  array  $images  Rutas locales o URLs públicas de imágenes.
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    public function askWithImages(string $systemPrompt, string $text, array $images, array $overrides = []): array
    {
        $content = [['type' => 'text', 'text' => $text]];

        foreach ($images as $image) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => ['url' => $this->resolveImageUrl($image)],
            ];
        }

        return $this->ask($systemPrompt, $content, $overrides);
    }

    /**
     * Cadena de modelos a probar (override → primary → fallbacks).
     *
     * @return array<int, string>
     */
    private function models(?string $override = null): array
    {
        $list = [
            $override ?? config('tokenrouter.model_primary') ?? config('tokenrouter.model'),
            config('tokenrouter.model_fallback1'),
            config('tokenrouter.model_fallback2'),
            config('tokenrouter.model'),
        ];

        return array_values(array_unique(array_filter(array_map('trim', $list), fn ($m) => $m !== '' && $m !== null)));
    }

    /**
     * Ejecuta una única petición a un modelo concreto.
     *
     * @param  array<string,mixed>  $overrides
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    private function requestOne(string $systemPrompt, string|array $message, array $overrides): array
    {
        $payload = $this->buildPayload($systemPrompt, $message, $overrides);

        try {
            $response = Http::timeout($overrides['timeout'] ?? config('tokenrouter.timeout', 60))
                ->withHeaders($this->headers())
                ->post($this->url(), $payload);

            if ($response->failed()) {
                return $this->errorResult(
                    'HTTP '.$response->status().': '.mb_substr($response->body(), 0, 300)
                );
            }

            $data = $response->json();
            $choice = $data['choices'][0] ?? null;

            if (! $choice || ! isset($choice['message'])) {
                return $this->errorResult('Respuesta inesperada de la API: sin choices[0].message.');
            }

            $content = $choice['message']['content'] ?? null;

            // Modelos nuevos pueden devolver content como array multimodal.
            if ($content === null && isset($choice['message']['refusal'])) {
                return $this->errorResult('El modelo rechazó la solicitud: '.$choice['message']['refusal']);
            }

            if ($content === null && is_array($choice['message']['content'] ?? null)) {
                $textParts = array_filter($choice['message']['content'], fn ($c) => ($c['type'] ?? '') === 'text');
                $content = $textParts === [] ? null : implode("\n", array_column($textParts, 'text'));
            }

            if ($content === null || (is_string($content) && trim($content) === '')) {
                $finish = $choice['finish_reason'] ?? 'unknown';
                $detail = match ($finish) {
                    'length' => 'La respuesta excedió el límite de tokens.',
                    'content_filter' => 'La respuesta fue filtrada por el sistema de seguridad.',
                    default => 'sin contenido (finish_reason: '.$finish.')',
                };

                return $this->errorResult('Respuesta inesperada de la API: '.$detail);
            }

            return [
                'success' => true,
                'content' => $content,
                'model' => $data['model'] ?? ($overrides['model'] ?? null),
                'usage' => $data['usage'] ?? null,
                'error' => null,
            ];
        } catch (RequestException $e) {
            return $this->errorResult('Error de conexión: '.$e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResult('Error inesperado: '.$e->getMessage());
        }
    }

    // ─── Internals ─────────────────────────────────────────────────

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.config('tokenrouter.api_key'),
            'Content-Type' => 'application/json',
        ];
    }

    private function url(): string
    {
        return rtrim(config('tokenrouter.base_url'), '/').'/chat/completions';
    }

    /**
     * @param  array<string,mixed>  $overrides
     * @return array<string,mixed>
     */
    private function buildPayload(string $systemPrompt, string|array $message, array $overrides): array
    {
        return [
            'model' => $overrides['model'] ?? config('tokenrouter.model'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $message],
            ],
            'max_tokens' => $overrides['max_tokens'] ?? (int) config('tokenrouter.max_tokens', 4096),
            'temperature' => $overrides['temperature'] ?? (float) config('tokenrouter.temperature', 0.7),
        ];
    }

    /**
     * Convierte una ruta local o URL pública en una URL de datos o la URL misma.
     */
    private function resolveImageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $full = str_starts_with($path, '/') ? public_path(ltrim($path, '/')) : $path;
        if (is_file($full)) {
            $mime = mime_content_type($full) ?: 'image/jpeg';
            $data = base64_encode((string) file_get_contents($full));

            return "data:{$mime};base64,{$data}";
        }

        return $path;
    }

    /**
     * Resultado unificado de error.
     *
     * @return array{success: bool, content: null, model: null, usage: null, error: string}
     */
    private function errorResult(string $message): array
    {
        return [
            'success' => false,
            'content' => null,
            'model' => null,
            'usage' => null,
            'error' => $message,
        ];
    }
}
