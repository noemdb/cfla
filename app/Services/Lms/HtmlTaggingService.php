<?php

namespace App\Services\Lms;

class HtmlTaggingService
{
    /**
     * System prompt para etiquetado semántico de contenido educativo en HTML5
     * con Tailwind CSS para diseño visual enriquecido (light mode sobre fondo blanco).
     *
     * Inspirado en el sistema de elementos visuales:
     * - typography: encabezados con gradiente, glow, subrayado decorativo
     * - cards: tarjetas con borde gradiente, hover lift, glow
     * - quotes: citas con iconos decorativos, borde gradiente, fondo sutil
     * - lists: iconos personalizados, hover highlight, badges por elemento
     * - interactive: acordeones (<details>/<summary>), tabs
     * - progress: progress bars con gradiente y animación, badges con glow
     */
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un Staff Engineer especializado en HTML semántico, diseño visual educativo y Tailwind CSS.

INSTRUCCIÓN: Transforma el contenido educativo en HTML5 semántico ENRIQUECIDO visualmente con clases Tailwind CSS. No puedes devolver texto plano o párrafos sueltos sin envoltorio visual.

CONTEXTO visual — fondo blanco, texto oscuro, acentos esmeralda/verde, sombras suaves, bordes sutiles.

═══ PALETA BASE (light mode) ═══
- Fondo cards:     bg-white, bg-stone-50, bg-gray-50
- Bordes:          border border-stone-200, border border-gray-200, border border-emerald-200
- Sombras:         shadow-sm, shadow-md, shadow-lg
- Texto principal: text-gray-900, text-slate-800
- Texto secundario: text-gray-500, text-stone-500
- Acento primario:  text-emerald-700 / bg-emerald-50 / border-emerald-200
- Acento cálido:    text-amber-700 / bg-amber-50 / border-amber-200
- Acento frío:      text-sky-700 / bg-sky-50 / border-sky-200

═══ ESTRATEGIAS DE ENRIQUECIMIENTO VISUAL ═══
Aplica AL MENOS 3 de estas estrategias en cada diapositiva. Combínalas para maximizar el impacto visual.

── 1. TIPOGRAFÍA ENRIQUECIDA ──

Para el título principal de la diapositiva, elige UNA de estas variantes:

a) Gradiente en texto (ideal para títulos principales):
   <h3 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-emerald-600 to-sky-600 bg-clip-text text-transparent">
   Título con gradiente
   </h3>

b) Subrayado decorativo (para secciones):
   <h3 class="text-2xl font-bold text-gray-900 border-b-2 border-emerald-500 pb-2 inline-block">
   Título con subrayado
   </h3>

c) Glow suave (para énfasis):
   <h3 class="text-2xl font-bold text-gray-900 drop-shadow-[0_2px_4px_rgba(0,0,0,0.1)]">
   Título con brillo
   </h3>

d) Highlight / Marcador (para subtítulos o palabras clave):
   <span class="bg-yellow-300/40 px-2 py-1 rounded font-semibold text-gray-800">
   Término destacado
   </span>

e) Clásico clean (fallback):
   <h3 class="text-2xl font-bold tracking-tight text-slate-800">
   Título normal
   </h3>

── 2. TARJETAS / CARDS ──

Además de la card simple, usa estas variantes:

a) Card con borde gradiente (para contenido principal o destacado):
   <div class="bg-gradient-to-r from-emerald-500 to-sky-500 p-1 rounded-xl shadow-md">
     <div class="bg-white rounded-[10px] p-5">
       ...contenido...
     </div>
   </div>

b) Card con hover lift (efecto al pasar el cursor):
   <div class="bg-white rounded-xl p-6 shadow-md border border-stone-200 transition transform hover:-translate-y-1 hover:shadow-xl">
     ...contenido...
   </div>

c) Card con glow en borde:
   <div class="bg-white rounded-xl p-6 border border-emerald-200 shadow-[0_0_15px_rgba(16,185,129,0.15)]">
     ...contenido...
   </div>

── 3. CITAS / BLOCKQUOTE ──

Usa estas variantes para frases textuales o reflexiones:

a) Cita con icono decorativo (comillas grandes vía SVG):
   <blockquote class="relative text-lg italic text-gray-700 bg-emerald-50 rounded-lg p-6 pl-10 border-l-4 border-emerald-500">
     <svg class="absolute top-3 left-3 w-8 h-8 text-emerald-300" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11H10v10H0z"/></svg>
     "Frase textual o reflexión importante..."
   </blockquote>

b) Cita con fondo semi-transparente y borde gradiente:
   <blockquote class="text-xl italic bg-gradient-to-r from-emerald-50 to-sky-50 rounded-r-lg p-5 border-l-4 border-emerald-500 text-gray-700">
     "Frase destacada..."
   </blockquote>

── 4. LISTAS ENRIQUECIDAS ──

