<div class="max-w-4xl mx-auto px-3 sm:px-6 md:px-8 py-6 sm:py-8 md:py-10 space-y-6 sm:space-y-8">

    {{-- ═══════════════════════════════════════ BACK NAV ═══════════════════════════════════════ --}}
    <nav class="flex items-center gap-3 px-1">
        <a href="{{ route('student.lms.lessons') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-500/20 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Lecciones
        </a>
        <span class="text-[11px] text-gray-400 dark:text-gray-500 hidden sm:inline">
            / {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Actividad' }}
        </span>
    </nav>

    {{-- ═══════════════════════════════════════ HEADER ═══════════════════════════════════════ --}}
    <header class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">

                {{-- Title --}}
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $activity->topic ?? 'Actividad' }}
                </h1>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    @if($activity->finicial)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ optional($activity->finicial)->format('d/m/Y') }}
                            @if($activity->ffinal)
                                – {{ optional($activity->ffinal)->format('d/m/Y') }}
                            @endif
                        </span>
                    @endif
                    @if($activity->lmsPublication?->status === 'published')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Publicada
                        </span>
                    @endif
                </div>
            </div>

            {{-- Status badge --}}
            @if($completed)
                <span class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-bold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Completada
                </span>
            @endif
        </div>

        {{-- Description --}}
        @if($activity->description)
            <p class="mt-3 sm:mt-4 text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed border-t border-gray-100 dark:border-gray-700/40 pt-3 sm:pt-4">
                {{ $activity->description }}
            </p>
        @endif
    </header>

    {{-- ═══════════════════════════════════════ SECTIONS ═══════════════════════════════════════ --}}
    @forelse($sections as $section)
        @php
            $sectionUpper = mb_strtoupper($section->title ?? '');
            $bgHue = '';
            $accentColor = 'emerald';
            $accentDot = 'bg-emerald-500';
            $accentRing = 'ring-emerald-500/20';
            $badgeLabel = null;
            $badgeClass = '';

            if (preg_match('/\b(INICIO|INTRODUCCI[OÓ]N|APERTURA|BIENVENIDA|PRESENTACI[OÓ]N)\b/', $sectionUpper)) {
                $badgeLabel = 'INICIO';
                $badgeClass = 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20';
                $accentColor = 'blue';
                $accentDot = 'bg-blue-500';
                $accentRing = 'ring-blue-500/20';
            } elseif (preg_match('/\b(DESARROLLO|ACTIVIDAD|CONTENIDO|EXPLICACI[OÓ]N|EJERCICIO|PR[AÁ]CTICA|AN[AÁ]LISIS|PROFUNDIZACI[OÓ]N|REFLEXI[OÓ]N|LECTURA|TEMA)\b/', $sectionUpper)) {
                $badgeLabel = 'DESARROLLO';
                $badgeClass = 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20';
                $accentColor = 'emerald';
                $accentDot = 'bg-emerald-500';
                $accentRing = 'ring-emerald-500/20';
            } elseif (preg_match('/\b(CIERRE|CONCLUSI[OÓ]N|RESUMEN|EVALUACI[OÓ]N|REPASO|S[IÍ]NTESIS|FINAL|RETROALIMENTACI[OÓ]N)\b/', $sectionUpper)) {
                $badgeLabel = 'CIERRE';
                $badgeClass = 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20';
                $accentColor = 'amber';
                $accentDot = 'bg-amber-500';
                $accentRing = 'ring-amber-500/20';
            }

            $contentCount = $section->visibleContents->count();
        @endphp

        <section wire:key="section-{{ $section->id }}"
                 class="bg-white dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">

            {{-- Section header --}}
            <div class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 dark:border-gray-700/40 bg-gray-50/50 dark:bg-gray-800/30">
                <span class="w-1 h-7 rounded-full {{ $accentDot }} shrink-0"></span>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex-1 min-w-0">
                    {{ $section->title }}
                </h2>
                @if($badgeLabel)
                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                @endif
                @if($contentCount)
                    <span class="shrink-0 text-[11px] font-medium text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700/50 px-2.5 py-0.5 rounded-full border border-gray-200 dark:border-gray-600/50">
                        {{ $contentCount }} {{ $contentCount === 1 ? 'paso' : 'pasos' }}
                    </span>
                @endif
            </div>

            {{-- Section body --}}
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                @foreach($section->visibleContents as $idx => $content)
                    @php
                        $stepNum = $idx + 1;
                        $bodyHtml = $content->body ?? '';
                        $isLast = $loop->last;
                    @endphp

                    <div wire:key="content-{{ $content->id }}" class="flex gap-3 sm:gap-4 {{ $isLast ? '' : 'pb-4 sm:pb-6 border-b border-gray-100 dark:border-gray-700/30' }}">
                        {{-- Step circle --}}
                        <div class="flex flex-col items-center shrink-0">
                            <span class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-xs sm:text-sm font-bold leading-none shadow-sm {{ $accentRing }} {{ $badgeLabel ? 'ring-2' : '' }}">
                                {{ $stepNum }}
                            </span>
                            @if(!$isLast)
                                <div class="w-0.5 flex-1 min-h-[1.5rem] bg-gray-200 dark:bg-gray-700/50 mt-1"></div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0 space-y-2">
                            @if($content->title)
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white leading-snug">{{ $content->title }}</h3>
                            @endif

                            @switch($content->type)
                                @case('TEXT')
                                    @php
                                        $plainText = strip_tags($bodyHtml);
                                        $textLen   = mb_strlen(trim($plainText));
                                        $hasUl     = str_contains($bodyHtml, '<ul');
                                        $hasOl     = str_contains($bodyHtml, '<ol');
                                        $hasBq     = str_contains($bodyHtml, '<blockquote');
                                        $hasEm     = str_contains($bodyHtml, '<em') || preg_match('/<i[^>]*>/', $bodyHtml);
                                        $hasImg    = str_contains($bodyHtml, '<img');
                                        $singleP   = substr_count($bodyHtml, '<p>') <= 1 && !$hasUl && !$hasOl && !$hasBq && !$hasImg;
                                        $isQ       = preg_match('/[¿\?]\s*$/', trim($plainText));

                                        $tpl = 'prose';
                                        if (preg_match('/\b(actividad|ejercicio|resuelve|practica|tarea|completa|investiga|realiza|escribe|dibuja|explica|elabora|construye|crea|diseña)\b/i', $plainText) && $textLen < 600) {
                                            $tpl = 'activity';
                                        } elseif ($isQ || preg_match('/\b(pregunta|¿qué|¿cómo|¿por qué|¿cuál|¿dónde|¿cuándo)\b/i', $plainText)) {
                                            $tpl = 'question';
                                        } elseif ($hasBq || (preg_match('/[«»]/u', $plainText) && $textLen < 300)) {
                                            $tpl = 'quote';
                                        } elseif ($hasUl || $hasOl) {
                                            $tpl = 'list';
                                        } elseif ($singleP && $textLen < 250 && $textLen > 10) {
                                            $tpl = 'concept';
                                        }
                                    @endphp

                                    @if($tpl === 'concept')
                                        <div class="bg-emerald-50/50 dark:bg-emerald-500/5 border-l-4 border-emerald-400 rounded-r-xl p-3 sm:p-4">
                                            <div class="flex items-start gap-3">
                                                <span class="text-lg leading-none mt-0.5 shrink-0">💡</span>
                                                <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed prose prose-sm max-w-none dark:prose-invert">{!! $bodyHtml !!}</div>
                                            </div>
                                        </div>
                                    @elseif($tpl === 'list')
                                        <div class="bg-white dark:bg-gray-800/30 rounded-xl p-3 sm:p-4 border border-gray-200 dark:border-gray-700/60">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-base leading-none">📋</span>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Lista</span>
                                            </div>
                                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed prose prose-sm max-w-none dark:prose-invert prose-ul:list-disc prose-ol:list-decimal">{!! $bodyHtml !!}</div>
                                        </div>
                                    @elseif($tpl === 'quote')
                                        <div class="bg-amber-50/50 dark:bg-amber-500/5 border-l-4 border-amber-400 rounded-r-xl p-3 sm:p-4">
                                            <div class="flex items-start gap-3">
                                                <span class="text-2xl leading-none text-amber-300/60 font-serif shrink-0">"</span>
                                                <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed prose prose-sm max-w-none dark:prose-invert">{!! $bodyHtml !!}</div>
                                            </div>
                                        </div>
                                    @elseif($tpl === 'question')
                                        <div class="bg-sky-50/50 dark:bg-sky-500/5 border border-sky-200 dark:border-sky-500/20 rounded-xl p-3 sm:p-4">
                                            <div class="flex items-start gap-3">
                                                <span class="text-base leading-none mt-0.5 shrink-0">💭</span>
                                                <div class="text-sm text-sky-900 dark:text-sky-300 leading-relaxed prose prose-sm max-w-none dark:prose-invert">{!! $bodyHtml !!}</div>
                                            </div>
                                        </div>
                                    @elseif($tpl === 'activity')
                                        <div class="bg-amber-50/50 dark:bg-amber-500/5 border-2 border-dashed border-amber-300/60 dark:border-amber-500/30 rounded-xl p-3 sm:p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-base leading-none">✏️</span>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Actividad</span>
                                            </div>
                                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed prose prose-sm max-w-none dark:prose-invert">{!! $bodyHtml !!}</div>
                                        </div>
                                    @else
                                        <div class="bg-white dark:bg-gray-800/20 rounded-xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700/40">
                                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-loose prose prose-sm max-w-none dark:prose-invert">{!! $bodyHtml !!}</div>
                                        </div>
                                    @endif
                                    @break

                                @case('IMAGE')
                                    @php
                                        $imgUrl = $content->media?->public_url ?? '';
                                        $imgAlt = $content->title ?? 'Imagen';
                                    @endphp
                                    @if($imgUrl)
                                        <div x-data="{ loaded: false, failed: false }" class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-800/30">
                                            {{-- Loading skeleton --}}
                                            <div x-show="!loaded && !failed"
                                                 class="flex items-center justify-center h-48 sm:h-64 bg-gray-100 dark:bg-gray-800/50 animate-pulse">
                                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            {{-- Error fallback --}}
                                            <div x-show="failed" x-cloak
                                                 class="flex flex-col items-center justify-center h-48 sm:h-64 bg-gray-100 dark:bg-gray-800/50 text-gray-400 dark:text-gray-500">
                                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <p class="text-xs sm:text-sm">No se pudo cargar la imagen</p>
                                            </div>
                                            {{-- Image --}}
                                            <img src="{{ $imgUrl }}"
                                                 alt="{{ $imgAlt }}"
                                                 x-on:load="loaded = true"
                                                 x-on:error="loaded = true; failed = true"
                                                 x-show="loaded"
                                                 x-bind:class="failed ? 'hidden' : ''"
                                                 class="w-full h-auto max-h-96 object-contain bg-gray-50 dark:bg-gray-800/30"
                                                 loading="lazy">
                                            @if($content->body)
                                                <div class="px-3 sm:px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700/40 bg-gray-50/50 dark:bg-gray-800/20">
                                                    {{ strip_tags($content->body) }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        {{-- No media — show fallback with body as text if present --}}
                                        <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-xl p-3 sm:p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Imagen no disponible</span>
                                            </div>
                                            @if($content->body)
                                                <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed prose prose-sm max-w-none dark:prose-invert">{!! $content->body !!}</div>
                                            @else
                                                <p class="text-sm text-gray-500 dark:text-gray-400 italic">No hay imagen disponible para este contenido.</p>
                                            @endif
                                        </div>
                                    @endif
                                    @break

                                @case('VIDEO')
                                    @if($content->media?->isLocal())
                                        <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700/60 bg-black">
                                            <video controls class="w-full aspect-video" preload="metadata">
                                                <source src="{{ $content->media->public_url }}" type="{{ $content->media->mime_type }}">
                                            </video>
                                        </div>
                                    @elseif($content->media?->provider === 'YOUTUBE')
                                        @php preg_match('/[?&]v=([^&]+)/', $content->media->external_url ?? '', $m); $vid = $m[1] ?? ''; @endphp
                                        @if($vid)
                                            <div class="aspect-video rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700/60 bg-black">
                                                <iframe src="https://www.youtube.com/embed/{{ $vid }}"
                                                        class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                                            </div>
                                        @endif
                                    @endif
                                    @break

                                @case('EMBED')
                                    <div class="aspect-video rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-800/30">
                                        {!! $content->body !!}
                                    </div>
                                    @break

                                @case('FILE_PREVIEW')
                                    @if($content->media)
                                        <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700/60" style="height: min(600px, 80vh);">
                                            <iframe src="{{ $content->media->public_url }}" class="w-full h-full" loading="lazy"></iframe>
                                        </div>
                                    @endif
                                    @break

                                @case('AUDIO')
                                    @if($content->media)
                                        <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700/60">
                                            <div class="flex items-center gap-3 mb-2">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                                </svg>
                                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Audio</span>
                                            </div>
                                            <audio controls class="w-full" preload="metadata">
                                                <source src="{{ $content->media->public_url }}" type="{{ $content->media->mime_type }}">
                                            </audio>
                                        </div>
                                    @endif
                                    @break

                                @case('HTML')
                                    <div class="prose dark:prose-invert max-w-none text-sm">
                                        {!! $content->body !!}
                                    </div>
                                    @break

                                @default
                                    @if($content->body)
                                        <div class="bg-gray-50 dark:bg-gray-800/20 rounded-xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700/40">
                                            <div class="text-sm text-gray-700 dark:text-gray-300 prose prose-sm max-w-none dark:prose-invert">{!! $content->body !!}</div>
                                        </div>
                                    @endif
                            @endswitch
                        </div>
                    </div>
                @endforeach

                {{-- HTML Embeds vinculados a esta sección --}}
                @php
                    $sectionEmbeds = $htmlEmbeds->filter(fn($e) => $e->section_id === $section->id);
                @endphp
                @if($sectionEmbeds->isNotEmpty())
                    <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-700/40">
                        @foreach($sectionEmbeds as $embed)
                            <div class="bg-white dark:bg-gray-800/20 border border-fuchsia-200 dark:border-fuchsia-500/20 rounded-xl p-3 sm:p-4 html-embed-item">
                                @if($embed->title)
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">{{ $embed->title }}</h4>
                                @endif
                                @if($embed->is_mermaid ?? false)
                                    <x-mermaid::component :data="$embed->html_content" class="w-full min-h-[200px]" />
                                @else
                                    <div class="text-sm text-gray-700 dark:text-gray-300 prose prose-sm max-w-none dark:prose-invert html-embed-content">
                                        {!! $embed->html_content !!}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Recursos vinculados a esta sección --}}
                @php
                    $secResources = $resources->filter(fn($r) => $r->section_id === $section->id);
                @endphp
                @if($secResources->isNotEmpty())
                    <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700/40">
                        @foreach($secResources as $resource)
                            <x-lms.student-resource-card :resource="$resource" :activity="$activity" />
                        @endforeach
                    </div>
                @endif

                {{-- Enlaces vinculados a esta sección --}}
                @php
                    $secLinks = $links->filter(fn($l) => $l->section_id === $section->id);
                @endphp
                @if($secLinks->isNotEmpty())
                    <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700/40">
                        @foreach($secLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800/20 border border-blue-200 dark:border-blue-500/20 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-500/5 transition-colors group">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300 truncate">{{ $link->title }}</p>
                                    <p class="text-xs text-blue-500 dark:text-blue-400 truncate">{{ $link->url }}</p>
                                </div>
                                <span class="shrink-0 text-[10px] font-medium text-blue-500 dark:text-blue-400 bg-blue-100 dark:bg-blue-500/10 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-500/20">{{ $link->link_type ?? 'Enlace' }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @empty
        <div class="bg-white dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700/50 p-8 sm:p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No hay contenido disponible</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Esta actividad no tiene secciones visibles.</p>
        </div>
    @endforelse

    {{-- ═══════════════════════════════════════ RESOURCES (no vinculados) ═══════════════════════════════════════ --}}
    @php
        $unlinkedResources = $resources->filter(fn($r) => empty($r->section_id));
    @endphp
    @if($unlinkedResources->isNotEmpty())
        <section class="bg-white dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 dark:border-gray-700/40 bg-gray-50/50 dark:bg-gray-800/30">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Recursos descargables</h2>
                <span class="ml-auto text-[11px] text-gray-400 dark:text-gray-500">{{ $unlinkedResources->count() }} {{ $unlinkedResources->count() === 1 ? 'archivo' : 'archivos' }}</span>
            </div>
            <div class="p-4 sm:p-6 space-y-2">
                @foreach($unlinkedResources as $resource)
                    <x-lms.student-resource-card :resource="$resource" :activity="$activity" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ LINKS (no vinculados) ═══════════════════════════════════════ --}}
    @php
        $unlinkedLinks = $links->filter(fn($l) => empty($l->section_id));
    @endphp
    @if($unlinkedLinks->isNotEmpty())
        <section class="bg-white dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 dark:border-gray-700/40 bg-gray-50/50 dark:bg-gray-800/30">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Referencias y enlaces</h2>
            </div>
            <div class="p-4 sm:p-6 space-y-2">
                @foreach($unlinkedLinks as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800/20 border border-blue-200 dark:border-blue-500/20 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-500/5 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-300 truncate">{{ $link->title }}</p>
                            @if($link->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $link->description }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 text-[10px] font-medium text-blue-500 dark:text-blue-400 bg-blue-100 dark:bg-blue-500/10 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-500/20">{{ $link->link_type ?? 'Enlace' }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ HTML EMBEDS (no vinculados) ═══════════════════════════════════════ --}}
    @php
        $unlinkedEmbeds = $htmlEmbeds->filter(fn($e) => empty($e->section_id));
    @endphp
    @if($unlinkedEmbeds->isNotEmpty())
        <section class="bg-white dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 dark:border-gray-700/40 bg-gray-50/50 dark:bg-gray-800/30">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Contenido embebido</h2>
            </div>
            <div class="p-4 sm:p-6 space-y-3">
                @foreach($unlinkedEmbeds as $embed)
                    <div class="bg-white dark:bg-gray-800/20 border border-fuchsia-200 dark:border-fuchsia-500/20 rounded-xl p-3 sm:p-4 html-embed-item">
                        @if($embed->title)
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">{{ $embed->title }}</h4>
                        @endif
                        @if($embed->is_mermaid ?? false)
                            <x-mermaid::component :data="$embed->html_content" class="w-full min-h-[200px]" />
                        @else
                            <div class="text-sm text-gray-700 dark:text-gray-300 prose prose-sm max-w-none dark:prose-invert html-embed-content">
                                {!! $embed->html_content !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ COMMENTS ═══════════════════════════════════════ --}}
    @if($activity->lmsPublication?->allow_comments)
        <section class="bg-white dark:bg-gray-800/40 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 dark:border-gray-700/40 bg-gray-50/50 dark:bg-gray-800/30">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Comentarios</h2>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                {{-- Form --}}
                <form wire:submit="saveComment" class="flex gap-2">
                    <input type="text" wire:model="newComment" placeholder="Escribe un comentario…"
                           class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all"
                           maxlength="1000"/>
                    <button type="submit"
                            class="shrink-0 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-xl transition-colors"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveComment">Enviar</span>
                        <span wire:loading wire:target="saveComment">Enviando…</span>
                    </button>
                </form>
                @error('newComment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                {{-- List --}}
                <div class="space-y-3">
                    @forelse($comments as $comment)
                        <div class="flex gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/30">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ strtoupper(substr($comment->user?->profile?->firstname ?? $comment->user?->name ?? '?', 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">
                                        {{ $comment->user?->profile?->firstname ?? $comment->user?->name ?? '—' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $comment->body }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════ BACK ═══════════════════════════════════════ --}}
    <div class="flex items-center justify-between pt-2">
        <a href="{{ route('student.lms.home') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-gray-700/50 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a mis actividades
        </a>
        @if($activity->lmsPublication?->status === 'published' && !$completed)
            <button wire:click="markComplete"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Marcar como completada
            </button>
        @endif
    </div>
</div>
