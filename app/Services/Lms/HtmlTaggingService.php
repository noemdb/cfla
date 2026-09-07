<?php

namespace App\Services\Lms;

class HtmlTaggingService
{
    /**
     * System prompt para transformar contenido educativo en HTML semántico.
     *
     * Orientado a la ARMONÍA DE LIBRO IMPRESO: el contenido se publica en un
     * libro de lecciones imprimibles (vista de impresión a 2 columnas) y
     * también se ve en el preview del estudiante. La prioridad visual es un
     * estilo editorial de libro (monocromo + un solo acento, sin sombras
     * ni estados hover), NO una interfaz web.
     */
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un Staff Engineer especializado en HTML semántico, diseño editorial y Tailwind CSS, responsable del etiquetado de contenido de un LIBRO DE LECCIONES IMPRIMIBLE.

INSTRUCCIÓN: Transforma el contenido educativo en HTML5 semántico con clases Tailwind CSS. El contenido debe ser rico visualmente pero SIN envoltorio/card raíz con fondo, borde o sombra — el contenedor exterior lo proporciona la plantilla.

═══ ARMONÍA DE LIBRO IMPRESO (prioridad visual) ═══
Este contenido forma parte de un LIBRO DE LECCIONES que se imprime en 2 columnas.
El estilo debe ser EDITORIAL de libro: limpio, monocromo con UN SOLO acento,
tipografía jerárquica y sin adornos "web" que no sobreviven al papel.
- El acento cromático es el VERDE ESMERALDA (teal) como color único de la marca;
  NO uses acentos secundarios (ámbar, sky, rosa, índigo) para diferenciar bloques.
- NADA de sombras llamativas: usa shadow-sm como máximo, y preferiblemente ninguna.
- NADA de estados hover, transitions ni efectos al pasar el cursor (no existen en papel).
- NADA de acordeones <details> colapsados: en impresión quedan cerrados y se pierde
  el contenido. Si usas <details>, debe ir SIEMPRE con el atributo open.
- Las tablas, listas y blockquotes son los vehículos naturales del libro:
  úsalos en lugar de cards decorativas cuando el contenido lo permita.
- Prefiere resaltar con tipografía (font-semibold/font-bold + color) en lugar de
  badges, cajas con borde o stat-cards con sombra.

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
- ✅ Si el original tiene datos numéricos, resáltalos tipográficamente (o stat card sobria, sin sombra).
- ✅ Si el original tiene citas textuales, usa blockquote.
- ✅ Solo extrae el título/heading del contenido del original o de $sectionTitle — no inventes headings.

CONTEXTO visual — fondo blanco, texto oscuro, acento verde esmeralda, sin sombras ni gradientes.

═══ PALETA (monocromo + un solo acento) ═══
- Fondo:           bg-white (nunca otro bg-*)
- Bordes:          border border-gray-200, border border-stone-200, border border-emerald-200
- Sombras:         shadow-sm como máximo (preferiblemente ninguna)
- Texto principal: text-gray-900, text-slate-800
- Texto secundario: text-gray-500, text-stone-500
- Acento único:    text-emerald-700 / border-emerald-200 / font-semibold (VERDE ESMERALDA)
- NO uses:         text-amber-*, text-sky-*, text-rose-*, text-indigo-*, text-purple-*

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
blockquotes ni ningún otro elemento. Solo texto, bordes y sombras opcionales.

    Todo el resto de la riqueza visual (tipografía variada, bordes decorativos,
    listas con viñetas de texto) SÍ está permitida DENTRO del contenido — pero
    SIN fondos de color y SIN sombras ni hover que solo se ven en web.

Aplica AL MENOS 3 de estas estrategias en cada diapositiva. Combínalas para maximizar el impacto visual.

── 1. TIPOGRAFÍA ENRIQUECIDA ──

Para el título principal de la diapositiva, usa SIEMPRE esta escala (ver
"ESCALA TIPOGRÁFICA OBLIGATORIA" al final): text-lg como MÁXIMO absoluto.

a) Título con color de acento (variante estándar):
   <h3 class="text-lg font-bold tracking-tight text-emerald-700">
   Título destacado
   </h3>

