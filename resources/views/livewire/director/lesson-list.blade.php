{{-- resources/views/livewire/director/lesson-list.blade.php --}}
<div class="fade-in" x-data="{ helpOpen: false }" x-cloak>

    <div class="mb-6">
        <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Lecciones</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de lecciones del LMS · solo lectura</p>
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
            <span class="text-emerald-400">Lecciones</span> del LMS · solo lectura
        </p>
        <div x-data="{ mode: localStorage.getItem('lessons-view-mode') || 'table' }"
             x-init="$watch('mode', val => {
                 localStorage.setItem('lessons-view-mode', val);
                 window.dispatchEvent(new CustomEvent('lessons-view-mode-changed', { detail: { mode: val } }))
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

            {{-- Ver/Imprimir: página de impresión de lecciones LMS (Mermaid renderizado
                 en el navegador). Lleva los filtros activos como query string; es un
                 <a href> sencillo — compatible con el módulo de solo lectura. --}}
            <a href="{{ route('app.director.lessons.print', array_filter([
                    'lapso'    => $lapso_id ?: null,
                    'pestudio' => $pestudio_id ?: null,
                    'grado'    => $grado_id ?: null,
                    'seccion'  => $seccion_id ?: null,
                    'profesor' => $profesor_id ?: null,
                    'search'   => $search ?: null,
                ])) }}"
                target="_blank"
                title="Ver todas las lecciones en una página de impresión (Mermaid renderizado en el navegador)"
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-teal-500/30 bg-teal-500/10 text-teal-400 transition-all duration-200 text-[10px] font-bold hover:bg-teal-500/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span class="hidden sm:inline">Ver / Imprimir</span>
            </a>
        </div>
    </div>

    {{-- View container: escucha el evento y sincroniza el modo con el toggle --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('lessons-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('lessons-view-mode')) localStorage.setItem('lessons-view-mode', 'table') }"
         x-on:lessons-view-mode-changed.window="mode = $event.detail.mode">

        {{-- Grid Mode: columnas masonry responsive --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-2.5">
                @forelse($lessons as $lesson)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 break-inside-avoid mb-2.5 dark:border-white/5 dark:bg-gray-900">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-purple-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate dark:text-white">{{ $lesson->topic }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                    @if($lesson->pevaluacion?->pensum?->asignatura?->name){{ $lesson->pevaluacion->pensum->asignatura->name }} · @endif
                                    @if($lesson->pevaluacion?->seccion?->name){{ $lesson->pevaluacion->seccion->name }}@if($lesson->pevaluacion?->seccion?->grado?->name) · {{ $lesson->pevaluacion->seccion->grado->name }}@endif @endif
                                </p>
                            </div>
                        </div>
                        @if($lesson->thematic)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 truncate">{{ $lesson->thematic }}</p>
                        @endif
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $lesson->pevaluacion?->profesor?->lastname }}, {{ $lesson->pevaluacion?->profesor?->name }}</span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $lesson->pevaluacion?->lapso?->name }}
                            </span>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            {{-- Estado de publicación LMS (PUBLISHED/SCHEDULED/DRAFT/ARCHIVED; null = Sin publicar) --}}
                            @if($lesson->lmsPublication && $lesson->lmsPublication->status === 'PUBLISHED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Publicada
                                </span>
                            @elseif($lesson->lmsPublication && $lesson->lmsPublication->status === 'SCHEDULED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-100 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-[10px] font-bold rounded-md border border-sky-200 dark:border-sky-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Programada
                                </span>
                            @elseif($lesson->lmsPublication && $lesson->lmsPublication->status === 'ARCHIVED')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-2 13H6L4 7m16 0l-8 4m0 0L4 7"></path></svg>
                                    Archivada
                                </span>
                            @elseif($lesson->lmsPublication)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Borrador
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-stone-100 dark:bg-stone-500/10 text-stone-600 dark:text-stone-400 text-[10px] font-bold rounded-md border border-stone-200 dark:border-stone-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"></path></svg>
                                    Sin publicar
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                            {{ $lesson->lmsSections->count() }} secciones
                            @if($lesson->lmsPublication?->published_at)· Publicada {{ $lesson->lmsPublication->published_at->format('d/m/Y') }} @endif
                        </p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 break-inside-avoid dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                        Sin lecciones para los filtros seleccionados.
                    </div>
                @endforelse
            </div>

            @if($lessons->hasPages())
                <x-pagination-wrapper :paginator="$lessons" />
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
                                <th class="px-5 py-3">Contenido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lessons as $lesson)
                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 dark:border-white/5 dark:hover:bg-white/5">
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $lesson->topic }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $lesson->pevaluacion?->pensum?->asignatura?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $lesson->pevaluacion?->seccion?->name }}
                                        @if($lesson->pevaluacion?->seccion?->grado?->name)
                                            <span class="text-gray-400 dark:text-gray-500">·</span> {{ $lesson->pevaluacion->seccion->grado->name }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $lesson->pevaluacion?->profesor?->lastname }}, {{ $lesson->pevaluacion?->profesor?->name }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $lesson->pevaluacion?->lapso?->name }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        {{-- Estado de publicación LMS (PUBLISHED/SCHEDULED/DRAFT/ARCHIVED; null = Sin publicar) --}}
                                        @if($lesson->lmsPublication && $lesson->lmsPublication->status === 'PUBLISHED')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Publicada
                                            </span>
                                        @elseif($lesson->lmsPublication && $lesson->lmsPublication->status === 'SCHEDULED')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-100 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-[10px] font-bold rounded-md border border-sky-200 dark:border-sky-500/20">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Programada
                                            </span>
                                        @elseif($lesson->lmsPublication && $lesson->lmsPublication->status === 'ARCHIVED')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-2 13H6L4 7m16 0l-8 4m0 0L4 7"></path></svg>
                                                Archivada
                                            </span>
                                        @elseif($lesson->lmsPublication)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-[10px] font-bold rounded-md border border-gray-200 dark:border-white/10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Borrador
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-stone-100 dark:bg-stone-500/10 text-stone-600 dark:text-stone-400 text-[10px] font-bold rounded-md border border-stone-200 dark:border-stone-500/20">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"></path></svg>
                                                Sin publicar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $lesson->lmsSections->count() }} secciones
                                        @if($lesson->lmsPublication?->published_at)· {{ $lesson->lmsPublication->published_at->format('d/m/Y') }} @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin lecciones para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($lessons->hasPages())
                <x-pagination-wrapper :paginator="$lessons" />
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- HELP BUTTON: Guía de estados de lecciones (contexto director) --}}
    {{-- ============================================================ --}}
    {{-- Botón flotante --}}
    <button @click="helpOpen = true"
            class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
            title="Guía de estados de lecciones"
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
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Guía de Estados de Lecciones</h2>
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
        <div class="p-6" x-data="{ tab: 'published' }">
            {{-- Intro text --}}
            <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    Cada lección en el LMS atraviesa un <strong class="text-gray-900 dark:text-white">ciclo de vida</strong> definido por su estado de publicación.
                    Desde el <strong class="text-gray-900 dark:text-white">panel de la Dirección</strong> supervisas el contenido publicable de <strong class="text-emerald-600 dark:text-emerald-500">toda la institución</strong> en <strong class="text-gray-900 dark:text-white">modo solo lectura</strong>.
                    Comprender estos estados te permite evaluar el avance docente, detectar rezago por grado o sección,
                    identificar oportunidades de mejora y garantizar que el contenido digital esté disponible para los estudiantes cuando lo necesiten.
                </p>
            </div>

            {{-- Tabs navigation --}}
            <div class="flex gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
                <button @click="tab = 'published'"
                        :class="tab === 'published' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Publicado
                    </span>
                </button>
                <button @click="tab = 'scheduled'"
                        :class="tab === 'scheduled' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Programado
                    </span>
                </button>
                <button @click="tab = 'draft'"
                        :class="tab === 'draft' ? 'bg-gray-200 dark:bg-white/20 text-gray-800 dark:text-gray-300 border-gray-300 dark:border-white/20 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Borrador
                    </span>
                </button>
                <button @click="tab = 'archived'"
                        :class="tab === 'archived' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-500/30 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M10 12h4"/></svg>
                        Archivado
                    </span>
                </button>
            </div>

            {{-- ─── TAB: PUBLICADO ─────────────────────────────── --}}
            <div x-show="tab === 'published'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Publicado"?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Estado: <span class="text-emerald-600 dark:text-emerald-400">PUBLISHED</span></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Una lección en estado <strong class="text-emerald-600 dark:text-emerald-400">Publicado</strong> está completamente visible y accesible para los estudiantes en su aula virtual. Pueden ver su contenido, descargar recursos, acceder a enlaces externos y participar en las actividades diseñadas por el docente.</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Visible para estudiantes</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Contenido completo</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Recursos descargables</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">✓ Participación activa</span>
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
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Publicado</strong> es el indicador principal de que el docente completó su flujo de trabajo y el contenido está siendo consumido por los estudiantes. Desde la dirección (solo lectura) debes:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Auditar el volumen publicado</strong>: verifica que todas las asignaturas, grados y secciones tengan lecciones publicadas en el lapso vigente.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Detectar rezago</strong>: identifica grados o secciones con pocas publicaciones o con lecciones sin contenido completo.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Monitorear la proporción</strong> entre borradores y publicaciones: un alto número de borradores puede indicar contenido estancado.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Usar la impresión</strong> de lecciones para revisión global del contenido publicado.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Rol de la dirección (no interviene)</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">La dirección solo observa</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10"><span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded border border-amber-200 dark:border-amber-500/20">No publica</span><span class="text-xs text-gray-500 dark:text-gray-400">La dirección no aprueba ni publica lecciones. Esa tarea corresponde a Planificación y al docente. Tu panel es de solo lectura.</span></div>
                                    <div class="flex items-center gap-3 p-2.5 bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10"><span class="text-xs font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-500/10 px-2 py-1 rounded border border-sky-200 dark:border-sky-500/20">Alcance global</span><span class="text-xs text-gray-500 dark:text-gray-400">Observas todas las asignaturas, grados, secciones y profesores, sin filtro de scope, para tener una visión institucional completa.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>El docente de <strong class="text-gray-800 dark:text-gray-200">Matemáticas</strong> publica la lección <strong class="text-gray-800 dark:text-gray-200">«Ecuaciones de primer grado»</strong> para 5to año, sección A</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>En tu listado, la lección muestra el badge <strong class="text-emerald-600 dark:text-emerald-400">Publicada</strong> junto a su fecha de publicación</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-sky-400"></span><span>Puedes filtrar por ese grado/sección y confirmar que la cobertura curricular está al día en toda la institución</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── TAB: PROGRAMADO ────────────────────────────── --}}
            <div x-show="tab === 'scheduled'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Programado"?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Estado: <span class="text-amber-600 dark:text-amber-400">SCHEDULED</span></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Una lección en estado <strong class="text-amber-600 dark:text-amber-400">Programado</strong> fue enviada por el docente para revisión de Planificación. El contenido está listo pero permanece invisible para los estudiantes hasta que Planificación lo <strong class="text-emerald-600 dark:text-emerald-400">publique</strong> o llegue la fecha programada.</p>
                                <div class="bg-amber-50/50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-lg p-3">
                                    <p class="text-xs text-amber-700/80 dark:text-amber-300/80 flex items-start gap-2"><svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>La <strong>programación</strong> es la fase previa a la publicación. La dirección la observa como señal de que el docente ya terminó su contenido y está a la espera de aprobación.</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
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
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Programado</strong> indica que el docente <strong class="text-gray-900 dark:text-white">solicitó la publicación</strong> de su lección y está esperando la revisión de Planificación. Desde la dirección (solo lectura):</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Mide la anticipación</strong>: contenidos programados con poca antelación pueden indicar planificación reactiva en algún docente o área.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Verifica la fecha</strong>: ¿la programación está alineada con el cronograma académico y las fechas de los lapsos?</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Detecta cuellos de botella</strong>: un volumen alto de programados que se acumula puede indicar un retraso en la revisión de Planificación, no del docente.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>El docente de <strong class="text-gray-800 dark:text-gray-200">Ciencias Naturales</strong> programa la lección <strong class="text-gray-800 dark:text-gray-200">«La Célula»</strong> para el viernes a las 8:00 AM</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>En tu listado aparece con el badge <strong class="text-sky-600 dark:text-sky-400">Programada</strong>. Puedes identificar rápidamente cuántas lecciones están pendientes de aprobación por Planificación.</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>Si el volumen de lecciones programadas es elevado y no se publican, coordina con Planificación la revisión pendiente.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── TAB: BORRADOR ──────────────────────────────── --}}
            <div x-show="tab === 'draft'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 border border-gray-200 dark:border-white/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Borrador"?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Estado: <span class="text-gray-600 dark:text-gray-400">DRAFT</span></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Una lección en estado <strong class="text-gray-700 dark:text-gray-300">Borrador</strong> está siendo creada o editada. Tiene un registro de publicación pero no es visible para los estudiantes. El docente puede estar agregando secciones, recursos, enlaces o ajustando el contenido. Es un estado <strong class="text-gray-900 dark:text-white">transitorio</strong>: la lección está en proceso de construcción.</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-400 border border-gray-200 dark:border-white/20">✗ No visible</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-400 border border-gray-200 dark:border-white/20">✗ Contenido parcial</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-400 border border-gray-200 dark:border-white/20">✗ Sin acceso estudiantes</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-400 border border-gray-200 dark:border-white/20">⟳ En edición</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
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
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Borrador</strong> es el más común durante la fase de creación. Desde la dirección representa una <strong class="text-gray-900 dark:text-white">oportunidad</strong> para medir el trabajo en proceso:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-gray-500 dark:text-gray-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Identifica contenido estancado</strong>: lecciones en borrador por más de 1-2 semanas pueden indicar dificultades del docente o falta de tiempo.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-gray-500 dark:text-gray-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Mide el progreso general</strong>: un volumen alto de borradores frente a publicaciones indica que el contenido está en preparación pero aún no disponible.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-gray-500 dark:text-gray-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Coordina con el docente o coordinador</strong> si observas borradores que deberían estar publicados según la planificación académica.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-gray-400"></span><span>El docente de <strong class="text-gray-800 dark:text-gray-200">Inglés</strong> comienza a crear la lección <strong class="text-gray-800 dark:text-gray-200">«Present Simple»</strong> — queda automáticamente en borrador</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-gray-400"></span><span>Agrega secciones y recursos, pero aún no publica ni programa</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-sky-400"></span><span>En tu listado aparece con el badge <strong class="text-gray-600 dark:text-gray-400">Borrador</strong>. Un volumen alto de borradores en un lapso te indica trabajo docente en curso.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── TAB: ARCHIVADO ─────────────────────────────── --}}
            <div x-show="tab === 'archived'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-500/15 border border-red-200 dark:border-red-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M10 12h4"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué significa "Archivado"?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Estado: <span class="text-red-600 dark:text-red-400">ARCHIVED</span></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Una lección en estado <strong class="text-red-600 dark:text-red-400">Archivado</strong> fue publicada previamente pero ha sido despublicada. Ya no es visible para los estudiantes. A diferencia de "Borrador" (que nunca fue visible), el contenido archivado tiene un <strong class="text-gray-900 dark:text-white">historial de publicación</strong> y la lección permanece en el sistema con todo su contenido intacto para consulta o republicación futura.</p>
                                <div class="bg-red-50/50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg p-3">
                                    <p class="text-xs text-red-700/80 dark:text-red-300/80 flex items-start gap-2"><svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span><strong>No se pierde nada.</strong> El contenido, secciones, recursos y configuraciones se conservan íntegramente.</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
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
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">El estado <strong class="text-gray-900 dark:text-white">Archivado</strong> representa contenido que cumplió su ciclo o fue retirado por decisión pedagógica. Desde la dirección:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-red-600 dark:text-red-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Revisa el volumen de archivados</strong>: muchas lecciones archivadas repentinamente pueden indicar un cambio de planificación o un error masivo.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-red-600 dark:text-red-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Usa el historial como referencia</strong>: las lecciones archivadas mantienen su contenido y son útiles para planificar el siguiente período.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-red-600 dark:text-red-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Identifica el contexto</strong>: ¿se archivó por fin de lapso, por decisión del docente o por error? Cada caso tiene implicaciones distintas.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo en el aula</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>La lección <strong class="text-gray-800 dark:text-gray-200">«Geometría básica»</strong> del Lapso 1 estuvo publicada y visible durante todo el período</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-red-400"></span><span>Al iniciar el Lapso 2, el docente archiva la lección para dejar espacio al nuevo contenido</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-sky-400"></span><span>En tu listado, el badge <strong class="text-gray-600 dark:text-gray-400">Archivada</strong> te permite distinguir contenido retirado del que nunca se publicó</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── FOOTER: nota read-only ─────────────────────── --}}
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-white/10">
                <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg p-4">
                    <div class="flex items-start gap-2 mb-3">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Modo solo lectura</h3>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed mt-1">
                                Este panel de la Dirección es de <strong class="text-emerald-600 dark:text-emerald-500">solo lectura</strong>:
                                observas, supervisas y auditas el contenido de toda la institución, pero <strong class="text-gray-800 dark:text-gray-200">no modificas</strong> estados de lecciones.
                                Las transiciones de publicación (programar, publicar, archivar) las realizan el docente y Planificación desde sus módulos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
