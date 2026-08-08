# Spec — Campo `content_type` en `lms_activity_sections`

> **Estado**: Propuesta (plan integral) · **Fecha**: 2026-08-08
> **Ámbito**: módulo LMS (estudiante, profesor, dirección) · **Impacto**: schema + modelo + clasificador + vistas + wizard
> **Principio rector**: la columna es una **caché denormalizada** derivada de los contenidos; la clasificación vive en un solo servicio (`LmsContentClassifier`); **ninguna funcionalidad existente se rompe** (degradación elegante con `null`/`mixed`).

---

## 1. Contexto y problema

`lms_activity_sections` (359 filas) no tiene ningún campo que indique el tipo de contenido de la sección. Los tipos viven en `lms_activity_contents.type` (`ENUM('TEXT','VIDEO','AUDIO','IMAGE','PRESENTATION','HTML','EMBED','FILE_PREVIEW')`, con valores realmente usados hoy: `TEXT`, `HTML`, `IMAGE`) y, además, la clasificación *fina* es **dinámica**: un body `TEXT` puede ser en realidad Mermaid (por clase CSS o keyword), un `IMAGE` puede ser SVG crudo, y un `HTML` puede contener KaTeX.

Consecuencias actuales:

1. Cada vista (impresión, modo libro, scroll, previsualización) re-clasifica contenido por contenido con regex duplicadas (ya unificadas en `LmsContentClassifier`, P4).
2. No hay forma de decidir a nivel de **sección** (p.ej. "esta sección es un diagrama mermaid", "esta es una sección de ejercicio") sin consultar y clasificar todos sus contenidos.
3. El wizard y las vistas no pueden aplicar tratamiento por tipo de sección (plantillas, espaciado, filtros, agrupación).
4. No hay estadística ni reporting por tipo de sección.

**Decisión**: agregar `content_type` a `lms_activity_sections` como **etiqueta derivada** de los contenidos visibles, mantenida por observer y recalculable por comando (backfill/reparación).

---

## 2. Objetivos y no-objetivos

### Objetivos

- O1. Campo persistente en `lms_activity_sections` con el tipo de contenido de la sección.
- O2. Clasificación **determinista y centralizada** en `LmsContentClassifier` (una sola fuente de verdad, ya existente).
- O3. Sincronización automática: cualquier cambio en contenidos (crear/editar/ocultar/eliminar) actualiza el tipo de su sección.
- O4. Backfill de las 359 secciones existentes + comando de mantenimiento.
- O5. Las interfaces usan el tipo de sección para **mejorar** el tratamiento (plantillas, agrupación, badges) sin cambiar el comportamiento actual cuando el tipo es `null`/`mixed`.

### No-objetivos

- NO cambiar el schema de `lms_activity_contents` ni su ENUM (riesgo alto, cero beneficio).
- NO eliminar la clasificación por contenido (la clasificación fina por bloque se mantiene).
- NO forzar una única semántica por sección: `mixed` es un tipo de primera clase.
- NO tocar el modo libro en curso (el usuario lo está eliminando en paralelo) — la integración se hará donde el modo libro siga existiendo o se adaptará tras ese refactor.

---

## 3. Taxonomía de tipos

Valores propuestos para `content_type` (VARCHAR(30), lowercase, un valor por sección):

| Tipo | Significado | Detección (por contenido visible) |
|---|---|---|
| `text` | Prosa simple (párrafos sin estructura) | `type=TEXT` sin markdown estructural, sin mermaid, sin math, sin `<svg>` |
| `markdown` | Markdown estructurado (listas, citas, tablas, títulos) | `type=TEXT` con `ul/ol/blockquote/table/h2-h4` o tokens markdown tras conversión |
| `html` | HTML/embeds enriquecidos | `type=HTML` (sin `<svg>` dominante, sin mermaid) |
| `mermaid` | Diagrama mermaid | `LmsContentClassifier::isMermaidBody()` |
| `svg` | Ilustración SVG cruda ("Generar Imagen") | `type=IMAGE` + `<svg>` o `LmsContentClassifier::isImageBody()` |
| `image` | Imagen raster (media adjunta: png/jpg/webp) | `type=IMAGE` con `media` y body sin `<svg>` |
| `math` | Texto matemático LaTeX (KaTeX) | `type=MATH` o body con `$...$`/`$$...$$`/`\(...\)`/`\[...\]` |
| `video` | Video (ENUM existente) | `type=VIDEO` |
| `audio` | Audio (ENUM existente) | `type=AUDIO` |
| `mixed` | Dos o más tipos distintos entre los contenidos visibles | agregación: `count(distinct tipo) > 1` |
| `none` | Sin contenidos visibles | agregación vacía |

