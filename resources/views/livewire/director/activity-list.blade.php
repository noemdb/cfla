{{-- resources/views/livewire/director/activity-list.blade.php --}}
<div class="fade-in" x-data="{ helpOpen: false }" x-cloak>

    <div class="mb-6">
        <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Actividades</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de actividades académicas · solo lectura</p>
    </div>

    {{-- Filter Bar: panel de filtros ampliado (patrón del módulo Planning) --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 p-2 sm:p-5 rounded-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            {{-- Búsqueda --}}
            <div class="lg:col-span-2 xl:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Buscar</label>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por tema o temática…"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
            </div>

            {{-- Plan Estudio --}}
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

            {{-- Profesor --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Profesor</label>
                <select wire:model.live="profesor_id"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_profesor as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Grado/Año --}}
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

            {{-- Sección --}}
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

            {{-- Fila secundaria: Lapso (ancho completo) --}}
            <div class="sm:col-span-2 lg:col-span-4 xl:col-span-6">
                <div class="w-40 sm:w-44">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Lapso</label>
                    <select wire:model.live="lapso_id"
                        class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        <option value="">Todos</option>
                        @foreach($lapsos as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Subtitle + View Toggle (persiste en localStorage, sincronizado por evento) --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
        <p class="text-[11px] text-gray-400 font-medium">
            <span class="text-emerald-400">Actividades</span> de la institución · solo lectura
        </p>
        <div x-data="{ mode: localStorage.getItem('director-activities-view-mode') || 'table' }"
             x-init="$watch('mode', val => {
                 localStorage.setItem('director-activities-view-mode', val);
                 window.dispatchEvent(new CustomEvent('director-activities-view-mode-changed', { detail: { mode: val } }))
             })">
            <button @click="mode = 'grid'"
                :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold"
                title="Vista Grid">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="hidden sm:inline">Grid</span>
            </button>
            <button @click="mode = 'table'"
                :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold"
                title="Vista Tabla">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="hidden sm:inline">Tabla</span>
            </button>
        </div>
    </div>

    {{-- View container: escucha el evento y sincroniza el modo con el toggle --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('director-activities-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('director-activities-view-mode')) localStorage.setItem('director-activities-view-mode', 'table') }"
         x-on:director-activities-view-mode-changed.window="mode = $event.detail.mode">

        {{-- Grid Mode: columnas masonry responsive --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-2.5">
                @forelse($activities as $activity)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 break-inside-avoid mb-2.5 dark:border-white/5 dark:bg-gray-900">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-cyan-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-cyan-500 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate dark:text-white">{{ $activity->topic }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                    @if($activity->pevaluacion?->pensum?->asignatura?->name){{ $activity->pevaluacion->pensum->asignatura->name }} · @endif
                                    @if($activity->pevaluacion?->seccion?->name){{ $activity->pevaluacion->seccion->name }}@if($activity->pevaluacion?->seccion?->grado?->name) · {{ $activity->pevaluacion->seccion->grado->name }}@endif @endif
                                </p>
                            </div>
                        </div>
                        @if($activity->thematic)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 truncate">{{ $activity->thematic }}</p>
                        @endif
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $activity->pevaluacion?->profesor?->lastname }}, {{ $activity->pevaluacion?->profesor?->name }}</span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $activity->pevaluacion?->lapso?->name }}
                            </span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            {{-- Aprobación del Jefe de Área (status boolean: 1=Aprobado, 0=En revisión) --}}
                            @if($activity->status !== null)
                                @if($activity->status)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Aprobada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        En revisión
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    Sin aprobar
                                </span>
                            @endif
                            {{-- Lección (lmsPublication.status: PUBLISHED/SCHEDULED) --}}
                            @if($activity->lmsPublication && $activity->lmsPublication->status === 'PUBLISHED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>
                                    Lección aprobada
                                </span>
                            @elseif($activity->lmsPublication && $activity->lmsPublication->status === 'SCHEDULED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-100 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-[10px] font-bold rounded-md border border-sky-200 dark:border-sky-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Lección programada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"></path></svg>
                                    Lección pendiente
                                </span>
                            @endif
                        </div>
                        <div class="mt-3 inline-flex items-center rounded-lg overflow-hidden border border-gray-200 dark:border-white/5 divide-x divide-gray-200 dark:divide-white/5" role="group">
                            {{-- Formato PDF --}}
                            <a href="{{ route('app.director.activities.format', $activity->pevaluacion_id) }}" target="_blank" rel="noopener" title="Formato PDF"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 min-h-[32px] bg-gray-100 dark:bg-white/5 hover:bg-purple-100 dark:hover:bg-purple-500/10 text-[10px] font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Formato</span>
                            </a>
                            {{-- Resumen PDF --}}
                            <a href="{{ route('app.director.activities.resume', $activity->pevaluacion_id) }}" target="_blank" rel="noopener" title="Resumen PDF"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 min-h-[32px] bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 text-[10px] font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition-all duration-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span>Resumen</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 break-inside-avoid dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                        Sin actividades para los filtros seleccionados.
                    </div>
                @endforelse
            </div>

            @if($activities->hasPages())
                <x-pagination-wrapper :paginator="$activities" />
            @endif
        </div>

        {{-- Table Mode --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-white/5 dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="px-5 py-3">Tema</th>
                                <th class="px-5 py-3">Asignatura</th>
                                <th class="px-5 py-3">Sección</th>
                                <th class="px-5 py-3">Profesor</th>
                                <th class="px-5 py-3">Lapso</th>
                                <th class="px-5 py-3">Estado</th>
                                <th class="px-5 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 dark:border-white/5 dark:hover:bg-white/5">
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $activity->topic }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $activity->pevaluacion?->pensum?->asignatura?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $activity->pevaluacion?->seccion?->name }}
                                        @if($activity->pevaluacion?->seccion?->grado?->name)
                                            <span class="text-gray-400 dark:text-gray-500">·</span> {{ $activity->pevaluacion->seccion->grado->name }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $activity->pevaluacion?->profesor?->lastname }}, {{ $activity->pevaluacion?->profesor?->name }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $activity->pevaluacion?->lapso?->name }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-col gap-1">
                                            {{-- Aprobación del Jefe de Área (status boolean: 1=Aprobado, 0=En revisión) --}}
                                            @if($activity->status !== null)
                                                @if($activity->status)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Aprobada
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        En revisión
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                    Sin aprobar
                                                </span>
                                            @endif
                                            {{-- Lección (lmsPublication.status: PUBLISHED/SCHEDULED) --}}
                                            @if($activity->lmsPublication && $activity->lmsPublication->status === 'PUBLISHED')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>
                                                    Lección aprobada
                                                </span>
                                            @elseif($activity->lmsPublication && $activity->lmsPublication->status === 'SCHEDULED')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-100 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-[10px] font-bold rounded-md border border-sky-200 dark:border-sky-500/20">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Lección programada
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"></path></svg>
                                                    Lección pendiente
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="inline-flex items-center rounded-lg overflow-hidden border border-gray-200 dark:border-white/5 divide-x divide-gray-200 dark:divide-white/5" role="group">
                                            {{-- Formato PDF --}}
                                            <a href="{{ route('app.director.activities.format', $activity->pevaluacion_id) }}" target="_blank" rel="noopener" title="Formato PDF"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 min-h-[32px] bg-gray-100 dark:bg-white/5 hover:bg-purple-100 dark:hover:bg-purple-500/10 text-[10px] font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span>Formato</span>
                                            </a>
                                            {{-- Resumen PDF --}}
                                            <a href="{{ route('app.director.activities.resume', $activity->pevaluacion_id) }}" target="_blank" rel="noopener" title="Resumen PDF"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 min-h-[32px] bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 text-[10px] font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition-all duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>Resumen</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin actividades para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($activities->hasPages())
                <x-pagination-wrapper :paginator="$activities" />
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- HELP BUTTON: Guía de Actividades (README) --}}
    {{-- ============================================================ --}}
    {{-- Botón flotante --}}
    <button @click="helpOpen = true"
            class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
            title="Guía de consulta de actividades"
            x-show="!helpOpen">
        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
        </svg>
    </button>

    {{-- Backdrop --}}
    <div x-show="helpOpen" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm" @click="helpOpen = false"></div>

    {{-- Slide-over panel --}}
    <div x-show="helpOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
         @keydown.escape.window="helpOpen = false"
         class="fixed top-0 right-0 z-50 h-full w-full max-w-2xl bg-white dark:bg-gray-950 shadow-2xl flex flex-col overflow-y-auto border-l border-gray-200 dark:border-white/10"
         role="dialog" aria-modal="true"
         :class="helpOpen ? 'pointer-events-auto' : 'pointer-events-none'">

        {{-- Header sticky --}}
        <div class="sticky top-0 z-[5] px-6 py-5 border-b border-gray-200 dark:border-white/10 bg-white/90 dark:bg-gray-950/90 backdrop-blur-md">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900 dark:text-white">Guía de Consulta de Actividades</h2>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Para el director · supervisión de toda la institución (solo lectura)</p>
                    </div>
                </div>
                <button @click="helpOpen = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" title="Cerrar">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                Cada registro es una <strong class="text-gray-900 dark:text-white">actividad académica</strong> (tema
                con su temática) que el <strong class="text-gray-900 dark:text-white">docente</strong> prepara sobre una
                asignatura del <strong class="text-gray-900 dark:text-white">pensum</strong> para una
                <strong class="text-gray-900 dark:text-white">sección</strong> en un <strong class="text-gray-900 dark:text-white">lapso</strong>.
                Esta guía te orienta a <strong class="text-gray-900 dark:text-white">leer y auditar</strong> el panel.
            </p>
        </div>

        <div class="px-6 py-5" x-data="{ tab: 'que-es' }">

        {{-- Tab buttons --}}
        <div class="flex flex-wrap gap-1.5 mb-5">
            <button @click="tab = 'que-es'"
                :class="tab === 'que-es' ? 'bg-sky-100 dark:bg-sky-500/15 text-sky-700 dark:text-sky-300 border-sky-300 dark:border-sky-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-sky-600 dark:hover:text-sky-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Qué es
            </button>
            <button @click="tab = 'estados'"
                :class="tab === 'estados' ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-emerald-600 dark:hover:text-emerald-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Estados
            </button>
            <button @click="tab = 'vistas'"
                :class="tab === 'vistas' ? 'bg-violet-100 dark:bg-violet-500/15 text-violet-700 dark:text-violet-300 border-violet-300 dark:border-violet-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-violet-600 dark:hover:text-violet-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Vistas y filtros
            </button>
        </div>
            {{-- ─── TAB: QUÉ ES ─────────────────────────────────── --}}
            <div x-show="tab === 'que-es'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué representa una actividad académica?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Tema + temática + su evaluación</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Es la <strong class="text-gray-900 dark:text-white">preparación de clase</strong> de un
                                    docente: un <strong class="text-gray-900 dark:text-white">tema</strong> (qué se enseña)
                                    y una <strong class="text-gray-900 dark:text-white">temática</strong> (cómo se aborda),
                                    registrados en una <strong class="text-gray-900 dark:text-white">Pevaluación</strong> que
                                    los vincula con la <strong class="text-gray-900 dark:text-white">asignatura del pensum</strong>,
                                    la <strong class="text-gray-900 dark:text-white">sección/grado</strong> y el
                                    <strong class="text-gray-900 dark:text-white">lapso</strong>.
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center rounded-md bg-sky-100 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 px-2 py-0.5 text-[10px] font-bold text-sky-700 dark:text-sky-400">Tema</span>
                                    <span class="inline-flex items-center rounded-md bg-sky-100 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/20 px-2 py-0.5 text-[10px] font-bold text-sky-700 dark:text-sky-400">Temática</span>
                                    <span class="inline-flex items-center rounded-md bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">Asignatura</span>
                                    <span class="inline-flex items-center rounded-md bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">Sección · Grado</span>
                                    <span class="inline-flex items-center rounded-md bg-violet-100 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20 px-2 py-0.5 text-[10px] font-bold text-violet-700 dark:text-violet-400">Profesor</span>
                                    <span class="inline-flex items-center rounded-md bg-violet-100 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20 px-2 py-0.5 text-[10px] font-bold text-violet-700 dark:text-violet-400">Lapso</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la dirección</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Qué observar</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Efectividad docente</strong>: qué temas están preparando y con qué temática.</span></p>
                                <p class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Cobertura</strong>: qué asignaturas/secciones concentran o carecen de actividades.</span></p>
                                <p class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Avance del lapso</strong>: qué actividades se consolidaron en lecciones.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2-3-6-1-6 3m12-3c2-3 6-1 6 3 0 5-5 8-9 10m-3-10c0 3 .5 5 3 7"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-3 space-y-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Radiación electromagnética</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">Investigación de ondas en el entorno cercano.</p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">Física · 3A · Prof. García · Lapso I</p>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Es un tema de Física preparado para el 3A
                                    en el Lapso I. La dirección puede verificar que la asignatura avanza y que el docente
                                    documentó su clase.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: ESTADOS ───────────────────────────────── --}}
            <div x-show="tab === 'estados'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Aprobación del Jefe de Área</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Aprobada · En revisión · Sin aprobar</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Cada actividad muestra <strong class="text-gray-900 dark:text-white">un
                                    badge de aprobación</strong> que refleja la revisión del jefe de área de la asignatura.
                                    Es la + primera capa de control.</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span><span><strong class="text-gray-800 dark:text-gray-200">Aprobada</strong> — validada por el jefe de área.</span></li>
                                    <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></span><span><strong class="text-gray-800 dark:text-gray-200">En revisión</strong> — el jefe aún no la aprueba (puede requerir ajustes).</span></li>
                                    <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-gray-400 flex-shrink-0"></span><span><strong class="text-gray-800 dark:text-gray-200">Sin aprobar</strong> — no registra revisión.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Estado de la lección LMS</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Lección aprobada · programada · pendiente</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">El segundo badge indica si la actividad se
                                    <strong class="text-gray-900 dark:text-white">consolidó en una lección</strong> del plan
                                    digital (LMS). La + segunda capa de avance.</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span><span><strong class="text-gray-800 dark:text-gray-200">Lección aprobada</strong> — publicada en el LMS.</span></li>
                                    <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-sky-500 flex-shrink-0"></span><span><strong class="text-gray-800 dark:text-gray-200">Lección programada</strong> — agendada para su publicación.</span></li>
                                    <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-gray-400 flex-shrink-0"></span><span><strong class="text-gray-800 dark:text-gray-200">Lección pendiente</strong> — la actividad aún no tiene lección.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Cómo leer ambos badges?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Interpretación del director</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Una actividad <strong class="text-gray-800 dark:text-gray-200">aprobada y con lección aprobada</strong> es el flujo completo deseado.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Muchos <strong class="text-gray-800 dark:text-gray-200">«lección pendiente»</strong> indican que las clases no se digitalizan de forma constante.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>La <strong class="text-gray-800 dark:text-gray-200">aprobación</strong> la gestiona el Jefe de Área; el director <strong class="text-gray-800 dark:text-gray-200">solo observa</strong> en este módulo.</span></li>
                                </ul>
                                <div class="rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-3">
                                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300">Este panel <strong>no filtra por estado</strong>: usa el buscador, el Plan Estudio/Grado/Sección, el Profesor y el Lapso para acotar la lista.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: VISTAS Y FILTROS ──────────────────────── --}}
            <div x-show="tab === 'vistas'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">El panel de filtros</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Buscador · contexto · lapso</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Buscar</strong> por <strong class="text-gray-800 dark:text-gray-200">tema o temática</strong> (búsqueda incremental, con retardo).</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Plan Estudio</strong> → filtra por malla académica.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Profesor</strong> → acota por docente (apellido, nombre).</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Grado/Año → Sección</strong> → cascada: al elegir un grado, la sección se actualiza.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Lapso</strong> → periodo académico (más reciente primero).</span></li>
                                </ul>
                                <div class="rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-3">
                                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300">Los filtros se combinan y se aplican al instante. Cambiar un plan o grado reinicia la selección de sus dependencias.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-cyan-50 dark:bg-cyan-500/15 border border-cyan-200 dark:border-cyan-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vista Grid</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Resumen visual por actividad</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Tarjetas en <strong class="text-gray-900 dark:text-white">columnas masonry</strong>
                                    (hasta 4 en pantallas amplias): tema, asignatura · sección · grado, temática, profesor, lapso y
                                    los dos badges de estado. Útil para una <strong class="text-gray-900 dark:text-white">lectura rápida</strong>
                                    del avance docente.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-cyan-50 dark:bg-cyan-500/15 border border-cyan-200 dark:border-cyan-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vista Tabla + PDFs</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Tema · Asignatura · Sección · Profesor · Lapso · Estado · Acciones</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">La <strong class="text-gray-900 dark:text-white">Tabla</strong> compara
                                    muchas filas de un vistazo. Cada fila ofrece dos documentos:
                                    <strong class="text-gray-900 dark:text-white">Formato PDF</strong> (plantilla de la
                                    evaluación) y <strong class="text-gray-900 dark:text-white">Resumen PDF</strong>.</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-cyan-600 dark:text-cyan-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Formato</strong> — documento oficial de la actividad/evaluación.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-cyan-600 dark:text-cyan-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Resumen</strong> — síntesis imprimible para la dirección.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-cyan-600 dark:text-cyan-400 mt-0.5">▸</span><span>El toggle <strong class="text-gray-800 dark:text-gray-200">Grid/Tabla</strong> se recuerda en <code class="text-[11px] px-1 py-0.5 rounded bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300">localStorage</code>.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4 mt-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-100 dark:bg-white/5 px-2 py-1 text-[10px] font-semibold text-gray-600 dark:text-gray-300">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Modo solo lectura
                    </span>
                </div>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">La aprobación y la digitalización las gestionan los Jefes de Área y Planificación · el director no modifica.</p>
            </div>
        </div>

    </div>
    {{-- /HELP PANEL --}}


</div>
