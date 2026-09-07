<?php

namespace App\Services\Lms;

/**
 * Reparaciones locales para contenido LMS cuando la IA no puede responder.
 *
 * Estas operaciones no intentan generar conocimiento nuevo: normalizan
 * estructura, eliminan wrappers inválidos y conservan el contenido recibido.
 */
class LmsDeterministicRepairService
{
    public function __construct(
        private readonly LmsHtmlSanitizerService $htmlSanitizer,
        private readonly LmsTextSanitizerService $textSanitizer,
    ) {}

    /**
     * Normaliza un bloque matemático a la estructura que consume mathContent().
     */
    public function repairMath(string $body): string
    {
        $body = $this->stripCodeFences(trim($body));
        $body = $this->normalizeMathDelimiters($body);
        $body = $this->htmlSanitizer->sanitize($body);

        if (preg_match('/<div\s+id=["\']math-block["\'][^>]*>(.*?)<\/div>/is', $body, $match)) {
            $inner = trim($match[1]);
        } else {
            $inner = trim($body);
        }

        if ($inner === '') {
            return '<div id="math-block"></div>';
        }

        if (! preg_match('/<\s*(p|div|ul|ol|table|h[1-6])\b/i', $inner)) {
            $inner = '<p>'.nl2br($inner, false).'</p>';
        }

        return '<div id="math-block">'.$inner.'</div>';
    }

    /**
     * Limpia Markdown generado con fences o meta-comentarios del modelo.
     */
    public function cleanMarkdown(string $body): string
    {
        $body = $this->stripCodeFences(trim($body));
        $lines = preg_split('/\R/u', $body) ?: [];
        $kept = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($this->isMetaLine($trimmed)) {
                continue;
            }
            $kept[] = rtrim($line);
        }

        $cleaned = trim(implode("\n", $kept));
        $cleaned = preg_replace('/\n{3,}/', "\n\n", $cleaned) ?? $cleaned;

        return trim($this->textSanitizer->sanitize($cleaned, 'basic') ?? '');
    }

    /**
     * Fallback local para un bloque de texto cuando no hay respuesta IA.
     */
    public function fallbackText(string $body, string $title, string $blockType = 'TEXT'): string
    {
        if (strtoupper($blockType) === 'HTML') {
            $safeHtml = $this->htmlSanitizer->sanitize($body);
            if ($safeHtml !== '') {
                return $safeHtml;
            }
        }

        $cleaned = $this->cleanMarkdown($body);
        $plain = trim(strip_tags($cleaned));
        $plain = preg_replace('/\s+/u', ' ', $plain) ?? $plain;

        if ($plain === '') {
            $plain = 'Contenido pendiente de completar.';
        }

        $heading = trim($title) !== '' ? trim($title) : 'Contenido de la lección';

        return "## {$heading}\n\n{$plain}\n\n> Contenido conservado como borrador. Revisa y completa esta diapositiva antes de publicar.";
    }

    private function normalizeMathDelimiters(string $body): string
    {
        $body = preg_replace('/\\\\\[\s*(.*?)\s*\\\\\]/s', '$$ $1 $$', $body) ?? $body;
        $body = preg_replace('/(?<!\\\\)\\\((.*?)\\\)/s', '\\($1\\)', $body) ?? $body;

        $dollarCount = preg_match_all('/(?<!\\\\)\$\$/', $body, $matches);
        if ($dollarCount !== false && $dollarCount % 2 !== 0) {
            $body .= ' $$';
        }

        $inlineCount = preg_match_all('/(?<!\\\\)\\\\\(/', $body, $matches)
            - preg_match_all('/(?<!\\\\)\\\\\)/', $body, $matches);
        if ($inlineCount > 0) {
            $body .= '\\)';
        }

        return $body;
    }

    private function stripCodeFences(string $body): string
    {
        $body = preg_replace('/^```(?:latex|tex|html|markdown|md)?\s*/i', '', $body) ?? $body;
        $body = preg_replace('/\s*```$/', '', $body) ?? $body;

        return trim($body);
    }

    private function isMetaLine(string $line): bool
    {
        if ($line === '') {
            return false;
        }

        return preg_match('/^(?:respuesta|resultado|soluci[oó]n|explicaci[oó]n|nota|observaci[oó]n|aqu[ií] tienes|he mejorado|como modelo|como ia)\s*:/iu', $line) === 1
            || preg_match('/^(?:here(?:\'s| is)|as an ai|i have|output|answer)\b/i', $line) === 1;
    }
}
