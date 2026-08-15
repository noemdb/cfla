<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Bitácora de Eventos</h1>
            <p class="text-emerald-400 font-medium text-sm">Registro cronológico e inmutable de actividad, autenticación y eventos del sistema.</p>
        </div>
        <div class="text-sm text-gray-400">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Solo lectura — registros inmutables
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-4">
        <form wire:submit.prevent="resetPage" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Buscar</label>
                <input type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Evento, usuario, objeto, IP, request_id…"
                       class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Categoría</label>
                <select wire:model.live="category" class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                    <option value="">Todas</option>
                    @foreach($meta['categories'] as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Severidad</label>
                <select wire:model.live="severity" class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                    <option value="">Todas</option>
                    @foreach($meta['severities'] as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Desde</label>
                <input type="date" wire:model.live="dateFrom"
                       class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Hasta</label>
                <input type="date" wire:model.live="dateTo"
                       class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
            </div>
        </form>

        <div class="flex flex-wrap items-center justify-between gap-3 mt-3 pt-3 border-t border-white/5">
            <button type="button" wire:click="resetFilters"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-400 hover:text-white hover:bg-white/5 border border-white/10 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Limpiar filtros
            </button>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.binnacle.export.pdf', request()->only(['search', 'category', 'severity', 'from', 'to'])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-300 border border-white/10 hover:bg-white/5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('admin.binnacle.export', request()->only(['search', 'category', 'severity', 'from', 'to'])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/10 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Exportar CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5 bg-gray-800/30">
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Severidad</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Categoría</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Evento</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Objeto</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-300 whitespace-nowrap">
                                {{ $entry->created_at?->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                <x-binnacle.badge :value="$entry->event_severity" kind="severity" />
                            </td>
                            <td class="px-4 py-3">
                                <x-binnacle.badge :value="$entry->event_category" kind="category" />
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-200 font-medium">{{ $entry->title }}</div>
                                @if($entry->description)
                                    <div class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $entry->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">
                                {{ $entry->subject_identifier ?: '—' }}
                                <span class="block text-xs text-gray-500">{{ $entry->subject_type ? class_basename($entry->subject_type) : '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">
                                {{ $entry->object_identifier ?: '—' }}
                                <span class="block text-xs text-gray-500">{{ $entry->object_type ? class_basename($entry->object_type) : '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono whitespace-nowrap">{{ $entry->ip_address ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-gray-400 text-sm">Aún no hay eventos registrados en la bitácora.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-white/5">
            {{ $entries->links() }}
        </div>
    </div>
</div>