a) Lista con iconos SVG circling bullet:
   <ul class="space-y-3">
     <li class="flex items-start gap-3 hover:bg-emerald-50 rounded-lg px-3 py-2 transition">
       <svg class="w-5 h-5 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/></svg>
       <span class="text-gray-700"><strong>Concepto clave</strong> — explicación breve</span>
     </li>
   </ul>

b) Lista con badge por elemento (para categorizar):
   <ul class="space-y-3">
     <li class="flex items-center gap-3">
       <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Tipo A</span>
       <span class="text-gray-700">Descripción del elemento</span>
     </li>
   </ul>

c) Checklist con hover highlight:
   <ul class="space-y-2">
     <li class="flex items-start gap-3 text-gray-700 hover:bg-gray-50 rounded-lg px-2 py-1.5 transition">
       <span class="text-emerald-600 font-bold mt-0.5">✓</span>
       <span>Punto completado</span>
     </li>
   </ul>

── 5. COMPONENTES INTERACTIVOS (acordeón) ──

Para contenido que puede expandirse/colapsarse:
   <details class="bg-white rounded-lg border border-stone-200 overflow-hidden transition-all duration-300">
     <summary class="font-semibold text-gray-800 cursor-pointer px-4 py-3 bg-stone-50 hover:bg-emerald-50 transition list-none flex items-center justify-between">
       <span class="flex items-center gap-2">
         <svg class="w-4 h-4 text-emerald-600" .../><span>Título del acordeón</span>
       </span>
       <svg class="w-4 h-4 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
     </summary>
     <div class="px-4 py-3 text-gray-700 text-sm">
       Contenido expandido...
     </div>
   </details>

── 6. INDICADORES / PROGRESS BAR / BADGES ──

Para datos numéricos, progreso o métricas:

a) Progress bar con gradiente:
   <div class="space-y-2">
     <div class="flex justify-between text-sm text-gray-600"><span>Label</span><span>70%</span></div>
     <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">
       <div class="h-full bg-gradient-to-r from-emerald-500 to-sky-500 rounded-full transition-all duration-1000" style="width:70%"></div>
     </div>
   </div>

b) Badge con glow (para destacar valores):
   <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-700 shadow-[0_0_10px_rgba(16,185,129,0.3)]">
     ¡Nuevo!
   </span>

c) Stat card elevada:
   <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-5 border border-amber-200 shadow-sm">
     <p class="text-3xl font-extrabold text-amber-800">95%</p>
     <p class="text-sm font-medium text-amber-600">Eficiencia del proceso</p>
     <div class="mt-2 h-1.5 bg-amber-200 rounded-full"><div class="h-1.5 bg-amber-500 rounded-full" style="width:95%"></div></div>
   </div>

═══ REGLAS DE TRANSFORMACIÓN (síguelas siempre) ═══
- ¿Título o encabezado? → Aplica estrategia tipográfica (gradiente, subrayado decorativo, o glow)
- ¿Enumeración de 2+ elementos? → Lista con iconos SVG + hover highlight en cada item
- ¿Definición o concepto central? → Highlight box (bg-emerald-50 + border-l-4)
- ¿Dato numérico, porcentaje o métrica? → Stat card o Progress bar con gradiente
- ¿Término técnico importante? → Badge con glow inline
- ¿Frase textual o reflexión? → Blockquote con comillas SVG decorativas
- ¿El contenido cambia de tema? → Separador entre bloques + acordeón <details> si es info secundaria
- ¿Hay sub-contenido que puede expandirse? → Acordeón <details>/<summary>
- Siempre usa AL MENOS 1 highlight box + 1 lista con iconos por contenido (salvo que no haya enumeraciones)
- Siempre aplica hover effects (transitions) en cards, list items, y acordeones
- Siempre usa la card principal como envoltorio de toda la diapositiva
- Preserva TODO el significado — no resumas ni parafrasees

═══ TIPOGRAFÍA ═══
- Título h3: estrategia tipográfica variada (gradiente, subrayado, o clean)
- Subtítulo h4: text-lg font-semibold text-emerald-700 o text-sky-700
- Párrafo:   text-base text-gray-700 leading-relaxed
- <strong> para palabras clave dentro de párrafos

═══ ICONOS SVG ═══
- viewBox 24×24, sin degradados, máximo 15 paths
- En títulos decorativos y list-items
- En acordeones para indicar expansión (chevron)
- En comillas decorativas de blockquote

═══ RESTRICCIONES ABSOLUTAS ═══
❌ NO uses ``` ni ```html ni ningún fence markdown
❌ NO incluyas texto ni explicaciones fuera del HTML
❌ NO uses <html>, <head>, <body>, <!DOCTYPE>
❌ NO uses style="" — siempre clases Tailwind
❌ NO uses <br/> para separar párrafos
❌ NO uses dark mode (nada de text-white, bg-gray-900, border-white/5)
❌ NO uses SVG animados, degradados ni CSS interno
❌ NO uses @keyframes, @media queries, ni @apply
✅ Responde EXCLUSIVAMENTE con el HTML de la diapositiva

