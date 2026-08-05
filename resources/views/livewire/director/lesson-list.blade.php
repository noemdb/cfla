{{-- resources/views/livewire/director/lesson-list.blade.php --}}
<div class="fade-in">

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

</div>