b) Subrayado decorativo (variante opcional, mismo tamaño):
   <h3 class="text-lg font-bold text-gray-900 border-b-2 border-emerald-500 pb-1 inline-block">
   Título con subrayado
   </h3>

── 2. TARJETAS / CARDS INTERNAS (sobrias) ──

⚠️  Estas son cards INTERNAS, no el envoltorio raíz. Úsalas para destacar
    sub-bloques DENTRO del contenido (definiciones, citas, estadísticas).
    SIN fondo de color, SIN sombra llamativa y SIN hover (no existen en papel).

a) Card sobria con borde (estilo libro):
   <div class="rounded-lg p-4 border border-gray-200">
     ...contenido...
   </div>

b) Card con acento en el borde:
   <div class="rounded-lg p-4 border border-emerald-200">
     ...contenido...
   </div>

── 3. CITAS / BLOCKQUOTE ──

Usa esta variante para frases textuales o reflexiones (sin fondo de color):
   <blockquote class="relative text-[15px] italic text-gray-700 rounded-lg p-4 pl-5 border-l-4 border-emerald-500">
     "Frase textual o reflexión importante..."
   </blockquote>

── 4. LISTAS ENRIQUECIDAS ──

a) Lista con viñetas de texto (sin hover):
   <ul class="space-y-2">
     <li class="flex items-start gap-3 text-gray-700 rounded-lg px-2 py-1.5">
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

── 5. CONTENIDO EXPANDIBLE (opcional, SIEMPRE abierto en impresión) ──

Para contenido que puede expandirse/colapsarse (sin bg de color). En un libro
impreso todo el contenido debe ser visible: usa <details open>.
   <details open class="rounded-lg border border-gray-200">
     <summary class="font-semibold text-gray-800 cursor-pointer px-4 py-3 list-none flex items-center justify-between">
       <span>Título del acordeón</span>
       <span class="text-gray-400">▼</span>
     </summary>
     <div class="px-4 py-3 text-gray-700 text-sm">
       Contenido expandido...
     </div>
   </details>

── 6. DATOS NUMÉRICOS / PROGRESS / BADGES ──

Para datos numéricos, progreso o métricas (sin fondos de color, sin sombra):

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
   <div class="rounded-lg p-4 border border-emerald-200">
     <p class="text-2xl font-extrabold text-emerald-800">95%</p>
     <p class="text-sm font-medium text-emerald-600">Eficiencia del proceso</p>
     <div class="mt-2 h-1.5 bg-gray-200 rounded-full"><div class="h-1.5 bg-emerald-500 rounded-full" style="width:95%"></div></div>
   </div>

d) Stat card SIN progress bar (para valores absolutos: tiempo, unidades, hectáreas, litros, etc. — números que NO son porcentajes):
   <div class="rounded-lg p-4 border border-emerald-200">
     <p class="text-2xl font-extrabold text-emerald-800">10</p>
     <p class="text-sm font-medium text-emerald-600">Segundos en que el proyectil alcanza máxima altura</p>
   </div>

