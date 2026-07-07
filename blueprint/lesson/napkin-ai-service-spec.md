# Spec: NapkinAiService — Generación de diagramas SVG vía napkin.ai

## Versión

| Campo | Valor |
|---|---|
| Versión | 1.0 |
| Fecha | 2026-07-07 |
| Estado | Implementado |
| Módulo | LMS — Lesson Wizard (Slide Editor) |
| Servicio | `App\Services\NapkinAiService` |
| Método consumidor | `LessonWizard::generateSlideImage()` |

## Propósito

Generar diagramas visuales en formato SVG a partir del contenido textual de una sección de lección, usando la API de napkin.ai. El SVG generado se incrusta directamente como HTML en el `body` del contenido de la sección (`type: 'TEXT'`), sin depender de URLs externas ni CDNs.

## Arquitectura

```
generateSlideImage()
  │
  ├─► NapkinAiService::generateDiagram(prompt, overrides)
  │     │
  │     ├─► tryVegaGenerator()    ← data_graphics_generator (Vega)
  │     │     └─► falla → sigue
  │     │
  │     ├─► tryImageGeneration()  ← features/image/generation
  │     │     └─► falla → error
  │     │
  │     └─► [error] → caller decide
  │
  ├─► [éxito] → buildEmbedHtml() → <figure> con SVG
  │
  └─► [fallback] → placeholder visual (gradiente azul)
```

## API de napkin.ai

### Endpoints

| Endpoint | URL | Propósito |
|---|---|---|
| **Vega Data Graphics** | `https://vega.nlp.api.napkin.ai/api/v1/data_graphics_generator` | Generación de gráficos/visualizaciones (basado en Vega) |
| **Image Generation** | `https://nlp-california-api.napkin.ai/api/v1/features/image/generation` | Generación de imágenes/diagramas |

### Autenticación

- **Header:** `Authorization: Bearer {api_key}`
- **Key format:** `sk-*` (similar a OpenAI)
- **Config:** `config/napkin.php` → `NAPKIN_API_KEY`
- **Origen:** Perfil del usuario en app.napkin.ai → API Keys

> **Nota:** La API interna de napkin.ai usa autenticación por cookies de sesión (`credentials: "include"`). El soporte para API keys `sk-*` puede requerir habilitación desde el dashboard de napkin.ai. Si la API retorna `403 {"message":"requires login"}`, el servicio falla limpiamente al placeholder.

### Formato de petición (Vega)

```json
POST /api/v1/data_graphics_generator
Content-Type: application/json
Authorization: Bearer sk-...

{
  "prompt": "Descripción del diagrama...",
  "format": "svg",
  "style": "educational_diagram"
}
```

### Formato de petición (Image Generation)

```json
POST /api/v1/features/image/generation
Content-Type: application/json
Accept: multipart/related
Authorization: Bearer sk-...

{
  "prompt": "Descripción del diagrama...",
  "resolution": "1024x1024",
  "create_scene": true
}
```

### Formato de respuesta (esperado)

El servicio acepta varios formatos de respuesta según el endpoint:

1. **SVG directo** — `Content-Type: image/svg+xml` o cuerpo que empieza con `<svg`
2. **JSON con campo `svg`** — `{"svg": "<svg>...</svg>"}`
3. **JSON con campo `image_url`** — `{"image_url": "https://..."}`
4. **Multipart/related** — respuesta con boundary, cada parte es un contenido distinto

## Contrato del servicio

### `NapkinAiService::generateDiagram(string $prompt, array $overrides): array`

**Input:**

| Parámetro | Tipo | Default | Descripción |
|---|---|---|---|
| `$prompt` | `string` | — | Descripción textual del diagrama a generar |
| `$overrides['style']` | `string` | `'diagram'` | Estilo visual del diagrama |
| `$overrides['resolution']` | `string` | `'1024x1024'` | Resolución para image generation |
| `$overrides['timeout']` | `int` | `config('napkin.timeout', 120)` | Timeout en segundos |
| `$overrides['base_url']` | `string` | `config('napkin.base_url')` | Base URL para endpoint Image |
| `$overrides['vega_url']` | `string` | `config('napkin.vega_url')` | Base URL para endpoint Vega |

**Output:**

```php
[
  'success'   => bool,    // true si se generó contenido
  'svg_html'  => ?string, // Código SVG (prioritario)
  'image_url' => ?string, // URL de imagen (fallback si no hay SVG)
  'error'     => ?string, // Mensaje de error si success=false
]
```

