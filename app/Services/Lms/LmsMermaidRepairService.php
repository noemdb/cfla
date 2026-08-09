<?php

namespace App\Services\Lms;

/**
 * Reparación determinista de diagramas Mermaid (sin IA) para el módulo LMS.
 *
 * Fuente única de verdad de las reglas de formato de diagramas:
 *   - postProcess(): fuerza orientación graph TD, reinserta espacios en
 *     labels concatenados (error recurrente de los LLM, ej.
 *     "AutoconocimientoVocacional") y parte etiquetas largas en multi-línea
 *     con <br/>.
 *   - validate(): reglas de impresión en columnas estrechas (~450px):
 *     ≤12 nodos, ≤11 flechas, labels ≤30 chars/línea y sin palabras
 *     concatenadas (runs de 23+ caracteres sin espacio).
 *
 * Usado por el LessonWizard (post-proceso de generación y reparación IA) y
 * por el comando `lms:repair-mermaid` (revisión/reparación de la BD en
 * producción). Operación 100% determinista y sin coste de API.
 */
class LmsMermaidRepairService
{
    public function __construct(
        private readonly LmsContentClassifier $classifier,
    ) {}

    /** ¿El body contiene un diagrama Mermaid (wrapper o código plano)? */
    public function hasMermaid(string $body): bool
    {
        return $this->classifier->isMermaidBody($body);
    }

    /** Extrae SOLO el código Mermaid del body (wrapper HTML o código plano). */
    public function extractMermaidCode(string $body): string
    {
        return $this->classifier->extractMermaidCode($body);
    }

    /**
     * Post-procesa un diagrama Mermaid: fuerza graph TD y repara los labels.
     *
     * @param  string  $code          Código Mermaid (con o sin wrapper HTML).
     * @param  bool    $forceGraphTd  Forzar orientación vertical top-down.
     */
    public function postProcess(string $code, bool $forceGraphTd = true): string
    {
        if ($forceGraphTd) {
            $code = preg_replace('/^graph\s+(LR|RL|BT)\b/m', 'graph TD', $code);
            $code = preg_replace('/^flowchart\s+(LR|RL|BT)\b/m', 'graph TD', $code);
        }

        // 1. Reinsertar espacios en labels con palabras concatenadas
        //    (ej. "AutoconocimientoVocacional" → "Autoconocimiento Vocacional").
        //    Cubre labels quoted ["..."] y labels planos [Texto].
        $code = preg_replace_callback('/\["([^"]*)"/', function ($m) {
            return '["'.$this->normalizeLabelSpacing($m[1]).'"';
        }, $code);
        $code = preg_replace_callback('/(?<![A-Za-z0-9_])\[([^\]"#;]{4,})\]/', function ($m) {
            return '['.$this->normalizeLabelSpacing($m[1]).']';
        }, $code);

        // 2. Partir etiquetas largas (35+ chars) en multi-línea con <br/>.
        //    Idempotente: respeta los <br/> ya existentes (cada segmento se
        //    re-particiona por palabras de forma independiente), de modo que
        //    postProcess(postProcess(x)) === postProcess(x).
        return preg_replace_callback('/\["([^"]{35,})"\]/', function ($m) {
            $text = $m[1];
            $lines = [];
            foreach (preg_split('/<br\s*\/?>/i', $text) as $segment) {
                $words = preg_split('/\s+/', trim($segment));
                $currentLine = '';
                foreach ($words as $word) {
                    $test = $currentLine ? $currentLine.' '.$word : $word;
                    if (mb_strlen($test) > 28 && $currentLine) {
                        $lines[] = $currentLine;
                        $currentLine = $word;
                    } else {
                        $currentLine = $test;
                    }
                }
                if ($currentLine !== '') {
                    $lines[] = $currentLine;
                }
            }

            return '["'.implode('<br/>', $lines).'"]';
        }, $code);
    }

