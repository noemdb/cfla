@props([
    'name' => 'help',
    'title' => 'Ayuda',
    'subtitle' => '',
    'color' => 'indigo',
    'icon' => 'default',
])

@php
$colorClasses = [
    'indigo' => [
        'btn' => 'bg-indigo-500/15 border-indigo-500/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/30 hover:text-indigo-300',
        'badge' => 'bg-indigo-50 dark:bg-indigo-500/15 border-indigo-200 dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400',
        'headline' => 'text-indigo-600 dark:text-indigo-400',
    ],
    'emerald' => [
        'btn' => 'bg-emerald-500/15 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300',
        'badge' => 'bg-emerald-50 dark:bg-emerald-500/15 border-emerald-200 dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400',
        'headline' => 'text-emerald-600 dark:text-emerald-400',
    ],
    'amber' => [
        'btn' => 'bg-amber-500/15 border-amber-500/30 text-amber-600 dark:text-amber-400 hover:bg-amber-500/30 hover:text-amber-300',
        'badge' => 'bg-amber-50 dark:bg-amber-500/15 border-amber-200 dark:border-amber-500/30 text-amber-600 dark:text-amber-400',
        'headline' => 'text-amber-600 dark:text-amber-400',
    ],
    'sky' => [
        'btn' => 'bg-sky-500/15 border-sky-500/30 text-sky-600 dark:text-sky-400 hover:bg-sky-500/30 hover:text-sky-300',
        'badge' => 'bg-sky-50 dark:bg-sky-500/15 border-sky-200 dark:border-sky-500/30 text-sky-600 dark:text-sky-400',
        'headline' => 'text-sky-600 dark:text-sky-400',
    ],
    'violet' => [
        'btn' => 'bg-violet-500/15 border-violet-500/30 text-violet-600 dark:text-violet-400 hover:bg-violet-500/30 hover:text-violet-300',
        'badge' => 'bg-violet-50 dark:bg-violet-500/15 border-violet-200 dark:border-violet-500/30 text-violet-600 dark:text-violet-400',
        'headline' => 'text-violet-600 dark:text-violet-400',
    ],
];
$c = $colorClasses[$color] ?? $colorClasses['indigo'];
$openVar = "{$name}Open";
@endphp

<div x-data="{ {{ $openVar }}: false }" class="contents">
    {{-- Floating help button --}}
    <button @click="{{ $openVar }} = true"
            class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full {{ $c['btn'] }} hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
            title="{{ $title }}"
            x-show="!{{ $openVar }}">
        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
        </svg>
    </button>

    {{-- Backdrop --}}
    <div x-cloak
         x-show="{{ $openVar }}"
         x-transition:enter="transition-opacity duration-300"
         x-transition:leave="transition-opacity duration-200"
         @click="{{ $openVar }} = false"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"></div>

    {{-- Slideover panel --}}
    <div x-cloak
         x-show="{{ $openVar }}"
         x-transition:enter="transition-transform duration-300 ease-out"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         @keydown.escape.window="{{ $openVar }} = false"
         class="fixed top-0 right-0 z-50 h-full w-full max-w-2xl bg-white dark:bg-slate-800 border-l border-gray-200 dark:border-slate-700/50 shadow-2xl overflow-y-auto">

        {{-- Sticky header --}}
        <div class="sticky top-0 bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm border-b border-gray-200 dark:border-slate-700/50 z-10">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full {{ $c['badge'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">{!! $title !!}</h2>
                        @if($subtitle)
                            <p class="text-xs text-gray-500 dark:text-slate-400">{!! $subtitle !!}</p>
                        @endif
                    </div>
                </div>
                <button @click="{{ $openVar }} = false"
                        class="p-2 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Content slot --}}
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
