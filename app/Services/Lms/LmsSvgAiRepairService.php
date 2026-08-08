<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Lms\LmsActivityContent;
use Illuminate\Support\Collection;

/**
 * Reparación de diagramas SVG almacenados en BD usando IA con cadena de
 * modelos (fallback) y reparación determinista como red de seguridad.
 *
 * Cobertura de daños:
 *   - Tag de apertura truncado sin su '>' que se come un cierre ajeno
 *     (p.ej. '<rect x="560" ... rx="8"</svg>') → rectángulo NEGRO al renderizar.
 *   - <svg> sin cierre, atributos cortados, tags sin cerrar.
 *   - Elementos declarados en comentarios pero nunca dibujados (cajas vacías).
 *   - Canvas desproporcionado (viewBox alto con el contenido solo arriba).
 *
 * Flujo (por contenido):
 *   1. IA (cadena de modelos con fallback vía LmsAiOrchestrationService):
 *      el modelo recibe el SVG roto + contexto pedagógico (lección/sección) y
 *      devuelve el SVG completo. Cada salida se valida con
 *      LmsSvgRepairService::isWellFormed(); si es inválida, se pasa al
 *      siguiente modelo de la cadena.
 *   2. Normalización: recorte del viewBox al contenido real (cropToContent).
 *   3. Fallback determinista: si toda la cadena falla, se aplica la reparación
 *      mecánica (LmsSvgRepairService::repair) — elimina el tag roto y
 *      reinserta '</svg>' — para que NUNCA quede un rectángulo negro.
 *   4. Persistencia opcional (options['persist']): reemplaza el bloque <svg>
 *      en el body del contenido y guarda. Por defecto NO persiste (dry-run).
 *
 * Usable desde cualquier lugar: comando artisan (lms:repair-svgs), tinker,
 * Livewire, jobs o tests. Sin dependencias de UI (las notificaciones se
 * inyectan por callback options['notify']).
 */
class LmsSvgAiRepairService
{
    public function __construct(
        private readonly LmsAiOrchestrationService $orchestrator,
        private readonly LmsSvgRepairService $repairService,
        private readonly ?\Psr\Log\LoggerInterface $logger = null,
    ) {}

    // ─── System prompt (nivel Staff Engineer) ────────────────────────────

