{{-- resources/views/livewire/director/activity-list.blade.php --}}
<div class="fade-in">

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

</div>
