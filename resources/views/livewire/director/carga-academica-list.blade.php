{{-- resources/views/livewire/director/carga-academica-list.blade.php --}}
<div class="fade-in" x-data="{ helpOpen: false }" x-cloak>

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Carga Académica</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de evaluaciones por sección y docente · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por docente, asignatura o sección…"
                class="w-full sm:w-64 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
            <select wire:model.live="peducativoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Todos los peducativos</option>
                @foreach($peducativos as $peducativo)
                    <option value="{{ $peducativo->id }}">{{ $peducativo->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="lapsoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Todos los lapsos</option>
                @foreach($lapsos as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Subtitle + View Toggle (persiste en localStorage, sincronizado por evento) --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
        <p class="text-[11px] text-gray-400 font-medium">
            <span class="text-emerald-400">Carga académica</span> de la institución · solo lectura
        </p>
        <div x-data="{ mode: localStorage.getItem('carga-academica-view-mode') || 'table' }"
             x-init="$watch('mode', val => {
                 localStorage.setItem('carga-academica-view-mode', val);
                 window.dispatchEvent(new CustomEvent('carga-academica-view-mode-changed', { detail: { mode: val } }))
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
         x-data="{ mode: localStorage.getItem('carga-academica-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('carga-academica-view-mode')) localStorage.setItem('carga-academica-view-mode', 'table') }"
         x-on:carga-academica-view-mode-changed.window="mode = $event.detail.mode">

        {{-- Grid Mode: columnas masonry responsive --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-2.5">
                @forelse($pevaluacions as $pevaluacion)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 break-inside-avoid mb-2.5 dark:border-white/5 dark:bg-gray-900">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-teal-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-teal-500 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate dark:text-white">{{ $pevaluacion->pensum?->asignatura?->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $pevaluacion->profesor?->lastname }}, {{ $pevaluacion->profesor?->name }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $pevaluacion->seccion?->name }}</span>
                            @if($pevaluacion->seccion?->grado?->name)
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $pevaluacion->seccion->grado->name }}</span>
                            @endif
                            <span class="inline-flex items-center rounded-md bg-sky-500/10 px-2 py-0.5 text-[10px] font-semibold text-sky-600 dark:text-sky-400">{{ $pevaluacion->pensum?->pestudio?->name }}</span>
                            <span class="inline-flex items-center rounded-md bg-sky-500/10 px-2 py-0.5 text-[10px] font-semibold text-sky-600 dark:text-sky-400">{{ $pevaluacion->pensum?->pestudio?->peducativo?->name ?? '—' }}</span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $pevaluacion->lapso?->name }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 break-inside-avoid dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                        Sin carga académica para los filtros seleccionados.
                    </div>
                @endforelse
            </div>

            @if($pevaluacions->hasPages())
                <x-pagination-wrapper :paginator="$pevaluacions" />
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
                                <th class="px-5 py-3">Asignatura</th>
                                <th class="px-5 py-3">Profesor</th>
                                <th class="px-5 py-3">Sección</th>
                                <th class="px-5 py-3">Plan</th>
                                <th class="px-5 py-3">Programa</th>
                                <th class="px-5 py-3">Lapso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pevaluacions as $pevaluacion)
                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 dark:border-white/5 dark:hover:bg-white/5">
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $pevaluacion->pensum?->asignatura?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $pevaluacion->profesor?->lastname }}, {{ $pevaluacion->profesor?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $pevaluacion->seccion?->name }}
                                        @if($pevaluacion->seccion?->grado?->name)
                                            <span class="text-gray-400 dark:text-gray-500">·</span> {{ $pevaluacion->seccion->grado->name }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $pevaluacion->pensum?->pestudio?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $pevaluacion->pensum?->pestudio?->peducativo?->name ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $pevaluacion->lapso?->name }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin carga académica para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($pevaluacions->hasPages())
                <x-pagination-wrapper :paginator="$pevaluacions" />
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- HELP BUTTON: Guía de consulta de carga académica (README) --}}
    {{-- ============================================================ --}}
    {{-- Botón flotante --}}
    <button @click="helpOpen = true"
            class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
            title="Guía de consulta de carga académica"
            x-show="!helpOpen">
        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
        </svg>
    </button>

    {{-- Backdrop --}}
    <div x-show="helpOpen"
         x-transition:enter="transition-opacity duration-300"
         x-transition:leave="transition-opacity duration-200"
         @click="helpOpen = false"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"></div>

    {{-- Slideover panel --}}
    <div x-show="helpOpen"
         x-transition:enter="transition-transform duration-300 ease-out"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         {{-- Cerrar con Escape --}}
         @keydown.escape.window="helpOpen = false"
         class="fixed top-0 right-0 z-50 h-full w-full max-w-2xl bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-white/10 shadow-2xl overflow-y-auto">

        {{-- Sticky header --}}
        <div class="sticky top-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-white/10 z-10">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Guía de Consulta de Carga Académica</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Para el director · supervisión de toda la institución (solo lectura)</p>
                    </div>
                </div>
                <button @click="helpOpen = false"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6" x-data="{ tab: 'que-es' }">
            {{-- Intro text --}}
            <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    La <strong class="text-gray-900 dark:text-white">carga académica</strong> es el conjunto de
                    <strong class="text-gray-900 dark:text-white">evaluaciones (Pevaluación)</strong> que materializan la malla:
                    cada registro asigna una <strong class="text-gray-900 dark:text-white">asignatura</strong> a un
                    <strong class="text-gray-900 dark:text-white">docente</strong> en una <strong class="text-gray-900 dark:text-white">sección</strong>, dentro de un
                    <strong class="text-gray-900 dark:text-white">estudio (Plan)</strong> y <strong class="text-gray-900 dark:text-white">lapso</strong>. Aquí la
                    <strong class="text-emerald-600 dark:text-emerald-400">consultas de forma read-only</strong> — su alta la realizan
                    Planificación y Coordinación.
                </p>
            </div>

            {{-- Tabs: Qué es / Vistas / Filtros y lectura --}}
            <div class="flex items-center gap-2 mb-5">
                <button @click="tab = 'que-es'"
                    :class="tab === 'que-es' ? 'bg-sky-500/15 border-sky-500/30 text-sky-600 dark:text-sky-400' : 'bg-gray-100 dark:bg-white/5 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border text-[11px] font-bold whitespace-nowrap transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Qué es
                </button>
                <button @click="tab = 'vistas'"
                    :class="tab === 'vistas' ? 'bg-emerald-500/15 border-emerald-500/30 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-white/5 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border text-[11px] font-bold whitespace-nowrap transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Vistas
                </button>
                <button @click="tab = 'filtros'"
                    :class="tab === 'filtros' ? 'bg-violet-500/15 border-violet-500/30 text-violet-600 dark:text-violet-400' : 'bg-gray-100 dark:bg-white/5 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border text-[11px] font-bold whitespace-nowrap transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtros y lectura
                </button>
            </div>
            {{-- ─── TAB: QUÉ ES ─────────────────────────────────── --}}
            <div x-show="tab === 'que-es'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué representa una evaluación (Pevaluación)?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">De la malla al aula</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    Cada fila/tarjeta registra <strong class="text-gray-900 dark:text-white">quién imparte qué, a quién y en qué lapso</strong>:
                                    la <strong class="text-sky-600 dark:text-sky-400">asignatura</strong> de un pensum, el
                                    <strong class="text-teal-600 dark:text-teal-400">docente</strong>, la
                                    <strong class="text-gray-900 dark:text-white">sección/grado</strong>, el
                                    <strong class="text-violet-600 dark:text-violet-400">Plan (Pestudio)</strong> al que pertenece,
                                    su <strong class="text-gray-900 dark:text-white">Peducativo</strong> y el
                                    <strong class="text-sky-600 dark:text-sky-400">lapso</strong> de vigencia.
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20">Asignatura</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-500/20">Docente</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-500/20">Plan</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/10">Lapso</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la dirección</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Como director puedes:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Auditar la cobertura docente</strong>: verificar que cada sección tenga sus asignaturas con profesor asignado.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Detectar sobrecarga</strong>: un mismo docente con muchas secciones/asignaturas en el mismo lapso.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Verificar coherente la malla</strong>: que las evaluaciones correspondan a pensums vigentes y no queden estudios sin carga.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Lectura de un registro</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-3">
                                    <p class="text-[11px] font-bold text-gray-900 dark:text-white">Matemática · I</p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400">García, María · Sección 2do "A"</p>
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400">Plan Básico</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400">U.E. Central</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold text-sky-600 dark:text-sky-400">● Lapso 1</span>
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">La <strong class="text-gray-700 dark:text-gray-300">docente García, María</strong> imparte Matemática I en el 2do "A" del Plan Básico, dentro de la U.E. Central, durante el Lapso 1.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: VISTAS ─────────────────────────────────── --}}
            <div x-show="tab === 'vistas'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-teal-50 dark:bg-teal-500/15 border border-teal-200 dark:border-teal-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vista Grid</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Tarjetas masonry</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    Tarjetas en columnas <strong class="text-gray-900 dark:text-white">masonry</strong> que muestran
                                    <strong class="text-teal-600 dark:text-teal-400">asignatura + docente</strong> y, debajo, las insignias:
                                    sección, grado, estudio (Plan), peducativo y lapso. Ideal para un
                                    <strong class="text-gray-900 dark:text-white">barrido visual rápido</strong> de la cobertura.
                                </p>
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-3">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-teal-500/10 flex items-center justify-center">
                                            <span class="text-teal-600 dark:text-teal-400 text-xs font-bold">M</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-gray-900 dark:text-white truncate">Matemática I</p>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">García, María</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300">2do "A"</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-sky-500/10 text-sky-600 dark:text-sky-400">Plan Básico</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-sky-500/10 text-sky-600 dark:text-sky-400">U.E. Central</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] text-sky-600 dark:text-sky-400">● Lapso 1</span>
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Muestra <strong class="text-gray-700 dark:text-gray-300">4 columnas</strong> en pantallas amplias; en dispositivos se pliega a 1–3 columnas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vista Tabla</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">6 columnas · ordenada</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    Tabla alineada con copete fijo: <strong class="text-gray-900 dark:text-white">Asignatura · Profesor · Sección ·
                                    Plan · Programa · Lapso</strong>. Ideal para <strong class="text-gray-900 dark:text-white">comparar y
                                    filtrar con exactitud</strong>, ordenar por columna y auditar en masa.
                                </p>
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 overflow-hidden">
                                    <table class="w-full text-[9px]">
                                        <thead>
                                            <tr class="text-left font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-white/10">
                                                <th class="px-2 py-1.5">Asignatura</th>
                                                <th class="px-2 py-1.5">Profesor</th>
                                                <th class="px-2 py-1.5">Sección</th>
                                                <th class="px-2 py-1.5">Plan</th>
                                                <th class="px-2 py-1.5">Programa</th>
                                                <th class="px-2 py-1.5">Lapso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b border-gray-100 dark:border-white/5">
                                                <td class="px-2 py-1.5 font-medium text-gray-900 dark:text-gray-100">Matemática I</td>
                                                <td class="px-2 py-1.5 text-gray-600 dark:text-gray-300">García, María</td>
                                                <td class="px-2 py-1.5 text-gray-600 dark:text-gray-300">2do "A"</td>
                                                <td class="px-2 py-1.5 text-gray-600 dark:text-gray-300">Básico</td>
                                                <td class="px-2 py-1.5 text-gray-600 dark:text-gray-300">U.E. Central</td>
                                                <td class="px-2 py-1.5 text-sky-600 dark:text-sky-400">L1</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Útil cuando necesitas ver <strong class="text-gray-700 dark:text-gray-300">muchas filas a la vez</strong> en un formato compacto y comparable.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Cuál usar?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Recomendación</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2.5">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Usa <strong class="text-sky-600 dark:text-sky-400">Tabla</strong> para auditar la cobertura total o detectar sobrecarga docente. Usa <strong class="text-teal-600 dark:text-teal-400">Grid</strong> para un repaso visual rápido de cada sección.</p>
                                <div class="rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-3">
                                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300"><strong>Recordatorio:</strong> tu elección se <strong>recuerda en este navegador</strong> (localStorage) y se aplica al volver a esta página.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: FILTROS Y LECTURA ─────────────────────── --}}
            <div x-show="tab === 'filtros'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Buscador</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Docente · asignatura · sección</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Docente</strong>: busca por apellido o nombre.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Asignatura</strong>: busca por el nombre de la materia.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Sección</strong>: busca por el código de la sección.</span></li>
                                </ul>
                                <div class="rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-3">
                                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300">La búsqueda es incremental (con retardo) y vuelve a la primera página automáticamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Filtros: Peducativo y Lapso</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Acota por unidad educativa / periodo</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Peducativo</strong>: «Todos los peducativos» o una unidad educativa concreta. Acota la carga a las secciones de esa unidad.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Lapso</strong>: «Todos los lapsos» o un periodo concreto (ordenados del más reciente al más antiguo).</span></li>
                                </ul>
                                <div class="rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-3">
                                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300">Filtra por <strong>un solo peducativo</strong> no siempre acota; combínalo con el buscador o el lapso para afinar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Cómo leer los resultados</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Paginación y estado vacío</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span>La lista se muestra de <strong class="text-gray-800 dark:text-gray-200">15 registros por página</strong> con paginación al pie.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span>Si ningún registro coincide, verás <strong class="text-gray-800 dark:text-gray-200">«Sin carga académica para los filtros seleccionados»</strong>: revisa o amplía los filtros.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span>Una <strong class="text-gray-800 dark:text-gray-200">ausencia de carga</strong> en una sección o un lapso puede señalar un <strong class="text-gray-800 dark:text-gray-200">vacío a revisar</strong> con Planificación.</span></li>
                                </ul>
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-3 text-center">
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Sin carga académica para los filtros seleccionados.</p>
                                </div>
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
                <p class="text-[10px] text-gray-400 dark:text-gray-500">La carga académica la gestiona Planificación y Coordinación · el director no modifica.</p>
            </div>
        </div>

    </div>
    {{-- /HELP PANEL --}}


</div>
