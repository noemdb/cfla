<div class="w-full mx-auto py-3 sm:py-8 px-2 sm:px-4 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Notificaciones</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                Histórico de las notificaciones de tu cuenta.
            </p>
        </div>

        @if($unreadCount > 0)
            <button type="button" wire:click="markAllAsRead" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Marcar todas como leídas
            </button>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-white/5">
        @foreach(\App\Livewire\App\Notifications\NotificationsIndex::TABS as $key => $label)
            <button type="button" wire:click="setTab('{{ $key }}')"
                class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px transition-colors
                       {{ $tab === $key
                           ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400'
                           : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Lista --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 rounded-xl overflow-hidden shadow-sm">
        @forelse($notifications as $notification)
            @php($data = (array) $notification->data)
            @php($url = app(\App\Services\NotificationTargetResolver::class)->resolveFor(auth()->user(), $data))
            <a href="{{ $url }}"
               wire:click="markAsRead('{{ $notification->id }}')"
               class="flex items-start gap-4 px-4 sm:px-6 py-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors
                      border-b border-gray-100 dark:border-white/5 last:border-b-0
                      {{ is_null($notification->read_at) ? 'bg-emerald-50/40 dark:bg-emerald-500/5' : '' }}">
                <span class="mt-1.5 shrink-0 w-2.5 h-2.5 rounded-full
                             {{ is_null($notification->read_at) ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-slate-600' }}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 dark:text-slate-200 {{ is_null($notification->read_at) ? 'font-semibold' : '' }}">
                        {{ $data['message'] ?? 'Nueva notificación' }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                <svg class="w-4 h-4 text-gray-300 dark:text-slate-600 shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        @empty
            <div class="px-6 py-16 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="text-sm text-gray-500 dark:text-slate-400">No tienes notificaciones{{ $tab !== 'all' ? ' ' . strtolower($tab === 'unread' ? 'sin leer' : 'leídas') : '' }}.</p>
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-gray-100 dark:border-white/5">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>