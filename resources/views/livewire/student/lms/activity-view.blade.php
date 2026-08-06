<div class="max-w-3xl mx-auto px-3 sm:px-6 md:px-8 py-4 sm:py-6 md:py-8 space-y-4 sm:space-y-6"
     x-data="readingNav()">

    {{-- ═══════════════════════ READING PROGRESS ═══════════════════════ --}}
    <div class="sticky top-14 z-20 -mx-3 sm:-mx-6 md:-mx-8 -mt-4 sm:-mt-6 md:-mt-8">
        <div class="h-[3px] bg-gradient-to-r from-emerald-600 to-emerald-400 transition-[width] duration-150 ease-out"
             :style="`width: ${progress}%`"></div>
    </div>

    {{-- ═══════════════════════════════════════ BACK NAV ═══════════════════════════════════════ --}}
    <nav class="flex items-center gap-3 px-1">
        <a href="{{ route('student.lms.lessons') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 min-h-[44px] rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-200 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-500/20 transition-all duration-200 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Lecciones
        </a>
        <span class="text-[11px] text-gray-500 dark:text-gray-400 hidden sm:inline">
            / {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Actividad' }}
        </span>
    </nav>

    {{-- ═══════════════════════════════════════ HEADER ═══════════════════════════════════════ --}}
    <header class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">

                {{-- Title --}}
                <h1 class="text-lg sm:text-xl md:text-2xl font-display font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $activity->topic ?? 'Actividad' }}
                </h1>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    @php $pub = $activity->lmsPublication; @endphp
                    @if($pub && $pub->publish_at)
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($pub->publish_at)->translatedFormat('j M Y') }}
                            @if($pub->unpublish_at)
                                – {{ \Carbon\Carbon::parse($pub->unpublish_at)->translatedFormat('j M Y') }}
                            @endif
                        </span>
                    @endif
                    @if($isPreview)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-amber-100 dark:bg-amber-500/10 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Vista previa
                        </span>
                    @elseif($activity->lmsPublication?->status === 'PUBLISHED')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Publicada
                        </span>
                    @endif
                </div>
            </div>

            {{-- Status badge --}}
            @if($completed)
                <span class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-bold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Completada
                </span>
            @endif
        </div>

        {{-- Description --}}
        @if($activity->description)
            <p class="mt-3 sm:mt-4 text-sm sm:text-[15px] text-gray-700 dark:text-gray-200 leading-relaxed border-t border-gray-200 dark:border-gray-700/40 pt-3 sm:pt-4">
                {{ $activity->description }}
            </p>
        @endif
    </header>

    {{-- ═══════════════════════════════════════ PREVIEW BANNER ═══════════════════════════════════════ --}}
    @if($isPreview)
        <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl p-3 sm:p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="min-w-0">
                <p class="text-sm font-bold text-amber-900 dark:text-amber-300">Vista previa de la lección</p>
                <p class="text-xs sm:text-[13px] text-amber-700 dark:text-amber-400 leading-relaxed mt-0.5">
                    @if($activity->lmsPublication?->publish_at)
                        Esta lección se publicará el <strong>{{ \Carbon\Carbon::parse($activity->lmsPublication->publish_at)->translatedFormat('j M Y \a \l\a\s H:i') }}</strong> y por ahora solo puedes ver la primera sección.
                    @else
                        Esta lección aún no está publicada por completo. Solo puedes ver la primera sección.
                    @endif
                </p>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════ TABLA DE CONTENIDO ═══════════════════════ --}}
    @if($sections->count() > 1)
        <nav class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-2.5 sm:py-3 border-b border-gray-200 dark:border-gray-700/40">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/>
                </svg>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">Contenido</span>
                <span class="ml-auto text-[11px] font-medium text-gray-400 dark:text-gray-500 tabular-nums">{{ $sections->count() }} secciones</span>
            </div>
            <ol class="grid grid-cols-1 sm:grid-cols-2 gap-1 p-2 sm:p-3">
                @foreach($sections as $section)
                    <li>
                        <a href="#seccion-{{ $section->id }}"
                           @click.prevent="goTo({{ $section->id }})"
                           :class="activeId === {{ $section->id }}
                               ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30'
                               : 'text-gray-600 dark:text-gray-300 border-transparent hover:bg-gray-50 dark:hover:bg-gray-700/40'"
                           class="flex items-center gap-2 px-2.5 py-2 min-h-[44px] rounded-lg text-[13px] font-medium border transition-colors duration-150 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                            <span class="shrink-0 inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-700/60 text-gray-500 dark:text-gray-300 text-[10px] font-bold"
                                  :class="activeId === {{ $section->id }} ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : ''">
                                {{ $loop->iteration }}
                            </span>
                            <span class="truncate">{{ $section->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    {{-- ═══════════════════════════════════════ SECTIONS ═══════════════════════════════════════ --}}
    @forelse($sections as $section)
        @php
            $sectionUpper = mb_strtoupper($section->title ?? '');
            $bgHue = '';
            $accentColor = 'mint';
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
                $badgeClass = 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30';
                $accentColor = 'mint';
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

        <section id="seccion-{{ $section->id }}" wire:key="section-{{ $section->id }}"
                 class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            {{-- Section header --}}
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <span class="w-1 h-6 rounded-full {{ $accentDot }} shrink-0"></span>
                <h2 class="text-sm sm:text-[15px] font-display font-bold text-gray-900 dark:text-white flex-1 min-w-0">
                    {{ $section->title }}
                </h2>
                @if($badgeLabel)
                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                @endif
                @if($contentCount)
                    <span class="shrink-0 text-[11px] font-semibold text-emerald-800 dark:text-gray-200 bg-emerald-100 dark:bg-gray-700/70 px-2.5 py-0.5 rounded-full border border-emerald-300 dark:border-gray-600">
                        {{ $contentCount }} {{ $contentCount === 1 ? 'paso' : 'pasos' }}
                    </span>
                @endif
            </div>

            {{-- Section body --}}
            <div class="p-3 sm:p-5 space-y-3 sm:space-y-4">
                @foreach($section->visibleContents as $idx => $content)
                    @php
                        $stepNum = $idx + 1;
                        $bodyHtml = $content->body ?? '';
                        $isLast = $loop->last;
                    @endphp

                    <div wire:key="content-{{ $content->id }}" class="{{ $isLast ? '' : 'pb-3 sm:pb-4 border-b border-gray-200' }}">
                        {{-- Step number above content --}}
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shrink-0">{{ $stepNum }}</span>
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
                                        <div wire:ignore x-data="mermaidEmbed()"
                                             data-mermaid-code="{{ $mermaidCode }}"
                                             class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                                            <div x-ref="target" class="w-full min-h-0"></div>
                                        </div>
                                    @elseif($tpl === 'concept')
                                        <div class="bg-white border-l-4 border-emerald-400 rounded-r-xl p-3 sm:p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-base leading-none">💡</span>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Concepto</span>
                                            </div>
                                            <x-lms.math-text
                                                :content="$bodyHtml"
                                                class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content" />
                                        </div>
                                    @elseif($tpl === 'list')
                                        <div class="bg-white rounded-xl p-3 sm:p-4 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-base leading-none">📋</span>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Lista</span>
                                            </div>
                                            <x-lms.math-text
                                                :content="$bodyHtml"
                                                class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none prose-ul:list-disc prose-ol:list-decimal lms-content" />
                                        </div>
                                    @elseif($tpl === 'quote')
                                        <div class="bg-white border-l-4 border-amber-500 rounded-r-xl p-3 sm:p-4">
                                            <div class="flex items-start gap-3">
                                                <span class="text-2xl leading-none text-amber-300/70 font-serif shrink-0">"</span>
                                                <x-lms.math-text
                                                    :content="$bodyHtml"
                                                    class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content" />
                                            </div>
                                        </div>
                                    @elseif($tpl === 'question')
                                        <div class="bg-white border border-sky-200 rounded-xl p-3 sm:p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-base leading-none">💭</span>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700">Pregunta</span>
                                            </div>
                                            <x-lms.math-text
                                                :content="$bodyHtml"
                                                class="text-[17px] text-sky-900 leading-7 prose prose-sm max-w-none lms-content" />
                                        </div>
                                    @elseif($tpl === 'activity')
                                        <div class="bg-white border-2 border-dashed border-amber-300/60 rounded-xl p-3 sm:p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-base leading-none">✏️</span>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Actividad</span>
                                            </div>
                                            <x-lms.math-text
                                                :content="$bodyHtml"
                                                class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content" />
                                        </div>
                                    @else
                                        <div class="bg-white rounded-xl p-3 sm:p-4 border border-gray-200">
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
                                        <div x-data="{ loaded: false, failed: false, retry() { this.failed = false; this.loaded = false; const img = this.$refs.img; const src = img.getAttribute('data-src'); img.src = ''; requestAnimationFrame(() => img.src = src); } }" class="rounded-xl overflow-hidden border border-gray-200 bg-white">
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
                                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-black">
                                            <video controls class="w-full aspect-video" preload="metadata">
                                                <source src="{{ $content->media->public_url }}" type="{{ $content->media->mime_type }}">
                                            </video>
                                        </div>
                                    @elseif($content->media?->provider === 'YOUTUBE')
                                        @php preg_match('/[?&]v=([^&]+)/', $content->media->external_url ?? '', $m); $vid = $m[1] ?? ''; @endphp
                                        @if($vid)
                                            <div class="aspect-video rounded-xl overflow-hidden border border-gray-200 bg-black">
                                                <iframe src="https://www.youtube.com/embed/{{ $vid }}"
                                                        class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                                            </div>
                                        @endif
                                    @endif
                                    @break

                                @case('EMBED')
                                    <div class="aspect-video rounded-xl overflow-hidden border border-gray-200 bg-white">
                                        {!! $content->body !!}
                                    </div>
                                    @break

                                @case('FILE_PREVIEW')
                                    @if($content->media)
                                        <div class="rounded-xl overflow-hidden border border-gray-200" style="height: min(600px, 80vh);">
                                            <iframe src="{{ $content->media->public_url }}" class="w-full h-full" loading="lazy"></iframe>
                                        </div>
                                    @endif
                                    @break

                                @case('AUDIO')
                                    @if($content->media)
                                        <div class="bg-white rounded-xl p-3 border border-gray-200">
                                            <div class="flex items-center gap-3 mb-1.5">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
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
                                        <div wire:ignore x-data="mermaidEmbed()"
                                             data-mermaid-code="{{ $mermaidCode }}"
                                             class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                                            <div x-ref="target" class="w-full min-h-0"></div>
                                        </div>
                                    @else
                                        <div class="bg-white rounded-xl p-3 sm:p-4 border border-gray-200 text-gray-900">
                                            {!! $content->body !!}
                                        </div>
                                    @endif
                                    @break

                                @default
                                    @if($content->body)
                                        <div class="bg-white rounded-xl p-3 sm:p-4 border border-gray-200">
                                            <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none">{!! $content->body !!}</div>
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
                    <div class="space-y-3 pt-2 border-t border-gray-200">
                        @foreach($sectionEmbeds as $embed)
                            <div class="bg-white border border-fuchsia-200 rounded-xl p-2.5 sm:p-3 html-embed-item">
                                @if($embed->title)
                                    <h4 class="text-sm font-bold text-gray-900 mb-2">{{ $embed->title }}</h4>
                                @endif
                                @if($embed->is_mermaid ?? false)
                                    <div wire:ignore x-data="mermaidEmbed()"
                                         data-mermaid-code="{{ trim(strip_tags($embed->html_content)) }}"
                                         class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                                        <div x-ref="target" class="w-full min-h-0"></div>
                                    </div>
                                @else
                                    <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none html-embed-content">
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
                    <div class="space-y-2 pt-2 border-t border-gray-200">
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
                    <div class="space-y-2 pt-2 border-t border-gray-200">
                        @foreach($secLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-3 p-3 min-h-[44px] bg-white border border-blue-200 rounded-xl hover:bg-blue-50 transition-colors group focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-blue-800 truncate">{{ $link->title }}</p>
                                    <p class="text-xs text-blue-500 truncate">{{ $link->url }}</p>
                                </div>
                                <span class="shrink-0 text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded border border-blue-200">{{ $link->link_type ?? 'Enlace' }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @empty
        <div class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 p-6 sm:p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">No hay contenido disponible</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Esta actividad no tiene secciones visibles.</p>
        </div>
    @endforelse

    {{-- ═══════════════════════════════════════ RESOURCES (no vinculados) ═══════════════════════════════════════ --}}
    @php
        $unlinkedResources = $resources->filter(fn($r) => empty($r->section_id));
    @endphp
    @if($unlinkedResources->isNotEmpty())
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Recursos descargables</h2>
                <span class="ml-auto text-[11px] text-gray-400 dark:text-gray-500">{{ $unlinkedResources->count() }} {{ $unlinkedResources->count() === 1 ? 'archivo' : 'archivos' }}</span>
            </div>
            <div class="p-3 sm:p-5 space-y-2">
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
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Referencias y enlaces</h2>
            </div>
            <div class="p-3 sm:p-5 space-y-2">
                @foreach($unlinkedLinks as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-3 p-3 bg-white border border-blue-200 rounded-xl hover:bg-blue-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-blue-800 truncate">{{ $link->title }}</p>
                            @if($link->description)
                                <p class="text-xs text-gray-500 truncate">{{ $link->description }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 text-[10px] font-medium text-blue-500 bg-blue-100 px-2 py-0.5 rounded border border-blue-200">{{ $link->link_type ?? 'Enlace' }}</span>
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
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Contenido embebido</h2>
            </div>
            <div class="p-2.5 sm:p-4 space-y-2">
                @foreach($unlinkedEmbeds as $embed)
                    <div class="bg-white border border-fuchsia-200 rounded-xl p-2.5 sm:p-3 html-embed-item">
                        @if($embed->title)
                            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ $embed->title }}</h4>
                        @endif
                        @if($embed->is_mermaid ?? false)
                            <div wire:ignore x-data="mermaidEmbed()"
                                 data-mermaid-code="{{ trim(strip_tags($embed->html_content)) }}"
                                 class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                                <div x-ref="target" class="w-full min-h-0"></div>
                            </div>
                        @else
                            <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none html-embed-content">
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
        <section class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            <div class="flex items-center gap-2 px-3 sm:px-5 py-2.5 sm:py-3.5 border-b border-emerald-200 dark:border-gray-700/40 bg-emerald-50 dark:bg-gray-800/70">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Comentarios</h2>
            </div>
            <div class="p-3 sm:p-5 space-y-3">
                {{-- Form --}}
                <form wire:submit="saveComment" class="flex items-end gap-2">
                    <textarea wire:model="newComment" placeholder="Escribe un comentario…" rows="2"
                              class="flex-1 resize-none bg-white dark:bg-gray-800 border border-gray-400 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-600 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all"
                              maxlength="1000"></textarea>
                    <button type="submit"
                            class="shrink-0 px-4 py-2.5 min-h-[44px] bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-xl transition-colors focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove wire:target="saveComment">Enviar</span>
                        <span wire:loading wire:target="saveComment">Enviando…</span>
                    </button>
                </form>
                @error('newComment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                {{-- List --}}
                <div class="space-y-3">
                    @forelse($comments as $comment)
                        <div class="flex gap-3 p-3 rounded-xl bg-white dark:bg-gray-800/50">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">
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
                                <p class="text-sm text-gray-900 dark:text-gray-100 mt-1 leading-relaxed">{{ $comment->body }}</p>
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
    <div class="relative bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/50 rounded-xl p-4 sm:p-5">
        {{-- Accent bar --}}
        <div class="absolute top-0 left-4 right-4 h-px bg-gradient-to-r from-transparent via-emerald-400/40 to-transparent"></div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <a href="{{ route('student.lms.lessons') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-emerald-700 dark:hover:text-emerald-300 bg-gray-100 dark:bg-gray-800/50 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-500/5 hover:border-emerald-300/70 dark:hover:border-emerald-500/20 transition-all duration-200 min-h-[44px] group focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a lecciones
            </a>

            @if(!$isPreview && $activity->lmsPublication?->status === 'PUBLISHED' && !$completed)
                <button wire:click="markComplete"
                        wire:loading.attr="disabled"
                        wire:target="markComplete"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 min-h-[48px] w-full sm:w-auto text-base font-bold text-white bg-gradient-to-br from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 shadow-sm shadow-emerald-600/20 hover:shadow-emerald-500/30 rounded-xl transition-all duration-200 active:scale-[0.97] disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                    <span wire:loading.remove wire:target="markComplete" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Marcar como completada
                    </span>
                    <span wire:loading wire:target="markComplete" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Guardando…
                    </span>
                </button>
            @elseif($completed)
                <div class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl min-h-[44px] select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Completada
                </div>
            @endif
        </div>
    </div>

    {{-- Estilos para Mermaid fullscreen / zoom toolbar --}}
    {{-- Sin @once — CSS es idempotente, y Livewire maneja re-renders sin duplicados problemáticos --}}
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

        /* ── Mermaid fill height ── */
        .mermaid-fill-height {
            min-height: 0;
        }
        .mermaid-fill-height svg {
            width: 100% !important;
            flex: 1 !important;
        }

        /* ── lms-content: alto contraste en headings, tablas, blockquotes ── */
        .lms-content {
            font-size: 17px !important;
            line-height: 1.75 !important;
            color: #1e293b !important;
        }
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
            border-bottom: 1.5px solid #CAE8BD !important;
            color: #4C7C3B !important;
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
            border-left: 4px solid #B0DB9C !important;
            background: #ECFAE5 !important;
            padding: 0.75em 1.25em !important;
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
            line-height: 1.6 !important;
        }
        .lms-content table thead {
            border-bottom: 2px solid #B0DB9C !important;
        }
        .lms-content table th {
            background-color: #DDF6D2 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 0.85em !important;
            text-transform: uppercase !important;
            letter-spacing: 0.025em !important;
            padding: 0.65rem 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            text-align: left !important;
            vertical-align: top !important;
        }
        .lms-content table td {
            padding: 0.55rem 0.75rem !important;
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
            line-height: 1.75 !important;
        }
        .lms-content p:first-child {
            margin-top: 0 !important;
        }
        .lms-content ul, .lms-content ol {
            margin: 0.6em 0 !important;
            padding-left: 1.4em !important;
        }
        .lms-content li {
            margin: 0.3em 0 !important;
            line-height: 1.7 !important;
            color: #1e293b !important;
        }
        .lms-content strong {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .lms-content em {
            color: #334155 !important;
            font-style: italic !important;
        }
        .lms-content code {
            font-size: 0.875em !important;
            font-weight: 600 !important;
            padding: 0.15em 0.4em !important;
            border-radius: 4px !important;
            background: #f1f5f9 !important;
            color: #be123c !important;
            border: 1px solid #e2e8f0 !important;
        }
        .lms-content pre {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 1em !important;
            overflow-x: auto !important;
            margin: 1em 0 !important;
            font-size: 0.875em !important;
            line-height: 1.6 !important;
        }
        .lms-content pre code {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            color: #1e293b !important;
            font-weight: 400 !important;
        }
        .lms-content img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 6px !important;
            margin: 1em 0 !important;
        }
        .lms-content a {
            color: #5E9849 !important;
            text-decoration: underline !important;
            text-underline-offset: 2px !important;
            text-decoration-thickness: 1px !important;
        }
        .lms-content a:hover {
            color: #4C7C3B !important;
            text-decoration-thickness: 2px !important;
        }
        .lms-content hr {
            border: none !important;
            border-top: 1.5px solid #e2e8f0 !important;
            margin: 1.5em 0 !important;
        }

        /* ── Diagramas SVG embebidos en contenido ── */
        .lms-svg-diagram svg {
            max-width: 100% !important;
            height: auto !important;
        }

        /* ── Anclas de sección: dejar espacio bajo el navbar sticky ── */
        [id^="seccion-"] {
            scroll-margin-top: 5.5rem;
        }

        /* ── Movimiento reducido: respeta prefers-reduced-motion ── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    {{-- ═══════════════════════ READING NAV (progreso + scroll-spy) ═══════════════════════ --}}
    @once
        <script>
            document.addEventListener('alpine:init', function () {
                if (Alpine._readingNavRegistered) return;
                Alpine._readingNavRegistered = true;
                Alpine.data('readingNav', function () {
                    return {
                        progress: 0,
                        activeId: null,
                        init() {
                            this.update();
                            window.addEventListener('scroll', () => this.update(), { passive: true });
                        },
                        update() {
                            // Progreso de lectura: scroll vertical / alto del documento.
                            const doc = document.documentElement;
                            const scrollTop = window.scrollY || doc.scrollTop;
                            const height = doc.scrollHeight - window.innerHeight;
                            this.progress = height > 0 ? Math.min(100, Math.round((scrollTop / height) * 100)) : 0;

                            // Scroll-spy: última sección cuyo tope pasó la línea de lectura.
                            const sections = Array.from(document.querySelectorAll('[id^="seccion-"]'));
                            const offset = 120;
                            let current = null;
                            for (const el of sections) {
                                if (el.getBoundingClientRect().top <= offset) {
                                    current = Number(el.id.replace('seccion-', ''));
                                }
                            }
                            this.activeId = current ?? (sections.length ? Number(sections[0].id.replace('seccion-', '')) : null);
                        },
                        goTo(id) {
                            const el = document.getElementById('seccion-' + id);
                            if (!el) return;
                            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                            el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
                        },
                    };
                });
            });
        </script>
    @endonce
</div>
