<div class="fade-in" x-data="{ mode: localStorage.getItem('pensums-view-mode') || 'table' }"
     x-init="$watch('mode', val => {
         localStorage.setItem('pensums-view-mode', val);
         window.dispatchEvent(new CustomEvent('pensums-view-mode-changed', { detail: { mode: val } }))
     })">

    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Pensums</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">Información académica — pensums de tus programas educativos</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-3 py-2 sm:px-5 sm:py-2.5 min-h-[44px] min-w-[44px] bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="hidden sm:inline">Refrescar</span>
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Asignatura, grado o plan de estudio..."
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Programa Educativo</label>
                <select wire:model.live="peducativoId"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos los programas</option>
                    @foreach($peducativos as $ped)
                        <option value="{{ $ped->id }}">{{ $ped->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Ver</label>
                <select wire:model.live="paginate"
                    class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Grid/Table Toggle --}}
    <div class="flex justify-end mb-4">
        <div class="inline-flex items-center bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/5 rounded-lg p-0.5 gap-0.5">
            <button @click="mode = 'grid'"
                :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Grid">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="hidden sm:inline">Grid</span>
            </button>
            <button @click="mode = 'table'"
                :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Tabla">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Tabla</span>
            </button>
        </div>
    </div>

    {{-- ═══ TABLE VIEW ═══ --}}
    <div x-cloak x-show="mode === 'table'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-gray-800/50">
                            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Programa</th>
                            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plan Estudio</th>
                            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Asignatura</th>
                            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($pensums as $pensum)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $pensum->pestudio?->peducativo?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $pensum->pestudio?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-200 font-medium">
                                    {{ $pensum->asignatura?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs rounded-full font-medium">
                                        {{ $pensum->grado?->name ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-16 text-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron pensums</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pensums->hasPages())
            <x-pagination-wrapper :paginator="$pensums" />
        @endif
    </div>

    {{-- ═══ GRID VIEW ═══ --}}
    <div x-cloak x-show="mode === 'grid'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <style>
            .pensums-masonry { --masonry-cols: 1; columns: var(--masonry-cols); column-gap: 0.75rem; }
            .pensums-masonry-item { break-inside: avoid; margin-bottom: 0.75rem; }
            .pensums-masonry-empty { break-inside: avoid; text-align: center; }
            @media (min-width: 640px)  { .pensums-masonry { --masonry-cols: 2; } }
            @media (min-width: 1024px) { .pensums-masonry { --masonry-cols: 3; } }
            @media (min-width: 1280px) { .pensums-masonry { --masonry-cols: 4; } }
            @supports (grid-template-rows: masonry) {
                .pensums-masonry { display: grid; gap: 0.75rem; columns: unset; grid-template-columns: repeat(var(--masonry-cols), 1fr); grid-template-rows: masonry; }
                .pensums-masonry-item { break-inside: unset; margin-bottom: unset; }
                .pensums-masonry-empty { grid-column: 1 / -1; }
            }
        </style>

        @forelse($pensums as $pensum)
            {{-- Open masonry wrapper on first item --}}
            @if($loop->first)
            <div class="pensums-masonry">
            @endif
                <div class="pensums-masonry-item">
                    <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 hover:border-emerald-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] rounded-full font-bold uppercase tracking-wider">
                                {{ $pensum->grado?->name ?? '—' }}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1 leading-snug">
                            {{ $pensum->asignatura?->name ?? '—' }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                            {{ $pensum->pestudio?->name ?? '—' }}
                        </p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500">
                            {{ $pensum->pestudio?->peducativo?->name ?? '—' }}
                        </p>
                    </div>
                </div>
            @if($loop->last)
            </div>{{-- /pensums-masonry --}}
            @endif
        @empty
            <div class="text-center py-16 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron pensums</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda.</p>
            </div>
        @endforelse

        @if($pensums->hasPages())
            <x-pagination-wrapper :paginator="$pensums" />
        @endif
    </div>
</div>
