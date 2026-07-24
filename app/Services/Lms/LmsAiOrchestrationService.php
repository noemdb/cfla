<?php

namespace App\Services\Lms;

use App\Models\app\Instrument\DiagReferent;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use Psr\Log\LoggerInterface;

/**
 * Orquesta las llamadas a IA (OpenRouter, Nvidia) para el LessonWizard.
 *
 * Responsabilidades:
 *   - Encadenar modelos LLM con fallback automático
 *   - Compactar prompts con Nvidia cuando exceden el token budget
 *   - Validar contenido generado contra reglas de estructura
 *   - Proveer utilidades de parseo (título/descripción, etiquetas, etc.)
 *   - Obtener contexto de referentes normativos
 *
 * NO depende de Livewire ni de propiedades reactivas. Las notificaciones
 * UI se manejan vía callback ($notify) que inyecta el componente.
 */
class LmsAiOrchestrationService
{
    // ─── FALLBACK REINFORCEMENT ─────────────────────────────────
    private const FALLBACK_REINFORCEMENT = <<<'TEXT'

⚠️ CORRECCIÓN — Intento anterior no siguió las instrucciones.

Reglas críticas:
1. Todo en ESPAÑOL académico. NO uses inglés.
2. Usa SOLO el contexto de la actividad — nada de superhéroes, aventuras fantásticas, identidades secretas ni temas genéricos.
3. Estructura exacta:
   //INICIO
   ...
   //DESARROLLO
   Bloque 1
   ...
   (mínimo 5 bloques separados por línea en blanco)
   //CIERRE
   ...
4. Sin meta-comentarios, explicaciones ni introducciones.
5. El ejemplo en las instrucciones es solo para mostrar el FORMATO — usa el contexto real de la actividad.
TEXT;

    public function __construct(
        private readonly OpenRouterService $openRouter,
        private readonly NvidiaService $nvidia,
        private readonly LmsTextSanitizerService $textSanitizer,
        private readonly LoggerInterface $logger,
    ) {}

    // ─── Pure utility methods ───────────────────────────────────

    /**
     * Estima tokens a partir de caracteres (ratio ~3.5 chars/token).
     */
    public function estimateTokens(string $text): int
    {
        return max(1, (int) ceil(mb_strlen($text) / 3.5));
    }

    /**
     * Elimina líneas de anotaciones de seguridad que ciertos modelos
     * (Nvidia, etc.) prefijan en las respuestas.
     *
     * Ejemplos: "User Safety: safe", "**Content Safety:** medium_low",
     * "Output Safety: high", "Safety: safe".
     */
    public function stripSafetyAnnotations(string $text): string
    {
        $lines = explode("\n", $text);
        $filtered = array_filter($lines, function (string $line): bool {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                return true;
            }
            // "User Safety: safe", "**User Safety:** safe"
            if (preg_match('/^(?:\*{1,2}\s*)?(?:User|Content|Output|Model)\s+Safety\s*:\s*(?:\*{1,2}\s*)?\w+/i', $trimmed)) {
                return false;
            }
            // "Safety: high", "**Safety:** safe"
            if (preg_match('/^(?:\*{1,2}\s*)?Safety\s*:\s*(?:\*{1,2}\s*)?\w+\s*$/i', $trimmed)) {
                return false;
            }

            return true;
        });

