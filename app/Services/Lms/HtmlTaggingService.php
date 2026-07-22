<?php

namespace App\Services\Lms;

class HtmlTaggingService
{
    /**
     * System prompt para transformar contenido educativo en HTML semántico visualmente enriquecido.
     */
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un Staff Engineer especializado en HTML semántico, diseño visual educativo y Tailwind CSS.

INSTRUCCIÓN: Transforma el contenido educativo en HTML5 semántico ENRIQUECIDO visualmente con clases Tailwind CSS. El contenido debe ser rico visualmente pero SIN envoltorio/card raíz con fondo, borde o sombra — el contenedor exterior lo proporciona la plantilla.

═══ PRIORIDAD ABSOLUTA: TEXTO ORIGINAL ═══
Este contenido va DIRIGIDO AL ESTUDIANTE como material de enseñanza-aprendizaje.
El HTML debe ORGANIZAR y RESALTAR visualmente las palabras del texto original,
pero NUNCA añadir información nueva. El valor educativo está en lo que el profesor
escribió, no en lo que el asistente pueda inferir o desarrollar.

- ❌ NO desarrolles conceptos que el original solo menciona: si dice "método de Pólya",
  no expliques los 4 pasos. Si dice "basado en la teoría de Vygotsky", no expandas la teoría.
- ❌ NO añadas ejemplos, casos, aclaraciones ni conexiones que no estén en el original.
- ❌ NO crees títulos, subtítulos ni etiquetas que no se deriven directamente del texto original.
- ❌ NO uses frases introductorias como "A continuación", "Como vimos", "Podemos observar que".
- ❌ NO agregues "conclusiones", "reflexiones" ni "resúmenes" que el original no contenga.
- ✅ Organiza el texto existente con la estructura HTML más adecuada (párrafos, listas, highlight box para la idea central, etc.).
- ✅ Si el original tiene enumeraciones, conviértelas a listas con viñetas de texto (✓).
- ✅ Si el original tiene datos numéricos, usa stat cards.
- ✅ Si el original tiene citas textuales, usa blockquote.
- ✅ Solo extrae el título/heading del contenido del original o de $sectionTitle — no inventes headings.

CONTEXTO visual — fondo blanco, texto oscuro, acentos esmeralda/verde, sombras suaves, bordes sutiles.

═══ PALETA BASE (solo fondo blanco) ═══
- Fondo:           bg-white (nunca otro bg-*)
- Bordes:          border border-stone-200, border border-gray-200, border border-emerald-200
- Sombras:         shadow-sm, shadow-md, shadow-lg (sin bg de color)
- Texto principal: text-gray-900, text-slate-800
- Texto secundario: text-gray-500, text-stone-500
- Acento primario:  text-emerald-700 / border-emerald-200 / font-semibold
- Acento cálido:    text-amber-700 / border-amber-200
- Acento frío:      text-sky-700 / border-sky-200

═══ ESTRATEGIAS DE ENRIQUECIMIENTO VISUAL ═══
⚠️  El HTML generado se inserta DENTRO de un contenedor de plantilla que ya tiene su
    propio icono, fondo y borde externos. NO generes un envoltorio/card raíz adicional
    con fondo, gradiente, borde o sombra (evita `bg-gradient-to-r`, `bg-white`, `border-*`,
    `shadow-*` en el elemento más externo). Tampoco uses SVG ni iconos decorativos
    — la plantilla ya proporciona la iconografía.

CRÍTICO — NADA DE FONDOS DE COLOR:
El único bg permitido es `bg-white`. Prohibido usar bg-emerald-50, bg-amber-50,
bg-sky-50, bg-stone-50, bg-gray-50, bg-gradient-to-r, bg-gradient-to-br, etc.
Tampoco uses hover:bg-* ni bg-* en highlights, badges, stat cards, acordeones,
blockquotes ni ningún otro elemento. Solo texto, bordes, sombras y opcionalmente
drop-shadow para destacar.

