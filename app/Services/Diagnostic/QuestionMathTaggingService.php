<?php

namespace App\Services\Diagnostic;

use App\Services\OpenRouterService;
use Illuminate\Support\Facades\Log;

/**
 * Etiqueta expresiones matemáticas con LaTeX en preguntas de diagnóstico.
 *
 * Réplica del flujo "Etiquetar Not. Mat." del LessonWizard, adaptado a
 * preguntas: trabaja con texto plano (sin HTML, apto para diag_questions),
 * envolviendo matemáticas inline en \(...\) y destacadas en $$...$$ para
 * renderizado con KaTeX.
 */
class QuestionMathTaggingService
{
    /**
     * @param  string   $pregunta Enunciado de la pregunta.
     * @param  string[] $options  Textos de las opciones (mismo orden).
     * @return array{success: bool, pregunta: ?string, opciones: string[], error: ?string}
     */
    public function tag(string $pregunta, array $options = []): array
    {
        $pregunta = trim($pregunta);
        if ($pregunta === '') {
            return $this->errorResult('El enunciado está vacío.');
        }

        $options = array_values(array_map(fn ($o) => (string) $o, $options));

        $result = $this->askWithMathChain(
            $this->buildSystemPrompt(count($options)),
            $this->buildUserPrompt($pregunta, $options)
        );

        if (! ($result['success'] ?? false)) {
            return $this->errorResult($result['error'] ?? 'No se pudo etiquetar la pregunta.');
        }

        $payload = $this->parsePayload($result['content'] ?? null, count($options));

        if (! $payload) {
            Log::warning('QuestionMathTagging: respuesta IA sin formato válido', [
                'content' => mb_substr((string) ($result['content'] ?? ''), 0, 500),
            ]);

            return $this->errorResult('La IA no devolvió un resultado válido. Intenta nuevamente.');
        }

        return [
            'success' => true,
            'pregunta' => $payload['pregunta'],
            'opciones' => $payload['opciones'],
            'error' => null,
        ];
    }

    /**
     * Llama a la cadena de modelos math (primario + fallback 1).
     */
    protected function askWithMathChain(string $systemPrompt, string $userPrompt): array
    {
        $service = app(OpenRouterService::class);
        $overrides = ['max_tokens' => 2000, 'temperature' => 0.05, 'timeout' => 120];

        $result = $service->ask($systemPrompt, $userPrompt, array_merge($overrides, [
            'model' => config('openrouter.model_math_primary'),
        ]));

        if ($result['success'] ?? false) {
            return $result;
        }

        Log::warning('QuestionMathTagging: primario falló, usando fallback', [
            'error' => $result['error'] ?? null,
        ]);

        return $service->ask($systemPrompt, $userPrompt, array_merge($overrides, [
            'model' => config('openrouter.model_math_fallback1'),
        ]));
    }

    protected function buildSystemPrompt(int $optionsCount): string
    {
        return <<<'PROMPT'
Eres un asistente que detecta expresiones matemáticas en preguntas de diagnóstico y las convierte a LaTeX, preservando texto plano (SIN HTML).

REGLAS ESTRICTAS:
1. Detecta CADA expresión matemática (fórmulas, ecuaciones, símbolos, exponentes/subíndices, operadores, fracciones, raíces, integrales, funciones trigonométricas, unidades de medida, probabilidades, etc.).
2. Convierte a LaTeX válido:
   - Inline: \(...\) (dentro del texto normal)
   - Destacada: $$...$$ (solo para ecuaciones grandes que lo ameriten)
3. El texto SIN matemáticas se preserva EXACTAMENTE IGUAL, carácter por carácter.
4. PROHIBIDO HTML, explicaciones o texto fuera del JSON. Responde SOLO con el JSON.
5. Si un texto no tiene matemáticas, devuélvelo IDÉNTICO.
6. "opciones" debe tener EXACTAMENTE la misma cantidad de elementos y el mismo orden recibidos, sin marcadores.
7. ESCAPADO OBLIGATORIO: en el JSON, TODA barra invertida del LaTeX debe duplicarse (\\) de lo contrario el JSON es inválido. Ej.: \\( x \\) y \\frac{a}{b}.

CALIDAD LATEX: \frac{}{}, \sqrt{}, x^{n}, x_{n}, \pm \times \cdot \div \sum \int, \pi \alpha \beta \theta, \sin \cos \tan \log \ln, \left( \right).

EJEMPLO:
Entrada: {"pregunta":"¿Por qué número hay que multiplicar 18 para obtener 648?","opciones":["36","32","30"]}
Salida: {"pregunta":"¿Por qué número hay que multiplicar 18 para obtener 648?","opciones":["36","32","30"]}

Entrada: {"pregunta":"La fórmula cuadrática es x = (-b ± √(b² - 4ac)) / 2a","opciones":[]}
Salida: {"pregunta":"La fórmula cuadrática es \\(x = \\frac{-b \\pm \\sqrt{b^2 - 4ac}}{2a}\\)","opciones":[]}

FORMATO JSON: {"pregunta":"...","opciones":["..."]}
PROMPT;
    }

