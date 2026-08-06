# Regenerar / corregir un bloque de contenido del wizard (Paso 2) con IA

> **Propósito**: Añadir un botón por bloque de contenido en el editor de diapositivas (Step 2) que, usando los servicios IA existentes, **regenera/corrige el contenido de ese bloque concreto** según su tipo (TEXT / MATH / IMAGE-SVG / MERMAID / HTML), **ratificando las mismas condiciones del prompt original** de ese tipo.
>
> **Componentes afectados**: `app/Livewire/Profesor/Lms/LessonWizard.php` + `resources/views/livewire/profesor/lms/_wizard-step-2.blade.php`.
>
> **Ubicación**: este blueprint vive en `blueprint/lesson/`, junto a `prompt-svg-educativo-v3.md`, `Spec refactor lessonwizard.md`, `Spec Staff Engineer ...`, etc.

---

## 1. Contexto y motivación

El editor de diapositivas (Step 2) muestra, por cada bloque de contenido de la diapositiva activa, una cabecera con acciones: **previsualizar** (lupa) y **eliminar**. La generación/enriquecimiento de contenido se dispara desde la **barra de herramientas** superior (botones globales).

| Botón toolbar (actual) | Método | Tipo que crea/muta |
|---|---|---|
| Generar texto | `generateSlideText()` | `TEXT` (markdown) |
| Generar imagen | `generateSlideImage()` | `IMAGE` (SVG en `<figure>`) |
| Ilustración | `generateSectionIllustration()` | `IMAGE` |
| Diagrama (comentado) | `generateSlideDiagram()` | `HTML` (Mermaid en `<div class="mermaid">`) |
| Etiquetar HTML | `generateSlideHtmlTags()` | `HTML` (semántico) |
| Matemáticas | `generateSlideMath()` | `MATH` (LaTeX en `#math-block`) |

**Limitación actual detectada**: los generadores que *mutan* (no insertan) — `generateSlideHtmlTags()` y `generateSlideMath()` — **siempre operan sobre `contents[0]`**, no sobre un índice arbitrario. Si el bloque deseado es el segundo (`contents[1]`), no hay manera de regenerar/enriquecer **solo ese bloque** desde la UI. El docente solo puede regenerar "todo" (sobre el bloque 0) o nada.

**Carencia que resuelve este blueprint**: un **botón "regenerar/corregir con IA"** anclado a cada bloque concreto, que re-usa el procedimiento IA correcto **según el tipo real del bloque** y que, en lugar de partir de cero, **analiza el contenido ya existente** (conserva la intención del docente) **y ratifica las condiciones de calidad del prompt original** de ese tipo.

---

## 2. Objetivos

1. **UI**: botón "regenerar/corregir con IA" al lado de la lupa y el borrar, en la cabecera de cada bloque (dentro del `<div class="flex items-center gap-1 shrink-0">`, líneas ~284–298 de `_wizard-step-2.blade.php`).
2. **Puerta de entrada**: método Livewire `regenerateWizardContent(int $sectionIndex, int $contentIndex)` que despacha al procedimiento correcto según el `type` real del bloque objetivo.
3. **Prompt de corrección**: system prompt polimórfico que:
   - inspecciona (analiza) el contenido actual del bloque,
   - reproduce 1:1 las reglas/condiciones del prompt original de ese tipo (calidad, estructura, restricciones de salida, prohibiciones),
   - devuelve contenido corregido/mantenido en el **mismo formato** (markdown, HTML, SVG, Mermaid, LaTeX), sobrescribiendo el `body` in-place.
4. **No duplicar**: reutilizar helpers existentes (`extractMermaidCodeFromRaw`, `postProcessMermaid`, `diagramCorrectionBlock`, `callMermaidModel`, `LmsHtmlSanitizerService::sanitize`, `LmsContentRendererService`, `HtmlTaggingService`, `askWithCompaction`).
5. **Respetar invariantes**: `publishedGuard()`; `$this->saved = false`; máximo 2 bloques/slide (como `generateSlideText`); feedback `generatingSection` / `generationError`.

---

## 3. Mapa de decisiones: tipo real → procedimiento de corrección

