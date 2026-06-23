<?php

namespace App\Services\Lms;

/**
 * Servicio robusto para sanitizar textos generados por IA o ingresados
 * por usuarios en el módulo LMS. Elimina espacios excesivos, marcadores
 * markdown, caracteres de control y normaliza el texto.
 */
class LmsTextSanitizerService
{
    /**
     * Nivel de saneamiento predeterminado.
     */
    public const LEVEL_BASIC = 'basic';   // solo trim + espacios
    public const LEVEL_STANDARD = 'standard'; // + saltos de línea + **
    public const LEVEL_AGGRESSIVE = 'aggressive'; // + markdown completo + unicode

    /**
     * Sanitiza un texto aplicando las reglas del nivel indicado.
     *
     * @param  string|null  $text   Texto a sanitizar.
     * @param  string       $level  Nivel: 'basic', 'standard' (default) o 'aggressive'.
     * @return string|null
     */
    public function sanitize(?string $text, string $level = self::LEVEL_STANDARD): ?string
    {
        if ($text === null) {
            return null;
        }

        $text = match ($level) {
            self::LEVEL_BASIC      => $this->basic($text),
            self::LEVEL_AGGRESSIVE => $this->aggressive($text),
            default                => $this->standard($text),
        };

        return $text;
    }

    // ─── Niveles ─────────────────────────────────────────────────

    /**
     * Nivel básico: solo trim y espacios múltiples.
     */
    private function basic(string $text): string
    {
        $text = $this->stripControlChars($text);
        $text = $this->stripSpaces($text);
        return trim($text);
    }

    /**
     * Nivel estándar (recomendado para contenido pedagógico).
     *
     * - Trim + espacios múltiples
     * - ** markdown bold → texto plano
     * - Saltos de línea excesivos → máximo 2 seguidos
     * - Caracteres de control no imprimibles
     */
    private function standard(string $text): string
    {
        $text = $this->basic($text);
        $text = $this->stripMarkdownBold($text);
        $text = $this->stripExcessiveNewlines($text);
        $text = $this->stripUnicodeSpaces($text);
        return trim($text);
    }

    /**
     * Nivel agresivo: elimina todo markdown y normaliza al máximo.
     *
     * - Todo lo de standard
     * - *cursiva* → texto plano
     * - `código` → texto plano
     * - Enlaces [texto](url) → solo texto
     * - Imágenes ![alt](url) → solo alt
     * - Viñetas markdown (-, *, +) al inicio de línea → texto plano
     * - # encabezados → texto plano
     * - > citas → texto plano
     */
    private function aggressive(string $text): string
    {
        $text = $this->standard($text);
        $text = $this->stripMarkdownItalic($text);
        $text = $this->stripMarkdownInlineCode($text);
        $text = $this->stripMarkdownLinks($text);
        $text = $this->stripMarkdownImages($text);
        $text = $this->stripMarkdownHeadings($text);
        $text = $this->stripMarkdownBlockquotes($text);
        $text = $this->stripMarkdownListMarkers($text);
        return trim($text);
    }

    // ─── Reglas individuales ─────────────────────────────────────

    /**
     * Elimina caracteres de control no imprimibles (excepto \n, \r, \t).
     */
    public function stripControlChars(string $text): string
    {
        // Mantiene \n (0x0A), \r (0x0D), \t (0x09); elimina el resto < 0x20
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
    }

    /**
     * Convierte 3+ espacios consecutivos en 1 espacio.
     */
    public function stripSpaces(string $text): string
    {
        return preg_replace('/[ ]{3,}/', ' ', $text);
    }

    /**
     * Convierte 4+ saltos de línea consecutivos en 2 saltos.
     */
    public function stripExcessiveNewlines(string $text): string
    {
        return preg_replace('/\n{4,}/', "\n\n", $text);
    }