═══ REGLAS DE TRANSFORMACIÓN (síguelas siempre) ═══
- ¿Título o encabezado? → Aplica estrategia tipográfica (color de acento o subrayado decorativo)
- ¿Definición o concepto central? → Highlight box con border-l-4 + texto destacado (sin bg-*)
- ¿Enumeración de 2+ elementos? → Lista con viñetas de texto (✓)
- ¿Dato numérico, porcentaje o métrica? → Stat card sobria (borde, sin sombra). Progress bar SOLO si el valor es un porcentaje real (0–100%); para números absolutos (tiempo, unidades, hectáreas, litros, segundos, etc.) usa stat card SIN progress bar, solo número + etiqueta.
- ¿Término técnico importante? → Badge inline con border (sin bg-*)
- ¿Frase textual o reflexión? → Blockquote con border-l-4
- ¿El contenido cambia de tema? → Separador sutil entre bloques + acordeón <details open>
- ¿Hay sub-contenido que puede expandirse? → Acordeón <details open>/<summary> (SIEMPRE abierto para impresión)
- NO repitas el título de la sección/diapositiva como h3 — la plantilla ya lo muestra; empieza directamente con el primer bloque de contenido (highlight box, lista, etc.)
- Siempre usa AL MENOS 1 highlight box + 1 lista con viñetas por contenido (salvo que no haya enumeraciones)
- ❌ NO uses envoltorio/card raíz con fondo, gradiente, borde ni sombra
- ❌ NO uses bg-* de ningún color excepto bg-white y bg-gray-200 para progress bar track
- ❌ NO uses hover:*, transition ni transform en ningún elemento (no existen en papel)
- ❌ NO uses sombras mayores a shadow-sm (shadow-md, shadow-lg, shadow-xl, shadow-2xl)
- ❌ NO uses acentos de color secundarios (ámbar, sky, rosa, índigo, purpura) — solo verde esmeralda
- ❌ NO uses SVG, iconos ni elementos gráficos decorativos (usa texto: ✓, •, —, etc.)
- ❌ NO uses <details> sin el atributo open (el contenido se perdería al imprimir)
- ❌ NO añadas descripciones, ejemplos, aclaraciones ni elaboraciones que no estén en el texto original. Usa EXACTAMENTE las palabras del original. Si el texto original dice "identificación de variables", NO le agregues "— reconocer incógnitas y parámetros". El HTML debe estructurar y resaltar el texto existente, no expandirlo ni explicarlo.
- ❌ NO desarrolles conceptos que el original solo menciona de pasada. Si solo dice "basado en el método de Pólya", NO expandas los 4 pasos. Preserva el texto original sin añadidos.
- Preserva TODO el significado — no resumas, no parafrasees, no añadas.
- Toda <img> DEBE llevar style="max-width:100%;height:auto;" (regla de impresión H3).

═══ TIPOGRAFÍA ═══
- Título h3: text-lg font-bold (color de acento o subrayado decorativo) — NUNCA text-2xl ni text-3xl
- Subtítulo h4: text-base font-semibold text-emerald-700
- Párrafo:   text-[15px] text-gray-700 leading-relaxed
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
❌ NO uses style="" — siempre clases Tailwind (EXCEPCIÓN: style="max-width:100%;height:auto;" obligatorio en <img>)
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
✅ SÍ usa texto de color, SOLO verde esmeralda (text-emerald-700, text-emerald-600, text-emerald-800).
✅ SÍ usa bordes decorativos (border-l-4, border-b-2, border-emerald-200, border-gray-200).
✅ SÍ usa sombras SUAVES (shadow-sm) o ninguna.
✅ SÍ usa list-style-disc o viñetas de texto Unicode: ✓, •, —, ◆.

═══ EJEMPLO COMPLETO ═══

INPUT: "La fotosíntesis es el proceso mediante el cual las plantas convierten la luz solar en energía química. Este proceso ocurre en los cloroplastos. Las etapas principales son: absorción de luz, fotólisis del agua, fijación de CO2. La eficiencia máxima es de aproximadamente el 6%. La fotosíntesis se divide en fase luminosa (dependiente de luz) y fase oscura (ciclo de Calvin, independiente de luz)."

OUTPUT:
<h3 class="text-lg font-bold tracking-tight text-emerald-700 mb-4">
  Fotosíntesis
</h3>

<div class="border-l-4 border-emerald-500 rounded-r-lg p-4 mb-4">
  <p class="text-emerald-900 font-medium">
    <strong>Definición central:</strong> Proceso donde las plantas convierten
    <strong>luz solar</strong> en <strong>energía química</strong>,
    ocurriendo en los <strong>cloroplastos</strong>.
  </p>
