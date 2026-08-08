@props([
    'type' => 'prose',   // 'prose' | 'concept' | 'list' | 'quote' | 'question' | 'activity'
    'body' => null,      // HTML del contenido (ya convertido desde Markdown)
])

@php
    $variant = match ($type) {
        'concept' => [
            'wrapper'    => 'bg-white border-l-4 border-emerald-400 rounded-r-xl p-3 sm:p-4',
            'icon'       => '💡',
            'label'      => 'Concepto',
            'labelClass' => 'text-emerald-700',
            'textClass'  => 'text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content',
        ],
        'list' => [
            'wrapper'    => 'bg-white rounded-xl p-3 sm:p-4 border border-gray-200',
            'icon'       => '📋',
            'label'      => 'Lista',
            'labelClass' => 'text-gray-500',
            'textClass'  => 'text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none prose-ul:list-disc prose-ol:list-decimal lms-content',
        ],
        'quote' => [
            'wrapper'    => 'bg-white border-l-4 border-amber-500 rounded-r-xl p-3 sm:p-4',
            'icon'       => null,
            'label'      => null,
            'labelClass' => '',
            'textClass'  => 'text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content',
        ],
        'question' => [
            'wrapper'    => 'bg-white border border-sky-200 rounded-xl p-3 sm:p-4',
            'icon'       => '💭',
            'label'      => 'Pregunta',
            'labelClass' => 'text-sky-700',
            'textClass'  => 'text-[17px] text-sky-900 leading-7 prose prose-sm max-w-none lms-content',
        ],
        'activity' => [
            'wrapper'    => 'bg-white border-2 border-dashed border-amber-300/60 rounded-xl p-3 sm:p-4',
            'icon'       => '✏️',
            'label'      => 'Actividad',
            'labelClass' => 'text-amber-700',
            'textClass'  => 'text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content',
        ],
        default => [
            'wrapper'    => 'bg-white rounded-xl p-3 sm:p-4 border border-gray-200',
            'icon'       => null,
            'label'      => null,
            'labelClass' => '',
            'textClass'  => 'text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none lms-content',
        ],
    };
@endphp

<div class="{{ $variant['wrapper'] }}">
    @if($type === 'quote')
        <div class="flex items-start gap-3">
            <span class="text-2xl leading-none text-amber-300/70 font-serif shrink-0">"</span>
            <x-lms.math-text :content="$body" class="{{ $variant['textClass'] }}" />
        </div>
    @else
        @if($variant['icon'])
            <div class="flex items-center gap-2 mb-2">
                <span class="text-base leading-none">{{ $variant['icon'] }}</span>
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $variant['labelClass'] }}">{{ $variant['label'] }}</span>
            </div>
        @endif
        <x-lms.math-text :content="$body" class="{{ $variant['textClass'] }}" />
    @endif
</div>