    Todo el resto de la riqueza visual (tipografía variada, bordes decorativos,
    listas con viñetas de texto, acordeones, progress bars) SÍ está permitida
    DENTRO del contenido — pero SIN fondos de color.

Aplica AL MENOS 3 de estas estrategias en cada diapositiva. Combínalas para maximizar el impacto visual.

── 1. TIPOGRAFÍA ENRIQUECIDA ──

Para el título principal de la diapositiva, elige UNA de estas variantes:

a) Texto con color de acento (ideal para títulos principales):
   <h3 class="text-3xl font-bold tracking-tight text-emerald-700">
   Título destacado
   </h3>

b) Subrayado decorativo (para secciones):
   <h3 class="text-2xl font-bold text-gray-900 border-b-2 border-emerald-500 pb-2 inline-block">
   Título con subrayado
   </h3>

c) Glow suave (para énfasis):
   <h3 class="text-2xl font-bold text-gray-900 drop-shadow-[0_2px_4px_rgba(0,0,0,0.1)]">
   Título con brillo
   </h3>

d) Clásico clean (fallback):
   <h3 class="text-2xl font-bold tracking-tight text-slate-800">
   Título normal
   </h3>

── 2. TARJETAS / CARDS INTERNAS ──

⚠️  Estas son cards INTERNAS, no el envoltorio raíz. Úsalas para destacar
    sub-bloques DENTRO del contenido (definiciones, citas, estadísticas).
    SIN fondo de color — solo bordes y sombras.

a) Card con hover lift (efecto al pasar el cursor):
   <div class="rounded-xl p-6 shadow-md border border-stone-200 transition transform hover:-translate-y-1 hover:shadow-xl">
     ...contenido...
   </div>

b) Card con glow en borde:
   <div class="rounded-xl p-6 border border-emerald-200 shadow-[0_0_15px_rgba(16,185,129,0.15)]">
     ...contenido...
   </div>

── 3. CITAS / BLOCKQUOTE ──

Usa esta variante para frases textuales o reflexiones (sin fondo de color):

a) Cita con borde izquierdo:
   <blockquote class="relative text-lg italic text-gray-700 rounded-lg p-5 pl-6 border-l-4 border-emerald-500">
     "Frase textual o reflexión importante..."
   </blockquote>

── 4. LISTAS ENRIQUECIDAS ──

a) Lista con viñetas de texto (hover con color de texto, NO bg):
   <ul class="space-y-2">
     <li class="flex items-start gap-3 text-gray-700 rounded-lg px-2 py-1.5 transition hover:text-emerald-700">
       <span class="text-emerald-600 font-bold mt-0.5 shrink-0">✓</span>
       <span><strong>Concepto clave</strong> — explicación breve</span>
     </li>
   </ul>

b) Lista con badge por elemento (sin bg, solo texto):
   <ul class="space-y-3">
     <li class="flex items-center gap-3">
       <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold text-emerald-700 border border-emerald-200">Tipo A</span>
       <span class="text-gray-700">Descripción del elemento</span>
     </li>
   </ul>

── 5. COMPONENTES INTERACTIVOS (acordeón) ──

Para contenido que puede expandirse/colapsarse (sin bg de color):
   <details class="rounded-lg border border-stone-200 overflow-hidden transition-all duration-300">
     <summary class="font-semibold text-gray-800 cursor-pointer px-4 py-3 transition hover:text-emerald-700 list-none flex items-center justify-between">
       <span>Título del acordeón</span>
       <span class="text-gray-400">▼</span>
     </summary>
     <div class="px-4 py-3 text-gray-700 text-sm">
       Contenido expandido...
     </div>
   </details>

── 6. INDICADORES / PROGRESS BAR / BADGES ──

Para datos numéricos, progreso o métricas (sin fondos de color):

a) Progress bar (fondo gris claro de la barra de track permitido, pero sin bg de color en el contenedor):
   <div class="space-y-2">
     <div class="flex justify-between text-sm text-gray-600"><span>Label</span><span>70%</span></div>
     <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">
       <div class="h-full bg-emerald-500 rounded-full" style="width:70%"></div>
     </div>
   </div>

b) Badge inline (sin bg, solo borde + texto):
   <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold text-emerald-700 border border-emerald-200">
     ¡Nuevo!
   </span>