> **Nota**: `null` queda reservado para filas legacy sin backfill (degradación elegante: las vistas tratan `null` exactamente como hoy).

---

## 4. Diseño de datos

### 4.1 Migración

`database/migrations/2026_08_08_000001_add_content_type_to_lms_activity_sections.php`

```php
Schema::table('lms_activity_sections', function (Blueprint $table) {
    $table->string('content_type', 30)
          ->nullable()
          ->after('description')
          ->comment('Tipo de contenido derivado de los contenidos visibles (caché, ver LmsContentClassifier)');
    $table->index('content_type');
});
```

- `down()`: drop index + column.
- **Backfill**: el mismo archivo de migración (tras `Schema::table`) ejecuta la clasificación de las filas existentes usando el servicio (ver §6). El backfill se hace con consultas por sección (359 filas → trivial, una sola pasada).

### 4.2 Modelo `LmsActivitySection`

```php
protected $fillable = ['activity_id', 'title', 'description', 'sort_order', 'is_visible', 'content_type'];

public const CONTENT_TYPES = [
    'text', 'markdown', 'html', 'mermaid', 'svg', 'image',
    'math', 'video', 'audio', 'mixed', 'none',
];

public const CONTENT_TYPE_LABELS = [
    'text' => 'Texto', 'markdown' => 'Markdown', 'html' => 'HTML',
    'mermaid' => 'Mermaid', 'svg' => 'SVG', 'image' => 'Imagen',
    'math' => 'Math (LaTeX)', 'video' => 'Video', 'audio' => 'Audio',
    'mixed' => 'Mixto', 'none' => 'Sin contenido',
];
```

Accesor defensivo (si la caché está vacía por drift, calcula en vivo):

```php
public function getContentTypeAttribute(?string $cached): ?string
{
    return $cached ?? app(LmsContentClassifier::class)->classifySection($this->visibleContents);
}
```

---

## 5. Clasificación centralizada (extender `LmsContentClassifier`)

Ya existe (P4). Se le añaden dos métodos **puros y testeables**:

```php
/**
 * Clasifica UN contenido → tipo fino.
 */
public function classifyContent(string $type, string $body, ?string $mediaMime = null): string
{
    if ($this->isMermaidBody($body))                 return 'mermaid';
    if ($this->isImageBody($type, $body)) {
        return ($mediaMime && ! str_contains($mediaMime, 'svg')) ? 'image' : 'svg';
    }
    if (str_contains($body, '$') || str_contains($body, '\\('))  return 'math'; // heurística KaTeX
    if ($type === 'HTML')                            return 'html';
    if ($type === 'VIDEO')                           return 'video';
    if ($type === 'AUDIO')                           return 'audio';
    if ($this->isMarkdownBody($body))                return 'markdown';
    return 'text';
}

/**
 * Agrega los contenidos visibles de una sección → tipo de sección.
 * null = sin clasificar; 'none' = sin contenidos; 'mixed' = varios tipos.
 */
public function classifySection(iterable $contents): ?string
{
    $types = [];
    foreach ($contents as $c) {
        $types[$this->classifyContent($c->type ?? 'TEXT', $c->body ?? '', $c->media?->mime_type)] = true;
    }
    if (count($types) === 0) return 'none';
    if (count($types) > 1)   return 'mixed';
    return array_key_first($types);
}

/**
 * ¿Markdown estructural? (listas, citas, tablas, títulos — vs prosa plana)
 */
public function isMarkdownBody(string $body): bool
{
    // tras conversión: presencia de <ul>/<ol>/<blockquote>/<table>/<h2>-<h4>
}
```

> El orden de precedencia es deliberado: **mermaid > svg/image > math > html > markdown > text**. Un body con `<svg>` dentro de un tipo `HTML` se clasifica `svg` (ya pasa hoy en la vista de impresión).

---

## 6. Sincronización (observers)

### 6.1 `LmsActivityContentObserver` (nuevo)

