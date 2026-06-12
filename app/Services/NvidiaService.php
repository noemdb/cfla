<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class NvidiaService
{
    /**
     * Realiza una consulta one-shot al modelo LLM vía NVIDIA build.nvidia.com.
     * No mantiene historial ni memoria entre llamadas.
     *
     * @param  string       $systemPrompt  Instrucción del sistema (contexto).
     * @param  string       $userMessage   Mensaje del usuario.
     * @param  array<string,mixed> $overrides  Parámetros opcionales que sobrescriben la config.
     *                                         Claves soportadas: model, max_tokens, temperature,
     *                                         timeout.
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, meta: ?array, error: ?string}
     */
    public function ask(string $systemPrompt, string $userMessage, array $overrides = []): array
    {
        $payload = $this->buildPayload($systemPrompt, $userMessage, $overrides);

        try {
            $response = Http::timeout($overrides['timeout'] ?? config('nvidia.timeout', 60))
                ->withHeaders($this->headers())
                ->post($this->url(), $payload);

            if ($response->failed()) {
                return $this->errorResult(
                    'HTTP ' . $response->status() . ': ' . $response->body()
                );
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                return $this->errorResult('Respuesta inesperada de la API: sin contenido en choices[0].');
            }

            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'],
                'model'   => $data['model'] ?? null,
                'usage'   => $data['usage'] ?? null,
                'meta'    => [
                    'id'            => $data['id'] ?? null,
                    'object'        => $data['object'] ?? null,
                    'created'       => $data['created'] ?? null,
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
                ],
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
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, meta: ?array, error: ?string}
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
            $response = Http::timeout($overrides['timeout'] ?? config('nvidia.timeout', 60))
                ->withHeaders($this->headers())
                ->post($this->url(), $payload);

            if ($response->failed()) {
                return $this->errorResult(
                    'HTTP ' . $response->status() . ': ' . $response->body()
                );
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                return $this->errorResult('Respuesta inesperada de la API: sin contenido en choices[0].');
            }

            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'],
                'model'   => $data['model'] ?? null,
                'usage'   => $data['usage'] ?? null,
                'meta'    => [
                    'id'            => $data['id'] ?? null,
                    'object'        => $data['object'] ?? null,
                    'created'       => $data['created'] ?? null,
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
                ],
                'error'   => null,
            ];
        } catch (RequestException $e) {
            return $this->errorResult('Error de conexión: ' . $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResult('Error inesperado: ' . $e->getMessage());
        }
    }

    /**
     * Envía un payload completo y crudo a la API (máxima flexibilidad).
     *
     * Útil cuando se necesita control total sobre la estructura de mensajes
     * (ej: múltiples turns, tool calls, etc.).
     *
     * @param  array $payload  Payload completo de chat/completions (messages, model, etc.)
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, meta: ?array, error: ?string}
     */
    public function generateText(array $payload): array
    {
        try {
            $response = Http::timeout(config('nvidia.timeout', 60))
                ->withHeaders($this->headers())
                ->post($this->url(), $payload);

            if ($response->failed()) {
                return $this->errorResult(
                    'HTTP ' . $response->status() . ': ' . $response->body()
                );
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                return $this->errorResult('Respuesta inesperada de la API: sin contenido en choices[0].');
            }

            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'],
                'model'   => $data['model'] ?? null,
                'usage'   => $data['usage'] ?? null,
                'meta'    => [
                    'id'            => $data['id'] ?? null,
                    'object'        => $data['object'] ?? null,
                    'created'       => $data['created'] ?? null,
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
                ],
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
            'Authorization' => 'Bearer ' . config('nvidia.api_key'),
            'Content-Type'  => 'application/json',
        ];
    }

    private function url(): string
    {
        return rtrim(config('nvidia.base_url'), '/') . '/chat/completions';
    }

    /**
     * @param  string|array  $message  Mensaje plano o arreglo multimodal.
     * @return array<string,mixed>
     */
    private function buildPayload(string $systemPrompt, string|array $message, array $overrides): array
    {
        return [
            'model'       => $overrides['model'] ?? config('nvidia.model'),
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $message],
            ],
            'max_tokens'  => $overrides['max_tokens'] ?? (int) config('nvidia.max_tokens', 2048),
            'temperature' => $overrides['temperature'] ?? (float) config('nvidia.temperature', 0.7),
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
