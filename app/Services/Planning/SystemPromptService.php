<?php

namespace App\Services\Planning;

use App\Services\OpenRouterService;
use Illuminate\Support\Facades\Log;

/**
 * Compone y refina el system prompt de consolidado de ejes temáticos.
 *
 * - `compose()` arma el prompt que se copia: prompt base + parámetros de la
 *   ejecución + (opcional) bloque de refinamiento generado por IA.
 * - `refine()` pide a OpenRouter un bloque de refinamiento compacto para los
 *   parámetros actuales. No reescribe el prompt ni el template HTML: agrega
 *   énfasis, vínculos curriculares, guardrails y criterios de aceptación.
 *
 * El archivo base vive en blueprint/activities/alimentacion/systemPrompt.md y
 * no se incrusta en Blade porque contiene `{{REPORT_TITLE}}` y `@media`.
 */
class SystemPromptService
{
    public const PARAMS_HEADING = '# PARÁMETROS DE ESTA EJECUCIÓN';

    public const REFINEMENT_HEADING = '# REFINAMIENTO PARA ESTA EJECUCIÓN';

    public const INSTRUCTIONS_HEADING = '# INSTRUCCIONES ADICIONALES';

    private const MIN_REFINEMENT_CHARS = 200;

    private const MAX_REFINEMENT_CHARS = 12000;

    public function __construct(
        private readonly OpenRouterService $openRouter,
    ) {}

    /**
     * Prompt base tal como está en disco.
     *
     * @throws \RuntimeException
     */
    public function basePrompt(): string
    {
        $path = base_path('blueprint/activities/alimentacion/systemPrompt.md');

        if (! is_file($path)) {
            throw new \RuntimeException('El system prompt no está disponible.');
        }

        $contents = (string) file_get_contents($path);

        if (trim($contents) === '') {
            throw new \RuntimeException('El system prompt está vacío.');
        }

        return $contents;
    }

    /**
     * Compone el prompt final que se copia al portapapeles.
     *
     * @param  array{eje:string,areas?:?string,nivel?:?string,periodo?:?string,titulo?:?string,instrucciones?:?string,refinamiento?:?string}  $params
     */
    public function compose(array $params): string
    {
        $lines = [self::PARAMS_HEADING, ''];
        $lines[] = '- Eje transversal del plan (`ejeTematico`): '.$this->clean($params['eje'] ?? '');

        $optional = [
            'areas' => ['Ejes/áreas articuladas (`areasArticuladas`)', $params['areas'] ?? null],
            'nivel' => ['Nivel educativo (`nivelEducativo`)', $params['nivel'] ?? null],
            'periodo' => ['Periodo (`periodo`)', $params['periodo'] ?? null],
            'titulo' => ['Título del reporte (`reportTitle`)', $params['titulo'] ?? null],
        ];

        foreach ($optional as [$label, $value]) {
            $value = $this->clean($value);

            if ($value !== '') {
                $lines[] = "- {$label}: {$value}";
            }
        }

        $composed = rtrim($this->basePrompt())."\n\n".implode("\n", $lines);

        $refinement = $this->cleanBlock($params['refinamiento'] ?? '');

        if ($refinement !== '') {
            $composed .= "\n\n".$this->normalizeRefinement($refinement);
        }

        $instructions = $this->cleanBlock($params['instrucciones'] ?? '');

        if ($instructions !== '') {
            $composed .= "\n\n".self::INSTRUCTIONS_HEADING."\n\n".$instructions;
        }

        return $composed."\n";
    }

    /**
     * Genera un bloque de refinamiento para los parámetros dados usando la
     * cadena de modelos de OpenRouter configurada.
     *
     * @param  array{eje:string,areas?:?string,nivel?:?string,periodo?:?string,titulo?:?string,instrucciones?:?string}  $params
     * @return array{success:bool,prompt:?string,model:?string,error:?string}
     */
    public function refine(array $params): array
    {
        if (empty(config('openrouter.api_key'))) {
            return $this->failure('El servicio de IA no está configurado (falta OPENROUTER_API_KEY).');
        }

        $base = $this->basePrompt();
        $systemPrompt = $this->refinerSystemPrompt();
        $userMessage = $this->refinerUserMessage($base, $params);

        $errors = [];

        foreach ($this->models() as $model) {
            $result = $this->openRouter->ask($systemPrompt, $userMessage, [
                'model' => $model,
                'temperature' => 0.4,
                'max_tokens' => 4096,
                'timeout' => 120,
            ]);

            if (! ($result['success'] ?? false)) {
                $errors[] = "{$model}: ".($result['error'] ?? 'respuesta vacía');
                Log::info('SystemPromptService: modelo descartado', [
                    'model' => $model,
                    'error' => $result['error'] ?? null,
                ]);

                continue;
            }

            if (($result['finish_reason'] ?? null) === 'length') {
                $errors[] = "{$model}: respuesta truncada por límite de tokens";

                continue;
            }

            $text = $this->extractRefinement((string) ($result['content'] ?? ''));

            if ($text === null) {
                $errors[] = "{$model}: respuesta sin un bloque de refinamiento válido";

                continue;
            }

            return [
                'success' => true,
                'prompt' => $text,
                'model' => $result['model'] ?? $model,
                'error' => null,
            ];
        }

        Log::warning('SystemPromptService: todos los modelos fallaron', ['errors' => $errors]);

        $detail = $errors === [] ? '' : ' Detalle: '.implode(' | ', array_slice($errors, 0, 4));

        return $this->failure('No se pudo mejorar el prompt con IA.'.$detail);
    }