    protected function buildUserPrompt(string $pregunta, array $options): string
    {
        $payload = json_encode(
            ['pregunta' => $pregunta, 'opciones' => $options],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        return "Etiqueta las expresiones matemáticas del siguiente JSON y devuelve el JSON completo con el mismo esquema:\n\n{$payload}";
    }

    /**
     * Extrae y valida {"pregunta","opciones"} de la respuesta IA.
     */
    protected function parsePayload(?string $content, int $optionsCount): ?array
    {
        if (! is_string($content) || trim($content) === '') {
            return null;
        }

        $json = trim($content);
        if (str_starts_with($json, '```')) {
            $json = preg_replace('/^```(?:json)?\s*/i', '', $json);
            $json = preg_replace('/\s*```$/', '', $json);
        }

        $start = strpos($json, '{');
        $end = strrpos($json, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        $json = substr($json, $start, $end - $start + 1);

        $data = null;

        if ($this->hasSingleEscapedCommands($json)) {
            // Secuencias como \frac \beta \neq \theta con escape simple:
            // el decode estricto las "aceptaría" corrompiendo (\f→formfeed,
            // \b→backspace...). Se va directo al modo tolerante.
            $data = $this->decodeJson($this->escapeLoneBackslashes($json));
        } else {
            $data = $this->decodeJson($json);

            if (! is_array($data)) {
                // Fallback tolerante para \( \) \[ \] \$ etc. con escape simple.
                $data = $this->decodeJson($this->escapeLoneBackslashes($json));
            }
        }

        if (! is_array($data) || ! isset($data['pregunta']) || ! is_string($data['pregunta']) || trim($data['pregunta']) === '') {
            return null;
        }

        $opciones = $data['opciones'] ?? [];
        if (! is_array($opciones) || count($opciones) !== $optionsCount) {
            return null;
        }
        foreach ($opciones as $text) {
            if (! is_string($text)) {
                return null;
            }
        }

        return ['pregunta' => $data['pregunta'], 'opciones' => array_values($opciones)];
    }

    protected function decodeJson(string $json): mixed
    {
        try {
            return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Detecta comandos LaTeX con escape simple (\frac \beta \neq \theta...)
     * que el decode estricto corrompería en silencio.
     */
    protected function hasSingleEscapedCommands(string $json): bool
    {
        return (bool) preg_match('/(?<!\\\\)\\\\[fbnrt][a-zA-Z]/', $json);
    }

    /**
     * Duplica barras invertidas sueltas, preservando escapes JSON legítimos:
     * \" \\ \/ \uXXXX y \b \n \r \t \f seguidos de NO-letra (intención real).
     * No toca pares ya duplicados (lookbehind).
     */
    protected function escapeLoneBackslashes(string $json): string
    {
        $out = preg_replace(
            '/(?<!\\\\)\\\\(?![\"\\\\\\/]|u[0-9a-fA-F]{4}|[bnrtf](?![a-zA-Z]))/',
            '\\\\\\\\',
            $json
        );

        return is_string($out) ? $out : $json;
    }

    protected function errorResult(string $message): array
    {
        return ['success' => false, 'pregunta' => null, 'opciones' => [], 'error' => $message];
    }
}
