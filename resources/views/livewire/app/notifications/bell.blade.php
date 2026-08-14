<div x-data="{ open: false }" wire:poll.30s="reconcile" class="relative">
    {{-- Botón campana --}}
    <button type="button"
            @click="open = !open; $wire.reconcile()"
            @keydown.escape.window="open = false"
            class="relative p-2 text-gray-500 hover:text-gray-900 bg-gray-100/50 hover:bg-emerald-100 dark:text-gray-400 dark:hover:text-white dark:bg-white/5 dark:hover:bg-emerald-500/20 rounded-lg border border-gray-200 dark:border-white/5 transition-all duration-300"
            :aria-expanded="open"
            aria-label="Notificaciones">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-white dark:border-gray-900">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak x-transition
         class="absolute right-0 mt-2 w-80 sm:w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-xl shadow-gray-200/50 dark:shadow-black/50 overflow-hidden z-[60]">
        {{-- Cabecera --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-white/5">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                Notificaciones
                @if($unreadCount > 0)
                    <span class="ml-1 text-xs font-semibold text-red-500">({{ $unreadCount }} sin leer)</span>
                @endif
            </h3>
            @if($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" wire:loading.attr="disabled"
                    class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:underline disabled:opacity-50">
                    Marcar todas como leídas
                </button>
            @endif
        </div>

        {{-- Lista --}}
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $item)
                <a href="{{ $item['url'] }}"
                   @click="open = false; $wire.markAsRead('{{ $item['id'] }}')"
                   class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-white/5 last:border-b-0 transition-colors
                          {{ $item['read_at'] ? 'hover:bg-gray-50 dark:hover:bg-white/5' : 'bg-emerald-50/40 dark:bg-emerald-500/5 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' }}">
                    <span class="mt-1.5 shrink-0 w-2 h-2 rounded-full
                                 {{ $item['read_at'] ? 'bg-gray-300 dark:bg-slate-600' : 'bg-emerald-500' }}"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 dark:text-slate-200 leading-snug {{ $item['read_at'] ? '' : 'font-semibold' }}">
                            {{ $item['message'] }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">
                            {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="text-sm text-gray-500 dark:text-slate-400">No tienes notificaciones.</p>
                </div>
            @endforelse
        </div>

        {{-- Ver todas --}}
        <div class="p-3 border-t border-gray-100 dark:border-white/5">
            <a href="{{ route('app.notifications.index') }}" @click="open = false"
               class="block w-full text-center text-sm font-semibold text-emerald-600 dark:text-emerald-400 py-2 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors">
                Ver todas las notificaciones
            </a>
        </div>
    </div>
</div>