@props([
    'preview'     => [],
    'closeMethod' => 'closeListStudentPreview',
    'wireKey'     => 'student-preview',
])

@php
    $pid = $preview['activity_id'] ?? $wireKey;

    // ── Procesamiento autónomo de html_embeds ─────────────────
    // Detecta diagramas Mermaid en html_content sin depender de
    // que el componente padre haya asignado is_mermaid.
    $previewHtmlEmbeds = collect($preview['html_embeds'] ?? [])->map(function ($embed) {
        if (!empty($embed['is_mermaid'])) return $embed;

        $content = trim($embed['html_content'] ?? '');

        // 1. ¿Empieza con palabra clave Mermaid?
        if (preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', $content)) {
            $embed['is_mermaid'] = true;
            return $embed;
        }

        // 2. Formato legacy: data-mermaid-code
        if (preg_match('/data-mermaid-code="([^"]*)"/', $content)) {
            $embed['is_mermaid'] = true;
            return $embed;
        }

        // 3. Formato legacy: <div class="mermaid">...</div>
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $content, $m)) {
            $inner = trim(strip_tags($m[1]));
            if (preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', $inner)) {
                $embed['is_mermaid'] = true;
                return $embed;
            }
        }

        $embed['is_mermaid'] = false;
        return $embed;
    });
@endphp