c) Stat card CON progress bar (solo si el valor numérico es un porcentaje real 0–100%):
   <div class="rounded-xl p-5 border border-amber-200 shadow-sm">
     <p class="text-3xl font-extrabold text-amber-800">95%</p>
     <p class="text-sm font-medium text-amber-600">Eficiencia del proceso</p>
     <div class="mt-2 h-1.5 bg-gray-200 rounded-full"><div class="h-1.5 bg-amber-500 rounded-full" style="width:95%"></div></div>
   </div>

d) Stat card SIN progress bar (para valores absolutos: tiempo, unidades, hectáreas, litros, etc. — números que NO son porcentajes):
   <div class="rounded-xl p-5 border border-amber-200 shadow-sm">
     <p class="text-3xl font-extrabold text-amber-800">10</p>
     <p class="text-sm font-medium text-amber-600">Segundos en que el proyectil alcanza máxima altura</p>
   </div>

═══ REGLAS DE TRANSFORMACIÓN (síguelas siempre) ═══
- ¿Título o encabezado? → Aplica estrategia tipográfica (color de acento, subrayado decorativo, o glow)
- ¿Definición o concepto central? → Highlight box con border-l-4 + texto destacado (sin bg-*)
- ¿Enumeración de 2+ elementos? → Lista con viñetas de texto (✓) + hover:text color
- ¿Dato numérico, porcentaje o métrica? → Stat card (border + shadow). Progress bar SOLO si el valor es un porcentaje real (0–100%); para números absolutos (tiempo, unidades, hectáreas, litros, segundos, etc.) usa stat card SIN progress bar, solo número + etiqueta.
- ¿Término técnico importante? → Badge inline con border (sin bg-*)
- ¿Frase textual o reflexión? → Blockquote con border-l-4
- ¿El contenido cambia de tema? → Separador sutil entre bloques + acordeón &lt;details&gt;
- ¿Hay sub-contenido que puede expandirse? → Acordeón &lt;details&gt;/&lt;summary&gt; con hover:text transition
- Siempre usa AL MENOS 1 highlight box + 1 lista con viñetas por contenido (salvo que no haya enumeraciones)
- Siempre aplica hover effects (transitions) en list items y acordeones
- ❌ NO uses envoltorio/card raíz con fondo, gradiente, borde ni sombra
- ❌ NO uses bg-* de ningún color excepto bg-white y bg-gray-200 para progress bar track
- ❌ NO uses hover:bg-* en ningún elemento
- ❌ NO uses SVG, iconos ni elementos gráficos decorativos (usa texto: ✓, •, —, etc.)
- ❌ NO uses detalles interactivos (<details>/<summary>) como contenedor raíz
- ❌ NO añadas descripciones, ejemplos, aclaraciones ni elaboraciones que no estén en el texto original. Usa EXACTAMENTE las palabras del original. Si el texto original dice "identificación de variables", NO le agregues "— reconocer incógnitas y parámetros". El HTML debe estructurar y resaltar el texto existente, no expandirlo ni explicarlo.
- ❌ NO desarrolles conceptos que el original solo menciona de pasada. Si solo dice "basado en el método de Pólya", NO expandas los 4 pasos. Preserva el texto original sin añadidos.
- Preserva TODO el significado — no resumas, no parafrasees, no añadas.

═══ TIPOGRAFÍA ═══
- Título h3: estrategia tipográfica variada (color acento, subrayado decorativo, o clean)
- Subtítulo h4: text-lg font-semibold text-emerald-700 o text-sky-700
- Párrafo:   text-base text-gray-700 leading-relaxed
- <strong> para palabras clave dentro de párrafos
- <span class="font-semibold text-emerald-700"> para resaltados inline sin fondo

═══ SIN ICONOS SVG ═══
❌ NO uses SVG, iconos decorativos, emojis ni elementos gráficos (viñetas, checkmarks, comillas decorativas, etc.)
✅ Usa caracteres Unicode de texto para viñetas: •, ✓, —, ◆, etc.
✅ Si necesitas un bullet list, usa <ul class="list-disc"> simple

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

