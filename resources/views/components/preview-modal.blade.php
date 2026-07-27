@props([
    'title' => '',
    'subtitle' => '',
    'maxWidth' => '2xl',
    'blur' => '2xl',
    'hideClose' => false,
])

<x-modal {{ $attributes->merge(['blur' => $blur, 'max-width' => $maxWidth, 'title' => '']) }}>
    <div class="-m-6 border border-white/10 rounded-2xl overflow-hidden shadow-2xl shadow-black/20">

        {{-- Header: title, subtitle, badges slot, close button --}}
        @if($title || $subtitle || isset($header))
        <div class="bg-gradient-to-r from-gray-800/80 to-gray-900 border-b border-white/5 px-6 py-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    @if($title)
                    <h2 class="text-lg font-extrabold text-white truncate" title="{{ $title }}">{{ $title }}</h2>
                    @endif
                    @if($subtitle)
                    <p class="text-sm text-gray-400 mt-0.5">{{ $subtitle }}</p>
                    @endif
                    {{ $header ?? '' }}
                </div>
                @unless($hideClose)
                <button type="button" x-on:click="show = false"
                    class="p-1.5 bg-white/5 hover:bg-red-500/15 rounded-lg text-gray-500 hover:text-red-400 transition-all shrink-0"
                    title="Cerrar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endunless
            </div>
        </div>
        @endif

        {{-- Body --}}
        <div class="px-6 py-5 space-y-5">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if(isset($footer))
        <div class="border-t border-white/5 bg-gray-900/50 px-6 py-3 flex items-center justify-between">
            {{ $footer }}
        </div>
        @endif

    </div>
</x-modal>