    /**
     * System prompt de reparación de SVG — nivel Staff Engineer.
     * Público para poder inspeccionarlo/ajustarlo desde cualquier consumidor.
     */
    public static function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres un **Staff Engineer** senior especializado en SVG, frontend y visualización de datos, con dominio de XML bien formado, accesibilidad (WCAG 2.1) y sistemas de diseño. Tu tarea única: **REPARAR** un diagrama SVG educativo dañado o incompleto que quedó almacenado en una base de datos.

## Contexto del daño
Estos SVG fueron generados por un LLM y se guardaron tal cual. Los defectos típicos son:

1. **Truncamiento a mitad de tag**: un tag de apertura sin su `>` que se "come" un cierre ajeno, p.ej. `<rect x="560" y="210" width="380" height="170" rx="8"</svg>`. El navegador lo interpreta como un `<rect>` SIN `fill` → se pinta un **rectángulo negro sólido** (el fill por defecto del SVG) que tapa medio diagrama.
2. **Elementos declarados pero nunca dibujados**: comentarios como `<!-- Caja A -->` que anuncian una caja que no existe en el código.
3. **Canvas desproporcionado**: `viewBox` alto (p.ej. `1000 950`) con el contenido concentrado en la parte superior → enorme espacio vacío abajo al imprimir.
4. **Cierres ausentes**: `<svg>` sin `</svg>`, tags de texto o de forma sin cerrar, atributos cortados a la mitad.

## Reglas de reparación (obligatorias)

1. **Preserva el diseño existente**: colores de la paleta del documento (p.ej. `#f8f9fa`, `#e3f2fd`, `#fce4ec`, `#fff3e0`, `#e8f5e9`), tipografía (`Arial`, tamaños 12–26px), estilo de cajas (`rx="8"`, `stroke="#bbbbbb"`, `stroke-width="1.5"`), flechas (`marker-end="url(#arrow)"`) y el layout general. NO rediseñes ni cambies la estructura de nodos.
2. **Completa solo lo inferible**: si falta una caja espejo de otra existente (p.ej. la contraparte de "PODER EXPLÍCITO"), reconstruye el elemento completo con texto coherente con el título/etiqueta y el contexto pedagógico proporcionado. NO inventes contenido educativo nuevo ni cambies el significado de los textos existentes.
3. **Nunca fill por defecto**: todo elemento de forma (`rect`, `circle`, `ellipse`, `path`, `polygon`, `polyline`, `line`) debe tener `fill` explícito o estar dentro de `<defs>`. Un elemento sin `fill` se pinta negro.
4. **XML estrictamente bien formado**: cada tag abierto se cierra, los elementos vacíos usan `/>`, atributos con comillas dobles, sin caracteres inválidos ni entidades rotas. El documento termina exactamente con `</svg>`.
5. **viewBox ajustado al contenido**: recorta el espacio vacío inferior dejando 10–40px de margen bajo el último elemento (mantén el ancho original del canvas).
6. **Contenido autocontenido**: respuesta SOLO con el elemento `<svg>`. Sin `<figure>`, `<div>`, texto explicativo, ni bloques de código Markdown (```).
7. **Accesibilidad**: conserva `<title>`, `<desc>`, `aria-label` y los comentarios útiles de estructura si ya existen; añádelos si faltan sin cambiar el contenido.
8. **Multilínea**: usa `<tspan>` con `dy` para textos de más de ~18 caracteres; nunca dejes que un texto se salga de su caja o del viewBox.
9. **Contraste de texto**: texto sobre fondo claro siempre en `#333333` o `#1a1a1a` (títulos y body) y mínimo `#444444` para subtítulos/etiquetas (≤12px). PROHIBIDOS los grises tenues (`#555555`, `#666666`, `#777777`, `#888888`, `#999999`, `#bbbbbb`, `#cccccc`) en texto, flechas o bordes; usa `stroke` mínimo `#666666` en conectores.

## Contrato de salida (estricto)

- La respuesta comienza **exactamente** con `<svg` y termina **exactamente** con `</svg>`.
- Nada de texto antes ni después del SVG. Nada de wrappers Markdown.
- El resultado debe renderizarse idéntico al diagrama original salvo por: elementos completados, tags cerrados, y canvas recortado.
PROMPT;
    }

    // ─── API pública ─────────────────────────────────────────────────────

    /**
     * Repara el SVG de un contenido LMS (type IMAGE/HTML con <svg> embebido).
     *
     * @param  int|LmsActivityContent  $content  ID o modelo del contenido.
     * @param  array  $options  [
     *                          'persist' => bool (por defecto false = dry-run),
     *                          'models'  => ?array cadena custom [['model','label'],...],
     *                          'notify'  => ?callable fn(string $type, string $title, string $desc): void,
     *                          'token_budget' => int (por defecto 8000),
     *                          ]
     */
    public function repairContent(int|LmsActivityContent $content, array $options = []): SvgRepairResult
    {
        $content = $content instanceof LmsActivityContent
            ? $content
            : LmsActivityContent::find($content);

        if (! $content) {
            return new SvgRepairResult(
                strategy: 'error',
                error: "Contenido no encontrado (id={$content}).",
            );
        }

        $body = $content->body ?? '';
        if (! str_contains($body, '<svg')) {
            return new SvgRepairResult(
                strategy: 'no-svg',
                contentId: $content->id,
                error: 'El contenido no embebe un <svg>.',
            );
        }

        // Extraer el bloque <svg>…</svg> (puede venir envuelto en figure/div).
        $svgBlock = $this->extractSvg($body);
        if ($svgBlock === null) {
            return new SvgRepairResult(
                strategy: 'no-svg',
                contentId: $content->id,
                error: 'No se pudo extraer el bloque <svg> del body.',
            );
        }

        $context = $this->buildContext($content);
        $result = $this->repairSvg($svgBlock, $context, $options);
        $result = new SvgRepairResult(
            strategy: $result->strategy,
            svg: $result->svg,
            body: $result->body,
            model: $result->model,
            error: $result->error,
            changes: $result->changes,
            contentId: $content->id,
        );

        // Persistir (opcional): reemplazar el bloque SVG en el body completo.
        if ($result->svg !== null && $result->svg !== $svgBlock && ($options['persist'] ?? false)) {
            $newBody = str_replace($svgBlock, $result->svg, $body);
            if ($newBody !== $body) {
                $content->update(['body' => $newBody]);
                $result = new SvgRepairResult(
                    strategy: $result->strategy,
                    svg: $result->svg,
                    body: $newBody,
                    model: $result->model,
                    error: $result->error,
                    changes: [...$result->changes, 'persisted' => true],
                    contentId: $content->id,
                );
            }
        }

        return $result;
    }

