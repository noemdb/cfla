<?php

namespace App\Services\Lms;

class HtmlTaggingService
{
    /**
     * System prompt para etiquetado semántico de contenido educativo en HTML5.
     */
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un Staff Engineer especializado en HTML semántico y contenido educativo digital.

INSTRUCCIÓN: Transforma el contenido educativo recibido en HTML5 semántico limpio y correcto.
Sin frameworks, sin clases CSS, sin estilos inline — solo HTML semántico puro.

ETIQUETAS PERMITIDAS (úsalas según el contenido):
- <h3>, <h4>        → Subtítulos y encabezados de sección
- <p>               → Párrafos de texto (NUNCA uses <br> para separar)
- <ul>, <ol>, <li>  → Listas desordenadas y ordenadas
- <blockquote>      → Citas textuales, reflexiones o frases destacadas
- <dl>, <dt>, <dd>  → Definiciones de términos
- <table>, <thead>, <tbody>, <tr>, <th>, <td> → Datos tabulares
- <strong>          → Conceptos clave o términos importantes
- <em>              → Énfasis natural en frases
- <span>            → Anotaciones inline, acentos o matices dentro de un párrafo
- <code>            → Fragmentos de código técnico
- <hr>              → Separación temática entre bloques
- <svg>             → Iconos decorativos inline que refuercen visualmente el contenido

PRINCIPIOS DE INGENIERÍA:
- Analiza la estructura intrínseca del contenido e identifica cada bloque semántico
- Si el contenido comienza con un título o tema principal, usa <h3>
- Subtemas dentro del contenido → <h4>
- Texto corrido con una idea → <p>
- Serie de elementos → <ul>/<ol> con <li>
- Definiciones técnica → <dl>/<dt>/<dd>
- Frase célebre, dichos, reflexiones → <blockquote>
- Números clave, datos, porcentajes → <span> o <strong>
- NOTA: usa <strong> solo en palabras/frases clave, no párrafos completos
- NOTA: <span> es para matices inline, no para bloques
- Preserva TODO el significado original — no resumas ni parafrasees, solo mejora el marcado

ICONOS SVG — cuándo y cómo usarlos:
- Agrega un <svg> pequeño (18×18 a 24×24) al inicio de <h3> o <h4> para reforzar el tema
- Simple: solo líneas, círculos, rectángulos — sin degradados ni sombras
- Usa stroke="currentColor" fill="none" con stroke-width="1.5" o "2" para outline
- Para íconos sólidos usa fill="currentColor" — siempre color neutro (hereda del texto)
- viewBox="0 0 24 24" estándar, inline con el texto
- EJ: <h3><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 12h3v8h6v-6h2v6h6v-8h3L12 2z"/></svg> Título</h3>
- EJ: <blockquote><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M6 17h3l2-4V7H5v6h3l-2 4zM14 17h3l2-4V7h-6v6h3l-2 4z"/></svg> Cita destacada</blockquote>
- EJ: <ul><li><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg> Elemento</li></ul>
- NO uses svg en párrafos largos — solo en títulos, lista headers, citas o encabezados
- NO más de 20 comandos path, NO animaciones, NO <style> dentro del svg
- NO satures: máximo 1 icono por título, 1 por bloque destacado

RESTRICCIONES ABSOLUTAS:
❌ NO uses markdown, ```fences, ni ```html
❌ NO incluyas explicaciones ni notas fuera del HTML
❌ NO añadas <html>, <head>, <body>, <!DOCTYPE> — solo fragmentos semánticos
❌ NO uses atributos style, class, o id
❌ NO uses <br/> para separar párrafos
❌ NO envuelvas todo en un solo <p> — estructura correctamente
✅ Responde ÚNICAMENTE con el HTML transformado

La calidad debe ser de producción: etiquetado semántico impecable, estructura jerárquica correcta, sin errores de anidamiento.
PROMPT;

    /**
     * Etiqueta contenido educativo con HTML semántico usando IA.
     *
     * @param  string   $originalBody Contenido plano original a etiquetar.
     * @param  string   $sectionTitle Título de la sección/diapositiva.
     * @param  string   $gradeName    Nombre del grado (ej. "1er Grado").
     * @param  string   $subjectName  Nombre de la asignatura.
     * @param  callable $aiCallback   Función que recibe (systemPrompt, userPrompt, overrides) y retorna array{success: bool, content: ?string, error: ?string}.
     * @return array{success: bool, html: ?string, error: ?string}
     */
    public function tag(
        string $originalBody,
        string $sectionTitle,
        string $gradeName,
        string $subjectName,
        callable $aiCallback,
    ): array {
        $userPrompt = <<<PROMPT
### Contexto pedagógico
- **Grado:** {$gradeName}
- **Asignatura:** {$subjectName}
- **Título de la sección:** {$sectionTitle}

### Contenido original a etiquetar

{$originalBody}

Analiza este contenido, identifica su estructura semántica, y transfórmalo a HTML5 limpio y semántico con las etiquetas apropiadas.
PROMPT;

        $overrides = [
            'max_tokens'  => 4096,
            'temperature' => 0.25,
            'timeout'     => 120,
        ];

        try {
            $aiResult = $aiCallback(self::SYSTEM_PROMPT, $userPrompt, $overrides);

            if (!$aiResult['success']) {
                return [
                    'success' => false,
                    'html'    => null,
                    'error'   => $aiResult['error'] ?? 'Error desconocido del servicio IA.',
                ];
            }

            $html = $aiResult['content'] ?? '';

            // Limpiar wrappers de markdown fences que el LLM pueda añadir
            $html = preg_replace('/^```(?:html)?\s*\n?/i', '', $html);
            $html = preg_replace('/\n?```\s*$/s', '', $html);
            $html = trim($html);

            // Validar que realmente contiene etiquetas HTML
            if (!str_contains($html, '<')) {
                return [
                    'success' => false,
                    'html'    => null,
                    'error'   => 'La IA no generó HTML semántico. Reintenta o etiqueta manualmente.',
                ];
            }

            return [
                'success' => true,
                'html'    => $html,
                'error'   => null,
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'html'    => null,
                'error'   => 'Error inesperado: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Versión simplificada que usa OpenRouterService directamente.
     * Útil cuando no se necesita el fallback con compactación de Nvidia.
     *
     * @param  string $sectionTitle
     * @param  string $originalBody
     * @param  string $gradeName
     * @param  string $subjectName
     * @return array{success: bool, html: ?string, error: ?string}
     */
    public function tagWithOpenRouter(
        string $originalBody,
        string $sectionTitle,
        string $gradeName,
        string $subjectName,
    ): array {
        /** @var \App\Services\OpenRouterService $llm */
        $llm = app(\App\Services\OpenRouterService::class);

        return $this->tag(
            $originalBody,
            $sectionTitle,
            $gradeName,
            $subjectName,
            fn(string $systemPrompt, string $userPrompt, array $overrides) => $llm->ask(
                $systemPrompt,
                $userPrompt,
                $overrides
            ),
        );
    }
}
