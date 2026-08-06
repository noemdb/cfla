{{-- resources/views/livewire/director/profesor-indicators.blade.php --}}
<div class="fade-in" x-data="{ helpOpen: false }" x-cloak>

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Profesores</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de docentes y su carga por lapso · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por apellido o nombre…"
                class="w-full sm:w-64 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
            <select wire:model.live="lapsoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Todos los lapsos</option>
                @foreach($lapsos as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($profesores as $profesor)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 flex flex-col md:flex-row md:items-center justify-between gap-3 dark:border-white/5 dark:bg-gray-900">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate dark:text-white">{{ $profesor->lastname }}, {{ $profesor->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Docente activo</p>
                    </div>
                </div>
                <div class="md:text-right shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                        {{ $profesor->peva_count }} evaluaciones
                    </span>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                Sin profesores para los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        <x-pagination-wrapper :paginator="$profesores" />
    </div>

    {{-- ============================================================ --}}
    {{-- HELP BUTTON: Guía de Profesores (READ ONLY) --}}
    {{-- ============================================================ --}}
    {{-- Botón flotante --}}
    <button @click="helpOpen = true"
            class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
            title="Guía de consulta de profesores"
            x-show="!helpOpen">
        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
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
                    <span class="w-10 h-10 rounded-full bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900 dark:text-white">Guía de Consulta de Profesores</h2>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Para el director · supervisión de toda la institución (solo lectura)</p>
                    </div>
                </div>
                <button @click="helpOpen = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" title="Cerrar">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                Cada registro es un <strong class="text-gray-900 dark:text-white">docente activo</strong> con su
                <strong class="text-gray-900 dark:text-white">carga de evaluaciones</strong> en el plano académico
                (<strong class="text-gray-900 dark:text-white">pevaluaciones</strong>). Esta guía te ayuda a
                <strong class="text-gray-900 dark:text-white">supervisar</strong> el volumen de carga docente por lapso.
            </p>
        </div>

        <div class="px-6 py-5" x-data="{ tab: 'que-es' }">

        {{-- Tab buttons --}}
        <div class="flex flex-wrap gap-1.5 mb-5">
            <button @click="tab = 'que-es'"
                :class="tab === 'que-es' ? 'bg-indigo-100 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border-indigo-300 dark:border-indigo-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-indigo-600 dark:hover:text-indigo-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Qué es
            </button>
            <button @click="tab = 'muestra'"
                :class="tab === 'muestra' ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-emerald-600 dark:hover:text-emerald-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Qué muestra
            </button>
            <button @click="tab = 'lectura'"
                :class="tab === 'lectura' ? 'bg-violet-100 dark:bg-violet-500/15 text-violet-700 dark:text-violet-300 border-violet-300 dark:border-violet-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-violet-600 dark:hover:text-violet-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V5a2 2 0 00-2-2h-7m-6 18V3a1 1 0 011-1h3a1 1 0 011 1v18a1 1 0 01-1 1H5a1 1 0 01-1-1z"/></svg>
                Filtros y lectura
            </button>
        </div>
            {{-- ─── TAB: QUÉ ES ─────────────────────────────────── --}}
            <div x-show="tab === 'que-es'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-200 dark:border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué representa un docente aquí?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Docente activo y su carga</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Cada fila es un <strong class="text-gray-900 dark:text-white">profesor activo</strong> de la
                                    institución. El número asociado es su <strong class="text-gray-900 dark:text-white">carga
                                    de evaluaciones</strong>: las <strong class="text-gray-900 dark:text-white">pevaluaciones</strong>
                                    que tiene asignadas, ya sea en todo el periodo o acotadas a un lapso.
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center rounded-md bg-indigo-100 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:text-indigo-400">Docente activo</span>
                                    <span class="inline-flex items-center rounded-md bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">Carga (pevaluaciones)</span>
                                    <span class="inline-flex items-center rounded-md bg-violet-100 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20 px-2 py-0.5 text-[10px] font-bold text-violet-700 dark:text-violet-400">Por lapso</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
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
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Distribución</strong>: detectar docentes con carga muy alta o muy baja.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Por lapso</strong>: comparar cómo se reparte la carga entre lapsos.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Plantilla</strong>: el total activo orienta decisiones de contratación/distribución.</span></p>
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
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo de lectura</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-3 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">García, Andrea</p>
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>14 evaluaciones
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Al seleccionar un lapso, ese número se recalcula
                                    mostrando solo la carga de ese periodo, p. ej. <strong class="text-gray-900 dark:text-white">5 evaluaciones</strong>
                                    en el Lapso II.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: QUÉ MUESTRA ───────────────────────────── --}}
            <div x-show="tab === 'muestra'" x-transition:enter="transition-opacity duration-200">
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                    Cada tarjeta de la lista resume un docente y su carga. Esto es lo que muestra:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-200 dark:border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Nombre del docente</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">apellido, nombre</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-indigo-600 dark:text-indigo-400 mt-0.5">▸</span><span>Se muestra como <strong class="text-gray-800 dark:text-gray-200">Apellido, Nombre</strong>.</span></p>
                                <p class="flex items-start gap-2"><span class="text-indigo-600 dark:text-indigo-400 mt-0.5">▸</span><span>El buscador lo localiza por <strong class="text-gray-800 dark:text-gray-200">apellido</strong> o <strong class="text-gray-800 dark:text-gray-200">nombre</strong>.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Docente activo</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Estado</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span>El etiquetado <strong class="text-gray-800 dark:text-gray-200">Docente activo</strong> indica que forma parte de la plantilla en ejercicio.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span>La lista solo incluye <strong class="text-gray-800 dark:text-gray-200">docentes con seguimiento global</strong> de su carga.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-200 dark:border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Carga (evaluaciones)</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">peva_count</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-indigo-600 dark:text-indigo-400 mt-0.5">▸</span><span>El contador <strong class="text-gray-800 dark:text-gray-200">N evaluaciones</strong> (a la derecha) cuenta sus <strong class="text-gray-800 dark:text-gray-200">pevaluaciones</strong>.</span></p>
                                <p class="flex items-start gap-2"><span class="text-indigo-600 dark:text-indigo-400 mt-0.5">▸</span><span>Si eliges un <strong class="text-gray-800 dark:text-gray-200">lapso</strong>, solo se cuentan las de ese periodo.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Indicador de carga</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Lectura rápida</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>La insignia se muestra en <strong class="text-gray-800 dark:text-gray-200">índigo</strong>, a la derecha de cada tarjeta.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>«evaluaciones» es el término del módulo; equivale a las <strong class="text-gray-800 dark:text-gray-200">pevaluaciones</strong> registradas.</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: FILTROS Y LECTURA ─────────────────────── --}}
            <div x-show="tab === 'lectura'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">El buscador</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Apellido o nombre</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Busca por <strong class="text-gray-800 dark:text-gray-200">apellido</strong> o <strong class="text-gray-800 dark:text-gray-200">nombre</strong> del docente.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Se aplica de forma <strong class="text-gray-800 dark:text-gray-200">incremental</strong> (con leve retardo) al escribir.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">El filtro de Lapso</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Todos los lapsos · uno específico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Por defecto muestra <strong class="text-gray-800 dark:text-gray-200">Todos los lapsos</strong> (carga total).</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Al elegir un lapso, el contador de cada docente se <strong class="text-gray-800 dark:text-gray-200">recalcula</strong> solo con ese periodo.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Los lapsos se listan de <strong class="text-gray-800 dark:text-gray-200">más recientes a más antiguos</strong>.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h5v5H4zM15 4h5v5h-5zM4 15h5v5H4zM15 15h5v5h-5z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">El listado y su orden</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">20 por página · alfabético</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Los docentes se ordenan <strong class="text-gray-800 dark:text-gray-200">alfabéticamente por apellido</strong>.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>La lista pagina de <strong class="text-gray-800 dark:text-gray-200">20 en 20</strong>; usa los controles al final.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Al cambiar el buscador o el lapso se <strong class="text-gray-800 dark:text-gray-200">vuelve a la primera página</strong>.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.84m0 0L4 21l.84-4m-.84-4c0-4.418 4.03-8 9-8 3.68 0 6.83 1.87 8.47 4.66C18.3 9.77 17 8.05 15.55 7.9M4 13c0-4.418 4.03-8 9-8 3.68 0 6.83 1.87 8.47 4.66"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Resultado vacío</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Sin profesores</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Si no hay coincidencias verás «Sin profesores para los filtros seleccionados».</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Borra o amplía el término de búsqueda, o cambia el lapso, para volver al listado.</span></p>
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
                <p class="text-[10px] text-gray-400 dark:text-gray-500">La carga docente la gestiona el personal académico · el director solo supervisa.</p>
            </div>
        </div>

    </div>
    </div>
    {{-- /HELP PANEL --}}

</div>