El `type` del array de contenido a veces no basta para distinguir subtipos dentro de `HTML`. Clasificación (reutiliza la lógica del renderer `LmsContentRendererService`):

```php
// pseudo-código — classifyContent(array $content): string
tipo = $content['type'];                 // TEXT | MATH | IMAGE | HTML
if tipo === 'MATH'  -> 'MATH'             // LaTeX en #math-block
if tipo === 'IMAGE' -> 'IMAGE'            // SVG dentro de <figure>
if tipo === 'HTML':
    if preg_match('/class="[^"]*\bmermaid\b[^"]*"/', body)
      || str_contains(body, 'data-mermaid-code=')
      || preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', body)
        -> 'MERMAID'
    if preg_match('/<svg[\s>]/i', body) -> 'SVG_IMG'   // SVG directo
    else                               -> 'HTML_SEMANTIC'
if tipo === 'TEXT':
    // detección defensiva opcional: si contiene \(...\) o $$...$$ puede derivar a MATH
    -> 'TEXT'
```

| Clase | Procedimiento IA a re-usar | Salida que debe retornar la corrección |
|---|---|---|
| `TEXT` | Prompt de `generateSlideText` | Markdown limpio (rango de longitud original; ver red de seguridad de 100 pal.) |
| `MATH` | Prompt de `generateSlideMath` (cadena Math Qwen/DeepSeek, temp 0.05) | HTML válido envuelto en `<div id="math-block">` con LaTeX `\(...\)`/`$$...$$` |
| `IMAGE` | Prompt SVG de `generateSlideImage` (cadena imagen) | Solo `<svg>...</svg>` (re-aplicar extracción + limpieza existente) |
| `MERMAID` | Prompt de `generateSlideDiagram` + `validateMermaidDiagram` + `diagramCorrectionBlock` + `postProcessMermaid` | Mermaid válido (con retry si la validación falla) + envolver en `<div class="mermaid">` |
| `HTML_SEMANTIC` | `HtmlTaggingService::tag()` (cadena estándar) | HTML5 semántico saneado |

**Detalle clave**: la corrección debe **volver a pasar el contenido por el mismo pipeline de post-procesado** (`sanitize`, extracción de SVG/Mermaid) que el generador original, para no degradar el formato.

---

## 4. Diseño de la UI (botón)

Se inserta un `wire:click` **entre la lupa y el eliminar**, dentro del contenedor de acciones del bloque:

```blade
<div class="flex items-center gap-1 shrink-0">
    {{-- Preview (lupa) --}}
    <button @click.prevent="previewIndex = {{ $cIdx }}" class="p-1.5 rounded-lg transition-all
            text-gray-400 dark:text-slate-600 hover:text-gray-700 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-slate-600/50">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
    </button>

    {{-- REGENERAR / CORREGIR con IA  (NUEVO) --}}
    <button wire:click="regenerateWizardContent({{ $currentSlideIndex }}, {{ $cIdx }})"
            wire:loading.attr="disabled"
            wire:target="regenerateWizardContent"
            class="p-1.5 rounded-lg transition-all
                   text-gray-400 dark:text-slate-600 hover:text-emerald-500 hover:bg-emerald-500/10"
            title="Regenerar / corregir este bloque con IA"
            @disabled($isPublished)">
        <span wire:loading wire:target="regenerateWizardContent" class="inline-flex">
            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
        </span>
        <span wire:loading.remove wire:target="regenerateWizardContent" class="inline-flex">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </span>
    </button>

    @if($blockCount > 1)
        <button wire:click="removeWizardContent({{ $currentSlideIndex }}, {{ $cIdx }})"
                wire:confirm="Eliminar este bloque de contenido?"
                class="p-1.5 rounded-lg transition-all
                       text-gray-400 dark:text-slate-600 hover:text-red-400 hover:bg-red-500/10" @disabled($isPublished)">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    @endif
</div>
```

**Notas de UI**:
- Icono de "refrescar/recarga" (dos flechas) — distinto al de la lupa y al de eliminar.
- `wire:loading` muestra un spinner mientras la cadena IA responde (la generación puede tardar varios segundos).
- Se deshabilita (`@disabled($isPublished)`) cuando la lección está publicada, igual que el borrar.

