{{-- Paso 3 · Clases — parcial del wizard completo. --}}
            <div class="bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-extrabold text-gray-900 dark:text-white">3 · Clases</h2>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-widest">{{ $step3SelectedCount }} seleccionadas</span>
                    </div>
                    {{-- <div class="flex items-center gap-1">
                        <button wire:click="setStep3ViewMode('tabs')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 {{ $step3ViewMode === 'tabs' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white/5 text-gray-400 border-white/10 hover:text-gray-300' }}">Pestañas</button>
                        <button wire:click="setStep3ViewMode('flat')"
                            class="px-3 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-200 {{ $step3ViewMode === 'flat' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white/5 text-gray-400 border-white/10 hover:text-gray-300' }}">Lista</button>
                    </div> --}}
                </div>

                @php $orphanLessons = $this->timetableOrphanLessons(); @endphp
                @if ($orphanLessons !== [])
                    <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/5 px-4 py-3" role="alert">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-widest text-red-500">
                                    Lessons con referencia académica huérfana
                                </div>
                                <p class="mt-1 text-[11px] text-gray-600 dark:text-gray-300">
                                    Estas lessons ya están registradas, pero su Pevaluación no existe. No se pueden editar ni publicar hasta restaurar la Pevaluación o retirar la lesson.
                                </p>
                            </div>
                            <span class="rounded-md bg-red-500/10 px-2 py-1 text-[10px] font-bold text-red-500">{{ count($orphanLessons) }}</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($orphanLessons as $orphanLesson)
                                <span class="rounded border border-red-500/30 bg-red-500/10 px-2 py-1 text-[10px] font-bold text-red-600 dark:text-red-300">
                                    Lesson #{{ $orphanLesson['id'] }} · Pevaluacion #{{ $orphanLesson['pevaluacion_id'] }} · {{ $orphanLesson['blocks'] }} bloque(s)
                                </span>
                            @endforeach
                        </div>
                        <button type="button" wire:click="confirmRemoveOrphanLessons"
                            wire:loading.attr="disabled" wire:target="confirmRemoveOrphanLessons"
                            class="mt-3 inline-flex items-center rounded-md border border-red-500/40 bg-red-500/10 px-3 py-2 text-[11px] font-bold text-red-600 transition hover:bg-red-500/20 dark:text-red-300">
                            <span wire:loading.remove wire:target="confirmRemoveOrphanLessons">Retirar lessons huérfanas</span>
                            <span wire:loading wire:target="confirmRemoveOrphanLessons">Preparando…</span>
                        </button>
                    </div>
                @endif

                {{-- Pestañas · Pestudio --}}
                @if ($step3ViewMode === 'tabs' && $tabPestudioOptions)
                    <div class="mb-3 border-b border-gray-200 dark:border-white/10">
                        <nav class="flex w-full overflow-x-auto">
                            @foreach ($tabPestudioOptions as $opt)
                                <button type="button" wire:click="selectStep5Pestudio({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                    class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                    {{ (string) $activePestudioId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                    {{ $opt['name'] }}
                                    @if ($opt['id'] !== 'general' && $this->pestudioAllSectionsLocked($opt['id']))
                                        <svg class="inline-block h-3 w-3 ml-1 -mt-0.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" title="Todas las secciones activas de este pestudio están bloqueadas">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                {{-- Pestañas · Grado --}}
                @if ($step3ViewMode === 'tabs' && $tabGradoOptions)
                    <div class="mb-3 border-b border-gray-200 dark:border-white/10">
                        <nav class="flex w-full overflow-x-auto">
                            @foreach ($tabGradoOptions as $opt)
                                <button type="button" wire:click="selectStep5Grade({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                    class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                    {{ (string) $activeGradoId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                    {{ $opt['name'] }}
                                    @if ($opt['id'] !== 'general' && $this->gradeAllSectionsLocked($opt['id']))
                                        <svg class="inline-block h-3 w-3 ml-1 -mt-0.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" title="Todas las secciones activas de este grado están bloqueadas">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                {{-- Pestañas · Sección --}}
                @if ($step3ViewMode === 'tabs' && $tabSeccionOptions)
                    <div class="mb-3 border-b border-gray-200 dark:border-white/10">
                        <nav class="flex w-full overflow-x-auto">
                            @foreach ($tabSeccionOptions as $opt)
                                <button type="button" wire:click="selectStep5Section({{ $opt['id'] === 'general' ? "'general'" : $opt['id'] }})"
                                    class="flex-1 text-center px-3 py-2 text-[11px] font-bold uppercase tracking-widest whitespace-nowrap border-b-2 transition-all duration-200
                                    {{ (string) $activeSeccionId === (string) $opt['id'] ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'text-gray-400 dark:text-gray-500 border-transparent hover:text-gray-600 dark:hover:text-gray-300' }}">
                                    {{ $opt['label'] ?? 'Sección '.$opt['name'] }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                @include('livewire.coordinacion.timetable.partials.step3-lessons-summary')

                @include('livewire.coordinacion.timetable.partials.step3-lessons-table')

                {{-- <div class="mt-5 rounded-lg border border-dashed border-gray-300 dark:border-white/10 p-4">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Importación masiva (CSV / Excel)</div>
                    <div class="flex flex-wrap items-center gap-3">
                        <input type="file" wire:model="importFile"
                            accept=".csv,.xlsx,.xls,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            class="text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-white/5 file:text-gray-300 file:text-xs file:font-bold hover:file:bg-white/10">
                        <button wire:click="importLessons" wire:loading.attr="disabled" wire:target="importFile"
                            class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-200 text-sm font-bold border border-white/10">
                            Importar
                        </button>
                        <button wire:click="downloadTemplate"
                            class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-bold border border-white/10">
                            Descargar plantilla
                        </button>
                    </div>
                    <p class="mt-2 text-[11px] text-gray-400 dark:text-gray-500">Columnas: <code>pevaluacion_id</code> <em>o</em> (<code>seccion_id</code> + <code>asignatura</code>), <code>turno</code> (M/T), <code>bloques_t</code>, <code>bloques_p</code>, <code>aula</code>, <code>prioridad</code>.</p>
                    @if ($importMessage)
                        <div class="mt-2 text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ $importMessage }}</div>
                    @endif
                    @if ($importErrors)
                        <ul class="mt-2 space-y-1 max-h-32 overflow-y-auto">
                            @foreach ($importErrors as $err)
                                <li class="text-xs text-red-500 dark:text-red-400">• {{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div> --}}

                <div class="mt-4 flex items-center justify-end">
                    <button wire:click="saveLessons"
                        @disabled($step3SelectedCount === 0)
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        wire:target="saveLessons"
                        class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                        <span wire:loading.remove wire:target="saveLessons">Guardar clases y continuar</span>
                        <span wire:loading wire:target="saveLessons" class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Guardando…
                        </span>
                    </button>
                </div>
            </div>
