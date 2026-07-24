<?php

namespace App\Services\Lms;

/**
 * Servicio de renderizado y sanitización de contenido de slides del LMS.
 *
 * Centraliza la detección de tipos de contenido (MATH/HTML/MERMAID/TEXT),
 * la conversión Markdown→HTML, la sanitización, y la detección/extracto
 * de diagramas Mermaid que antes estaba duplicada en slidePreviewContent(),
 * renderPreviewContent() y ensureMermaidWrapper().
 */
class LmsContentRendererService
{
    private LmsHtmlSanitizerService $htmlSanitizer;
    private LmsTextSanitizerService $textSanitizer;

    public function __construct(
        LmsHtmlSanitizerService $htmlSanitizer,
        LmsTextSanitizerService $textSanitizer
    ) {
        $this->htmlSanitizer = $htmlSanitizer;
        $this->textSanitizer = $textSanitizer;
    }

    // ─── API pública (métodos extraídos del LessonWizard) ───────

    /**
     * Renderiza el contenido de una slide para la vista previa.
     * Recibe el array de la slide actual (contenido, type, etc.).
     *
     * @param array|null $currentSlide Slide actual (ej: $wizardSections[$index]).
     * @return string HTML renderizado.
     */
    public function slidePreviewContent(?array $currentSlide): string
    {
        if (! $currentSlide) {
            return '';
        }

        $blocks = collect($currentSlide['contents'] ?? [])
            ->filter(fn ($c) => ! empty($c['body']))
            ->values();

        $rendered = $blocks->map(function (array $block, int $idx): string {
            $body = $block['body'] ?? '';
            $type = $block['type'] ?? 'TEXT';
            $wrapperClass = 'slide-block slide-block-'.($idx % 2 === 0 ? 'even' : 'odd');

            // ─── IMAGE: SVG/ilustración — render raw, sin mathContent ──
            if ($type === 'IMAGE' || preg_match('/<svg\b/', $body)) {
                return '<div class="'.$wrapperClass.'">'
                    ."\n".$body."\n"
                    .'</div>';
            }

            // ─── MERMAID: detectar por clase CSS o keyword ─────────────
            $isMermaid = $this->hasMermaidClass($body);
            if (! $isMermaid) {
                $isMermaid = $this->isMermaidKeyword($body, true);
            }

            if ($isMermaid) {
                $mermaidCode = $this->extractMermaidCode($body);

                return '<div class="'.$wrapperClass.'">'
                    ."\n"
                    .'<div wire:ignore x-data="mermaidEmbed()"'
                    .' data-mermaid-code="'.htmlspecialchars($mermaidCode, ENT_QUOTES, 'UTF-8').'"'
                    .' class="w-full bg-white rounded-xl p-4 overflow-x-auto border border-slate-200">'
                    .'<div x-ref="target" class="w-full"></div>'
                    .'</div>'
                    ."\n"
                    .'</div>';
            }

            // ─── HTML: contenido semántico estructurado, render raw ──
            if ($type === 'HTML') {
                $sanitized = $this->htmlSanitizer->sanitize($body);

                return '<div class="'.$wrapperClass.'">'
                    ."\n"
                    .'<div class="prose max-w-none">'
                    ."\n".$sanitized."\n"
                    .'</div>'
                    ."\n"
                    .'</div>';
            }

            // ─── MATH: render con mathContent (KaTeX para LaTeX) ──────
            if ($type === 'MATH') {
                $html = $this->renderContentBody($body, 'MATH');

                return '<div class="'.$wrapperClass.'">'
                    ."\n"
                    .'<div x-data="mathContent()" data-math-content="'.htmlspecialchars($html, ENT_QUOTES, 'UTF-8').'">'
                    .'<div wire:ignore><div x-ref="target"></div></div>'
                    .'</div>'
                    ."\n"
                    .'</div>';
            }

            // ─── TEXT / default: render sin mathContent ──────────────
            $html = $this->renderContentBody($body, 'TEXT');

            return '<div class="'.$wrapperClass.'">'
                ."\n"
                .'<div class="prose max-w-none">'
                ."\n".$html."\n"
                .'</div>'
                ."\n"
                .'</div>';
        });

        return $rendered->implode("\n");
    }

    /**
     * Renderiza el body de un bloque de contenido.
     *
     * Para TEXT: convierte Markdown a HTML primero (incluso si contiene
     *           HTML mixto), luego sanitiza.
     * Para MATH: solo sanitiza, preservando LaTeX (\(...\) y $$...$$)
     *            que el markdown rompería (subíndices con _).
     */
    public function renderContentBody(?string $body, string $type = 'TEXT'): string
    {
        if (empty($body)) {
            return '';
        }

        // MATH: body es HTML procesado con LaTeX — no convertir markdown
        if ($type === 'MATH') {
            return $this->htmlSanitizer->sanitize($body);
        }

        // TEXT: siempre convertir markdown primero, luego sanitizar
        $html = \Illuminate\Support\Str::markdown($body);
        // Defense-in-depth: eliminar marcadores markdown ## residuales
        $html = preg_replace('/(?:^|(?<=>))#{1,6}\s+/m', '', $html);
        if (\Illuminate\Support\Str::contains($html, '<')) {
            return $this->htmlSanitizer->sanitize($html);
        }

        return $html;
    }

