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
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-widest text-gray-400">Detalle</th>
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
                            <td class="px-4 py-3 text-right">
                                <button type="button" wire:click="openEntryDetails({{ $entry->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/10 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </button>
                            </td>
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

    <!-- Modal de detalle del evento (7xl) -->
    @if($viewingEntry)
        <div x-data="{ open: @js($showEntryDetails) }"
             x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div @click.self="open = false"
                 class="absolute inset-0"></div>
            <div class="relative w-full max-w-7xl max-h-[90vh] overflow-y-auto bg-gray-900 border border-white/10 rounded-xl shadow-2xl">
                <!-- Header -->
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 px-6 py-4 bg-gray-900/95 backdrop-blur border-b border-white/10">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-white truncate">{{ $viewingEntry->title }}</h3>
                            <p class="text-xs text-gray-500 font-mono">{{ $viewingEntry->event_type }} · {{ $viewingEntry->uuid }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <x-binnacle.badge :value="$viewingEntry->event_severity" kind="severity" />
                        <x-binnacle.badge :value="$viewingEntry->event_category" kind="category" />
                        <button type="button" wire:click="closeEntryDetails"
                                class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6">
                    @if($viewingEntry->description)
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-sm text-gray-300">{{ $viewingEntry->description }}</p>
                        </div>
                    @endif

                    <!-- Grid de metadatos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Fecha</p>
                            <p class="text-sm text-gray-200">{{ $viewingEntry->created_at?->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">IP</p>
                            <p class="text-sm text-gray-200 font-mono">{{ $viewingEntry->ip_address ?: '—' }}</p>
                        </div>
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Método</p>
                            <p class="text-sm text-gray-200">{{ $viewingEntry->request_method ?: '—' }}</p>
                        </div>
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-1">Request ID</p>
                            <p class="text-sm text-gray-200 font-mono">{{ $viewingEntry->request_id ?: '—' }}</p>
                        </div>
                    </div>

                    <!-- Sujeto / Objeto / Sesión -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Sujeto</p>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-200">{{ $viewingEntry->subject_identifier ?: '—' }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $viewingEntry->subject_type ? class_basename($viewingEntry->subject_type) : '' }}@if($viewingEntry->subject_id) #{{ $viewingEntry->subject_id }}@endif</p>
                            </div>
                        </div>
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Objeto</p>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-200">{{ $viewingEntry->object_identifier ?: '—' }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $viewingEntry->object_type ? class_basename($viewingEntry->object_type) : '' }}@if($viewingEntry->object_id) #{{ $viewingEntry->object_id }}@endif</p>
                            </div>
                        </div>
                    </div>

                    <!-- Request URL / User Agent -->
                    <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Request</p>
                        <p class="text-sm text-gray-300 font-mono break-all">{{ $viewingEntry->request_url ?: '—' }}</p>
                        @if($viewingEntry->user_agent)
                            <p class="text-xs text-gray-500 mt-2 break-all">{{ $viewingEntry->user_agent }}</p>
                        @endif
                        @if($viewingEntry->session_id)
                            <p class="text-xs text-gray-500 mt-2 font-mono">sesión: {{ $viewingEntry->session_id }}</p>
                        @endif
                    </div>

                    <!-- Cambios (old/new) -->
                    @if($viewingEntry->changed_fields)
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Campos modificados</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($viewingEntry->changed_fields as $field)
                                    <span class="px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-[11px] text-gray-400 font-mono">{{ $field }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($viewingEntry->old_values || $viewingEntry->new_values)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @if($viewingEntry->old_values)
                                <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Valores anteriores</p>
                                    <pre class="text-xs text-gray-300 font-mono overflow-x-auto max-h-64">{{ json_encode($viewingEntry->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @endif
                            @if($viewingEntry->new_values)
                                <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Valores nuevos</p>
                                    <pre class="text-xs text-gray-300 font-mono overflow-x-auto max-h-64">{{ json_encode($viewingEntry->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Metadata completa -->
                    @if($viewingEntry->metadata)
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Metadata</p>
                            <pre class="text-xs text-gray-300 font-mono overflow-x-auto max-h-64">{{ json_encode($viewingEntry->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif

                    <!-- Integridad de cadena -->
                    @if($viewingEntry->entry_hash)
                        <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Integridad (hash-chain)</p>
                            <div class="space-y-2 text-xs font-mono break-all">
                                <p class="text-gray-400"><span class="text-gray-600">entry_hash: </span>{{ $viewingEntry->entry_hash }}</p>
                                <p class="text-gray-400"><span class="text-gray-600">previous_hash: </span>{{ $viewingEntry->previous_hash ?: '— (génesis)' }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 flex items-center justify-between gap-4 px-6 py-4 bg-gray-900/95 backdrop-blur border-t border-white/10">
                    <p class="text-xs text-gray-500">Registro inmutable — solo lectura</p>
                    <button type="button" wire:click="closeEntryDetails"
                            class="px-4 py-2 rounded-lg text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>