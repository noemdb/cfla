# Spec — Armonía tipográfica del contenido etiquetado (`generateSlideHtmlTags` / `HtmlTaggingService`)

> **Estado**: Propuesta (plan integral) · **Fecha**: 2026-08-08
> **Problema observado**: el tamaño de las fuentes del HTML etiquetado rompe la armonía visual con el resto de los contenidos de la lección.
> **Principio rector**: una sola escala tipográfica (design tokens) para TODO el contenido LMS; el prompt la exige y un normalizador determinista la garantiza (defensa en profundidad, mismo patrón que `LmsSvgRepairService::normalizeContrast`).

---

## 1. Análisis de la implementación actual

### 1.1 La función `generateSlideHtmlTags()` — `app/Livewire/Profesor/Lms/LessonWizard.php:2146`

Es un wrapper delgado y correcto en su esqueleto:

- Guardas: `publishedGuard()` ✓, existe `contents[0]` ✓, body no vacío ✓.
- Delega en `HtmlTaggingService::tag()` con un callback que usa `askWithCompaction` (cadena de modelos con fallback).
- Éxito → reemplaza body + `type='HTML'` + dispatch `show-preview` ✓. Error → `generationError` + notificación ✓. `finally` resetea `generatingSection` ✓.

**Debilidades**:

| # | Hallazgo | Impacto |
|---|---|---|
| F1 | Etiqueta **solo `contents[0]`**; el wizard permite 2 bloques por diapositiva (`generateSectionIllustration` valida `count >= 2`) | El 2º bloque queda sin etiquetar (texto plano al lado de HTML enriquecido) → inconsistencia visual |
| F2 | **Cero validación del HTML generado** (estructura, clases prohibidas, tamaños) | Dependencia total del prompt; si el LLM emite `text-3xl`, se guarda tal cual |
| F3 | Sin "deshacer": el body original se reemplaza irreversiblemente (el wizard guarda en memoria, pero el guardado posterior persiste el HTML) | El profesor no puede volver al texto plano |
| F4 | El resultado puede **duplicar el título** de la sección como `h3` gigante (la plantilla ya muestra el título del paso) | Jerarquía rota, ruido visual |

### 1.2 `HtmlTaggingService::tag()` — `app/Services/Lms/HtmlTaggingService.php:298`

- Construye userPrompt (contexto pedagógico + body original), llama al callback con `max_tokens 8192, temperature 0.20, timeout 120`.
- Cleanup agresivo de fences markdown ✓.
- **Sin post-procesamiento del HTML** (no hay clamp de tamaños, no hay validación de clases).

### 1.3 El SYSTEM_PROMPT (275 líneas) — causas de la desarmonía tipográfica

El prompt es sólido en lo estructural (preservar texto original, prohibir bg-*/SVG/card raíz), pero **prescribe una escala desproporcionada** respecto al resto de la lección:

| Elemento | Escala prescrita hoy | Escala del resto de la lección (renderer) |
|---|---|---|
| Título h3 | `text-3xl` (30px) / `text-2xl` (24px) — 4 variantes | Título de paso: `text-sm` (14px) en `_content-renderer` / `step-card` |
| Subtítulo h4 | `text-lg` (18px) | — |
| Párrafo | `text-base` (16px) | `text-[17px]` (step-card body) |
| Blockquote | `text-lg` (18px) | — |
| Stat number | `text-3xl font-extrabold` (30px) | — |
| Cards internas | `p-6`/`p-5` + `shadow-lg`/`shadow-md` | — |
| Badges | `text-sm` | — |
| Acordeón | `px-4 py-3`, contenido `text-sm` | — |

Causas raíz específicas:

1. **Escala absoluta desalineada**: `text-3xl`/`text-2xl` en títulos y stats (24–30px) vs 14–17px del contenido vecino. La lección mezcla diapositivas TEXT (heredan la plantilla) y diapositivas HTML etiquetado (auto-especifican tamaños) → contraste brutal.
2. **Duplicación de título**: la plantilla ya renderiza el título del paso (`text-sm`); el prompt invita a emitir otro `h3 text-3xl` con el mismo texto → doble título + elemento gigante.
3. **Few-shot dominante**: el EJEMPLO COMPLETO del prompt usa `text-3xl` para el h3 y `p-4`/`mb-4` — el modelo imita el ejemplo por encima de las instrucciones abstractas.
4. **Variantes sin límite**: "elige UNA de estas variantes" (a-d) con tamaños distintos → el modelo tiende a la más grande (a: `text-3xl`).
5. **Cards pesadas**: `p-6 shadow-lg` internas compiten con el contenedor de la plantilla (que ya tiene borde/fondo).

### 1.4 El USER_PROMPT

Correcto en preservación de texto, pero **no menciona la escala tipográfica**; la frase "tipografía variada (color de acento en título, subrayado decorativo)" refuerza la variación sin tope.

### 1.5 El renderer (lado estudiante)