---

## 5. System prompt de corrección (polimórfico por tipo)

Este es el **system prompt** que el usuario solicita: analiza el contenido existente del bloque y **ratifica las condiciones del prompt original** de ese tipo en concreto. Se presenta en dos capas:

- **Capa genérica** (instrucciones transversales, indistinguibles del resto de prompts en español / formal / best-seller).
- **Capa por tipo** (reproduce 1:1 las reglas del prompt original de cada tipo).

### 5.1 Capa genérica (se antepone siempre)

```text
Tarea: CORREGIR / REGENERAR el contenido de un bloque educativo sin perder su intención
pedagógica y cumpliendo estrictamente el formato y las normas de calidad del tipo de
contenido indicado.

Reglas transversales (obligatorias en TODAS las respuestas):
1. Idioma: responde SIEMPRE en español, con registro formal y tono de manual de calidad
   editorial.
2. NO añadas explicaciones, comentarios, notas, marcas de proceso, bloques "```...```"
   ni texto fuera del formato de salida exigido. Responde únicamente con el contenido
   solicitado.
3. Preserva el significado, los conceptos clave, los datos y la estructura pedagógica
   del contenido que se te entrega; corrige solo errores, incoherencias, repeticiones,
   o mejoras de claridad y diagramación SIN inventar hechos nuevos ni contradecir el
   contexto de la actividad.
4. No rompas el flujo semántico de la sección: respeta títulos, jerarquías y longitud
   razonable equivalente al contenido original (+/- 30 %).
5. Si el contenido original está mal, vácialo o incompleto, reescríbelo desde la lógica
   pedagógica de su título y contexto, manteniendo el formato de salida.
6. Entrega el resultado listo para almacenarse tal cual (sin mayúsculas sueltas, sin
   HTML escapado, sin caracteres corruptos tipo mojibake).
```

### 5.2 Capa por tipo — ratifica el prompt original

**Para `TEXT`** (ratifica las condiciones del prompt de `generateSlideText`):
- Markdown limpio y estructurado de la sección, en español formal.
- Estructura: introducción → desarrollo (min. 100 palabras/bloque, red de seguridad del wizard) → cierre.
- Sin promesas vacías ni relleno; calidad de manual best-seller.
- Contexto: grado, asignatura, competencias/indicadores ("referentes") si se dispone de ellos.

**Para `MATH`** (ratifica `generateSlideMath` — cadena Math Qwen/DeepSeek, temp 0.05):
- Detección total de cada expresión matemática del texto original.
- Conversión a LaTeX: inline `\(...\)`, bloque `$$...$$`.
- Estructura HTML obligatoria: todo dentro de `<div id="math-block">`, párrafos en `<p>`.
- Prohibido texto fuera del HTML. LaTeX sintácticamente válido (`\frac`, `\sqrt`, `\pm`, `\int_{a}^{b}`, `\left( \right)`, matrices, etc.).
- Texto sin matemáticas: preservarlo EXACTAMENTE IGUAL dentro de los `<p>`.

**Para `IMAGE` / `SVG_IMG`** (ratifica `generateSlideImage` + `prompt-svg-educativo-v3.md`):
- Solo `<svg>...</svg>` válido y autocontenido, sin JS/CSS externo, sin CDN, sin markdown.
- Fondo blanco o `#f8f9fa`, cajas pastel con `rx="8"`, texto oscuro legible (14px+/16px+).
- Espaciado amplio (nada solapado), `viewBox` proporcionado.
- Jerarquía visual clara; flechas/líneas simples; sin gradientes ni sombras excesivas ni 3D.
- Semántica y accesibilidad WCAG 2.1 AA (por `prompt-svg-educativo-v3.md`).

**Para `MERMAID`** (ratifica `generateSlideDiagram` + `validateMermaidDiagram`):
- Código Mermaid válido (flowchart/graph/mindmap/sequenceDiagram/classDiagram/gantt/pie/stateDiagram/erDiagram/journey/gitgraph/timeline).
- Etiquetas de nodo ≤ 30 caracteres; máx. 12 nodos; single-link simple; `graph TD` por defecto (post-proceso).
- Se aplica el `postProcessMermaid()` y el `wrapping` en `<div class="mermaid">` tal y como hace el generador original.

