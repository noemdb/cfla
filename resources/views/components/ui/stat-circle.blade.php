{{--
Stat Circle component for visualizing a percentage or count
Parameters:
- $percentage (int|float): Value from 0 to 100 representing progress
- $label (string): Label underneath the circle (e.g., "Actividades")
- $value (string|int|null): Optional numeric value to display inside the circle (overrides percentage)
- $size (string): Tailwind size for the wrapper (default: w-16 h-16)
- $color (string): Tailwind color for the progress ring (default: emerald)
- $bgColor (string): Tailwind color for the background ring (default: gray)
- $class (string): Additional classes for the container
--}}
@props([
    'percentage' => 0,
    'label' => '',
    'value' => null,
    'size' => null,
    'color' => 'emerald',
    'bgColor' => 'gray',
    'class' => '',
])
<div class="text-center {{$class}}">
    <div class="relative {{$size ?? 'w-16 h-16'}}">
        <svg class="w-full h-full -rotate-90" viewBox="0 0 40 40" aria-hidden="true">
            <circle cx="20" cy="20" r="18" fill="none" stroke-width="3.5" class="stroke-{{$bgColor}}-200 dark:stroke-{{$bgColor}}-700"></circle>
            @php
                $displayValue = $value ?? $percentage;
                $pct = min(max((float) $displayValue, 0), 100);
                $circumference = 113.1; // 2*pi*r ~= 113.097
            @endphp
            <circle
                cx="20"
                cy="20"
                r="18"
                fill="none"
                stroke-width="3.5"
                class="stroke-{{$color}}-500 transition-all duration-500"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $circumference - $pct * 1.131 }}"
            ></circle>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            @if(isset($value))
                <div class="text-2xl font-bold text-{{$color ?? 'emerald'}}-600 dark:text-{{$color ?? 'emerald'}}-400 tabular-nums">
                    {{$value}}
                </div>
            @else
                <div class="text-2xl font-bold text-{{$color ?? 'emerald'}}-600 dark:text-{{$color ?? 'emerald'}}-400 tabular-nums">
                    {{ number_format($percentage, 0) }}<span class="text-xs font-normal ml-0.5">%</span>
                </div>
            @endif
        </div>
    </div>
    <p class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400">{{$label}}</p>
</div>