El contenido `HTML` se inserta **crudo** (`{!! !!}`) en el step — sin clamp de tamaños. Los contenidos `TEXT` pasan por `step-card` con `text-[17px]`. No hay ninguna capa que uniformice el HTML etiquetado.

---

## 2. Objetivos

- O1. Una **escala tipográfica única** (design tokens) para todo el contenido LMS: títulos, párrafos, stats, citas, cards, badges, acordeones.
- O2. El SYSTEM_PROMPT exige la escala (máximos absolutos) y **elimina la duplicación de título**.
- O3. **Normalizador determinista post-generación** que clampa las clases de tamaño (defensa en profundidad: aunque el LLM falle, el HTML queda en escala).
- O4. La función del wizard etiqueta **todos** los bloques de la diapositiva (no solo `contents[0]`).
- O5. Verificación E2E: diapositiva etiquetada junto a diapositivas TEXT → misma armonía.

---

## 3. Propuesta integral

### F0 — Design tokens LMS (fuente de verdad)

Nueva constante compartida (p. ej. `app/Services/Lms/LmsDesignTokens.php` o dentro de `LmsContentClassifier`/un servicio de estilos) con la escala:

| Token | Clase Tailwind (máximo) | Uso |
|---|---|---|
| `heading-1` | `text-lg font-bold` (18px) — NUNCA mayor | Título de contenido (h3) |
| `heading-2` | `text-base font-semibold` (16px) | Subtítulo (h4) |
| `body` | `text-[15px] text-gray-700 leading-relaxed` | Párrafos, listas (≈ step-card 17px) |
| `quote` | `text-[15px] italic text-gray-700` | Blockquote |
| `stat` | `text-2xl font-extrabold` (24px) — NUNCA mayor | Número de stat card |
| `badge` | `text-xs` | Badges inline |
| `card-pad` | `p-4` — NUNCA mayor | Padding de cards internas |
| `card-shadow` | `shadow-sm` — NUNCA mayor | Sombras |
| `accordion-summary` | `text-[15px] font-semibold` | Acordeón |

El token se expone también como **constante PHP** para el normalizador y como **texto** para el prompt (sin drift entre prompt y código).

### F1 — Rework del SYSTEM_PROMPT (HtmlTaggingService)

1. Nueva sección **"═══ ESCALA TIPOGRÁFICA OBLIGATORIA ═══"** con la tabla de máximos (texto generado desde la constante, para no duplicar).
2. **Eliminar la duplicación de título**:
   > ❌ NO emitas el título de la sección/diapositiva como `<h3>` — la plantilla ya lo muestra. Empieza directamente con el primer bloque de contenido (highlight box, lista, etc.).
3. **Unificar las 4 variantes de título** a una sola (`text-lg font-bold text-emerald-700`), manteniendo el subrayado decorativo como variante opcional (mismo tamaño).
4. **Corregir el EJEMPLO COMPLETO** al nuevo scale (h3 `text-lg`, stat `text-2xl`, cards `p-4`, blockquote `text-[15px]`) — el few-shot debe enseñar la escala correcta.
5. Reducir cards internas a `p-4` + `shadow-sm` (máx).
6. Añadir regla de armonía: *"El contenido se inserta junto a otros pasos de la lección que usan tipografía pequeña (14–17px). Tu HTML NO debe verse más grande que el resto."*

### F2 — Rework del USER_PROMPT

Añadir al final (antes de las restricciones):

```
ESCALA TIPOGRÁFICA (obligatoria): títulos máx text-lg (18px), subtítulos text-base,
párrafos/listas text-[15px], números de stat card máx text-2xl (24px), padding de
cards máx p-4, sombras máx shadow-sm. PROHIBIDO: text-3xl, text-2xl en títulos,
p-5/p-6, shadow-lg/shadow-xl. NO repitas el título de la sección como heading.
```

### F3 — Normalizador determinista post-generación (defensa en profundidad)

Nuevo `app/Services/Lms/LmsTypographyNormalizerService.php` (patrón `LmsSvgRepairService::normalizeContrast`):

```php
public function normalize(string $html): string
// Clamp conservador (solo baja, nunca sube):
//   text-3xl|text-4xl|... → text-lg   (títulos)
//   text-2xl → text-lg | text-xl     (según contexto: stat numbers → text-2xl permitido)
//   text-xl  → text-lg
//   text-lg  → text-lg (se respeta si es cita/stat; opción estricta: text-base)
//   p-5|p-6|p-8 → p-4
//   shadow-lg|shadow-xl|shadow-2xl → shadow-sm
```

- Se aplica en `HtmlTaggingService::tag()` justo después del cleanup de fences (antes de devolver el html).
- Modo estricto/conservador configurable; por defecto conservador.
- Tests unitarios: cada regla de clamp + no-toca (input sin clases grandes pasa intacto).

### F4 — Función del wizard: etiquetar todos los bloques

En `generateSlideHtmlTags()`: iterar `contents[0..n]` (máx 2), etiquetar cada uno, y notificar cuántos se etiquetaron. Mantener guardas por bloque (los vacíos se saltan).