    /**
     * Elimina **marcador** markdown bold, preservando el texto interno.
     */
    public function stripMarkdownBold(string $text): string
    {
        return preg_replace('/\*\*(.+?)\*\*/', '$1', $text);
    }

    /**
     * Elimina *marcador* markdown cursiva, preservando el texto interno.
     */
    public function stripMarkdownItalic(string $text): string
    {
        return preg_replace('/\*(.+?)\*/', '$1', $text);
    }

    /**
     * Elimina `código inline` markdown, preservando el texto interno.
     */
    public function stripMarkdownInlineCode(string $text): string
    {
        return preg_replace('/`([^`]+)`/', '$1', $text);
    }

    /**
     * Elimina enlaces markdown [texto](url) → solo "texto".
     */
    public function stripMarkdownLinks(string $text): string
    {
        return preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);
    }

    /**
     * Elimina imágenes markdown ![alt](url) → solo "alt".
     */
    public function stripMarkdownImages(string $text): string
    {
        return preg_replace('/!\[([^\]]*)\]\([^)]+\)/', '$1', $text);
    }

    /**
     * Elimina marcadores de encabezados markdown (#, ##, etc.).
     */
    public function stripMarkdownHeadings(string $text): string
    {
        return preg_replace('/^#{1,6}\s+/m', '', $text);
    }

    /**
     * Elimina marcadores de citas markdown (>).
     */
    public function stripMarkdownBlockquotes(string $text): string
    {
        return preg_replace('/^>\s?/m', '', $text);
    }

    /**
     * Elimina viñetas markdown (-, *, +) al inicio de línea.
     */
    public function stripMarkdownListMarkers(string $text): string
    {
        // Viñetas -, *, + seguidas de espacio
        $text = preg_replace('/^[-*+]\s+/m', '', $text);
        // Viñetas numeradas 1. 2. etc.
        $text = preg_replace('/^\d+\.\s+/m', '', $text);
        return $text;
    }

    /**
     * Normaliza espacios unicode (nbsp, thin space, etc.) a espacio normal.
     */
    public function stripUnicodeSpaces(string $text): string
    {
        // \u{00A0} = non-breaking space, \u{2000}-\u{200A} = varios tipos de espacios unicode
        // \u{202F} = narrow no-break space, \u{205F} = medium mathematical space
        // \u{3000} = ideographic space (CJK)
        return preg_replace('/[\x{00A0}\x{2000}-\x{200A}\x{202F}\x{205F}\x{3000}]/u', ' ', $text);
    }

    /**
     * Normaliza la puntuación: comillas curvas a rectas, guiones largos a cortos.
     */
    public function normalizePunctuation(string $text): string
    {
        $replacements = [
            "\xC2\xAB" => '"',  // «
            "\xC2\xBB" => '"',  // »
            "\xE2\x80\x98" => "'",  // '
            "\xE2\x80\x99" => "'",  // '
            "\xE2\x80\x9C" => '"',  // "
            "\xE2\x80\x9D" => '"',  // "
            "\xE2\x80\x93" => '-',  // –
            "\xE2\x80\x94" => '-',  // —
        ];
        return strtr($text, $replacements);
    }

    /**
     * Método conveniente: aplica el nivel estándar + normaliza puntuación.
     * Útil para contenido pedagógico que se mostrará a estudiantes.
     */
    public function forDisplay(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        $text = $this->sanitize($text, self::LEVEL_STANDARD);
        $text = $this->normalizePunctuation($text);
        return trim($text);
    }

    /**
     * Método conveniente: para títulos cortos (solo básico + bold).
     */
    public function forTitle(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        $text = $this->stripControlChars($text);
        $text = $this->stripSpaces($text);
        $text = $this->stripMarkdownBold($text);
        return trim($text);
    }

    /**
     * Método conveniente: para contenido técnico (Mermaid, HTML, código).
     */
    public function forTechnical(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        $text = $this->stripControlChars($text);
        $text = $this->stripUnicodeSpaces($text);
        return trim($text);
    }
}
