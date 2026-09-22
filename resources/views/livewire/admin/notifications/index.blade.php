<div class="fade-in">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Notificaciones</h1>
            <p class="text-emerald-400 font-medium text-sm">Histórico completo de la tabla <span class="font-mono text-xs bg-white/5 px-1.5 py-0.5 rounded">notifications</span> — todas las cuentas.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-xs font-semibold text-gray-400">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                {{ number_format($notifications->total()) }} registros
            </span>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Buscar</label>
                <input type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Mensaje, clase, usuario, email…"
                       class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Clase</label>
                <select wire:model.live="filterType" class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                    <option value="">Todas</option>
                    @foreach($meta['types'] as $t)
                        <option value="{{ $t }}">{{ class_basename($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Tipo dato</label>
                <select wire:model.live="filterDataType" class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                    <option value="">Todos</option>
                    @foreach($meta['dataTypes'] as $dt)
                        <option value="{{ $dt }}">{{ $dt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Estado</label>
                <select wire:model.live="filterRead" class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                    <option value="">Todos</option>
                    <option value="unread">No leídas</option>
                    <option value="read">Leídas</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Usuario</label>
                <input type="search" wire:model.live.debounce.300ms="filterUser"
                       placeholder="username, email o ID"
                       class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:border-emerald-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 mt-3">
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
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Por página</label>
                <select wire:model.live="paginate" class="w-full bg-gray-800/60 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500 focus:outline-none">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" wire:click="resetFilters"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-400 hover:text-white hover:bg-white/5 border border-white/10 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5 bg-gray-800/30">
                        <th class="px-3 py-3 text-left">
                            <button type="button" wire:click="sortBy('created_at')" class="flex items-center gap-1 text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white">
                                Fecha
                                @if($sortField === 'created_at')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Destinatario</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Clase</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Tipo dato</th>
                        <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-400">Mensaje</th>
                        <th class="px-3 py-3 text-left">
                            <button type="button" wire:click="sortBy('read_at')" class="flex items-center gap-1 text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-white">
                                Estado
                                @if($sortField === 'read_at')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-widest text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        @php($data = (array) $notification->data)
                        @php($notifiable = $notification->notifiable)
                        @php($dataType = $data['type'] ?? $data['event_type'] ?? '—')
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-3 py-3 text-xs text-gray-300 whitespace-nowrap">
                                <div>{{ $notification->created_at?->format('d/m/Y H:i:s') }}</div>
                                <div class="text-[11px] text-gray-500">{{ $notification->created_at?->diffForHumans() }}</div>
                            </td>
                            <td class="px-3 py-3">
                                @if($notifiable)
                                    <div class="text-sm text-gray-200 font-medium">{{ $notifiable->username ?? 'ID '.$notifiable->id }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-[180px]">{{ $notifiable->email ?? '' }}</div>
                                    @if($notifiable->profile ?? null)
                                        <div class="text-[11px] text-gray-600">{{ trim(($notifiable->profile->firstname ?? '').' '.($notifiable->profile->lastname ?? '')) }}</div>
                                    @endif
                                    <div class="text-[11px] font-mono text-gray-600">#{{ $notifiable->id }} · {{ class_basename($notification->notifiable_type) }}</div>
                                @else
                                    <span class="text-xs text-gray-600">—</span>
                                    <div class="text-[11px] font-mono text-gray-600">#{{ $notification->notifiable_id }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-[11px] font-mono text-gray-400" title="{{ $notification->type }}">
                                    {{ \Illuminate\Support\Str::afterLast($notification->type, '\\') }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold
                                    @if($dataType === '—') bg-gray-800 text-gray-500 border border-white/5
                                    @elseif(str_contains($dataType, 'approved')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @elseif(str_contains($dataType, 'scheduled') || str_contains($dataType, 'pending')) bg-amber-500/10 text-amber-400 border border-amber-500/20
                                    @elseif(str_contains($dataType, 'stale') || str_contains($dataType, 'backlog')) bg-red-500/10 text-red-400 border border-red-500/20
                                    @else bg-sky-500/10 text-sky-400 border border-sky-500/20 @endif
                                ">
                                    {{ $dataType }}
                                </span>
                            </td>
                            <td class="px-3 py-3 max-w-[320px]">
                                <div class="text-sm text-gray-200 line-clamp-2" title="{{ $data['message'] ?? '' }}">{{ $data['message'] ?? '—' }}</div>
                                @if(!empty($data['url']))
                                    <div class="text-[11px] text-emerald-400/70 truncate max-w-[280px]">{{ $data['url'] }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @if(is_null($notification->read_at))
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> No leída
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">
                                        Leída
                                        <span class="text-[10px] text-gray-500">{{ $notification->read_at->format('d/m H:i') }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" wire:click="openDetails('{{ $notification->id }}')"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-300 hover:bg-white/5 border border-transparent hover:border-white/10 transition-colors"
                                            title="Ver detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    @if(is_null($notification->read_at))
                                        <button type="button" wire:click="markAsRead('{{ $notification->id }}')"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5" title="Marcar leída">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    @else
                                        <button type="button" wire:click="markAsUnread('{{ $notification->id }}')"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/5" title="Marcar no leída">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        </button>
                                    @endif
                                    <button type="button" wire:click="deleteNotification('{{ $notification->id }}')" wire:confirm="¿Eliminar esta notificación?"
                                            class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-14 text-center">
                                <svg class="w-10 h-10 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="text-sm text-gray-400">Sin notificaciones con los filtros actuales.</p>
                                <p class="text-xs text-gray-600 mt-1">Prueba ajustando la búsqueda o limpiando filtros.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-wrapper :paginator="$notifications" />
    </div>

    {{-- Modal detalle --}}
    @if($showDetails && $viewingNotification)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="closeDetails">
            <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto bg-gray-900 border border-white/10 rounded-xl shadow-2xl">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 px-6 py-4 bg-gray-900/95 backdrop-blur border-b border-white/10">
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-white truncate">Notificación {{ $viewingNotification->id }}</h3>
                        <p class="text-xs font-mono text-gray-500 truncate">{{ $viewingNotification->type }}</p>
                    </div>
                    <button type="button" wire:click="closeDetails" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="bg-white/5 border border-white/5 rounded-lg p-3">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Destinatario</div>
                            <div class="text-sm text-white mt-1">
                                @if($viewingNotification->notifiable)
                                    {{ $viewingNotification->notifiable->username ?? 'ID '.$viewingNotification->notifiable_id }}<br>
                                    <span class="text-xs text-gray-400">{{ $viewingNotification->notifiable->email ?? '' }}</span>
                                @else
                                    ID {{ $viewingNotification->notifiable_id }} ({{ class_basename($viewingNotification->notifiable_type) }})
                                @endif
                            </div>
                        </div>
                        <div class="bg-white/5 border border-white/5 rounded-lg p-3">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Fechas</div>
                            <div class="text-xs text-gray-300 mt-1">
                                Creada: {{ $viewingNotification->created_at?->format('d/m/Y H:i:s') }}<br>
                                Leída: {{ $viewingNotification->read_at?->format('d/m/Y H:i:s') ?? '— no leída' }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-800/40 border border-white/5 rounded-lg p-4">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Payload (data)</div>
                        <pre class="text-xs text-emerald-300 font-mono overflow-x-auto max-h-[45vh] whitespace-pre-wrap break-all">{{ json_encode($viewingNotification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
                <div class="sticky bottom-0 flex justify-end gap-2 px-6 py-4 bg-gray-900/95 backdrop-blur border-t border-white/10">
                    <button type="button" wire:click="closeDetails" class="px-4 py-2 rounded-lg text-sm font-semibold bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