**Para `HTML_SEMANTIC`** (ratifica `HtmlTaggingService::tag`, `generateSlideHtmlTags`):
- HTML5 semántico (`<section>`, `<p>`, `<strong>`, listas, tablas solo si procede).
- Sin `<div>` planos anidados sin significado; sin estilos inline caóticos; accesible.
- Preservar el contenido textual original (el servicio etiqueta el body plano de partida).

---

## 6. Implementación en el componente

### 6.1 Nuevo método público Livewire

```php
public function regenerateWizardContent(int $sectionIndex, int $contentIndex): void
{
    if ($this->publishedGuard()) { return; }
    if (! isset($this->wizardSections[$sectionIndex]['contents'][$contentIndex])) { return; }

    $this->saved = false;
    $this->generatingSection = $sectionIndex;
    $this->generationError = null;

    $content = &$this->wizardSections[$sectionIndex]['contents'][$contentIndex];
    $kind = $this->classifyContent($content);   // TEXT | MATH | IMAGE | MERMAID | HTML_SEMANTIC

    try {
        match ($kind) {
            'TEXT'           => $this->regenerateTextBlock($sectionIndex, $contentIndex),
            'MATH'           => $this->regenerateMathBlock($sectionIndex, $contentIndex),
            'IMAGE'          => $this->regenerateImageBlock($sectionIndex, $contentIndex),
            'MERMAID'        => $this->regenerateMermaidBlock($sectionIndex, $contentIndex),
            'HTML_SEMANTIC'  => $this->regenerateHtmlBlock($sectionIndex, $contentIndex),
            default          => null,
        };
        $this->dispatch('content-regenerated', contentType: $kind);
    } catch (\Throwable $e) {
        $this->generationError = $e->getMessage();
        $this->notification()->error('Error inesperado', $e->getMessage());
    } finally {
        $this->generatingSection = null;
    }
}
```

### 6.2 Refactor de los generadores para admitir índice objetivo

En lugar de duplicar lógica, los generadores mutadores se generalizan para recibir el índice:

- `generateSlideHtmlTags()` / `generateSlideMath()` pasan a operar sobre `contents[$contentIndex]` (hoy fijan `contents[0]`). Default para la toolbar: índice `0`.
- Se extrae el cuerpo del prompt original + user-prompt a métodos privados reutilizables:
  - `private function textCorrectionSystemPrompt(): string`
  - `private function mathCorrectionSystemPrompt(): string`
  - `private function imageCorrectionSystemPrompt(): string`
  - `private function mermaidCorrectionSystemPrompt(): string`
  - (el de HTML_SEMANTIC lo aporta `HtmlTaggingService`).
- Cada método de regeneración construye el `$userPrompt` con: (a) el `$content['body']` actual, (b) `$content['title']`, (c) contexto (grado/asignatura/actividad), y (d) las instrucciones "ratifican las condiciones del prompt original".

### 6.3 Re-aplicar el pipeline de post-procesado

Cada `regenerate*Block` debe reutilizar el mismo post-procesado que el generador original:
- MATH → `LmsHtmlSanitizerService::sanitize()`.
- IMAGE → extracción `<svg>...</svg>`, limpieza doctype/xml.
- MERMAID → `extractMermaidCodeFromRaw` → `validateMermaidDiagram` → con `diagramCorrectionBlock` de retry → `postProcessMermaid` → wrapping `<div class="mermaid">`.

---

## 7. Flujo de trabajo (secuencia)

```
[Docente pedir]  clic en botón regenerar del bloque X
   ├─ events: regenerateWizardContent(currentSlideIndex, cIdx)
   ├─ classifyContent(bloque X)  → tipo real
   ├─ construye userPrompt (body actual + contexto + ratificación)
   ├─ askWithCompaction(systemPrompt tipo, userPrompt, {...}, ...
   ├─ post-procesa (sanitize / extraer svg / validar mermaid)
   ├─ sobrescribe body (y type si procede, e.g. TEXT->HTML en math) en bloques[cIdx]
   ├─ dispatch('content-regenerated') + notificación success/error
   └─ docente guarda (saveStep2) → persiste
```

