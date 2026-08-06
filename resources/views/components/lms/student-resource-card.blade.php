@props([
    'resource' => null,
    'activity' => null,
])

@php
    $isImage = $resource && $resource->media && str_starts_with($resource->media->mime_type ?? '', 'image/');
    $publicUrl = $resource->media->public_url ?? '';
    $displayName = $resource->display_name ?? 'Recurso';
@endphp

@if($isImage)
    <div x-data="{ loaded: false, failed: false }" class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
        {{-- Loading skeleton --}}
        <div x-show="!loaded && !failed"
             class="flex items-center justify-center h-40 sm:h-48 bg-gray-100 animate-pulse">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        {{-- Error --}}
        <div x-show="failed" x-cloak
             class="flex flex-col items-center justify-center h-40 sm:h-48 bg-gray-100 text-gray-400">
            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-xs">No se pudo cargar la imagen</p>
        </div>
        {{-- Image --}}
        <img src="{{ $publicUrl }}"
             alt="{{ $displayName }}"
             x-on:load="loaded = true"
             x-on:error="loaded = true; failed = true"
             x-show="loaded"
             x-bind:class="failed ? 'hidden' : ''"
             class="w-full h-40 sm:h-48 object-cover bg-gray-50"
             loading="lazy">
        <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 bg-white border-t border-gray-100">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $displayName }}</p>
                @if($resource->description)
                    <p class="text-xs text-gray-600 truncate">{{ $resource->description }}</p>
                @endif
            </div>
            @if($activity && $activity->lmsPublication?->allow_downloads)
                <a href="{{ route('student.lms.resource.download', $resource) }}"
                   class="shrink-0 ml-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg shadow-sm transition-colors min-h-[36px]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar
                </a>
            @endif
        </div>
    </div>
@else
    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-200">
        <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-900 truncate">{{ $displayName }}</p>
            @if($resource->description)
                <p class="text-xs text-gray-600 truncate">{{ $resource->description }}</p>
            @endif
        </div>
        @if($activity && $activity->lmsPublication?->allow_downloads)
            <a href="{{ route('student.lms.resource.download', $resource) }}"
               class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg shadow-sm transition-colors min-h-[36px]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar
            </a>
        @endif
    </div>
@endif