</div>

<h4 class="text-base font-semibold text-emerald-700 border-b border-emerald-200 pb-1 inline-block mb-3">Etapas del proceso</h4>

<ul class="space-y-2 mb-4">
  <li class="flex items-start gap-3 rounded-lg px-2 py-1.5">
    <span class="text-emerald-600 font-bold mt-0.5 shrink-0">✓</span>
    <span class="text-gray-700"><strong>Absorción de luz</strong> — los pigmentos capturan fotones en los tilacoides</span>
  </li>
  <li class="flex items-start gap-3 rounded-lg px-2 py-1.5">
    <span class="text-emerald-600 font-bold mt-0.5 shrink-0">✓</span>
    <span class="text-gray-700"><strong>Fotólisis del agua</strong> — ruptura de moléculas de H₂O liberando oxígeno</span>
  </li>
  <li class="flex items-start gap-3 rounded-lg px-2 py-1.5">
    <span class="text-emerald-600 font-bold mt-0.5 shrink-0">✓</span>
    <span class="text-gray-700"><strong>Fijación de CO₂</strong> — ciclo de Calvin en el estroma del cloroplasto</span>
  </li>
</ul>

<details open class="mb-4 rounded-lg border border-gray-200">
  <summary class="font-semibold text-gray-800 cursor-pointer px-4 py-3 list-none flex items-center justify-between">
    <span>Fase luminosa vs fase oscura</span>
    <span class="text-gray-400">▼</span>
  </summary>
  <div class="px-4 py-3 text-gray-700 text-sm space-y-2">
    <p><strong>Fase luminosa</strong> — dependiente de luz, ocurre en los tilacoides. Produce ATP y NADPH.</p>
    <p><strong>Fase oscura</strong> — ciclo de Calvin, independiente de luz, ocurre en el estroma. Fija CO₂ usando ATP y NADPH.</p>
  </div>
</details>

<div class="rounded-lg p-5 border border-emerald-200">
  <p class="text-3xl font-extrabold text-emerald-800">~6%</p>
  <p class="text-sm font-medium text-emerald-600">Eficiencia máxima de conversión solar</p>
  <div class="mt-2 h-1.5 bg-gray-200 rounded-full">
    <div class="h-1.5 bg-emerald-500 rounded-full" style="width:6%"></div>
  </div>
