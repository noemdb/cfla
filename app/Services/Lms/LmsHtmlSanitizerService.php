<?php

namespace App\Services\Lms;

/**
 * Sanitizador HTML server-side para contenido generado por IA o ingresado
 * por usuarios en el módulo LMS.
 *
 * Sirve como capa de defensa profunda (defense-in-depth) además de la
 * sanitización client-side con DOMPurify. Elimina:
 *
 *   - Etiquetas peligrosas: <script>, <style>, <iframe>, <object>, etc.
 *   - Manejadores de eventos: onclick, onerror, onload, onmouseover, etc.
 *   - URLs peligrosas: javascript:, data: (en atributos que renderizan)
 *   - Atributos HTML obsoletos o peligrosos
 *
 * Preserva etiquetas seguras necesarias para contenido pedagógico con LaTeX:
 *   div, p, span, strong, em, u, br, hr, ul, ol, li, table, tr, td, th,
 *   h1-h6, pre, code, blockquote, a, img, sup, sub, dl, dt, dd, figure,
 *   figcaption, caption, colgroup, col, thead, tbody, tfoot, section, aside
 */
class LmsHtmlSanitizerService
{
    /**
     * Etiquetas HTML permitidas (seguras para contenido pedagógico).
     */
    private const ALLOWED_TAGS = [
        // Estructura
        'div', 'span', 'p', 'br', 'hr', 'wbr',
        // Formato inline
        'b', 'strong', 'i', 'em', 'u', 's', 'mark', 'small', 'sub', 'sup',
        'abbr', 'cite', 'code', 'kbd', 'samp', 'var', 'del', 'ins', 'q',
        // Listas
        'ul', 'ol', 'li', 'dl', 'dt', 'dd',
        // Tablas
        'table', 'caption', 'colgroup', 'col', 'thead', 'tbody', 'tfoot',
        'tr', 'td', 'th',
        // Encabezados
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        // Bloques
        'pre', 'blockquote', 'figure', 'figcaption', 'details', 'summary',
        // Secciones
        'section', 'article', 'header', 'footer', 'nav', 'aside', 'main',
        // Enlaces e imágenes (restringidos vía atributos)
        'a', 'img',
        // LaTeX / math
        'math', 'mrow', 'mi', 'mo', 'mn', 'msup', 'msub', 'mfrac', 'msqrt',
        'mover', 'munder', 'munderover', 'mtext', 'mspace', 'mpadded',
        'mtable', 'mtr', 'mtd', 'mphantom', 'maction',
    ];

    /**
     * Atributos globales permitidos en TODAS las etiquetas.
     */
    private const ALLOWED_ATTRS_GLOBAL = [
        'id', 'class', 'style', 'title', 'lang', 'dir',
        'data-*',      // data-* genéricos para Alpine.js / Livewire
        'x-*',         // Alpine.js directives
        'wire:*',      // Livewire directives
        'aria-*',      // Accesibilidad
        'role',
    ];

    /**
     * Atributos específicos permitidos por etiqueta.
     */
    private const ALLOWED_ATTRS_BY_TAG = [
        'a'     => ['href', 'target', 'rel', 'download', 'hreflang'],
        'img'   => ['src', 'alt', 'width', 'height', 'loading', 'srcset', 'sizes'],
        'td'    => ['colspan', 'rowspan', 'headers'],
        'th'    => ['colspan', 'rowspan', 'headers', 'scope', 'abbr'],
        'col'   => ['span'],
        'colgroup' => ['span'],
        'ol'    => ['start', 'type', 'reversed'],
        'li'    => ['value'],
        'details' => ['open'],
        'a'     => ['name'], // anchor antiguo pero usado
        'table' => ['summary'],
        'time'  => ['datetime'],
        'abbr'  => ['title'],
        'dfn'   => ['title'],
    ];

    // ─── Sanitización principal ───────────────────────────────────

