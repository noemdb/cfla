{{--
    Renderer compartido de contenido (pasos) de una sección LMS.
    Se usa en modo desplazamiento (activity-view) y en modo libro (flipbook).
    --}}
@props([
    'content' => null,    // LmsActivityContent
    'mode' => 'scroll',   // 'scroll' | 'book'
    'stepNum' => 1,
    'isLast' => false,
    'sectionId' => null,  // id de la sección contenedora (placeholder enlazado, modo libro)
])

@php
    $bodyHtml = $content->body ?? '';
    $bookModeClass = $mode === 'book' ? 'book-compact' : '';
@endphp

@php
    $wrapperClasses = $mode === 'scroll'
        ? ($isLast ? '' : 'pb-3 sm:pb-4 border-b border-gray-200')
        : 'py-1';
    $stepCircle = $mode === 'scroll'
        ? 'w-8 h-8 rounded-full bg-emerald-600 text-white text-sm'
        : 'w-6 h-6 rounded-full bg-emerald-600/10 text-emerald-700 text-xs';
    $stepHeader = $mode === 'scroll' ? 'flex items-center justify-center gap-2 mb-2' : 'flex items-center gap-2 mb-2';
@endphp

<div @if($mode === 'scroll') wire:key="content-{{ $content->id }}" @endif class="{{ $wrapperClasses }}">
    {{-- Step number above content --}}
    <div class="{{ $stepHeader }}">
        <span class="flex items-center justify-center font-bold shrink-0 {{ $stepCircle }}">{{ $stepNum }}</span>
        @if($content->title)
            <h3 class="text-sm font-display font-bold text-gray-900 leading-snug">{{ $content->title }}</h3>
        @endif
    </div>

    {{-- Content --}}
    <div class="space-y-1.5">

        @switch($content->type)
            @case('TEXT')
                @php
                    // Detectar Mermaid antes de cualquier conversión
                    $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $bodyHtml) === 1;
                    if (!$isMermaid) {
                        $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($bodyHtml)) === 1;
                    }

                    // Si no es Mermaid: convertir Markdown a HTML
                    if (!$isMermaid) {
                        $trimmed = trim($bodyHtml);
                        if ($trimmed !== '' && !preg_match('/<[a-z\/][^>]*>/i', $trimmed)) {
                            $bodyHtml = Str::markdown($bodyHtml);
                        }
                    }

                    // Template detection (solo texto/Markdown, no Mermaid)
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
                    if ($isMermaid) {
                        $tpl = 'mermaid';
                    } elseif (preg_match('/\b(actividad|ejercicio|resuelve|practica|tarea|completa|investiga|realiza|escribe|dibuja|explica|elabora|construye|crea|diseña)\b/i', $plainText) && $textLen < 600) {
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

                @if($tpl === 'mermaid')
                    @php
                        preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $bodyHtml, $m);
                        $mermaidCode = trim(strip_tags($m[1] ?? ''));
                        if (empty($mermaidCode)) {
                            $mermaidCode = trim(strip_tags($bodyHtml));
                        }
                    @endphp
                    @if($mode === 'book')
                        <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-4 text-center">
                            <p class="text-sm text-amber-800">📊 Este diagrama se ve mejor en modo deslizar.</p>
                            <button type="button" @click="openSection({{ $sectionId }})"
                                    class="mt-2 text-sm font-semibold text-amber-700 underline hover:text-amber-900">
                                Ir a la sección
                            </button>
                        </div>
                    @else
                        <div wire:ignore x-data="mermaidEmbed()"
                             data-mermaid-code="{{ $mermaidCode }}"
                             class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                            <div x-ref="target" class="w-full min-h-0"></div>
                        </div>
                    @endif
                @elseif($tpl === 'concept')
                    <div class="{{ $bookModeClass }} bg-white border-l-4 border-emerald-400 rounded-r-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base leading-none">💡</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Concepto</span>
                        </div>
                        <x-lms.math-text
                            :content="$bodyHtml"
                            class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content" />
                    </div>
                @elseif($tpl === 'list')
                    <div class="{{ $bookModeClass }} bg-white rounded-xl p-3 sm:p-4 border border-gray-200">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base leading-none">📋</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Lista</span>
                        </div>
                        <x-lms.math-text
                            :content="$bodyHtml"
                            class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none prose-ul:list-disc prose-ol:list-decimal lms-content" />
                    </div>
                @elseif($tpl === 'quote')
                    <div class="{{ $bookModeClass }} bg-white border-l-4 border-amber-500 rounded-r-xl p-3 sm:p-4">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl leading-none text-amber-300/70 font-serif shrink-0">"</span>
                            <x-lms.math-text
                                :content="$bodyHtml"
                                class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content" />
                        </div>
                    </div>
                @elseif($tpl === 'question')
                    <div class="{{ $bookModeClass }} bg-white border border-sky-200 rounded-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base leading-none">💭</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700">Pregunta</span>
                        </div>
                        <x-lms.math-text
                            :content="$bodyHtml"
                            class="text-[17px] text-sky-900 leading-7 prose prose-sm max-w-none lms-content" />
                    </div>
                @elseif($tpl === 'activity')
                    <div class="{{ $bookModeClass }} bg-white border-2 border-dashed border-amber-300/60 rounded-xl p-3 sm:p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base leading-none">✏️</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Actividad</span>
                        </div>
                        <x-lms.math-text
                            :content="$bodyHtml"
                            class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content" />
                    </div>
                @else
                    <div class="{{ $bookModeClass }} bg-white rounded-xl p-3 sm:p-4 border border-gray-200">
                        <x-lms.math-text
                            :content="$bodyHtml"
                            class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content" />
                    </div>
                @endif
                @break

            @case('IMAGE')
                @php
                    $imgUrl = $content->media?->public_url ?? '';
                    $imgAlt = $content->title ?? 'Imagen';
                @endphp
                @if($imgUrl)
                    <div x-data="{ loaded: false, failed: false, retry() { this.failed = false; this.loaded = false; const img = this.$refs.img; const src = img.getAttribute('data-src'); img.src = ''; requestAnimationFrame(() => img.src = src); } }" class="{{ $bookModeClass }} rounded-xl overflow-hidden border border-gray-200 bg-white">
                        {{-- Loading skeleton --}}
                        <div x-show="!loaded && !failed"
                             class="flex items-center justify-center h-48 sm:h-64 bg-gray-100 animate-pulse">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{-- Error fallback --}}
                        <div x-show="failed" x-cloak
                             class="flex flex-col items-center justify-center h-48 sm:h-64 bg-gray-100 text-gray-500">
                            <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-xs sm:text-sm font-medium">Ups, algo salió mal. Inténtalo de nuevo.</p>
                            <button type="button" x-on:click="retry"
                                    class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 min-h-[44px] text-xs font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-lg transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reintentar
                            </button>
                        </div>
                        {{-- Image --}}
                        <img src="{{ $imgUrl }}"
                             data-src="{{ $imgUrl }}"
                             x-ref="img"
                             alt="{{ $imgAlt }}"
                             x-on:load="loaded = true"
                             x-on:error="loaded = true; failed = true"
                             x-show="loaded"
                             x-bind:class="failed ? 'hidden' : ''"
                             class="w-full h-auto max-h-96 object-contain bg-white"
                             loading="lazy">
                        @if($content->body)
                            <div class="px-3 sm:px-4 py-2 text-xs text-gray-500 border-t border-gray-100 bg-white">
                                {{ strip_tags($content->body) }}
                            </div>
                        @endif
                    </div>
                @else
                    @php $isSvgBody = str_contains($content->body ?? '', '<svg'); @endphp
                    {{-- No media — show fallback with body as text if present --}}
                    <div class="bg-white border border-amber-200 rounded-xl p-2.5 sm:p-3">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">
                                {{ $isSvgBody ? 'Diagrama' : 'Imagen no disponible' }}
                            </span>
                        </div>
                        @if($content->body)
                            <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none {{ $isSvgBody ? 'lms-svg-diagram overflow-x-auto' : '' }}">{!! $content->body !!}</div>
                        @else
                            <p class="text-sm text-gray-500 italic">No hay imagen disponible para este contenido.</p>
                        @endif
                    </div>
                @endif
                @break

            @case('VIDEO')
                @if($content->media?->isLocal())
                    <div class="{{ $bookModeClass }} rounded-xl overflow-hidden border border-gray-200 bg-black">
                        <video controls class="w-full aspect-video" preload="metadata">
                            <source src="{{ $content->media->public_url }}" type="{{ $content->media->mime_type }}">
                        </video>
                    </div>
                @elseif($content->media?->provider === 'YOUTUBE')
                    @php preg_match('/[?&]v=([^&]+)/', $content->media->external_url ?? '', $m); $vid = $m[1] ?? ''; @endphp
                    @if($vid)
                        <div class="{{ $bookModeClass }} aspect-video rounded-xl overflow-hidden border border-gray-200 bg-black">
                            <iframe src="https://www.youtube.com/embed/{{ $vid }}"
                                    class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                        </div>
                    @endif
                @endif
                @break

            @case('EMBED')
                <div class="{{ $bookModeClass }} aspect-video rounded-xl overflow-hidden border border-gray-200 bg-white">
                    {!! $content->body !!}
                </div>
                @break

            @case('FILE_PREVIEW')
                @if($content->media)
                    <div class="{{ $bookModeClass }} rounded-xl overflow-hidden border border-gray-200" style="height: min(600px, 80vh);">
                        <iframe src="{{ $content->media->public_url }}" class="w-full h-full" loading="lazy"></iframe>
                    </div>
                @endif
                @break

            @case('AUDIO')
                @if($content->media)
                    <div class="{{ $bookModeClass }} bg-white rounded-xl p-3 border border-gray-200">
                        <div class="flex items-center gap-3 mb-1.5">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 01-3 3z"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-500">Audio</span>
                        </div>
                        <audio controls class="w-full" preload="metadata">
                            <source src="{{ $content->media->public_url }}" type="{{ $content->media->mime_type }}">
                        </audio>
                    </div>
                @endif
                @break

            @case('HTML')
                @php
                    $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $content->body ?? '') === 1;
                    if (!$isMermaid) {
                        $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($content->body ?? '')) === 1;
                    }
                @endphp
                @if($isMermaid)
                    @php
                        preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $content->body, $m);
                        $mermaidCode = trim(strip_tags($m[1] ?? ''));
                        if (empty($mermaidCode)) {
                            $mermaidCode = trim(strip_tags($content->body));
                        }
                    @endphp
                    @if($mode === 'book')
                        <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-4 text-center">
                            <p class="text-sm text-amber-800">📊 Este diagrama se ve mejor en modo deslizar.</p>
                            <button type="button" @click="openSection({{ $sectionId }})"
                                    class="mt-2 text-sm font-semibold text-amber-700 underline hover:text-amber-900">
                                Ir a la sección
                            </button>
                        </div>
                    @else
                        <div wire:ignore x-data="mermaidEmbed()"
                             data-mermaid-code="{{ $mermaidCode }}"
                             class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                            <div x-ref="target" class="w-full min-h-0"></div>
                        </div>
                    @endif
                @else
                    <div class="{{ $bookModeClass }} bg-white rounded-xl p-3 sm:p-4 border border-gray-200 text-gray-900">
                        {!! $content->body !!}
                    </div>
                @endif
                @break

            @default
                @if($content->body)
                    <div class="{{ $bookModeClass }} bg-white rounded-xl p-3 sm:p-4 border border-gray-200">
                        <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none">{!! $content->body !!}</div>
                    </div>
                @endif
        @endswitch
    </div>
</div>