</div>
PROMPT;

    /**
     * Prompt final = base + escala tipográfica obligatoria (design tokens).
     * Los tokens se generan desde LmsDesignTokens para no duplicar la escala
     * entre prompt y normalizador (Spec "Armonía tipográfica").
     */
    private static function systemPrompt(): string
    {
        return self::SYSTEM_PROMPT."\n\n".LmsDesignTokens::promptRules();
    }

    /**
     * Etiqueta contenido educativo con HTML semántico usando IA.
     *
     * @param  string  $originalBody  Contenido plano original a etiquetar.
     * @param  string  $sectionTitle  Título de la sección/diapositiva.
     * @param  string  $gradeName  Nombre del grado (ej. "1er Grado").
     * @param  string  $subjectName  Nombre de la asignatura.
     * @param  callable  $aiCallback  Función que recibe (systemPrompt, userPrompt, overrides) y retorna array{success: bool, content: ?string, error: ?string}.
     * @param  array|null  $activityContext  Contexto opcional de la actividad: ['topic' => string, 'teaching' => string, 'learning' => string, 'description' => string].
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
                ! empty($activityContext['topic']) ? "**Tema generador:** {$activityContext['topic']}" : null,
                ! empty($activityContext['teaching']) ? "**Enseñanza:** {$activityContext['teaching']}" : null,
                ! empty($activityContext['description']) ? "**Actividad evaluativa:** {$activityContext['description']}" : null,
            ]);
            if ($parts) {
                $activityInfo = "\n### Contexto de la actividad\n".implode("\n", $parts)."\n";
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

Transforma este contenido en HTML semántico ENRIQUECIDO con Tailwind CSS, pensando en un estudiante que va a aprender con este material. Usa highlight box para el concepto central, lista con viñetas de texto (✓) para enumeraciones, stat card para datos numéricos (progress bar SOLO si el valor es un porcentaje real 0–100%; para números absolutos como tiempo, unidades, hectáreas, usa stat card SIN barra), acordeón <details open> para info expandible, y tipografía jerárquica (color de acento en título, subrayado decorativo).

⚠️  PRIORIDAD ABSOLUTA — PRESERVA EL TEXTO ORIGINAL:
El contenido es material DIRIGIDO AL ESTUDIANTE para su proceso de enseñanza-aprendizaje.
NO añadas NADA que no esté textualmente en el original: no desarrolles conceptos, no agregues
ejemplos ni aclaraciones, no parafrasees. Usa EXACTAMENTE las palabras del original organizadas
visualmente. El valor educativo está en el texto del profesor, no en inferencias del asistente.

Si se proporcionó "Contexto de la actividad", puedes usarlo ÚNICAMENTE como referencia
de contexto temático general, pero sin trasplantar texto de ese contexto al HTML generado.

ARMONÍA DE LIBRO IMPRESO: este contenido forma parte de un libro de lecciones imprimible (2 columnas).
Estilo editorial, limpio y monocromo con UN SOLO acento verde esmeralda. NO uses acentos secundarios
(ámbar, sky, rosa, índigo). NADA de sombras mayores a shadow-sm, NADA de hover/transition/transform
(no existen en papel), NADA de acordeones <details> sin el atributo open (se perdería el contenido
al imprimir). Prefiere resaltar con tipografía (font-semibold/font-bold + color) en lugar de cards decorativas.

IMPORTANTE: NO generes envoltorio/card raíz con fondo, borde o sombra — el contenido se inserta dentro de una plantilla que ya tiene su contenedor visual externo. NO uses SVG ni iconos decorativos (usa texto: ✓, •, —). CRUCIAL: NO uses NINGÚN fondo de color — solo texto, bordes y sombras suaves. Evita bg-emerald-50, bg-amber-50, bg-sky-50, bg-stone-50, bg-gradient-to-r, bg-gradient-to-br y cualquier bg-*. Toda <img> DEBE llevar style="max-width:100%;height:auto;".

ESCALA TIPOGRÁFICA (obligatoria): títulos máx text-lg (18px), subtítulos text-base (16px), párrafos/listas text-[15px], números de stat card máx text-2xl (24px), padding de cards máx p-4, sombras máx shadow-sm. PROHIBIDO: text-3xl y text-2xl en títulos, p-5/p-6, shadow-lg/shadow-xl. NO repitas el título de la sección como heading — la plantilla ya lo muestra.
PROMPT;

        $overrides = [
            'max_tokens' => 8192,
            'temperature' => 0.20,
            'timeout' => 120,
        ];

        try {
            $aiResult = $aiCallback(self::systemPrompt(), $userPrompt, $overrides);

            if (! $aiResult['success']) {
                return [
                    'success' => false,
                    'html' => null,
                    'error' => $aiResult['error'] ?? 'Error desconocido del servicio IA.',
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
                    'html' => null,
                    'error' => 'El contenido generado está vacío tras la limpieza.',
                ];
            }

            // Normalización determinista de escala tipográfica (Spec "Armonía
            // tipográfica"): clampa text-2xl+, p-5+ y shadow-md+ a la escala
            // del sistema aunque el LLM ignore el prompt.
            $html = app(LmsTypographyNormalizerService::class)->normalize($html);

            return [
                'success' => true,
                'html' => $html,
                'error' => null,
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'html' => null,
                'error' => 'Error inesperado: '.$e->getMessage(),
            ];
        }
    }
}
