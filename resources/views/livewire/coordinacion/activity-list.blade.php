<div class="fade-in"
     x-data="{
         mode: localStorage.getItem('coord-activities-view-mode') || 'table',
         toggleObservation: @entangle('filterObservations'),
         toggleRevision: @entangle('filterRevision'),
     }"
     x-init="$watch('mode', val => {
         localStorage.setItem('coord-activities-view-mode', val);
         window.dispatchEvent(new CustomEvent('coord-activities-view-mode-changed', { detail: { mode: val } }))
     })">

    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Actividades de Planificación</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">Revisión y control de calidad pedagógica de los planes de evaluación</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-3 py-2 sm:px-5 sm:py-2.5 min-h-[44px] min-w-[44px] bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="hidden sm:inline">Actualizar</span>
            </button>
        </div>
    </div>

    {{-- Lapso NavTabs --}}
    <div class="mb-6 bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden">
        <nav class="flex overflow-x-auto [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
            @foreach($lapsos as $lapso)
                <button wire:click="selectLapso({{ $lapso->id }})"
                    title="{{ $lapso->name }}"
                    class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap
                        {{ $lapsoId == $lapso->id ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5' : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="hidden sm:inline">{{ $lapso->name }}</span>
                    <span class="hidden sm:block text-[9px] font-normal text-gray-400 dark:text-gray-500 normal-case">{{ Str::of($lapso->name)->limit(6, '') }}</span>
                </button>
            @endforeach
            <button wire:click="selectLapso('')"
                class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap ml-auto
                    {{ !$lapsoId ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5' : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span class="hidden sm:inline">Todos</span>
            </button>
        </nav>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 p-2 sm:p-5 rounded-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Plan Estudio</label>
                <select wire:model.live="pestudioId"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($listPestudio as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Profesor</label>
                <select wire:model.live="profesorId"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($listProfesores as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Grado/Año</label>
                <select wire:model.live="gradoId"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($listGrado as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Sección</label>
                <select wire:model.live="seccionId"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    @foreach($listSeccion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Actividades</label>
                <select wire:model.live="statusActivities"
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
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div class="flex items-end gap-4 col-span-1 sm:col-span-2 lg:col-span-4 xl:col-span-6">
                {{-- Toggle Observaciones --}}
                <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none">
                    <input type="checkbox" wire:model.live="filterObservations" class="sr-only peer">
                    <div class="relative w-10 h-6 rounded-full transition-all duration-300 peer-checked:bg-blue-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-sm peer-checked:shadow-blue-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-sm after:border after:border-gray-200 dark:after:border-white/10"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-colors duration-300">
                        <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Observaciones
                    </span>
                    <span wire:loading wire:target="filterObservations" class="w-3 h-3">
                        <svg class="w-3 h-3 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </label>

                {{-- Toggle En Revisión --}}
                <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none group">
                    <input type="checkbox" wire:model.live="filterRevision" class="sr-only peer">
                    <div class="relative w-10 h-6 rounded-full transition-all duration-500 peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-yellow-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-lg peer-checked:shadow-amber-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-md after:border after:border-gray-200 dark:after:border-white/10 peer-checked:after:shadow-amber-500/30 group-hover:after:scale-110 peer-checked:group-hover:after:scale-110"></div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all duration-300 peer-checked:drop-shadow-[0_1px_2px_rgba(217,119,6,0.15)]">
                        <svg class="w-3.5 h-3.5 transition-transform duration-300 peer-checked:scale-110 peer-checked:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                        </svg>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600 peer-checked:bg-amber-500 transition-all duration-300 peer-checked:shadow-[0_0_6px_rgba(217,119,6,0.5)]"></span>
                        En revisión
                    </span>
                    <span wire:loading wire:target="filterRevision" class="w-3 h-3">
                        <svg class="w-3 h-3 animate-spin text-amber-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </label>

                {{-- Filter: Estado (segmentado) --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Estado</label>
                    <div class="flex rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
                        <button type="button" wire:click="$set('filterStatus', '')"
                            class="flex-1 min-h-[44px] px-3 py-2 text-[11px] font-bold transition-all duration-200
                                {{ $filterStatus === '' ? 'bg-emerald-500/15 text-emerald-400 border-r border-r-emerald-500/30 shadow-inner' : 'bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 border-r border-gray-200 dark:border-white/10' }}">
                            Todos
                        </button>
                        <button type="button" wire:click="$set('filterStatus', 'pending')"
                            class="flex-1 min-h-[44px] px-3 py-2 text-[11px] font-bold transition-all duration-200
                                {{ $filterStatus === 'pending' ? 'bg-amber-500/15 text-amber-400 border-r border-r-amber-500/30 shadow-inner' : 'bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 border-r border-gray-200 dark:border-white/10' }}">
                            <span class="flex items-center justify-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $filterStatus === 'pending' ? 'bg-amber-400' : 'bg-gray-400' }}"></span>
                                Pendientes
                            </span>
                        </button>
                        <button type="button" wire:click="$set('filterStatus', 'approved')"
                            class="flex-1 min-h-[44px] px-3 py-2 text-[11px] font-bold transition-all duration-200
                                {{ $filterStatus === 'approved' ? 'bg-emerald-500/15 text-emerald-400 shadow-inner' : 'bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10' }}">
                            <span class="flex items-center justify-center gap-1.5">
                                <svg class="w-3 h-3 {{ $filterStatus === 'approved' ? 'text-emerald-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Aprobadas
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid/Table Toggle --}}
    <div class="flex items-center gap-2 mb-4">
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

    {{-- ═══ CONTENT: View container ═══ --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('coord-activities-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('coord-activities-view-mode')) localStorage.setItem('coord-activities-view-mode', 'table') }"
         x-on:coord-activities-view-mode-changed.window="mode = $event.detail.mode">

        {{-- ═══ TABLE VIEW ═══ --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">

            <div class="space-y-4">
                @forelse($activities as $activity)
                    @php
                        $pev = $activity->pevaluacion;
                        $hasPending = !$activity->status;
                        $hasObservations = $pev && $pev->observations;
                    @endphp
                    <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-xl p-4 sm:p-5 transition-all duration-200 hover:border-emerald-500/30
                        {{ $hasPending ? 'ring-1 ring-amber-500/20 border-amber-500/10 border-l-4 border-l-amber-400' : 'border-l-4 border-l-emerald-400' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $activity->topic }}</h3>
                                    @if($hasPending)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20 rounded-md shrink-0">
                                            <span class="w-1 h-1 rounded-full bg-amber-400"></span>
                                            Revisión
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20 rounded-md shrink-0">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Aprobada
                                        </span>
                                    @endif
                                    @if($hasObservations)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold bg-blue-500/12 text-blue-400 border border-blue-500/20 rounded-md shrink-0" title="Tiene observaciones">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Obs
                                        </span>
                                    @endif
                                </div>
                                @if($activity->thematic)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $activity->thematic }}</p>
                                @endif
                                <div class="flex flex-wrap gap-2 mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-full font-medium">
                                        {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $activity->pevaluacion?->seccion?->grado?->name ?? '' }} · Sección {{ $activity->pevaluacion?->seccion?->name ?? '—' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        {{ $activity->pevaluacion?->pensum?->pestudio?->name ?? '—' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $activity->pevaluacion?->profesor?->lastname ?? '' }}, {{ $activity->pevaluacion?->profesor?->name ?? '—' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $activity->pevaluacion?->lapso?->name ?? '' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('app.coordinacion.activities.format', $activity->pevaluacion_id) }}"
                                    target="_blank"
                                    class="px-3 py-1.5 text-xs bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-transparent transition-colors font-medium">
                                    Formato
                                </a>
                                <a href="{{ route('app.coordinacion.activities.resume', $activity->pevaluacion_id) }}"
                                    target="_blank"
                                    class="px-3 py-1.5 text-xs bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-transparent transition-colors font-medium">
                                    Resumen
                                </a>
                            </div>
                        </div>

                        {{-- Observations Section --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                            @if($editingPevId === $activity->pevaluacion_id)
                                <div class="space-y-2">
                                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Observaciones</label>
                                    <textarea wire:model="observations" rows="3"
                                        class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600"
                                        placeholder="Agregar observaciones..."></textarea>
                                    <div class="flex gap-2">
                                        <button wire:click="saveObservations" wire:loading.attr="disabled"
                                            class="px-4 py-1.5 text-xs bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors font-medium">
                                            Guardar
                                        </button>
                                        <button wire:click="cancelEdit"
                                            class="px-4 py-1.5 text-xs bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-transparent transition-colors font-medium">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        @if($activity->pevaluacion?->observations)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                                "{{ $activity->pevaluacion->observations }}"
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400 dark:text-gray-600">Sin observaciones registradas.</p>
                                        @endif
                                    </div>
                                    <button wire:click="editObservations({{ $activity->pevaluacion_id }})"
                                        class="px-3 py-1.5 text-xs bg-emerald-100 dark:bg-emerald-500/10 hover:bg-emerald-200 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-lg transition-colors shrink-0 ml-4 font-medium">
                                        {{ $activity->pevaluacion?->observations ? 'Editar' : 'Agregar' }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron actividades</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda.</p>
                    </div>
                @endforelse
            </div>

            @if($activities->hasPages())
                <div class="mt-6">
                    <x-pagination-wrapper :paginator="$activities" />
                </div>
            @endif
        </div>

        {{-- ═══ GRID VIEW ═══ --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">

            <style>
                .coord-acts-masonry { --masonry-cols: 1; columns: var(--masonry-cols); column-gap: 0.75rem; }
                .coord-acts-masonry-item { break-inside: avoid; margin-bottom: 0.75rem; }
                .coord-acts-masonry-empty { break-inside: avoid; text-align: center; }
                @media (min-width: 640px)  { .coord-acts-masonry { --masonry-cols: 2; } }
                @media (min-width: 1024px) { .coord-acts-masonry { --masonry-cols: 3; } }
                @media (min-width: 1280px) { .coord-acts-masonry { --masonry-cols: 4; } }
            </style>

            @forelse($activities as $activity)
                @php
                    $pev = $activity->pevaluacion;
                    $hasPending = !$activity->status;
                    $hasObservations = $pev && $pev->observations;
                @endphp
                @if($loop->first)
                <div class="coord-acts-masonry">
                @endif
                    <div class="coord-acts-masonry-item">
                        <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 hover:border-emerald-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/5 h-full flex flex-col
                            {{ $hasPending ? 'ring-1 ring-amber-500/20 border-amber-500/10' : '' }}
                            {{ $activity->status ? 'border-l-4 border-l-emerald-400' : 'border-l-4 border-l-amber-400' }}">
                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-3 gap-3">
                                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($hasPending)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20 rounded-md">
                                            <span class="w-1 h-1 rounded-full bg-amber-400"></span>
                                            Revisión
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20 rounded-md">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Aprobada
                                        </span>
                                    @endif
                                    @if($hasObservations)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold bg-blue-500/12 text-blue-400 border border-blue-500/20 rounded-md">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Obs
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Body --}}
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1 leading-snug line-clamp-2">
                                {{ $activity->topic }}
                            </h3>
                            @if($activity->thematic)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $activity->thematic }}</p>
                            @endif

                            {{-- Metadata --}}
                            <div class="mt-auto pt-3 space-y-1.5">
                                <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="truncate">{{ $pev?->profesor?->lastname ?? '' }}, {{ $pev?->profesor?->name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <span class="truncate">{{ $pev?->pensum?->asignatura?->name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span>{{ $pev?->seccion?->grado?->name ?? '' }} · Sección {{ $pev?->seccion?->name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span class="truncate">{{ $pev?->pensum?->pestudio?->name ?? '—' }}</span>
                                </div>

                                {{-- Observations excerpt --}}
                                @if($hasObservations)
                                    <div class="pt-2 border-t border-gray-100 dark:border-white/5">
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 italic line-clamp-1">
                                            "{{ $pev->observations }}"
                                        </p>
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="pt-2 flex items-center gap-2">
                                    <a href="{{ route('app.coordinacion.activities.format', $activity->pevaluacion_id) }}"
                                        target="_blank"
                                        class="flex-1 text-center px-2 py-1 text-[10px] bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-transparent transition-colors font-medium">
                                        Formato
                                    </a>
                                    <a href="{{ route('app.coordinacion.activities.resume', $activity->pevaluacion_id) }}"
                                        target="_blank"
                                        class="flex-1 text-center px-2 py-1 text-[10px] bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-transparent transition-colors font-medium">
                                        Resumen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @if($loop->last)
                </div>{{-- /coord-acts-masonry --}}
                @endif
            @empty
                <div class="text-center py-16 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron actividades</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda.</p>
                </div>
            @endforelse

            @if($activities->hasPages())
                <div class="mt-6">
                    <x-pagination-wrapper :paginator="$activities" />
                </div>
            @endif
        </div>
    </div>
</div>
