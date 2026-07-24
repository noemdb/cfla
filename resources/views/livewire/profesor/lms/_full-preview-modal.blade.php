            {{-- ═══════════ MODAL VISTA PREVIA (full-screen) ═══════════ --}}
            @if($showFullPreview)
                <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="full-preview-modal">
                    <div class="fixed inset-0 bg-slate-900/95 backdrop-blur-md"
                         wire:click="$set('showFullPreview', false)"></div>

                    <div class="relative min-h-screen flex items-start justify-center p-4 pt-10">
                        <div class="relative w-full max-w-5xl bg-slate-800 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">

                            {{-- Header --}}
                            <div class="flex items-center justify-between px-6 py-2 bg-slate-700/50 border-b border-slate-700">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <div>
                                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Vista Previa</h2>
                                        <p class="text-[11px] text-slate-400">Así se verá la lección al publicarse</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-700 text-slate-300 border border-slate-600/50 ml-auto">
                                    {{ count($this->previewSections) }} secciones
                                </span>
                                <button wire:click="$set('showFullPreview', false)"
                                        class="p-2 hover:bg-white/10 rounded-lg transition-all text-slate-400 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Body: 2 columnas --}}
                            <div class="flex flex-1 overflow-hidden min-h-0" x-data="tocNavigation()">
                                {{-- ═══ SIDEBAR TOC ═══ --}}
                                <aside class="hidden lg:block w-56 shrink-0 border-r border-slate-700/50 bg-slate-800/80 overflow-y-auto p-3 sticky top-0 self-start max-h-[calc(100vh-12rem)]">
                                    <div class="flex items-center gap-1.5 mb-2 px-1">
                                        <span class="text-xs text-slate-400">📑</span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Índice</span>
                                        <span class="ml-auto text-[10px] font-mono text-slate-600">{{ count($this->previewSections) }} sec.</span>
                                    </div>
                                    <div class="space-y-0.5">
                                        @foreach($this->previewSections as $sIdx => $section)
                                            @php
                                                $hasContent = !empty(array_filter($section['contents'] ?? [], fn($c) => !empty($c['body'])));
                                            @endphp
                                            <button @click="scrollTo({{ $sIdx }})"
                                                    :class="activeSection === {{ $sIdx }} ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700/40 border-transparent'"
                                                    class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left transition-all text-xs border">
                                                <span class="flex items-center justify-center w-5 h-5 rounded {{ $hasContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-700/60 text-slate-500' }} text-[10px] font-mono shrink-0">
                                                    {{ str_pad($sIdx + 1, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                                <span class="truncate flex-1">{{ $section['title'] }}</span>
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $hasContent ? 'bg-emerald-400/60' : 'bg-slate-600/40' }}"></span>
                                            </button>
                                        @endforeach
                                    </div>
                                </aside>

                                {{-- ═══ CONTENIDO ═══ --}}
                                <div class="flex-1 overflow-y-auto p-6 space-y-5" x-ref="contentArea">
                                {{-- Header preview --}}
                                <div class="border-b border-slate-700 pb-4">
                                    <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-1">
                                        {{ $selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura' }}
                                    </p>
                                    <h2 class="text-lg font-bold text-white">{{ $lessonTitle ?: 'Título de la lección' }}</h2>
                                    @if($lessonDescription)
                                        <p class="text-sm text-slate-400 mt-1">{{ $lessonDescription }}</p>
                                    @endif
                                    @if($selectedActivity)
                                        <p class="text-xs text-slate-500 mt-2">
                                            {{ \Carbon\Carbon::parse($selectedActivity->finicial)->format('d/m/Y') }} &mdash; {{ \Carbon\Carbon::parse($selectedActivity->ffinal)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Secciones --}}
                                @forelse($this->previewSections as $sIdx => $section)
                                    <div data-section-index="{{ $sIdx }}" x-data="{ expanded: true }" class="space-y-3 scroll-mt-20">
                                        <div class="flex items-center gap-2">
                                            <button @click="expanded = !expanded"
                                                    class="p-1 -ml-1 rounded-lg hover:bg-slate-700/50 text-slate-500 hover:text-slate-300 transition-all"
                                                    :class="expanded ? 'rotate-90' : ''">
                                                <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                            <span class="w-1 h-5 bg-emerald-500 rounded-full shrink-0"></span>
                                            <h3 class="text-base font-bold text-slate-100">{{ $section['title'] }}</h3>
                                            <span class="text-[10px] text-slate-600 font-mono ml-auto">
                                                {{ count($section['contents'] ?? []) }} bloques
                                            </span>
                                        </div>
                                        <div x-show="expanded" x-transition:enter.duration.200 x-collapse>
                                        @foreach($section['contents'] as $content)
                                            @php
                                                $rawBody = $content['body'] ?? '';
                                                $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $rawBody) === 1;
                                                if (!$isMermaid) {
                                                    $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($rawBody)) === 1;
                                                }
                                            @endphp
                                            @if($content['title'])
                                                <h4 class="text-sm font-semibold text-slate-300">{{ $content['title'] }}</h4>
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
                                                     class="w-full bg-slate-800 rounded-lg p-4 overflow-x-auto border border-slate-700/50">
                                                    <div x-ref="target" class="w-full"></div>
                                                </div>
                                            @else
                                                <x-lms.math-text
                                                :content="$this->renderContentBody($rawBody)"
                                                class="text-sm text-slate-400 leading-relaxed prose prose-invert prose-sm max-w-none" />
                                            @endif
                                        @endforeach

                                        {{-- Recursos vinculados a esta sección --}}
                                        @php
                                            $secResources = collect($wizardResources)->where('section_id', $section['id'])->values()->all();
                                            $secLinks = collect($wizardLinks)->where('section_id', $section['id'])->values()->all();
                                            $secEmbeds = collect($wizardHtmlEmbeds)->where('section_id', $section['id'])->values()->all();
                                            $hasSecResources = count($secResources) > 0 || count($secLinks) > 0 || count($secEmbeds) > 0;
                                        @endphp
                                        @if($hasSecResources)
                                            <div class="border-t border-slate-700/40 pt-3 mt-2 space-y-2">
                                                @foreach($secResources as $res)
                                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 border border-slate-700/20 rounded-lg">
                                                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="text-sm text-slate-300 truncate flex-1">{{ $res['display_name'] }}</span>
                                                        <span class="text-xs text-slate-500">{{ $res['media']['size_for_humans'] ?? '' }}</span>
                                                    </div>
                                                @endforeach
                                                @foreach($secLinks as $link)
                                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 border border-slate-700/20 rounded-lg">
                                                        <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                        <span class="text-sm text-slate-300 truncate flex-1">{{ $link['title'] }}</span>
                                                        <span class="text-xs text-slate-500">({{ $link['link_type'] }})</span>
                                                    </div>
                                                @endforeach
                                                @foreach($secEmbeds as $embed)
                                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 border border-slate-700/20 rounded-lg">
                                                        <svg class="w-4 h-4 text-fuchsia-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                        </svg>
                                                        <span class="text-sm text-slate-300 truncate flex-1">{{ $embed['title'] ?? 'Embed HTML' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-16 h-16 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">No hay contenido disponible</p>
                                        <p class="text-xs text-slate-600 mt-1">Agrega secciones y contenido en el paso 2 del asistente.</p>
                                    </div>
                                @endforelse

                                {{-- Recursos no vinculados (aparecen al final) --}}
                                @php
                                    $unlinkedResources = collect($wizardResources)->filter(fn($r) => empty($r['section_id']))->values()->all();
                                    $unlinkedLinks = collect($wizardLinks)->filter(fn($l) => empty($l['section_id']))->values()->all();
                                    $unlinkedEmbeds = collect($wizardHtmlEmbeds)->filter(fn($e) => empty($e['section_id']))->values()->all();
                                @endphp
                                @if(count($unlinkedResources) > 0)
                                    <div class="border-t border-slate-700 pt-4 space-y-2">
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            Recursos
                                        </h4>
                                        @foreach($unlinkedResources as $res)
                                            <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 rounded-lg">
                                                <span class="text-sm text-slate-300">{{ $res['display_name'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if(count($unlinkedLinks) > 0)
                                    <div class="border-t border-slate-700 pt-4 space-y-2">
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Enlaces
                                        </h4>
                                        @foreach($unlinkedLinks as $link)
                                            <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 rounded-lg">
                                                <span class="text-sm text-slate-300">{{ $link['title'] }}</span>
                                                <span class="text-xs text-slate-500">({{ $link['link_type'] }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if(count($unlinkedEmbeds) > 0)
                                    <div class="border-t border-slate-700 pt-4 space-y-2">
                                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            HTML Embeds
                                        </h4>
                                        @foreach($unlinkedEmbeds as $embed)
                                            <div class="flex items-center gap-3 px-3 py-2 bg-slate-700/30 rounded-lg">
                                                <span class="text-sm text-slate-300">{{ $embed['title'] ?? 'Embed HTML' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div> {{-- Cierra content area --}}
                            </div> {{-- Cierra flex container --}}
                        </div>
                    </div>
                </div>

                {{-- Botón TOC flotante (mobile) --}}
                <div x-data="{ mobileTocOpen: false }"
                     x-init="$watch('mobileTocOpen', val => document.body.classList.toggle('overflow-hidden', val))">
                    {{-- Floating button --}}
                    <button @click="mobileTocOpen = true"
                            class="lg:hidden fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white shadow-xl shadow-emerald-900/30 flex items-center justify-center transition-all active:scale-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Overlay TOC --}}
                    <div x-show="mobileTocOpen" x-cloak
                         x-transition:enter.duration.200
                         class="lg:hidden fixed inset-0 z-[9999]">
                        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="mobileTocOpen = false"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-slate-800 border-t border-slate-700 rounded-t-2xl p-4 max-h-[50vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-2 px-1">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Índice</span>
                                <button @click="mobileTocOpen = false" class="p-1.5 text-slate-500 hover:text-white rounded-lg hover:bg-slate-700/50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="space-y-0.5">
                                @foreach($this->previewSections as $sIdx => $section)
                                    @php
                                        $hasContent = !empty(array_filter($section['contents'] ?? [], fn($c) => !empty($c['body'])));
                                    @endphp
                                    <button @click="mobileTocOpen = false; document.querySelector('[x-data=\'tocNavigation()\']')?.__x.$data.scrollTo({{ $sIdx }})"
                                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left transition-all text-sm hover:bg-slate-700/50">
                                        <span class="flex items-center justify-center w-6 h-6 rounded {{ $hasContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-700/60 text-slate-500' }} text-xs font-mono shrink-0">
                                            {{ $sIdx + 1 }}
                                        </span>
                                        <span class="text-slate-300 font-medium truncate">{{ $section['title'] }}</span>
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 ml-auto {{ $hasContent ? 'bg-emerald-400/60' : 'bg-slate-600/40' }}"></span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