```php
class LmsActivityContentObserver
{
    public function __construct(private readonly LmsContentClassifier $classifier) {}

    public function saved(LmsActivityContent $content): void { $this->refreshSection($content); }
    public function deleted(LmsActivityContent $content): void { $this->refreshSection($content); }

    private function refreshSection(LmsActivityContent $content): void
    {
        $section = $content->section()->with('visibleContents.media')->first();
        if (! $section) return;
        $section->content_type = $this->classifier->classifySection($section->visibleContents);
        $section->saveQuietly(); // no disparar el observer de section
    }
}
```

- El `saved` cubre create + update (incluido toggle de `is_visible`).
- **Cuidado con `saveQuietly`** para no re-disparar eventos en cascada.
- Registro en `AppServiceProvider::boot()` o `EventServiceProvider`.

### 6.2 Contenido huérfano / N+1

El observer carga la sección con `visibleContents.media` en una consulta — 2 queries por mutación de contenido, aceptable (mutaciones de contenido son raras y de admin/wizard).

### 6.3 Alternativa considerada (descartada)

Recomputar el tipo **solo al leer** (accesor sin columna): evita la migración pero no permite indexar/filtrar/reportear por tipo y recalcula en cada render. La columna + observer es el equilibrio correcto.

---

## 7. Backfill y mantenimiento

### 7.1 En la migración

Tras crear la columna:

```php
$classifier = app(LmsContentClassifier::class);
LmsActivitySection::with('visibleContents.media')->chunkById(200, function ($sections) use ($classifier) {
    foreach ($sections as $section) {
        $section->content_type = $classifier->classifySection($section->visibleContents);
        $section->saveQuietly();
    }
});
```

### 7.2 Comando de mantenimiento

`php8.2 artisan lms:sync-section-types [--dry-run] [--activity=]` — recalcula el `content_type` de todas las secciones (o de una actividad). Sirve también como **reparación de drift** (si alguien edita la BD a mano) y para el CI.

---

## 8. Integración en interfaces (sin romper nada)

Regla: **todo consumo del nuevo campo es opcional y aditivo**; si `content_type` es `null` o `mixed`, el comportamiento es idéntico al actual.

| Interfaz | Uso del tipo de sección | Comportamiento legacy |
|---|---|---|
| `lessons-print.blade.php` | clase `section--{content_type}` en `.section` → espaciado/columnas por tipo (p.ej. `section--mermaid` con `break-inside:avoid`, `section--svg` centrada) | `null/mixed` → sin clase extra (igual que hoy) |
| `_content-renderer.blade.php` (scroll/libro) | wrapper con variante por tipo (p.ej. `section--math` tipografía mayor, `section--image` centrado) | idéntico |
| `activity-view.blade.php` | badge del tipo en el encabezado de sección (solo si hay un label) | sin badge |
| `student-preview.blade.php` | paso con icono según tipo | sin icono |
| Wizard (`LessonWizard`) | **escribe** el tipo al generar: `generateSectionIllustration()` → `svg`; `generateStep2Sections()` → `markdown`/`text`; sección de math → `math`; mermaid → `mermaid` | no rompe nada (escribe un campo nuevo) |
| Director/Profesor listados | filtro por tipo de sección (opcional, fase 2) | filtro ausente |
| Reporting/auditoría | conteos por tipo (`GROUP BY content_type`) | n/a |

**Cambios de vista sugeridos (fase 1, mínimos y seguros)**:

1. `lessons-print`: `<div class="section section--{{ $section->content_type ?? 'none' }}">` + 3-4 reglas CSS aditivas.
2. `_content-renderer`: variante de wrapper por tipo (aditiva).
3. Badge en `activity-view` (solo si label existe).

---

## 9. Tests

| Suíte | Cobertura |
|---|---|
| `tests/Unit/Lms/LmsContentClassifierTest` | `classifyContent` para cada tipo (text, markdown, html, mermaid, svg, image, math, video, audio) + precedencias (svg-in-html, mermaid-in-text) + `classifySection` (vacío → `none`, un tipo, varios → `mixed`) |
| `tests/Feature/Lms/SectionContentTypeTest` | migración: columna + backfill correcto; observer: crear/editar/ocultar/eliminar contenido actualiza el tipo de la sección; `saveQuietly` sin recursión |
| `tests/Feature/Lms/StudentLessonsPrintTest` | smoke: la vista print sigue renderizando con la columna nueva (`null` y `mixed` incluidos) |
| Comando | `lms:sync-section-types --dry-run` idempotente (segunda corrida sin cambios) |

