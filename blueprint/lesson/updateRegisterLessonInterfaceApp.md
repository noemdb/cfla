# LMS Lesson Wizard — Step 2 Slide Editor

## Overview

El paso 2 ("Contenido de la Lección") del wizard de registro de lecciones LMS se rediseñó de un layout de tarjetas en grilla a una **interfaz de editor de diapositivas**. Cada sección de la lección se muestra una a la vez como una diapositiva con contenido HTML, con tres botones de acción: Generar Texto, Generar Imagen, Generar Diagrama (Mermaid).

## URL

```
/app/profesors/lms/activity/lesson/new?activity_id=1
```

## Architecture

### Stack

- **Componente**: `App\Livewire\Profesor\Lms\LessonWizard` (~2600 líneas PHP)
- **Vista**: `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` (~3720 líneas Blade)
- **Servicios IA**: `OpenRouterService` (primario), `NvidiaService` (fallback para rate-limit/timeout)
- **Modelos**: `LmsActivitySection` → `LmsActivityContent` (relación hasMany)

### Data Model (sin cambios)

| Modelo | Campos clave |
|--------|-------------|
| `LmsActivitySection` | `id`, `activity_id`, `title`, `sort_order`, `is_visible` |
| `LmsActivityContent` | `id`, `section_id`, `type` (TEXT/HTML), `title`, `body`, `sort_order`, `is_visible` |

El campo `body` almacena HTML directamente. No se requirieron migraciones.

## Component Properties (nuevas)

Agregadas a `LessonWizard.php`:

```php
// ─── Slide navigation (paso 2)
public int $currentSlideIndex = 0;
public bool $showSlideHtmlPreview = false;
```

Propiedades existentes relevantes:
```php
public array $wizardSections = [];      // Colección en memoria de secciones
public ?int $generatingSection = null;   // Índice de sección generándose
public ?string $generationError = null;  // Error de generación
```

## Component Methods (nuevas/modificadas)

### Slide Navigation

```php
public function goToSlide(int $index): void    // Navegar a diapositiva por índice (clamped 0..max-1)
public function nextSlide(): void              // Siguiente diapositiva
public function prevSlide(): void              // Diapositiva anterior
```

### Slide Generation

```php
public function generateSlideText(): void      // Genera contenido HTML vía OpenRouter (con clases Tailwind)
public function generateSlideImage(): void     // Agrega placeholder <figure> con SVG para imagen
public function generateSlideDiagram(): void   // Genera diagrama Mermaid inyectado como bloque HTML
```

### Métodos existentes (sin cambios)

```php
public function generateStep1Content(): void   // Genera título y descripción de la lección (paso 1)
public function generateSectionContent(int $i): void  // Genera contenido de sección (texto plano, usado internamente)
public function generateStep2Sections(): void  // Genera estructura completa de secciones vía IA
```

## AI Generation Flow

```
askWithCompaction() ──→ OpenRouterService.ask()
     │                        │
     │  HTTP 429/timeout      │  HTTP 401 (sin API key)
     └──→ NvidiaService.ask() └──→ Error propagado al usuario
```

**Pipeline**: `generateSlideText()` → `askWithCompaction()` → `OpenRouterService::ask()` — con fallback a `NvidiaService::ask()` solo para errores 429 (rate limit) y timeout. Errores 401 (Missing Authentication header) se devuelven directamente.

**Prompt**: Sistema configurado como "docente venezolano". Genera HTML semántico con Tailwind CSS: `<h2>`, `<p>`, `<ul>/<ol>`, `<blockquote>`, `<div>` con clases utilitarias.

## View Structure (Paso 2)

```
@if($currentStep === 2)
  ┌─ Slide Navigation Bar ──────────────────────────┐
  │  [← Anterior] | [Siguiente →]   Slide 3/8 [☰] │
  └─────────────────────────────────────────────────┘
  ┌─ Slide List (collapsible) ──────────────────────┐
  │  ○ 1. Introducción           ● (current)        │
  │  ○ 2. ¿Qué es...            ● (has content)     │
  │  ○ 3. Partes...             ○ (empty)           │
  └─────────────────────────────────────────────────┘
  ┌─ Slide Title ───────────────────────────────────┐
  │  [3] [Título editable inline...]      [👁/🚫]   │
  ├─ Tab: Editor | Preview ────────────────────────┤
  │  [EDIT tab]  <textarea> HTML content            │
  │  [PREVIEW]   Rendered HTML (white bg, prose)    │
  ├─ Action Buttons ───────────────────────────────┤
  │  [🧠 Generar Texto] [🖼 Generar Imagen]          │
  │  [📊 Generar Diagrama]  |  [🗑 Eliminar]         │
  └─────────────────────────────────────────────────┘
  ┌─ Add Section ───────────────────────────────────┐
  │  [Nueva diapositiva...          ] [+ Diapositiva]│
  └─────────────────────────────────────────────────┘
@endif
```

