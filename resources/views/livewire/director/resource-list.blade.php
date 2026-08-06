{{-- resources/views/livewire/director/resource-list.blade.php --}}
<div class="fade-in" x-data="{ helpOpen: false }" x-cloak>

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Recursos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de recursos educativos del LMS · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o tema…"
                class="w-full sm:w-64 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
        </div>
    </div>

    <div class="space-y-3">
        @forelse($resources as $resource)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 flex flex-col md:flex-row md:items-center justify-between gap-3 dark:border-white/5 dark:bg-gray-900">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate dark:text-white">{{ $resource->display_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            @if($resource->activity?->topic){{ $resource->activity->topic }} @endif
                            @if($resource->activity?->pevaluacion?->pensum?->asignatura?->name)· {{ $resource->activity->pevaluacion->pensum->asignatura->name }} @endif
                            @if($resource->activity?->pevaluacion?->profesor?->lastname)· {{ $resource->activity->pevaluacion->profesor->lastname }}, {{ $resource->activity->pevaluacion->profesor->name }} @endif
                        </p>
                        @if($resource->media?->original_name)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ $resource->media->original_name }} @if($resource->media->mime_type)· {{ $resource->media->mime_type }} @endif</p>
                        @endif
                    </div>
                </div>
                <div class="md:text-right shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Recurso
                    </span>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                Sin recursos para los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $resources->links() }}</div>

    {{-- ============================================================ --}}
    {{-- HELP BUTTON: Guía de Recursos (README) --}}
    {{-- ============================================================ --}}
    {{-- Botón flotante --}}
    <button @click="helpOpen = true"
            class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
            title="Guía de consulta de recursos"
            x-show="!helpOpen">
        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
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
                    <span class="w-10 h-10 rounded-full bg-amber-500/15 border border-amber-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900 dark:text-white">Guía de Consulta de Recursos</h2>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Para el director · supervisión de toda la institución (solo lectura)</p>
                    </div>
                </div>
                <button @click="helpOpen = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" title="Cerrar">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                Cada registro es un <strong class="text-gray-900 dark:text-white">archivo educativo</strong> que un
                <strong class="text-gray-900 dark:text-white">docente</strong> comparte en el plan digital (LMS) vinculado a
                una <strong class="text-gray-900 dark:text-white">actividad</strong> de una <strong class="text-gray-900 dark:text-white">asignatura</strong>
                del pensum. Esta guía te ayuda a <strong class="text-gray-900 dark:text-white">auditar</strong> los recursos
                compartidos.
            </p>
        </div>

        <div class="px-6 py-5" x-data="{ tab: 'que-es' }">

        {{-- Tab buttons --}}
        <div class="flex flex-wrap gap-1.5 mb-5">
            <button @click="tab = 'que-es'"
                :class="tab === 'que-es' ? 'bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-300 dark:border-amber-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-amber-600 dark:hover:text-amber-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Qué es
            </button>
            <button @click="tab = 'contenido'"
                :class="tab === 'contenido' ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-emerald-600 dark:hover:text-emerald-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Contenido
            </button>
            <button @click="tab = 'lectura'"
                :class="tab === 'lectura' ? 'bg-violet-100 dark:bg-violet-500/15 text-violet-700 dark:text-violet-300 border-violet-300 dark:border-violet-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-transparent hover:text-violet-600 dark:hover:text-violet-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V5a2 2 0 00-2-2h-7m-6 18V3a1 1 0 011-1h3a1 1 0 011 1v18a1 1 0 01-1 1H5a1 1 0 01-1-1z"/></svg>
                Lectura y filtros
            </button>
        </div>
            {{-- ─── TAB: QUÉ ES ─────────────────────────────────── --}}
            <div x-show="tab === 'que-es'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué representa un recurso?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Un archivo compartido del LMS</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Un recurso es un <strong class="text-gray-900 dark:text-white">archivo</strong>
                                    (presentación, guía, PDF…) que un docente <strong class="text-gray-900 dark:text-white">sube al LMS</strong>
                                    y vincula a una <strong class="text-gray-900 dark:text-white">actividad</strong> de una
                                    <strong class="text-gray-900 dark:text-white">asignatura</strong>. Refleja el material de
                                    apoyo que se pone a disposición del alumnado.
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center rounded-md bg-amber-100 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-400">Archivo</span>
                                    <span class="inline-flex items-center rounded-md bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">Actividad</span>
                                    <span class="inline-flex items-center rounded-md bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">Asignatura</span>
                                    <span class="inline-flex items-center rounded-md bg-violet-100 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20 px-2 py-0.5 text-[10px] font-bold text-violet-700 dark:text-violet-400">Docente</span>
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
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Material disponible</strong>: qué asignaturas/docentes comparten más apoyos.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Alcance</strong>: detectar asignaturas o secciones sin recursos publicados.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Tipos de archivo</strong>: si se suben materiales variados o solo de un formato.</span></p>
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
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo de un recurso</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <div class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 p-3 space-y-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Guía de ondas mecánicas.pdf</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">Ondas mecánicas · Física · Prof. García</p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">ondas-guia.pdf · application/pdf</p>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Es un PDF con la guía de ondas que el docente
                                    de Física comparte con su sección: el material de apoyo de una actividad de la asignatura.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: CONTENIDO ─────────────────────────────── --}}
            <div x-show="tab === 'contenido'" x-transition:enter="transition-opacity duration-200">
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                    Cada tarjeta de la lista resume un recurso compartido. Esto es lo que muestra:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Nombre del recurso</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">display_name</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span>Título <strong class="text-gray-800 dark:text-gray-200">visible</strong> del archivo tal como lo ven docentes y alumnado.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span>El buscador lo localiza por este nombre <strong class="text-gray-800 dark:text-gray-200">o</strong> por el tema de la actividad.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Actividad y asignatura</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Contexto académico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Tema</strong> de la actividad a la que pertenece.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Asignatura</strong> del pensum que el recurso apoya.</span></p>
                                <p class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Docente</strong> que la comparte (apellido, nombre).</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7.586a2 2 0 100 2.828l2.828 2.828a2 2 0 102.828-2.828l-2.828-2.828a2 2 0 000-2.828zM13 6a4 4 0 115.657 5.657M8 8l-4 4a2 2 0 102.828 2.828L16.242 5.414"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Archivo original y tipo</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">original_name · mime_type</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Nombre del archivo</strong> tal como se subió (p. ej. <code class="text-[11px] px-1 py-0.5 rounded bg-gray-100 dark:bg-white/10">guia.pdf</code>).</span></p>
                                <p class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Tipo MIME</strong> (p. ej. <code class="text-[11px] px-1 py-0.5 rounded bg-gray-100 dark:bg-white/10">application/pdf</code>) indica el formato del archivo.</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">La insignia «Recurso»</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Tipo de elemento</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span>La etiqueta <strong class="text-gray-800 dark:text-gray-200">ámbar</strong> a la derecha identifica el elemento como <strong class="text-gray-800 dark:text-gray-200">Recurso</strong> en el listado.</span></p>
                                <p class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span>Cada fila es de un <strong class="text-gray-800 dark:text-gray-200">solo tipo</strong>; esta lista es de recursos compartidos del LMS.</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ─── TAB: LECTURA Y FILTROS ─────────────────────── --}}
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
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Nombre o tema</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Busca por el <strong class="text-gray-800 dark:text-gray-200">nombre del recurso</strong> (<code class="text-[11px] px-1 py-0.5 rounded bg-gray-100 dark:bg-white/10">display_name</code>)…</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>…<strong class="text-gray-800 dark:text-gray-200">o por el tema de la actividad</strong> (<code class="text-[11px] px-1 py-0.5 rounded bg-gray-100 dark:bg-white/10">topic</code>) a la que pertenece.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Se aplica de forma <strong class="text-gray-800 dark:text-gray-200">incremental</strong> (con leve retardo) al escribir.</span></p>
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
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">20 por página · más recientes primero</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Los recursos se muestran de <strong class="text-gray-800 dark:text-gray-200">los más recientes a los más antiguos</strong>.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>La lista pagina de <strong class="text-gray-800 dark:text-gray-200">20 en 20</strong>; usa los controles al final para navegar.</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Al buscar se <strong class="text-gray-800 dark:text-gray-200">vuelve a la primera página</strong>.</span></p>
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
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Sin recursos</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Si no hay coincidencias verás «Sin recursos para los filtros seleccionados».</span></p>
                                <p class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span>Borra o amplía el término de búsqueda para volver al listado completo.</span></p>
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
                <p class="text-[10px] text-gray-400 dark:text-gray-500">Los recursos los suben los docentes al plan digital · el director no modifica.</p>
            </div>
        </div>

    </div>
    {{-- /HELP PANEL --}}


</div>
