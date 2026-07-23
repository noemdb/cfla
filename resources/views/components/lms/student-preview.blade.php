@props([
    'preview'     => [],
    'closeMethod' => 'closeListStudentPreview',
    'wireKey'     => 'student-preview',
])

@php
    $pid = $preview['activity_id'] ?? $wireKey;

    // ── Helper: renderizar body detectando HTML o Markdown ────
    $renderBody = function (string $rawBody): string {
        $trimmed = trim($rawBody);
        if ($trimmed === '') return $rawBody;

        // Si tiene etiquetas HTML → es HTML, devolver tal cual
        if (preg_match('/<[a-z\/][^>]*>/i', $trimmed)) return $rawBody;

        // Patrones de sintaxis Markdown (flag x ignora whitespace,
        // \# evita que #{1,6} sea tratado como comentario PCRE)
        $isMd = (bool) preg_match('/
            (?:^\#{1,6}\s)        |
            (?:\*\*[^*].*?\*\*)  |
            (?:__[^_].*?__)      |
            (?:^\s*[-*+]\s+.+)   |
            (?:^\s*\d+[.)]\s+.+) |
            (?:^\s*>\s.+)        |
            (?:`{1,3}[^`].*?`{1,3}) |
            (?:\[[^\]]+\]\([^)]+\)) |
            (?:\!\[[^\]]*\]\([^)]+\)) |
            (?:\|.+\|.+\|)       |
            (?:^[-=]{3,}\s*$)    |
            (?:^\[.+\]:\s+.+)/mx', $trimmed);

        return $isMd ? \Illuminate\Support\Str::markdown($rawBody) : $rawBody;
    };

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
            <div class="w-full bg-stone-50 swiper overflow-hidden flex-1 min-h-0 self-stretch"
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
                        <div class="swiper-slide overflow-y-auto w-full h-full min-h-0 p-6 md:p-8 flex flex-col">
                            {{-- Section header with teaching structure badge + step count --}}
                            @php
                                $sectionTitleUpper = mb_strtoupper($section['title'] ?? '');
                                $teachingStyle = null;
                                if (preg_match('/\b(INICIO|INTRODUCCI[OÓ]N|APERTURA|BIENVENIDA|PRESENTACI[OÓ]N)\b/', $sectionTitleUpper)) {
                                    $teachingStyle = ['label' => 'INICIO', 'symbol' => '→', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200'];
                                } elseif (preg_match('/\b(DESARROLLO|ACTIVIDAD|CONTENIDO|EXPLICACI[OÓ]N|EJERCICIO|PR[AÁ]CTICA|AN[AÁ]LISIS|PROFUNDIZACI[OÓ]N|REFLEXI[OÓ]N|LECTURA)\b/', $sectionTitleUpper)) {
                                    $teachingStyle = ['label' => 'DESARROLLO', 'symbol' => '◆', 'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200'];
                                } elseif (preg_match('/\b(CIERRE|CONCLUSI[OÓ]N|RESUMEN|EVALUACI[OÓ]N|REPASO|S[IÍ]NTESIS|FINAL|RETROALIMENTACI[OÓ]N)\b/', $sectionTitleUpper)) {
                                    $teachingStyle = ['label' => 'CIERRE', 'symbol' => '●', 'class' => 'bg-amber-50 text-amber-700 border border-amber-200'];
                                }
                                $contentCount = count($section['contents'] ?? []);
                            @endphp
                            <div class="text-[10px] font-medium text-slate-400 dark:text-slate-500 tracking-wide px-0.5 mb-1 text-right">{{ $preview['title'] }}</div>
                            <div class="flex items-center gap-2 mb-4 shrink-0">
                                <span class="w-1 h-6 bg-emerald-500 rounded-full shrink-0"></span>
                                <h2 class="text-lg font-bold text-slate-800">{{ $section['title'] }}</h2>
                                @if($teachingStyle)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $teachingStyle['class'] }}">
                                        <span>{{ $teachingStyle['symbol'] }}</span>
                                        {{ $teachingStyle['label'] }}
                                    </span>
                                @endif
                                @if($contentCount > 1)
                                    <span class="ml-auto text-[11px] font-medium text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200/60 shrink-0">{{ $contentCount }} pasos</span>
                                @elseif($contentCount === 1)
                                    <span class="ml-auto text-[11px] font-medium text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200/60 shrink-0">1 paso</span>
                                @endif
                            </div>

                            {{-- Step progress indicator --}}
                            @foreach($section['contents'] ?? [] as $idx => $content)
                                @php
                                    $rawBody  = $content['body'] ?? '';
                                    $bodyHtml = $renderBody($rawBody); // Markdown → HTML si aplica

                                    $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $rawBody) === 1;
                                    if (!$isMermaid) {
                                        $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($rawBody)) === 1;
                                    }
                                    $stepNumber = $idx + 1;
                                @endphp
                                @php $isLastWithMermaid = $loop->last && $isMermaid; @endphp
                                <div class="flex gap-3 {{ $isLastWithMermaid ? 'flex-1 min-h-0 items-stretch' : 'items-start' }} {{ $loop->last ? '' : 'mb-3' }}">
                                    {{-- Step circle with connector line --}}
                                    <div class="flex flex-col items-center shrink-0">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold leading-none shadow-sm">
                                            {{ $stepNumber }}
                                        </span>
                                        @if(!$loop->last)
                                            <div class="w-0.5 flex-1 min-h-[1.5rem] bg-emerald-200/50 mt-1.5"></div>
                                        @endif
                                    </div>

                                    {{-- Step content --}}
                                    <div class="flex-1 min-w-0 {{ $isLastWithMermaid ? 'min-h-0 flex flex-col' : '' }} {{ $loop->last ? '' : 'pb-1' }}">
                                        @if(($content['title'] ?? null))
                                            <h3 class="text-sm font-bold text-slate-800 leading-snug mb-1.5 shrink-0">{{ $content['title'] }}</h3>
                                        @endif
                                        @if($isMermaid)
                                            @php
                                                preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $bodyHtml, $m);
                                                $mermaidCode = trim(strip_tags($m[1] ?? ''));
                                                if (empty($mermaidCode)) {
                                                    $mermaidCode = trim(strip_tags($rawBody));
                                                }
                                            @endphp
                                            <div wire:ignore x-data="mermaidEmbed()"
                                                 data-mermaid-code="{{ $mermaidCode }}"
                                                 data-mermaid-delay
                                                 class="w-full bg-transparent rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex-1 min-h-0 flex flex-col mermaid-fill-height">
                                                <div x-ref="target" class="w-full flex-1 min-h-0"></div>
                                            </div>
                                        @elseif(!empty(trim(strip_tags($bodyHtml))))
                                            @php
                                                $__pt   = strip_tags($bodyHtml);
                                                $__len  = mb_strlen(trim($__pt));
                                                $__hasUl = str_contains($bodyHtml, '<ul');
                                                $__hasOl = str_contains($bodyHtml, '<ol');
                                                $__hasBq = str_contains($bodyHtml, '<blockquote');
                                                $__hasEm = str_contains($bodyHtml, '<em') || preg_match('/<i[^>]*>/', $bodyHtml);
                                                $__hasImg = str_contains($bodyHtml, '<img');
                                                $__singleP = substr_count($bodyHtml, '<p>') <= 1 && !$__hasUl && !$__hasOl && !$__hasBq && !$__hasImg;
                                                $__isQ = preg_match('/[¿\?]\s*$/', trim($__pt));

                                                $__tpl = 'prose';
                                                if (preg_match('/\b(actividad|ejercicio|resuelve|practica|tarea|completa|investiga|realiza|escribe|dibuja|explica|elabora|construye|crea|diseña)\b/i', $__pt) && $__len < 600) {
                                                    $__tpl = 'activity';
                                                } elseif ($__isQ || preg_match('/\b(pregunta|¿qué|¿cómo|¿por qué|¿cuál|¿dónde|¿cuándo)\b/i', $__pt)) {
                                                    $__tpl = 'question';
                                                } elseif ($__hasBq || (preg_match('/[«»]/u', $__pt) && $__len < 300)) {
                                                    $__tpl = 'quote';
                                                } elseif ($__hasUl || $__hasOl) {
                                                    $__tpl = 'list';
                                                } elseif ($__singleP && $__len < 250 && $__len > 10) {
                                                    $__tpl = 'concept';
                                                }
                                            @endphp

                                            @if($__tpl === 'concept')
                                                <div class="bg-transparent border-l-4 border-emerald-400 rounded-r-xl p-4">
                                                    <div class="flex items-start gap-3">
                                                        <span class="text-xl leading-none mt-0.5 shrink-0">💡</span>
                                                        <x-lms.math-text
                                                            :content="$bodyHtml"
                                                            class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none lms-content" />
                                                    </div>
                                                </div>
                                            @elseif($__tpl === 'list')
                                                <div class="bg-transparent rounded-xl p-4 border border-slate-200/60">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="text-lg leading-none">📋</span>
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Lista</span>
                                                    </div>
                                                    <x-lms.math-text
                                                        :content="$bodyHtml"
                                                        class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none prose-ul:list-disc prose-ol:list-decimal lms-content" />
                                                </div>
                                            @elseif($__tpl === 'quote')
                                                <div class="bg-transparent border-l-4 border-amber-400 rounded-r-xl p-4">
                                                    <div class="flex items-start gap-3">
                                                        <span class="text-2xl leading-none text-amber-300/60 font-serif shrink-0">"</span>
                                                        <x-lms.math-text
                                                            :content="$bodyHtml"
                                                            class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none [&_em]:text-amber-800 [&_em]:not-italic [&_em]:font-medium lms-content" />
                                                    </div>
                                                </div>
                                            @elseif($__tpl === 'question')
                                                <div class="bg-transparent border border-sky-200/60 rounded-xl p-4">
                                                    <div class="flex items-start gap-3">
                                                        <span class="text-xl leading-none mt-0.5 shrink-0">💭</span>
                                                        <x-lms.math-text
                                                            :content="$bodyHtml"
                                                            class="text-sm text-sky-900 leading-relaxed prose prose-sm max-w-none lms-content" />
                                                    </div>
                                                </div>
                                            @elseif($__tpl === 'activity')
                                                <div class="bg-transparent border-2 border-dashed border-amber-300/60 rounded-xl p-4">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="text-lg leading-none">✏️</span>
                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Actividad</span>
                                                    </div>
                                                    <x-lms.math-text
                                                        :content="$bodyHtml"
                                                        class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none lms-content" />
                                                </div>
                                            @else
                                                <div class="bg-transparent rounded-xl p-4 border border-stone-200/60">
                                                    <x-lms.math-text
                                                        :content="$bodyHtml"
                                                        class="text-sm text-slate-700 leading-loose prose prose-sm max-w-none lms-content" />
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            {{-- HTML Embeds vinculados a esta sección --}}
                            @php
                                $sectionEmbeds = $previewHtmlEmbeds->where('section_id', $section['id']);
                            @endphp
                            @if($sectionEmbeds->count() > 0)
                                <div class="space-y-2 pt-2">
                                    @foreach($sectionEmbeds as $embed)
                                        <div class="p-4 bg-transparent border border-fuchsia-200/60 rounded-lg html-embed-item">
                                            @if(!empty($embed['title']))
                                                <h4 class="text-sm font-semibold text-fuchsia-800 mb-2">{{ $embed['title'] }}</h4>
                                            @endif
                                            <div class="text-sm text-slate-700 prose prose-sm max-w-none lms-content html-embed-content">
                                                @if($embed['is_mermaid'] ?? false)
                                                    <div wire:ignore x-data="mermaidEmbed()"
                                                         data-mermaid-code="{{ $embed['html_content'] }}"
                                                         data-mermaid-delay
                                                         class="w-full min-h-[250px] flex flex-col mermaid-fill-height">
                                                        <div x-ref="target" class="w-full flex-1 min-h-0"></div>
                                                    </div>
                                                @else
                                                    <x-lms.math-text :content="$embed['html_content']" as="div" />
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
                                            <div class="rounded-lg overflow-hidden border border-stone-200/60 bg-transparent resource-image-wrap">
                                                <img src="{{ $res['media']['public_url'] }}" alt="{{ $res['display_name'] }}"
                                                     onerror="this.closest('.resource-image-wrap')?.querySelector('.image-fallback')?.classList?.remove('hidden'); this.classList.add('hidden')"
                                                     class="w-full h-auto max-h-80 object-contain bg-transparent">
                                                <div class="image-fallback hidden">
                                                    <div class="flex items-center gap-3 p-3 bg-transparent">
                                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                            <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between px-3 py-2 bg-transparent border-t border-stone-200/40">
                                                    <span class="text-xs text-slate-600 truncate">{{ $res['display_name'] }}</span>
                                                    <a href="{{ $res['media']['public_url'] }}" target="_blank"
                                                       class="ml-2 text-[10px] font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded border border-emerald-200 transition-colors shrink-0 flex items-center gap-1"
                                                       title="Descargar {{ $res['display_name'] }}">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        Descargar
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 p-3 bg-transparent rounded-lg border border-stone-200/60">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                    <p class="text-xs text-slate-400">{{ $res['media']['size_for_humans'] ?? '' }}</p>
                                                </div>
                                                <a href="{{ $res['media']['public_url'] }}" target="_blank"
                                                   class="ml-auto text-[10px] font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded border border-emerald-200 transition-colors shrink-0 flex items-center gap-1"
                                                   title="Descargar {{ $res['display_name'] }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Descargar
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if(count($secLinks) > 0)
                                <div class="space-y-2 pt-2">
                                    @foreach($secLinks as $link)
                                        <div class="flex items-center gap-3 p-3 bg-transparent rounded-lg border border-blue-200/60">
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
                            <div class="flex items-center gap-1.5 text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 mb-2">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Los archivos de imagen (PNG, JPG, GIF) se muestran en línea. Todo el contenido permanece visible en su sección asociada, independientemente del tipo de archivo.</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($unlinkedResources as $res)
                                    @if(str_starts_with($res['media']['mime_type'] ?? '', 'image/'))
                                        <div class="rounded-lg overflow-hidden border border-stone-200/60 bg-transparent resource-image-wrap">
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
                                            <div class="flex items-center justify-between px-3 py-2 bg-slate-50 border-t border-slate-100">
                                                    <p class="text-sm font-medium text-slate-700 truncate">{{ $res['display_name'] }}</p>
                                                    <a href="{{ $res['media']['public_url'] }}" target="_blank"
                                                       class="ml-2 text-[10px] font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded border border-emerald-200 transition-colors shrink-0 flex items-center gap-1"
                                                       title="Descargar {{ $res['display_name'] }}">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        Descargar
                                                    </a>
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
                                            <a href="{{ $res['media']['public_url'] }}" target="_blank"
                                               class="ml-auto text-[10px] font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded border border-emerald-200 transition-colors shrink-0 flex items-center gap-1"
                                               title="Descargar {{ $res['display_name'] }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Descargar
                                            </a>
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
                                    <div class="flex items-center gap-3 p-3 bg-transparent rounded-lg border border-blue-200/60">
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
                                    <div class="p-4 bg-transparent border border-fuchsia-200/60 rounded-lg html-embed-item">
                                        @if(!empty($embed['title']))
                                            <h4 class="text-sm font-semibold text-fuchsia-800 mb-2">{{ $embed['title'] }}</h4>
                                        @endif
                                        <div class="text-sm text-slate-700 prose prose-sm max-w-none html-embed-content">
                                            @if($embed['is_mermaid'] ?? false)
                                                <div wire:ignore x-data="mermaidEmbed()"
                                                     data-mermaid-code="{{ $embed['html_content'] }}"
                                                     data-mermaid-delay
                                                     class="w-full min-h-[250px] flex flex-col mermaid-fill-height">
                                                    <div x-ref="target" class="w-full flex-1 min-h-0"></div>
                                                </div>
                                            @else
                                                <x-lms.math-text :content="$embed['html_content']" as="div" />
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══════ SLIDE: PREGUNTAS DE REPASO ═══════ --}}
                    @if(!empty($preview['review_questions']))
                        <div class="swiper-slide overflow-y-auto w-full h-auto p-6 md:p-8">
                            {{-- 
                            <div class="flex items-center gap-2 mb-4 shrink-0">
                                <span class="w-1 h-6 bg-emerald-500 rounded-full shrink-0"></span>
                                <h2 class="text-lg font-bold text-slate-800">Preguntas de Repaso</h2>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                                    <span>●</span>
                                    CIERRE
                                </span>
                            </div> 
                            --}}
                            <div class="bg-transparent rounded-xl p-5 border border-stone-200/60">
                                <div class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none lms-content">
                                    @php
                                        $reviewHtml = $preview['review_questions'];
                                        // 1) Convertir **negrita** → <strong> (pares completos)
                                        $reviewHtml = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $reviewHtml);
                                        // 2) Eliminar ** huérfanos (sin cierre) que el parser dejaría literales
                                        $reviewHtml = str_replace('**', '', $reviewHtml);
                                    @endphp
                                    <x-lms.math-text
                                        :content="Str::markdown($reviewHtml)"
                                        class="text-sm text-slate-700 leading-relaxed prose prose-sm max-w-none lms-content" />
                                </div>
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
            <div class="flex items-center gap-1">
                <button x-on:click="_getSwiper()?.slideTo(0)"
                        class="w-8 h-9 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 flex items-center justify-center transition-all"
                        :class="currentSlide <= 1 ? 'opacity-30' : ''"
                        title="Ir al inicio">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                </button>
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
                <button x-on:click="_getSwiper()?.slideTo(totalSlides - 1)"
                        class="w-8 h-9 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 flex items-center justify-center transition-all"
                        :class="currentSlide >= totalSlides ? 'opacity-30' : ''"
                        title="Ir al final">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
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

{{-- Marca de agua institucional --}}
@php
    $watermarkUrl = asset('image/logo/logo1x1.png');
@endphp

{{-- Estilos para Mermaid fullscreen / zoom toolbar + watermark --}}
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
        .zoom-act {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Mermaid fill height (diagrama ocupa todo el alto disponible) ── */
        .mermaid-fill-height {
            min-height: 0;
        }
        .mermaid-fill-height svg {
            min-height: 100% !important;
            width: 100% !important;
            flex: 1 !important;
        }

        /* ── lms-content: estilos consistentes para contenido Markdown ── */
        .lms-content :is(h1, h2, h3, h4) {
            color: #0f172a !important;
            font-weight: 700 !important;
            line-height: 1.3 !important;
            letter-spacing: -0.01em !important;
        }
        .lms-content h1 {
            font-size: 1.5em !important;
            margin: 1.2em 0 0.6em !important;
            padding-bottom: 0.3em !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .lms-content h2 {
            font-size: 1.25em !important;
            margin: 1.4em 0 0.5em !important;
            padding-bottom: 0.25em !important;
            border-bottom: 1.5px solid #e2e8f0 !important;
            color: #0d9488 !important;
        }
        .lms-content h3 {
            font-size: 1.05em !important;
            margin: 1.2em 0 0.4em !important;
            color: #1e293b !important;
        }
        .lms-content h4 {
            font-size: 0.95em !important;
            margin: 1em 0 0.3em !important;
            color: #334155 !important;
            font-weight: 600 !important;
        }
        .lms-content blockquote {
            border-left: 3px solid #0d9488 !important;
            background: #f0fdfa !important;
            padding: 0.75em 1em !important;
            margin: 1.2em 0 !important;
            border-radius: 0 0.5rem 0.5rem 0 !important;
        }
        .lms-content blockquote p {
            margin: 0 !important;
        }
        .lms-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 1.2em 0 !important;
            font-size: 0.9em !important;
            line-height: 1.5 !important;
        }
        .lms-content table thead {
            border-bottom: 2px solid #0d9488 !important;
        }
        .lms-content table th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 0.9em !important;
            text-transform: uppercase !important;
            letter-spacing: 0.025em !important;
            padding: 0.6rem 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            text-align: left !important;
            vertical-align: top !important;
        }
        .lms-content table td {
            padding: 0.5rem 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            vertical-align: top !important;
            color: #334155 !important;
        }
        .lms-content table tbody tr {
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .lms-content table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
        }
        .lms-content table tbody tr:hover {
            background-color: #f1f5f9 !important;
        }
        .lms-content table :is(th, td) strong {
            color: inherit !important;
        }
        .lms-content p {
            margin: 0.6em 0 !important;
            line-height: 1.65 !important;
        }
        .lms-content p:first-child {
            margin-top: 0 !important;
        }
        .lms-content ul, .lms-content ol {
            margin: 0.5em 0 !important;
            padding-left: 1.2em !important;
        }
        .lms-content li {
            margin: 0.2em 0 !important;
        }
        .lms-content strong {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .lms-content em {
            color: #475569 !important;
        }

        /* ── Watermark institucional ───────────────────────────── */
        :root {
            --watermark-logo: url('{{ $watermarkUrl }}');
        }
        .swiper-slide {
            position: relative;
        }
        .swiper-slide::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            height: 50%;
            background: var(--watermark-logo) center / contain no-repeat;
            opacity: 0.06;
            pointer-events: none;
            z-index: -1;
        }
    </style>
@endonce
