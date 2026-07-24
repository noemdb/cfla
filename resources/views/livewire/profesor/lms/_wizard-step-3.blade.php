                        <div wire:key="step3-recursos"
                             class="w-full bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden"
                             x-data="{ activeTab: 'resources', showConfirmDeleteResources: false }">
                            {{-- Header --}}
                            <div class="flex items-center gap-3 px-5 py-2.5 bg-gray-100 dark:bg-slate-800/40 border-b border-gray-200 dark:border-slate-700/30">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">Recursos y Enlaces</h2>
                                    <p class="text-[11px] text-gray-400 dark:text-slate-500 truncate">Material descargable, HTML embeds y enlaces de interés para la lección</p>
                                </div>
                                <button @click="showConfirmDeleteResources = true"
                                        class="text-[11px] text-red-400/60 hover:text-red-300 bg-red-500/5 hover:bg-red-500/10 px-2 py-1 rounded-lg transition-all inline-flex items-center gap-1.5 shrink-0 {{ count($wizardResources) === 0 && count($wizardLinks) === 0 && count($wizardHtmlEmbeds) === 0 ? 'hidden' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar todos
                                </button>
                            </div>

                            {{-- Tabs de navegación (tab-fill: ancho completo) --}}
                            <div class="flex items-stretch gap-0.5 border-b border-gray-200 dark:border-slate-700/50 bg-gray-50 dark:bg-slate-900/30 px-5">
                                <button @click="activeTab = 'resources'"
                                        :class="activeTab === 'resources' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/40' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 border-transparent hover:border-gray-300 dark:hover:border-slate-600/40'"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wider border-b-2 transition-all duration-200">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Archivos</span> descargables
                                    <span class="text-[10px] font-mono ml-1 px-1.5 py-0.5 rounded-full"
                                          :class="activeTab === 'resources' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700/50 text-slate-500'"
                                          x-text="{{ count($wizardResources) }}"></span>
                                </button>
                                <button @click="activeTab = 'embeds'"
                                        :class="activeTab === 'embeds' ? 'text-fuchsia-300 bg-fuchsia-500/10 border-fuchsia-500/40' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 border-transparent hover:border-gray-300 dark:hover:border-slate-600/40'"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wider border-b-2 transition-all duration-200">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                    HTML Embeds
                                    <span class="text-[10px] font-mono ml-1 px-1.5 py-0.5 rounded-full"
                                          :class="activeTab === 'embeds' ? 'bg-fuchsia-500/20 text-fuchsia-400' : 'bg-gray-200 dark:bg-slate-700/50 text-gray-500 dark:text-slate-500'"
                                          x-text="{{ count($wizardHtmlEmbeds) }}"></span>
                                </button>
                                <button @click="activeTab = 'links'"
                                        :class="activeTab === 'links' ? 'text-sky-300 bg-sky-500/10 border-sky-500/40' : 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 border-transparent hover:border-gray-300 dark:hover:border-slate-600/40'"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wider border-b-2 transition-all duration-200">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    <span class="hidden sm:inline">Enlaces</span> externos
                                    <span class="text-[10px] font-mono ml-1 px-1.5 py-0.5 rounded-full"
                                          :class="activeTab === 'links' ? 'bg-sky-500/20 text-sky-400' : 'bg-gray-200 dark:bg-slate-700/50 text-gray-500 dark:text-slate-500'"
                                          x-text="{{ count($wizardLinks) }}"></span>
                                </button>
                            </div>

                            {{-- Body: Tab panels --}}
                            <div class="p-5">

                                {{-- ═══ Tab: Archivos descargables ═══ --}}
                                <div x-show="activeTab === 'resources'" x-cloak x-transition:enter.duration.200ms>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Archivos descargables</h3>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-gray-400 dark:text-slate-500 bg-gray-200 dark:bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardResources) }} archivos</span>
                                        </div>
                                    </div>

                                    @if(count($wizardResources) > 0)
                                        <div class="space-y-1.5 mb-2">
                                            @foreach($wizardResources as $rIdx => $res)
                                                @php
                                                    $ext = strtolower(pathinfo($res['media']['original_name'] ?? '', PATHINFO_EXTENSION));
                                                    $icon = match($ext) {
                                                        'pdf' => 'pdf',
                                                        'jpg','jpeg','png','gif','webp' => 'image',
                                                        'mp4','webm','mov' => 'video',
                                                        'mp3','wav','ogg' => 'audio',
                                                        'doc','docx' => 'word',
                                                        'xls','xlsx' => 'excel',
                                                        'ppt','pptx' => 'powerpoint',
                                                        default => 'file'
                                                    };

                                                    $iconStyles = [
                                                        'pdf' => ['bg' => 'bg-red-500/15', 'text' => 'text-red-400'],
                                                        'image' => ['bg' => 'bg-blue-500/15', 'text' => 'text-blue-400'],
                                                        'video' => ['bg' => 'bg-purple-500/15', 'text' => 'text-purple-400'],
                                                        'audio' => ['bg' => 'bg-amber-500/15', 'text' => 'text-amber-400'],
                                                        'word' => ['bg' => 'bg-blue-600/15', 'text' => 'text-blue-400'],
                                                        'excel' => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-400'],
                                                        'powerpoint' => ['bg' => 'bg-orange-500/15', 'text' => 'text-orange-400'],
                                                        'file' => ['bg' => 'bg-slate-600/30', 'text' => 'text-slate-400'],
                                                    ];
                                                    $is = $iconStyles[$icon] ?? $iconStyles['file'];
                                                @endphp
                                                <div wire:key="resource-{{ $res['id'] }}" class="flex items-center gap-3 px-3 py-2.5 bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/40 rounded-lg hover:border-gray-300 dark:hover:border-slate-600/60 hover:bg-gray-50 dark:hover:bg-slate-800/60 transition-all group">
                                                    @if($icon === 'image')
                                                        <button wire:click="previewResourceImage({{ $rIdx }})"
                                                                class="w-9 h-9 rounded-lg overflow-hidden shrink-0 border border-slate-600/30 hover:ring-2 hover:ring-emerald-500/50 transition-all cursor-pointer"
                                                                title="Ver previsualización">
                                                            <img src="{{ $res['media']['public_url'] }}" alt=""
                                                                 class="w-full h-full object-cover">
                                                        </button>
                                                    @else
                                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $is['bg'] }}">
                                                            <svg class="w-4 h-4 {{ $is['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                @switch($icon)
                                                                    @case('pdf')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11v4m0 0l-2-2m2 2l2-2" opacity="0.5"/>
                                                                        @break
                                                                    @case('image')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                        @break
                                                                    @case('video')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                        @break
                                                                    @case('audio')
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                                                        @break
                                                                    @default
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                                @endswitch
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-medium text-gray-700 dark:text-slate-200 truncate">{{ $res['display_name'] }}</p>
                                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 truncate">{{ $res['media']['original_name'] ?? '' }} <span class="text-gray-300 dark:text-slate-600">·</span> {{ $res['media']['size_for_humans'] ?? '' }}
                                                            @if($res['section_id'])
                                                                <span class="text-gray-300 dark:text-slate-600">·</span>
                                                                <span class="text-emerald-400/70">{{ collect($wizardSections)->firstWhere('id', $res['section_id'])['title'] ?? 'Sección' }}</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button wire:click="editWizardResource({{ $rIdx }})"
                                                                class="text-gray-400 dark:text-slate-400/60 hover:text-sky-300 transition-all text-xs p-1 rounded hover:bg-sky-500/10"
                                                                title="Editar recurso" @disabled($isPublished)>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="removeWizardResource({{ $rIdx }})"
                                                                class="text-red-400/60 hover:text-red-300 transition-all text-xs p-1 rounded hover:bg-red-500/10"
                                                                title="Eliminar recurso" @disabled($isPublished)>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-gray-50 dark:bg-slate-800/20 border border-dashed border-gray-200 dark:border-slate-700/30 rounded-lg mb-2">
                                            <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-xs text-gray-400 dark:text-slate-500">Sin archivos aún. Agrega recursos descargables para la lección.</p>
                                        </div>
                                    @endif

                                    {{-- Add resource form --}}
                                    <div class="space-y-2">
                                        {{-- Edit mode indicator --}}
                                        @if($editingResourceIndex !== null && isset($wizardResources[$editingResourceIndex]))
                                            <div class="flex items-center gap-2 px-3 py-1.5 bg-sky-500/10 border border-sky-500/20 rounded-lg">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400 shrink-0"></span>
                                                <span class="text-[11px] font-medium text-sky-300">
                                                    Editando: <span class="text-sky-200">{{ $wizardResources[$editingResourceIndex]['display_name'] }}</span>
                                                </span>
                                                <button wire:click="cancelEditResource"
                                                        class="ml-auto text-[10px] text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700/50 px-2 py-0.5 rounded transition-colors" @disabled($isPublished)>
                                                    Cancelar
                                                </button>
                                            </div>
                                        @endif

                                        {{-- Row 1: Name + Section --}}
                                        <div class="flex gap-2">
                                            <div class="flex-1">
                                                <input wire:model="resourceName" placeholder="Nombre del recurso"
                                                       class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors @error('resourceName') border-red-500/50 @enderror" @disabled($isPublished)/>
                                                @error('resourceName')
                                                    <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            @if(count($wizardSections) > 0)
                                                <div class="flex-none">
                                                    <select wire:model="resourceSectionId"
                                                            class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors min-w-[130px]" @disabled($isPublished)>
                                                        <option value="">Sin sección</option>
                                                        @foreach($wizardSections as $sec)
                                                            <option value="{{ $sec['id'] }}">{{ $sec['title'] }} {{ !$sec['is_visible'] ? '(oculta)' : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Row 2: File + Upload + Preview --}}
                                        <div x-data="{ previewUrl: window._filePreviewUrl || null, previewType: window._filePreviewType || null }"
                                             x-on:file-preview-reset.window="previewUrl = null; previewType = null; window._filePreviewUrl = null; window._filePreviewType = null">
                                            <div class="flex gap-2 items-start">
                                                <div class="relative flex-none">
                                                    <input wire:model="resourceFile" type="file" id="resourceFile"
                                                           class="absolute inset-0 opacity-0 cursor-pointer @error('resourceFile') border-2 border-red-500/50 @enderror"
                                                           @change="const f = $event.target.files[0]; if (f && f.type.startsWith('image/')) { const r = new FileReader(); r.onload = e => { window._filePreviewUrl = e.target.result; window._filePreviewType = f.type; previewUrl = e.target.result; previewType = f.type }; r.readAsDataURL(f) } else { window._filePreviewUrl = null; window._filePreviewType = null; previewUrl = null; previewType = null }" @disabled($isPublished)/>
                                                    <label for="resourceFile"
                                                           class="flex items-center gap-1.5 px-3 py-2 @error('resourceFile') bg-red-800/40 border-red-500/50 @else bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 border-gray-300 dark:border-slate-600/50 hover:border-gray-400 dark:hover:border-slate-500/50 @enderror text-gray-600 dark:text-slate-300 text-xs rounded-lg cursor-pointer transition-colors whitespace-nowrap border">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                        </svg>
                                                        {{ $editingResourceIndex !== null ? 'Cambiar archivo' : 'Adjuntar' }}
                                                    </label>
                                                    @error('resourceFile')
                                                        <p class="text-[10px] text-red-400 mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <p class="flex-1 text-[10px] text-gray-400 dark:text-slate-500 leading-[36px]">Máx. 2 MB por archivo · 10 MB total por lección</p>
                                                <button wire:click="addWizardResource"
                                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5 shrink-0" @disabled($isPublished)>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    {{ $editingResourceIndex !== null ? 'Actualizar' : 'Subir' }}
                                                </button>
                                            </div>
                                            <template x-if="previewUrl">
                                                <div class="mt-2 rounded-xl overflow-hidden border border-emerald-500/30 bg-white dark:bg-slate-800/50"
                                                     title="Vista previa del archivo seleccionado">
                                                    <div class="relative w-full max-w-[200px] mx-auto">
                                                        <img :src="previewUrl" alt="Preview"
                                                             class="w-full h-auto object-contain max-h-48">
                                                    </div>
                                                    <div class="px-3 py-1.5 border-t border-emerald-500/20 flex items-center gap-2">
                                                        <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="text-[10px] text-gray-500 dark:text-slate-400">Archivo seleccionado — listo para subir</span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- ═══ IMAGE PROMPT (como en paso 2, con selector de sección) ═══ --}}
                                    @php
                                        $step3ImageSection = $step3ImageSectionId
                                            ? collect($wizardSections)->firstWhere('id', (int) $step3ImageSectionId)
                                            : null;

                                        $step3SectionContentForPrompt = '';
                                        if ($step3ImageSection) {
                                            $step3Text = collect($step3ImageSection['contents'] ?? [])
                                                ->pluck('body')
                                                ->map(fn($b) => strip_tags($b))
                                                ->implode("\n");
                                            $step3SectionContentForPrompt = \Illuminate\Support\Str::limit($step3Text, 500) ?: 'Describe un recurso visual genérico que complemente esta sección.';
                                        } else {
                                            $step3SectionContentForPrompt = 'No hay sección específica seleccionada. Crea un recurso visual genérico para la lección.';
                                        }

                                        $step3ImagePrompt = "## Rol\n"
                                            ."Eres un ilustrador educativo senior y diseñador instruccional con 15 años de experiencia creando recursos visuales pedagógicamente efectivos para entornos de aprendizaje presencial y digital. Dominas principios de comunicación visual, psicología cognitiva del aprendizaje y diseño universal para el aprendizaje (DUA).\n\n"
                                            ."## Contexto pedagógico\n"
                                            ."- **Grado/Nivel:** {$gradoName}\n"
                                            ."- **Asignatura:** {$asignaturaName}\n"
                                            ."- **Sección escolar:** {$seccionName}\n"
                                            ."- **Título de la lección:** {$lessonTitle}\n"
                                            ."- **Sección destino:** ".($step3ImageSection ? $step3ImageSection['title'] : 'Sin sección específica')."\n"
                                            ."- **Contenido de la sección:** {$step3SectionContentForPrompt}\n"
                                            ."- **Tipo de recurso:** Imagen descargable / recurso visual complementario\n\n"
                                            ."## Especificaciones técnicas del recurso visual\n"
                                            ."- **Estilo gráfico:** Ilustración educativa profesional en estilo «flat design» con paleta de color armónica, saturada pero no fluorescente. Trazos vectoriales definidos sin sombras complejas ni degradados extensos. Composición ordenada con jerarquía visual clara (tamaño, color, posición).\n"
                                            ."- **Proporción:** 16:9 horizontal. La imagen debe funcionar tanto en pantalla proyectada como en impresión tamaño carta (margen de 1\").\n"
                                            ."- **Resolución:** Mínimo 1920×1080px, 300 DPI si es vectorial.\n"
                                            ."- **Tipografía:** NO incluir texto ni etiquetas en la imagen. Todo el texto debe poder añadirse por separado como capa independiente.\n"
                                            ."- **Paleta de color:** Accesible para daltonismo (evitar rojo/verde como único contraste). Usar azul, naranja, amarillo, verde azulado como colores principales de distinción.\n"
                                            ."- **Público objetivo:** Estudiantes de {$gradoName}. El nivel de abstracción, las metáforas visuales y el vocabulario gráfico deben ser apropiados para esta etapa educativa.\n\n"
                                            ."## Instrucciones de contenido didáctico\n"
                                            ."1. **Concepto central:** Representa visualmente la idea o proceso fundamental de la sección de manera concreta, evitando abstracciones que requieran texto explicativo.\n"
                                            ."2. **Metáfora visual:** Usa una analogía visual que conecte el nuevo conocimiento con experiencias cotidianas del estudiante (si aplica).\n"
                                            ."3. **Secuencia didáctica:** Si el contenido describe un proceso (causa-efecto, línea de tiempo, ciclo), represéntalo en 3-4 viñetas o pasos dentro de una misma composición.\n"
                                            ."4. **Punto focal:** La composición debe tener un único elemento visual dominante que capture la atención primero, con elementos secundarios que amplíen o contextualicen.\n"
                                            ."5. **Inclusión y diversidad:** Cualquier figura humana debe representar diversidad étnica, de género y funcional de manera natural y no estereotipada.\n"
                                            ."6. **Fondo:** Neutro o contextual mínimo (sin texturas distractoras). El fondo no debe competir con el contenido pedagógico.\n\n"
                                            ."## Restricciones\n"
                                            ."- ❌ Sin texto renderizado en la imagen (ni títulos, ni etiquetas, ni pies de foto).\n"
                                            ."- ❌ Sin elementos decorativos que no tengan función pedagógica directa.\n"
                                            ."- ❌ Sin violencia, estereotipos de género/raza, representaciones inexactas científicamente.\n"
                                            ."- ❌ Sin marcas de agua, logos o referencias a la herramienta generadora.\n"
                                            ."- ✅ La imagen debe mantener legibilidad y contraste si se imprime en escala de grises.\n"
                                            ."- ✅ El estilo debe ser coherente con otras imágenes didácticas de la misma lección (mantener misma paleta y nivel de detalle).\n\n"
                                            ."## Formato de salida\n"
                                            ."Genera ÚNICAMENTE la imagen solicitada. Sin descripciones adicionales, sin explicaciones, sin variantes. Entrega la imagen en el formato y proporción especificados.";
                                    @endphp

                                    <div class="mt-3 border-t border-gray-200 dark:border-slate-700/30 pt-3"
                                         x-data="{ showPrompt: false }">
                                        <button @click="showPrompt = !showPrompt"
                                                class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-[11px] font-medium transition-colors
                                                       text-gray-500 dark:text-slate-400 hover:text-amber-300 hover:bg-amber-500/5">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                            </svg>
                                            Imagen IA — Prompt para recurso visual
                                            <svg class="w-3.5 h-3.5 ml-auto transition-transform" :class="showPrompt ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="showPrompt" x-cloak x-transition:enter.duration.200ms
                                             class="mt-3 p-4 bg-gradient-to-br from-amber-500/5 via-slate-900/80 to-slate-900 border border-amber-500/20 rounded-lg space-y-3">
                                            {{-- Selector de sección --}}
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-slate-400 shrink-0">
                                                    <svg class="w-3.5 h-3.5 text-amber-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                    Sección:
                                                </div>
                                                <select wire:model.live="step3ImageSectionId"
                                                        class="flex-1 bg-white dark:bg-slate-800/80 border border-gray-300 dark:border-slate-600/50 rounded-lg px-3 py-1.5 text-xs text-gray-900 dark:text-slate-200 focus:border-amber-500/50 focus:outline-none">
                                                    <option value="">— Seleccionar sección —</option>
                                                    @foreach($wizardSections as $sec)
                                                        <option value="{{ $sec['id'] }}">{{ $sec['title'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Header --}}
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500/20 to-orange-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-bold text-amber-300">Prompt — Imagen didáctica</h4>
                                                        <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-relaxed">
                                                            Copia este prompt y pégalo en un generador de imágenes con IA
                                                            (<span class="text-gray-600 dark:text-slate-300">DALL·E, Midjourney, Stable Diffusion, Copilot</span>)
                                                            para crear un recurso visual descargable para la lección.
                                                        </p>
                                                    </div>
                                                </div>
                                                <button @click="showPrompt = false"
                                                        class="p-1 hover:bg-gray-200 dark:hover:bg-slate-700/50 rounded-lg transition-colors shrink-0">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>

                                            {{-- Prompt text --}}
                                            <div class="relative" x-data="{}">
                                                <pre class="bg-white dark:bg-slate-950/80 border border-gray-200 dark:border-slate-700/50 rounded-lg p-4 text-[11px] text-gray-600 dark:text-slate-300 leading-relaxed font-mono whitespace-pre-wrap overflow-x-auto max-h-96 overflow-y-auto">{{ $step3ImagePrompt }}</pre>
                                                <button @click="
                                                    const btn = $event.currentTarget;
                                                    navigator.clipboard.writeText(btn.parentElement.querySelector('pre')?.textContent || '');
                                                    btn.textContent = '✓ Copiado';
                                                    setTimeout(() => btn.textContent = 'Copiar prompt', 2000);
                                                "
                                                        class="absolute top-3 right-3 px-2.5 py-1 text-[10px] font-medium text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 rounded-lg transition-all"
                                                        type="button">
                                                    Copiar prompt
                                                </button>
                                            </div>

                                            {{-- Footer --}}
                                            <div class="flex items-center justify-between text-[10px] text-gray-400 dark:text-slate-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    La imagen generada podrás asociarla como recurso descargable a la lección.
                                                </span>
                                                <span>{{ strlen($step3ImagePrompt) }} caracteres</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ═══ Tab: HTML Embeds ═══ --}}
                                <div x-show="activeTab === 'embeds'" x-cloak x-transition:enter.duration.200ms>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">HTML Embeds</h3>
                                            @if($editingEmbedIndex !== null)
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                    Editando #{{ $editingEmbedIndex + 1 }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500 bg-gray-200 dark:bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardHtmlEmbeds) }} embeds</span>
                                    </div>

                                    @if(count($wizardHtmlEmbeds) > 0)
                                        <div class="space-y-1.5 mb-2">
                                            @foreach($wizardHtmlEmbeds as $eIdx => $embed)
                                                <div class="flex items-start gap-3 px-3 py-2.5 bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/40 rounded-lg hover:border-gray-300 dark:hover:border-slate-600/60 hover:bg-gray-50 dark:hover:bg-slate-800/60 transition-all group">
                                                    <div class="w-9 h-9 rounded-lg bg-fuchsia-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                                        <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-xs font-medium text-gray-700 dark:text-slate-200 truncate">{{ $embed['title'] ?? 'Embed HTML' }}</p>
                                                            @if(!empty($embed['section_id']))
                                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded border text-amber-300 bg-amber-500/10 border-amber-500/20 shrink-0">
                                                                    Sección {{ collect($wizardSections)->firstWhere('id', $embed['section_id'])['title'] ?? '' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-[10px] text-gray-400 dark:text-slate-500 font-mono mt-1 line-clamp-2">{{ Str::limit(strip_tags($embed['html_content'] ?? ''), 120) }}</div>
                                                    </div>
                                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all">
                                                        <button wire:click="previewExistingEmbed({{ $eIdx }})"
                                                                class="text-gray-400 dark:text-slate-400 hover:text-fuchsia-300 transition-all text-xs p-1 rounded hover:bg-fuchsia-500/10"
                                                                title="Vista previa">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="editWizardHtmlEmbed({{ $eIdx }})"
                                                                class="text-gray-400 dark:text-slate-400 hover:text-amber-300 transition-all text-xs p-1 rounded hover:bg-amber-500/10"
                                                                title="Editar embed" @disabled($isPublished)>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="removeWizardHtmlEmbed({{ $eIdx }})"
                                                                class="text-red-400/60 hover:text-red-300 transition-all text-xs p-1 rounded hover:bg-red-500/10"
                                                                title="Eliminar embed" @disabled($isPublished)>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-gray-50 dark:bg-slate-800/20 border border-dashed border-gray-200 dark:border-slate-700/30 rounded-lg mb-2">
                                            <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            <p class="text-xs text-gray-400 dark:text-slate-500">Sin código HTML embebido aún. Agrega contenido HTML para la lección.</p>
                                        </div>
                                    @endif

                                    {{-- Add HTML embed form --}}
                                    <div class="space-y-2">
                                        <div class="flex gap-2">
                                            <div class="flex-1">
                                                <input wire:model="embedTitle" placeholder="Título del embed (opcional)"
                                                       class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors" @disabled($isPublished)/>
                                            </div>
                                            @if(count($wizardSections) > 0)
                                                <div class="flex-none">
                                                    <select wire:model.live="embedSectionId"
                                                            class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors min-w-[130px]" @disabled($isPublished)>
                                                        <option value="">Sin sección</option>
                                                        @foreach($wizardSections as $sec)
                                                            <option value="{{ $sec['id'] }}">{{ $sec['title'] }} {{ !$sec['is_visible'] ? '(oculta)' : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>

                                        <div x-data="{ showHelpModal: false }">
                                            <div class="flex justify-end">
                                                <button @click="showHelpModal = true"
                                                        class="text-[11px] text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 px-2 py-1 -mb-1 rounded-lg transition-colors flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    ¿Cómo se usa?
                                                </button>
                                            </div>

                                            {{-- Modal de ayuda --}}
                                            <div x-show="showHelpModal" x-cloak
                                                 @keydown.escape.window="showHelpModal = false"
                                                 class="fixed inset-0 z-[9999] overflow-y-auto">
                                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="showHelpModal = false"></div>
                                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                                    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                                        {{-- Header --}}
                                                        <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200 dark:border-slate-700">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Guía: HTML Embeds</h3>
                                                            </div>
                                                            <button @click="showHelpModal = false"
                                                                    class="p-1.5 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-all">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        {{-- Body del modal --}}
                                                        <div class="p-6 space-y-4">
                                                            <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                                                                Pega código HTML (<em>iframe</em>, <em>script</em>, tablas, etc.) para enriquecer la lección con contenido interactivo externo. Los iframes se renderizan en vivo dentro de la lección.
                                                            </p>

                                                            {{-- YouTube --}}
                                                            <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg overflow-hidden">
                                                                <div class="p-3 border-b border-gray-200 dark:border-slate-700/50">
                                                                    <p class="text-xs font-semibold text-emerald-400/80 mb-1">📺 YouTube — Video</p>
                                                                    <code class="text-[11px] text-gray-500 dark:text-slate-400 font-mono break-all select-all">&lt;iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video explicativo" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</code>
                                                                </div>
                                                            </div>

                                                            {{-- Google Maps + Google Drive --}}
                                                            <div class="grid grid-cols-2 gap-3">
                                                                <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-3">
                                                                    <p class="text-[11px] font-semibold text-blue-400/80 mb-1">🗺️ Google Maps — Ubicación</p>
                                                                    <code class="text-[10px] text-gray-500 dark:text-slate-400 font-mono break-all select-all">&lt;iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" allowfullscreen&gt;&lt;/iframe&gt;</code>
                                                                </div>
                                                                <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700/50 rounded-lg p-3">
                                                                    <p class="text-[11px] font-semibold text-fuchsia-400/80 mb-1">📁 Google Drive — Archivo</p>
                                                                    <code class="text-[10px] text-gray-500 dark:text-slate-400 font-mono break-all select-all">&lt;iframe src="https://drive.google.com/file/d/FILE_ID/preview" width="640" height="480" allowfullscreen&gt;&lt;/iframe&gt;</code>
                                                                </div>
                                                            </div>

                                                            {{-- CORS warning --}}
                                                            <div class="flex items-start gap-2 bg-amber-500/10 border border-amber-500/20 rounded-lg p-3">
                                                                <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                                </svg>
                                                                <p class="text-[11px] text-amber-400/80 leading-relaxed">
                                                                    Algunos servicios bloquean iframes por políticas CORS. Siempre verifica que el embed funcione antes de publicar la lección.
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {{-- Footer --}}
                                                        <div class="flex items-center justify-end px-6 py-3 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-200 dark:border-slate-700">
                                                            <button @click="showHelpModal = false"
                                                                    class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition-all">
                                                                Cerrar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <textarea wire:model="embedHtml" rows="4"
                                                      placeholder="Pega aquí el código HTML (iframe, script, etc.)"
                                                      class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors font-mono resize-y" @disabled($isPublished)></textarea>
                                        </div>
                                        <div x-data="{ showEmbedPreview: false }">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    @if(trim($embedHtml))
                                                    <button @click="showEmbedPreview = true"
                                                            class="px-3 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white text-xs font-medium rounded-lg transition-all whitespace-nowrap flex items-center gap-1.5 border border-gray-300 dark:border-slate-600/50">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Vista previa
                                                    </button>
                                                    @endif
                                                    <button wire:click="addWizardHtmlEmbed"
                                                            class="px-4 py-2 {{ $editingEmbedIndex !== null ? 'bg-amber-600 hover:bg-amber-500' : 'bg-fuchsia-600 hover:bg-fuchsia-500' }} text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5" @disabled($isPublished)>
                                                        @if($editingEmbedIndex !== null)
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                            Actualizar cambios
                                                        @else
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            Agregar Embed
                                                        @endif
                                                    </button>
                                                </div>
                                                @if($editingEmbedIndex !== null)
                                                    <button wire:click="cancelEditEmbed"
                                                            class="px-3 py-2 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white text-xs font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors" @disabled($isPublished)>
                                                        Cancelar
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Modal vista previa del embed --}}
                                            <div x-show="showEmbedPreview" x-cloak
                                                 @keydown.escape.window="showEmbedPreview = false"
                                                 class="fixed inset-0 z-[9999] overflow-y-auto">
                                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="showEmbedPreview = false"></div>
                                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                                    <div class="relative w-full max-w-4xl bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                                        <div class="flex items-center justify-between px-6 py-3 border-b border-slate-700">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Vista previa del Embed</h3>
                                                                @if($embedTitle)
                                                                    <span class="text-xs text-slate-400 font-normal">— {{ $embedTitle }}</span>
                                                                @endif
                                                            </div>
                                                            <button @click="showEmbedPreview = false"
                                                                    class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="p-6">
                                                            <div class="prose prose-sm max-w-none prose-invert">{!! $embedHtml !!}</div>
                                                        </div>
                                                        <div class="flex items-center justify-end px-6 py-3 bg-slate-800/50 border-t border-slate-700">
                                                            <button @click="showEmbedPreview = false"
                                                                    class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                                                Cerrar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 leading-relaxed">
                                            El código HTML se renderizará en la vista del estudiante. Usa con precaución: iframes, tablas, formularios, etc.
                                        </p>
                                    </div>
                                </div>

                                {{-- ═══ Tab: Enlaces externos ═══ --}}
                                <div x-show="activeTab === 'links'" x-cloak x-transition:enter.duration.200ms>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <h3 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Enlaces externos</h3>
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500 bg-gray-200 dark:bg-slate-700/40 px-2 py-0.5 rounded-full">{{ count($wizardLinks) }} enlaces</span>
                                    </div>

                                    @if(count($wizardLinks) > 0)
                                        <div class="space-y-1.5 mb-2">
                                            @foreach($wizardLinks as $lIdx => $link)
                                                @php
                                                    $badge = match($link['link_type']) {
                                                        'REFERENCE' => ['label' => 'Ref', 'color' => 'text-amber-300 bg-amber-500/10 border-amber-500/20'],
                                                        'VIDEO'     => ['label' => 'Video', 'color' => 'text-purple-300 bg-purple-500/10 border-purple-500/20'],
                                                        'TOOL'      => ['label' => 'Tool', 'color' => 'text-sky-300 bg-sky-500/10 border-sky-500/20'],
                                                        'DOCUMENT'  => ['label' => 'Doc', 'color' => 'text-blue-300 bg-blue-500/10 border-blue-500/20'],
                                                        default     => ['label' => 'Otro', 'color' => 'text-slate-300 bg-slate-500/10 border-slate-500/20'],
                                                    };
                                                    $displayUrl = parse_url($link['url'], PHP_URL_HOST) ?: $link['url'];
                                                @endphp
                                                <div class="flex items-center gap-3 px-3 py-2.5 bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/40 rounded-lg hover:border-gray-300 dark:hover:border-slate-600/60 hover:bg-gray-50 dark:hover:bg-slate-800/60 transition-all group">
                                                    <div class="w-9 h-9 rounded-lg bg-sky-500/10 flex items-center justify-center shrink-0">
                                                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-xs font-medium text-gray-700 dark:text-slate-200 truncate">{{ $link['title'] }}</p>
                                                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded border {{ $badge['color'] }} shrink-0">{{ $badge['label'] }}</span>
                                                        </div>
                                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 truncate">{{ $displayUrl }}</p>
                                                    </div>
                                                    <button wire:click="removeWizardLink({{ $lIdx }})"
                                                            class="opacity-0 group-hover:opacity-100 text-red-400/60 hover:text-red-300 transition-all text-xs p-1 rounded hover:bg-red-500/10"
                                                            title="Eliminar enlace" @disabled($isPublished)>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-3 px-4 py-5 bg-gray-50 dark:bg-slate-800/20 border border-dashed border-gray-200 dark:border-slate-700/30 rounded-lg mb-2">
                                            <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <p class="text-xs text-gray-400 dark:text-slate-500">Sin enlaces aún. Agrega enlaces de referencia, videos o herramientas.</p>
                                        </div>
                                    @endif

                                    {{-- Add link form --}}
                                    <div class="flex flex-wrap gap-2 items-end">
                                        <div class="flex-1 min-w-[140px]">
                                            <input wire:model="linkTitle" placeholder="Título del enlace"
                                                   class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors" @disabled($isPublished)/>
                                        </div>
                                        <div class="flex-1 min-w-[140px]">
                                            <input wire:model="linkUrl" placeholder="https://…"
                                                   class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none transition-colors" @disabled($isPublished)/>
                                        </div>
                                        <select wire:model="linkType"
                                                class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors" @disabled($isPublished)>
                                            <option value="REFERENCE">Referencia</option>
                                            <option value="VIDEO">Video</option>
                                            <option value="TOOL">Herramienta</option>
                                            <option value="DOCUMENT">Documento</option>
                                            <option value="OTHER">Otro</option>
                                        </select>
                                        @if(count($wizardSections) > 0)
                                            <select wire:model="linkSectionId"
                                                    class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-2.5 py-2 text-xs text-gray-900 dark:text-slate-200 focus:border-emerald-500 focus:outline-none transition-colors min-w-[120px]" @disabled($isPublished)>
                                                <option value="">Sin sección</option>
                                                @foreach($wizardSections as $sec)
                                                    <option value="{{ $sec['id'] }}">{{ $sec['title'] }} {{ !$sec['is_visible'] ? '(oculta)' : '' }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <button wire:click="addWizardLink"
                                                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap flex items-center gap-1.5" @disabled($isPublished)>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Agregar
                                        </button>
                                    </div>
                                </div>

                            </div>{{-- /body tabs --}}

                            {{-- ═══════ CONFIRM DELETE ALL RESOURCES MODAL ═══════ --}}
                            <div x-show="showConfirmDeleteResources" x-cloak
                                 class="fixed inset-0 z-[9999] overflow-y-auto"
                                 wire:key="confirm-delete-all-resources">
                                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"
                                     @click="showConfirmDeleteResources = false"></div>
                                <div class="relative min-h-screen flex items-center justify-center p-4">
                                    <div class="relative w-full max-w-md bg-slate-800 border border-slate-600/50 rounded-xl shadow-2xl overflow-hidden"
                                         @click.outside="showConfirmDeleteResources = false">
                                        {{-- Header --}}
                                        <div class="flex items-center gap-3 px-6 pt-6 pb-2">
                                            <div class="w-10 h-10 rounded-xl bg-red-500/15 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-white">Eliminar todos los recursos</h3>
                                                <p class="text-xs text-slate-400 leading-relaxed mt-0.5">
                                                    Se eliminarán <strong class="text-slate-300">todos</strong> los archivos descargables,
                                                    HTML embeds y enlaces externos de esta lección.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Body: summary counts --}}
                                        <div class="px-6 py-3 space-y-1.5">
                                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-emerald-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span><strong class="text-slate-300" x-text="{{ count($wizardResources) }}"></strong> archivos descargables</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-fuchsia-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                                </svg>
                                                <span><strong class="text-slate-300" x-text="{{ count($wizardHtmlEmbeds) }}"></strong> HTML embeds</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-sky-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                </svg>
                                                <span><strong class="text-slate-300" x-text="{{ count($wizardLinks) }}"></strong> enlaces externos</span>
                                            </div>
                                        </div>

                                        {{-- Warning --}}
                                        <div class="mx-6 mb-2 p-3 bg-amber-500/10 border border-amber-500/20 rounded-lg">
                                            <p class="text-[11px] text-amber-300/80 leading-relaxed flex items-start gap-2">
                                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                </svg>
                                                <span>Esta acción no se puede deshacer. Los recursos eliminados se perderán permanentemente al guardar la lección.</span>
                                            </p>
                                        </div>

                                        {{-- Footer actions --}}
                                        <div class="flex items-center justify-end gap-2 px-6 py-4 bg-slate-800/80 border-t border-slate-700/30">
                                            <button @click="showConfirmDeleteResources = false"
                                                    class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                                Cancelar
                                            </button>
                                            <button wire:click="removeAllWizardResources"
                                                    @click="showConfirmDeleteResources = false"
                                                    class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-500 rounded-lg transition-all flex items-center gap-1.5" @disabled($isPublished)>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar todo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- ═══════ MODAL PREVIEW EMBED EXISTENTE (global a los tabs) ═══════ --}}
                        @php $previewEmbed = $previewEmbedIndex !== null ? ($wizardHtmlEmbeds[$previewEmbedIndex] ?? null) : null; @endphp
                        @if($previewEmbed)
                        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="existing-embed-preview-modal">
                            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeExistingEmbedPreview"></div>
                            <div class="relative min-h-screen flex items-center justify-center p-4">
                                <div class="relative w-full max-w-4xl bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-2 border-b border-slate-700">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                            </svg>
                                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Vista previa del embed</h3>
                                            @if(!empty($previewEmbed['title']))
                                                <span class="text-xs text-slate-400 font-normal">— {{ $previewEmbed['title'] }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="closeExistingEmbedPreview"
                                                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        @php
                                            $previewContent = $previewEmbed['html_content'] ?? '';
                                            $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', trim($previewContent)) === 1;
                                        @endphp
                                        @if($isMermaid)
                                            <div wire:ignore x-data="mermaidEmbed()"
                                                 data-mermaid-code="{{ $previewContent }}"
                                                 class="w-full bg-white rounded-lg p-4 overflow-x-auto">
                                                <div x-ref="target" class="w-full"></div>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-3 text-center">Diagrama Mermaid renderizado en vivo</p>
                                        @else
                                            <div class="prose prose-sm max-w-none prose-invert">{!! $previewContent !!}</div>
                                            <p class="text-xs text-amber-400 mt-3 text-center">ℹ️ Contenido HTML embebido</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-end gap-2 px-6 py-2 bg-slate-800/50 border-t border-slate-700">
                                        <button wire:click="closeExistingEmbedPreview"
                                                class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ═══════ MODAL PREVIEW DIAGRAMA (global a los tabs) ═══════ --}}
                        @if($showEmbedPreview)
                        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="embed-preview-modal">
                            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeEmbedPreview"></div>
                            <div class="relative min-h-screen flex items-center justify-center p-4">
                                <div class="relative w-full max-w-4xl bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-2 border-b border-slate-700">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                            </svg>
                                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Vista previa del diagrama</h3>
                                            @if($embedTitle)
                                                <span class="text-xs text-slate-400 font-normal">— {{ $embedTitle }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="closeEmbedPreview"
                                                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-6">
                                        @php
                                            $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/', trim($embedHtml)) === 1;
                                        @endphp
                                        @if($isMermaid)
                                            <div wire:ignore x-data="mermaidEmbed()"
                                                 data-mermaid-code="{{ $embedHtml }}"
                                                 class="w-full bg-white rounded-lg p-4 overflow-x-auto">
                                                <div x-ref="target" class="w-full"></div>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-3 text-center">Diagrama Mermaid renderizado en vivo</p>
                                        @else
                                            <div class="prose prose-sm max-w-none prose-invert">{!! $embedHtml !!}</div>
                                            <p class="text-xs text-amber-400 mt-3 text-center">ℹ️ Este contenido no se reconoce como diagrama Mermaid. Se muestra como HTML.</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-end gap-2 px-6 py-2 bg-slate-800/50 border-t border-slate-700">
                                        <button wire:click="closeEmbedPreview"
                                                class="px-4 py-2 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                            Cerrar
                                        </button>
                                        <button wire:click="addWizardHtmlEmbed"
                                                class="px-4 py-2 text-xs font-medium text-white bg-fuchsia-600 hover:bg-fuchsia-500 rounded-lg transition-all" @disabled($isPublished)>
                                            Agregar Embed
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ═══════ MODAL PREVIEW IMAGEN ═══════ --}}
                        @php $previewResource = $previewResourceIndex !== null ? ($wizardResources[$previewResourceIndex] ?? null) : null; @endphp
                        @if($previewResource)
                        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="resource-image-preview-modal">
                            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeResourcePreview"></div>
                            <div class="relative min-h-screen flex items-center justify-center p-4">
                                <div class="relative bg-gray-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden"
                                     x-data="{ imgWidth: 0, imgHeight: 0 }">
                                    {{-- Header --}}
                                    <div class="flex items-center justify-between gap-3 px-6 py-2 border-b border-slate-700">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <h3 class="text-sm font-bold text-white uppercase tracking-wider truncate">{{ $previewResource['display_name'] }}</h3>
                                            <span class="text-xs text-slate-500 shrink-0">({{ $previewResource['media']['size_for_humans'] ?? '' }})</span>
                                        </div>
                                        <button wire:click="closeResourcePreview"
                                                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-all shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    {{-- Body: imagen con dimensiones naturales --}}
                                    <div class="p-4 flex items-center justify-center bg-black/40">
                                        <img src="{{ $previewResource['media']['public_url'] }}"
                                             alt="{{ $previewResource['display_name'] }}"
                                             @load="imgWidth = $el.naturalWidth; imgHeight = $el.naturalHeight"
                                             class="max-w-full h-auto rounded-lg shadow-lg object-contain"
                                             :style="`max-height: min(80vh, ${imgHeight}px); max-width: min(90vw, ${imgWidth}px)`"
                                             style="max-height: 80vh"/>
                                    </div>
                                    {{-- Footer: dimensiones --}}
                                    <div class="flex items-center justify-between px-6 py-2 bg-slate-800/50 border-t border-slate-700">
                                        <span class="text-[11px] text-slate-400">
                                            <span x-text="imgWidth ? imgWidth + ' × ' + imgHeight + ' px' : 'Cargando dimensiones…'"></span>
                                        </span>
                                        <button wire:click="closeResourcePreview"
                                                class="px-4 py-1.5 text-xs font-medium text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition-all">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