### Slide Navigation Bar
- Botones Anterior/Siguiente (deshabilitados en extremos)
- Contador: "Diapositiva N / M"
- Botón ☰ para toggle de lista de diapositivas
- Slide counter con estilo monospace

### Slide List (colapsable)
- Lista vertical con botón por diapositiva
- Indicador de contenido (punto verde = tiene contenido, gris = vacío)
- Al hacer clic navega a la diapositiva y cierra la lista
- Scroll si hay muchas diapositivas (max-h-48)

### Slide Content Area
- Número de diapositiva en badge gradient
- Título editable inline (input con border-bottom)
- Botón de visibilidad (eye/eye-off)
- **Tabs**: Editor / Preview
  - **Editor**: textarea monospace (12 rows), wire:model al primer bloque de contenido (`contents.0.body`)
  - **Preview**: renderizado HTML en contenedor white con clases `prose prose-sm`, más indicador Mermaid si detecta `class="mermaid"` en el contenido
- Estado vacío si no hay contenido ni bloques

### Action Buttons
- **Generar Texto** (emerald): llama `generateSlideText()`, reemplaza o crea primer bloque HTML
- **Generar Imagen** (amber): llama `generateSlideImage()`, agrega placeholder `<figure>` con SVG
- **Generar Diagrama** (fuchsia): llama `generateSlideDiagram()`, inyecta bloque HTML con Mermaid
- **Eliminar** (red): llama `removeWizardSection()`, con confirmación `wire:confirm`
- Separador vertical entre acciones principales y eliminar

### Generation Error
- Banda roja debajo de botones si `$generationError` está establecido
- Muestra el mensaje de error con ícono de advertencia

### Empty State (sin diapositivas)
- Icono grande de "+" en círculo
- Texto: "No hay diapositivas"
- Botón: "Generar estructura con IA" → llama `generateStep2Sections()`

### Add Section
- Input + botón en barra inferior
- `wire:keydown.enter` para agregar con Enter
- `wire:model="newSectionTitle"` para el título

## Files Modified

### `app/Livewire/Profesor/Lms/LessonWizard.php`

**Agregado (~90 líneas nuevas)**:
- Propiedades: `currentSlideIndex`, `showSlideHtmlPreview` (3 líneas)
- Métodos: `goToSlide()`, `nextSlide()`, `prevSlide()` (~15 líneas)
- Métodos: `generateSlideText()` (~120 líneas)
- Métodos: `generateSlideImage()` (~50 líneas)  
- Métodos: `generateSlideDiagram()` (~40 líneas)

### `resources/views/livewire/profesor/lms/lesson-wizard.blade.php`

**Reemplazado** (~240 líneas nuevas de slide editor, eliminando ~560 líneas de old deck-card grid):
- Todo el bloque `@if($currentStep === 2)` (líneas 2055-2298)
- Markers `__REPLACE_P3__`, `__REPLACE_MID__`, `__END_MARKER__` usados durante la migración (eliminados)

## Configuration Required

| Variable | Archivo Config | Propósito |
|----------|---------------|-----------|
| `OPENROUTER_API_KEY` | `config/openrouter.php` | API key para OpenRouter (provider primario) |
| `NVIDIA_API_KEY` | `config/nvidia.php` | API key para NVIDIA (fallback) |

Si ninguna está definida, la generación falla con:
```
HTTP 401: {"error":{"message":"Missing Authentication header","code":401}}
```

## Verification Checklist

- [x] Slide navigation (prev/next/counter) funciona
- [x] Slide list toggle muestra/oculta la lista
- [x] Título editable inline se sincroniza con `wizardSections`
- [x] Toggle de visibilidad funciona
- [x] Editor tab muestra textarea con contenido HTML
- [x] Preview tab renderiza HTML con prose styling
- [x] "Generar Texto" produce HTML con Tailwind classes
- [x] "Generar Imagen" crea placeholder `<figure>`
- [x] "Generar Diagrama" inyecta bloque Mermaid
- [x] Empty state se muestra cuando no hay diapositivas
- [x] Add section form agrega nuevas secciones
- [x] Delete button con confirmación elimina la sección
- [x] API keys configuradas en `.env` para que IA funcione