    /**
     * Cadena de modelos de OpenRouter (texto) usada para refinar.
     *
     * @return array<int, string>
     */
    private function models(): array
    {
        $models = [
            config('openrouter.model_text_primary'),
            config('openrouter.model_text_fallback1'),
            config('openrouter.model_text_fallback2'),
            config('openrouter.model_primary'),
            config('openrouter.model_fallback1'),
        ];

        $models = array_values(array_unique(array_filter(
            array_map('trim', array_map('strval', $models)),
            fn (string $model) => $model !== '',
        )));

        return $models;
    }

    private function refinerSystemPrompt(): string
    {
        return <<<'TEXT'
Eres un ingeniero de prompts sénior especializado en educación y visualización de datos.

Recibirás:
1. un SYSTEM PROMPT existente para un agente que genera un consolidado HTML (Tailwind + ApexCharts) a partir de un Markdown de actividades de planificación;
2. los PARÁMETROS de una ejecución concreta (eje transversal, áreas articuladas, nivel educativo, periodo, título e instrucciones del usuario).

Tu tarea NO es reescribir el prompt ni el template HTML. Debes producir un BLOQUE DE REFINAMIENTO compacto en Markdown que se anexará al system prompt para esta ejecución.

Reglas estrictas:
- Escribe en español académico.
- No inventes datos, secciones ni campos que el prompt base no contemple.
- No repitas el prompt base ni el template; no uses bloques de código.
- No contradigas el contrato `REPORT_DATA`, las fases ni las reglas de ejecución existentes.
- Adapta el énfasis al eje, áreas, nivel y periodo recibidos.
- Máximo 900 palabras. Sé concreto y accionable.

Formato obligatorio (empieza exactamente con este encabezado):

# REFINAMIENTO PARA ESTA EJECUCIÓN

## Énfasis temático
...

## Vinculación curricular
...

## Evidencia y trazabilidad
...

## Riesgos y sesgos a vigilar
...

## Criterios de aceptación adicionales
...

Devuelve únicamente el bloque Markdown, sin introducciones, conclusiones ni meta-comentarios.
TEXT;
    }

    /**
     * @param  array{eje:string,areas?:?string,nivel?:?string,periodo?:?string,titulo?:?string,instrucciones?:?string}  $params
     */
    private function refinerUserMessage(string $base, array $params): string
    {
        $lines = ['# PARÁMETROS DE ESTA EJECUCIÓN', ''];
        $lines[] = '- Eje transversal del plan: '.$this->clean($params['eje'] ?? '');

        $optional = [
            'Ejes/áreas articuladas' => $params['areas'] ?? null,
            'Nivel educativo' => $params['nivel'] ?? null,
            'Periodo' => $params['periodo'] ?? null,
            'Título del reporte' => $params['titulo'] ?? null,
        ];

        foreach ($optional as $label => $value) {
            $value = $this->clean($value);

            if ($value !== '') {
                $lines[] = "- {$label}: {$value}";
            }
        }

        $instructions = $this->clean($params['instrucciones'] ?? '');

        if ($instructions !== '') {
            $lines[] = '';
            $lines[] = '# INSTRUCCIONES ADICIONALES DEL USUARIO';
            $lines[] = '';
            $lines[] = $instructions;
        }

        return implode("\n", $lines)."\n\n---\n\n# SYSTEM PROMPT BASE A REFINAR\n\n".$base;
    }

    /**
     * Limpia fences y valida que la respuesta sea un bloque de refinamiento.
     */
    private function extractRefinement(string $raw): ?string
    {
        $text = trim($raw);

        if (preg_match('/```(?:markdown|md)?\s*(.*?)\s*```/s', $text, $matches)) {
            $text = trim($matches[1]);
        }

        if ($text === '' || mb_strlen($text) < self::MIN_REFINEMENT_CHARS) {
            return null;
        }

        if (! str_contains($text, self::REFINEMENT_HEADING)) {
            // Si el modelo omitió el encabezado, lo recuperamos cuando el bloque
            // empieza con un título Markdown propio.
            if (! str_starts_with($text, '#')) {
                return null;
            }

            $text = self::REFINEMENT_HEADING."\n\n".$text;
        }

        if (mb_strlen($text) > self::MAX_REFINEMENT_CHARS) {
            $text = mb_substr($text, 0, self::MAX_REFINEMENT_CHARS);
        }

        return $text;
    }

    private function normalizeRefinement(string $refinement): string
    {
        if (str_contains($refinement, self::REFINEMENT_HEADING)) {
            return $refinement;
        }

        return self::REFINEMENT_HEADING."\n\n".$refinement;
    }

    private function clean(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        return trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    }

    /**
     * Limpia un bloque conservando saltos de línea (instrucciones/refinamiento).
     */
    private function cleanBlock(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        $value = str_replace(["\r\n", "\r"], "\n", $value);

        return trim(preg_replace("/\n{3,}/", "\n\n", $value) ?? '');
    }

    /**
     * @return array{success:false,prompt:null,model:null,error:string}
     */
    private function failure(string $message): array
    {
        return [
            'success' => false,
            'prompt' => null,
            'model' => null,
            'error' => $message,
        ];
    }
}
