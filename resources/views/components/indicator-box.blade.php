@props([
    'value',
    'label',
    'icon',
    'color' => 'emerald',
    'subtext' => '',
    'trend' => null, // null | 'up' | 'down' | 'neutral'
    'trendValue' => '',
])

@php
    $colorMap = [
        'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400', 'icon' => 'text-emerald-400'],
        'blue' => ['bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/20', 'text' => 'text-blue-400', 'icon' => 'text-blue-400'],
        'purple' => ['bg' => 'bg-purple-500/10', 'border' => 'border-purple-500/20', 'text' => 'text-purple-400', 'icon' => 'text-purple-400'],
        'amber' => ['bg' => 'bg-amber-500/10', 'border' => 'border-amber-500/20', 'text' => 'text-amber-400', 'icon' => 'text-amber-400'],
        'rose' => ['bg' => 'bg-rose-500/10', 'border' => 'border-rose-500/20', 'text' => 'text-rose-400', 'icon' => 'text-rose-400'],
        'cyan' => ['bg' => 'bg-cyan-500/10', 'border' => 'border-cyan-500/20', 'text' => 'text-cyan-400', 'icon' => 'text-cyan-400'],
        'indigo' => ['bg' => 'bg-indigo-500/10', 'border' => 'border-indigo-500/20', 'text' => 'text-indigo-400', 'icon' => 'text-indigo-400'],
        'pink' => ['bg' => 'bg-pink-500/10', 'border' => 'border-pink-500/20', 'text' => 'text-pink-400', 'icon' => 'text-pink-400'],
        'teal' => ['bg' => 'bg-teal-500/10', 'border' => 'border-teal-500/20', 'text' => 'text-teal-400', 'icon' => 'text-teal-400'],
        'violet' => ['bg' => 'bg-violet-500/10', 'border' => 'border-violet-500/20', 'text' => 'text-violet-400', 'icon' => 'text-violet-400'],
    ];
    $c = $colorMap[$color] ?? $colorMap['emerald'];

    $trendIcon = match($trend) {
        'up' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
        'down' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>',
        default => '',
    };
    $trendColor = match($trend) {
        'up' => 'text-emerald-400',
        'down' => 'text-red-400',
        default => 'text-gray-500',
    };
@endphp

<div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-lg transition-all duration-300 hover:border-{{ $color }}-500/30 hover:shadow-lg hover:shadow-{{ $color }}-500/5">
    <div class="flex items-start justify-between mb-2">
        <div class="w-10 h-10 {{ $c['bg'] }} rounded-lg flex items-center justify-center {{ $c['icon'] }}">
            {!! $icon !!}
        </div>
        @if($trend)
            <div class="flex items-center gap-1 {{ $trendColor }} text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $trendIcon !!}</svg>
                {{ $trendValue }}
            </div>
        @endif
    </div>
    <p class="text-lg font-bold text-white mb-1">{{ $value }}</p>
    <p class="text-[11px] font-medium {{ $c['text'] }} uppercase tracking-wider">{{ $label }}</p>
    @if($subtext)
        <p class="text-[10px] text-gray-500 mt-1">{{ $subtext }}</p>
    @endif
</div>
