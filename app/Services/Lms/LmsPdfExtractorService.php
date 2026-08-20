<?php

namespace App\Services\Lms;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para extraer el contenido textual de un PDF.
 *
 * Prefiere la API de Datalab (https://www.datalab.to) — el mismo motor marker
 * pero gestionado — para obtener markdown estructurado (títulos, tablas,
 * listas). Si la API no está configurada, falla o se agota el tiempo, cae
 * automáticamente a la utilidad del sistema `pdftotext`.
 */
class LmsPdfExtractorService
{
    /**
     * Extrae el texto/markdown de un PDF.
     */
    public function extract(string $pdfPath): string
    {
        $markdown = $this->datalabToMarkdown($pdfPath);

        if ($markdown !== null && trim($markdown) !== '') {
            Log::debug('LmsPdfExtractorService: markdown extraído con la API de Datalab', [
                'bytes' => mb_strlen($markdown),
            ]);

            return $markdown;
        }

        return $this->pdfToText($pdfPath);
    }

    /**
     * Convierte el PDF a markdown usando la API de Datalab (submit + poll).
     * Devuelve null si la API no está configurada o falla.
     */
    public function datalabToMarkdown(string $pdfPath): ?string
    {
        $apiKey = config('lms.datalab.api_key');

        if (blank($apiKey)) {
            Log::debug('LmsPdfExtractorService: DATALAB_API_KEY no configurada, se usará pdftotext');

            return null;
        }

        $baseUrl = config('lms.datalab.base_url', 'https://www.datalab.to');
        $mode = config('lms.datalab.mode', 'balanced');
        $pollInterval = max(1, (int) config('lms.datalab.poll_interval', 2));
        $timeout = (int) config('lms.datalab.timeout', 300);
        $deadline = microtime(true) + $timeout;

        $checkUrl = $this->submitConversion($pdfPath, $apiKey, $baseUrl, $mode);

        if ($checkUrl === null) {
            return null;
        }

        while (true) {
            if (microtime(true) >= $deadline) {
                Log::warning('LmsPdfExtractorService: Datalab agotó el tiempo de espera', [
                    'timeout' => $timeout,
                ]);

                return null;
            }

            usleep($pollInterval * 1000000);

            $response = Http::withHeaders(['X-API-Key' => $apiKey])
                ->timeout(60)
                ->get($checkUrl);

            if ($response->failed()) {
                $this->logApiFailure('poll', $response);

                return null;
            }

            $payload = $response->json();

            if (($payload['status'] ?? null) === 'complete') {
                $markdown = $payload['markdown'] ?? null;

                if (! is_string($markdown) || trim($markdown) === '') {
                    Log::warning('LmsPdfExtractorService: Datalab completó sin markdown', [
                        'keys' => array_keys($payload ?: []),
                    ]);

                    return null;
                }

                return $markdown;
            }

            if (($payload['status'] ?? null) === 'failed' || ($payload['success'] ?? null) === false) {
                Log::warning('LmsPdfExtractorService: Datalab reportó fallo en la conversión', [
                    'error' => $payload['error'] ?? null,
                ]);

                return null;
            }
        }
    }

    /**
     * Envía el PDF a /api/v1/convert y devuelve la URL de polling,
     * o null si falla tras reintentar.
     */
    private function submitConversion(string $pdfPath, string $apiKey, string $baseUrl, string $mode): ?string
    {
        $maxAttempts = max(1, (int) config('lms.datalab.max_attempts', 3));

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $response = Http::withHeaders(['X-API-Key' => $apiKey])
                ->timeout(120)
                ->asMultipart()
                ->post($baseUrl.'/api/v1/convert', $this->multipartPayload($pdfPath, $mode));

            if ($response->successful()) {
                $checkUrl = $response->json('request_check_url');

                if (blank($checkUrl)) {
                    Log::warning('LmsPdfExtractorService: Datalab no devolvió request_check_url', [
                        'response' => mb_substr($response->body(), 0, 500),
                    ]);

                    return null;
                }

                return $checkUrl;
            }

            if ($response->status() === 429 || $response->status() >= 500) {
                if ($attempt < $maxAttempts) {
                    sleep(min(4, $attempt * 2));

                    continue;
                }
            }

            $this->logApiFailure('submit', $response, ['attempt' => $attempt]);

            return null;
        }

        return null;
    }

    /**
     * Construye el payload multipart del submit de conversión.
     */
    private function multipartPayload(string $pdfPath, string $mode): array
    {
        return [
            [
                'name' => 'file',
                'contents' => fopen($pdfPath, 'r'),
                'filename' => basename($pdfPath),
                'headers' => ['Content-Type' => 'application/pdf'],
            ],
            ['name' => 'output_format', 'contents' => 'markdown'],
            ['name' => 'mode', 'contents' => $mode],
        ];
    }

    /**
     * Extrae texto plano con la utilidad del sistema `pdftotext`.
     */
    public function pdfToText(string $pdfPath): string
    {
        $process = new \Symfony\Component\Process\Process(['pdftotext', '-enc', 'UTF-8', $pdfPath, '-']);
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            Log::warning('LmsPdfExtractorService: pdftotext falló', [
                'exit_code' => $process->getExitCode(),
                'stderr' => mb_substr($process->getErrorOutput() ?: '', 0, 500),
            ]);

            return '';
        }

        return $process->getOutput();
    }

    /**
     * Devuelve el número de páginas de un PDF usando la utilidad del
     * sistema `pdfinfo`. Retorna 0 si el archivo no es un PDF válido
     * o la utilidad falla.
     */
    public function pageCount(string $pdfPath): int
    {
        $process = new \Symfony\Component\Process\Process(['pdfinfo', $pdfPath]);
        $process->setTimeout(30);
        $process->run();

        if (! $process->isSuccessful()) {
            Log::warning('LmsPdfExtractorService: pdfinfo falló', [
                'exit_code' => $process->getExitCode(),
                'stderr' => mb_substr($process->getErrorOutput() ?: '', 0, 500),
            ]);

            return 0;
        }

        if (preg_match('/^Pages:\s*(\d+)/m', $process->getOutput(), $match) !== 1) {
            Log::warning('LmsPdfExtractorService: pdfinfo no reportó número de páginas');

            return 0;
        }

        return (int) $match[1];
    }

    private function logApiFailure(string $stage, Response $response, array $extra = []): void
    {
        Log::warning('LmsPdfExtractorService: Datalab falló en '.$stage.', se usará pdftotext', array_merge([
            'status' => $response->status(),
            'body' => mb_substr($response->body(), 0, 500),
        ], $extra));
    }
}
