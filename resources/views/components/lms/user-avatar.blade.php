@props([
    'user' => null,
    'size' => 'md',
    'ring' => 'ring-2 ring-emerald-500/30',
    'fallback' => 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300',
    'class' => '',
])

@php
    $sizes = [
        'xs' => 'w-6 h-6 text-[9px]',
        'sm' => 'w-7 h-7 text-[10px]',
        'md' => 'w-8 h-8 text-xs',
        'lg' => 'w-10 h-10 text-sm',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $urlImg = $user?->profile?->url_img ?? null;
    if ($urlImg !== null && ($urlImg === '' || str_contains($urlImg, 'user_default') || str_contains($urlImg, 'default_user_admin'))) {
        $urlImg = null;
    }

    $name = $user?->profile?->fullname ?? $user?->name ?? 'usuario';
    $initial = mb_strtoupper(mb_substr(trim((string) ($user?->profile?->firstname ?? $user?->name ?? '?')), 0, 1)) ?: '?';
@endphp

@if($urlImg)
    <img src="{{ $urlImg }}" alt="{{ $name }}"
         class="{{ $sizeClass }} {{ $ring }} rounded-full object-cover shrink-0 {{ $class }}">
@else
    <div class="{{ $sizeClass }} {{ $ring }} rounded-full flex items-center justify-center shrink-0 font-bold {{ $fallback }} {{ $class }}">
        <span>{{ $initial }}</span>
    </div>
@endif