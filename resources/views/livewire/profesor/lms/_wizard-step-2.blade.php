                        {{-- ===== SLIDE EDITOR CON SIDEBAR PERSISTENTE ===== --}}
                        @php
                            $totalSlides = count($wizardSections);
                            $currentSlide = $wizardSections[$currentSlideIndex] ?? null;
                        @endphp

                        <div class="slide-editor-root bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg"
                             x-data="{
                                showSlideList: false,
                                sidebarCompact: $wire.entangle('sidebarCompact'),
                                dragIndex: null,
                                dragOverIndex: null,
                                editSlideTitle: false,
                                slideTitleBuffer: '{{ addslashes($wizardSections[$currentSlideIndex]['title'] ?? '') }}',

                                startDrag(idx) { this.dragIndex = idx; },
                                dragOver(idx) { if (this.dragIndex !== null && this.dragIndex !== idx) this.dragOverIndex = idx; },
                                endDrag() {
                                    if (this.dragIndex !== null && this.dragOverIndex !== null && this.dragIndex !== this.dragOverIndex) {
                                        $wire.call('moveSlide', this.dragIndex, this.dragOverIndex);
                                    }
                                    this.dragIndex = null; this.dragOverIndex = null;
                                },
                                cancelDrag() { this.dragIndex = null; this.dragOverIndex = null; }
                             }">

                            {{-- Slide Navigation Bar — compacta --}}
                            <div class="flex items-center justify-between gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-800/40 border-b border-gray-200 dark:border-slate-700/30">
                                <div class="flex items-center gap-1">
                                    <button wire:click="prevSlide"
                                            class="flex items-center gap-1 px-2 py-1.5 text-[11px] font-medium text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-all {{ $totalSlides <= 1 || $currentSlideIndex <= 0 ? 'opacity-40 pointer-events-none' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        <span class="hidden sm:inline">Anterior</span>
                                    </button>
                                    <span class="text-gray-300 dark:text-slate-600 mx-0.5">|</span>
                                    <button wire:click="nextSlide"
                                            class="flex items-center gap-1 px-2 py-1.5 text-[11px] font-medium text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-all {{ $totalSlides <= 1 || $currentSlideIndex >= $totalSlides - 1 ? 'opacity-40 pointer-events-none' : '' }}">
                                        <span class="hidden sm:inline">Siguiente</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    <span class="text-[11px] text-gray-400 dark:text-slate-500 font-mono ml-2">
                                        <span class="text-emerald-400 font-bold">{{ $currentSlideIndex + 1 }}</span>/{{ max(0, $totalSlides) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button @click="showSlideList = !showSlideList"
                                            class="lg:hidden p-1.5 text-gray-400 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-all"
                                            title="Lista de diapositivas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Mobile: Navegación rápida de diapositivas (collapsed) --}}
                            <div class="flex lg:hidden items-center gap-1 px-3 py-1.5 border-b border-gray-200 dark:border-slate-700/30 overflow-x-auto bg-gray-50/50 dark:bg-slate-800/20" x-show="!showSlideList">
                                <button @click="showSlideList = !showSlideList"
                                        class="flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 shrink-0 transition-all"
                                        title="Lista de diapositivas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                </button>
                                <span class="w-px h-4 bg-gray-200 dark:bg-slate-700 shrink-0"></span>
                                @if($totalSlides > 0)
                                    @foreach(range(0, $totalSlides - 1) as $sIdx)
                                        <button wire:click="goToSlide({{ $sIdx }})"
                                                @class([
                                                    'flex items-center justify-center w-7 h-7 rounded-full text-[10px] font-bold shrink-0 transition-all',
                                                    'bg-emerald-500 text-white shadow-sm ring-2 ring-emerald-400/30' => $sIdx === $currentSlideIndex,
                                                    'bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 hover:bg-gray-300 dark:hover:bg-slate-600' => $sIdx !== $currentSlideIndex,
                                                ])>
                                            {{ $sIdx + 1 }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>

                            {{-- 🔲 Sidebar + Contenido en flex row --}}
                            <div class="flex flex-col lg:flex-row">
                                {{-- ═══ SIDEBAR: Lista persistente de secciones (desktop) ═══ --}}
                                <aside :style="`width: ${sidebarCompact ? '3rem' : '14rem'}`" class="hidden lg:flex flex-col shrink-0 border-r border-gray-200 dark:border-slate-700/30 bg-gray-50/70 dark:bg-slate-900/40 transition-all duration-200" style="width: 14rem">
                                    <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-slate-700/30">
                                        <span x-show="!sidebarCompact" class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500">Secciones</span>
                                        <span x-show="!sidebarCompact" class="text-[10px] font-mono text-gray-400 dark:text-slate-600">{{ $totalSlides }}</span>
                                        <button wire:click="toggleSidebar"
                                                class="p-1 rounded-lg text-gray-400 dark:text-slate-500 hover:text-gray-700 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 transition-all ml-auto"
                                                title="Toggle sidebar">
                                            <svg x-show="!sidebarCompact" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                                            <svg x-show="sidebarCompact" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                        </button>
                                    </div>
                                    <div class="flex-1 overflow-y-auto p-2 space-y-0.5 min-h-[200px] max-h-[55vh]">
                                        @forelse($wizardSections as $sIdx2 => $sec)
                                            @php
                                                $secContent = $sec['contents'][0]['body'] ?? '';
                                                $hasSecContent = !empty($secContent);
                                                $isActive = $sIdx2 === $currentSlideIndex;
                                            @endphp
                                            <div wire:key="slide-list-{{ $sIdx2 }}"
                                                 @dragover.prevent="dragOver({{ $sIdx2 }})"
                                                 @dragleave.prevent="if (dragOverIndex === {{ $sIdx2 }}) dragOverIndex = null"
                                                 @drop.prevent="endDrag()">
                                                <button wire:click="goToSlide({{ $sIdx2 }})"
                                                        draggable="{{ $isPublished ? 'false' : 'true' }}"
                                                        @dragstart="startDrag({{ $sIdx2 }})"
                                                        @dragend="cancelDrag()"
                                                        @class([
                                                            'w-full flex items-center py-2 rounded-lg transition-all text-[11px] border cursor-grab active:cursor-grabbing group',
                                                            'bg-emerald-500/15 text-emerald-300 border-emerald-500/20' => $isActive,
                                                            'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/40 border-transparent' => !$isActive,
                                                        ])
                                                        :class="{
                                                            'opacity-40': dragIndex === {{ $sIdx2 }},
                                                            'border-t-2 border-t-emerald-400/40': dragOverIndex === {{ $sIdx2 }},
                                                            'px-2.5 gap-2 text-left': !sidebarCompact,
                                                            'px-1 justify-center': sidebarCompact
                                                        }">
                                                    <span class="flex items-center justify-center w-5 h-5 rounded shrink-0 text-[10px] font-mono {{ $hasSecContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-200 dark:bg-slate-700/60 text-gray-500 dark:text-slate-500' }}">
                                                        {{ $sIdx2 + 1 }}
                                                    </span>
                                                    <span x-show="!sidebarCompact" class="truncate flex-1">{{ $sec['title'] ?: 'Sin título' }}</span>
                                                    @if($hasSecContent)
                                                        <span x-show="!sidebarCompact" class="w-1.5 h-1.5 rounded-full bg-emerald-400/60 shrink-0"></span>
                                                    @else
                                                        <span x-show="!sidebarCompact" class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-slate-600/40 shrink-0"></span>
                                                    @endif
                                                    <svg x-show="!sidebarCompact" class="w-3 h-3 text-gray-400 dark:text-slate-600 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm-8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm-8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>
                                                </button>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <p class="text-[11px] text-gray-400 dark:text-slate-600">Sin secciones</p>
                                                <p class="text-[10px] text-gray-500 dark:text-slate-500 mt-1">Agrega una abajo</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <div x-show="!sidebarCompact" class="p-2 border-t border-gray-200 dark:border-slate-700/30 space-y-1">
                                        <button wire:click="addWizardSection"
                                                class="w-full flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-medium text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all" @disabled($isPublished)>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Nueva diapositiva
                                        </button>
                                        @if($totalSlides > 0)
                                            <button wire:click="confirmResetWizardSections"
                                                    class="w-full flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-medium text-gray-400 dark:text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all" @disabled($isPublished)>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Limpiar todo
                                            </button>
                                        @endif
                                    </div>
                                </aside>

                                {{-- ═══ CONTENIDO PRINCIPAL (slide actual) ═══ --}}
                                <div class="flex-1 min-w-0">
                                    @if($currentSlide)
                                        <div class="px-4 py-2" wire:key="slide-{{ $currentSlideIndex }}">
                                    {{-- Slide Title (editable inline) --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 text-emerald-400 text-xs font-bold shrink-0">
                                            {{ $currentSlideIndex + 1 }}
                                        </span>
                                        <input wire:model="wizardSections.{{ $currentSlideIndex }}.title"
                                               class="flex-1 bg-transparent border-b border-transparent hover:border-gray-400 dark:hover:border-slate-600 focus:border-emerald-500 text-sm font-bold text-gray-900 dark:text-white px-0 py-0.5 focus:outline-none transition-colors"
                                               placeholder="Titulo de la diapositiva" @disabled($isPublished)/>
                                        <button wire:click="toggleWizardSectionVisibility({{ $currentSlideIndex }})"
                                                class="p-1.5 rounded-lg transition-all {{ ($currentSlide['is_visible'] ?? true) ? 'text-emerald-400/60 hover:text-emerald-400 hover:bg-emerald-500/10' : 'text-gray-400 dark:text-slate-600 hover:text-gray-600 dark:hover:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-700/50' }}"
                                                title="{{ ($currentSlide['is_visible'] ?? true) ? 'Visible' : 'Oculto' }}" @disabled($isPublished)>
                                            @if($currentSlide['is_visible'] ?? true)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l22 22"/></svg>
                                            @endif
                                        </button>
                                    </div>

                                    {{-- Tab: Editor / Preview --}}
                                    <div x-data="{ editorTab: 'preview' }"
                                         @show-preview.window="editorTab = 'preview'">
                                        {{-- Tab buttons --}}
                                        <div class="flex gap-0.5 mb-2">
                                            <button @click="editorTab = 'edit'"
                                                    :class="editorTab === 'edit' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30' : 'text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 border-transparent'"
                                                    class="flex-1 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border border-b-0 transition-all text-center">
                                                <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Editor
                                            </button>
                                            <button @click="editorTab = 'preview'"
                                                    :class="editorTab === 'preview' ? 'text-fuchsia-300 bg-fuchsia-500/10 border-fuchsia-500/30' : 'text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 border-transparent'"
                                                    class="flex-1 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border border-b-0 transition-all text-center">
                                                <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Vista previa
                                            </button>
                                        </div>

                                        {{-- EDIT TAB: HTML Content Editor --}}
                                        <div x-show="editorTab === 'edit'" x-transition:enter.duration.150ms class="space-y-3">
                                            {{-- Título de la diapositiva --}}
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-1">Título de la diapositiva</label>
                                                <input type="text" wire:model="wizardSections.{{ $currentSlideIndex }}.title"
                                                       class="w-full bg-white dark:bg-slate-950/80 border border-gray-300 dark:border-slate-700/50 rounded-lg px-3 py-1.5 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all"
                                                       placeholder="Título de la diapositiva" @disabled($isPublished)/>
                                            </div>

                                            @if(isset($wizardSections[$currentSlideIndex]['contents'][0]))
                                                <textarea wire:model="wizardSections.{{ $currentSlideIndex }}.contents.0.body"
                                                          rows="12"
                                                          class="w-full bg-white dark:bg-slate-950/80 border border-gray-300 dark:border-slate-700/50 rounded-lg px-4 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all resize-y font-mono leading-relaxed"
                                                          placeholder="<!-- Escribe o pega el contenido HTML de esta diapositiva -->"
                                                          spellcheck="false" @disabled($isPublished)></textarea>
                                            @else
                                                <div class="text-center py-10 bg-gray-50 dark:bg-slate-900/50 border border-dashed border-gray-200 dark:border-slate-700/50 rounded-lg">
                                                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-gray-200 dark:bg-slate-700/30 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                    </div>
                                                    <p class="text-xs text-gray-400 dark:text-slate-500 font-medium mb-2">Esta diapositiva esta vacia</p>
                                                    <button wire:click="addWizardFirstBlock({{ $currentSlideIndex }})"
                                                            @disabled($isPublished)
                                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[11px] font-medium transition-all
                                                                   text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 active:scale-[0.97]">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        Crear bloque
                                                    </button>
                                                </div>
                                            @endif

                                            {{-- Block list with delete buttons --}}
                                            @php
                                                $allContents = $currentSlide['contents'] ?? [];
                                                $blockCount = count($allContents);
                                            @endphp
                                            @if($blockCount > 0)
                                                <div class="mt-3 space-y-1.5"
                                                     x-data="{ previewIndex: null }">
                                                    @foreach($allContents as $cIdx => $content)
                                                        <div class="flex items-start gap-2 px-3 py-2 rounded-lg transition-all text-xs
                                                                    {{ $cIdx === 0 ? 'bg-emerald-500/8 border border-emerald-500/10' : 'bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/30 hover:bg-gray-50 dark:hover:bg-slate-800/80' }}">
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center gap-1.5 mb-0.5">
                                                                    @if($cIdx === 0)
                                                                        <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-400/60">Editor principal</span>
                                                                    @endif
                                                                    <span class="text-[9px] font-mono uppercase px-1.5 py-0.5 rounded
                                                                                {{ ($content['type'] ?? 'TEXT') === 'TEXT' ? 'bg-sky-500/10 text-sky-400' : '' }}
                                                                                {{ ($content['type'] ?? '') === 'HTML' ? 'bg-amber-500/10 text-amber-400' : '' }}
                                                                                {{ ($content['type'] ?? '') === 'MEDIA' ? 'bg-fuchsia-500/10 text-fuchsia-400' : '' }}">
                                                                        {{ $content['type'] ?? 'TEXT' }}
                                                                    </span>
                                                                    <span class="text-gray-400 dark:text-slate-500 truncate max-w-[200px]"
                                                                          x-data="{ editing: false }"
                                                                          x-cloak>
                                                                        {{-- Display mode --}}
                                                                        <span x-show="!editing"
                                                                              class="inline-flex items-center gap-1 cursor-pointer hover:text-gray-700 dark:hover:text-slate-300 transition-colors"
                                                                              @click="editing = true">
                                                                            <span class="truncate max-w-[160px]">{{ $content['title'] ? Str::limit($content['title'], 40) : 'sin título' }}</span>
                                                                            <svg class="w-3 h-3 shrink-0 text-slate-600 hover:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                        </span>
                                                                        {{-- Edit mode --}}
                                                                        <span x-show="editing"
                                                                              x-cloak
                                                                              class="inline-flex items-center gap-1">
                                                                            <input x-ref="titleInput"
                                                                                   wire:model.blur="wizardSections.{{ $currentSlideIndex }}.contents.{{ $cIdx }}.title"
                                                                                   @keydown.escape="editing = false"
                                                                                   @keydown.enter="$wire.set('wizardSections.{{ $currentSlideIndex }}.contents.{{ $cIdx }}.title', $refs.titleInput.value).then(() => { editing = false })"
                                                                                   x-init="$nextTick(() => $refs.titleInput?.focus())"
                                                                                   class="w-full bg-white dark:bg-slate-900/60 border border-emerald-500/40 rounded px-1.5 py-0.5 text-xs text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-400" @disabled($isPublished)/>
                                                                            <button @click="$wire.set('wizardSections.{{ $currentSlideIndex }}.contents.{{ $cIdx }}.title', $refs.titleInput.value).then(() => { editing = false })"
                                                                                    class="p-1 rounded transition-all text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/15"
                                                                                    title="Guardar título" @disabled($isPublished)>
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                                            </button>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="text-[10px] text-gray-400 dark:text-slate-600 leading-relaxed line-clamp-1">
                                                                    @php
                                                                        $bodyText = strip_tags($content['body'] ?? '');
                                                                        $bodyText = preg_replace('/\s+/', ' ', $bodyText);
                                                                    @endphp
                                                                    {{ $bodyText ? Str::limit(trim($bodyText), 100) : '(vacio)' }}
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-1 shrink-0">
                                                                <button @click.prevent="previewIndex = {{ $cIdx }}"
                                                                        class="p-1.5 rounded-lg transition-all
                                                                               text-gray-400 dark:text-slate-600 hover:text-gray-700 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-slate-600/50">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                </button>
                                                                @if($blockCount > 1)
                                                                    <button wire:click="removeWizardContent({{ $currentSlideIndex }}, {{ $cIdx }})"
                                                                            wire:confirm="Eliminar este bloque de contenido?"
                                                                            class="p-1.5 rounded-lg transition-all
                                                                                   text-gray-400 dark:text-slate-600 hover:text-red-400 hover:bg-red-500/10" @disabled($isPublished)>
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Preview modal for this block --}}
                                                        <div x-show="previewIndex === {{ $cIdx }}"
                                                             x-cloak
                                                             x-transition:enter.duration.200
                                                             class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                                                             @keydown.escape.window="previewIndex = null">
                                                            {{-- Overlay --}}
                                                            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"
                                                                 @click="previewIndex = null"></div>
                                                            {{-- Modal card --}}
                                                            <div class="relative w-full max-w-3xl max-h-[85vh] bg-white rounded-lg shadow-2xl flex flex-col overflow-hidden">
                                                                {{-- Header --}}
                                                                <div class="flex items-center justify-between px-5 py-2 border-b border-gray-200 shrink-0">
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-[10px] font-mono font-bold uppercase px-2 py-0.5 rounded
                                                                                    {{ ($content['type'] ?? 'TEXT') === 'TEXT' ? 'bg-sky-100 text-sky-700' : '' }}
                                                                                    {{ ($content['type'] ?? '') === 'HTML' ? 'bg-amber-100 text-amber-700' : '' }}
                                                                                    {{ ($content['type'] ?? '') === 'MEDIA' ? 'bg-fuchsia-100 text-fuchsia-700' : '' }}">
                                                                            {{ $content['type'] ?? 'TEXT' }}
                                                                        </span>
                                                                        <span class="text-sm font-semibold text-gray-800">
                                                                            {{ $content['title'] ?? 'Sin titulo' }}
                                                                        </span>
                                                                    </div>
                                                                    <button @click="previewIndex = null"
                                                                            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </div>
                                                                {{-- Body --}}
                                                                <div class="flex-1 overflow-y-auto p-5">
                                                                    <div class="prose prose-sm prose-slate max-w-none !text-gray-800"
                                                                         style="color: #1e293b !important;">
                                                                        {!! $this->renderPreviewContent($content['body'] ?? '') !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- PREVIEW TAB: HTML Rendered --}}
                                        {{-- NOTA: slidePreviewContent() ya envuelve cada bloque en su        --}}
                                        {{-- estrategia de renderizado (mathContent() para texto,             --}}
                                        {{-- mermaidEmbed() para diagramas). Aquí solo se renderiza raw.      --}}
                                        <div x-show="editorTab === 'preview'" x-cloak x-transition:enter.duration.150ms>
                                            <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6 min-h-[200px] overflow-x-auto shadow-sm">
                                                @php $previewContent = trim($this->slidePreviewContent()); @endphp
                                                @if(!empty($previewContent))
                                                    <div class="slide-preview-wrapper" style="color: #1e293b; line-height: 1.7;">
                                                        {!! $previewContent !!}
                                                    </div>
                                                @else
                                                    <div class="text-center py-12 text-gray-400 dark:text-slate-400">
                                                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <p class="text-sm font-medium">Sin contenido para previsualizar</p>
                                                        <p class="text-xs mt-1">Genera contenido o escribe HTML en la pestana Editor</p>
                                                    </div>
                                                @endif
                                            </div>
                                            @php
                                                $hasMermaid = str_contains($previewContent, 'x-data="mermaidEmbed()"');
                                            @endphp
                                            @if($hasMermaid)
                                                <div class="flex items-center gap-1.5 mt-2 px-3 py-2 bg-fuchsia-500/10 border border-fuchsia-500/20 rounded-lg">
                                                    <svg class="w-4 h-4 text-fuchsia-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-[11px] text-fuchsia-300">Esta diapositiva contiene un diagrama Mermaid</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                @php $blockCount = count($wizardSections[$currentSlideIndex]['contents'] ?? []); @endphp
                                <div class="px-4 py-2 border-t border-gray-200 dark:border-slate-700/30 bg-gray-50 dark:bg-slate-900/30">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($blockCount >= 2)
                                            <span class="text-[10px] text-gray-400 dark:text-slate-500 italic px-1 mr-1">Máx. 2 bloques</span>
                                        @endif
                                        <button wire:click="generateSlideText"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideText"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                @disabled($isPublished)
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 active:scale-[0.97]
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Generar Texto
                                        </button>
                                        <button wire:click="generateSlideImage"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideImage"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                @disabled($isPublished)
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 hover:border-amber-500/40 active:scale-[0.97]
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                            Generar Imagen
                                        </button>
                                        <button wire:click="generateSectionIllustration"
                                                @click="editorTab = 'preview'"
                                                disabled
                                                wire:loading.attr="disabled"
                                                wire:target="generateSectionIllustration"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                @disabled($isPublished)
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-sky-400 bg-sky-500/10 hover:bg-sky-500/20 border border-sky-500/20 hover:border-sky-500/40 active:scale-[0.97]
                                                       disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
                                            Generar Ilustración
                                        </button>
                                        <button wire:click="generateSlideDiagram"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideDiagram"
                                                {{ $blockCount >= 2 ? 'disabled' : '' }}
                                                @disabled($isPublished)
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-fuchsia-400 bg-fuchsia-500/10 hover:bg-fuchsia-500/20 border border-fuchsia-500/20 hover:border-fuchsia-500/40 active:scale-[0.97]
                                                       {{ $blockCount >= 2 ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                            Generar Diagrama
                                        </button>

                                        <button wire:click="generateSlideHtmlTags"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideHtmlTags"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 hover:border-indigo-500/40 active:scale-[0.97]" @disabled($isPublished)>
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v4a1 1 0 001 1h4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6m-3-3v6"/></svg>
                                            Etiquetar HTML
                                        </button>

                                        {{-- Detectar y convertir expresiones matemáticas a LaTeX --}}
                                        <button title="Etiquetar con Notación matemática" wire:click="generateSlideMath"
                                                @click="editorTab = 'preview'"
                                                wire:loading.attr="disabled"
                                                wire:target="generateSlideMath"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[11px] font-medium transition-all duration-200
                                                       text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 active:scale-[0.97]" @disabled($isPublished)>
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM8 12h8m-4-4v8"/></svg>
                                            Etiquetar Not. Mat. 
                                        </button>

                                        <span class="w-px h-5 bg-slate-700/50 mx-1 ml-auto"></span>

                                        <button wire:click="removeWizardSection({{ $currentSlideIndex }})"
                                                wire:confirm="Eliminar esta diapositiva?"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-medium transition-all
                                                       text-red-400/70 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20" @disabled($isPublished)>
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Eliminar
                                        </button>

                                        <span class="w-px h-5 bg-red-900/50 mx-1"></span>

                                        <button wire:click="confirmResetWizardSections"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-medium transition-all
                                                       text-red-400/50 hover:text-red-300 hover:bg-red-500/15 border border-red-900/30 hover:border-red-500/30" @disabled($isPublished)>
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3 3m0 0l3-3m-3 3V8"/></svg>
                                            Limpiar todo
                                        </button>
                                    </div>
                                </div>

                                {{-- Debug: raw LLM response (solo visible si hay debugRawContent) --}}
                                @if($debugRawContent)
                                    <details class="border-t border-gray-200 dark:border-slate-700/30 bg-gray-50/50 dark:bg-slate-900/30">
                                        <summary class="px-4 py-2 text-[10px] font-mono text-gray-400 dark:text-slate-500 cursor-pointer hover:text-gray-600 dark:hover:text-slate-400 transition-colors select-none">
                                            🔍 Debug: respuesta cruda del LLM
                                        </summary>
                                        <pre class="px-4 py-3 text-[10px] font-mono text-gray-500 dark:text-slate-400 leading-relaxed whitespace-pre-wrap max-h-60 overflow-y-auto border-t border-gray-200 dark:border-slate-700/20">{{ $debugRawContent }}</pre>
                                    </details>
                                @endif

                                {{-- Generation Error --}}
                                @if($generatingSection === $currentSlideIndex && $generationError)
                                    <div class="px-4 py-2.5 bg-red-500/10 border-t border-red-500/20">
                                        <p class="text-xs text-red-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $generationError }}
                                        </p>
                                    </div>
                                @endif
                            @else
                                {{-- Empty State: No slides --}}
                                <div class="p-8 text-center">
                                    <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-gray-200 dark:bg-slate-700/30 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-500 dark:text-slate-400 mb-2">No hay diapositivas</h3>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mb-2">Agrega una seccion o genera la estructura con IA para empezar.</p>
                                    <div class="flex items-center justify-center gap-3">
                                        <button wire:click="generateStep2Sections"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-medium
                                                       text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 transition-all" @disabled($isPublished)>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Generar estructura con IA
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>


                            {{-- ═══ Mobile: Bottom Sheet con lista de secciones ═══ --}}
                            <div x-show="showSlideList" x-cloak x-transition:enter.duration.200ms
                                 class="fixed inset-0 z-50 lg:hidden" @click.away="showSlideList = false">
                                {{-- Backdrop --}}
                                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                                     @click="showSlideList = false"></div>
                                {{-- Sheet --}}
                                <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-800 rounded-t-2xl shadow-xl border-t border-gray-200 dark:border-slate-700 max-h-[60vh] overflow-hidden"
                                     @click.stop>
                                    {{-- Handle visual --}}
                                    <div class="flex items-center justify-center pt-2 pb-1">
                                        <span class="w-8 h-1 bg-gray-300 dark:bg-slate-600 rounded-full"></span>
                                    </div>
                                    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-slate-700/50">
                                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Secciones</span>
                                        <span class="text-[10px] font-mono text-gray-400">{{ $totalSlides }} diapositivas</span>
                                    </div>
                                    <div class="overflow-y-auto p-2 space-y-0.5 max-h-[calc(60vh-80px)]">
                                        @forelse($wizardSections as $sIdx2 => $sec)
                                            @php
                                                $secContent = $sec['contents'][0]['body'] ?? '';
                                                $hasSecContent = !empty($secContent);
                                            @endphp
                                            <button wire:click="goToSlide({{ $sIdx2 }}); showSlideList = false"
                                                    @class([
                                                        'w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-left transition-all text-xs',
                                                        'bg-emerald-500/15 text-emerald-300 border border-emerald-500/20' => $sIdx2 === $currentSlideIndex,
                                                        'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/40 border border-transparent' => $sIdx2 !== $currentSlideIndex,
                                                    ])>
                                                <span class="flex items-center justify-center w-5 h-5 rounded shrink-0 text-[10px] font-mono {{ $hasSecContent ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-200 dark:bg-slate-700/60 text-gray-500 dark:text-slate-500' }}">
                                                    {{ $sIdx2 + 1 }}
                                                </span>
                                                <span class="truncate flex-1">{{ $sec['title'] ?: 'Sin título' }}</span>
                                                @if($hasSecContent)
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400/60 shrink-0"></span>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-slate-600/40 shrink-0"></span>
                                                @endif
                                            </button>
                                        @empty
                                            <p class="text-center text-[11px] text-gray-400 dark:text-slate-600 py-6">Sin secciones aún</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                        </div>
