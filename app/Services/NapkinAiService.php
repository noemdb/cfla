<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class NapkinAiService
{
    /**
     * Genera un diagrama SVG a partir de texto usando napkin.ai.
     *
     * Toma el contenido textual de una sección de lección y genera
     * un diagrama visual en formato SVG que se incrusta directamente
     * como HTML en la sección.
     *
     * @param  string       $prompt   Descripción textual del diagrama a generar.
     * @param  array<string,mixed> $overrides Parámetros opcionales (resolution, timeout, endpoint, style).
     * @return array{success: bool, svg_html: ?string, image_url: ?string, error: ?string}
     */
    public function generateDiagram(string $prompt, array $overrides = []): array
    {
        // ─── Estrategia 1: endpoint data_graphics_generator (Vega) ──
        $result = $this->tryVegaGenerator($prompt, $overrides);
        if ($result['success']) {
            return $result;
        }

        // ─── Estrategia 2: endpoint image/generation ──────────────
        $result = $this->tryImageGeneration($prompt, $overrides);
        if ($result['success']) {
            return $result;
        }

        // ─── Fallback: devolver error ──────────────────────────────
        return $result;
    }

    /**
     * Intenta generar el diagrama via el endpoint data_graphics_generator
     * (Vega-based visualization engine de napkin.ai).
     */
    private function tryVegaGenerator(string $prompt, array $overrides): array
    {
        $vegaUrl = rtrim($overrides['vega_url'] ?? config('napkin.vega_url', 'https://vega.nlp.api.napkin.ai/api/v1'), '/');
        $url = $vegaUrl . '/data_graphics_generator';
        $timeout = $overrides['timeout'] ?? (int) config('napkin.timeout', 120);

        try {
            $response = Http::timeout($timeout)
                ->withHeaders($this->headers())
                ->post($url, [
                    'prompt'  => $prompt,
                    'format'  => 'svg',
                    'style'   => $overrides['style'] ?? 'diagram',
                ]);

            if ($response->successful()) {
                $body = $response->body();
                $contentType = $response->header('Content-Type');

                // Si la respuesta es SVG directamente
                if (str_contains($contentType ?? '', 'image/svg+xml') || str_starts_with(trim($body), '<svg')) {
                    return [
                        'success'   => true,
                        'svg_html'  => $body,
                        'image_url' => null,
                        'error'     => null,
                    ];
                }

                // Si es JSON con el SVG dentro
                $data = $response->json();
                if ($data && isset($data['svg'])) {
                    return [
                        'success'   => true,
                        'svg_html'  => $data['svg'],
                        'image_url' => null,
                        'error'     => null,
                    ];
                }
                if ($data && isset($data['image_url'])) {
                    return [
                        'success'   => true,
                        'svg_html'  => null,
                        'image_url' => $data['image_url'],
                        'error'     => null,
                    ];
                }

                // Respuesta inesperada → seguir a la siguiente estrategia
                return $this->errorResult('Vega: formato de respuesta inesperado');
            }

            // Falló → log y siguiente estrategia
            return $this->errorResult('Vega: HTTP ' . $response->status());
        } catch (RequestException $e) {
            return $this->errorResult('Vega: error de conexión: ' . $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResult('Vega: error inesperado: ' . $e->getMessage());
        }
    }

    /**
     * Intenta generar la imagen via el endpoint features/image/generation
     * (NLP California API).
     */
    private function tryImageGeneration(string $prompt, array $overrides): array
    {
        $baseUrl = rtrim($overrides['base_url'] ?? config('napkin.base_url', 'https://nlp-california-api.napkin.ai/api/v1'), '/');
        $url = $baseUrl . '/features/image/generation';
        $timeout = $overrides['timeout'] ?? (int) config('napkin.timeout', 120);

        try {
            $response = Http::timeout($timeout)
                ->withHeaders($this->headers())
                ->accept('multipart/related')
                ->post($url, [
                    'prompt'       => $prompt,
                    'resolution'   => $overrides['resolution'] ?? config('napkin.resolution', '1024x1024'),
                    'create_scene' => true,
                ]);

            if ($response->successful()) {
                $body = $response->body();
                $contentType = $response->header('Content-Type');

                // multipart/related response — extraer SVG o imagen
                if (str_contains($contentType ?? '', 'multipart/related')) {
                    $parts = $this->parseMultipartRelated($body, $contentType);
                    foreach ($parts as $part) {
                        // Buscar SVG
                        if (str_contains($part['content'] ?? '', '<svg')) {
                            return [
                                'success'   => true,
                                'svg_html'  => $part['content'],
                                'image_url' => null,
                                'error'     => null,
                            ];
                        }
                    }

                    // Si hay al menos una parte, devolver la primera como imagen
                    if (!empty($parts) && isset($parts[0]['content'])) {
                        $content = $parts[0]['content'];
                        if (str_contains($contentType ?? '', 'image/')) {
                            return [
                                'success'   => true,
                                'svg_html'  => null,
                                'image_url' => 'data:' . ($parts[0]['type'] ?? 'image/png') . ';base64,' . base64_encode($content),
                                'error'     => null,
                            ];
                        }
                        return [
                            'success'   => true,
                            'svg_html'  => $content,
                            'image_url' => null,
                            'error'     => null,
                        ];
                    }
                }

                // Si es SVG directo
                if (str_starts_with(trim($body), '<svg')) {
                    return [
                        'success'   => true,
                        'svg_html'  => $body,
                        'image_url' => null,
                        'error'     => null,
                    ];
                }

                // JSON response
                $data = $response->json();
                if ($data && isset($data['image_url'])) {
                    return [
                        'success'   => true,
                        'svg_html'  => null,
                        'image_url' => $data['image_url'],
                        'error'     => null,
                    ];
                }

                return $this->errorResult('ImageGen: formato de respuesta inesperado');
            }

            return $this->errorResult('ImageGen: HTTP ' . $response->status() . ': ' . $response->body());
        } catch (RequestException $e) {
            return $this->errorResult('ImageGen: error de conexión: ' . $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResult('ImageGen: error inesperado: ' . $e->getMessage());
        }
    }

    /**
     * Analiza una respuesta multipart/related y extrae sus partes.
     *
     * @param  string      $body        Cuerpo de la respuesta.
     * @param  string|null $contentType Cabecera Content-Type (contiene boundary).
     * @return array<int, array{type: string, content: string}>
     */
    private function parseMultipartRelated(string $body, ?string $contentType): array
    {
        $parts = [];
        if (!$contentType || !preg_match('/boundary="?([^";\s]+)"?/', $contentType, $m)) {
            // Intentar detectar boundary en el cuerpo
            if (preg_match('/^--([^\s]+)/m', $body, $m)) {
                $boundary = $m[1];
            } else {
                return $parts;
            }
        } else {
            $boundary = $m[1];
        }

        $delimiter = '--' . $boundary;
        $blocks = explode($delimiter, $body);

        foreach ($blocks as $block) {
            // Saltar bloques vacíos y el terminador final
            $block = trim($block);
            if ($block === '' || $block === '--') {
                continue;
            }

            // Separar cabeceras del contenido
            $partsInfo = explode("\r\n\r\n", $block, 2);
            if (count($partsInfo) < 2) {
                $partsInfo = explode("\n\n", $block, 2);
            }

            $headerStr = $partsInfo[0] ?? '';
            $content = $partsInfo[1] ?? '';

            // Extraer Content-Type de las cabeceras
            $type = 'application/octet-stream';
            if (preg_match('/Content-Type:\s*([^;\s]+)/i', $headerStr, $cm)) {
                $type = $cm[1];
            }

            $parts[] = [
                'type'    => $type,
                'content' => trim($content),
            ];
        }

        return $parts;
    }

    /**
     * Construye el HTML para incrustar el resultado en la sección.
     *
     * @param  string|null $svgHtml  Código SVG para incrustar directamente.
     * @param  string|null $imageUrl URL de la imagen (si no es SVG embebido).
     * @return string HTML listo para insertar en el body de la sección.
     */
    public function buildEmbedHtml(?string $svgHtml, ?string $imageUrl, string $title = 'Diagrama'): string
    {
        if ($svgHtml) {
            // Limpiar el SVG de posibles etiquetas HTML envolventes
            $svgHtml = trim($svgHtml);
            // Si viene envuelto en ```svg ... ```, extraer solo el SVG
            $svgHtml = preg_replace('/^```(?:svg|html)?\s*\n?/i', '', $svgHtml);
            $svgHtml = preg_replace('/\n?```\s*$/s', '', $svgHtml);
            $svgHtml = trim($svgHtml);

            // Verificar que tenga la etiqueta <svg>
            if (preg_match('/^<svg[\s>]/i', $svgHtml) || preg_match('/<svg[\s>]/i', $svgHtml)) {
                return '<figure class="my-6 napkin-diagram">' . "\n" .
                       '  <figcaption class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">' .
                       e($title) . '</figcaption>' . "\n" .
                       '  <div class="flex justify-center bg-gray-50 dark:bg-gray-800 rounded-xl p-4">' .
                       "\n    " . $svgHtml . "\n  </div>\n" .
                       '</figure>';
            }
        }

        if ($imageUrl) {
            return '<figure class="my-6 napkin-image">' . "\n" .
                   '  <figcaption class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">' .
                   e($title) . '</figcaption>' . "\n" .
                   '  <img src="' . e($imageUrl) . '" alt="' . e($title) . '" ' .
                   'class="rounded-xl max-w-full mx-auto shadow-sm"/>' . "\n" .
                   '</figure>';
        }

        // Fallback: marcador de posición
        return '';
    }

    // ─── Internals ─────────────────────────────────────────────────

    private function headers(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $apiKey = config('napkin.api_key');
        if ($apiKey) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        return $headers;
    }

    private function errorResult(string $message): array
    {
        return [
            'success'   => false,
            'svg_html'  => null,
            'image_url' => null,
            'error'     => $message,
        ];
    }
}