    /**
     * Reinserta espacios en labels Mermaid con palabras concatenadas sin
     * espacios (error recurrente de los LLM, ej. "AutoconocimientoVocacional").
     *
     * Orden de aplicación (conectores primero, camelCase después):
     *   1. Conector pegado al inicio de palabra ("yMotivación" → "y Motivación").
     *   2. Conector pegado tras una palabra ("VocacionalesyAcertadas" → "Vocacionales y Acertadas").
     *   3. Frontera camelCase: "CapacidadesNaturalesyDesarrolladas" → "Capacidades Naturales y Desarrolladas"
     *      (ya separado el "y" por el paso 2).
     *
     * Lista de conectores restringida a los que rara vez son final de palabra
     * en español (se excluyen e/la/las/el/los por colisionar con "Viaje",
     * "Nivel", "Escala"...). El texto ya correctamente espaciado no cambia, y
     * palabras como "eLearning" (una sola minúscula antes de la mayúscula) NO
     * se separan.
     */
    public function normalizeLabelSpacing(string $label): string
    {
        $connectors = 'y|de|del|en|al|con|por|para|su|sus|mi|mis|tu|tus|un|una|unos|unas';

        // 1. Conector pegado al inicio de palabra ("yMotivación").
        $label = preg_replace('/\b((?i:'.$connectors.'))(?=[A-ZÁÉÍÓÚÑÜ])/u', '$1 ', $label);

        // 2. Conector pegado tras una palabra ("VocacionalesyAcertadas").
        $label = preg_replace('/(?<=[a-záéíóúñü])((?i:'.$connectors.'))(?=[A-ZÁÉÍÓÚÑÜ])/u', ' $1 ', $label);

        // 3. Frontera camelCase: 2+ minúsculas/acentos seguidos de mayúscula.
        //    (lookbehind de longitud fija: dos caracteres minúsculos explícitos)
        $label = preg_replace('/(?<=[a-záéíóúñü][a-záéíóúñü])(?=[A-ZÁÉÍÓÚÑÜ])/u', ' ', $label);

        // Espacios múltiples residuales.
        return preg_replace('/\s{2,}/u', ' ', $label);
    }

    /**
     * Valida un diagrama Mermaid para impresión en columnas estrechas (~450px).
     *
     * @return array{ok: bool, issues: array, nodes: int, arrows: int, maxLabel: int}
     */
    public function validate(string $src): array
    {
        $issues = [];

        $nodes = preg_match_all('/[A-Za-z_][A-Za-z0-9_]*\s*(?:\[|\[\[|\(|\(\(|\{|%|>)/', $src);
        if ($nodes > 14) {
            $issues[] = "demasiados nodos ({$nodes}; máximo recomendado 12)";
        }

        $arrows = preg_match_all('/--[->]|==>|\.\.->/s', $src);
        if ($arrows > 16) {
            $issues[] = "demasiadas flechas ({$arrows}; máximo recomendado 11)";
        }

        // Label más largo (solo labels quoted ["..."] o arreglos multi-línea):
        // mide POR LÍNEA (los <br/> existentes no suman caracteres).
        $maxLabel = 0;
        if (preg_match_all('/\["([^"]*)"/', $src, $mLabels)) {
            foreach ($mLabels[1] as $label) {
                foreach (preg_split('/<br\s*\/?>/i', $label) as $line) {
                    $maxLabel = max($maxLabel, mb_strlen($line));
                }

                // Palabras concatenadas sin espacios (ej. "AutoconocimientoVocacional"):
                // un run de 23+ caracteres sin espacio delata la concatenación.
                if (preg_match('/[^\s]{23,}/u', preg_replace('/<br\s*\/?>/i', ' ', $label))) {
                    $issues[] = "hay palabras concatenadas sin espacios en una etiqueta (\"{$label}\"; separa cada palabra con espacio o <br/>)";
                }
            }
        }
        if ($maxLabel > 30) {
            $issues[] = "hay etiquetas de {$maxLabel} caracteres en una sola línea (máximo 30 por línea; separa con <br/>)";
        }

        return [
            'ok' => empty($issues),
            'issues' => $issues,
            'nodes' => $nodes,
            'arrows' => $arrows,
            'maxLabel' => $maxLabel,
        ];
    }

    /**
     * Repara el body completo de un contenido/embed: extrae el Mermaid, lo
     * post-procesa y lo re-inserta preservando el wrapper HTML si existía.
     * Devuelve el body sin cambios si no hay Mermaid o nada que corregir.
     */
    public function repairBody(string $body): string
    {
        $src = $this->extractMermaidCode($body);
        if (trim($src) === '') {
            return $body;
        }

        $fixed = $this->postProcess($src);
        if ($fixed === $src) {
            return $body;
        }

        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>/s', $body)) {
            return preg_replace_callback(
                '/(<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>)(.*?)(<\/div>)/s',
                fn ($m) => $m[1]."\n".$fixed."\n".$m[3],
                $body,
                1
            );
        }

        // Código desnudo (típico de LmsHtmlEmbed.html_content)
        return $fixed;
    }
}
