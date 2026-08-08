{{--
Info Card component for displaying labeled information
Parameters:
- $label (string): Label for the field (e.g., "Nombre Completo")
- $value (string|array|null): Value to display; if array, will be rendered as list items
- $badge (bool): If true, render value as a badge (useful for statuses)
- $badgeColor (string): Tailwind color for badge when $badge is true (default: gray)
- $icon (string): SVG icon markup or component class prefix (optional)
- $iconColor (string): Tailwind color for icon (default: inherits from label color)
- $description (string|null): Optional helper text below the value
- $class (string): Additional classes for the container
- $labelClass (string): Additional classes for label
- $valueClass (string): Additional classes for value
--}}
@props([
    'label' => '',
    'value' => null,
    'badge' => false,
    'badgeColor' => 'gray',
    'icon' => null,
    'iconColor' => 'gray',
    'description' => null,
    'class' => '',
    'labelClass' => '',
    'valueClass' => '',
])
<div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{$class}}">
    <div class="flex items-center flex-wrap gap-3 mb-2">
        @if($icon)
            <div class="w-8 h-8 flex items-center justify-center bg-{{$iconColor ?? 'gray'}}-500/10 text-{{$iconColor ?? 'gray'}}-500 rounded-lg">
                {!! $icon !!}
            </div>
        @endif
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-500 {{$labelClass}}">{{$label}}</p>
            @if(is_array($value))
                <div class="space-y-1 text-sm font-medium text-gray-900 dark:text-white">
                    @foreach($value as $item)
                        <p class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                            <span>{{ $item }}</span>
                        </p>
                    @endforeach
                </div>
            @elseif($badge)
                <p class="flex items-center gap-2 mt-0.5">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{$badgeColor ?? 'gray'}}-100 text-{{$badgeColor ?? 'gray'}}-800">
                        {{$value}}
                    </span>
                </p>
            @else
                <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5 {{$valueClass}}">{{$value ?? '—'}}</p>
            @endif
        </div>
    </div>
    @if($description)
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{$description}}</p>
    @endif
</div>