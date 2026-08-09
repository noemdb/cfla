<?php

namespace App\Services\Lms;

/**
 * Reparación IA de diagramas Mermaid para el módulo LMS.
 *
 * complementa LmsMermaidRepairService (determinista) con reparación
 * basada en IA para diagramas que requieren reestructuración (demasiados
 * nodos, flechas rotas, sintaxis ilegible, etc.).
 *
 * Flujo:
 *   1. Extrae código Mermaid del body
 *   2. Valida con LmsMermaidRepairService::validate()
 *   3. Si tiene issues → llama a la cadena de modelos de diagramas
 *   4. Valida la respuesta de la IA
 *   5. Si la IA falla → reintenta con feedback de corrección
 *   6. Post-procesa (graph TD, labels multi-línea)
 *   7. Reinserta preservando wrapper HTML original
 *
 * Cadena de modelos (misma que LessonWizard):
 *   - Primario: config('openrouter.model_diagram_primary')
 *   - Fallback 1: config('openrouter.model_diagram_fallback1')
 *   - Fallback 2: config('openrouter.model_diagram_fallback2')
 */
class LmsMermaidAiRepairService
{
    public function __construct(
        private readonly LmsMermaidRepairService $deterministic,
        private readonly LmsContentClassifier $classifier,
        private readonly LmsAiOrchestrationService $ai,
    ) {}

    /**
     * Intenta reparar un diagrama Mermaid con IA.
     *
     * @param  string  $body  Body completo del contenido/embed
     * @return array{ok: bool, newBody: string|null, message: string, attempts: int}
     */
    public function repairBody(string $body): array
    {
        $src = $this->classifier->extractMermaidCode($body);
        if (trim($src) === '') {
            return ['ok' => false, 'newBody' => null, 'message' => 'No se pudo extraer código Mermaid.', 'attempts' => 0];
        }

        // 1. Validar el diagrama actual
        $validation = $this->deterministic->validate($src);
        if ($validation['ok']) {
            return ['ok' => false, 'newBody' => null, 'message' => 'El diagrama ya es válido.', 'attempts' => 0];
        }

        // 2. Intentar reparación con IA
        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt = $this->buildUserPrompt($src);
        $attempts = 0;

        $result = $this->callModel($systemPrompt, $userPrompt);
        $attempts++;

        if (! $result['success']) {
            return ['ok' => false, 'newBody' => null, 'message' => $result['error'] ?? 'Error al llamar al modelo.', 'attempts' => $attempts];
        }

        $code = $this->extractCode($result['content'] ?? '');
        if (empty($code)) {
            return ['ok' => false, 'newBody' => null, 'message' => 'La IA no devolvió código Mermaid válido.', 'attempts' => $attempts];
        }

        // 3. Validar respuesta de la IA
        $codeSrc = $this->extractMermaidSrc($code);
        $validation2 = $this->deterministic->validate($codeSrc);

        // 4. Reintento con feedback si aún tiene issues
        if (! $validation2['ok'] && $validation2['issues']) {
            $feedback = $this->buildCorrectionBlock($codeSrc, $validation2);
            $retry = $this->callModel($systemPrompt, $userPrompt."\n\n".$feedback);
            $attempts++;

            if ($retry['success']) {
                $retryCode = $this->extractCode($retry['content'] ?? '');
                if (! empty($retryCode)) {
                    $code = $retryCode;
                }
            }
        }

        // 5. Post-procesar (graph TD, labels multi-línea)
        $codeSrc = $this->extractMermaidSrc($code);
        $codeSrc = $this->deterministic->postProcess($codeSrc);

        // 6. Reinsertar preservando wrapper HTML
        $newBody = $this->reinsertCode($body, $codeSrc);

        // 7. Validación final
        $finalSrc = $this->classifier->extractMermaidCode($newBody);
        $finalValidation = $this->deterministic->validate($finalSrc);

        if ($finalValidation['ok'] || count($finalValidation['issues']) < count($validation['issues'])) {
            $improved = count($validation['issues']) - count($finalValidation['issues']);
            return [
                'ok' => true,
                'newBody' => $newBody,
                'message' => "Diagrama reparado con IA ({$improved} issue(s) resueltos).",
                'attempts' => $attempts,
            ];
        }

        return [
            'ok' => false,
            'newBody' => null,
            'message' => 'La IA no logró mejorar el diagrama.',
            'attempts' => $attempts,
        ];
    }

    // ── Construcción de prompts ──────────────────────────────────────

