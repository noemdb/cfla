{{--
    Renderer compartido de un HTML embed LMS (diagrama Mermaid o contenido
    HTML embebido). Elimina la duplicación entre la vista de detalle del
    estudiante (activity-view) y la de impresión (lessons-print).

    Requiere que el embed ya esté normalizado (LmsPreviewService::normalizeEmbeds):
      - $embed->is_mermaid  (bool)
      - $embed->html_content (código plano o HTML)
    --}}
@props([
    'embed' => null,
    'variant' => 'detail', // 'detail' (UI Tailwind) | 'print' (hoja impresa)
])

@if($embed)

@if($variant === 'print')
    <div class="content-block">
        @if($embed->title)
            <div class="content-title">{{ $embed->title }}</div>
        @endif
        @if($embed->is_mermaid ?? false)
            <div class="mermaid-wrap">
                <div wire:ignore x-data="mermaidEmbed()"
                     data-mermaid-code="{{ app(\App\Services\Lms\LmsContentClassifier::class)->extractMermaidCode($embed->html_content) }}">
                    <div x-ref="target" class="w-full"></div>
                </div>
            </div>
        @else
            <div class="content">{!! $embed->html_content !!}</div>
        @endif
    </div>
@else
    <div class="bg-white border border-fuchsia-200 rounded-xl p-2.5 sm:p-3 html-embed-item">
        @if($embed->title)
            <h4 class="text-sm font-bold text-gray-900 mb-2">{{ $embed->title }}</h4>
        @endif
        @if($embed->is_mermaid ?? false)
            <div wire:ignore x-data="mermaidEmbed()"
                 data-mermaid-code="{{ app(\App\Services\Lms\LmsContentClassifier::class)->extractMermaidCode($embed->html_content) }}"
                 class="w-full bg-white rounded-lg p-4 overflow-x-auto border border-slate-200/60 flex flex-col mermaid-fill-height relative">
                <div x-ref="target" class="w-full min-h-0"></div>
            </div>
        @else
            <div class="text-[17px] text-gray-900 leading-7 prose prose-sm max-w-none html-embed-content">
                {!! $embed->html_content !!}
            </div>
        @endif
    </div>
@endif

@endif