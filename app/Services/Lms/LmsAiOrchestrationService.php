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

    /**
     * Construye el prompt de reparación con el feedback específico del validador.
     */
    private function buildRepairPrompt(string $feedback): string
    {
        return "\n\n⚠️ CORRECCIÓN — Tu intento anterior fue rechazado por el validador:\n"
            .'MOTIVO: '.$feedback."\n"
            .'Reescribe la respuesta COMPLETA corrigiendo exactamente ese problema. '
            .'Conserva el tema y la estructura //INICIO, mínimo 5 bloques en //DESARROLLO y //CIERRE. '
            .'Sin meta-comentarios ni explicaciones.';
    }

    public function __construct(
        private readonly OpenRouterService $openRouter,
        private readonly NvidiaService $nvidia,
        private readonly LmsTextSanitizerService $textSanitizer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Cadena default de modelos OpenRouter con labels desde config.
     * (Antes las labels estaban hardcodeadas y no coincidían con el .env real.)
     */
    public function defaultModelChain(): array
    {
        return [
            ['model' => config('openrouter.model_primary'),   'label' => 'Primario'],
            ['model' => config('openrouter.model_fallback1'), 'label' => 'Fallback 1'],
            ['model' => config('openrouter.model_fallback2'), 'label' => 'Fallback 2'],
            ['model' => config('openrouter.model_fallback3'), 'label' => 'Fallback 3'],
            ['model' => config('openrouter.model_fallback4'), 'label' => 'Fallback 4'],
        ];
    }

    /**
     * Entrada de emergencia (fallback entre proveedores) para la cadena indicada.
     * OPT-IN explícito: null = sin emergencia (retrocompatible con todas las
     * llamadas existentes, incluidas diagramas/SVG/math que no deben usarla).
     * Devuelve null si la entrada no existe/está deshabilitada o si el proveedor
     * no tiene credenciales (NVIDIA_API_KEY) — se omite silenciosamente.
     *
     * @param  string|null  $chainKey  Clave de config('openrouter.chains') ('text').
     * @return array{provider: string, model: ?string, label: string}|null
     */
    public function emergencyEntry(?string $chainKey = null): ?array
    {
        if ($chainKey === null) {
            return null;
        }

        $entry = config("openrouter.chains.{$chainKey}.emergency");

        if (! is_array($entry) || ($entry['enabled'] ?? false) !== true) {
            return null;
        }

        $provider = $entry['provider'] ?? 'nvidia';

        // Sin credenciales del proveedor de emergencia → omitir
        if ($provider === 'nvidia' && empty(config('nvidia.api_key'))) {
            return null;
        }

        return [
            'provider' => $provider,
            'model' => $entry['model'] ?? null,
            'label' => $entry['label'] ?? 'Proveedor de emergencia',
        ];
    }

    /**
     * Ejecuta una llamada a un proveedor directo (fuera de OpenRouter).
     * Actualmente solo 'nvidia' (NvidiaService::ask, misma forma de respuesta).
     */
    private function askProvider(string $provider, string $systemPrompt, string $userPrompt, array $overrides): array
    {
        if ($provider === 'nvidia') {
            return $this->nvidia->ask($systemPrompt, $userPrompt, $overrides);
        }

        return [
            'success' => false,
            'content' => null,
            'model' => null,
            'usage' => null,
            'error' => "Proveedor de emergencia desconocido: {$provider}",
            'error_type' => 'provider_unknown',
        ];
    }

    /**
     * Último recurso cuando TODA la cadena OpenRouter falló (todos los modelos
     * agotados o la plataforma abortó por auth/credits): consulta al proveedor
     * de emergencia directo (Nvidia) con el mismo prompt compactado.
     *
     * Un solo intento, sin bucle de reparación: es el último recurso tras una
     * cadena ya agotada, y las operaciones de texto (únicas con emergencia)
     * apenas usan $contentValidator.
     *
     * @param  array{provider: string, model: ?string, label: string}  $emergency
     * @return array|null  Resultado normalizado si la emergencia generó contenido válido; null si falló.
     */
    private function tryEmergencyProvider(
        array $emergency,
        string $systemPrompt,
        string $userPrompt,
        array $overrides,
        ?callable $contentValidator,
        ?callable $notify,
        ?string $failedContent,
    ): ?array {
        // Sin reinforcement de fallback: las reglas de //INICIO//DESARROLLO//CIERRE
        // no aplican a las operaciones de texto que usan la emergencia.
        $emergencyOverrides = array_merge($overrides, [
            'timeout' => min((int) ($overrides['timeout'] ?? 120), 120),
        ]);
        unset($emergencyOverrides['model']);
        if (! empty($emergency['model'])) {
            $emergencyOverrides['model'] = $emergency['model'];
        }

        $startedAt = microtime(true);
        $result = $this->askProvider($emergency['provider'], $systemPrompt, $userPrompt, $emergencyOverrides);
        $elapsed = round(microtime(true) - $startedAt, 1);

        if (! $result['success']) {
            $this->logger->warning('askWithCompaction: proveedor de emergencia falló', [
                'provider' => $emergency['provider'],
                'label' => $emergency['label'],
                'error' => $result['error'] ?? 'Error desconocido',
                'elapsed_seconds' => $elapsed,
            ]);

            return null;
        }

        $content = trim($result['content'] ?? '');

        // Nvidia a veces prefija anotaciones de seguridad — limpiarlas.
        $content = $this->stripSafetyAnnotations($content);

        if ($content === '') {
            $this->logger->warning('askWithCompaction: proveedor de emergencia sin contenido', [
                'provider' => $emergency['provider'],
                'elapsed_seconds' => $elapsed,
            ]);

            return null;
        }

        // Misma validación de contenido que la cadena OpenRouter.
        if ($contentValidator !== null && ($validation = $contentValidator($content)) !== true) {
            $this->logger->warning('askWithCompaction: contenido del proveedor de emergencia inválido', [
                'provider' => $emergency['provider'],
                'length' => mb_strlen($content),
                'feedback' => is_string($validation) ? mb_substr($validation, 0, 300) : 'estructura inválida',
                'elapsed_seconds' => $elapsed,
            ]);

            return null;
        }

        $this->logger->info('askWithCompaction: contenido generado por proveedor de emergencia', [
            'provider' => $emergency['provider'],
            'model' => $result['model'] ?? null,
            'length' => mb_strlen($content),
            'elapsed_seconds' => $elapsed,
        ]);

        if ($notify) {
            $notify(
                'warning',
                "{$emergency['label']} entró en acción",
                'OpenRouter no respondió; el contenido se generó con el proveedor de emergencia.'
            );
        }

        $result['content'] = $content;
        $result['debug_raw_content'] = $failedContent;

        return $result;
    }

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

    // ─── Clasificación de errores (P2) ───────────────────────────
    //
    // No todos los errores merecen el mismo tratamiento. Este par de métodos
    // convierte la cadena de error del proveedor en una categoría y, para esa
    // categoría, en una estrategia accionable que askWithCompaction consumirá
    // para decidir si saltar de modelo, reintentar, retroceder o abortar la
    // cadena (evitando recorrer 5 modelos ante configuraciones inválidas).

    /**
     * Estrategias de recuperación ante fallos de un modelo/proveedor.
     */
    private const ERROR_STRATEGY_SKIP_CHAIN = 'SKIP_CHAIN';          // configuración inválida: no proseguir

    private const ERROR_STRATEGY_RETRY_ONCE = 'RETRY_ONCE';          // transitorio: repetir mismo modelo una vez

    private const ERROR_STRATEGY_BACKOFF_THEN_NEXT = 'BACKOFF_THEN_NEXT'; // 429/robo: esperar y pasar al siguiente

    private const ERROR_STRATEGY_NEXT = 'NEXT';                      // salta al siguiente modelo

    /**
     * Clasifica la cadena de error en una categoría normalizada.
     *
     * @return string Una de: rate_limit, credits, auth, not_found, timeout,
     *                server, empty, safety, connection, unknown.
     */
    public function classifyError(string $errorMsg): string
    {
        $msg = mb_strtolower($errorMsg);

        if (str_contains($msg, '429')
            || str_contains($msg, 'rate limit')
            || str_contains($msg, 'too many requests')) {
            return 'rate_limit';
        }

        if (str_contains($msg, '402') || str_contains($msg, 'insufficient credits')) {
            return 'credits';
        }

        if (str_contains($msg, '401') || str_contains($msg, 'unauthorized')) {
            return 'auth';
        }

        if (str_contains($msg, '403') || str_contains($msg, 'forbidden')) {
            return 'auth';
        }

        if (str_contains($msg, '404') || str_contains($msg, 'not found') || str_contains($msg, 'does not exist')) {
            return 'not_found';
        }

        if (str_contains($msg, '408')
            || str_contains($msg, 'timed out')
            || str_contains($msg, 'timeout')
            || str_contains($msg, 'gateway timeout')) {
            return 'timeout';
        }

        if (str_contains($msg, '500')
            || str_contains($msg, '501')
            || str_contains($msg, '502')
            || str_contains($msg, '503')
            || str_contains($msg, '504')
            || str_contains($msg, 'server error')) {
            return 'server';
        }

        if (str_contains($msg, 'empty reply')
            || str_contains($msg, 'connection refused')
            || str_contains($msg, 'connection reset')
            || str_contains($msg, 'connection error')
            || str_contains($msg, 'no content')
            || str_contains($msg, 'closed')) {
            return 'connection';
        }

        if (str_contains($msg, 'refusal')
            || str_contains($msg, 'content_filter')
            || str_contains($msg, 'safety')
            || str_contains($msg, 'rechaz')) {
            return 'safety';
        }

        if (str_contains($msg, 'sin contenido') || str_contains($msg, 'sin generar contenido')) {
            return 'empty';
        }

        return 'unknown';
    }

    /**
     * Devuelve la estrategia de recuperación para una categoría de error.
     *
     * @return string Una de las constantes ERROR_STRATEGY_*.
     */
    public function strategyForError(string $category): string
    {
        return match ($category) {
            // Configuración GLOBAL inválida: un 401/403 (API key mala) o 402
            // (saldo insuficiente) afecta a TODOS los modelos del proveedor;
            // no tiene sentido recorrer el resto de la cadena. Abortar.
            'auth', 'credits' => self::ERROR_STRATEGY_SKIP_CHAIN,

            // 404 = el modelo concreto no existe / dejó de estar disponible
            // (p.ej. "no longer available as free model"). Es un problema del
            // MODELO, no de la cuenta: saltar al siguiente modelo de la cadena.
            'not_found' => self::ERROR_STRATEGY_NEXT,

            // Transitorio pero potencialmente recuperable en el mismo modelo:
            // un único reintento corto antes de saltar.
            'timeout' => self::ERROR_STRATEGY_RETRY_ONCE,
            'server' => self::ERROR_STRATEGY_RETRY_ONCE,

            // Rozamos la cuota del proveedor: esperar (backoff jitter) y
            // pasar al siguiente modelo/proveedor.
            'rate_limit' => self::ERROR_STRATEGY_BACKOFF_THEN_NEXT,
            'connection' => self::ERROR_STRATEGY_BACKOFF_THEN_NEXT,

            // Respuesta vacía / rechazo de seguridad: saltar (un prompt
            // distinto o un modelo distinto lo resuelve mejor).
            'empty', 'safety', 'unknown' => self::ERROR_STRATEGY_NEXT,
        };
    }

    /**
     * Backoff exponencial con jitter (P3), acotado globalmente.
     *
     * @param  int  $attemptIndex  Índice base (0 = primer fallo).
     * @param  int  $maxSleepMs  Máximo total en ms (piso para no colgar la petición).
     * @param  int  $remainingBudgetMs  Presupuesto (ms) restante de la generación.
     *                                  0 = sin presupuesto (usa solo el tope por espera).
     * @return int Dormido realmente (ms) — 0 si no procedía.
     */
    public function backoffSleep(int $attemptIndex, int $maxSleepMs = 0, int $remainingBudgetMs = 0): int
    {
        if ($maxSleepMs === 0) {
            $maxSleepMs = (int) config('lms.backoff_max_ms', 4000);
        }

        $base = (2 ** min($attemptIndex, 5)) * 1000; // 1s, 2s, 4s, 8s, 16s → cap 32s
        $jitter = random_int(0, (int) ($base * 0.2));
        $sleepMs = min($base + $jitter, $maxSleepMs);

        // El presupuesto global de la generación es el tope final: mayor que
        // el presupuesto por espera queda descartado (evita que una cadena
        // acumule 1-2-4-8-16 y cuelgue la petición Livewire).
        if ($remainingBudgetMs > 0) {
            $sleepMs = min($sleepMs, $remainingBudgetMs);
        }

        if ($sleepMs <= 0) {
            return 0;
        }

        usleep($sleepMs * 1000);

        return $sleepMs;
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
     * Marcadores curriculares críticos que la compactación NO debe perder.
     *
     * Cada clave es el identificador de un campo; cada valor es un patrón PCRE
     * que lo detecta. La compactación solo se acepta si conserva todos los
     * marcadores presentes en el texto original (ver $lostCurricularFields).
     */
    private const CURRICULAR_MARKERS = [
        // Curso => grado + asignatura (en la cabecera "**Curso:**" del wizard).
        'grado' => '/curso\s*:/i',
        'asignatura' => '/curso\s*:/i',
        'tema_generador' => '/tema\s+generador/i',
        'indicadores' => '/indicadores(\s+de\s+logro)?/i',
        'referentes' => '/referentes\s+normativos|competencia/i',
    ];

    /**
     * Estructuras de fallback de compactación, en orden de prioridad.
     */
    private const COMPACT_TIMEOUT_ATTEMPTS = 2;

    /**
     * Punto de entrada de la compactación (P7).
     *
     * Orquesta la cadena de fallback:
     *   Nvidia (con 1 reintento transitorio)
     *     -> compactador local estructurado
     *     -> truncado controlado por secciones
     *     -> prompt original (si nada reduce, o nada conserva lo crítico)
     *
     * @return array{text: string, method: string, original_size: int,
     *               final_size: int, lost_fields: array, reduced_pct: float}
     */
    public function compactPrompt(string $text): array
    {
        $originalSize = mb_strlen($text);

        // 1) Nvidia con reintento ante fallo transitorio.
        $nvidia = $this->compactWithNvidia($text);
        if ($this->isUsableCompact($nvidia, $text)) {
            return $this->compactReport($text, $nvidia, 'nvidia');
        }

        // 2) Compactador local estructurado (determinista, sin red).
        $local = $this->compactLocallyStructured($text);
        if ($this->isUsableCompact($local, $text)) {
            return $this->compactReport($text, $local, 'local_structured');
        }

        // 3) Truncado controlado por secciones (preserva cabeceras).
        $truncated = $this->compactBySections($text);
        if ($this->isUsableCompact($truncated, $text)) {
            return $this->compactReport($text, $truncated, 'truncate_sections');
        }

        // 4) Prompt original, sin compactar.
        return $this->compactReport($text, $text, 'none');
    }

    /**
     * Compacta texto vía NvidiaService preservando la información pedagógica.
     *
     * Reintenta UNA vez ante fallo transitorio (timeout / 5xx) con un breve
     * backoff. Si falla o no reduce significativamente, retorna el texto
     * original. La validación curricular (campos conservados) la hace el
     * caller vía $isUsableCompact.
     */
    private function compactWithNvidia(string $text): string
    {
        $overrides = [
            'max_tokens' => min(1536, (int) ceil($this->estimateTokens($text) * 0.55)),
            'temperature' => 0.3,
        ];

        $result = null;
        for ($attempt = 1; $attempt <= self::COMPACT_TIMEOUT_ATTEMPTS; $attempt++) {
            $result = $this->nvidia->ask(
                'Eres un asistente que compacta texto pedagógico. Preserva TODA la información esencial: datos curriculares, nombres de competencias, indicadores de logro, áreas de aprendizaje, grado, asignatura y contenidos. Elimina solo redundancias, relleno y repeticiones. No pierdas contenido sustantivo ni datos clave. Conserva intactas las cabeceras de sección y las etiquetas "Curso:", "Tema generador:", "Indicadores", "Referentes normativos". Responde SOLO con el texto compactado, sin explicaciones ni metadatos.',
                $text,
                $overrides,
            );

            // Reintento único ante fallo transitorio del proveedor.
            if (! $this->isTransientCompactionFailure($result)) {
                break;
            }
            $this->backoffSleep($attempt, 0, 1500);
        }

        if (! $result['success'] || empty(trim($result['content'] ?? ''))) {
            return $text;
        }

        $compacted = trim($result['content']);

        // Limpiar anotaciones de seguridad que Nvidia a veces prefija.
        $compacted = $this->stripSafetyAnnotations($compacted);

        if (empty($compacted)) {
            return $text;
        }

        // Solo usar si realmente se redujo (evita respuestas espurias).
        if (mb_strlen($compacted) >= mb_strlen($text) * 0.95) {
            return $text;
        }

        return $compacted;
    }

    /**
     * Compactador local determinista: conserva la estructura marcada (cabeceras
     * markdown, viñetas, etiquetas "Label: ...") y trunca solo la prosa larga.
     * No depende de ninguna red ni proveedor.
     */
    private function compactLocallyStructured(string $text): string
    {
        $lines = preg_split('/\R/', $text);
        $kept = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            // Estructura: cabecera markdown / viñeta / etiqueta "Label:".
            $isStructural = preg_match('/^(#{1,6}\s|\*\*|[-•*]\s)/', $trimmed) === 1
                || preg_match('/^[A-ZÁÉÍÓÚÑ][A-Za-zÁÉÍÓÚÑáéíóúñ\s\/,\.\(\)\-–]{1,60}\s*:/u', $trimmed) === 1;

            if ($isStructural) {
                $kept[] = $trimmed;

                continue;
            }

            // Prosa libre: recortar valores largos a 200 chars.
            $kept[] = mb_strlen($trimmed) > 200
                ? mb_substr($trimmed, 0, 200).'…'
                : $trimmed;
        }

        $result = trim(implode("\n", $kept));

        if ($result === '' || $result === $text) {
            return $text;
        }

        return $result;
    }

    /**
     * Truncado controlado por secciones: conserva el primer tramo del prompt
     * (donde viven cabeceras y contexto) cortando en un límite de línea para
     * no partir etiquetas. Es el fallback último antes de devolver el original.
     */
    private function compactBySections(string $text): string
    {
        $targetChars = (int) (mb_strlen($text) * 0.55);
        if ($targetChars <= 0) {
            return $text;
        }

        $cut = mb_substr($text, 0, $targetChars);
        $newline = mb_strrpos($cut, "\n");
        if ($newline !== false) {
            $cut = mb_substr($cut, 0, $newline);
        }

        $truncated = rtrim($cut)."\n[contexto curricular truncado]";
        if (mb_strlen($truncated) >= mb_strlen($text) * 0.95) {
            return $text;
        }

        return $truncated;
    }

    /**
     * Devuelve los campos curriculares críticos que están presentes en el
     * original pero ausentes en la versión compactada (es decir, los perdidos).
     *
     * @return list<string>
     */
    public function lostCurricularFields(string $original, string $compacted): array
    {
        $lost = [];
        foreach (self::CURRICULAR_MARKERS as $field => $pattern) {
            if (preg_match($pattern, $original) === 1 && preg_match($pattern, $compacted) === 0) {
                $lost[] = $field;
            }
        }

        return $lost;
    }

    /**
     * ¿Resultado de compactación aceptable? Debe reducir de verdad y conservar
     * todos los campos curriculares críticos presentes en el original.
     */
    private function isUsableCompact(string $candidate, string $original): bool
    {
        if ($candidate === '' || $candidate === $original) {
            return false;
        }

        if (mb_strlen($candidate) >= mb_strlen($original) * 0.95) {
            return false;
        }

        return $this->lostCurricularFields($original, $candidate) === [];
    }

    private function compactReport(string $original, string $compacted, string $method): array
    {
        $originalSize = mb_strlen($original);
        $finalSize = mb_strlen($compacted);
        $lost = $this->lostCurricularFields($original, $compacted);

        $this->logger->info('Compactación de prompt', [
            'method' => $method,
            'original_chars' => $originalSize,
            'final_chars' => $finalSize,
            'reduction_pct' => $originalSize > 0
                ? round((1 - ($finalSize / $originalSize)) * 100, 1)
                : 0,
            'lost_curricular_fields' => $lost,
        ]);

        return [
            'text' => $compacted,
            'method' => $method,
            'original_size' => $originalSize,
            'final_size' => $finalSize,
            'lost_fields' => $lost,
            'reduced_pct' => $originalSize > 0
                ? round((1 - ($finalSize / $originalSize)) * 100, 1)
                : 0,
        ];
    }

    /**
     * Indica si un fallo de Nvidia es transitorio (timeout / 5xx) y por tanto
     * merece un reintento. Un 401/404/402 no debe reintentar (config inválida).
     */
    private function isTransientCompactionFailure(array $result): bool
    {
        if ($result['success'] ?? false) {
            return false;
        }

        $category = $this->classifyError($result['error'] ?? '');

        return in_array($category, ['timeout', 'server', 'rate_limit', 'connection'], true);
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
     * cada modelo se valida con ese callable. Si retorna true, es válido;
     * si retorna false o un string con feedback, se considera fallido y se
     * reintenta el MISMO modelo (hasta `lms.repair_attempts`) con el feedback
     * como corrección antes de pasar al siguiente modelo.
     *
     * @param  string  $systemPrompt  Instrucción del sistema.
     * @param  string  $userPrompt  Mensaje del usuario.
     * @param  array  $overrides  Overrides base para el LLM.
     * @param  int  $tokenBudget  Máx. tokens del user prompt antes de compactar.
     * @param  callable|null  $contentValidator  Recibe (string $content): true|string|bool.
     *                                           true = válido; string = inválido + feedback;
     *                                           false = inválido (feedback genérico).
     * @param  array|null  $customChain  Cadena custom de modelos [['model','label'],...].
     *                                   null = usa la cadena por defecto.
     * @param  callable|null  $notify  Recibe (string $type, string $title, string $desc): void.
     *                                 Tipos: 'info', 'warning', 'error'.
     * @param  string|null  $chainKey  Clave de config('openrouter.chains') para el
     *                                 proveedor de EMERGENCIA (fallback entre proveedores,
     *                                 propuesta #1). null + customChain = sin emergencia
     *                                 (cadenas de diagramas/SVG/math no la usan).
     *                                 Las operaciones de texto pasan 'text'.
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
        ?string $chainKey = null,
    ): array {
        $estimatedTokens = $this->estimateTokens($userPrompt);
        $compacted = false;
        $originalSize = mb_strlen($userPrompt);
        $failedContent = null;

        if ($estimatedTokens > $tokenBudget) {
            $compact = $this->compactPrompt($userPrompt);

            // Solo adoptar el texto compactado si realmente redujo ∓ conservó
            // los campos curriculares críticos (isUsableCompact ya lo valida).
            if ($compact['method'] !== 'none' && $compact['final_size'] < $originalSize * 0.9) {
                $userPrompt = $compact['text'];
                $compacted = true;

                if ($notify) {
                    $reason = match ($compact['method']) {
                        'nvidia' => 'vía NVIDIA',
                        'local_structured' => 'vía compactador local estructurado',
                        'truncate_sections' => 'vía truncado controlado por secciones',
                        default => '',
                    };
                    $notify(
                        'info',
                        'Prompt compactado',
                        'El contexto se compactó '.$reason.' para optimizar tokens ('
                        .number_format($compact['original_size']).' → '.number_format($compact['final_size']).' chars, '
                        .number_format($compact['reduced_pct'], 1).'% reducción).'
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
        $repairAttempts = max(0, (int) config('lms.repair_attempts', 1));
        $skipChain = false; // aborta la cadena ante configuración inválida (auth/404/credits)
        // Presupuesto global de backoff de esta generación (ms). Acumulado en
        // cada espera para que el total no cuelgue la petición Livewire.
        $backoffBudgetMs = max(0, (int) config('lms.backoff_generation_max_ms', 8000));

        foreach ($modelChain as $i => $attempt) {
            $baseUserPrompt = $i > 0 ? $userPrompt.self::FALLBACK_REINFORCEMENT : $userPrompt;

            $attemptOverrides = array_merge($overrides, [
                'model' => $attempt['model'],
                'timeout' => max($overrides['timeout'] ?? 120, 120),
            ]);

            $attemptUserPrompt = $baseUserPrompt;

            for ($repair = 0, $techRetried = false; $repair <= $repairAttempts; $repair++) {
                $startedAt = microtime(true);
                $result = $llm->ask($systemPrompt, $attemptUserPrompt, $attemptOverrides);
                $elapsed = round(microtime(true) - $startedAt, 1);

                if (! $result['success']) {
                    $lastError = $result['error'] ?? 'Error desconocido';
                    $reason = $this->describeModelError($lastError);
                    $category = $this->classifyError($lastError);
                    $strategy = $this->strategyForError($category);

                    $this->logger->warning("askWithCompaction: {$attempt['label']} falló", [
                        'model' => $attempt['model'],
                        'error' => $lastError,
                        'reason' => $reason,
                        'category' => $category,
                        'strategy' => $strategy,
                        'chain_index' => $i,
                        'elapsed_seconds' => $elapsed,
                    ]);

                    // ─── Estrategia por tipo de error (P2) ───────────
                    // SKIP_CHAIN (auth/credits): configuración GLOBAL inválida
                    // del proveedor (API key mala o saldo insuficiente).
                    // Afecta a todos los modelos — abortar y reportar.
                    if ($strategy === self::ERROR_STRATEGY_SKIP_CHAIN) {
                        if ($notify) {
                            $notify(
                                'error',
                                'Configuración de IA inválida',
                                "{$attempt['label']}: {$reason}. Verifica la API key y el saldo del proveedor. "
                                .'No se reintentará automáticamente.'
                            );
                        }

                        // Abortar la cadena entera: un 401/403/402 se repetirá
                        // en los demás modelos del mismo proveedor.
                        $skipChain = true;

                        break; // salir del bucle de reparación; el foreach se corta abajo
                    }

                    // RETRY_ONCE (timeout/5xx): transitorio; reintentar el
                    // MISMO modelo una única vez antes de abandonarlo. No
                    // consume el presupuesto de reparación de contenido
                    // (que es un concepto distinto).
                    if ($strategy === self::ERROR_STRATEGY_RETRY_ONCE && ! $techRetried) {
                        // Espera corta (backoff con jitter, acotado al
                        // presupuesto restante) antes de volver a llamar al
                        // mismo modelo.
                        $sleptMs = $this->backoffSleep($i + 1, 0, $backoffBudgetMs);
                        $backoffBudgetMs = max(0, $backoffBudgetMs - $sleptMs);

                        if ($notify) {
                            $notify(
                                'warning',
                                "{$attempt['label']} falló (transitorio)",
                                ($sleptMs > 0
                                    ? "{$reason}. Esperando ".round($sleptMs / 1000, 1).'s y reintentando...'
                                    : "{$reason}. Reintentando una vez...")
                            );
                        }

                        // Repetir el mismo modelo sin cambiar el prompt.
                        $techRetried = true;
                        $repair--; // compensar el ++ del for para no gastar repair

                        continue; // mismo intento
                    }

                    // BACKOFF_THEN_NEXT (429/connection): espera con jitter
                    // y pasa al siguiente modelo/proveedor.
                    if ($strategy === self::ERROR_STRATEGY_BACKOFF_THEN_NEXT) {
                        $sleptMs = $this->backoffSleep($i + 1, 0, $backoffBudgetMs);
                        $backoffBudgetMs = max(0, $backoffBudgetMs - $sleptMs);

                        if ($notify) {
                            $notify(
                                'warning',
                                "{$attempt['label']} limitado",
                                "{$reason}. Esperando ".round($sleptMs / 1000, 1).'s y pasando al siguiente modelo...'
                            );
                        }

                        break; // ir al siguiente modelo de la cadena
                    }

                    // NEXT (not_found/empty/safety/unknown): saltar al
                    // siguiente modelo. Un 404 suele ser un modelo concreto
                    // retirado/no disponible — NO es un problema de la cuenta,
                    // así que se sigue con la cadena.
                    if ($notify) {
                        $title = $category === 'not_found'
                            ? "{$attempt['label']} no disponible"
                            : "{$attempt['label']} no respondió";
                        $notify(
                            'warning',
                            $title,
                            "Cambiando al siguiente modelo... ({$reason})"
                        );
                    }

                    break; // ir al siguiente modelo de la cadena
                }

                $content = $result['content'] ?? '';

                if ($contentValidator !== null && (empty($content) || ($validation = $contentValidator($content)) !== true)) {
                    $failedContent = $content;
                    $feedback = is_string($validation ?? false)
                        ? $validation
                        : 'El contenido no cumple la estructura requerida (//INICIO, mínimo 5 bloques en //DESARROLLO, //CIERRE).';
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
                        'feedback' => $feedback,
                        'chain_index' => $i,
                        'repair_attempt' => $repair,
                        'elapsed_seconds' => $elapsed,
                        'content_preview' => mb_substr(preg_replace('/\s+/', ' ', $content), 0, 500),
                    ]);

                    if ($notify) {
                        $notify(
                            'warning',
                            "{$attempt['label']} contenido inválido",
                            'El contenido generado no cumple la estructura requerida. Cambiando al siguiente modelo...'
                        );
                    }

                    if ($repair < $repairAttempts) {
                        // Reintentar el MISMO modelo con feedback específico
                        $attemptUserPrompt = $baseUserPrompt."\n\n".$this->buildRepairPrompt($feedback);

                        $this->logger->info('askWithCompaction: reparación con feedback', [
                            'model' => $attempt['model'],
                            'label' => $attempt['label'],
                            'repair_attempt' => $repair + 1,
                            'max_repairs' => $repairAttempts,
                            'feedback' => mb_substr($feedback, 0, 300),
                        ]);

                        if ($notify) {
                            $notify(
                                'info',
                                "{$attempt['label']} reparando contenido",
                                'El contenido fue rechazado: '.mb_substr($feedback, 0, 120).' Reintentando el mismo modelo...'
                            );
                        }

                        continue;
                    }

                    break; // reparaciones agotadas → siguiente modelo
                }

                $this->logger->info("askWithCompaction: {$attempt['label']} generó contenido válido", [
                    'model' => $attempt['model'],
                    'length' => mb_strlen($content),
                    'chain_index' => $i,
                    'repair_attempt' => $repair,
                    'elapsed_seconds' => $elapsed,
                ]);

                $result['debug_raw_content'] = $failedContent;

                return $result;
            }

            // SKIP_CHAIN: abortar la cadena completa (configuración inválida).
            if ($skipChain) {
                break;
            }
        }

        // ─── Todos los modelos fallaron ──────────────────────────
        // Propuesta #1 (fallback entre proveedores): último recurso con el
        // proveedor de EMERGENCIA (Nvidia API directa) ANTES de declarar el
        // fallo total. Solo para cadenas de texto: si $chainKey no define una
        // entrada (diagramas/SVG/math), emergencyEntry() devuelve null y el
        // comportamiento es idéntico al anterior.
        // NOTA: se dispara también con $skipChain (401/402 de OpenRouter):
        // el fallo es de la PLATAFORMA completa — exactamente el caso donde
        // un proveedor distinto más aporta (la cadena se abortó a mitad).
        $emergency = $this->emergencyEntry($chainKey);

        if ($emergency !== null) {
            $emergencyResult = $this->tryEmergencyProvider(
                $emergency,
                $systemPrompt,
                $userPrompt,
                $overrides,
                $contentValidator,
                $notify,
                $failedContent,
            );

            if ($emergencyResult !== null) {
                return $emergencyResult;
            }
        }

        $this->logger->error('askWithCompaction: todos los modelos de la cadena OpenRouter fallaron', [
            'attempts' => count($modelChain),
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