        return trim(implode("\n", $filtered));
    }

    /**
     * Extrae una descripción legible del error del modelo para mostrarla
     * en la notificación de fallback.
     */
    public function describeModelError(string $errorMsg): string
    {
        if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'Rate limit')) {
            return 'límite de requests excedido';
        }
        if (str_contains($errorMsg, '402') || str_contains($errorMsg, 'Insufficient credits')) {
            return 'créditos insuficientes';
        }
        if (str_contains($errorMsg, '28') || str_contains($errorMsg, 'timed out') || str_contains($errorMsg, 'timeout')) {
            return 'tiempo de espera agotado (60s)';
        }
        if (str_contains($errorMsg, '52') || str_contains($errorMsg, 'Empty reply') || str_contains($errorMsg, 'Connection refused')) {
            return 'el servidor cerró la conexión';
        }
        if (str_contains($errorMsg, '404') || str_contains($errorMsg, '500') || str_contains($errorMsg, '503')) {
            return 'error del modelo ('.$errorMsg.')';
        }
        if (str_contains($errorMsg, 'excedió el límite de tokens') || str_contains($errorMsg, 'content_filter')) {
            return 'el modelo rechazó la solicitud por seguridad o longitud';
        }
        if (str_contains($errorMsg, 'sin contenido') || str_contains($errorMsg, 'finalizó sin contenido')) {
            return 'el modelo finalizó sin generar contenido';
        }

        // Genérico
        $truncated = mb_strlen($errorMsg) > 60 ? mb_substr($errorMsg, 0, 57).'...' : $errorMsg;

        return $truncated;
    }

    /**
     * Elimina prefijos de etiqueta como "Título:" o "Línea 1 →" del texto.
     */
    public function stripLabelPrefix(string $text, array $labels): string
    {
        $text = trim($text);
        foreach ($labels as $label) {
            // Con dos puntos
            if (str_starts_with(mb_strtolower($text), mb_strtolower($label).':')) {
                $text = trim(mb_substr($text, mb_strlen($label) + 1));
            }
            // Con flecha "→"
            if (str_starts_with(mb_strtolower($text), mb_strtolower($label).'→')) {
                $text = trim(mb_substr($text, mb_strlen($label) + 1));
            }
            // Con guión " - " o " -> "
            if (str_starts_with(mb_strtolower($text), mb_strtolower($label).' -')) {
                $text = trim(mb_substr($text, mb_strlen($label) + 2));
            }
        }

        return trim($text);
    }

    /**
     * Parsea la respuesta del LLM extrayendo título y descripción.
     * Soporta múltiples formatos de respuesta:
     *
     *   "Título || Descripción"        (separador ||)
     *   "Título\nDescripción"           (primera línea = título)
     *   "**Título:** ...\n**Descripción:** ..."  (markdown)
     *   "Título: ...\nDescripción: ..." (etiquetas literales)
     *
     * @return array{string, string} [title, description]
     */
    public function parseTitleDescription(string $content): array
    {
        $content = trim($content);
        if (empty($content)) {
            return ['', ''];
        }

        // ── Pre-procesamiento: eliminar líneas de seguridad/anotaciones ──
        $content = $this->stripSafetyAnnotations($content);

        // Si después de filtrar solo quedaban anotaciones de seguridad
        if (empty($content)) {
            return ['', ''];
        }

        // ── Estrategia 1: separador "||" (formato original) ──────
        if (str_contains($content, '||')) {
            $parts = explode('||', $content, 2);
            $title = trim($parts[0]);
            $desc = trim($parts[1] ?? '');
            // Limpiar posibles prefijos tipo "Título:" o "Linea 1 →"
            $title = $this->stripLabelPrefix($title, ['titulo', 'título', 'title', 'linea 1', 'línea 1']);
            $desc = $this->stripLabelPrefix($desc, ['descripcion', 'descripción', 'description', 'linea 2', 'línea 2']);
            if (! empty($title)) {
                return [$title, $desc];
            }
        }

        // ── Estrategia 2: etiquetas markdown "**Título:**" / "**Descripción:**" ─
        $mdPattern = '/\*\*(?:T[íi]tulo|Título|Title|Descripci[oó]n|Description)\s*:\s*\*\*(.*?)(?=\s*\*\*(?:T[íi]tulo|Descripci[oó]n|))\s*/ius';
        if (preg_match_all($mdPattern, $content, $mdMatches)) {
            $title = '';
            $desc = '';
            foreach ($mdMatches[0] as $i => $fullMatch) {
                $value = trim($mdMatches[1][$i] ?? '');
                if (stripos($fullMatch, 'título') !== false || stripos($fullMatch, 'titulo') !== false || stripos($fullMatch, 'title') !== false) {
                    $title = $value;
                } elseif (stripos($fullMatch, 'descripción') !== false || stripos($fullMatch, 'descripcion') !== false || stripos($fullMatch, 'description') !== false) {
                    $desc = $value;
                }
            }
            if (! empty($title) && ! empty($desc)) {
                return [$title, $desc];
            }
        }

        // ── Estrategia 3: etiquetas literales "Título:" / "Descripción:" ──
        $lines = explode("\n", $content);
        $title = '';
        $desc = '';
        $currentLabel = null;
        $buffer = '';
        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if (empty($line)) {
                continue;
            }
            // Detectar etiqueta
            $matched = false;
            foreach (['Título:', 'Titulo:', 'Title:', 'Descripción:', 'Descripcion:', 'Description:'] as $label) {
                if (str_starts_with(mb_strtolower($line), mb_strtolower($label))) {
                    // Guardar buffer anterior
                    if ($currentLabel === 'title' && ! empty($buffer)) {
                        $title = trim($buffer);
                    } elseif ($currentLabel === 'desc' && ! empty($buffer)) {
                        $desc = trim($buffer);
                    }
                    $currentLabel = (stripos($label, 'título') !== false || stripos($label, 'titulo') !== false || stripos($label, 'title') !== false) ? 'title' : 'desc';
                    $buffer = trim(mb_substr($line, mb_strlen($label)));
                    $matched = true;
                    break;
                }
            }
            if (! $matched) {
                $buffer .= "\n".$line;
            }
        }
        // Último buffer
        if ($currentLabel === 'title' && ! empty($buffer)) {
            $title = trim($buffer);
        } elseif ($currentLabel === 'desc' && ! empty($buffer)) {
            $desc = trim($buffer);
        }
        if (! empty($title) && ! empty($desc)) {
            return [$title, $desc];
        }

        // ── Estrategia 4: primera línea = título, resto = descripción ──
        $nonEmpty = array_values(array_filter(explode("\n", $content), fn ($l) => ! empty(trim($l))));
        if (count($nonEmpty) >= 2) {
            $first = trim($nonEmpty[0]);
            $rest = trim(implode("\n", array_slice($nonEmpty, 1)));
            // La primera línea no debería ser muy larga para ser título
            if (mb_strlen($first) <= 200 && ! empty($rest)) {
                return [$first, $rest];
            }
        }

        // ── Estrategia 5 (fallback absoluto): todo es el título ──
        $maxTitle = 120;
        $fallbackTitle = mb_strlen($content) > $maxTitle ? mb_substr($content, 0, $maxTitle).'…' : $content;

        return [$fallbackTitle, ''];
    }

    // ─── Internal compaction ─────────────────────────────────────

    /**
     * Compacta texto vía NvidiaService preservando la información
     * pedagógica esencial. Si falla, retorna el texto original.
     */
    private function compactWithNvidia(string $text): string
    {
        $result = $this->nvidia->ask(
            'Eres un asistente que compacta texto pedagógico. Preserva TODA la información esencial: datos curriculares, nombres de competencias, indicadores de logro, áreas de aprendizaje y contenidos. Elimina solo redundancias, relleno y repeticiones. No pierdas contenido sustantivo ni datos clave. Responde SOLO con el texto compactado, sin explicaciones ni metadatos.',
            $text,
            [
                'max_tokens' => min(1536, (int) ceil($this->estimateTokens($text) * 0.55)),
                'temperature' => 0.3,
            ]
        );

        if (! $result['success'] || empty(trim($result['content'] ?? ''))) {
            return $text;
        }

        $compacted = trim($result['content']);

        // Limpiar anotaciones de seguridad que Nvidia a veces prefija
        $compacted = $this->stripSafetyAnnotations($compacted);

        if (empty($compacted)) {
            return $text;
        }

        // Solo usar si realmente se redujo (evita respuestas espurias)
        if (mb_strlen($compacted) >= mb_strlen($text) * 0.95) {
            return $text;
        }

        return $compacted;
    }

    // ─── Core AI orchestration ───────────────────────────────────

    /**
     * Envía un prompt a OpenRouter, compactándolo automáticamente
     * con Nvidia si el user prompt supera el token budget.
     *
     * Prueba hasta 5 modelos de OpenRouter en cascada con 60s de timeout
     * cada uno. Si todos fallan, retorna error.
     *
     * Si se proporciona un $contentValidator, el contenido devuelto por
     * cada modelo se valida con ese callable. Si retorna false, se considera
     * que el modelo falló (contenido inválido) y se pasa al siguiente.
     *
     * @param  string        $systemPrompt      Instrucción del sistema.
     * @param  string        $userPrompt        Mensaje del usuario.
     * @param  array         $overrides         Overrides base para el LLM.
     * @param  int           $tokenBudget       Máx. tokens del user prompt antes de compactar.
     * @param  callable|null $contentValidator  Recibe (string $content): bool.
     *                                          true = válido, false = inválido (pasar al sig. modelo).
     * @param  array|null    $customChain       Cadena custom de modelos [['model','label'],...].
     *                                          null = usa la cadena por defecto.
     * @param  callable|null $notify            Recibe (string $type, string $title, string $desc): void.
     *                                          Tipos: 'info', 'warning', 'error'.
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string, debug_raw_content: ?string}
     */
    public function askWithCompaction(
        string $systemPrompt,
        string $userPrompt,
        array $overrides = [],
        int $tokenBudget = 2000,
        ?callable $contentValidator = null,
        ?array $customChain = null,
        ?callable $notify = null,
    ): array {
        $estimatedTokens = $this->estimateTokens($userPrompt);
        $compacted = false;
        $originalSize = mb_strlen($userPrompt);
        $failedContent = null;

        if ($estimatedTokens > $tokenBudget) {
            $compactResult = $this->compactWithNvidia($userPrompt);

            if ($compactResult !== $userPrompt && mb_strlen($compactResult) < $originalSize * 0.9) {
                $userPrompt = $compactResult;
                $compacted = true;

                if ($notify) {
                    $notify(
                        'info',
                        'Prompt compactado',
                        'El contexto se compactó vía NVIDIA para optimizar tokens ('
                        .number_format($originalSize).' → '.number_format(mb_strlen($compactResult)).' chars).'
                    );
                }
            }
        }

        // ─── Cadena de modelos OpenRouter (desde config/openrouter.php) ──
        $modelChain = $customChain ?? [
            ['model' => config('openrouter.model_primary'),   'label' => 'Qwen 3.1 32B primario'],
            ['model' => config('openrouter.model_fallback1'), 'label' => 'Mistral Large fallback 1'],
            ['model' => config('openrouter.model_fallback2'), 'label' => 'Ling 2.6 Flash fallback 2'],
            ['model' => config('openrouter.model_fallback3'), 'label' => 'Nemotron 3 Nano fallback 3'],
            ['model' => config('openrouter.model_fallback4'), 'label' => 'Claude Sonnet 4 fallback 4'],
        ];

        $llm = $this->openRouter;
        $lastError = null;

        foreach ($modelChain as $i => $attempt) {
            $attemptUserPrompt = $i > 0 ? $userPrompt.self::FALLBACK_REINFORCEMENT : $userPrompt;

            $attemptOverrides = array_merge($overrides, [
                'model' => $attempt['model'],
                'timeout' => max($overrides['timeout'] ?? 120, 120),
            ]);

            $result = $llm->ask($systemPrompt, $attemptUserPrompt, $attemptOverrides);

            if ($result['success']) {
                $content = $result['content'] ?? '';
                if ($contentValidator !== null && (empty($content) || ! $contentValidator($content))) {
                    $failedContent = $content;
                    $lastError = 'Contenido inválido: no superó la validación de estructura.';

                    $vHasInicio = preg_match('/^\/\/INICIO\s*$/m', $content) === 1;
                    $vHasDesarrollo = preg_match('/^\/\/DESARROLLO\s*$/m', $content) === 1;
                    $vHasCierre = preg_match('/^\/\/CIERRE\s*$/m', $content) === 1;
                    $vDevBlocks = 0;
                    if ($vHasInicio && $vHasDesarrollo && $vHasCierre) {
                        $vDevMatch = null;
                        preg_match('/^\/\/DESARROLLO\s*$(.*?)^\/\/CIERRE\s*$/ms', $content, $vDevMatch);
                        if (! empty($vDevMatch[1])) {
                            $vBlocks = preg_split('/\n\s*\n/', trim($vDevMatch[1]));
                            $vValidBlocks = array_filter($vBlocks, fn (string $b): bool => ! empty(trim($b)));
                            $vDevBlocks = count($vValidBlocks);
                        }
                    }

                    $this->logger->warning("askWithCompaction: {$attempt['label']} contenido inválido", [
                        'model' => $attempt['model'],
                        'length' => mb_strlen($content),
                        'validation' => [
                            'has_inicio' => $vHasInicio,
                            'has_desarrollo' => $vHasDesarrollo,
                            'has_cierre' => $vHasCierre,
                            'dev_blocks' => $vDevBlocks,
                        ],
                        'content_preview' => mb_substr(preg_replace('/\s+/', ' ', $content), 0, 500),
                    ]);

                    if ($notify) {
                        $notify(
                            'warning',
                            "{$attempt['label']} contenido inválido",
                            'El contenido generado no cumple la estructura requerida. Cambiando al siguiente modelo...'
                        );
                    }

                    continue;
                }

                $this->logger->info("askWithCompaction: {$attempt['label']} generó contenido válido", [
                    'model' => $attempt['model'],
                    'length' => mb_strlen($content),
                    'chain_index' => $i,
                ]);

                $result['debug_raw_content'] = $failedContent;

                return $result;
            }

            $lastError = $result['error'] ?? 'Error desconocido';
            $reason = $this->describeModelError($lastError);
            $this->logger->warning("askWithCompaction: {$attempt['label']} falló", [
                'model' => $attempt['model'],
                'error' => $lastError,
                'reason' => $reason,
            ]);

            if ($notify) {
                $notify(
                    'warning',
                    "{$attempt['label']} no respondió",
                    "Cambiando al siguiente modelo... ({$reason})"
                );
            }
        }

        // ─── Todos los modelos fallaron ──────────────────────────
        $this->logger->error('askWithCompaction: los 3 modelos OpenRouter fallaron', [
            'last_error' => $lastError,
        ]);

        if ($notify) {
            $notify(
                'error',
                'Generación interrumpida',
                'Los modelos de IA no pudieron completar la generación. Verifica tu conexión a Internet y el saldo en OpenRouter, luego intenta de nuevo pulsando el botón de generar.'
            );
        }

        return [
            'success' => false,
            'content' => null,
            'model' => null,
            'usage' => null,
            'error' => $lastError,
            'debug_raw_content' => $failedContent,
        ];
    }

    // ─── Context retrieval ───────────────────────────────────────

    /**
     * Obtiene el contexto de referentes normativos formateado,
     * filtrado por pensum para reducir tokens.
     */
    public function getReferentsContext(?int $pestudioId, $pensum = null): string
    {
        if (! $pestudioId) {
            return '—';
        }

        $referents = DiagReferent::with(['competencies' => function ($q) use ($pensum) {
            if ($pensum) {
                $q->where('pensum_id', $pensum->id);
            }
        }, 'competencies.indicators'])
            ->where('pestudio_id', $pestudioId)
            ->where('active', true)
            ->get();

        if ($referents->isEmpty()) {
            return 'No hay referentes registrados para este plan de estudio.';
        }

        $lines = [];
        foreach ($referents as $ref) {
            $lines[] = "Referente: {$ref->name} ({$ref->code})";
            foreach ($ref->competencies as $comp) {
                $indList = $comp->indicators->take(3);
                $text = mb_strlen($comp->name) > 80
                    ? mb_substr($comp->name, 0, 80).'…'
                    : $comp->name;
                $lines[] = "  {$text}";
                foreach ($indList as $ind) {
                    $t = mb_strlen($ind->description) > 60
                        ? mb_substr($ind->description, 0, 60).'…'
                        : $ind->description;
                    $lines[] = "    - {$t}";
                }
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Sanitiza texto delegando en LmsTextSanitizerService.
     */
    public function sanitizeText(?string $text, string $level = 'standard'): ?string
    {
        return $this->textSanitizer->sanitize($text, $level);
    }
}