    /**
     * Repara un bloque SVG aislado (sin tocar BD).
     *
     * @param  string  $svg  Bloque <svg>…</svg> dañado.
     * @param  string|null  $context  Contexto pedagógico opcional (lección/sección) para completar elementos.
     * @param  array  $options  Igual que repairContent().
     */
    public function repairSvg(string $svg, ?string $context = null, array $options = []): SvgRepairResult
    {
        $damage = $this->damageReport($svg);
        if (empty($damage['issues'])) {
            return new SvgRepairResult(strategy: 'unchanged', svg: $svg, changes: ['sin daños detectados']);
        }

        // 1) Intento IA con cadena de modelos (fallback automático).
        $ai = $this->attemptAiRepair($svg, $context, $options);

        if ($ai['success']) {
            return new SvgRepairResult(
                strategy: 'ai',
                svg: $ai['svg'],
                model: $ai['model'],
                changes: $damage['issues'],
            );
        }

        // 2) Fallback determinista: eliminar el tag roto y reinsertar </svg>.
        //    Nunca deja un rectángulo negro; solo pierde el elemento incompleto.
        $deterministic = $this->repairService->repair($svg);
        if ($deterministic !== $svg) {
            return new SvgRepairResult(
                strategy: 'deterministic',
                svg: $deterministic,
                changes: [...$damage['issues'], 'fallback_determinista'],
            );
        }

        // 3) Sin opciones: reportar el error de la IA.
        return new SvgRepairResult(
            strategy: 'error',
            error: $ai['error'] ?? 'La cadena de modelos no pudo reparar el SVG.',
            changes: $damage['issues'],
        );
    }

    /**
     * Escanea los contenidos LMS (IMAGE/HTML) y devuelve los que tienen SVG dañado.
     *
     * @return Collection<int, LmsActivityContent>
     */
    public function scanDamaged(?int $limit = null): Collection
    {
        $query = LmsActivityContent::where(function ($q) {
            $q->where('type', 'IMAGE')->orWhere('type', 'HTML');
        })->where('body', 'like', '%<svg%');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->filter(function (LmsActivityContent $content) {
            $svg = $this->extractSvg($content->body ?? '');

            return $svg !== null && ! empty($this->damageReport($svg)['issues']);
        })->values();
    }

    /**
     * Repara en lote todos los contenidos dañados (o los ids indicados).
     *
     * @param  array|null  $ids  Solo estos ids de contenido (null = todos los dañados).
     * @param  int|null  $limit  Máx. contenidos a procesar.
     * @param  array  $options  Igual que repairContent() (+ 'persist').
     * @return Collection<int, SvgRepairResult>
     */
    public function repairAll(?array $ids = null, ?int $limit = null, array $options = []): Collection
    {
        $contents = $ids !== null
            ? LmsActivityContent::whereIn('id', $ids)->get()
            : $this->scanDamaged($limit);

        return $contents->map(
            fn (LmsActivityContent $content) => $this->repairContent($content, $options)
        );
    }