<div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="student-preview-{{ $pid }}">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md"
         wire:click="{{ $closeMethod }}"></div>

    {{-- Modal panel --}}
    <div class="relative min-h-screen flex flex-col items-center p-4 pt-10"
         x-data="lessonPreviewSwiper">
        <div class="w-full max-w-7xl bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col flex-1 min-h-0">
            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold uppercase tracking-wider">Vista del Estudiante</h2>
                        <p class="text-xs text-emerald-100/80">Así verán los estudiantes la lección</p>
                    </div>
                </div>
                <button wire:click="{{ $closeMethod }}"
                        class="p-2 hover:bg-white/10 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Swiper body --}}
            <div class="w-full bg-stone-50 swiper overflow-hidden"
                 x-ref="swiperContainer">
                <div class="swiper-wrapper">
                    {{-- ═══════ SLIDE 1: PORTADA INSTITUCIONAL ═══════ --}}
                    <div class="swiper-slide overflow-y-auto w-full h-auto p-6 md:p-10 flex flex-col min-h-[65vh] bg-stone-50">
                        {{-- Letterhead --}}
                        <div class="shrink-0">
                            <div class="h-[3px] w-24 rounded-full bg-amber-400 mb-5"></div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 md:gap-3">
                                    <img src="{{ asset('image/avatar/uecfla.jpg') }}" alt=""
                                         class="w-10 h-10 md:w-14 md:h-14 object-contain rounded-full ring-1 ring-stone-200/80 shrink-0">
                                    <div>
                                        <h2 class="text-sm md:text-base font-semibold text-stone-800 leading-tight tracking-tight">
                                            {{ $preview['institution'] ?: 'U.E. Colegio Fray Luis Amigó' }}
                                        </h2>
                                        <p class="text-[11px] text-stone-400 font-medium mt-0.5">
                                            @if($preview['institution_city'])
                                                {{ $preview['institution_city'] }}
                                                @if($preview['institution_rif']) · @endif
                                            @endif
                                            @if($preview['institution_rif'])
                                                RIF {{ $preview['institution_rif'] }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="hidden sm:block text-right shrink-0">
                                    <p class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">
                                        {{ $preview['plan_estudio'] ?: 'Plan de Estudio' }}
                                    </p>
                                    @if($preview['plan_estudio_code'])
                                        <p class="text-[10px] text-stone-400 mt-0.5">Cód. {{ $preview['plan_estudio_code'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="border-t border-stone-200 mt-4 mb-6"></div>
                        </div>

                        {{-- Central content --}}
                        <div class="flex-1 flex flex-col items-center justify-center min-h-0 py-2 md:py-6">
                            <div class="flex flex-wrap items-center justify-center gap-x-2 gap-y-1 mb-5">
                                <span class="px-2.5 py-0.5 text-[11px] font-semibold text-stone-600 bg-stone-100 rounded-md border border-stone-200/60">{{ $preview['pensum'] ?: $preview['subject'] }}</span>
                                <span class="text-stone-300 text-[10px]">/</span>
                                <span class="text-xs text-stone-500">{{ $preview['grado'] }} · {{ $preview['seccion'] }}</span>
                                @if($preview['lapso'])
                                    <span class="text-stone-300 text-[10px]">/</span>
                                    <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200/60">{{ $preview['lapso'] }}</span>
                                @endif
                            </div>

                            <h1 class="text-lg md:text-lg font-bold text-stone-900 leading-tight text-center w-full">
                                {{ $preview['title'] }}
                            </h1>

                            @if($preview['description'])
                                <p class="text-sm md:text-base text-stone-500 mt-2.5 w-full text-center leading-relaxed">
                                    {{ $preview['description'] }}
                                </p>
                            @endif

                            @if($preview['thematic'] || $preview['references'])
                                <div class="mt-5 space-y-1 text-center w-full">
                                    @if($preview['thematic'])
                                        <p class="text-[11px] text-stone-400">
                                            <span class="font-medium text-stone-500">Tejido temático:</span>
                                            {{ $preview['thematic'] }}
                                        </p>
                                    @endif
                                    @if($preview['references'])
                                        <p class="text-[11px] text-stone-400">
                                            <span class="font-medium text-stone-500">Ref. teórico-prácticos:</span>
                                            {{ $preview['references'] }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($preview['start_date'])
                                <div class="mt-5 flex items-center justify-center gap-1.5 text-xs text-stone-400">
                                    <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($preview['start_date'])->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($preview['end_date'])->format('d/m/Y') }}</span>
                                    @php
                                        $start = \Carbon\Carbon::parse($preview['start_date']);
                                        $end   = \Carbon\Carbon::parse($preview['end_date']);
                                        $days  = $start->diffInDays($end) + 1;
                                    @endphp
                                    <span class="text-stone-300 mx-0.5">·</span>
                                    <span>{{ $days }} día{{ $days !== 1 ? 's' : '' }}</span>
                                    @if($preview['activity_status'])
                                        <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 rounded">APROBADO</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Footer info --}}
                        <div class="shrink-0">
                            <div class="border-t border-stone-200 pt-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1">Curso</p>
                                        <p class="text-stone-700 font-medium">{{ $preview['grado'] }} · {{ $preview['seccion'] }}</p>
                                        @if($preview['seccion_students'])
                                            <p class="text-[11px] text-stone-400">{{ $preview['seccion_students'] }} estudiantes</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1">Asignatura</p>
                                        <p class="text-stone-700 font-medium">{{ $preview['pensum'] ?: $preview['subject'] }}</p>
                                        @if($preview['asignatura_hours'])
                                            <p class="text-[11px] text-stone-400">{{ $preview['asignatura_hours'] }} h/sem · Cód. {{ $preview['asignatura_code'] ?? '' }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1">Periodo</p>
                                        <p class="text-stone-700 font-medium">{{ $preview['periodo'] ?: $preview['plan_educativo'] }}</p>
                                        @if($preview['lapso_finicial'])
                                            <p class="text-[11px] text-stone-400">{{ \Carbon\Carbon::parse($preview['lapso_finicial'])->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($preview['lapso_ffinal'])->format('d/m/Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════ SECTION SLIDES ═══════ --}}
                    @forelse($preview['sections'] ?? [] as $section)
                        <div class="swiper-slide overflow-y-auto w-full h-auto p-8">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                                <h2 class="text-lg font-bold text-slate-800">{{ $section['title'] }}</h2>
                            </div>
                            @foreach($section['contents'] ?? [] as $content)
                                @php
                                    $rawBody = $content['body'] ?? '';
                                    $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $rawBody) === 1;
                                    if (!$isMermaid) {
                                        $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($rawBody)) === 1;
                                    }
                                @endphp
                                @if(($content['title'] ?? null))
                                    <div class="flex items-start gap-2 mb-2">
                                        <span class="w-0.5 h-5 bg-emerald-500 rounded-full mt-1 shrink-0"></span>
                                        <h3 class="text-sm font-bold text-slate-800 leading-snug">{{ $content['title'] }}</h3>
                                    </div>
                                @endif
                                @if($isMermaid)
                                    @php
                                        preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $rawBody, $m);
                                        $mermaidCode = trim(strip_tags($m[1] ?? ''));
                                        if (empty($mermaidCode)) {
                                            $mermaidCode = trim(strip_tags($rawBody));
                                        }
                                    @endphp
                                    <div wire:ignore x-data="mermaidEmbed()"
                                         data-mermaid-code="{{ $mermaidCode }}"
                                         data-mermaid-delay
                                         class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200">
                                        <div x-ref="target" class="w-full"></div>
                                    </div>
                                @else
                                    <div class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                        {!! $rawBody !!}
                                    </div>
                                @endif
                            @endforeach

                            {{-- HTML Embeds vinculados a esta sección --}}
                            @php
                                $sectionEmbeds = $previewHtmlEmbeds->where('section_id', $section['id']);
                            @endphp
                            @if($sectionEmbeds->count() > 0)
                                <div class="space-y-2 pt-2">
                                    @foreach($sectionEmbeds as $embed)
                                        <div class="p-4 bg-fuchsia-50 border border-fuchsia-100 rounded-lg html-embed-item">
                                            @if(!empty($embed['title']))
                                                <h4 class="text-sm font-semibold text-fuchsia-800 mb-2">{{ $embed['title'] }}</h4>
                                            @endif
                                            <div class="text-sm text-slate-700 prose prose-sm max-w-none html-embed-content">
                                                @if($embed['is_mermaid'] ?? false)
                                                    <div wire:ignore x-data="mermaidEmbed()"
                                                         data-mermaid-code="{{ $embed['html_content'] }}"
                                                         data-mermaid-delay
                                                         class="w-full">
                                                        <div x-ref="target" class="w-full"></div>
                                                    </div>
                                                @else
                                                    {!! $embed['html_content'] !!}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Recursos vinculados a esta sección --}}
                            @php
                                $secResources = collect($preview['resources'] ?? [])->where('section_id', $section['id'])->values()->all();
                                $secLinks = collect($preview['links'] ?? [])->where('section_id', $section['id'])->values()->all();
                            @endphp
                            @if(count($secResources) > 0)
                                <div class="border-t border-slate-200 pt-3 mt-2 space-y-2">
                                    @foreach($secResources as $res)
                                        @if(str_starts_with($res['media']['mime_type'] ?? '', 'image/'))
                                            <div class="rounded-lg overflow-hidden border border-slate-200 bg-white resource-image-wrap">
                                                <img src="{{ $res['media']['public_url'] }}" alt="{{ $res['display_name'] }}"
                                                     onerror="this.closest('.resource-image-wrap')?.querySelector('.image-fallback')?.classList?.remove('hidden'); this.classList.add('hidden')"
                                                     class="w-full h-auto max-h-80 object-contain bg-slate-50">
                                                <div class="image-fallback hidden">
                                                    <div class="flex items-center gap-3 p-3 bg-slate-100">
                                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                            <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between px-3 py-2 bg-slate-50 border-t border-slate-100">
                                                    <span class="text-xs text-slate-600 truncate">{{ $res['display_name'] }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 p-3 bg-slate-100 rounded-lg border border-slate-200">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                    <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                </div>
                                                @if($preview['allow_downloads'] ?? false)
                                                    <span class="ml-auto text-xs text-emerald-600 font-medium shrink-0">Descargar</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if(count($secLinks) > 0)
                                <div class="space-y-2 pt-2">
                                    @foreach($secLinks as $link)
                                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-blue-800 truncate">{{ $link['title'] }}</p>
                                                <p class="text-xs text-blue-500 truncate">{{ $link['url'] }}</p>
                                            </div>
                                            <span class="ml-auto text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded shrink-0">{{ $link['link_type'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="swiper-slide overflow-y-auto w-full h-auto p-8">
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                <p class="text-xs text-slate-400 mt-1">Esta actividad no tiene secciones visibles para estudiantes.</p>
                            </div>
                        </div>
                    @endforelse

                    {{-- ═══════ SLIDE: RECURSOS NO VINCULADOS ═══════ --}}
                    @php
                        $unlinkedResources = collect($preview['resources'] ?? [])->filter(fn($r) => empty($r['section_id']))->values()->all();
                        $unlinkedLinks = collect($preview['links'] ?? [])->filter(fn($l) => empty($l['section_id']))->values()->all();
                        $unlinkedEmbeds = $previewHtmlEmbeds->filter(fn($e) => empty($e['section_id']))->values()->all();
                    @endphp
                    @if(count($unlinkedResources) > 0)
                        <div class="swiper-slide overflow-y-auto w-full h-auto p-8">
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Recursos descargables
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($unlinkedResources as $res)
                                    @if(str_starts_with($res['media']['mime_type'] ?? '', 'image/'))
                                        <div class="rounded-lg overflow-hidden border border-slate-200 bg-white resource-image-wrap">
                                            <img src="{{ $res['media']['public_url'] }}" alt="{{ $res['display_name'] }}"
                                                 onerror="this.closest('.resource-image-wrap')?.querySelector('.image-fallback')?.classList?.remove('hidden'); this.classList.add('hidden')"
                                                 class="w-full h-48 object-cover">
                                            <div class="image-fallback hidden">
                                                <div class="flex items-center gap-3 p-3 bg-slate-100">
                                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                        <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-3 py-2 bg-slate-50 border-t border-slate-100">
                                                <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3 p-3 bg-slate-100 rounded-lg border border-slate-200">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                            </div>
                                            @if($preview['allow_downloads'] ?? false)
                                                <span class="ml-auto text-xs text-emerald-600 font-medium shrink-0">Descargar</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══════ SLIDE: ENLACES NO VINCULADOS ═══════ --}}
                    @if(count($unlinkedLinks) > 0)
                        <div class="swiper-slide overflow-y-auto w-full h-auto p-8">
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Enlaces de interés
                            </h3>
                            <div class="space-y-2">
                                @foreach($unlinkedLinks as $link)
                                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-blue-800 truncate">{{ $link['title'] }}</p>
                                            <p class="text-xs text-blue-500 truncate">{{ $link['url'] }}</p>
                                        </div>
                                        <span class="ml-auto text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded shrink-0">{{ $link['link_type'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══════ SLIDE: HTML EMBEDS NO VINCULADOS ═══════ --}}
                    @if(count($unlinkedEmbeds) > 0)
                        <div class="swiper-slide overflow-y-auto w-full h-auto p-8">
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                                HTML Embeds
                            </h3>
                            <div class="space-y-3">
                                @foreach($unlinkedEmbeds as $embed)
                                    <div class="p-4 bg-fuchsia-50 border border-fuchsia-100 rounded-lg html-embed-item">
                                        @if(!empty($embed['title']))
                                            <h4 class="text-sm font-semibold text-fuchsia-800 mb-2">{{ $embed['title'] }}</h4>
                                        @endif
                                        <div class="text-sm text-slate-700 prose prose-sm max-w-none html-embed-content">
                                            @if($embed['is_mermaid'] ?? false)
                                                <div wire:ignore x-data="mermaidEmbed()"
                                                     data-mermaid-code="{{ $embed['html_content'] }}"
                                                     data-mermaid-delay
                                                     class="w-full">
                                                    <div x-ref="target" class="w-full"></div>
                                                </div>
                                            @else
                                                {!! $embed['html_content'] !!}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══════ SLIDE: RESUMEN ═══════ --}}
                    @php
                        $totalSections = count($preview['sections'] ?? []);
                        $totalBlocks = collect($preview['sections'] ?? [])->sum(fn($s) => count($s['contents'] ?? []));
                        $totalResources = count($preview['resources'] ?? []);
                        $totalEmbeds = count($previewHtmlEmbeds);
                        $totalLinks = count($preview['links'] ?? []);
                    @endphp
                    <div class="swiper-slide overflow-y-auto w-full h-auto p-4 md:p-6">
                        <div class="max-w-3xl mx-auto space-y-3">
                            {{-- Header --}}
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-stone-800">Resumen de la lección</h3>
                                    <p class="text-[11px] text-stone-500">{{ $preview['pensum'] ?: $preview['subject'] }} · {{ $preview['grado'] }} {{ $preview['seccion'] }}</p>
                                </div>
                            </div>

                            {{-- Title + Description + Thematic/References --}}
                            <div class="bg-white rounded-lg border border-stone-200 p-3 space-y-1">
                                <h4 class="text-sm font-bold text-stone-800 leading-snug">{{ $preview['title'] }}</h4>
                                @if($preview['description'])
                                    <p class="text-xs text-stone-500 leading-relaxed">{{ $preview['description'] }}</p>
                                @endif
                                @if($preview['thematic'] || $preview['references'])
                                    <div class="pt-1.5 space-y-0.5 text-xs text-stone-400 border-t border-stone-100">
                                        @if($preview['thematic'])
                                            <p><span class="font-medium text-stone-500">Tejido temático:</span> {{ $preview['thematic'] }}</p>
                                        @endif
                                        @if($preview['references'])
                                            <p><span class="font-medium text-stone-500">Ref. teórico-prácticos:</span> {{ $preview['references'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Teaching structure --}}
                            @if($preview['has_teaching_structure'] ?? false)
                                <div class="bg-white rounded-lg border border-stone-200 overflow-hidden">
                                    <div class="px-3 py-2 bg-amber-50 border-b border-amber-100">
                                        <h4 class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Estructura de Enseñanza</h4>
                                    </div>
                                    <div class="p-3 space-y-2">
                                        @php
                                            $sectionStyles = [
                                                'INICIO' => ['class' => 'bg-blue-50 text-blue-700 border border-blue-200', 'symbol' => '→'],
                                                'DESARROLLO' => ['class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200', 'symbol' => '◆'],
                                                'CIERRE' => ['class' => 'bg-amber-50 text-amber-700 border border-amber-200', 'symbol' => '●'],
                                            ];
                                        @endphp
                                        @foreach($preview['teaching_sections'] as $ts)
                                            @php
                                                $ss = $sectionStyles[$ts['title']] ?? ['class' => 'bg-stone-50 text-stone-600 border border-stone-200', 'symbol' => '•'];
                                            @endphp
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $ss['class'] }}">
                                                    {{ $ss['symbol'] }}
                                                    {{ $ts['title'] }}
                                                </span>
                                                <p class="text-xs text-stone-600 mt-1 leading-relaxed">{{ $ts['content'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($preview['teaching'])
                                <div class="bg-white rounded-lg border border-stone-200 p-3">
                                    <h4 class="text-[10px] font-bold text-stone-400 uppercase tracking-wider mb-1">Enseñanza / Actividad Globalizada</h4>
                                    <p class="text-xs text-stone-600 leading-relaxed whitespace-pre-line">{{ $preview['teaching'] }}</p>
                                </div>
                            @endif

                            {{-- Sections compact list --}}
                            @if($totalSections > 0)
                                <div class="bg-white rounded-lg border border-stone-200 overflow-hidden">
                                    <div class="px-3 py-2 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                                        <h4 class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Secciones</h4>
                                        <span class="text-[10px] text-stone-400 font-mono">{{ $totalSections }} · {{ $totalBlocks }} bloques</span>
                                    </div>
                                    <div class="divide-y divide-stone-100">
                                        @foreach($preview['sections'] as $sec)
                                            <div class="px-3 py-2 flex items-center justify-between gap-2 hover:bg-stone-50/50 transition-colors">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-medium text-stone-700 truncate">{{ $sec['title'] }}</p>
                                                    @if(count($sec['contents'] ?? []) > 0)
                                                        <p class="text-[11px] text-stone-400">
                                                            @foreach($sec['contents'] as $ci => $c)
                                                                @if($c['title'] ?? null)
                                                                    <span>· {{ $c['title'] }}</span>
                                                                @endif
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                </div>
                                                <span class="shrink-0 text-[11px] font-mono text-stone-400">{{ count($sec['contents'] ?? []) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Stats grid --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                <div class="bg-emerald-50 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-emerald-600">{{ $totalSections }}</p>
                                    <p class="text-[9px] text-emerald-500/70">Secciones</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-blue-600">{{ $totalBlocks }}</p>
                                    <p class="text-[9px] text-blue-500/70">Bloques</p>
                                </div>
                                <div class="bg-amber-50 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-amber-600">{{ max($totalResources, 0) }}</p>
                                    <p class="text-[9px] text-amber-500/70">Recursos</p>
                                </div>
                                <div class="bg-fuchsia-50 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-fuchsia-600">{{ max($totalEmbeds, 0) }}</p>
                                    <p class="text-[9px] text-fuchsia-500/70">Embeds</p>
                                </div>
                                <div class="bg-cyan-50 rounded-lg p-2.5 text-center">
                                    <p class="text-lg font-bold text-cyan-600">{{ max($totalLinks, 0) }}</p>
                                    <p class="text-[9px] text-cyan-500/70">Enlaces</p>
                                </div>
                                <div class="bg-stone-50 rounded-lg p-2.5">
                                    <p class="text-[9px] font-bold text-stone-400 uppercase tracking-wider">Curso</p>
                                    <p class="text-stone-700 font-medium text-xs leading-tight mt-0.5">{{ $preview['grado'] }} · {{ $preview['seccion'] }}</p>
                                </div>
                                <div class="bg-stone-50 rounded-lg p-2.5">
                                    <p class="text-[9px] font-bold text-stone-400 uppercase tracking-wider">Lapso</p>
                                    <p class="text-stone-700 font-medium text-xs mt-0.5">{{ $preview['lapso'] ?? '—' }}</p>
                                </div>
                                <div class="bg-stone-50 rounded-lg p-2.5">
                                    <p class="text-[9px] font-bold text-stone-400 uppercase tracking-wider">Duración</p>
                                    <p class="text-stone-700 font-medium text-xs mt-0.5">
                                        @if($preview['start_date'])
                                            {{ \Carbon\Carbon::parse($preview['start_date'])->format('d/m') }}—
                                            {{ \Carbon\Carbon::parse($preview['end_date'])->format('d/m') }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /swiper-wrapper --}}
            </div>{{-- /swiper-body --}}
        </div>{{-- /card --}}

        {{-- Footer navigation --}}
        <div class="w-full max-w-7xl mt-auto px-8 py-2 bg-white border-t border-slate-200 rounded-lg shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button x-on:click="prev()"
                        class="w-9 h-9 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 flex items-center justify-center transition-all"
                        title="Anterior">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button x-on:click="next()"
                        class="w-9 h-9 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 flex items-center justify-center transition-all"
                        title="Siguiente">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            <div class="swiper-pagination-fraction text-sm font-medium text-slate-500" x-text="currentSlide + ' / ' + totalSlides"></div>
            <div class="flex items-center gap-2">
                <button x-on:click="toggleFullscreen"
                        class="px-3 py-1.5 text-xs text-slate-500 hover:text-slate-700 border border-slate-200 rounded-lg hover:bg-white transition-all">
                    ⛶ Pantalla completa
                </button>
                <button wire:click="{{ $closeMethod }}"
                        class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition-all">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Estilos para Mermaid fullscreen / zoom toolbar --}}
@once
    <style>
        .mermaid-zoom-toolbar {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        [x-data="mermaidEmbed()"]:fullscreen {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: auto;
        }
        [x-data="mermaidEmbed()"]:fullscreen .mermaid-zoom-toolbar {
            opacity: 1 !important;
            position: fixed;
            top: 1rem;
            right: 1rem;
        }
        .dark [x-data="mermaidEmbed()"]:fullscreen {
            background: #0f172a;
        }
        .zoom-act {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endonce
