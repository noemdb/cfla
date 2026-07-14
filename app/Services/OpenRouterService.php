<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    /**
     * Realiza una consulta one-shot al modelo LLM vía OpenRouter.
     * No mantiene historial ni memoria entre llamadas.
     *
     * @param  string       $systemPrompt  Instrucción del sistema (contexto).
     * @param  string       $userMessage   Mensaje del usuario.
     * @param  array<string,mixed> $overrides  Parámetros opcionales que sobrescriben la config.
     *                                         Claves soportadas: model, max_tokens, temperature,
     *                                         timeout, response_format.
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    public function ask(string $systemPrompt, string $userMessage, array $overrides = []): array
    {
        $payload = $this->buildPayload($systemPrompt, $userMessage, $overrides);

        try {
            $response = Http::timeout($overrides['timeout'] ?? config('openrouter.timeout', 60))
                ->withHeaders($this->headers())
                ->post($this->url(), $payload);

            if ($response->failed()) {
                return $this->errorResult(
                    'HTTP ' . $response->status() . ': ' . $response->body()
                );
            }

            $data = $response->json();

            $content = $data['choices'][0]['message']['content'] ?? null;

            // GPT-5 y modelos nuevos pueden devolver content en formatos alternativos
            if ($content === null && !isset($data['choices'][0]['message']['refusal'])) {
                // Revisar si el content está como array multimodal
                $msgContent = $data['choices'][0]['message']['content'] ?? [];
                if (is_array($msgContent)) {
                    $textParts = array_filter($msgContent, fn($c) => ($c['type'] ?? '') === 'text');
                    if (!empty($textParts)) {
                        $content = implode("\n", array_column($textParts, 'text'));
                    }
                }
            }

            if ($content === null || (is_string($content) && trim($content) === '')) {
                Log::warning('OpenRouter: respuesta sin contenido', [
                    'model' => $overrides['model'] ?? config('openrouter.model'),
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? 'unknown',
                    'content_null' => array_key_exists('content', $data['choices'][0]['message'] ?? []) ? ($data['choices'][0]['message']['content'] === null ? 'yes_null' : 'yes_empty') : 'missing_key',
                    'has_refusal' => isset($data['choices'][0]['message']['refusal']) ? 'yes' : 'no',
                    'message_keys' => $data['choices'][0]['message'] ?? 'no_message',
                    'choice_keys' => array_keys($data['choices'][0] ?? []),
                ]);

                $refusal = $data['choices'][0]['message']['refusal'] ?? null;
                if ($refusal) {
                    return $this->errorResult('El modelo rechazó la solicitud: ' . $refusal);
                }

                $finishReason = $data['choices'][0]['finish_reason'] ?? 'unknown';
                $errorDetail = match ($finishReason) {
                    'length' => 'La respuesta excedió el límite de tokens.',
                    'content_filter' => 'La respuesta fue filtrada por el sistema de seguridad.',
                    'stop' => 'El modelo finalizó sin contenido.',
                    default => "sin contenido en choices[0] (finish_reason: {$finishReason})",
                };

                return $this->errorResult('Respuesta inesperada de la API: ' . $errorDetail);
            }

            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'],
                'model'   => $data['model'] ?? null,
                'usage'   => $data['usage'] ?? null,
                'error'   => null,
            ];
        } catch (RequestException $e) {
            return $this->errorResult('Error de conexión: ' . $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResult('Error inesperado: ' . $e->getMessage());
        }
    }

    /**
     * Consulta con soporte de imágenes (modelos vision).
     * Envía el mensaje de usuario como contenido multimodal (texto + imágenes).
     *
     * @param  string       $systemPrompt
     * @param  string       $text         Texto que acompaña a las imágenes.
     * @param  array        $images       Arreglo de rutas locales o URLs públicas.
     * @param  array<string,mixed> $overrides
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    public function askWithImages(string $systemPrompt, string $text, array $images, array $overrides = []): array
    {
        $content = [['type' => 'text', 'text' => $text]];

        foreach ($images as $image) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $this->resolveImageUrl($image),
                ],
            ];
        }

        $payload = $this->buildPayload($systemPrompt, $content, $overrides);

        try {
            $response = Http::timeout($overrides['timeout'] ?? config('openrouter.timeout', 60))
                ->withHeaders($this->headers())
                ->post($this->url(), $payload);

            if ($response->failed()) {
                return $this->errorResult(
                    'HTTP ' . $response->status() . ': ' . $response->body()
                );
            }

            $data = $response->json();

            $content = $data['choices'][0]['message']['content'] ?? null;

            // GPT-5 y modelos nuevos pueden devolver content en formatos alternativos
            if ($content === null && !isset($data['choices'][0]['message']['refusal'])) {
                // Revisar si el content está como array multimodal
                $msgContent = $data['choices'][0]['message']['content'] ?? [];
                if (is_array($msgContent)) {
                    $textParts = array_filter($msgContent, fn($c) => ($c['type'] ?? '') === 'text');
                    if (!empty($textParts)) {
                        $content = implode("\n", array_column($textParts, 'text'));
                    }
                }
            }

            if ($content === null || (is_string($content) && trim($content) === '')) {
                Log::warning('OpenRouter: respuesta sin contenido', [
                    'model' => $overrides['model'] ?? config('openrouter.model'),
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? 'unknown',
                    'content_null' => array_key_exists('content', $data['choices'][0]['message'] ?? []) ? ($data['choices'][0]['message']['content'] === null ? 'yes_null' : 'yes_empty') : 'missing_key',
                    'has_refusal' => isset($data['choices'][0]['message']['refusal']) ? 'yes' : 'no',
                    'message_keys' => $data['choices'][0]['message'] ?? 'no_message',
                    'choice_keys' => array_keys($data['choices'][0] ?? []),
                ]);

                $refusal = $data['choices'][0]['message']['refusal'] ?? null;
                if ($refusal) {
                    return $this->errorResult('El modelo rechazó la solicitud: ' . $refusal);
                }

                $finishReason = $data['choices'][0]['finish_reason'] ?? 'unknown';
                $errorDetail = match ($finishReason) {
                    'length' => 'La respuesta excedió el límite de tokens.',
                    'content_filter' => 'La respuesta fue filtrada por el sistema de seguridad.',
                    'stop' => 'El modelo finalizó sin contenido.',
                    default => "sin contenido en choices[0] (finish_reason: {$finishReason})",
                };

                return $this->errorResult('Respuesta inesperada de la API: ' . $errorDetail);
            }

            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'],
                'model'   => $data['model'] ?? null,
                'usage'   => $data['usage'] ?? null,
                'error'   => null,
            ];
        } catch (RequestException $e) {
            return $this->errorResult('Error de conexión: ' . $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResult('Error inesperado: ' . $e->getMessage());
        }
    }

    // ─── Internals ─────────────────────────────────────────────────

    private function headers(): array
    {
        return [
            'Authorization'           => 'Bearer ' . config('openrouter.api_key'),
            'Content-Type'            => 'application/json',
            'X-Title'                 => config('app.name', 'SAEFL'),
            'HTTP-Referer'            => config('app.url', 'http://localhost'),
        ];
    }

    private function url(): string
    {
        return rtrim(config('openrouter.base_url'), '/') . '/chat/completions';
    }

    /**
     * @param  string|array  $message  Mensaje plano o arreglo multimodal.
     * @return array<string,mixed>
     */
    private function buildPayload(string $systemPrompt, string|array $message, array $overrides): array
    {
        return [
            'model'       => $overrides['model'] ?? config('openrouter.model'),
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $message],
            ],
            'max_tokens'           => $overrides['max_tokens'] ?? (int) config('openrouter.max_tokens', 2048),
            'max_completion_tokens' => $overrides['max_tokens'] ?? (int) config('openrouter.max_tokens', 2048),
            'temperature' => $overrides['temperature'] ?? (float) config('openrouter.temperature', 0.7),
        ];
    }

    /**
     * Convierte una ruta local o URL pública en una URL de datos o la URL misma.
     */
    private function resolveImageUrl(string $path): string
    {
        // Si ya es una URL pública, devolverla directamente
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Si es una ruta local, codificarla como base64
        if (file_exists($path)) {
            $mime = mime_content_type($path) ?: 'image/png';
            $data = base64_encode(file_get_contents($path));
            return "data:{$mime};base64,{$data}";
        }

        // Si es una ruta relativa de storage
        $storagePath = storage_path("app/public/{$path}");
        if (file_exists($storagePath)) {
            $mime = mime_content_type($storagePath) ?: 'image/png';
            $data = base64_encode(file_get_contents($storagePath));
            return "data:{$mime};base64,{$data}";
        }

        // Fallback: devolver la ruta tal cual (probablemente fallará)
        return $path;
    }

    /**
     * @return array{success: false, content: null, model: null, usage: null, error: string}
     */
    private function errorResult(string $message): array
    {
        return [
            'success' => false,
            'content' => null,
            'model'   => null,
            'usage'   => null,
            'error'   => $message,
        ];
    }
}