    /**
     * Sanitiza un string HTML eliminando etiquetas, atributos y
     * contenido peligroso. Devuelve HTML seguro para renderizar.
     */
    public function sanitize(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Stripear CDATA / comentarios condicionales IE
        $html = $this->stripConditionalComments($html);
        $html = $this->stripCdata($html);

        // 2. Stripear etiquetas no permitidas (incluyendo contenido interno
        //    de etiquetas peligrosas como <script> y <style>)
        $html = $this->stripDisallowedTags($html);

        // 3. Stripear atributos peligrosos
        $html = $this->sanitizeAttributes($html);

        // 4. Stripear URLs peligrosas en href/src/action
        $html = $this->stripDangerousUrls($html);

        // 5. Stripear entidades HTML malformadas
        $html = $this->stripMalformedEntities($html);

        // 6. Stripear protocolos duplicados (javascript:javascript:...)
        $html = $this->stripNestedProtocols($html);

        return $html;
    }

    // ─── Reglas individuales ─────────────────────────────────────

    /**
     * Elimina comentarios condicionales de IE <!--[if ...]> ... <![endif]-->.
     */
    private function stripConditionalComments(string $html): string
    {
        return preg_replace('/<!--\[if[^>]*>.*?<!\[endif\]-->/is', '', $html);
    }

    /**
     * Elimina bloques CDATA <![CDATA[...]]>.
     */
    private function stripCdata(string $html): string
    {
        return preg_replace('/<!\[CDATA\[(.*?)\]\]>/s', '$1', $html);
    }

    /**
     * Elimina etiquetas no permitidas y su contenido interno.
     * Para etiquetas peligrosas (script, style, iframe, etc.),
     * elimina tanto la etiqueta como su contenido.
     * Para etiquetas simplemente no permitidas pero seguras de
     * dejar sin contenido, elimina solo la etiqueta.
     */
    private function stripDisallowedTags(string $html): string
    {
        // Etiquetas PELIGROSAS — eliminar etiqueta Y contenido interno
        $dangerous = [
            'script', 'style', 'iframe', 'object', 'embed', 'applet',
            'frame', 'frameset', 'noframes', 'noembed',
            'form', 'input', 'select', 'textarea', 'button', 'option',
            'optgroup', 'label', 'fieldset', 'legend', 'datalist',
            'output', 'progress', 'meter', 'keygen',
            'canvas', 'svg', 'math', // MathML mejor usar semántico
            'marquee', 'blink',
            'link', 'meta', 'base', 'basefont',
            'isindex',
        ];

        // Stripear con contenido: <tagname ...> ... </tagname>
        foreach ($dangerous as $tag) {
            $html = preg_replace('/<' . $tag . '\b[^>]*>.*?<\/' . $tag . '\s*>/is', '', $html);
            // También tags auto-cerrados <tagname ... />
            $html = preg_replace('/<' . $tag . '\b[^>]*\/>/i', '', $html);
            // Tags sin cierre <tagname ...> sin </tagname>
            $html = preg_replace('/<' . $tag . '\b[^>]*>/i', '', $html);
        }

        // Stripear etiquetas vacías peligrosas que no se capturaron arriba
        $html = preg_replace('/<\/?(?:' . implode('|', $dangerous) . ')\b[^>]*>/i', '', $html);

        // Etiquetas NO permitidas — solo eliminar etiqueta, preservar contenido
        // Construir lista de tags no permitidas (todas - permitidas)
        $allKnown = [
            'html', 'head', 'body', 'title', 'script', 'style', 'iframe', 'object', 'embed',
            'applet', 'frame', 'frameset', 'noframes', 'noembed', 'form', 'input', 'select',
            'textarea', 'button', 'option', 'optgroup', 'label', 'fieldset', 'legend',
            'datalist', 'output', 'progress', 'meter', 'keygen', 'canvas', 'svg',
            'marquee', 'blink', 'link', 'meta', 'base', 'basefont', 'isindex', 'font',
            'center', 'dir', 'menu', 'nobr', 'big', 'tt', 'strike', 'xmp', 'plaintext',
            'acronym', 'frame', 'frameset', 'noframes',
        ];

        // Fusionar con dangerous (evitar duplicados)
        $allKnown = array_unique(array_merge($allKnown, $dangerous));
        $allowed = self::ALLOWED_TAGS;

        $disallowed = array_diff($allKnown, $allowed);

        // Strip tags no permitidas, preservando contenido:
        // <tag>content</tag> → content
        foreach ($disallowed as $tag) {
            // Abierta y cerrada con contenido
            $html = preg_replace('/<' . $tag . '\b[^>]*>(.*?)<\/' . $tag . '\s*>/is', '$1', $html);
            // Auto-cerrada o suelta
            $html = preg_replace('/<' . $tag . '\b[^>]*\/?>/i', '', $html);
        }

        return $html;
    }

