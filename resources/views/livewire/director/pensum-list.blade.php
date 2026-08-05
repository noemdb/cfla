{{-- resources/views/livewire/director/pensum-list.blade.php --}}
<div class="fade-in">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Pensums</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Consulta de pensums por estudio · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por asignatura, grado o estudio…"
                class="w-full sm:w-64 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
            <select wire:model.live="peducativoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Todos los peducativos</option>
                @foreach($peducativos as $peducativo)
                    <option value="{{ $peducativo->id }}">{{ $peducativo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Subtitle + Resultados por página + View Toggle (persiste en localStorage, sincronizado por evento) --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
        <div class="flex flex-wrap items-center gap-3">
            <p class="text-[11px] text-gray-400 font-medium">
                <span class="text-emerald-400">Pensums</span> de la institución · solo lectura
            </p>
            <div class="flex items-center gap-2">
                <label for="paginate" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Resultados</label>
                <select id="paginate" wire:model.live="paginate"
                    class="rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs font-semibold text-gray-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-300">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="9999">Todos</option>
                </select>
            </div>
        </div>
        <div x-data="{ mode: localStorage.getItem('pensums-view-mode') || 'table' }"
             x-init="$watch('mode', val => {
                 localStorage.setItem('pensums-view-mode', val);
                 window.dispatchEvent(new CustomEvent('pensums-view-mode-changed', { detail: { mode: val } }))
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
         x-data="{ mode: localStorage.getItem('pensums-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('pensums-view-mode')) localStorage.setItem('pensums-view-mode', 'table') }"
         x-on:pensums-view-mode-changed.window="mode = $event.detail.mode">

        {{-- Grid Mode: columnas masonry responsive --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-2.5">
                @forelse($pensums as $pensum)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 break-inside-avoid mb-2.5 dark:border-white/5 dark:bg-gray-900">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-sky-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-sky-500 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate dark:text-white">{{ $pensum->asignatura?->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $pensum->grado?->name }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $pensum->pestudio?->name }}</span>
                            <span class="inline-flex items-center rounded-md bg-sky-500/10 px-2 py-0.5 text-[10px] font-semibold text-sky-600 dark:text-sky-400">{{ $pensum->pestudio?->peducativo?->name ?? '—' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 break-inside-avoid dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                        Sin pensums para los filtros seleccionados.
                    </div>
                @endforelse
            </div>
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
                                <th class="px-5 py-3">Grado</th>
                                <th class="px-5 py-3">Estudio</th>
                                <th class="px-5 py-3">Peducativo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pensums as $pensum)
                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 dark:border-white/5 dark:hover:bg-white/5">
                                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $pensum->asignatura?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $pensum->grado?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $pensum->pestudio?->name }}</td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $pensum->pestudio?->peducativo?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin pensums para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Paginación: mismo estilo que el módulo de planificación (vendor.pagination.custom-tailwind) --}}
    @if($pensums->hasPages())
        <div class="mt-6">
            {{ $pensums->links('vendor.pagination.custom-tailwind') }}
        </div>
    @endif

</div>