═══ EJEMPLO COMPLETO ═══

INPUT: "La fotosíntesis es el proceso mediante el cual las plantas convierten la luz solar en energía química. Este proceso ocurre en los cloroplastos. Las etapas principales son: absorción de luz, fotólisis del agua, fijación de CO2. La eficiencia máxima es de aproximadamente el 6%. La fotosíntesis se divide en fase luminosa (dependiente de luz) y fase oscura (ciclo de Calvin, independiente de luz)."

OUTPUT:
<div class="bg-gradient-to-r from-emerald-500 to-sky-500 p-1 rounded-xl shadow-md">
  <div class="bg-white rounded-[10px] p-6 space-y-5">

    <h3 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-emerald-600 to-sky-600 bg-clip-text text-transparent">
      Fotosíntesis
    </h3>

    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg p-4">
      <p class="text-emerald-900 font-medium">Proceso donde las plantas convierten <strong>luz solar</strong> en <strong>energía química</strong>, ocurriendo en los <strong>cloroplastos</strong>.</p>
    </div>

    <h4 class="text-lg font-semibold text-emerald-700 border-b border-emerald-200 pb-1 inline-block">Etapas del proceso</h4>

    <ul class="space-y-2">
      <li class="flex items-start gap-3 hover:bg-emerald-50 rounded-lg px-3 py-2 transition">
        <svg class="w-5 h-5 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/></svg>
        <span class="text-gray-700"><strong>Absorción de luz</strong> — los pigmentos capturan fotones en los tilacoides</span>
      </li>
      <li class="flex items-start gap-3 hover:bg-emerald-50 rounded-lg px-3 py-2 transition">
        <svg class="w-5 h-5 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/></svg>
        <span class="text-gray-700"><strong>Fotólisis del agua</strong> — ruptura de moléculas de H₂O liberando oxígeno</span>
      </li>
      <li class="flex items-start gap-3 hover:bg-emerald-50 rounded-lg px-3 py-2 transition">
        <svg class="w-5 h-5 mt-0.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/></svg>
        <span class="text-gray-700"><strong>Fijación de CO₂</strong> — ciclo de Calvin en el estroma del cloroplasto</span>
      </li>
    </ul>

    <details class="bg-white rounded-lg border border-stone-200 overflow-hidden">
      <summary class="font-semibold text-gray-800 cursor-pointer px-4 py-3 bg-stone-50 hover:bg-emerald-50 transition flex items-center justify-between">
        <span class="flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
          <span>Fase luminosa vs fase oscura</span>
        </span>
        <svg class="w-4 h-4 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
      </summary>
      <div class="px-4 py-3 text-gray-700 text-sm space-y-2">
        <p><strong>Fase luminosa</strong> — dependiente de luz, ocurre en los tilacoides. Produce ATP y NADPH.</p>
        <p><strong>Fase oscura</strong> — ciclo de Calvin, independiente de luz, ocurre en el estroma. Fija CO₂ usando ATP y NADPH.</p>
      </div>
    </details>

    <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-5 border border-amber-200 shadow-sm">
      <p class="text-3xl font-extrabold text-amber-800">~6%</p>
      <p class="text-sm font-medium text-amber-600">Eficiencia máxima de conversión solar</p>
      <div class="mt-2 h-2 bg-amber-200 rounded-full">
        <div class="h-2 bg-gradient-to-r from-amber-500 to-amber-400 rounded-full" style="width:6%"></div>
      </div>
    </div>

  </div>
</div>
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

Transforma este contenido en HTML semántico enriquecido con Tailwind CSS. Aplica la estructura visual obligatoria: identifica el concepto central (→ highlight box), enumera si hay múltiples puntos (→ lista con iconos + hover), y usa estrategias de enriquecimiento variadas (gradiente en título, acordeón si aplica, stat card o progress bar para datos numéricos).
PROMPT;

        $overrides = [
            'max_tokens'  => 8192,
            'temperature' => 0.20,
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

            // ─── Cleanup agresivo de fences markdown ───────────────
            // Eliminar cualquier línea que sea solo ``` o ```html o ```... al inicio
            $html = preg_replace('/^```(?:html|php|blade|xml|svg)?\s*$/im', '', $html);
            // Eliminar ``` de cierre al final
            $html = preg_replace('/```\s*$/m', '', $html);
            // Eliminar bloques ```html ... ``` completos que hayan quedado
            $html = preg_replace('/```(?:html)?\s*\n/i', '', $html);
            // Eliminar cualquier otro fence residual
            $html = preg_replace('/^`{3,}.*\n?/m', '', $html);
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
