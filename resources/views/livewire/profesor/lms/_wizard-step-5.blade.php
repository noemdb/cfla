                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-gray-200 dark:border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold">5</span>
                                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Publicar Lección</h2>
                            </div>

                            {{-- ═══ Estados de publicación ═══ --}}
                            <div class="bg-amber-500/5 border border-amber-500/10 rounded-lg p-3 space-y-2">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-400">Estados de publicación</span>
                                </div>

                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="px-2 py-0.5 rounded bg-slate-700/60 text-slate-400 border border-slate-600/50 font-medium whitespace-nowrap">📋 Borrador</span>
                                    <svg class="w-3 h-3 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>

                                    <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 font-medium ring-1 ring-emerald-500/30 whitespace-nowrap">🟢 Publicado</span>

                                    <span class="text-slate-600 mx-1">·</span>

                                    <span class="text-amber-400/70">o programa con fecha</span>
                                </div>

                                <div class="grid grid-cols-3 gap-1.5 pt-0.5">
                                    <div class="text-center px-1.5 py-1 rounded bg-slate-800/40 border border-slate-700/40">
                                        <p class="text-[10px] font-semibold text-slate-400">📋 Borrador</p>
                                        <p class="text-[9px] text-slate-500 leading-tight">No visible</p>
                                    </div>
                                    <div class="text-center px-1.5 py-1 rounded bg-slate-800/40 border border-slate-700/40">
                                        <p class="text-[10px] font-semibold text-slate-400">⏰ Programado</p>
                                        <p class="text-[9px] text-slate-500 leading-tight">Pub. automática</p>
                                    </div>
                                    <div class="text-center px-1.5 py-1 rounded bg-emerald-500/8 border border-emerald-500/15">
                                        <p class="text-[10px] font-semibold text-emerald-400">🟢 Publicado</p>
                                        <p class="text-[9px] text-emerald-400/60 leading-tight">Visible ahora</p>
                                    </div>
                                </div>

                                <p class="text-[11px] text-amber-400/70 leading-relaxed">
                                    Al <strong class="text-amber-300">guardar</strong>, se genera la notificación para la aprobación / publicación.
                                    Si aún no está lista, usa el botón flotante <strong class="text-blue-400">Guardar</strong> para mantenerla en borrador.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="text-sm text-gray-600 dark:text-slate-300">Programar publicación:</label>
                                <input wire:model.live="publishAt" type="datetime-local"
                                       class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-1.5 text-sm text-gray-900 dark:text-slate-200 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 focus:outline-none" @disabled($isPublished)/>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-slate-300 cursor-pointer">
                                <input wire:model="allowDownloads" type="checkbox"
                                       class="rounded border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-500" @disabled($isPublished)/>
                                Permitir descarga de recursos
                            </label>

                            <div class="bg-gray-50 dark:bg-slate-900/30 rounded-lg p-3 border border-gray-200 dark:border-slate-700/50">
                                <p class="text-xs text-gray-500 dark:text-slate-400">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($this->previewSections) }}</span> secciones visibles ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ collect($this->previewSections)->sum(fn($s) => count($s['contents'])) }}</span> bloques de contenido ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($wizardResources) }}</span> recursos ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($wizardHtmlEmbeds) }}</span> embeds ·
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ count($wizardLinks) }}</span> enlaces ·
                                    <span class="{{ empty($this->reviewQuestions) ? 'text-red-400 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }} font-medium">{{ empty($this->reviewQuestions) ? '0' : '✓' }}</span> preguntas de repaso
                                </p>
                            </div>


                            @php $isPlanner = auth()->user()->isPlanner; @endphp

                            @if($isPlanner)
                                {{-- Planner/Admin: botón contextual --}}
                                <button wire:click="confirmPublish"
                                        wire:loading.attr="disabled"
                                        class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 text-white text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2" @disabled($isPublished)>
                                    <svg wire:loading wire:target="confirmPublish" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmPublish">
                                        @if(blank($publishAt))
                                            Guardar lección
                                        @else
                                            Programar lección
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="confirmPublish">
                                        @if(blank($publishAt))
                                            Guardando…
                                        @else
                                            Programando…
                                        @endif
                                    </span>
                                </button>
                            @else
                                {{-- Profesor: botón "Programar lección" --}}
                                <button wire:click="confirmPublish"
                                        wire:loading.attr="disabled"
                                        @if(blank($publishAt)) disabled @endif
                                        class="w-full py-2 @if(blank($publishAt)) bg-gray-300 dark:bg-slate-700 text-gray-500 dark:text-slate-500 cursor-not-allowed @else bg-amber-600 hover:bg-amber-500 text-white @endif disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2" @disabled($isPublished)>
                                    <svg class="w-4 h-4 @if(!blank($publishAt)) text-amber-200 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmPublish">
                                        Programar lección
                                    </span>
                                    <span wire:loading wire:target="confirmPublish">Programando…</span>
                                </button>
                                @if(blank($publishAt))
                                    <p class="text-[10px] text-amber-400 text-center -mt-1">
                                        Establece una fecha de programación primero
                                    </p>
                                @endif
                            @endif
                        </div>