    private function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres un Staff Engineer frontend especializado en diagramas Mermaid.js de una plataforma LMS escolar.
Tu tarea es REPARAR y MEJORAR un diagrama Mermaid dañado o descuidado, preservando su significado pedagógico y los textos originales.

REGLAS ESTRICTAS (aplica siempre):
1. Corrige errores de sintaxis Mermaid: nodos sin cerrar, flechas rotas, caracteres sin escapar, ids duplicados.
2. ORIENTACIÓN: SOLO graph TD (top-down, flujo vertical arriba→abajo). PROHIBIDO graph LR, graph RL, graph BT u otras. Máximo 3 niveles de profundidad.
3. TAMAÑO ACOTADO: MÁXIMO 12 nodos y 11 flechas. Si hay más conceptos, agrupa los secundarios en un nodo resumen (ej. "Otros: A, B, C").
4. Labels multi-línea: máximo 30 caracteres POR LÍNEA usando <br/> o arreglos [línea1, línea2]. NUNCA una etiqueta larga en una sola línea.
5. IDs de nodo cortos (A, B, C, N1, N2...). Nunca repitas el texto del label como ID.
6. Sin emojis de relleno en labels (🎯, 🌉, 👤, etc.) — solo texto pedagógico limpio.
7. ESPACIADO DE PALABRAS: CADA palabra conserva SUS espacios. NUNCA concatenes palabras sin espacio (CORRECTO: "Autoconocimiento Vocacional"; INCORRECTO: "AutoconocimientoVocacional"). Español correcto con tildes y puntuación. Al dividir con <br/> o arreglos, divide entre palabras completas.
8. Preserva el significado y los textos del diagrama original. NO inventes contenido educativo nuevo.
9. Responde ÚNICAMENTE el código Mermaid (o el HTML con <div class="mermaid"> si el original estaba envuelto). Sin explicaciones, sin markdown.
PROMPT;
    }

    private function buildUserPrompt(string $mermaidSrc): string
    {
        return <<<PROMPT
### Diagrama Mermaid actual (repáralo)
{$mermaidSrc}

Repara y mejora el diagrama aplicando las reglas del Staff Engineer. Responde SOLO el código.
PROMPT;
    }

    private function buildCorrectionBlock(string $src, array $validation): string
    {
        $block = "El diagrama anterior incumple las reglas de tamaño para impresión y necesita corrección.\n"
            ."Problemas detectados:\n";
        foreach ($validation['issues'] as $issue) {
            $block .= "- {$issue}\n";
        }
        $block .= "\nCorrige el diagrama: reduce el número de nodos (agrupa los secundarios en un nodo resumen), "
            .'acorta los labels a máximo 30 caracteres por línea usando <br/> o arreglos [l1, l2], '
            .'usa IDs de nodo cortos y orientación vertical graph TD. '
            ."Regenera SOLO el código (Mermaid o el card HTML), sin explicaciones.\n\n"
            ."Diagrama anterior:\n```\n".mb_substr($src, 0, 3000)."\n```";

        return $block;
    }

    // ── Llamada al modelo ────────────────────────────────────────────

    private function callModel(string $systemPrompt, string $userPrompt): array
    {
        return $this->ai->askWithCompaction(
            $systemPrompt,
            $userPrompt,
            ['max_tokens' => 4096, 'temperature' => 0.3, 'timeout' => 300],
            3500,
            null,
            [
                ['model' => config('openrouter.model_diagram_primary'),   'label' => 'Diagrama primario'],
                ['model' => config('openrouter.model_diagram_fallback1'), 'label' => 'Diagrama fallback 1'],
                ['model' => config('openrouter.model_diagram_fallback2'), 'label' => 'Diagrama fallback 2'],
            ],
        );
    }

    // ── Utilidades ───────────────────────────────────────────────────

    /**
     * Extrae código Mermaid de la respuesta raw de la IA.
     */
    private function extractCode(string $raw): string
    {
        $code = $raw;
        if (preg_match('/```(?:html|mermaid)?\s*\n?(.*?)```/s', $raw, $m)) {
            $code = trim($m[1]);
        } else {
            $mermaidKeywords = 'flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline';
            if (preg_match('/\b('.$mermaidKeywords.')\s+(LR|TD|BT|RL)?/s', $raw, $kwMatch, PREG_OFFSET_CAPTURE)) {
                $startPos = $kwMatch[0][1];
                $rawCode = substr($raw, $startPos);
                $rawCode = preg_replace('/```\s*$/s', '', $rawCode);
                $code = trim($rawCode);
            }
        }

        // Limpiar scripts y wrappers
        $code = preg_replace('/<script\b[^>]*src=["\'][^"\']*cdn\.(?:tailwindcss|jsdelivr)[^"\']*["\'][^>]*><\/script>\s*/i', '', $code);
        $code = preg_replace('/<script\b[^>]*src=["\'][^"\']*mermaid[^"\']*["\'][^>]*><\/script>\s*/i', '', $code);
        $code = preg_replace('/<script>mermaid\.initialize\(.*?<\/script>\s*/is', '', $code);
        $code = preg_replace('/<\/?(?:html|head|body)[^>]*>\s*/i', '', $code);
        $code = preg_replace('/<meta[^>]*>\s*/i', '', $code);
        $code = preg_replace('/<link\b[^>]*href=["\'][^"\']*cdn\.(?:tailwindcss|jsdelivr)[^"\']*["\'][^>]*>\s*/i', '', $code);
        $code = preg_replace('/\s*<(\/)?div[^>]*>\s*$/s', '', trim($code));
        $code = preg_replace('/\s*<(\/)?div[^>]*>\s*$/s', '', trim($code));

        return trim($code);
    }

    /**
     * Extrae el código Mermaid interno de un bloque HTML
     * (<div class="mermaid">...</div>) preservando los <br/> de labels.
     */
    private function extractMermaidSrc(string $code): string
    {
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $code, $m)) {
            return trim(html_entity_decode(strip_tags($m[1], '<br><br/>')));
        }

        return trim($code);
    }

    /**
     * Reinserta el código Mermaid reparado en el body original,
     * preservando el wrapper HTML si existía.
     */
    private function reinsertCode(string $body, string $code): string
    {
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>/s', $body)) {
            return preg_replace_callback(
                '/(<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>)(.*?)(<\/div>)/s',
                fn ($m) => $m[1]."\n".$code."\n".$m[3],
                $body,
                1
            );
        }

        return $code;
    }
}