    /**
     * Diagnóstico de daños de un bloque SVG (mismo criterio que la auditoría).
     *
     * @return array{issues: list<string>, empty_bottom_pct: ?int}
     */
    public function damageReport(string $svg): array
    {
        $issues = [];
        $emptyBottom = null;

        $opens = preg_match_all('/<svg\b/', $svg);
        $closes = preg_match_all('#</svg>#', $svg);
        if ($opens > $closes) {
            $issues[] = 'svg_sin_cierre';
        }

        if (preg_match('/<[a-zA-Z][\w:-]*\b(?:(?!\/>)[^<>])*?<\/[^>]*>/s', $svg)) {
            $issues[] = 'tag_roto';
        }

        if (preg_match('/\w+="[^"]*$/', $svg)) {
            $issues[] = 'atributo_cortado';
        }

        // Espacio vacío inferior del canvas (>= 20% se considera desproporcionado).
        if (preg_match('/viewBox="([^"]+)"/', $svg, $vm)) {
            [$vx, $vy, $vw, $vh] = array_map('floatval', preg_split('/[\s,]+/', $vm[1]));
            if ($vw > 0 && $vh > 0) {
                $maxBottom = $this->contentBottom($svg, $vw, $vh);
                if ($maxBottom !== null) {
                    $emptyBottom = (int) round((1 - $maxBottom / $vh) * 100);
                    if ($emptyBottom >= 20) {
                        $issues[] = "canvas_desproporcionado_{$emptyBottom}pct";
                    }
                }
            }
        }

        return ['issues' => $issues, 'empty_bottom_pct' => $emptyBottom];
    }

    // ─── Internos ────────────────────────────────────────────────────────

    /**
     * Intento de reparación por IA: cadena de modelos con fallback y validador.
     *
     * @return array{success: bool, svg: ?string, model: ?string, error: ?string}
     */
    private function attemptAiRepair(string $svg, ?string $context, array $options): array
    {
        $userPrompt = $this->buildUserPrompt($svg, $context);

        try {
            $result = $this->orchestrator->askWithCompaction(
                systemPrompt: self::getSystemPrompt(),
                userPrompt: $userPrompt,
                overrides: [
                    'max_tokens' => 8192,
                    'temperature' => 0.2,
                    'timeout' => 180,
                ],
                tokenBudget: $options['token_budget'] ?? 8000,
                contentValidator: function (string $content): bool {
                    $candidate = $this->extractSvg($content);

                    return $candidate !== null
                        && $this->repairService->isWellFormed($candidate);
                },
                customChain: $options['models'] ?? $this->defaultModelChain(),
                notify: $options['notify'] ?? null,
            );
        } catch (\Throwable $e) {
            ($this->logger ?? new \Psr\Log\NullLogger)
                ->warning('[LmsSvgAiRepairService] excepción en cadena de modelos', [
                    'exception' => $e->getMessage(),
                ]);

            return ['success' => false, 'svg' => null, 'model' => null, 'error' => $e->getMessage()];
        }

        if (! $result['success'] || empty($result['content'])) {
            return [
                'success' => false,
                'svg' => null,
                'model' => $result['model'] ?? null,
                'error' => $result['error'] ?? 'Sin respuesta de la cadena de modelos.',
            ];
        }

        $repaired = $this->extractSvg((string) $result['content']);
        if ($repaired === null) {
            return [
                'success' => false,
                'svg' => null,
                'model' => $result['model'] ?? null,
                'error' => 'La respuesta no contenía un bloque <svg>.',
            ];
        }

        // Normalización final: recorte del canvas al contenido real.
        $repaired = $this->repairService->cropToContent($repaired);

        // Contraste: oscurece grises claros de textos/flechas para que el
        // contenido persista ya legible (mejora 1) sin re-procesar en render.
        $repaired = $this->repairService->normalizeContrast($repaired);

        return ['success' => true, 'svg' => $repaired, 'model' => $result['model'] ?? null, 'error' => null];
    }

    /**
     * Cadena de modelos por defecto para reparación (misma familia que la
     * generación de ilustraciones: modelo fuerte + 2 fallbacks).
     *
     * Robusto sin container Laravel (tests unitarios): si config() no está
     * disponible o las claves están vacías, usa una cadena estática.
     *
     * @return list<array{model: string, label: string}>
     */
    private function defaultModelChain(): array
    {
        $container = app();
        $config = $container && $container->bound('config') ? $container->make('config') : null;

        $chain = [];
        if ($config) {
            foreach ([
                'openrouter.model_illustration_primary' => 'Reparación SVG (primario)',
                'openrouter.model_illustration_fallback1' => 'Reparación SVG fallback 1',
                'openrouter.model_illustration_fallback2' => 'Reparación SVG fallback 2',
            ] as $key => $label) {
                $model = $config->get($key);
                if ($model) {
                    $chain[] = ['model' => $model, 'label' => $label];
                }
            }
        }

        if (empty($chain)) {
            $chain = [
                ['model' => 'anthropic/claude-sonnet-4', 'label' => 'Reparación SVG (primario)'],
                ['model' => 'nvidia/nemotron-3-nano-30b-a3b', 'label' => 'Reparación SVG fallback 1'],
                ['model' => 'mistralai/mistral-large', 'label' => 'Reparación SVG fallback 2'],
            ];
        }

        return $chain;
    }

