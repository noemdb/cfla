@props(['item' => [], 'mobile' => false])

@if (!empty($item['disabled']))
    <span class="block px-3.5 py-2 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
        {{ $item['label'] }}
    </span>
@else
    @php
        $color = $item['icon_color'] ?? 'emerald';
        $base = !empty($item['active'])
            ? ($mobile ? 'bg-emerald-500/5 text-emerald-400' : 'text-emerald-400 bg-emerald-500/5')
            : 'text-gray-300 hover:text-emerald-300 hover:bg-white/5';
        $layout = $mobile
            ? 'flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg transition-colors'
            : 'flex items-center gap-2.5 px-3.5 py-2 text-sm transition-colors';
    @endphp

    <a href="{{ $item['href'] ?? '#' }}"
       class="{{ $layout }} {{ $base }}"
       @if (!empty($item['active'])) aria-current="page" @endif>
        <x-menu.icon :path="$item['icon'] ?? null" :color="$color" />
        {{ $item['label'] }}
        @if (!empty($item['badge']))
            @livewire($item['badge'])
        @endif
    </a>
@endif