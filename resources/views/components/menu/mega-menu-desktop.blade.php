@props(['group' => []])

@php
    $color = $group['color'] ?? 'emerald';
    $btnActiveClass = !empty($group['active'])
        ? "bg-{$color}-500/10 text-{$color}-400"
        : "text-gray-400 hover:text-{$color}-300 hover:bg-white/5";
@endphp

<div x-data="{ open: false }" class="relative">
    <button type="button"
            @click="open = !open"
            @click.outside="open = false"
            @keydown.escape.window="open = false"
            :aria-expanded="open ? 'true' : 'false'"
            class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ $btnActiveClass }}">
        <x-menu.icon :path="$group['icon'] ?? null" :color="$color" />
        {{ $group['label'] }}
        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute left-1/2 -translate-x-1/2 mt-1 w-max max-w-[calc(100vw-2rem)] bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-5 z-50 overflow-hidden">

        @if (!empty($group['dashboard_href']))
            <div class="mb-3 pb-2 border-b border-white/5">
                <a href="{{ $group['dashboard_href'] }}"
                   class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-{{ $color }}-300 hover:bg-white/5 rounded-lg transition-colors {{ !empty($group['dashboard_active']) ? "text-{$color}-400 bg-{$color}-500/5" : '' }}"
                   @if (!empty($group['dashboard_active'])) aria-current="page" @endif>
                    <x-menu.icon path="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" :color="$color" />
                    Dashboard
                </a>
            </div>
        @endif

        <div class="flex gap-x-6">
            @foreach ($group['columns'] ?? [] as $col)
                <div class="min-w-max space-y-0.5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-{{ $color }}-400/60 px-3 py-1.5">{{ $col['title'] }}</div>
                    @foreach ($col['items'] ?? [] as $item)
                        <x-menu.item :item="$item" />
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>