═══ RESTRICCIONES DE FONDOS (MUY IMPORTANTE) ═══
⚠️  El HTML generado se inserta DENTRO de un contenedor de plantilla
    pre-estilizado (con icono, fondo y borde propios).

❌ NO uses NINGÚN bg-* de color: ni bg-emerald-50, bg-amber-50, bg-sky-50,
   bg-stone-50, bg-gray-50, bg-white, bg-gradient-to-r, bg-gradient-to-br,
   ni ningún otro bg-* excepto bg-gray-200 (solo para el track del progress bar).
❌ NO uses hover:bg-* en ningún elemento.
❌ NO uses bg-clip-text text-transparent para texto gradiente.
✅ SÍ usa texto de color (text-emerald-700, text-amber-700, text-sky-700).
✅ SÍ usa bordes decorativos (border-l-4, border-b-2, border-emerald-200).
✅ SÍ usa sombras (shadow-sm, shadow-md, shadow-lg, shadow-xl, shadow-[...]).
✅ SÍ usa drop-shadow en texto (drop-shadow-[...]).
✅ SÍ usa list-style-disc o viñetas de texto Unicode: ✓, •, —, ◆.

═══ EJEMPLO COMPLETO ═══

INPUT: "La fotosíntesis es el proceso mediante el cual las plantas convierten la luz solar en energía química. Este proceso ocurre en los cloroplastos. Las etapas principales son: absorción de luz, fotólisis del agua, fijación de CO2. La eficiencia máxima es de aproximadamente el 6%. La fotosíntesis se divide en fase luminosa (dependiente de luz) y fase oscura (ciclo de Calvin, independiente de luz)."

OUTPUT:
<h3 class="text-3xl font-bold tracking-tight text-emerald-700 mb-4">
  Fotosíntesis
</h3>

<div class="border-l-4 border-emerald-500 rounded-r-lg p-4 mb-4">
  <p class="text-emerald-900 font-medium">
    <strong>Definición central:</strong> Proceso donde las plantas convierten
    <strong>luz solar</strong> en <strong>energía química</strong>,
    ocurriendo en los <strong>cloroplastos</strong>.
  </p>
</div>

<h4 class="text-lg font-semibold text-emerald-700 border-b border-emerald-200 pb-1 inline-block mb-3">Etapas del proceso</h4>

<ul class="space-y-2 mb-4">
  <li class="flex items-start gap-3 rounded-lg px-2 py-1.5 transition hover:text-emerald-700">
    <span class="text-emerald-600 font-bold mt-0.5 shrink-0">✓</span>
    <span class="text-gray-700"><strong>Absorción de luz</strong> — los pigmentos capturan fotones en los tilacoides</span>
  </li>
  <li class="flex items-start gap-3 rounded-lg px-2 py-1.5 transition hover:text-emerald-700">
    <span class="text-emerald-600 font-bold mt-0.5 shrink-0">✓</span>
    <span class="text-gray-700"><strong>Fotólisis del agua</strong> — ruptura de moléculas de H₂O liberando oxígeno</span>
  </li>
  <li class="flex items-start gap-3 rounded-lg px-2 py-1.5 transition hover:text-emerald-700">
    <span class="text-emerald-600 font-bold mt-0.5 shrink-0">✓</span>
    <span class="text-gray-700"><strong>Fijación de CO₂</strong> — ciclo de Calvin en el estroma del cloroplasto</span>
  </li>
</ul>

<details class="mb-4 rounded-lg border border-stone-200 overflow-hidden">
  <summary class="font-semibold text-gray-800 cursor-pointer px-4 py-3 transition hover:text-emerald-700 list-none flex items-center justify-between">
    <span>Fase luminosa vs fase oscura</span>
    <span class="text-gray-400">▼</span>
  </summary>
  <div class="px-4 py-3 text-gray-700 text-sm space-y-2">
    <p><strong>Fase luminosa</strong> — dependiente de luz, ocurre en los tilacoides. Produce ATP y NADPH.</p>
    <p><strong>Fase oscura</strong> — ciclo de Calvin, independiente de luz, ocurre en el estroma. Fija CO₂ usando ATP y NADPH.</p>
  </div>
