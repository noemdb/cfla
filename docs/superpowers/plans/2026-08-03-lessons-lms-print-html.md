# Vista de Impresión HTML de Lecciones LMS — Plan de Implementación

> **Para agentic workers:** SUB-SKILL REQUERIDO: usar `superpowers:subagent-driven-development` (recomendado) o `superpowers:executing-plans` para implementar este plan tarea por tarea. Los pasos usan sintaxis de checkbox (`- [ ]`) para seguimiento.

**Objetivo:** Reemplazar la exportación de lecciones LMS del profesor a PDF (dompdf) por una página HTML autónoma de impresión donde Mermaid y KaTeX se renderizan en el navegador.

**Arquitectura:** Nueva ruta `lessons/print` con `LessonsPrintController` que reutiliza la query filtrada del controlador PDF actual y devuelve una vista Blade autónoma (sin layout de la app) que envuelve cada bloque Mermaid en `mermaidEmbed()` de Alpine. Se elimina todo el código del PDF (controlador, servicio Graphviz, vista y ruta).

**Tech Stack:** Laravel 10, Blade, Alpine.js (vía Livewire bundle), Mermaid 11 (Vite), KaTeX, Tailwind CSS.

## Global Constraints

- PHP 8.2 es la versión de producción — usar `php8.2` siempre (el `php` por defecto es 7.4.33).
- App en español (`es`); copia/UI en español.
- No se puede ejecutar JavaScript server-side (solo navegador).
- Alpine se carga SOLO vía `@livewireScripts` (NO desde `app.js`); `mermaidEmbed` y `_ensureMermaidReady` se registran en `app.js` vía `lms-student-preview.js`.
- Usar `php8.2 artisan test` para las pruebas.
- No conservar el PDF server-side ni Graphviz.

---

### Task 1: Controlador de impresión `LessonsPrintController`

**Files:**
- Create: `app/Http/Controllers/Profesor/Lms/LessonsPrintController.php`

**Interfaces:**
- Consumes: modelo `Activity`, `Profesor`, `Institucion` (mismos imports que el controlador PDF actual).
- Produces: método público `index(Request $request): \Illuminate\View\View` que retorna la vista `profesor.lms.lessons-print` con las variables `lessons`, `institucion`, `filters`, `profesor`, `fecha`.

- [ ] **Step 1: Crear el controlador**

Crea `app/Http/Controllers/Profesor/Lms/LessonsPrintController.php` con el siguiente contenido (basado en `LessonsPdfController` eliminando todo lo de dompdf):