    /**
     * Renderiza el body para el modal de previsualización.
     * Si contiene Mermaid, lo envuelve en el componente Alpine mermaidEmbed().
     */
    public function renderPreviewContent(string $body): string
    {
        // Detectar si el body contiene un div.mermaid
        $mermaidCode = $this->extractMermaidCodeFromPreview($body);
        if ($mermaidCode !== null) {
            return '<div class="slide-block">'
                ."\n"
                .'<div wire:ignore x-data="mermaidEmbed()"'
                .' data-mermaid-code="'.htmlspecialchars($mermaidCode, ENT_QUOTES, 'UTF-8').'"'
                .' class="w-full bg-white rounded-xl p-4 overflow-x-auto border border-slate-200">'
                .'<div x-ref="target" class="w-full"></div>'
                .'</div>'
                ."\n"
                .'</div>';
        }

        return $this->renderContentBody($body);
    }

    /**
     * Renderiza las preguntas de repaso con formato visual mejorado.
     */
    public function renderReviewQuestions(string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Eliminar el título "## Preguntas de Repaso" si existe
        $body = preg_replace('/^##\s+Preguntas?\s+de\s+Repaso\s*\n+/im', '', $markdown);
        $body = trim($body);

        if (empty($body)) {
            return '';
        }

        // Convertir markdown a HTML
        $html = \Illuminate\Support\Str::markdown($body);

        return '<div class="review-questions">'."\n".$html."\n".'</div>';
    }

    /**
     * Normaliza un embed existente: detecta si es Mermaid y extrae el
     * código plano desde formatos legacy (data-mermaid-code, div.mermaid).
     */
    public function ensureMermaidWrapper(array $embed): array
    {
        $content = trim($embed['html_content'] ?? '');

        // 1. Ya procesado (flag is_mermaid)
        if (! empty($embed['is_mermaid'])) {
            return $embed;
        }

        // 2. Código Mermaid suelto (empieza con keyword)
        if ($this->isMermaidKeyword($content, false)) {
            $embed['is_mermaid'] = true;

            return $embed;
        }

        // 3. Formato legacy: data-mermaid-code
        if (preg_match('/data-mermaid-code="([^"]*)"/', $content, $m)) {
            $code = htmlspecialchars_decode($m[1], ENT_QUOTES);
            $embed['html_content'] = $code;
            $embed['is_mermaid'] = true;

            return $embed;
        }

        // 4. Formato legacy: div.mermaid con código inline
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $content, $m)) {
            $innerCode = strip_tags(trim($m[1]));
            if ($this->isMermaidKeyword($innerCode, false)) {
                $embed['html_content'] = $innerCode;
                $embed['is_mermaid'] = true;

                return $embed;
            }
        }

        // 5. No es mermaid
        $embed['is_mermaid'] = false;

        return $embed;
    }

    /**
     * Sanitiza texto delegando en LmsTextSanitizerService.
     */
    public function sanitizeText(?string $text, string $level = 'standard'): ?string
    {
        return $this->textSanitizer->sanitize($text, $level);
    }

    // ─── Detección unificada de Mermaid ──────────────────────────

    /**
     * Verifica si el body coincide con la keyword de un diagrama Mermaid.
     *
     * @param  string  $body       Contenido a evaluar.
     * @param  bool    $multiline  true = busca en cualquier línea (flag m),
     *                             false = solo primera línea (comportamiento
     *                             original de ensureMermaidWrapper).
     * @return bool
     */
    private function isMermaidKeyword(string $body, bool $multiline = false): bool
    {
        $pattern = '/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/'
            .($multiline ? 'm' : '');
        $subject = $multiline ? trim($body) : $body;

        return preg_match($pattern, $subject) === 1;
    }

    /**
     * Verifica si el body contiene un div con clase mermaid.
     */
    private function hasMermaidClass(string $body): bool
    {
        return preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $body) === 1;
    }

    /**
     * Extrae el código Mermaid del body desde un div.mermaid.
     * Si no hay div.mermaid, devuelve el body sin etiquetas HTML.
     */
    private function extractMermaidCode(string $body): string
    {
        preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $body, $m);
        $code = trim(strip_tags($m[1] ?? ''));
        if (empty($code)) {
            $code = trim(strip_tags($body));
        }

        return $code;
    }

    /**
     * Extrae código Mermaid del body para renderPreviewContent.
     * Retorna null si no detecta un div.mermaid con contenido.
     */
    private function extractMermaidCodeFromPreview(string $body): ?string
    {
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $body, $m)) {
            $code = trim(strip_tags($m[1]));
            if (! empty($code)) {
                return $code;
            }
        }

        return null;
    }
}
