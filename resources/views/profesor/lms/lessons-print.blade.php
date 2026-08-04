{{-- ════════════════════════════════════════════════════════════════════════
     VISTA DE IMPRESIÓN DE LECCIONES LMS (reemplaza el PDF).
     Documento HTML autónomo: Mermaid y KaTeX se renderizan en el
     navegador (mermaidEmbed / x-lms.math-text). Imprimir / Guardar PDF
     con window.print() incluye los diagramas ya dibujados.
     ═══════════════════════════════════════════════════════════════════════ --}}
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
        .lesson{page-break-inside:avoid;break-inside:avoid-page;padding:10px 16px;margin-bottom:8px;}
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
        .section{margin-top:10px;page-break-inside:avoid;break-inside:avoid-page;}
        .section-head{display:flex;align-items:center;gap:8px;background:#e2e8f0;border:1px solid #cbd5e1;
                      padding:4px 8px;border-radius:4px;font-size:8pt;font-weight:700;color:#0f766e;
                      text-transform:uppercase;letter-spacing:0.3px;}
        .section-head .bar{width:4px;height:14px;background:#0d9488;border-radius:2px;}

        .content-block{padding:6px 8px;border:1px solid #e2e8f0;border-top:none;}
        .content-title{font-weight:700;color:#334155;font-size:8pt;margin-bottom:3px;}

        /* ── Imagen / SVG ── */
        .content-image{margin:4px 0;text-align:center;}
        .content-image svg{max-width:100%;height:auto;display:block;margin:0 auto;}

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
            /* Configuración de página para modo libro (horizontal, dos páginas por hoja) - MARGENES REDUCIDOS 20% */
            @page {
                size: landscape;
                margin: 1.2cm; /* 1.5cm * 0.8 = 1.2cm */
            }

            body{
                font-size:7pt;
                line-height:1.3;
                column-count: 2;
                column-gap: 1.2cm; /* 1.5cm * 0.8 = 1.2cm */
                column-fill: balance;
            }

            /* Evitar que los elementos se rompan de manera inapropiada */
            .lesson, .section, .content-block, .doc-head, .print-bar, .footer {
                break-inside: avoid-page;
                break-inside: avoid-column;
            }

            /* Asegurar que los encabezados de lección y sección no se separen de su contenido */
            .lesson-head, .lesson-meta, .section-head {
                break-after: avoid;
            }

            /* Ocultar barra de acciones */
            .print-bar{display:none;}

            /* Ajustar márgenes y paddings para mejor ajuste en columnas - REDUCCIÓN 20% */
            .lesson{padding:6px 10px;margin:0 0 5px 0;} /* 8px->6px, 12px->10px, 6px->5px */
            .doc-head{padding:8px 10px 6px;} /* 10px->8px, 12px->10px, 8px->6px */
            .footer{padding-top:3px;margin-top:6px;} /* 4px->3px, 8px->6px */

            /* Ajustar tamaños de fuente para mejor legibilidad en modo libro */
            .doc-head h1{font-size:11pt;}
            .doc-head h2{font-size:8pt;}
            .doc-head .sub{font-size:6pt;}
            .lesson-head .nnum{width:18px;height:18px;font-size:8pt;}
            .lesson-head .topic{font-size:9pt;}
            .lesson-head .estado{font-size:6pt;padding:1px 6px;}
            .lesson-meta{font-size:6pt;}
            .section-head{font-size:7pt;}
            .content-title{font-size:7pt;}
            .content p{font-size:6pt;}
            .content h1{font-size:9pt;}
            .content h2{font-size:8pt;}
            .content h3{font-size:7pt;}
            .content h4{font-size:6pt;}
            .content table th, .content table td{font-size:6pt;padding:1px 3px;}
            .content blockquote{padding:0 4px;margin:1px 0;}
            .mermaid-wrap{padding:4px;margin:3px 0;}
            .lesson-res{font-size:6pt;padding:3px 6px;}
            .footer{font-size:5pt;}
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
                            // Detección de tipo de contenido (idéntico al modal de vista previa completa)
                            $rawBody = $content['body'] ?? '';
                            $type = $content['type'] ?? 'TEXT';

                            // ─── IMAGE: SVG/ilustración ("Generar Imagen") — render
                            //     crudo, sin markdown ni sanitize. El sanitizador
                            //     elimina <svg>, así que NO puede pasar por él
                            //     (mismo criterio que slidePreviewContent()).
                            $isImage = ($type === 'IMAGE') || preg_match('/<svg\b/', $rawBody) === 1;

                            // ─── MERMAID: detectar por clase CSS o keyword inicial ──
                            $isMermaid = false;
                            $mermaidCode = '';
                            if (!$isImage) {
                                $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $rawBody) === 1;
                                if (!$isMermaid) {
                                    $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($rawBody)) === 1;
                                }
                                if ($isMermaid) {
                                    preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $rawBody, $m);
                                    $mermaidCode = trim(strip_tags($m[1] ?? ''));
                                    if (empty($mermaidCode)) {
                                        $mermaidCode = trim(strip_tags($rawBody));
                                    }
                                }
                            }
                        @endphp
                        <div class="content-block">
                            @if($content['title'])
                                <div class="content-title">{{ $content['title'] }}</div>
                            @endif
                            @if($isImage)
                                {{-- SVG/ilustración: crudo en el DOM (svg no pasa el sanitizador) --}}
                                <div class="content-image">{!! $rawBody !!}</div>
                            @elseif($isMermaid)
                                {{-- Diagrama Mermaid → wrapper Alpine mermaidEmbed() --}}
                                <div class="mermaid-wrap">
                                    <div wire:ignore x-data="mermaidEmbed()"
                                         data-mermaid-code="{{ $mermaidCode }}">
                                        <div x-ref="target" class="w-full"></div>
                                    </div>
                                </div>
                            @elseif($type === 'HTML')
                                {{-- HTML semántico: sanitizar y render directo (sin markdown) --}}
                                <div class="content">
                                    {!! app(\App\Services\Lms\LmsHtmlSanitizerService::class)->sanitize($rawBody) !!}
                                </div>
                            @else
                                {{-- TEXT / MATH: markdown (TEXT) o LaTeX (MATH) → math-text (KaTeX) --}}
                                @php
                                    $renderType = ($type === 'MATH') ? 'MATH' : 'TEXT';
                                    $renderedBody = app(\App\Services\Lms\LmsContentRendererService::class)->renderContentBody($rawBody, $renderType);
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

    <script>
        // Esperar a que todos los diagramas Mermaid estén renderizados antes de
        // permitir imprimir (si hay diagramas). Con timeout de seguridad.
        (function () {
            var btn = document.querySelector('.btn-print');
            if (!btn) return;
            var targets = document.querySelectorAll('.mermaid-wrap [x-ref="target"]');
            if (!targets.length) return; // sin mermaid: botón activo siempre
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = 'Renderizando diagramas…';
            var started = Date.now();
            var poll = setInterval(function () {
                var done = Array.prototype.every.call(targets, function (t) {
                    return t.querySelector('svg');
                });
                if (done || Date.now() - started > 10000) {
                    clearInterval(poll);
                    btn.disabled = false;
                    btn.innerHTML = original;
                }
            }, 200);
        })();
    </script>

</body>
</html>