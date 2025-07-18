@props([
    'value',
    'label',
    'icon',
    'color' => 'text-primary-500',
    'description' => '',
    'iconSize' => 'md'
])

<div class="p-4 bg-white rounded-lg shadow">
    <div class="flex items-start">
        <div class="p-2 mr-4 rounded-full {{ $color }} bg-opacity-10">
            <x-icon :name="$icon" class="w-6 h-6" />
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $value }}</p>
            @if($description)
                <p class="mt-1 text-xs text-gray-400">{{ $description }}</p>
            @endif
        </div>
    </div>
</div>
