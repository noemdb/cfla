<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class KimiService
{
    /**
     * Realiza una consulta one-shot al modelo LLM vía Kimi (Moonshot).
     * No mantiene historial ni memoria entre llamadas.
     *
     * @param  string       $systemPrompt  Instrucción del sistema (contexto).
     * @param  string       $userMessage   Mensaje del usuario.
     * @param  array<string,mixed> $overrides  Parámetros opcionales que sobrescriben la config.
     *                                         Claves soportadas: model, max_tokens, temperature,
     *                                         timeout.
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    public function ask(string $systemPrompt, string $userMessage, array $overrides = []): array
    {
        $payload = $this->buildPayload($systemPrompt, $userMessage, $overrides);

        try {
            $response = Http::timeout($overrides['timeout'] ?? config('kimi.timeout', 60))
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

            $content = $data['choices'][0]['message']['content'];

            if (is_string($content) && trim($content) === '') {
                return $this->errorResult('El modelo devolvió contenido vacío.');
            }

            return [
                'success' => true,
                'content' => $content,
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
            'Authorization' => 'Bearer ' . config('kimi.api_key'),
            'Content-Type'  => 'application/json',
        ];
    }

    private function url(): string
    {
        return rtrim(config('kimi.base_url'), '/') . '/chat/completions';
    }

    /**
     * @return array<string,mixed>
     */
    private function buildPayload(string $systemPrompt, string $message, array $overrides): array
    {
        return [
            'model'       => $overrides['model'] ?? config('kimi.model'),
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $message],
            ],
            'max_tokens'  => $overrides['max_tokens'] ?? (int) config('kimi.max_tokens', 8192),
            'temperature' => $overrides['temperature'] ?? (float) config('kimi.temperature', 0.7),
        ];
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