    /**
     * Extrae el bloque <svg>…</svg> (hasta el ÚLTIMO cierre, tolerante a
     * respuestas con texto alrededor o wrappers Markdown).
     */
    private function extractSvg(string $content): ?string
    {
        $content = preg_replace('/^```(?:svg|html)?\s*\n?/i', '', $content);
        $content = preg_replace('/\n?```\s*$/s', '', $content);

        if (preg_match('/<svg\b[^>]*>.*<\/svg>/is', $content, $m)) {
            return trim($m[0]);
        }

        return null;
    }

    /**
     * Contexto pedagógico del contenido para que la IA complete elementos
     * faltantes con coherencia (título de sección + texto + lección).
     */
    private function buildContext(LmsActivityContent $content): string
    {
        $section = $content->section;
        $activity = $section?->activity;
        $sectionText = $section?->visibleContents
            ->where('id', '!=', $content->id)
            ->map(fn ($c) => strip_tags($c->body ?? ''))
            ->implode("\n");

        $parts = [];
        if ($activity?->topic) {
            $parts[] = "Lección: {$activity->topic}";
        }
        if ($section?->title) {
            $parts[] = "Sección: {$section->title}";
        }
        if ($content->title) {
            $parts[] = "Diagrama: {$content->title}";
        }
        if ($sectionText && trim($sectionText) !== '') {
            $parts[] = "Texto de la sección (referencia para completar contenido):\n"
                .\Illuminate\Support\Str::limit(trim($sectionText), 1200);
        }

        return implode("\n", $parts);
    }

    /**
     * Prompt de usuario: contexto pedagógico + SVG dañado.
     */
    private function buildUserPrompt(string $svg, ?string $context): string
    {
        $prompt = "Repara el siguiente SVG. Aplica las reglas del Staff Engineer del system prompt.\n\n";
        if ($context && trim($context) !== '') {
            $prompt .= "## Contexto pedagógico\n{$context}\n\n";
        }
        $prompt .= "## SVG dañado\n{$svg}\n";

        return $prompt;
    }

    /**
     * Borde inferior real del contenido (rects sin fondo + textos).
     * Devuelve null si no hay geometría detectable.
     */
    private function contentBottom(string $svg, float $vw, float $vh): ?float
    {
        $maxBottom = 0.0;
        $hasContent = false;

        if (preg_match_all('/<rect\b[^>]*>/', $svg, $rects)) {
            foreach ($rects[0] as $rect) {
                $w = $this->attr($rect, 'width') ?? 0;
                $h = $this->attr($rect, 'height') ?? 0;
                $y = $this->attr($rect, 'y') ?? 0;

                if ($w * $h >= 0.9 * $vw * $vh) {
                    continue; // rect de fondo del canvas
                }
                if ($w > 0 && $h > 0) {
                    $maxBottom = max($maxBottom, $y + $h);
                    $hasContent = true;
                }
            }
        }

        if (preg_match_all('/<text\b[^>]*>/', $svg, $texts)) {
            foreach ($texts[0] as $t) {
                $y = $this->attr($t, 'y') ?? 0;
                if ($y > 0) {
                    $fontSize = $this->attr($t, 'font-size') ?? 14;
                    $maxBottom = max($maxBottom, $y + $fontSize);
                    $hasContent = true;
                }
            }
        }

        return $hasContent ? $maxBottom : null;
    }

    private function attr(string $tag, string $name): ?float
    {
        return preg_match('/'.$name.'="([\d.]+)"/', $tag, $m) ? (float) $m[1] : null;
    }
}