---

## 8. Plan de fases (staff engineer)

### Fase 1 — Base seguro (sin IA)
- [ ] Backup de `LessonWizard.php` (patrón `.bak_*` ya usado en refactor previo) y de la vista step-2.
- [ ] Añadir `classifyContent()` privado y sus pruebas unitarias de clasificación (TEXT/MATH/IMAGE/MERMAID/HTML) con fixtures.
- [ ] Añadir el botón en la vista (UI) con `wire:loading` spinner y `@disabled($isPublished)`. Aceptación: renderiza sin errores, espaciado coherente, spinner visible mientras `regeneratingSection !== null`.

### Fase 2 — Refactor de generadores mutadores
- [ ] Generalizar `generateSlideHtmlTags()` y `generateSlideMath()` para recibir `contentIndex` (default 0 por compatibilidad toolbar).
- [ ] Regresión: `php8.2 artisan test --filter="generateSlideMath|generateSlideHtmlTags|LessonWizardCharacterizationTest"`.
- [ ] Confirmar que la toolbar sigue actuando sobre `contents[0]` y que la red de seguridad (test de caracterización) no se altera. **Nota**: el docstring de `LessonWizardCharacterizationTest` exige NO modificar sus tests durante refactor; sólo deberían seguir pasando.

### Fase 3 — Métodos de regeneración por tipo
- [ ] Implementar `regenerateTextBlock`, `regenerateMathBlock`, `regenerateImageBlock`, `regenerateMermaidBlock`, `regenerateHtmlBlock`.
- [ ] Centralizar los system prompts de corrección (sección 5) en métodos reutilizables.
- [ ] Re-aplicar el post-procesado de cada tipo.
- [ ] `$this->saved = false` y `generatingSection/generationError` correctos.

### Fase 4 — Pruebas y seguridad
- [ ] Tests de caracterización nuevos (sin borrar los existentes): uno por tipo, con respuestas IA *stubbed/vacías* para verificar que el fallo IA no corrompe el estado, y con éxito simulado para verificar la re-escritura in-place.
- [ ] Verificar `publishedGuard()` y límite de 2 bloques.
- [ ] Correr suite completa: `php8.2 artisan test tests/Feature/Lms`.

### Fase 5 — Documentación y limpieza
- [ ] Actualizar `docWizardLesson.md` (página de ayuda) explicando el nuevo botón.
- [ ] Consolidar/eliminar backups temporales antiguos (`.bak_utf8fix`, `.bak_promptrange`, nuevos `.bak_*`) una vez confirmado.

---

## 9. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Refactor de `generateSlideMath`/`generateSlideHtmlTags` rompe la toolbar | Medio | Default `contentIndex=0`; red de seguridad de caracterización; test de regresión |
| Modelo devuelve formato no parseable (SVG/Mermaid) | Medio | Reutilizar pipelines de post-procesado + `diagramCorrectionBlock` de retry |
| Sobrescritura in-place borra trabajo del docente si falla IA | Alto | Guardar `$previousBody`; en error, restaurar y notificar; NO tocar body hasta que la respuesta pase post-procesado |
| Latencia IA larga con botón blocado | Bajo | `wire:loading` spinner + deshabilitar botón |
| Test de caracterización sensible al refactor | Alto | No modificar tests existentes; añadir tests nuevos aislados; preservar firmas públicas usadas |

---

## 10. Criterios de aceptación

- [ ] El botón aparece en cada bloque (salvo el único bloque de una slide si `blockCount === 1`, como el eliminar — aunque el usuario lo pidió junto a la lupa, conviene decidir si aplica el mismo `@if($blockCount > 1)`).
- [ ] Al hacer clic, el bloque objetivo se regenera según su tipo real, manteniendo formato y ratificando el prompt original.
- [ ] Si la slide está publicada, el botón está deshabilitado.
- [ ] Si la respuesta IA es inválida, se restaura el contenido anterior y se muestra error sin perder datos.
- [ ] Todas las pruebas pasan bajo `php8.2`.
- [ ] La toolbar superior sigue funcionando sin cambios de comportamiento (operando sobre `contents[0]`).
