<div class="fade-in">

    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Lecciones LMS</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">Contenido publicado en el aula virtual</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-3 py-2 sm:px-5 sm:py-2.5 min-h-[44px] min-w-[44px] bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="hidden sm:inline">Actualizar</span>
            </button>

            {{-- View Mode Toggle (Grid / Table) — patrón crud-mode-toggle --}}
            <div class="inline-flex items-center bg-gray-100 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg p-0.5 gap-0.5"
                 x-data="{ mode: localStorage.getItem('coord-lessons-view-mode') || 'table' }"
                 x-init="$watch('mode', val => {
                     localStorage.setItem('coord-lessons-view-mode', val);
                     window.dispatchEvent(new CustomEvent('coord-lessons-view-mode-changed', { detail: { mode: val } }))
                 })">
                <button @click="mode = 'grid'"
                    :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                    title="Vista Grid">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="hidden sm:inline">Grid</span>
                </button>
                <button @click="mode = 'table'"
                    :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                    title="Vista Tabla">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="hidden sm:inline">Tabla</span>
                </button>
            </div>

            {{-- Ver/Imprimir: página de impresión de lecciones LMS (Mermaid renderizado en
                 el navegador). Lleva los filtros activos como query string; el scope del
                 coordinador (peducativos) lo aplica el controlador vía nombre de ruta. --}}
            <a href="{{ route('app.coordinacion.lessons.print', array_filter([
                    'lapso'    => $lapsoId ?: null,
                    'pestudio' => $pestudioId ?: null,
                    'profesor' => $profesorId ?: null,
                    'status'   => $filterStatus ?: null,
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

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tema de la lección..."
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Plan Estudio</label>
                <select wire:model.live="pestudioId"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($listPestudio as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Profesor</label>
                <select wire:model.live="profesorId"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($listProfesores as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Grado/Año</label>
                <select wire:model.live="gradoId"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($listGrado as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Sección</label>
                <select wire:model.live="seccionId"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    @foreach($listSeccion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Estado</label>
                <select wire:model.live="filterStatus"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="DRAFT">Borrador</option>
                    <option value="SCHEDULED">Programado</option>
                    <option value="PUBLISHED">Publicado</option>
                    <option value="ARCHIVED">Archivado</option>
                </select>
            </div>
        </div>
    </div>

    {{-- View Container (Grid / Table) — sincronizado con el toggle del header --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('coord-lessons-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('coord-lessons-view-mode')) localStorage.setItem('coord-lessons-view-mode', 'table') }"
         x-on:coord-lessons-view-mode-changed.window="mode = $event.detail.mode"
         wire:key="coord-lessons-view-{{ $lapsoId }}-{{ $pestudioId ?? 'all' }}-{{ $profesorId ?? 'all' }}-{{ $filterStatus ?? 'all' }}-{{ $search ?? 'all' }}">

        {{-- ═══ GRID MODE ═══ --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <style>
                .masonry-grid { --masonry-cols: 1; columns: var(--masonry-cols); column-gap: 0.625rem; }
                .masonry-item { break-inside: avoid; margin-bottom: 0.625rem; }
                .masonry-empty { break-inside: avoid; text-align: center; }
                @media (min-width: 640px)  { .masonry-grid { --masonry-cols: 2; } }
                @media (min-width: 1024px) { .masonry-grid { --masonry-cols: 3; } }
                @media (min-width: 1280px) { .masonry-grid { --masonry-cols: 4; } }
            </style>
            @if($lessons->count())
                <div class="masonry-grid">
                    @foreach($lessons as $lesson)
                        <div class="masonry-item">
                            @include('livewire.coordinacion.partials.lesson-card', ['lesson' => $lesson])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron lecciones</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda.</p>
                </div>
            @endif

            @if($lessons->hasPages())
                <div class="mt-6">
                    <x-pagination-wrapper :paginator="$lessons" />
                </div>
            @endif
        </div>

        {{-- ═══ TABLE MODE ═══ --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            @if($lessons->count())
                <div class="bg-white dark:bg-gray-800/30 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto [&::-webkit-scrollbar]:h-1.5" style="scrollbar-width: thin;">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-white/5">
                                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Tema</th>
                                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Asignatura</th>
                                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Profesor</th>
                                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Grado / Sección</th>
                                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Lapso</th>
                                    <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Estado</th>
                                    <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @foreach($lessons as $lesson)
                                    @php $pubStatus = $lesson->lmsPublication?->status; @endphp
                                    <tr class="hover:bg-emerald-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                                        <td class="px-5 py-3">
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $lesson->topic }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                            {{ $lesson->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                            {{ $lesson->pevaluacion?->profesor?->lastname ?? '' }}, {{ $lesson->pevaluacion?->profesor?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                            {{ $lesson->pevaluacion?->seccion?->grado?->name ?? '' }} · {{ $lesson->pevaluacion?->seccion?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                            {{ $lesson->pevaluacion?->lapso?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($pubStatus === 'PUBLISHED')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                                    Publicada
                                                </span>
                                            @elseif($pubStatus === 'SCHEDULED')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                                    Programada
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-500/10 text-gray-600 dark:text-gray-400 border border-gray-500/20">
                                                    Borrador
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <button type="button" wire:click="previewLesson({{ $lesson->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition-all duration-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Ver
                                                </button>
                                                <button type="button" wire:click="openActivityReview({{ $lesson->id }})"
                                                    class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg transition-all border
                                                        {{ $lesson->status
                                                            ? 'text-emerald-500 bg-emerald-500/10 hover:bg-emerald-500/20 border-emerald-500/20 hover:border-emerald-500/40'
                                                            : 'text-amber-500 bg-amber-500/10 hover:bg-amber-500/20 border-amber-500/20 hover:border-amber-500/40' }}"
                                                    title="{{ $lesson->status ? 'Actividad aprobada · ver/comentar' : 'Actividad en revisión: revisar y aprobar' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                @if($pubStatus === 'SCHEDULED')
                                                    <button type="button" wire:click="confirmPublish({{ $lesson->id }})"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all duration-200">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Publicar
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-16 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron lecciones</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda.</p>
                </div>
            @endif

            @if($lessons->hasPages())
                <div class="mt-6">
                    <x-pagination-wrapper :paginator="$lessons" />
                </div>
            @endif
        </div>
    </div>

    {{-- Student Preview Modal --}}
    @if($showLessonPreview && $previewData)
        <x-lms.student-preview
            :preview="$previewData"
            closeMethod="closeLessonPreview"
            wireKey="coord-lesson-preview" />
    @endif

    {{-- Publish Modal --}}
    @if($showPublishModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" wire:key="coord-publish-modal">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        Publicar lección
                    </h3>
                    <button wire:click="cancelPublish" class="p-2 min-w-[44px] min-h-[44px] rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        ¿Publicar la lección <strong class="text-gray-900 dark:text-white">{{ $publishLessonTitle }}</strong>?
                    </p>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de publicación (opcional)</label>
                        <input type="datetime-local" wire:model="publishPublishAt"
                               class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                        @error('publishPublishAt') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                            Vacío: visible de inmediato. Con fecha futura: queda en vista previa (1ª sección) hasta esa fecha.
                        </p>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-white/10 flex items-center justify-end gap-2">
                    <button wire:click="cancelPublish"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        Cancelar
                    </button>
                    <button wire:click="doPublish"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        Publicar ahora
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Actividad asociada (revisión / aprobación) --}}
    @if($showActivityModal && $activity)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" wire:key="coord-activity-review-{{ $activityId }}">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded-lg shadow-2xl w-full max-w-2xl mx-4 overflow-hidden max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 {{ $activity->status ? 'text-emerald-500' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Actividad asociada
                    </h3>
                    <button wire:click="closeActivityReview" class="p-2 min-w-[44px] min-h-[44px] rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4 overflow-y-auto">
                    {{-- Detalle de la actividad --}}
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-white/10 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border
                                {{ $activity->status
                                    ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20'
                                    : 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity->status ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/>
                                </svg>
                                {{ $activity->status ? 'Aprobada' : 'En revisión' }}
                            </span>
                            @if($activity->pevaluacion?->lapso)
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                    {{ $activity->pevaluacion->lapso->name }}
                                </span>
                            @endif
                            @if($activity->finicial && $activity->ffinal)
                                <span class="text-[10px] font-mono text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity->topic }}</p>
                        @if($activity->pevaluacion?->pensum?->asignatura)
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $activity->pevaluacion->pensum->asignatura->name }}
                                @if($activity->pevaluacion?->profesor) · {{ $activity->pevaluacion->profesor->fullname }} @endif
                            </p>
                        @endif
                        @if($activity->description)
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $activity->description }}</p>
                        @endif
                    </div>

                    {{-- Estado de aprobación --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Estado de Aprobación</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="activityStatus" value="1"
                                    class="w-4 h-4 text-emerald-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-emerald-500/50 focus:ring-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Aprobado</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="activityStatus" value="0"
                                    class="w-4 h-4 text-amber-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-amber-500/50 focus:ring-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">En revisión</span>
                            </label>
                        </div>
                    </div>

                    {{-- Comentario --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Comentario</label>
                        <textarea wire:model="comments" rows="4"
                            class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none resize-none transition-all"
                            placeholder="Escribe tu comentario..."></textarea>
                        @error('comments') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($activity->comments)
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900/30">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1">Comentario actual</p>
                            <p class="text-xs text-gray-700 dark:text-gray-200 italic">"{{ $activity->comments }}"</p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-white/10 flex items-center justify-end gap-2 shrink-0">
                    <button wire:click="closeActivityReview"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        Cancelar
                    </button>
                    <button wire:click="saveActivityReview" wire:loading.attr="disabled"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: revisión / aprobación de la actividad asociada --}}
    @if($showActivityModal && $activity)
        <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4"
            x-data x-init="$el.focus()" x-trap.noscroll="$root.classList.add('overflow-hidden')"
            wire:ignore.self>
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeActivityReview"></div>
            <div class="relative bg-white dark:bg-slate-900 w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl border border-gray-200 dark:border-slate-700/50 shadow-2xl max-h-[92vh] sm:max-h-[85vh] flex flex-col slide-up">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700/50 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Actividad asociada
                    </h3>
                    <button wire:click="closeActivityReview" class="p-2 min-w-[44px] min-h-[44px] rounded-lg text-gray-400 dark:text-slate-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4 overflow-y-auto">
                    {{-- Detalle de la actividad --}}
                    <div class="bg-gray-50 dark:bg-slate-900/50 p-4 rounded-lg border border-gray-200 dark:border-slate-700/50 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border
                                {{ $activity->status
                                    ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20'
                                    : 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity->status ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/>
                                </svg>
                                {{ $activity->status ? 'Aprobada' : 'En revisión' }}
                            </span>
                            @if($activity->pevaluacion?->lapso)
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">
                                    {{ $activity->pevaluacion->lapso->name }}
                                </span>
                            @endif
                            @if($activity->pevaluacion?->seccion?->grado)
                                <span class="text-[10px] font-mono text-gray-500 dark:text-slate-400">
                                    {{ $activity->pevaluacion->seccion->grado->name }} · Sección {{ $activity->pevaluacion->seccion->name }}
                                </span>
                            @endif
                            @if($activity->finicial && $activity->ffinal)
                                <span class="text-[10px] font-mono text-gray-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity->topic }}</p>
                        @if($activity->pevaluacion?->pensum?->asignatura)
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                {{ $activity->pevaluacion->pensum->asignatura->name }}
                                @if($activity->pevaluacion?->profesor) · {{ $activity->pevaluacion->profesor->fullname }} @endif
                            </p>
                        @endif
                        @if($activity->description)
                            <p class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed">{{ $activity->description }}</p>
                        @endif
                    </div>

                    {{-- Estado de aprobación --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-2">Estado de Aprobación</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="activityStatus" value="1"
                                    class="w-4 h-4 text-emerald-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-emerald-500/50 focus:ring-2">
                                <span class="text-sm text-gray-700 dark:text-slate-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Aprobado</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" wire:model="activityStatus" value="0"
                                    class="w-4 h-4 text-amber-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-amber-500/50 focus:ring-2">
                                <span class="text-sm text-gray-700 dark:text-slate-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">En revisión</span>
                            </label>
                        </div>
                    </div>

                    {{-- Comentario del Coordinador --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-2">Comentario del Coordinador</label>
                        <textarea wire:model="comments" rows="4"
                            class="w-full bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none resize-none transition-all"
                            placeholder="Escribe tu comentario como coordinador..."></textarea>
                        @error('comments') <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($activity->comments)
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/30">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-500 mb-1">Comentario actual</p>
                            <p class="text-xs text-gray-700 dark:text-slate-200 italic">"{{ $activity->comments }}"</p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-3 bg-gray-50 dark:bg-slate-900/50 border-t border-gray-200 dark:border-slate-700/50 flex items-center justify-end gap-2 shrink-0">
                    <button wire:click="closeActivityReview"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                    <button wire:click="saveActivityReview" wire:loading.attr="disabled"
                            class="px-4 py-1.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm border border-emerald-400/40 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