```php
<?php

namespace App\Http\Controllers\Profesor\Lms;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Profesor;
use App\Models\app\Entity\Institucion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonsPrintController extends Controller
{
    /**
     * Página HTML autónoma de impresión con todas las lecciones que el
     * profesor está visualizando en el listado, respetando los filtros activos.
     * Los diagramas Mermaid y las matemáticas se renderizan en el navegador
     * (mermaid.js / KaTeX), por lo que el PDF generado con "Imprimir" los
     * incluye ya dibujados.
     */
    public function index(Request $request): View
    {
        $profesor = Profesor::where('user_id', auth()->id())->first();

        $query = Activity::whereHas('pevaluacion', function ($q) use ($profesor, $request) {
            $q->where('profesor_id', $profesor?->id);

            if ($request->filled('lapso')) {
                $q->where('lapso_id', $request->integer('lapso'));
            }
            if ($request->filled('pestudio')) {
                $q->whereHas('pensum', fn ($pq) => $pq->where('pestudio_id', $request->integer('pestudio')));
            }
            if ($request->filled('grado')) {
                $q->whereHas('pensum', fn ($pq) => $pq->where('grado_id', $request->integer('grado')));
            }
            if ($request->filled('seccion')) {
                $q->where('seccion_id', $request->integer('seccion'));
            }
        })->with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections' => fn ($q) => $q->withCount('contents')->orderBy('sort_order'),
            'lmsSections.contents' => fn ($q) => $q->orderBy('sort_order'),
            'lmsHtmlEmbeds' => fn ($q) => $q->where('is_visible', true),
            'lmsResources' => fn ($q) => $q->where('is_visible', true),
            'lmsLinks' => fn ($q) => $q->where('is_visible', true),
        ]);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', '%'.$search.'%')
                    ->orWhere('thematic', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $activities = $query->orderBy('finicial', 'desc')->get();

        $lessons = $activities->map(fn ($act) => $this->prepareLesson($act))->values();

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $filters = [
            'lapso' => $request->input('lapso'),
            'pestudio' => $request->input('pestudio'),
            'grado' => $request->input('grado'),
            'seccion' => $request->input('seccion'),
            'search' => $request->input('search'),
        ];

        return view('profesor.lms.lessons-print', compact(
            'lessons',
            'institucion',
            'filters',
            'profesor'
        ) + ['fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY')]);
    }

    /**
     * Normaliza una actividad en un arreglo plano con las secciones y su
     * contenido (body crudo, sin renderizar — lo renderiza el navegador).
     */
    private function prepareLesson(Activity $act): array
    {
        $estado = $act->lmsPublication?->status;

        return [
            'topic' => $act->topic ?? 'Actividad sin título',
            'thematic' => $act->thematic ?? '',
            'description' => $act->description ?? '',
            'asignatura' => $act->pevaluacion?->pensum?->asignatura?->name ?? '',
            'grado' => $act->pevaluacion?->pensum?->grado?->name ?? '',
            'seccion' => $act->pevaluacion?->seccion?->name ?? '',
            'lapso' => $act->pevaluacion?->lapso?->name ?? '',
            'finicial' => $act->finicial,
            'ffinal' => $act->ffinal,
            'estado' => $estado,
            'estado_label' => $this->estadoLabel($estado),
            'estado_class' => $this->estadoClass($estado),
            'section_count' => $act->lmsSections->count(),
            'content_count' => $act->lmsSections->sum(fn ($s) => $s->contents->count()),
            'has_lms' => $act->lmsSections->isNotEmpty()
                || $act->lmsResources->isNotEmpty()
                || $act->lmsLinks->isNotEmpty()
                || $act->lmsPublication !== null,
            'sections' => $act->lmsSections
                ->map(function ($s) use ($act) {
                    $items = $s->contents
                        ->map(fn ($c) => [
                            'title' => $c->title ?? '',
                            'body' => $c->body ?? '',
                            'type' => $c->type ?? 'TEXT',
                        ]);

                    // Embeds asociados a la sección (diagramas Mermaid, HTML…).
                    // Misma forma que los contenidos: la detección Mermaid se
                    // hace en la vista (keyword / div.mermaid) sobre `body`.
                    $embeds = $act->lmsHtmlEmbeds
                        ->where('section_id', $s->id)
                        ->map(fn ($e) => [
                            'title' => $e->title ?? '',
                            'body' => $e->html_content ?? '',
                            'type' => 'TEXT',
                        ]);

                    return [
                        'id' => $s->id,
                        'title' => $s->title ?? '',
                        'contents' => $items
                            ->concat($embeds)
                            ->filter(fn ($c) => trim((string) ($c['body'] ?? '')) !== '' || trim((string) $c['title']) !== '')
                            ->values(),
                    ];
                })
                ->filter(fn ($s) => $s['contents']->isNotEmpty() || trim((string) $s['title']) !== '')
                ->values(),
            'resources' => $act->lmsResources->pluck('display_name')->filter()->values(),
            'links' => $act->lmsLinks->map(fn ($l) => [
                'title' => $l->title ?? '',
                'url' => $l->url ?? '',
            ])->values(),
        ];
    }

    /**
     * Etiqueta legible para el estado de publicación.
     */
    private function estadoLabel(?string $estado): string
    {
        return match ($estado) {
            'PUBLISHED' => 'Publicado',
            'SCHEDULED' => 'Programado',
            'ARCHIVED' => 'Archivado',
            null => 'N.PUB',
            default => 'Borrador',
        };
    }

    /**
     * Clase CSS del estado para la vista de impresión.
     */
    private function estadoClass(?string $estado): string
    {
        return match ($estado) {
            'PUBLISHED' => 'estado-pub',
            'SCHEDULED' => 'estado-prog',
            'ARCHIVED' => 'estado-arc',
            null => 'estado-npub',
            default => 'estado-draft',
        };
    }
}
```

