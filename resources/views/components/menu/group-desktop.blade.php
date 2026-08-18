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
         class="absolute left-0 mt-1 w-56 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-2 z-50">
        @foreach ($group['items'] ?? [] as $item)
            <x-menu.item :item="$item" />
        @endforeach
    </div>
</div>