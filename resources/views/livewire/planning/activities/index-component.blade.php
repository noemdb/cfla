<div class="fade-in" x-data="{
    modeObservation: @entangle('modeObservation'),
    modeComments: @entangle('modeComments'),
    modePreview: @entangle('modePreview'),
    commentStatus: @entangle('status')
}">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Plan de Actividades</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium">Revisión y control de calidad pedagógica de los planes de evaluación.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('app.planning.index') }}"
                class="inline-flex items-center gap-2 min-h-[44px] px-5 py-2.5 bg-cyan-100 dark:bg-cyan-500/10 hover:bg-cyan-200 dark:hover:bg-cyan-500/20 text-cyan-700 dark:text-cyan-400 rounded-lg border border-cyan-200 dark:border-cyan-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Planificación
            </a>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 min-h-[44px] px-5 py-2.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Actualizar
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 p-2 sm:p-5 rounded-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Plan Estudio</label>
                <select wire:model.live="pestudio_id"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_pestudio as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Profesor</label>
                <select wire:model.live="profesor_id"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_profesors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Grado/Año</label>
                <select wire:model.live="grado_id"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_grado as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Sección</label>
                <select wire:model.live="seccion_id"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    @foreach($list_seccion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Actividades</label>
                <select wire:model.live="status_activities"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    <option value="SI">Con actividades</option>
                    <option value="NO">Sin actividades</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Resultados</label>
                <select wire:model.live="paginate"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="9999">Todos</option>
                </select>
            </div>

            <div class="flex items-end gap-4">
                <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none">
                    <input type="checkbox" wire:model.live="filter_observations" class="sr-only peer">
                    <div class="relative w-10 h-6 rounded-full transition-all duration-300 peer-checked:bg-blue-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-sm peer-checked:shadow-blue-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-sm after:border after:border-gray-200 dark:after:border-white/10"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-colors duration-300">
                        <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Observaciones
                    </span>
                    <span wire:loading wire:target="filter_observations" class="w-3 h-3">
                        <svg class="w-3 h-3 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </label>

                <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none group">
                    <input type="checkbox" wire:model.live="filter_revision" class="sr-only peer">
                    <div class="relative w-10 h-6 rounded-full transition-all duration-500 peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-yellow-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-lg peer-checked:shadow-amber-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-md after:border after:border-gray-200 dark:after:border-white/10 peer-checked:after:shadow-amber-500/30 group-hover:after:scale-110 peer-checked:group-hover:after:scale-110"></div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all duration-300 peer-checked:drop-shadow-[0_1px_2px_rgba(217,119,6,0.15)]">
                        <svg class="w-3.5 h-3.5 transition-transform duration-300 peer-checked:scale-110 peer-checked:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                        </svg>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600 peer-checked:bg-amber-500 transition-all duration-300 peer-checked:shadow-[0_0_6px_rgba(217,119,6,0.5)]"></span>
                        En revisión
                    </span>
                    <span wire:loading wire:target="filter_revision" class="w-3 h-3">
                        <svg class="w-3 h-3 animate-spin text-amber-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </label>
            </div>

            </div>
    </div>

    <!-- Global Indicator (solo cuando hay profesor seleccionado) -->
    @if($profesor_id && $pevaluacions->total() > 0)
        @php
            $totalActivities = $pevaluacions->sum('activities_count');
            $aboveAvg = 0;
            foreach($pevaluacions as $peva) {
                foreach($peva->activities as $act) {
                    $avr = $act->activities_avr;
                    $count = $act->teachingWordsMayorCount();
                    if ($avr !== null && $count > $avr) $aboveAvg++;
                }
            }
            $pct = $totalActivities > 0 ? round(($aboveAvg / $totalActivities) * 100) : 0;
            $level = $pct >= 50 ? 'success' : ($pct >= 25 ? 'warning' : 'danger');
            $message = $pct >= 50 ? 'Buen desempeño: la mayoría de las actividades superan el promedio de palabras esperado.'
                : ($pct >= 25 ? 'Desempeño moderado: una parte de las actividades alcanza el promedio.'
                : 'Atención: pocas actividades superan el promedio de palabras esperado.');
            $colors = ['success' => 'emerald', 'warning' => 'amber', 'danger' => 'red'];
            $c = $colors[$level];
        @endphp

        <div class="bg-{{ $c }}-50 dark:bg-{{ $c }}-500/10 border border-{{ $c }}-200 dark:border-{{ $c }}-500/20 p-5 rounded-lg mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-{{ $c }}-100 dark:bg-{{ $c }}-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-{{ $c }}-600 dark:text-{{ $c }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-{{ $c }}-700 dark:text-{{ $c }}-300 text-sm font-bold uppercase tracking-wider">Indicador — Actividades sobre el Promedio</p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">{{ $totalActivities }} actividades, {{ $aboveAvg }} superan el promedio (&gt;3 palabras)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-{{ $c }}-100 dark:bg-{{ $c }}-500/10 border border-{{ $c }}-200 dark:border-{{ $c }}-500/20 rounded-lg">
                    <span class="text-{{ $c }}-700 dark:text-{{ $c }}-400 text-xs font-bold uppercase">{{ $level === 'success' ? 'Buen desempeño' : ($level === 'warning' ? 'Moderado' : 'Atención') }}</span>
                    <span class="text-gray-900 dark:text-white text-lg font-black">{{ $pct }}%</span>
                </div>
            </div>
            <p class="text-gray-500 dark:text-gray-500 text-xs mt-3">{{ $message }}</p>
        </div>
    @endif

    <!-- ===== VIEW MODE TOGGLE (Grid / Table) ===== -->
    <div class="flex items-center gap-2 mb-4"
         x-data="{ mode: localStorage.getItem('planning-activities-view-mode') || 'table' }"
         x-init="$watch('mode', val => {
             localStorage.setItem('planning-activities-view-mode', val);
             window.dispatchEvent(new CustomEvent('planning-activities-view-mode-changed', { detail: { mode: val } }))
         })">
        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mr-1">Vista</span>
        <button @click="mode = 'grid'"
            :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/5 hover:text-gray-700 dark:hover:text-gray-300'"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="hidden sm:inline">Grid</span>
        </button>
        <button @click="mode = 'table'"
            :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/5 hover:text-gray-700 dark:hover:text-gray-300'"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span class="hidden sm:inline">Tabla</span>
        </button>
    </div>

    <!-- ===== CONTENT: View container ===== -->
    <div x-cloak
         x-data="{ mode: localStorage.getItem('planning-activities-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('planning-activities-view-mode')) localStorage.setItem('planning-activities-view-mode', 'table') }"
         x-on:planning-activities-view-mode-changed.window="mode = $event.detail.mode">

        {{-- GRID MODE --}}
        <div :class="mode === 'grid' ? '' : '!hidden'">
            <style>
                .masonry-grid-pla { columns: 1; column-gap: 0.75rem; }
                .masonry-item-pla { break-inside: avoid; margin-bottom: 0.75rem; }
                @media (min-width: 640px)  { .masonry-grid-pla { columns: 2; } }
                @media (min-width: 1024px) { .masonry-grid-pla { columns: 3; } }
                @media (min-width: 1280px) { .masonry-grid-pla { columns: 4; } }
                @supports (grid-template-rows: masonry) {
                    .masonry-grid-pla { display: grid; gap: 0.75rem; columns: unset; grid-template-columns: repeat(var(--masonry-cols), 1fr); grid-template-rows: masonry; }
                    .masonry-item-pla { break-inside: unset; margin-bottom: unset; }
                }
            </style>
            <div wire:key="tab-content-grid-{{ $lapso_id }}-{{ $pestudio_id ?? 'all' }}-{{ $filter_revision ? 'rev' : 'all' }}-{{ $filter_observations ? 'obs' : 'all' }}-{{ $status_activities ?? 'all' }}" class="masonry-grid-pla">
                @forelse($pevaluacions as $item)
                    <div class="masonry-item-pla bg-white dark:bg-gray-900/60 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden transition-all duration-300 hover:border-emerald-300 dark:hover:border-emerald-500/10 @if($item->activities && $item->activities->where('status', 0)->isNotEmpty()) border-t-4 border-t-amber-500 @endif">
                        <div class="p-4">
                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-[10px] font-bold text-gray-500 dark:text-gray-400 rounded-md border border-gray-200 dark:border-white/5">{{ $item->pensum?->asignatura?->code ?? '' }}</span>
                            </div>

                            {{-- Asignatura --}}
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1 leading-tight">{{ $item->pensum?->asignatura?->name ?? 'Sin asignatura' }}</h3>

                            {{-- Metadata --}}
                            <div class="space-y-1 mb-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ $item->seccion?->grado?->name ?? '' }} · Sección {{ $item->seccion?->name ?? '' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $item->profesor?->lastname ?? '' }} {{ $item->profesor?->name ?? '' }}
                                </p>
                            </div>

                            {{-- Badge --}}
                            <div class="mb-3">
                                @if($item->activities_count > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    {{ $item->activities_count }} {{ $item->activities_count === 1 ? 'Actividad' : 'Actividades' }}
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-[10px] font-bold rounded-md border border-red-200 dark:border-red-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Sin actividades
                                    </span>
                                @endif
                            </div>

                            {{-- Observations (in card) --}}
                            @if($item->observations)
                                <div class="mb-3 p-2.5 bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/10 rounded-lg">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[9px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-0.5">Observaciones</p>
                                            <p class="text-xs text-gray-700 dark:text-gray-200 line-clamp-3">{{ $item->observations }}</p>
                                        </div>
                                        <button type="button"
                                            @click="$dispatch('confirm-delete-observation', { id: {{ $item->id }}, message: '¿Eliminar las observaciones de «{{ addslashes($item->pensum->asignatura->name ?? '') }}»?' })"
                                            class="shrink-0 inline-flex items-center justify-center p-1.5 text-[10px] font-bold text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-md border border-red-200 dark:border-red-500/20 transition-all"
                                            title="Eliminar observaciones">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 pt-3 border-t border-gray-200 dark:border-white/5">
                                <button type="button" wire:click="createObservation({{ $item->id }})" stop
                                    class="p-1.5 bg-gray-100 dark:bg-white/5 hover:bg-blue-100 dark:hover:bg-blue-500/10 rounded-md border border-gray-200 dark:border-white/5 hover:border-blue-300 dark:hover:border-blue-500/20 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200"
                                    title="Observaciones del coordinador">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <a href="{{ route('app.planning.activities.resume', $item->id) }}" target="_blank" title="Resumen PDF"
                                    class="p-1.5 bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 rounded-md border border-gray-200 dark:border-white/5 hover:border-sky-300 dark:hover:border-sky-500/20 text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('app.planning.activities.format', $item->id) }}" target="_blank" title="Plan Completo PDF"
                                    class="p-1.5 bg-gray-100 dark:bg-white/5 hover:bg-purple-100 dark:hover:bg-purple-500/10 rounded-md border border-gray-200 dark:border-white/5 hover:border-purple-300 dark:hover:border-purple-500/20 text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="masonry-item-pla bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-white/5 rounded-lg py-16 text-center col-span-full">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-500 font-medium mb-2">No se encontraron planes de evaluación</p>
                        <p class="text-gray-400 dark:text-gray-600 text-sm">Ajusta los filtros o verifica que existan planes de evaluación con el módulo de planificación activo.</p>
                    </div>
                @endforelse
            </div>
            @if($pevaluacions->hasPages())
                <div class="mt-6">
                    {{ $pevaluacions->links('vendor.pagination.custom-tailwind') }}
                </div>
            @endif
        </div>

        {{-- TABLE MODE (current view) --}}
        <div :class="mode === 'table' ? '' : '!hidden'">
            <!-- ===== TABBED CONTENT (Lapso tabs like profesor home) ===== -->
            <div wire:key="tab-content-{{ $lapso_id }}-{{ $pestudio_id ?? 'all' }}-{{ $filter_revision ? 'rev' : 'all' }}-{{ $filter_observations ? 'obs' : 'all' }}-{{ $status_activities ?? 'all' }}" class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden">

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-white/5">
            <nav class="flex overflow-x-auto [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
                @foreach($tabsLapsos as $index => $lapsoItem)
                    @php $isActive = $lapsoItem->id == $lapso_id; @endphp
                    <button wire:click="selectLapso({{ $lapsoItem->id }})"
                        title="{{ $lapsoItem->name }}"
                        class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap
                               {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5' : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    >
                        <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">{{ $lapsoItem->name }}</span>
                        <span class="hidden sm:block text-[9px] font-normal text-gray-400 dark:text-gray-500 normal-case">{{ $lapsoItem->code }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="space-y-6 p-2 sm:p-4 lg:p-6">
            @forelse($pevaluacions as $item)
                <div class="bg-white dark:bg-gray-900/60 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden transition-all duration-300 hover:border-emerald-300 dark:hover:border-emerald-500/10 @if($item->activities && $item->activities->where('status', 0)->isNotEmpty()) border-t-4 border-t-amber-500 @endif"
                    wire:key="peva-{{ $item->id }}"
                    x-data="{ open: false, activeTab: 0 }">

                    <!-- Header Row -->
                    <div @click="open = !open"
                        class="flex items-center justify-between p-5 cursor-pointer hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition-colors group">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->pensum?->asignatura?->name ?? 'Sin asignatura' }}</span>
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-[10px] font-bold text-gray-500 dark:text-gray-400 rounded-md border border-gray-200 dark:border-white/5">{{ $item->pensum?->asignatura?->code ?? '' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    <span>{{ $item->seccion?->grado?->name ?? '' }} - Sección {{ $item->seccion?->name ?? '' }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span>{{ $item->profesor?->lastname ?? '' }} {{ $item->profesor?->name ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <!-- Activity count -->
                            <div class="flex items-center gap-2">
                                @if($item->activities_count > 0)
                                    <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-lg border border-emerald-200 dark:border-emerald-500/20">
                                        {{ $item->activities_count }} Act.
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg border border-red-200 dark:border-red-500/20">
                                        Sin actividades
                                    </span>
                                @endif
                            </div>

                            <!-- Button Group: Observación + PDFs -->
                            <div class="inline-flex items-center rounded-lg overflow-hidden border border-gray-200 dark:border-white/5 divide-x divide-gray-200 dark:divide-white/5" role="group">
                                <!-- Observation -->
                                <button type="button" wire:click="createObservation({{ $item->id }})" stop
                                    class="p-2 min-w-[36px] min-h-[36px] bg-gray-100 dark:bg-white/5 hover:bg-blue-100 dark:hover:bg-blue-500/10 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200"
                                    title="Observaciones del coordinador">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <!-- PDF Resume -->
                                <a href="{{ route('app.planning.activities.resume', $item->id) }}" target="_blank" title="Resumen PDF"
                                    class="p-2 min-w-[36px] min-h-[36px] bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </a>
                                <!-- PDF Format -->
                                <a href="{{ route('app.planning.activities.format', $item->id) }}" target="_blank" title="Plan Completo PDF"
                                    class="p-2 min-w-[36px] min-h-[36px] bg-gray-100 dark:bg-white/5 hover:bg-purple-100 dark:hover:bg-purple-500/10 text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            </div>

                            <!-- Toggle -->
                            <div class="min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center border border-gray-200 dark:border-white/5 transition-transform duration-300"
                                :class="open ? 'rotate-180 bg-emerald-100 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-500'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Expanded Activities with Tabs -->
                    <div :class="open ? '' : '!hidden'">
                        <div class="px-5 pb-5 pt-0 border-t border-gray-200 dark:border-white/5">
                            @if($item->activities_count > 0)
                                {{-- Activity Tab Bar (border-b-2 style like profesor home) --}}
                                <div class="border-b border-gray-200 dark:border-white/5 mt-4">
                                    <nav class="flex overflow-x-auto [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
                                        @foreach($item->activities as $i => $act)
                                            @php
                                                $wordCount = $act->teachingWordsMayorCount();
                                                $avr = $act->activities_avr;
                                                $qualityIcon = null;
                                                $qualityColor = null;
                                                $qualityTitle = null;
                                                if ($avr !== null) {
                                                    if ($wordCount > $avr) {
                                                        $qualityIcon = '↑';
                                                        $qualityColor = 'text-emerald-400';
                                                        $qualityTitle = "Palabras ({$wordCount}) por encima del promedio ({$avr})";
                                                    } elseif ($wordCount == $avr) {
                                                        $qualityIcon = '−';
                                                        $qualityColor = 'text-blue-400';
                                                        $qualityTitle = "Palabras ({$wordCount}) igual al promedio ({$avr})";
                                                    } else {
                                                        $qualityIcon = '↓';
                                                        $qualityColor = 'text-amber-400';
                                                        $qualityTitle = "Palabras ({$wordCount}) por debajo del promedio ({$avr})";
                                                    }
                                                }
                                            @endphp
                                            <button type="button" @click="activeTab = {{ $i }}"
                                                :class="activeTab === {{ $i }}
                                                    ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5'
                                                    : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                                                class="flex-1 px-4 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap"
                                                title="{{ \Carbon\Carbon::parse($act->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($act->ffinal)->format('d/m/Y') }}{{ $qualityTitle ? ' · ' . $qualityTitle : '' }}">
                                                <span class="flex items-center justify-center gap-1.5">
                                                    <span>Act. {{ $i + 1 }}</span>
                                                    @if($qualityIcon)
                                                        <span class="text-[10px] font-bold leading-none {{ $qualityColor }}">{{ $qualityIcon }}</span>
                                                    @endif
                                                    @if($act->status !== null)
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $act->status ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                                    @endif
                                                </span>
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>

                                {{-- Activity Tab Content --}}
                                @foreach($item->activities as $i => $act)
                                    <div :class="activeTab === {{ $i }} ? 'mt-4' : '!hidden'" class="transition-all duration-200">
                                        <div class="bg-gray-50 dark:bg-white/[0.03] p-4 rounded-lg border border-gray-200 dark:border-white/5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <!-- Fechas -->
                                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 mb-2">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($act->finicial)->format('d/m/Y') }}</span>
                                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($act->ffinal)->format('d/m/Y') }}</span>
                                                    </div>

                                                    <!-- Topic -->
                                                    <p class="text-sm text-gray-800 dark:text-gray-100 font-medium mb-1">{{ $act->topic }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-300 line-clamp-2">{{ $act->teaching }}</p>

                                                    <!-- Word quality indicator -->
                                                    @php
                                                        $wordCount = $act->teachingWordsMayorCount();
                                                        $avr = $act->activities_avr;
                                                        $quality = null;
                                                        if ($avr !== null) {
                                                            $quality = $wordCount > $avr ? 'above' : ($wordCount === $avr ? 'at' : 'below');
                                                        }
                                                    @endphp
                                                    @if($quality)
                                                        <div class="flex items-center gap-1.5 mt-2">
                                                            <span class="text-[10px] text-gray-500 dark:text-gray-500 font-medium">Calidad:</span>
                                                            @if($quality === 'above')
                                                                <span class="flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                                    {{ $wordCount }} > {{ $avr }}
                                                                </span>
                                                            @elseif($quality === 'at')
                                                                <span class="flex items-center gap-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 text-[10px] font-bold rounded-md border border-blue-200 dark:border-blue-500/20">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                                    {{ $wordCount }} = {{ $avr }}
                                                                </span>
                                                            @else
                                                                <span class="flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                                    {{ $wordCount }} < {{ $avr }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- Comments section -->
                                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-white/5">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500">Comentario [Jefe Área]</span>
                                                            <div class="flex items-center gap-2">
                                                                <button type="button" wire:click="showPreview({{ $act->id }})"
                                                                    class="flex items-center gap-1.5 px-3 py-1.5 min-h-[44px] bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-sky-300 dark:hover:border-sky-500/20 text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 text-[10px] font-bold uppercase tracking-wider transition-all duration-300">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                    </svg>
                                                                    Vista Previa
                                                                </button>
                                                                <button type="button" wire:click="setModeComment({{ $act->id }})"
                                                                    class="flex items-center gap-1.5 px-3 py-1.5 min-h-[44px] bg-gray-100 dark:bg-white/5 hover:bg-emerald-100 dark:hover:bg-emerald-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-emerald-300 dark:hover:border-emerald-500/20 text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 text-[10px] font-bold uppercase tracking-wider transition-all duration-300">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                    </svg>
                                                                    {{ $act->comments ? 'Editar' : 'Agregar' }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @if($act->comments)
                                                            <p class="text-xs text-gray-700 dark:text-gray-200 mt-1 italic">"{{ $act->comments }}"</p>
                                                        @else
                                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">Sin comentarios</p>
                                                        @endif
                                                        @if($act->status !== null)
                                                            <div class="mt-1">
                                                                @if($act->status)
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                        Aprobado
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        En revisión
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center justify-center py-8 text-center">
                                    <div>
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-500 text-sm">No hay actividades registradas en este plan de evaluación.</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Observations -->
                            @if($item->observations)
                                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/10 rounded-lg">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-1">Observaciones del Coordinador</p>
                                            <p class="text-xs text-gray-700 dark:text-gray-200">{{ $item->observations }}</p>
                                        </div>
                                        <button type="button"
                                            @click="$dispatch('confirm-delete-observation', { id: {{ $item->id }}, message: '¿Eliminar las observaciones de «{{ addslashes($item->pensum->asignatura->name ?? '') }}»?' })"
                                            class="shrink-0 inline-flex items-center gap-1 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-md border border-red-200 dark:border-red-500/20 transition-all"
                                            title="Eliminar observaciones">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-white/5 rounded-lg py-16 text-center">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-500 font-medium mb-2">No se encontraron planes de evaluación</p>
                    <p class="text-gray-400 dark:text-gray-600 text-sm">Ajusta los filtros o verifica que existan planes de evaluación con el módulo de planificación activo.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($pevaluacions->hasPages())
                <div class="mt-6">
                    {{ $pevaluacions->links('vendor.pagination.custom-tailwind') }}
                </div>
            @endif
        </div>
    </div>
        </div>
    </div>

    <!-- ===== MODAL: Observaciones del Coordinador ===== -->
    <div x-show="modeObservation" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        @keydown.escape.window="modeObservation = false">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-950/70 backdrop-blur-sm" @click="modeObservation = false"></div>

        {{-- Panel --}}
        <div x-show="modeObservation" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-2xl overflow-hidden">

            {{-- Top accent bar --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-emerald-500"></div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Observaciones del Coordinador</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Supervisión pedagógica del plan de evaluación</p>
                    </div>
                </div>
                <button type="button" @click="modeObservation = false"
                    class="p-1.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                @if($pevaluacion)
                <div class="bg-gray-50 dark:bg-white/[0.03] p-4 rounded-lg border border-gray-200 dark:border-white/5 space-y-3">
                    {{-- Section label --}}
                    <div class="flex items-center gap-2 text-[10px] text-gray-500 dark:text-gray-500 font-bold uppercase tracking-widest">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Plan de Evaluación
                    </div>
                    {{-- Asignatura --}}
                    <p class="text-sm text-gray-900 dark:text-white font-semibold">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }}</p>
                    {{-- Metadata row --}}
                    <div class="flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $pevaluacion->seccion?->grado?->name ?? '—' }} · Sección {{ $pevaluacion->seccion?->name ?? '—' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $pevaluacion->profesor?->lastname ?? '—' }} {{ $pevaluacion->profesor?->name ?? '—' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $pevaluacion->lapso?->name ?? '—' }}
                        </span>
                    </div>
                </div>
                @endif

                {{-- Textarea con contador --}}
                <div x-data="{ obsCount: {{ strlen($observations ?? '') }} }">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Observaciones</label>
                        <span class="text-[10px] tabular-nums text-gray-400" x-text="`${obsCount}/65535 caracteres`"></span>
                    </div>
                    <textarea wire:model="observations" rows="5" maxlength="65535"
                        x-on:input="obsCount = $el.value.length"
                        class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none resize-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 @error('observations') border-red-300 dark:border-red-500/50 focus:ring-red-500/50 focus:border-red-500/50 @enderror"
                        placeholder="Escribe las observaciones del coordinador de evaluación..."></textarea>
                    @error('observations')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-white/5">
                <div class="flex items-center gap-2">
                    @if($pevaluacion && $pevaluacion->observations)
                        <span class="inline-flex items-center gap-1 text-emerald-500 not-italic font-medium text-[10px]">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tiene observaciones previas
                        </span>
                    @else
                        <span class="text-[10px] text-gray-400 italic">Sin observaciones previas</span>
                    @endif
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="modeObservation = false"
                        class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-lg border border-gray-200 dark:border-white/5 transition-all">
                        Cancelar
                    </button>
                    <button type="button" wire:click="saveObservation" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-white bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 rounded-lg shadow-lg shadow-emerald-500/10 disabled:opacity-50 transition-all">
                        <span wire:loading.remove wire:target="saveObservation">
                            <span class="hidden sm:inline">Guardar </span>Observaciones
                        </span>
                        <span wire:loading wire:target="saveObservation" class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL: Comentario del Jefe de Área ===== -->
    <x-modal-card title="Comentario del Jefe de Área" blur wire:model="modeComments" max-width="lg">
        <div class="space-y-4">
            @if($activity)
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}
                    </div>
                    <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $activity->topic }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-300 mt-1 line-clamp-2">{{ $activity->teaching }}</p>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Estado de Aprobación</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="status" value="1"
                            class="w-4 h-4 text-emerald-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-emerald-500/50 focus:ring-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Aprobado</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="status" value="0"
                            class="w-4 h-4 text-amber-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-amber-500/50 focus:ring-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">En revisión</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Comentario</label>
                <textarea wire:model="comments" rows="4"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none resize-none transition-all"
                    placeholder="Escribe tu comentario como jefe de área..."></textarea>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="modeComments = false" />
                <x-button primary label="Guardar Comentario" wire:click="saveComent" spinner="saveComent" />
            </div>
        </x-slot>
    </x-modal-card>

    <!-- ===== MODAL: Vista Previa de Actividad ===== -->
    <x-modal-card title="Vista Previa de la Actividad" blur="lg" wire:model="modePreview" width="max-w-[80vw]" class="border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-gray-900">
        @if($previewActivity)
            <div class="space-y-5" x-data="{ showTeaching: false }">

                {{-- Fechas --}}
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-white/5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">
                        {{ \Carbon\Carbon::parse($previewActivity->finicial)->format('d/m/Y') }}
                        —
                        {{ \Carbon\Carbon::parse($previewActivity->ffinal)->format('d/m/Y') }}
                    </span>
                    @if($previewActivity->status !== null)
                        <span class="ml-auto">
                            @if($previewActivity->status)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Aprobado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    En revisión
                                </span>
                            @endif
                        </span>
                    @endif
                </div>

                {{-- Topic --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Tema generador / Énfasis</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->topic ?? '—' }}</p>
                </div>

                {{-- Thematic --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Tejido temático / Tema Indispensable</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->thematic ?? '—' }}</p>
                </div>

                {{-- References --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Referentes teórico-prácticos y Éticos</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->references ?? '—' }}</p>
                </div>

                {{-- Teaching --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Enseñanza / Actividad Globalizada</span>
                        @if($previewActivity->hasTeachingStructure())
                            <button @click="showTeaching = !showTeaching"
                                class="text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
                                <span x-show="!showTeaching">Ver estructura</span>
                                <span x-show="showTeaching">Ver completo</span>
                            </button>
                        @endif
                    </div>

                    {{-- Teaching structured view --}}
                    @php $sections = $previewActivity->getTeachingSections(); @endphp
                    @if(!empty($sections))
                        <div x-show="showTeaching" x-cloak x-transition:enter.duration.200ms>
                            <div class="space-y-3">
                                <div class="bg-cyan-50 dark:bg-cyan-500/5 border border-cyan-200 dark:border-cyan-500/10 rounded-lg p-3">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1">INICIO</div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $sections['INICIO'] ?? '' }}</p>
                                </div>
                                <div class="bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/10 rounded-lg p-3">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1">DESARROLLO</div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $sections['DESARROLLO'] ?? '' }}</p>
                                </div>
                                <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 rounded-lg p-3">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400 mb-1">CIERRE</div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $sections['CIERRE'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Teaching raw view --}}
                    <div x-show="!showTeaching">
                        <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $previewActivity->teaching ?? '—' }}</p>
                    </div>
                </div>

                {{-- Learning --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Aprendizaje</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->learning ?? '—' }}</p>
                </div>

                {{-- Description --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Actividad Evaluativa</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->description ?? '—' }}</p>
                </div>

                {{-- Grid: Achievements + ODS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- Achievements --}}
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Indicadores de Logro</div>
                        @if($previewActivity->achievements->isNotEmpty())
                            <ul class="space-y-1.5">
                                @foreach($previewActivity->achievements as $ach)
                                    <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $ach->name }}</span>
                                        @if($ach->weighting)
                                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 px-1.5 py-0.5 rounded-md">[{{ $ach->weighting }}]</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Sin indicadores de logro</p>
                        @endif
                    </div>

                    {{-- Observations / ODS --}}
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">ODS / Sistematización</div>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ $previewActivity->observations ?? '—' }}</p>
                    </div>
                </div>

                {{-- Comments --}}
                @if($previewActivity->comments)
                    <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 p-4 rounded-lg">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">Comentario del Jefe de Área</span>
                        </div>
                        <p class="text-sm text-amber-800 dark:text-amber-200 italic">"{{ $previewActivity->comments }}"</p>
                    </div>
                @endif

                {{-- Teaching word count indicator --}}
                @php
                    $wordCountPrev = $previewActivity->teachingWordsMayorCount();
                    $avrPrev = $previewActivity->activities_avr;
                @endphp
                @if($avrPrev !== null)
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 bg-gray-50 dark:bg-white/5 px-4 py-2 rounded-lg border border-gray-200 dark:border-white/5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Indicador de calidad:</span>
                        @if($wordCountPrev > $avrPrev)
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $wordCountPrev }} &gt; {{ $avrPrev }}</span>
                            <span class="text-emerald-600/70 dark:text-emerald-400/70">(Supera el promedio)</span>
                        @elseif($wordCountPrev === $avrPrev)
                            <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $wordCountPrev }} = {{ $avrPrev }}</span>
                            <span class="text-blue-600/70 dark:text-blue-400/70">(En el promedio)</span>
                        @else
                            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $wordCountPrev }} &lt; {{ $avrPrev }}</span>
                            <span class="text-amber-600/70 dark:text-amber-400/70">(Debajo del promedio)</span>
                        @endif
                    </div>
                @endif
            </div>
        @endif
        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="Cerrar" x-on:click="modePreview = false" />
            </div>
        </x-slot>
    </x-modal-card>

    <x-confirm-modal
        name="delete-observation"
        title="Eliminar observaciones"
        message="Esta acción no se puede deshacer."
        confirm-text="Sí, eliminar"
        cancel-text="Cancelar"
        type="danger"
        action="deleteObservation"
    />

    @script
    <script>
        // Fix for WireUI modal not closing properly
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                // WireUI handles modal closing via wire:model
            });
        });
    </script>
    @endscript
</div>