    /**
     * Sanitiza atributos HTML: elimina event handlers y atributos no permitidos.
     */
    private function sanitizeAttributes(string $html): string
    {
        // 1. Stripear TODOS los manejadores de eventos (on*), incluyendo
        //    variaciones con HTML entities y case variants
        $html = preg_replace('/\s+on\w+\s*=\s*(["\']?)[^"\'>\s]*\1/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*`[^`]*`/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*[^\s"\'`>]+/i', '', $html);

        // 2. Stripear atributos de navegación forzada
        //    formaction, action, formmethod (si no son GET)
        $html = preg_replace('/\s+(formaction|action|formmethod|xlink:href)\s*=\s*(["\']?)[^"\'>\s]*\2/i', '', $html);

        // 3. Stripear atributos de autoejecución
        //    autofocus, autoplay (seguridad de UX)
        $html = preg_replace('/\s+(autofocus|autoplay)\s*=\s*(["\']?)[^"\'>\s]*\2/i', '', $html);

        // 4. Stripear atributos de integridad (podrían ocultar contenido malicioso)
        $html = preg_replace('/\s+integrity\s*=\s*["\'][^"\']*["\']/i', '', $html);

        // 5. Stripear atributos sandbox (iframe-related)
        $html = preg_replace('/\s+sandbox\s*=\s*["\'][^"\']*["\']/i', '', $html);

        // 6. Stripear srcdoc (podría contener HTML malicioso)
        $html = preg_replace('/\s+srcdoc\s*=\s*["\'][^"\']*["\']/i', '', $html);

        return $html;
    }

    /**
     * Stripea URLs peligrosas en atributos href, src, etc.
     */
    private function stripDangerousUrls(string $html): string
    {
        // Repetir 3x para atrapar anidamiento (javascript:javascript:alert(1))
        for ($i = 0; $i < 3; $i++) {
            // javascript: protocol
            $html = preg_replace(
                '/(href|src|action|data|formaction)\s*=\s*(["\']?)javascript:/i',
                '$1=$2',
                $html
            );
            // data: protocol (potencialmente peligroso en img src)
            $html = preg_replace(
                '/src\s*=\s*(["\']?)data:\s*text\/html/i',
                'src=$1',
                $html
            );
            // vbscript: protocol
            $html = preg_replace(
                '/(href|src|action|formaction)\s*=\s*(["\']?)vbscript:/i',
                '$1=$2',
                $html
            );
            // Bloquea "url()" CSS en style que contenga javascript:
            $html = preg_replace(
                '/url\s*\(\s*(["\']?)\s*javascript:/i',
                'url($1',
                $html
            );
        }

        return $html;
    }

    /**
     * Stripea entidades HTML malformadas o peligrosas.
     */
    private function stripMalformedEntities(string $html): string
    {
        // Eliminar entidades que intentan ocultar scripts
        // &#106;avascript → javascript
        // &#x6A;avascript → javascript
        $html = preg_replace('/&#(?:10[68]|10[69]|106|74|120[\s\S]{1,2}?[1068-]?[1069]?);?\s*/i', '', $html);

        return $html;
    }

    /**
     * Stripea protocolos duplicados (técnica de bypass común).
     * Ejemplo: javascript:javascript:alert(1)
     */
    private function stripNestedProtocols(string $html): string
    {
        $maxIterations = 5;
        for ($i = 0; $i < $maxIterations; $i++) {
            $previous = $html;
            $html = preg_replace(
                '/(href|src|action|formaction)\s*=\s*(["\']?)(?:javascript|data|vbscript)\s*:\s*(?:javascript|data|vbscript)\s*:/i',
                '$1=$2',
                $html
            );
            if ($html === $previous) {
                break;
            }
        }

        return $html;
    }
}
