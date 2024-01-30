<label {{ $attributes->class([
        'block text-sm',
        'text-negative-600'  => $hasError,
        'opacity-60'         => $attributes->get('disabled'),
        'text-gray-700 dark:text-gray-700' => !$hasError,
        'opacity-50' => $attributes->get('opacity'),
        'font-medium' => $attributes->get('bold'),
    ]) }}>
    {{ $label ?? $slot }}
</label>