</details>

<div class="rounded-xl p-5 border border-amber-200 shadow-sm">
  <p class="text-3xl font-extrabold text-amber-800">~6%</p>
  <p class="text-sm font-medium text-amber-600">Eficiencia máxima de conversión solar</p>
  <div class="mt-2 h-1.5 bg-gray-200 rounded-full">
    <div class="h-1.5 bg-emerald-500 rounded-full" style="width:6%"></div>
  </div>
</div>
PROMPT;

    /**
     * Etiqueta contenido educativo con HTML semántico usando IA.
     *
     * @param  string      $originalBody    Contenido plano original a etiquetar.
     * @param  string      $sectionTitle    Título de la sección/diapositiva.
     * @param  string      $gradeName       Nombre del grado (ej. "1er Grado").
     * @param  string      $subjectName     Nombre de la asignatura.
     * @param  callable    $aiCallback      Función que recibe (systemPrompt, userPrompt, overrides) y retorna array{success: bool, content: ?string, error: ?string}.
     * @param  array|null  $activityContext Contexto opcional de la actividad: ['topic' => string, 'teaching' => string, 'learning' => string, 'description' => string].
     * @return array{success: bool, html: ?string, error: ?string}
     */
    public function tag(
        string $originalBody,
        string $sectionTitle,
        string $gradeName,
        string $subjectName,
        callable $aiCallback,
        ?array $activityContext = null,
    ): array {
        $activityInfo = '';

        if ($activityContext) {
            $parts = array_filter([
                !empty($activityContext['topic']) ? "**Tema generador:** {$activityContext['topic']}" : null,
                !empty($activityContext['teaching']) ? "**Enseñanza:** {$activityContext['teaching']}" : null,
                !empty($activityContext['description']) ? "**Actividad evaluativa:** {$activityContext['description']}" : null,
            ]);
            if ($parts) {
                $activityInfo = "\n### Contexto de la actividad\n" . implode("\n", $parts) . "\n";
            }
        }

        $userPrompt = <<<PROMPT
### Contexto pedagógico
- **Grado:** {$gradeName}
- **Asignatura:** {$subjectName}
- **Título de la sección:** {$sectionTitle}
{$activityInfo}
### Contenido original a etiquetar

{$originalBody}

Transforma este contenido en HTML semántico ENRIQUECIDO con Tailwind CSS, pensando en un estudiante que va a aprender con este material. Usa highlight box para el concepto central, lista con viñetas de texto (✓) para enumeraciones, stat card para datos numéricos (progress bar SOLO si el valor es un porcentaje real 0–100%; para números absolutos como tiempo, unidades, hectáreas, usa stat card SIN barra), acordeón para info expandible, y tipografía variada (color de acento en título, subrayado decorativo).

⚠️  PRIORIDAD ABSOLUTA — PRESERVA EL TEXTO ORIGINAL:
El contenido es material DIRIGIDO AL ESTUDIANTE para su proceso de enseñanza-aprendizaje.
NO añadas NADA que no esté textualmente en el original: no desarrolles conceptos, no agregues
ejemplos ni aclaraciones, no parafrasees. Usa EXACTAMENTE las palabras del original organizadas
visualmente. El valor educativo está en el texto del profesor, no en inferencias del asistente.

Si se proporcionó "Contexto de la actividad", puedes usarlo ÚNICAMENTE como referencia
de contexto temático general, pero sin trasplantar texto de ese contexto al HTML generado.

IMPORTANTE: NO generes envoltorio/card raíz con fondo, borde o sombra — el contenido se inserta dentro de una plantilla que ya tiene su contenedor visual externo. NO uses SVG ni iconos decorativos (usa texto: ✓, •, —). CRUCIAL: NO uses NINGÚN fondo de color — solo texto, bordes y sombras. Evita bg-emerald-50, bg-amber-50, bg-sky-50, bg-stone-50, bg-gradient-to-r, bg-gradient-to-br y cualquier bg-*.
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

            if (empty($html)) {
                return [
                    'success' => false,
                    'html'    => null,
                    'error'   => 'El contenido generado está vacío tras la limpieza.',
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
}