- [ ] **Step 2: Verificar sintaxis**

Run: `php8.2 -l app/Http/Controllers/Profesor/Lms/LessonsPrintController.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Profesor/Lms/LessonsPrintController.php
git commit -m "feat(lms): controlador de impresión HTML de lecciones
Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: Vista autónoma de impresión `lessons-print.blade.php`

**Files:**
- Create: `resources/views/profesor/lms/lessons-print.blade.php`

**Interfaces:**
- Consumes: variables de `LessonsPrintController::index` (`$lessons`, `$institucion`, `$filters`, `$profesor`, `$fecha`). Cada `$lesson` tiene: `topic`, `thematic`, `description`, `asignatura`, `grado`, `seccion`, `lapso`, `finicial`, `ffinal`, `estado`, `estado_label`, `estado_class`, `section_count`, `content_count`, `has_lms`, `sections` (cada una con `id`, `title`, `contents` donde cada content tiene `title` + `body` + `type` — forma uniforme para contenidos normales y embeds), `resources` (colección de strings), `links` (colección de `['title'=>..., 'url'=>...]`).
- Produces: documento HTML autónomo que usa `mermaidEmbed()` de Alpine y `x-lms.math-text`.

- [ ] **Step 1: Crear la vista**

Crea `resources/views/profesor/lms/lessons-print.blade.php`:

```blade
{{-- ══════════════════════════════════════════════════════════════════
     VISTA DE IMPRESIÓN DE LECCIONES LMS (reemplaza el PDF).
     Documento HTML autónomo: Mermaid y KaTeX se renderizan en el
     navegador (mermaidEmbed / x-lms.math-text). Imprimir / Guardar PDF
     con window.print() incluye los diagramas ya dibujados.
     ══════════════════════════════════════════════════════════════════ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecciones LMS · {{ $institucion?->name ?? 'Institución' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @livewireScripts

    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'DejaVu Sans',ui-sans-serif,system-ui,sans-serif;font-size:8.5pt;color:#1a1a2e;line-height:1.45;background:#fff;}

        /* ── Barra de acciones (oculta al imprimir) ── */
        .print-bar{position:sticky;top:0;z-index:50;display:flex;align-items:center;justify-content:space-between;gap:12px;
                   padding:8px 16px;background:#0f172a;color:#e2e8f0;border-bottom:1px solid #1e293b;}
        .print-bar .title{font-size:10pt;font-weight:700;letter-spacing:0.3px;}
        .print-bar .subtitle{font-size:7pt;color:#94a3b8;}
        .btn-print{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;border:1px solid #10b981;
                   background:#059669;color:#fff;font-size:8.5pt;font-weight:700;cursor:pointer;transition:background .15s;}
        .btn-print:hover{background:#047857;}

        /* ── Cabecera del documento ── */
        .doc-head{text-align:center;padding:14px 16px 10px;border-bottom:2px solid #0d9488;}
        .doc-head h1{font-size:13pt;color:#0d9488;letter-spacing:0.5px;}
        .doc-head h2{font-size:9pt;color:#374151;font-weight:700;}
        .doc-head .sub{font-size:7pt;color:#6b7280;margin-top:2px;}
        .doc-head .sep{color:#d1d5db;margin:0 4px;}

        /* ── Lección ── */
        .lesson{page-break-before:always;padding:10px 16px;}
        .lesson:first-child{page-break-before:auto;}
        .lesson-head{display:flex;align-items:center;gap:10px;background:#0d9488;color:#fff;
                     padding:5px 10px;border-radius:6px 6px 0 0;}
        .lesson-head .nnum{width:22px;height:22px;border-radius:6px;background:rgba(255,255,255,.15);
                           display:flex;align-items:center;justify-content:center;font-weight:800;font-size:9pt;flex-shrink:0;}
        .lesson-head .topic{font-weight:800;font-size:10pt;flex:1;}
        .lesson-head .estado{font-size:7pt;font-weight:700;padding:2px 8px;border-radius:999px;background:#f0fdfa;color:#0f766e;}

        .lesson-meta{display:flex;flex-wrap:wrap;gap:4px 14px;padding:5px 10px;background:#f0fdfa;
                     border:1px solid #99f6e4;border-top:none;border-radius:0 0 6px 6px;font-size:7pt;color:#374151;}
        .lesson-meta .lbl{color:#0d9488;font-weight:700;}
        .lesson-meta .dot{color:#99f6e4;}

        /* ── Secciones ── */
        .section{margin-top:10px;}
        .section-head{display:flex;align-items:center;gap:8px;background:#e2e8f0;border:1px solid #cbd5e1;
                      padding:4px 8px;border-radius:4px;font-size:8pt;font-weight:700;color:#0f766e;
                      text-transform:uppercase;letter-spacing:0.3px;}
        .section-head .bar{width:4px;height:14px;background:#0d9488;border-radius:2px;}

        .content-block{padding:6px 8px;border:1px solid #e2e8f0;border-top:none;}
        .content-title{font-weight:700;color:#334155;font-size:8pt;margin-bottom:3px;}

        /* ── Contenido (prose) ── */
        .content p{margin:3px 0;line-height:1.5;}
        .content h1,.content h2,.content h3,.content h4{margin:6px 0 3px;color:#0f766e;font-weight:700;}
        .content h1{font-size:11pt;} .content h2{font-size:10pt;} .content h3{font-size:9pt;} .content h4{font-size:8.5pt;}
        .content ul,.content ol{margin:3px 0;padding-left:16px;}
        .content li{margin:1px 0;}
        .content table{width:100%;border-collapse:collapse;margin:4px 0;}
        .content table th{background:#e2e8f0;padding:2px 6px;border:1px solid #94a3b8;font-size:7.5pt;text-align:left;}
        .content table td{padding:2px 6px;border:1px solid #cbd5e1;font-size:7.5pt;}
        .content blockquote{border-left:3px solid #0d9488;margin:4px 0;padding:1px 8px;color:#475569;background:#f0fdfa;}
        .content strong{font-weight:700;}
        .content a{color:#0d9488;word-wrap:break-word;}

        /* ── Mermaid ── */
        .mermaid-wrap{background:#f8fafc;border:1px solid #cbd5e1;border-radius:6px;padding:8px;margin:6px 0;}
        .mermaid-wrap svg{display:block;max-width:100%;height:auto;margin:0 auto;}
        .mermaid-wrap .mermaid-zoom-toolbar{display:none;} {{-- sin toolbar en impresión --}}

        /* ── Recursos / Enlaces ── */
        .lesson-res{padding:5px 10px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:4px;margin-top:8px;font-size:7pt;color:#334155;}
        .lesson-res .lbl{font-weight:700;color:#0d9488;}
        .lesson-res .link-sep{color:#cbd5e1;margin:0 4px;}

        /* ── Estados ── */
        .estado-pub{color:#059669;} .estado-prog{color:#0891b2;} .estado-draft{color:#d97706;}
        .estado-arc{color:#6b7280;} .estado-npub{color:#9ca3af;}

        /* ── Sin contenido ── */
        .no-content{padding:14px;text-align:center;color:#9ca3af;font-size:8pt;border:1px dashed #cbd5e1;border-radius:6px;margin-top:10px;}

        /* ── Footer ── */
        .footer{text-align:center;font-size:6.5pt;color:#6b7280;margin-top:10px;padding-top:6px;border-top:1px solid #e2e8f0;}

        @media print {
            .print-bar{display:none;}
            body{font-size:8pt;}
            .lesson{page-break-before:always;}
            .lesson:first-child{page-break-before:auto;}
            .section-head,.lesson-head{page-break-after:avoid;}
            .content-block{page-break-inside:avoid;}
        }
    </style>
</head>
<body class="lms-print">

    {{-- Barra de acciones --}}
    <div class="print-bar">
        <div>
            <div class="title">{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</div>
            <div class="subtitle">LECCIONES LMS · CONTENIDO COMPLETO</div>
        </div>
        <button class="btn-print" type="button" onclick="window.print()">
            🖨 Imprimir / Guardar PDF
        </button>
    </div>

    {{-- Cabecera del documento --}}
    <div class="doc-head">
        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
        <h2>LECCIONES LMS · CONTENIDO COMPLETO</h2>
        <div class="sub">
            {{ $profesor?->name ?? '' }} {{ $profesor?->lastname ?? '' }}
            <span class="sep">·</span> {{ $fecha }}
            @if(collect($filters)->filter()->isNotEmpty())
                <span class="sep">·</span> Filtros:
                @if($filters['lapso'])
                    Lapso {{ \App\Models\app\Academy\Lapso::find($filters['lapso'])?->name ?? '' }}
                @endif
                @if($filters['pestudio'])
                    <span class="sep">·</span> P.Estudio {{ \App\Models\app\Academy\Pestudio::find($filters['pestudio'])?->name ?? '' }}
                @endif
                @if($filters['grado'])
                    <span class="sep">·</span> Grado {{ \App\Models\app\Academy\Grado::find($filters['grado'])?->name ?? '' }}
                @endif
                @if($filters['seccion'])
                    <span class="sep">·</span> Sección {{ \App\Models\app\Academy\Seccion::find($filters['seccion'])?->name ?? '' }}
                @endif
                @if($filters['search'])
                    <span class="sep">·</span> "{{ $filters['search'] }}"
                @endif
            @endif
        </div>
    </div>

    @forelse($lessons as $i => $lesson)
        <div class="lesson">
            {{-- Cabecera de la lección --}}
            <div class="lesson-head">
                <span class="nnum">{{ $i + 1 }}</span>
                <span class="topic">{{ $lesson['topic'] }}</span>
                <span class="estado {{ $lesson['estado_class'] }}">{{ $lesson['estado_label'] }}</span>
            </div>

            {{-- Metadatos --}}
            <div class="lesson-meta">
                <span class="lbl">Asignatura:</span> {{ $lesson['asignatura'] ?: '—' }}
                @if($lesson['grado'])
                    <span class="dot">·</span> {{ $lesson['grado'] }}
                @endif
                @if($lesson['seccion'])
                    <span class="dot">·</span> Sec. {{ $lesson['seccion'] }}
                @endif
                @if($lesson['lapso'])
                    <span class="dot">·</span> {{ $lesson['lapso'] }}
                @endif
                <span class="dot">·</span>
                <span class="lbl">Fechas:</span>
                @if($lesson['finicial'])
                    {{ \Carbon\Carbon::parse($lesson['finicial'])->format('d/m') }}
                @endif
                al
                @if($lesson['ffinal'])
                    {{ \Carbon\Carbon::parse($lesson['ffinal'])->format('d/m') }}
                @endif
                @if($lesson['thematic'])
                    <span class="dot">·</span>
                    <span class="lbl">Eje:</span> {{ $lesson['thematic'] }}
                @endif
                @if($lesson['has_lms'])
                    <span class="dot">·</span>
                    {{ $lesson['section_count'] }} sec.
                    @if($lesson['content_count'] > 0)
                        · {{ $lesson['content_count'] }} cont.
                    @endif
                @endif
            </div>

            {{-- Secciones --}}
            @forelse($lesson['sections'] as $section)
                <div class="section">
                    <div class="section-head">
                        <span class="bar"></span>
                        <span>{{ $section['title'] ?: 'Sección sin título' }}</span>
                    </div>
                    @forelse($section['contents'] as $content)
                        @php
                            // Detección Mermaid uniforme (patrón _full-preview-modal):
                            // 1) <div class="… mermaid …">  o  2) keyword inicial.
                            $rawBody = $content['body'] ?? '';
                            $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $rawBody) === 1;
                            if (!$isMermaid) {
                                $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($rawBody)) === 1;
                            }
                            $mermaidCode = '';
                            if ($isMermaid) {
                                preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $rawBody, $m);
                                $mermaidCode = trim(strip_tags($m[1] ?? ''));
                                if (empty($mermaidCode)) {
                                    $mermaidCode = trim(strip_tags($rawBody));
                                }
                            }
                        @endphp
                        <div class="content-block">
                            @if($content['title'])
                                <div class="content-title">{{ $content['title'] }}</div>
                            @endif
                            @if($isMermaid)
                                {{-- Diagrama Mermaid → wrapper Alpine mermaidEmbed() --}}
                                <div class="mermaid-wrap">
                                    <div wire:ignore x-data="mermaidEmbed()"
                                         data-mermaid-code="{{ $mermaidCode }}">
                                        <div x-ref="target" class="w-full"></div>
                                    </div>
                                </div>
                            @else
                                @php
                                    $renderedBody = app(\App\Services\Lms\LmsContentRendererService::class)->renderContentBody($rawBody);
                                @endphp
                                <div class="content">
                                    <x-lms.math-text :content="$renderedBody" />
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="content-block" style="color:#9ca3af;font-style:italic;">Sin contenido en esta sección.</div>
                    @endforelse
                </div>
            @empty
                @if($lesson['has_lms'])
                    <div class="no-content">Sin secciones definidas.</div>
                @else
                    <div class="no-content">Lección sin contenido LMS.</div>
                @endif
            @endforelse

            {{-- Recursos / Enlaces --}}
            @if($lesson['resources']->isNotEmpty() || $lesson['links']->isNotEmpty())
                <div class="lesson-res">
                    @if($lesson['resources']->isNotEmpty())
                        <span class="lbl">Recursos:</span>
                        {{ $lesson['resources']->join(' · ') }}
                    @endif
                    @if($lesson['links']->isNotEmpty())
                        @if($lesson['resources']->isNotEmpty())
                            <span class="link-sep">|</span>
                        @endif
                        <span class="lbl">Enlaces:</span>
                        @foreach($lesson['links'] as $link)
                            {{ $link['title'] ?: $link['url'] }}
                            @if(!$loop->last); @endif
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="no-content">No hay lecciones que coincidan con los filtros aplicados.</div>
    @endforelse

    <div class="footer">
        {{ $institucion?->name ?? '' }}
        · {{ $lessons->count() }} lección{{ $lessons->count() === 1 ? '' : 'es' }}
        · Elaborado por: {{ auth()->user()?->username ?? 'Sistema' }} · {{ $fecha }}
    </div>

</body>
</html>
```

- [ ] **Step 2: Verificar que la vista compila**

Run: `php8.2 artisan view:cache`
Expected: `Compiled views cleared` + `Compiled views cached successfully` (sin errores de Blade)

- [ ] **Step 3: Commit**

```bash
git add resources/views/profesor/lms/lessons-print.blade.php
git commit -m "feat(lms): vista HTML de impresión de lecciones con Mermaid/KaTeX
Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 3: Ruta `lessons/print` y actualizar el botón del listado

**Files:**
- Modify: `routes/web.php:356-357` (bloque `profesors.lms.lessons.pdf`)
- Modify: `resources/views/livewire/profesor/lms/_list.blade.php:8-24` (botón "Exportar PDF")

**Interfaces:**
- Consumes: `LessonsPrintController@index` de la Task 1.
- Produces: ruta con nombre `app.profesors.lms.lessons.print`; el botón del listado navega a ella.

- [ ] **Step 1: Reemplazar la ruta del PDF**

En `routes/web.php`, dentro del bloque `Route::prefix('lms')->name('lms.')->group(...)` del profesor (líneas 349-358), reemplaza:

```php
            Route::get('/lessons/pdf', [\App\Http\Controllers\Profesor\Lms\LessonsPdfController::class, 'export'])
                 ->name('lessons.pdf');
```

por:

```php
            Route::get('/lessons/print', [\App\Http\Controllers\Profesor\Lms\LessonsPrintController::class, 'index'])
                 ->name('lessons.print');
```

- [ ] **Step 2: Actualizar el botón del listado**

En `resources/views/livewire/profesor/lms/_list.blade.php`, reemplaza el bloque del botón "Exportar PDF" (líneas 8-24):

```blade
                {{-- Botón: Exportar lecciones visibles a PDF --}}
                <a href="{{ route('app.profesors.lms.lessons.pdf', array_filter([
                            'lapso'   => $lapsoId,
                            'pestudio'=> $pestudioId,
                            'grado'   => $gradoId,
                            'seccion' => $seccionId,
                            'search'  => $search,
                        ])) }}"
                   target="_blank"
                   title="Exportar en un único PDF todas las lecciones que se están visualizando"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200
                          bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm border border-gray-200 dark:border-slate-600
                          hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-emerald-300 dark:hover:border-emerald-500/30">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                    <span class="hidden sm:inline">Exportar PDF</span>
                </a>
```

por:

```blade
                {{-- Botón: Ver / Imprimir lecciones visibles (HTML con Mermaid nativo) --}}
                <a href="{{ route('app.profesors.lms.lessons.print', array_filter([
                            'lapso'   => $lapsoId,
                            'pestudio'=> $pestudioId,
                            'grado'   => $gradoId,
                            'seccion' => $seccionId,
                            'search'  => $search,
                        ])) }}"
                   target="_blank"
                   title="Ver todas las lecciones en una página de impresión (Mermaid renderizado en el navegador)"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200
                          bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm border border-gray-200 dark:border-slate-600
                          hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-emerald-300 dark:hover:border-emerald-500/30">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h-13a2 2 0 01-2-2V5a2 2 0 012-2h9.5a1.5 1.5 0 011.5 1.5M17 17l3.5-3.5M17 17v-7a2 2 0 012-2h.5a1.5 1.5 0 011.5 1.5V17a2 2 0 01-2 2h-1.5"/></svg>
                    <span class="hidden sm:inline">Ver / Imprimir</span>
                </a>
```

- [ ] **Step 3: Verificar rutas**

Run: `php8.2 artisan route:list --path=profesors/lms/lessons`
Expected: muestra `app.profesors.lms.lessons.print` y NO `app.profesors.lms.lessons.pdf`.

- [ ] **Step 4: Commit**

```bash
git add routes/web.php resources/views/livewire/profesor/lms/_list.blade.php
git commit -m "feat(lms): ruta lessons/print reemplaza lessons/pdf y botón Ver/Imprimir
Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 4: Eliminar el código del PDF (dompdf + Graphviz)

**Files:**
- Delete: `app/Http/Controllers/Profesor/Lms/LessonsPdfController.php`
- Delete: `app/Services/Lms/LmsPdfContentRendererService.php`
- Delete: `resources/views/pdfs/profesor/lms/lessons.blade.php`
- Delete: `resources/views/pdfs/profesor/` (solo si queda vacía)
- Modify: `routes/web.php` (si quedó alguna referencia)

**Interfaces:**
- Consumes: nada — solo borrado.
- Produces: repositorio sin referencias a dompdf/Graphviz en LMS.

- [ ] **Step 1: Borrar los archivos del PDF**

Run:
```bash
git rm app/Http/Controllers/Profesor/Lms/LessonsPdfController.php
git rm app/Services/Lms/LmsPdfContentRendererService.php
git rm resources/views/pdfs/profesor/lms/lessons.blade.php
```

- [ ] **Step 2: Borrar la carpeta `pdfs/profesor` si quedó vacía**

Run:
```bash
ls resources/views/pdfs/profesor/ 2>/dev/null
if [ -z "$(ls -A resources/views/pdfs/profesor 2>/dev/null)" ]; then rmdir resources/views/pdfs/profesor; echo "removed"; fi
```
Expected: `removed` si estaba vacía, o se listan los archivos restantes.

- [ ] **Step 3: Buscar referencias residuales**

Run:
```bash
grep -rn "lessons.pdf\|LessonsPdfController\|LmsPdfContentRendererService\|pdfs.profesor" routes resources app --include='*.php' --include='*.blade.php' | grep -v vendor || echo "SIN REFERENCIAS"
```
Expected: `SIN REFERENCIAS`

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "refactor(lms): elimina exportación PDF (dompdf + Graphviz)

La vista de impresión HTML con Mermaid/KaTeX nativo reemplaza el PDF.
- Elimina LessonsPdfController
- Elimina LmsPdfContentRendererService (pipeline Graphviz)
- Elimina la vista pdfs/profesor/lms/lessons.blade.php
Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 5: Verificación end-to-end

**Files:**
- Test: ninguno nuevo — verificación manual y suite existente.

**Interfaces:**
- Consumes: todas las tareas 1-4.

- [ ] **Step 1: Compilar assets**

Run: `npm run build`
Expected: compilación de `resources/js/app.js` (mermaid, lms-student-preview) sin errores.

- [ ] **Step 2: Probar la página como profesor**

Run:
```bash
php8.2 artisan tinker --execute="
  auth()->loginUsingId(1);
  \$r = app('router')->getRoutes()->match(app('request')->create('/app/profesors/lms/lessons/print?lapso=1'));
  echo 'ROUTE: '.\$r->getName().PHP_EOL;
  echo 'CONTROLLER: '.get_class(\$r->getController()).PHP_EOL;
"
```
Expected: `ROUTE: app.profesors.lms.lessons.print` y `CONTROLLER: App\Http\Controllers\Profesor\Lms\LessonsPrintController`.

Nota: la autenticación real se hace en el navegador como `ccortez23`. El check de tinker confirma que la ruta y el controlador resuelven.

- [ ] **Step 3: Verificación visual en navegador**

Como `ccortez23`, abrir `GET http://cfla.local/app/profesors/lms/lessons/print?lapso=1`:
- [ ] Los diagramas Mermaid se ven como SVG (no como texto fuente).
- [ ] Las matemáticas (si hay) se ven con KaTeX.
- [ ] El botón "Imprimir / Guardar PDF" está arriba y al pulsarlo se abre el diálogo de impresión.
- [ ] Al guardar como PDF, los diagramas aparecen dibujados en el PDF.

- [ ] **Step 4: Ejecutar la suite de pruebas**

Run: `php8.2 artisan test`
Expected: suite existente pasa (sin roturas por el cambio de ruta).

---

## Self-Review

**1. Cobertura del spec:**
- Ruta nueva `lessons/print` + `LessonsPrintController` → Task 1, 3. ✅
- Vista autónoma sin layout de la app, `@vite` + `@livewireScripts` → Task 2. ✅
- Mermaid nativo vía `mermaidEmbed()` + detección keyword/div → Task 2. ✅
- Matemáticas con `x-lms.math-text` → Task 2. ✅
- Botón "Imprimir / Guardar PDF" + `@media print` → Task 2. ✅
- Embeds (detección uniforme con contenidos — keyword/div.mermaid sobre `body`) → Task 2. ✅
- Recursos y enlaces → Task 2. ✅
- Eliminación de `LessonsPdfController`, `LmsPdfContentRendererService`, vista PDF, ruta → Task 3, 4. ✅
- Verificación (`?lapso=1` como `ccortez23`, `php artisan test`, sin referencias) → Task 5. ✅

**2. Placeholder scan:** Sin "TBD"/"TODO". Todos los pasos tienen código completo. ✅

**3. Consistencia de tipos:** `LessonsPrintController@index` produce `lessons` con la forma exacta que consume la vista (Task 2). Todo `content` (contenidos normales y embeds) tiene `title`/`body`/`type`; la vista corre la detección Mermaid uniforme sobre `body` y cae a `renderContentBody` + `x-lms.math-text` si no es Mermaid. ✅
