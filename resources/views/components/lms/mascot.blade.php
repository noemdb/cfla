{{-- Mascota del LMS del estudiante (decorativa, aria-hidden). --}}
{{-- Uso: <x-lms-mascot :variant="'greet'|'celebrate'|'idle'" :size="'lg'|'sm'" :emphasis="bool" /> --}}
{{-- Variantes: greet = saluda (hero), celebrate = celebra (overlay C3), idle = buscando (empty state C4). --}}
{{-- emphasis = ojos de estrella dorados "oro puro" (estudiantes 5–8 años, C4). --}}
@props(['variant' => 'greet', 'size' => 'lg', 'emphasis' => false])

@php
    $sizes = [
        'lg' => 'w-20 h-20',
        'sm' => 'w-14 h-14',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['lg'];
    // Todas las variantes flotan: greet (hero), celebrate (C3) e idle ("anima
    // en el vacío", C4). Se respeta prefers-reduced-motion en la clase CSS.
    $float = true;
@endphp

<svg class="{{ $sizeClass }} shrink-0 {{ $float ? 'animate-mascot-float' : '' }} motion-reduce:animate-none"
     viewBox="0 0 100 100" fill="none" aria-hidden="true">
    <defs>
        <linearGradient id="lms-mascot-body" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#34d399"/>
            <stop offset="100%" stop-color="#059669"/>
        </linearGradient>
    </defs>

    {{-- Cuerpo redondo --}}
    <circle cx="50" cy="58" r="34" fill="url(#lms-mascot-body)"/>

    {{-- Mejillas --}}
    <circle cx="30" cy="63" r="5" fill="#a7f3d0" opacity="0.85"/>
    <circle cx="70" cy="63" r="5" fill="#a7f3d0" opacity="0.85"/>

    {{-- Ojos: punto (default) o estrella dorada "oro puro" (emphasis, 5–8 años, C4) --}}
    @if($emphasis)
        <path d="M 38 44.5 L 39.8 48.2 L 43.5 50 L 39.8 51.8 L 38 55.5 L 36.2 51.8 L 32.5 50 L 36.2 48.2 Z" fill="#fbbf24" stroke="#065f46" stroke-width="1.5"/>
        <path d="M 62 44.5 L 63.8 48.2 L 67.5 50 L 63.8 51.8 L 62 55.5 L 60.2 51.8 L 56.5 50 L 60.2 48.2 Z" fill="#fbbf24" stroke="#065f46" stroke-width="1.5"/>
    @else
        <circle cx="38" cy="50" r="4" fill="#064e3b"/>
        <circle cx="62" cy="50" r="4" fill="#064e3b"/>
    @endif

    {{-- Boca según variante --}}
    @if($variant === 'celebrate')
        <path d="M41 60 Q50 74 59 60 Z" fill="#064e3b"/>
    @elseif($variant === 'idle')
        <path d="M43 63 H57" stroke="#064e3b" stroke-width="3.5" stroke-linecap="round"/>
    @else
        <path d="M42 59 Q50 68 58 59" stroke="#064e3b" stroke-width="3.5" stroke-linecap="round"/>
    @endif

    {{-- Brazos según variante --}}
    @if($variant === 'greet')
        <path d="M26 66 Q18 76 20 84" stroke="#34d399" stroke-width="8" stroke-linecap="round"/>
        <circle cx="20" cy="85" r="5.5" fill="#34d399"/>
        <path d="M72 62 Q82 50 80 40" stroke="#34d399" stroke-width="8" stroke-linecap="round"/>
        <circle cx="80" cy="39" r="5.5" fill="#34d399"/>
    @elseif($variant === 'celebrate')
        <path d="M28 62 Q20 48 22 38" stroke="#34d399" stroke-width="8" stroke-linecap="round"/>
        <circle cx="22" cy="37" r="5.5" fill="#34d399"/>
        <path d="M72 62 Q80 48 78 38" stroke="#34d399" stroke-width="8" stroke-linecap="round"/>
        <circle cx="78" cy="37" r="5.5" fill="#34d399"/>
        {{-- Estrella de celebración arriba --}}
        <path d="M50 7 L52.4 12.6 L58 15 L52.4 17.4 L50 23 L47.6 17.4 L42 15 L47.6 12.6 Z" fill="#fbbf24"/>
    @else
        {{-- Sentado: brazos reposando a los lados --}}
        <path d="M24 70 Q16 78 18 84" stroke="#34d399" stroke-width="8" stroke-linecap="round"/>
        <circle cx="18" cy="85" r="5.5" fill="#34d399"/>
        <path d="M76 70 Q84 78 82 84" stroke="#34d399" stroke-width="8" stroke-linecap="round"/>
        <circle cx="82" cy="85" r="5.5" fill="#34d399"/>
        {{-- Lupa de búsqueda --}}
        <circle cx="83" cy="22" r="8" stroke="#064e3b" stroke-width="3.5" fill="none"/>
        <path d="M89 28 L96 35" stroke="#064e3b" stroke-width="3.5" stroke-linecap="round"/>
    @endif
</svg>
