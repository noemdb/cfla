{{-- �� ═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═
     VISTA DE IMPRESIÓN DE LECCIÓN LMS PARA ESTUDIANTE.
     Documento HTML autónomo: Mermaid y KaTeX se renderizan en el navegador
     (mermaidEmbed / x-lms.math-text). Imprimir / Guardar PDF con
     window.print() incluye los diagramas ya dibujados.
     SOLO LECTURA: sin formularios ni acciones de escritura.
     �� ═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═�═ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lección LMS · {{ $activity?->topic ?? 'Lección' }} · {{ $institucion?->name ?? 'Institución' }}</title>

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
        .lesson-head .topic{font-weight:800;font-size:10pt;flex:1;overflow-wrap:break-word;word-break:break-word;min-width:0;}
        .lesson-head .estado{font-size:7pt;font-weight:700;padding:2px 8px;border-radius:999px;background:#f0fdfa;color:#0f766e;flex-shrink:0;}

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
            /* Configuración de página para modo libro (horizontal, dos páginas por hoja) - MÁS COMPACTO */
            @page {
                size: landscape;
                margin: 0.9cm; /* 25% reduction from original 1.2cm for denser layout */
            }

            body{
                font-size:6pt; /* 8px as requested */
                line-height:1.2; /* Tighter line height for density */
                color:#1e293b;
            }

            /* Modo libro: las dos "páginas" de cada hoja horizontal son las dos
               columnas. El membrete (.doc-head) abre la columna 1 (primera
               página) y column-fill:auto llena la columna 1 por completo antes
               de pasar a la 2, con lo que no queda vacío tras el membrete. */
            .lessons-columns{
                column-count: 2;
                column-gap: 0.9cm; /* Reduced gap for denser columns */
                column-fill: auto; /* llenar la columna 1 antes de pasar a la 2 */
            }

            /* Flujo continuo: lecciones/secciones/contenido se parten en el
               límite entre columnas y páginas sin dejar huecos. Solo los
               encabezados quedan pegados a su contenido (break-after:avoid) y
               los bloques atómicos (membrete, footer, diagramas, figuras) no
               se parten. */
            .lesson, .section, .content-block {
                break-inside: auto;
                page-break-inside: auto;
            }

            .doc-head, .footer, .mermaid-wrap, .content-image {
                break-inside: avoid;
            }

            /* Asegurar que los encabezados de lección y sección no se separen de su contenido */
            .lesson-head, .lesson-meta, .section-head {
                break-after: avoid;
            }

            /* Ocultar barra de acciones */
            .print-bar{display:none;}

            /* Ajustar márgenes y paddings para máximo density - tipo libro técnico */
            .lesson{padding:4px 8px;margin:0 0 6px 0;} /* Further reduced padding */
            .doc-head{padding:6px 8px 3px;} /* Reduced padding */
            .footer{padding-top:2px;margin-top:4px;} /* Minimal footer spacing */

            /* Tipografía precisa según solicitud - tamaño específico para impresión */
            .doc-head h1{font-size:9.75pt; font-weight:800; letter-spacing:-0.3px; color:#0f766e; margin:0;} /* 13px */
            .doc-head h2{font-size:6pt; font-weight:600; color:#0f766e; margin:0;} /* 8px */
            .doc-head .sub{font-size:4.5pt; color:#64748b; margin-top:1px;} /* 6px */

            .lesson-head{display:flex;align-items:center;gap:4px;background:#0f766e;color:#fff;
                         padding:3px 6px;border-radius:4px 4px 0 0;margin-bottom:6px;}
            .lesson-head .nnum{width:14px;height:14px;border-radius:4px;background:rgba(255,255,255,.2);
                               display:flex;align-items:center;justify-content:center;font-weight:600;color:#fff;
                               font-size:4.5pt;flex-shrink:0;} /* 6px */
            .lesson-head .topic{font-size:7.5pt; font-weight:700; flex:1; min-width:0;
                                display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;} /* 10px · máx 2 líneas */
            .lesson-head .estado{font-size:4.5pt; font-weight:600; padding:1px 4px;
                                 border-radius:999px;} /* 6px */

            .lesson-meta{display:flex;flex-wrap:wrap;gap:2px 4px;padding:3px 6px;
                         background:#f0fdfa;border:1px solid #dcfce7;border-top:none;border-radius:0 0 4px 4px;
                         font-size:4.5pt; color:#374151;margin-bottom:8px;} /* 6px */
            .lesson-meta .lbl{color:#0f766e;font-weight:600;}
            .lesson-meta .dot{color:#dcfce7;}

            .section{margin:8px 0;padding:0 2px;break-inside:auto;}
            /* Tratamiento aditivo por tipo de sección (Spec "Campo content_type").
               null/mixed → sin clase extra → comportamiento idéntico al anterior. */
            .section--mermaid{break-inside:avoid;}
            .section--svg .content-image{page-break-inside:avoid;break-inside:avoid;margin:2px auto;}
            .section--math .content{font-size:6.25pt;}
            .section--image .content-image{margin:2px auto;}
            .section-head{display:flex;align-items:center;gap:2px;background:#f0fdfa;border:1px solid #ccfbf1;
                          padding:2px 4px;border-radius:2px;font-size:5.25pt; font-weight:700;
                          color:#0f766e;text-transform:uppercase;letter-spacing:0.2px;margin-bottom:4px;} /* 7px */
            .section-head .bar{width:2px;height:8px;background:#0f766e;border-radius:1px;}

            .content-block{margin:6px 0;padding:4px;border:1px solid #e2e8f0;border-top:none;}
            .content-title{font-weight:600;color:#334155;font-size:9pt;margin:0 0 4px;} /* 12px as requested */

            /* Contenido principal - tamanos especificados */
            .content p{font-size:6pt; line-height:1.2; margin:3px 0; color:#1e293b;} /* 8px as requested */
            .content h1{font-size:8.25pt; font-weight:800; color:#0f766e; margin:6px 0 4px;} /* 11px as requested */
            .content h2{font-size:6pt; font-weight:700; color:#0f766e; margin:4px 0 2px;} /* 8px as requested */
            .content h3{font-size:4.5pt; font-weight:600; color:#0f766e; margin:3px 0 1px;} /* 6px */
            .content h4{font-size:4.5pt; font-weight:600; color:#0f766e; margin:3px 0 1px;} /* 6px */

            .content ul,.content ol{margin:4px 0;padding-left:6px;}
            .content li{margin:2px 0;}
            .content table{width:100%;border-collapse:collapse;margin:6px 0;font-size:4.5pt;}
            .content table th{background:#e2e8f0;padding:2px 4px;border:1px solid #cbd5e1;font-size:4.5pt;text-align:left;}
            .content table td{padding:2px 4px;border:1px solid #e2e8f0;font-size:4.5pt;}
            .content blockquote{border-left:2px solid #0f766e;margin:4px 0;padding:0 4px;color:#475569;background:#f8fafc;font-style:italic;}
            .content strong{font-weight:700;}
            .content a{color:#0f766e;word-wrap:break-word;}

            /* Imagen / SVG */
            .content-image{margin:6px 0;text-align:center;}
            .content-image svg,.content-image img{max-width:100%;height:auto;display:block;margin:0 auto;border:1px solid #e2e8f0;border-radius:2px;}
            .content-image figcaption{font-size:4.5pt; font-weight:600; color:#1e293b; margin-top:3px;} /* 6px */

            /* Mermaid compacto. break-inside:avoid (B2) mantiene el diagrama
               entero en la misma página — no se parte a mitad del render. El
               marco (fondo/borde/padding) enmarca el diagrama DENTRO de su
               columna: ningún diagrama sale del flujo de 2 columnas (sin
               column-span), el ancho lo limita max-width:100% y el alto lo
               limita max-height (J2). */
            .mermaid-wrap{margin:6px 0;padding:4px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;break-inside:avoid;page-break-inside:avoid;}
            /* !important gana al style inline del SVG ("max-width:<naturalWidth>px")
               que mermaid.render() incrusta; sin esto un diagrama ancho desborda
               la columna en el PDF. overflow visible evita recortes al escalar. */
            .mermaid-wrap svg{display:block;max-width:100% !important;height:auto !important;margin:0 auto;overflow:visible !important;}
            .mermaid-wrap .mermaid-zoom-toolbar{display:none;}
            /* J2: acotar la ALTURA. Un diagrama grande (p.ej. graph TD de 13
               nodos / ~9 niveles) ocupaba más de una página en vertical.
               max-height en el SVG lo escala a la página respetando la
               proporción del viewBox (max-width ya lo anclaba al ancho).
               Cascada a propósito: el valor en pt es el fallback universal;
               el valor en vh gana si el motor lo soporta en print y se
               adapta al tamaño real de la página. overflow:hidden en el marco
               es la red de seguridad: recorta si el escalado no llegara a
               aplicarse. */
            .mermaid-wrap{position:relative;overflow:hidden;border-color:#94a3b8;}
            .mermaid-wrap svg{max-height:430pt !important;max-height:70vh !important;}

            /* Recursos / Enlaces */
            .lesson-res{margin-top:6px;padding:4px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;
                       font-size:4.5pt; color:#334155;} /* 6px */
            .lesson-res .lbl{font-weight:600;color:#0f766e;}
            .lesson-res .link-sep{color:#cbd5e1;margin:0 1px;}

            /* Estados */
            .estado-pub{background:#dcfce7;color:#166534;}
            .estado-prog{background:#dbeafe;color:#1e40af;}
            .estado-draft{background:#fed7aa;color:#9a3412;}
            .estado-arc{background:#e5e7eb;color:#6b7280;}
            .estado-npub{background:#f3f4f6;color:#6b7280;}

            /* Sin contenido */
            .no-content{padding:6px;text-align:center;color:#64748b;font-size:4.5pt;
                       border:1px dashed #e2e8f0;border-radius:3px;margin-top:6px;}

            /* Footer */
            .footer{text-align:center;font-size:4.5pt; color:#64748b;margin-top:6px;
                    padding-top:2px;border-top:1px solid #e2e8f0;} /* 6px as requested */
        }
    </style>
</head>
<body class="lms-print">

    @php
        // Nombre del estudiante para el membrete y el pie de página.
        $__estudiante = auth()->user()?->estudiant?->full_name
            ?? auth()->user()?->name
            ?? 'Estudiante';

        // Footer dinámico (P7): secciones y contenidos visibles reales.
        $__secciones  = $activity ? $activity->lmsSections->where('is_visible', true)->count() : 0;
        $__contenidos = $activity
            ? $activity->lmsSections
                ->where('is_visible', true)
                ->sum(fn ($s) => $s->visibleContents->count())
            : 0;
    @endphp

    {{-- Barra de acciones --}}
    <div class="print-bar">
        <div>
            <div class="title">{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</div>
            <div class="subtitle">LECCIÓN LMS · CONTENIDO COMPLETO</div>
        </div>
        <button id="btn-print" class="btn-print" type="button" onclick="handlePrint()" aria-label="Imprimir o guardar PDF">
            �� 🖨 Imprimir / Guardar PDF
        </button>
    </div>

    @if(!$activity)
    {{-- Cabecera del documento (caso sin actividad) --}}
    <div class="doc-head">
        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
        <h2>LECCIÓN LMS · CONTENIDO COMPLETO</h2>
        <div class="sub">
            {{ $__estudiante }}
            <span class="sep">·</span> {{ $fecha }}
        </div>
    </div>

        <div class="no-content">Esta lección no está disponible o no tienes permiso para verla.</div>
    @else
    {{-- Modo libro: el membrete abre la columna 1 (primera "página" de la hoja
         horizontal) y la lección fluye de forma continua; el footer cierra. --}}
    <div class="lessons-columns">
        {{-- Cabecera del documento (dentro del flujo de columnas) --}}
        <div class="doc-head">
            <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
            <h2>LECCIÓN LMS · CONTENIDO COMPLETO</h2>
            <div class="sub">
                {{ $__estudiante }}
                <span class="sep">·</span> {{ $fecha }}
                @if($activity->pevaluacion?->lapso)
                    <span class="sep">·</span> Lapso {{ $activity->pevaluacion?->lapso?->name }}
                @endif
                @if($activity->pevaluacion?->pensum?->asignatura)
                    <span class="sep">·</span> Asignatura {{ $activity->pevaluacion?->pensum?->asignatura?->name }}
                @endif
                @if($activity->pevaluacion?->grado)
                    <span class="sep">·</span> Grado {{ $activity->pevaluacion?->grado?->name }}
                @endif
                @if($activity->pevaluacion?->seccion)
                    <span class="sep">·</span> Sección {{ $activity->pevaluacion?->seccion?->name }}
                @endif
            </div>
        </div>

        @php
            $i = 0; // Índice de la lección (siempre 0 para una sola lección)
        @endphp
        <div class="lesson">
            {{-- Cabecera de la lección --}}
            <div class="lesson-head">
                <span class="nnum">{{ $i + 1 }}</span>
                <span class="topic">{{ $activity->topic }}</span>
                <span class="estado {{ $estadoClass }}">{{ $estadoLabel }}</span>
            </div>

            {{-- Metadatos --}}
            <div class="lesson-meta">
                <span class="lbl">Asignatura:</span> {{ $activity->pevaluacion?->pensum?->asignatura?->name ?: '—' }}
                @if($activity->pevaluacion?->profesor)
                    <span class="dot">·</span>
                    <span class="lbl">Profesor:</span> {{ $activity->pevaluacion?->profesor?->name }}
                @endif
                @if($activity->pevaluacion?->grado)
                    <span class="dot">·</span> {{ $activity->pevaluacion?->grado?->name }}
                @endif
                @if($activity->pevaluacion?->seccion)
                    <span class="dot">·</span> Sec. {{ $activity->pevaluacion?->seccion?->name }}
                @endif
                @if($activity->pevaluacion?->lapso)
                    <span class="dot">·</span> {{ $activity->pevaluacion?->lapso?->name }}
                @endif
                <span class="dot">·</span>
                <span class="lbl">Fechas:</span>
                @if($activity->finicial)
                    {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m') }}
                @endif
                al
                @if($activity->ffinal)
                    {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m') }}
                @endif
                @if($activity->thematic)
                    <span class="dot">·</span>
                    <span class="lbl">Eje:</span> {{ $activity->thematic }}
                @endif
            </div>

            {{-- Secciones --}}
            @forelse($activity->lmsSections->where('is_visible', true) as $section)
                <div class="section section--{{ $section->content_type ?? 'none' }}">
                    <div class="section-head">
                        <span class="bar"></span>
                        <span>{{ $section->title ?: 'Sección sin título' }}</span>
                    </div>
                    @forelse($section->visibleContents as $content)
                        @php
                            // Detección de tipo de contenido — clasificador único (P4):
                            // LmsContentClassifier (misma lógica que director/preview).
                            $rawBody  = $content->body ?? '';
                            $type     = $content->type ?? 'TEXT';
                            $__cls    = app(\App\Services\Lms\LmsContentClassifier::class);

                            // ─── IMAGE: SVG/ilustración ("Generar Imagen") — render
                            //     crudo, sin sanitizar (el sanitizador elimina <svg>).
                            $isImage  = $__cls->isImageBody($type, $rawBody);

                            // ─── MERMAID: detectar por clase CSS o keyword inicial ──
                            $isMermaid = false;
                            $mermaidCode = '';
                            if (!$isImage) {
                                $isMermaid = $__cls->isMermaidBody($rawBody);
                                if ($isMermaid) {
                                    // A1: conserva <br/> de labels multi-línea.
                                    $mermaidCode = $__cls->extractMermaidCode($rawBody);
                                }
                            }
                        @endphp
                        <div class="content-block">
                            @if($content->title && ! $isImage && $type !== 'HTML' && $type !== 'MATH' && trim($rawBody) !== '')
                                <div class="content-title">{{ $content->title }}</div>
                            @endif
                            @if($isImage)
                                {{-- SVG/ilustración: crudo en el DOM. Si el contenido
                                     IMAGE tiene un media apuntando a un archivo y el
                                     body no es un svg, se muestra el archivo. --}}
                                @if($content->media?->public_url && ! Str::contains($rawBody, '<svg'))
                                    <div class="content-image">
                                        <img src="{{ $content->media->public_url }}" alt="{{ $content->title ?: 'Imagen' }}">
                                    </div>
                                @else
                                    <div class="content-image">{!! app(\App\Services\Lms\LmsSvgRepairService::class)->renderImage($rawBody) !!}</div>
                                @endif
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
                <div class="no-content">Lección sin contenido LMS.</div>
            @endforelse

            {{-- Recursos / Enlaces --}}
            @php
                $resources = $activity->lmsResources->where('is_visible', true);
                $links     = $activity->lmsLinks->where('is_visible', true);
            @endphp
            @if($resources->isNotEmpty() || $links->isNotEmpty())
                <div class="lesson-res">
                    @if($resources->isNotEmpty())
                        <span class="lbl">Recursos:</span>
                        {{ $resources->map(fn ($r) => $r->display_name ?: $r->media?->original_name ?: 'Archivo')->join(' · ') }}
                    @endif
                    @if($links->isNotEmpty())
                        @if($resources->isNotEmpty())
                            <span class="link-sep">|</span>
                        @endif
                        <span class="lbl">Enlaces:</span>
                        @foreach($links as $link)
                            {{ $link->title ?: $link->url }}
                            @if(!$loop->last); @endif
                        @endforeach
                    @endif
                </div>
            @endif
        </div>

        <div class="footer">
            {{ $institucion?->name ?? '' }}
            · {{ $__secciones }} {{ $__secciones === 1 ? 'sección' : 'secciones' }}
            · {{ $__contenidos }} {{ $__contenidos === 1 ? 'contenido' : 'contenidos' }}
            · Elaborado por: {{ $__estudiante }} · {{ $fecha }}
        </div>
    </div>
    @endif

    <script>
        // Esperar a que todos los diagramas Mermaid alcancen un estado terminal
        // ('ok' o 'error') antes de imprimir. Cada wrapper mermaidEmbed() marca
        // data-mermaid-state al terminar, de modo que un diagrama con error no
        // bloquee la espera y un diagrama en render no imprima SVGs en blanco.
        function handlePrint() {
            var btn = document.querySelector('.btn-print');
            if (!btn) return;
            var roots = document.querySelectorAll('[data-mermaid-code]');
            if (!roots.length) {
                // sin mermaid: imprimir directamente
                'print' in window && window.print();
                return;
            }

            var original = btn.innerHTML;
            btn.disabled = true;
            var total = roots.length;
            var started = Date.now();
            var poll = setInterval(function () {
                var done = 0;
                for (var i = 0; i < roots.length; i++) {
                    var s = roots[i].getAttribute('data-mermaid-state');
                    if (s === 'ok' || s === 'error') done++;
                }
                btn.textContent = 'Renderizando diagramas… (' + done + '/' + total + ')';
                if (done === total || Date.now() - started > 30000) {
                    clearInterval(poll);
                    btn.disabled = false;
                    btn.innerHTML = original;
                    'print' in window && window.print();
                }
            }, 150);
        }
    </script>

</body>
</html>