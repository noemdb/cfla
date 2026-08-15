@props(['value' => '', 'kind' => 'severity'])

@php
    $severityColors = [
        'debug' => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
        'info' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
        'warning' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
        'critical' => 'bg-red-500/10 text-red-400 border-red-500/20',
        'alert' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
    ];
    $categoryColors = [
        'authentication' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
        'user_action' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        'system' => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
        'security' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
        'error' => 'bg-red-500/10 text-red-400 border-red-500/20',
    ];
    $labels = [
        'debug' => 'Debug', 'info' => 'Info', 'warning' => 'Warning',
        'critical' => 'Critical', 'alert' => 'Alert',
        'authentication' => 'Autenticación', 'user_action' => 'Usuario',
        'system' => 'Sistema', 'security' => 'Seguridad', 'error' => 'Error',
    ];
    $map = $kind === 'category' ? $categoryColors : $severityColors;
    $label = $labels[$value] ?? $value;
    $class = $map[$value] ?? 'bg-gray-500/10 text-gray-400 border-gray-500/20';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $class }}">
    {{ $label }}
</span>