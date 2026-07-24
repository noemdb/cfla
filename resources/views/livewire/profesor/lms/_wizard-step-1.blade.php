                        <div class="bg-white dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-5 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-gray-200 dark:border-slate-700">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">1</span>
                                <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Información de la Lección</h2>
                                <div class="ml-auto">
                                    <button wire:click="generateStep1Content"
                                            @disabled($isPublished)
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-medium
                                                   text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20
                                                   transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Generar con IA
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Título de la lección</label>
                                <input type="text" wire:model="lessonTitle"
                                       @disabled($isPublished)
                                       class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none disabled:bg-gray-100 dark:disabled:bg-slate-700/50 disabled:cursor-not-allowed"
                                       placeholder="Título de la lección"/>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Descripción</label>
                                <textarea wire:model="lessonDescription" rows="3"
                                          @disabled($isPublished)
                                          class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:outline-none disabled:bg-gray-100 dark:disabled:bg-slate-700/50 disabled:cursor-not-allowed"
                                          placeholder="Breve descripción de la lección…"></textarea>
                            </div>

                            @if($selectedActivity?->learning)
                                <div class="bg-white dark:bg-slate-900/30 rounded-lg p-3 border border-gray-200 dark:border-slate-700/50">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Aprendizaje esperado</span>
                                    <p class="text-sm text-gray-600 dark:text-slate-300 mt-1">{{ $selectedActivity->learning }}</p>
                                </div>
                            @endif

                            {{-- Referentes normativos con competencias e indicadores --}}
                            @if($wizardReferents && count($wizardReferents) > 0)
                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mt-2"
                                     x-data="{ expandedReferent: null }">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Referentes Normativos</span>
                                        <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono">({{ count($wizardReferents) }})</span>
                                    </div>

                                    <div class="space-y-2">
                                        @foreach($wizardReferents as $rIdx => $referent)
                                            <div class="bg-gray-50 dark:bg-slate-900/40 border border-gray-200 dark:border-slate-700/60 rounded-lg overflow-hidden">
                                                {{-- Cabecera del referente (click para expandir) --}}
                                                <button @click="expandedReferent = expandedReferent === {{ $rIdx }} ? null : {{ $rIdx }}"
                                                        class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-slate-800/40 transition-colors group">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <svg class="w-3.5 h-3.5 text-amber-400/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-sm font-medium text-gray-700 dark:text-slate-200 truncate">{{ $referent['name'] }}</span>
                                                        @if($referent['code'])
                                                            <span class="text-[10px] text-gray-400 dark:text-slate-500 font-mono shrink-0">({{ $referent['code'] }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <span class="text-[10px] text-gray-400 dark:text-slate-600">
                                                            {{ count($referent['competencies'] ?? []) }} comp.
                                                        </span>
                                                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-slate-500 transition-transform duration-200"
                                                             :class="expandedReferent === {{ $rIdx }} ? 'rotate-180' : ''"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </div>
                                                </button>

                                                {{-- Competencias e indicadores (accordion body) --}}
                                                <div x-show="expandedReferent === {{ $rIdx }}"
                                                     x-cloak
                                                     x-transition:enter.duration.150ms>
                                                    <div class="px-3 pb-3 space-y-2 border-t border-gray-200 dark:border-slate-700/50 pt-2">
                                                        @forelse($referent['competencies'] ?? [] as $competency)
                                                            <div class="pl-3 border-l-2 border-emerald-500/30">
                                                                <div class="flex items-center gap-1.5 mb-1">
                                                                    <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                                    </svg>
                                                                    <span class="text-xs font-medium text-emerald-300">{{ $competency['name'] }}</span>
                                                                </div>
                                                                @if(count($competency['indicators'] ?? []) > 0)
                                                                    <ul class="ml-5 space-y-0.5">
                                                                        @foreach($competency['indicators'] as $indicator)
                                                                            <li class="text-[11px] text-gray-500 dark:text-slate-400 flex items-start gap-1.5">
                                                                                <span class="text-gray-400 dark:text-slate-600 mt-0.5 select-none">•</span>
                                                                                <span>{{ $indicator['description'] }}</span>
                                                                                @if($indicator['code'])
                                                                                    <span class="text-[10px] text-gray-400 dark:text-slate-600 font-mono shrink-0">[{{ $indicator['code'] }}]</span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="ml-5 text-[11px] text-gray-400 dark:text-slate-600 italic">Sin indicadores asociados</p>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <p class="text-xs text-gray-400 dark:text-slate-600 italic">Sin competencias asociadas</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mt-2">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-xs font-bold text-gray-400 dark:text-slate-600 uppercase tracking-wider">Referentes Normativos</span>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-slate-600 italic">No hay referentes normativos registrados para este plan de estudio.</p>
                                </div>
                            @endif
                        </div>
