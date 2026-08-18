@props(['group' => []])

@php
    $color = $group['color'] ?? 'emerald';
    $id = preg_replace('/[^a-z0-9]+/', '-', strtolower($group['label'] ?? 'menu')) . '-submenu';
@endphp

<div x-data="{ open: false }">
    <button type="button"
            @click="open = !open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="{{ $id }}"
            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-white/5 rounded-lg transition-colors focus-visible:ring-2 focus-visible:ring-{{ $color }}-400/40">
        <span class="flex items-center gap-2">
            <x-menu.icon :path="$group['icon'] ?? null" :color="$color" />
            {{ $group['label'] }}
        </span>
        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" x-cloak id="{{ $id }}" class="ml-4 mt-1 space-y-0.5 border-l border-gray-200 dark:border-white/10 pl-3">
        @foreach ($group['items'] ?? [] as $item)
            <x-menu.item :item="$item" :mobile="true" />
        @endforeach
    </div>
</div>