---

## 10. Rollout / verificación

Fases (cada una termina verificada, mismo estándar del resto del módulo):

1. **F0 — Clasificador**: extender `LmsContentClassifier` + tests unitarios (verde).
2. **F1 — Migración + backfill**: migrar, verificar conteos por tipo (suma = 359), `down()` funcional.
3. **F2 — Observer + comando**: tests de sincronización + `lms:sync-section-types` idempotente.
4. **F3 — Vistas**: integración aditiva en print + `_content-renderer` (+ badge si aplica); correr `StudentLessonsPrintTest`, suites Lms completas y `npm run build`.
5. **F4 — Wizard**: escribir el tipo en los puntos de generación (sin cambiar el flujo).
6. **F5 — Verificación E2E**: renderizar una actividad con secciones `svg`/`mermaid`/`text`/`mixed` en print y pantalla; confirmar que el HTML/DOM no cambia para `null/mixed` y mejora para los tipos concretos.

Verificación canónica: `php8.2 -l` en todo lo tocado, `php8.2 artisan test` (suites Lms + nuevas), `npm run build`, y captura headless de la vista print antes/después.

---

## 11. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Drift entre columna y contenidos (edición manual de BD) | Comando `lms:sync-section-types` + observer en el flujo normal |
| `mixed` pierde granularidad (53 secciones hoy) | El tipo por contenido se conserva (clasificación fina intacta); `mixed` es explícito y puede desglosarse a futuro con una columna JSON `content_types[]` sin romper nada |
| ENUM de contents no cubre `MATH` (los wizard lo generan como `TEXT`/`HTML`) | La clasificación fina (body con `$...$`) detecta math independientemente del ENUM |
| Ruptura en vistas por columna nueva | Columna nullable + acceso opcional; tests de smoke con `null` |
| Costo del observer en mutaciones masivas (wizard) | `saveQuietly` + recálculo por sección (no por contenido) + chunk en backfill; si un día duele: cola/evento `lazy` |
| Conflicto con el refactor del modo libro en curso | F3 se adapta al estado final del modo libro; el plan no depende de él |

---

## 12. Archivos afectados (estimación)

**Nuevos**
- `database/migrations/2026_08_08_000001_add_content_type_to_lms_activity_sections.php`
- `app/Observers/LmsActivityContentObserver.php`
- `app/Console/Commands/SyncSectionTypes.php`
- `tests/Feature/Lms/SectionContentTypeTest.php`

**Modificados**
- `app/Services/Lms/LmsContentClassifier.php` (+`classifyContent`, `classifySection`, `isMarkdownBody`)
- `app/Models/app/Academy/Lms/LmsActivitySection.php` (fillable + constantes + accesor)
- `app/Providers/AppServiceProvider.php` (registro del observer)
- `app/Livewire/Profesor/Lms/LessonWizard.php` (escribe el tipo al generar)
- `app/Services/Lms/GenerateIllustrationLesson.php` (tipo `svg` al generar)
- `resources/views/livewire/student/lms/lessons-print.blade.php` (clase aditiva)
- `resources/views/livewire/student/lms/_content-renderer.blade.php` (variante aditiva)
- `resources/views/livewire/student/lms/activity-view.blade.php` (badge opcional)
- `tests/Unit/Lms/LmsContentClassifierTest.php` (nuevos casos)
- `blueprint/estudiant/implementations.md` (registro de cambios)

---

## 13. Resumen de decisiones (ADR-lite)

1. **Campo único `content_type` VARCHAR NULL** en vez de ENUM de BD: los ENUM de MySQL son rígidos para evolucionar; la validación vive en el modelo (`CONTENT_TYPES`) y el clasificador.
2. **Caché denormalizada + observer** en vez de computar siempre: permite indexar, filtrar y reportear; el accesor defensivo cubre el drift.
3. **`mixed` como ciudadano de primera clase**: 53/359 secciones ya son mixtas; ocultarlas tras un único tipo sería mentira.
4. **Precedencia mermaid > svg > math > html > markdown > text**: refleja exactamente el orden con el que las vistas ya deciden hoy (P4), así no hay cambio de comportamiento perceptible.
