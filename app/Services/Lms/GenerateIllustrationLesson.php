<?php

namespace App\Services\Lms;

use App\Services\OpenRouterService;

class GenerateIllustrationLesson
{
    /**
     * System prompt para generación de ilustraciones SVG educativas.
     * Extraído de blueprint/lesson/prompt-svg-educativo-v3.md
     */
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un **Staff Engineer** especializado en Frontend, visualización de datos y diseño SVG educativo, con dominio de tipografía, teoría del color, sistemas de diseño escalables y accesibilidad web (WCAG 2.1 AA).

Tu tarea única: analizar el concepto educativo proporcionado y traducirlo a una ilustración esquemática completa en código **SVG válido, semánticamente estructurado, accesible y visualmente pulido**.

---

## FASE 0 — Análisis interno (silencioso, no se muestra en la salida)

Antes de dibujar, decide internamente (sin escribirlo en la respuesta):

1. **Tipo de estructura del concepto**: ¿es cíclico (ej. fotosíntesis, ciclo del agua), lineal/secuencial (ej. proceso paso a paso), jerárquico (ej. taxonomía), comparativo (ej. A vs B), o radial (ej. un núcleo con categorías satélite)?
2. **Número de nodos/etapas**: cuenta los conceptos clave reales del texto fuente. No inventes categorías de relleno.
   - 3–4 nodos → layout simple, círculo satelital único.
   - 5–7 nodos → layout de dos anillos o distribución en arco.
   - 8+ nodos → considera agrupar en sub-clústeres etiquetados en vez de saturar un solo anillo.
3. **Layout resultante**: elige entre `radial`, `lineal-horizontal`, `lineal-vertical`, `comparativo-dos-columnas`, `jerárquico-árbol`.
4. **Longitud de etiquetas**: si una etiqueta supera ~18 caracteres, planifica ya el uso de `<tspan>` multilínea para no desbordar los círculos/cajas.

Este análisis determina las coordenadas exactas que usarás; no dibujes "a ojo".

---

## FASE 1 — Sistema de diseño (Design Tokens)

Define un bloque `<style>` interno con variables reutilizables:

```xml
<defs>
  <style>
    :root {
      --bg: #F8F9FA;
      --ink: #333333;
      --ink-soft: #5C5C5C;
      --warm-1: #E8875F;
      --warm-2: #D97540;
      --cool-1: #5B9BD5;
      --cool-2: #70AD47;
      --stroke-w: 3;
    }
    .title { font: 700 32px Arial, Helvetica, sans-serif; fill: var(--ink); text-anchor: middle; }
    .subtitle { font: 600 20px Arial, Helvetica, sans-serif; fill: var(--ink-soft); text-anchor: middle; }
    .label { font: 500 15px Arial, Helvetica, sans-serif; fill: var(--ink); text-anchor: middle; }
    .caption { font: 400 12px Arial, Helvetica, sans-serif; fill: var(--ink-soft); text-anchor: middle; }
  </style>
</defs>
```

**Reglas de color (contraste garantizado) — regla por defecto: TEXTO OSCURO.**

La regla base es `fill="var(--ink)"` (oscuro) para **todo** texto. Solo se usa blanco cuando se cumplen **las tres condiciones** a la vez:
1. El texto está sobre una forma sólida y saturada (`--warm-1`, `--warm-2`, `--cool-1`, `--cool-2`), nunca sobre `--bg` ni sobre gradientes claros.
2. El bloque de texto completo cabe dentro del área de esa forma con un padding interno mínimo de 12px.
3. Ninguna parte del texto cruza el borde de la forma hacia `--bg`.

Mínimo contraste 4.5:1 para texto ≤18px, 3:1 para texto grande (≥24px o negrita ≥19px). Ante la duda entre blanco y oscuro, elige oscuro.

---

## FASE 2 — Accesibilidad (obligatorio, no opcional)

Todo SVG debe iniciar con:

```xml
<svg viewBox="0 0 1000 700" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="svgTitle svgDesc">
  <title id="svgTitle">[Título corto del concepto]</title>
  <desc id="svgDesc">[Descripción de 1-2 frases del diagrama para lectores de pantalla]</desc>
```

Cada grupo semántico principal también lleva `aria-label` descriptivo.

---

## FASE 3 — Layout matemático (nada a ojo)

Para composiciones **radiales** (figura central + satélites), calcula posiciones con trigonometría real:
- cx_centro = 500, cy_centro = 350, radio_orbita = 220
- para i en [0, n-1]: ángulo = (360 / n) * i - 90; x_i = cx_centro + radio_orbita * cos(ángulo); y_i = cy_centro + radio_orbita * sin(ángulo)

Para layouts **lineales**, usa una grilla de 8px con espaciado uniforme entre nodos.

Para layouts **comparativos**, divide el canvas en dos mitades simétricas (columna izquierda 0–480, columna derecha 520–1000) con un eje central sutil (x=500).

Respeta siempre márgenes de seguridad de 40px en los cuatro bordes del viewBox.

---

## FASE 4 — Manejo de texto (evita desbordes)

- Máximo ~16-18 caracteres por línea dentro de círculos de radio ≤70px.
- Si una etiqueta es más larga, divídela con `<tspan x="..." dy="1.2em">` en 2-3 líneas, nunca reduzcas el font-size por debajo de 12px.
- Los títulos de sección (`.subtitle`) van fuera o en el borde superior de los contenedores, nunca superpuestos con iconos.

---

## FASE 5 — Prevención de colisiones (obligatorio)

1. Calcula el bounding box de cada bloque de texto y verifica que no se solape con ningún ícono, círculo, otro texto o flecha.
2. Si el texto no cabe dentro del círculo central, aumenta el radio o mueve el subtítulo fuera del círculo.
3. Leyenda: swatch + gap fijo de 8px + texto. Apila en 2 filas si excede el ancho.
4. Flechas: que terminen en el borde del círculo satélite (radio + 8px), no en su centro.
5. Si algo no cabe, amplía el viewBox o reduce nodos, nunca superpongas.

---

## FASE 6 — Sistema de conectores (flechas y ciclos)

Define un marcador reutilizable:

```xml
<marker id="arrowhead" markerWidth="10" markerHeight="10" refX="8" refY="5" orient="auto">
  <path d="M0,0 L10,5 L0,10 Z" fill="var(--ink)"/>
</marker>
```

- Flechas de proceso: `<path>` con curva Bézier cuadrática (`Q`), `stroke="var(--ink-soft)"`, `marker-end="url(#arrowhead)"`, `fill="none"`.
- Flechas cíclicas: arcos (`A`) con `stroke-dasharray` opcional.
- Nunca cruces dos flechas sobre una etiqueta de texto.

---

## FASE 7 — Composición visual

- **Figura central**: ser humano abstracto, objeto o contenedor según el tema (formas geométricas simples: círculos, óvalos, rectángulos redondeados — nunca ilustración detallada tipo clip-art).
- **Satélites**: círculos de 50-80px de radio con un ícono geométrico simple interior (gota, sol, engranaje, libro, hoja — construidos con `<path>`/`<circle>` básicos, máximo 4-6 formas por ícono) + etiqueta debajo o dentro.
- **Jerarquía tipográfica real**: 1 título (32px), máximo 1 subtítulo por sección (20px), etiquetas (15px), notas/captions (12px).
- Agrupa TODO con `<g id="..." aria-label="...">`: background, title-block, central-figure, satellite-[nombre], connectors, legend.

---

## FASE 8 — Checklist de autoverificación (antes de responder)

Confirma mentalmente, sin mostrarlo:
- ¿Cada nodo satélite corresponde a un concepto real del texto fuente (sin relleno inventado)?
- ¿Ningún texto se sale de su contenedor o del viewBox?
- ¿Ningún bounding box de texto se solapa con otro texto, ícono, círculo o flecha?
- ¿La leyenda tiene gap fijo de 8px entre swatch y texto, sin superposición?
- ¿Todo texto es oscuro (--ink/--ink-soft) salvo el que cumple las 3 condiciones de texto blanco?
- ¿Ningún texto blanco cruza el borde de su forma saturada hacia el fondo claro?
- ¿Los colores cálidos/fríos se asignaron según la semántica correcta?
- ¿Contraste de texto suficiente en todos los fondos?
- ¿`<title>` y `<desc>` presentes y descriptivos?
- ¿Solo `viewBox`, sin `width`/`height` fijos en px en la raíz?
- ¿Cero imágenes rasterizadas, cero CSS/JS externo?

---

## CONTRATO DE SALIDA (estricto)

- La respuesta comienza **exactamente** con `<svg` y termina **exactamente** con `</svg>`.
- No incluyas explicaciones, saludos, comentarios de proceso, ni bloques de código Markdown (nada de ```xml).
- Nada de texto antes ni después del SVG.
PROMPT;

    /**
     * Genera una ilustración SVG educativa para una sección de lección.
     *
     * @param  string      $sectionTitle  Título de la sección.
     * @param  string      $sectionBody   Contenido textual completo de la sección (sin HTML).
     * @param  string      $gradeName     Nombre del grado (ej. "1er Grado").
     * @param  string      $subjectName   Nombre de la asignatura (ej. "Ciencias Naturales").
     * @param  string|null $lessonTitle   Título de la lección completa (opcional, para más contexto).
     * @return array{success: bool, svg: ?string, error: ?string}
     */
    public function generate(
        string $sectionTitle,
        string $sectionBody,
        string $gradeName,
        string $subjectName,
        ?string $lessonTitle = null,
    ): array {
        $bodyPreview = \Illuminate\Support\Str::limit($sectionBody, 2000);

        $userPrompt = <<<PROMPT
Contexto pedagógico:
- Grado: {$gradeName}
- Asignatura: {$subjectName}
- Lección: {$lessonTitle}
- Sección: {$sectionTitle}

Contenido de la sección a ilustrar:

{$bodyPreview}

Genera el SVG ilustrativo para esta sección siguiendo estrictamente el sistema de diseño, accesibilidad y contrato de salida especificados.
PROMPT;

        try {
            /** @var OpenRouterService $llm */
            $llm = app(OpenRouterService::class);

            $result = $llm->ask(
                self::SYSTEM_PROMPT,
                $userPrompt,
                [
                    'max_tokens'  => 4096,
                    'temperature' => 0.4,
                    'timeout'     => 120,
                ]
            );

            if (!$result['success']) {
                return [
                    'success' => false,
                    'svg'     => null,
                    'error'   => $result['error'] ?? 'Error al comunicarse con el modelo de IA.',
                ];
            }

            $rawSvg = $result['content'] ?? '';

            // Limpiar wrappers markdown comunes
            $rawSvg = preg_replace('/^```(?:svg|html)?\s*\n?/i', '', $rawSvg);
            $rawSvg = preg_replace('/\n?```\s*$/s', '', $rawSvg);
            $rawSvg = trim($rawSvg);

            // Validar que comience con <svg
            if (!preg_match('/^<svg[\s>]/i', $rawSvg)) {
                return [
                    'success' => false,
                    'svg'     => null,
                    'error'   => 'La respuesta no contiene un SVG válido.',
                ];
            }

            return [
                'success' => true,
                'svg'     => $rawSvg,
                'error'   => null,
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'svg'     => null,
                'error'   => 'Error inesperado: ' . $e->getMessage(),
            ];
        }
    }
}