### `NapkinAiService::buildEmbedHtml(?string $svgHtml, ?string $imageUrl, string $title): string`

Construye HTML semántico para incrustar en la sección:

**SVG output:**
```html
<figure class="my-6 napkin-diagram">
  <figcaption class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
    Diagrama: título
  </figcaption>
  <div class="flex justify-center bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
    <svg>...</svg>
  </div>
</figure>
```

**Image URL output:**
```html
<figure class="my-6 napkin-image">
  <figcaption class="...">Diagrama: título</figcaption>
  <img src="data:image/png;base64,..." alt="..." class="rounded-xl max-w-full mx-auto shadow-sm"/>
</figure>
```

## Integración en LessonWizard

### Método: `generateSlideImage()`

**Trigger:** Botón "Generar Imagen" en el slide editor (step 2 del wizard)

**Flujo:**

1. Recopila contexto de la diapositiva actual:
   - `sectionTitle` — título de la sección
   - `sectionBody` — texto plano de todos los bloques de contenido
   - `gradeName`, `subjectName` — contexto pedagógico
2. Construye prompt con el contenido y contexto
3. Llama a `NapkinAiService::generateDiagram(prompt)`
4. Si éxito:
   - `buildEmbedHtml()` genera `<figure>` con SVG
   - Agrega un nuevo bloque `contents[]` a la sección con `type: 'TEXT'`
   - Muestra notificación "Diagrama generado"
5. Si fallo:
   - Loguea warning
   - Crea placeholder visual (gradiente azul con icono)
   - Muestra notificación con indicación de que napkin.ai no estuvo disponible

**Loading state:** `wire:target="generateSlideImage"` + overlay general "Generando contenido con IA"

## Renderizado en vistas

El SVG se renderiza sin escapado HTML en todos los puntos de visualización:

| Vista | Código | Notas |
|---|---|---|
| Slide editor (preview) | `{!! $slideContent !!}` | Concatenación de todos los bodies |
| Vista estudiante | `{!! $content->body !!}` | `type === 'TEXT'` |
| Vista estudiante (embeds) | `{!! $embed->html_content !!}` | Solo para `LmsHtmlEmbed` no-Mermaid |

## Configuración

### `config/napkin.php`

```php
'api_key'    => env('NAPKIN_API_KEY'),          // sk-...
'base_url'   => env('NAPKIN_API_URL'),           // nlp-california-api
'vega_url'   => env('NAPKIN_VEGA_URL'),          // vega.nlp.api
'tool_url'   => env('NAPKIN_TOOL_URL'),          // api.tool.napkin.ai
'resolution' => env('NAPKIN_RESOLUTION', '1024x1024'),
'timeout'    => env('NAPKIN_API_TIMEOUT', 120),
```

### `.env`

```env
NAPKIN_API_KEY=sk-b91218139390f0b12736c263eb972097b56dbe000b9df9e0284cf9676d78266d
```

## Manejo de errores

| Escenario | Comportamiento |
|---|---|
| napkin.ai retorna 403 "requires login" | Log warning + placeholder visual |
| napkin.ai retorna timeout/excepción | Catch Throwable + log + placeholder |
| napkin.ai retorna contenido vacío | Log "generación sin contenido útil" + placeholder |
| napkin.ai retorna SVG válido | Incrusta SVG directamente en la sección |
| napkin.ai retorna image_url | Crea `<img>` con la URL |

## Pruebas

### Casos de prueba recomendados

1. **API key válida, prompt pedagógico**
   - Ejecutar `generateSlideImage()` con sección que tenga contenido
   - Verificar que se agrega bloque `type: 'TEXT'` con SVG dentro de `<figure>`
   - Verificar que el SVG se renderiza en preview

2. **API no disponible (403)**
   - Verificar que cae al placeholder visual
   - Verificar que loguea warning
   - Verificar que muestra notificación informativa

3. **Sin API key configurada**
   - `config('napkin.api_key')` retorna null
   - Servicio envía petición sin `Authorization` header
   - Mismo comportamiento que caso 2

4. **Renderizado en vista estudiante**
   - Guardar lección con SVG incrustado
   - Verificar que se renderiza como gráfico (no como código)

## Dependencias

- `illuminate/support/facades/Http` — Laravel HTTP client
- napkin.ai cuenta activa con API key generada desde Profile → API Keys

## Historial

| Fecha | Cambio |
|---|---|
| 2026-07-07 | Versión inicial del spec e implementación |