### F5 — Verificación

1. Tests unitarios del normalizador (F3).
2. E2E real (wizard, actividad con contenido): diapositiva etiquetada junto a diapositivas TEXT → captura antes/después; comprobar que los tamaños del HTML etiquetado (≤18px títulos, ≤24px stats, p-4) armonizan con step-card (17px) y _content-renderer (14px).
3. Suites: `LessonWizardCharacterizationTest`, `tests/Feature/Lms/`, `tests/Unit/Lms/`, `npm run build`.

---

## 4. Fases y archivos afectados

| Fase | Cambio | Archivos |
|---|---|---|
| F0 | Design tokens compartidos | `app/Services/Lms/LmsDesignTokens.php` (nuevo) |
| F1 | Rework SYSTEM_PROMPT (escala + sin duplicar título + ejemplo corregido) | `app/Services/Lms/HtmlTaggingService.php` |
| F2 | Rework USER_PROMPT (escala) | `app/Services/Lms/HtmlTaggingService.php` |
| F3 | Normalizador + tests + hook en `tag()` | `app/Services/Lms/LmsTypographyNormalizerService.php` (nuevo), `HtmlTaggingService.php`, `tests/Unit/Lms/LmsTypographyNormalizerServiceTest.php` (nuevo) |
| F4 | Etiquetar todos los bloques | `app/Livewire/Profesor/Lms/LessonWizard.php` |
| F5 | Verificación E2E + suites + build | — |
| (opcional) | Registro en `blueprint/estudiant/implementations.md` | — |

---

## 5. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| El clamp rompe diseños intencionales | Solo **baja** tamaños (nunca sube); umbral = escala máxima del sistema; las variantes menores (text-sm, text-xs) pasan intactas |
| Stat cards pierden impacto al bajar de text-3xl a text-2xl | text-2xl (24px) sigue siendo un número destacado; se compensa con `font-extrabold` + color de acento |
| El LLM sigue emitiendo títulos duplicados | Doble defensa: prompt (no duplicar) + normalizador (no puede saber qué es duplicado) → mitigación parcial; opción futura: eliminar el primer h3 si coincide con `$sectionTitle` (normalizador con contexto) |
| Cambiar el prompt degrada la calidad estructural (highlight boxes, listas) | El rework SOLO toca tamaños y duplicación de título; las estrategias 1–6 y restricciones bg/SVG se mantienen intactas |
| F4 (multi-bloque) cambia el flujo del wizard | Es aditivo: los bloques adicionales se etiquetan con el mismo servicio; si falla uno, se notifica y el resto continúa |

---

## 6. Conclusión

La función y el servicio están bien estructurados; el defecto es de **escala tipográfica prescrita** (24–30px en un ecosistema de 14–17px) agravado por 4 factores: duplicación de título, few-shot con ejemplo gigante, variantes sin límite y ausencia de validación/normalización del HTML. La solución integral: **design tokens + prompt con máximos + normalizador determinista + multi-bloque**, con verificación E2E comparativa.

---

## 7. Estado de implementación (2026-08-08)

- [x] **F0** — `LmsDesignTokens` (nuevo): escala única (heading-1 ≤ text-lg, stat ≤ text-2xl, card-pad p-4, card-shadow shadow-sm) + `promptRules()` generado desde la constante (sin drift prompt↔código).
- [x] **F1** — SYSTEM_PROMPT rework: estrategia tipográfica unificada (2 variantes a text-lg), blockquote a text-[15px]/p-4, stat cards a text-2xl/p-4, TIPOGRAFÍA (h3 text-lg, h4 text-base, p text-[15px]), regla "NO repitas el título de la sección", EJEMPLO COMPLETO corregido al nuevo scale. El prompt final = const + `LmsDesignTokens::promptRules()` (`systemPrompt()`).
- [x] **F2** — USER_PROMPT: bloque "ESCALA TIPOGRÁFICA (obligatoria)" con máximos y prohibiciones.
- [x] **F3** — `LmsTypographyNormalizerService` (nuevo): clamp determinista de `class="..."` — text-2xl+ → text-lg (text-2xl se conserva solo con `font-extrabold`, patrón stat), text-[NNpx] > 18 → text-lg, p-5+ (y variantes) → p-4, shadow-md+ → shadow-sm; dedupe de clases; **solo baja, nunca sube**. Hook en `HtmlTaggingService::tag()` tras el cleanup de fences. 9 tests unitarios.
- [x] **F4** — `generateSlideHtmlTags()`: etiqueta TODOS los bloques de la diapositiva (máx 2), salta vacíos, acumula errores por bloque, notifica el conteo etiquetado.
- [x] **F5** — Verificación: 215 tests verdes (Unit/Lms + Feature/Lms + Feature/Livewire/Profesor/Lms), `npm run build` ✓.

Pendiente (fuera de alcance): verificación visual E2E comparativa en navegador con una diapositiva etiquetada junto a diapositivas TEXT (recomendado tras regenerar contenido real con el nuevo prompt).
