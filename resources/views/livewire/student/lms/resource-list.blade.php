<div class="max-w-6xl mx-auto py-8 px-4 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Recursos Compartidos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Material descargable de tus actividades
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Buscar recurso o actividad…"
               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm"/>
        <select wire:model.live="lapsoId"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm">
            <option value="">Todos los lapsos</option>
            @foreach($lapsos as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Grid de recursos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($resources as $resource)
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-3 hover:border-emerald-500/30 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $resource->display_name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $resource->activity?->topic ?? '—' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-[10px] text-gray-400">
                        {{ $resource->activity?->pevaluacion?->pensum?->asignatura?->name ?? '' }}
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click="preview({{ $resource->id }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-medium text-gray-400 hover:text-sky-400 bg-transparent hover:bg-sky-500/10 border border-transparent hover:border-sky-500/20 transition-all"
                                title="Vista previa">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Vista previa
                        </button>
                        <a href="{{ route('student.lms.resource.download', $resource) }}"
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-[10px] font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <p class="text-gray-500 font-medium">No hay recursos disponibles</p>
                <p class="text-xs text-gray-400 mt-1">Los recursos aparecerán cuando los profesores los compartan.</p>
            </div>
        @endforelse
    </div>

    @if($resources->hasPages())
        <div class="pt-4">{{ $resources->links('vendor.livewire.custom-tailwind') }}</div>
    @endif

    {{-- ═══ PREVIEW MODAL ═══ --}}
    @if($showPreviewModal && $previewResource)
        @php
            $r = $previewResource;
            $media = $r['media'] ?? [];
            $mime = $media['mime_type'] ?? '';
            $isImage = str_starts_with($mime, 'image/');
            $isPdf = $mime === 'application/pdf';
            $isVideo = str_starts_with($mime, 'video/');
            $dataUrl = $media['public_url'] ?? '';
        @endphp

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-cloak
             x-init="$el.querySelector('.backdrop').addEventListener('click', () => $wire.closePreview())">
            {{-- Backdrop --}}
            <div class="backdrop fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            {{-- Modal panel --}}
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden z-10">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white truncate pr-4">
                            {{ $r['display_name'] ?? 'Recurso' }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $r['activity']['topic'] ?? '' }}
                            @if($r['description'])
                                · {{ $r['description'] }}
                            @endif
                        </p>
                    </div>
                    <button type="button" wire:click="closePreview"
                            class="shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body: preview area --}}
                <div class="flex-1 overflow-auto p-6 bg-gray-50/50 dark:bg-gray-900/30">
                    @if($isImage && $dataUrl)
                        <div class="flex items-center justify-center min-h-[200px]">
                            <img src="{{ $dataUrl }}"
                                 alt="{{ $r['display_name'] ?? 'Preview' }}"
                                 class="max-w-full max-h-[60vh] rounded-lg shadow-lg object-contain"
                                 onerror="this.closest('.flex').innerHTML = '<p class=\'text-sm text-red-400\'>No se pudo cargar la vista previa de la imagen.</p>'">
                        </div>
                    @elseif($isPdf && $dataUrl)
                        <div class="w-full" style="height: 65vh;">
                            <iframe src="{{ $dataUrl }}"
                                    class="w-full h-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white"
                                    title="PDF Preview"
                                    onerror="this.outerHTML = '<p class=\'text-sm text-red-400\'>No se pudo cargar la vista previa del PDF.</p>'">
                            </iframe>
                        </div>
                    @elseif($isVideo && $dataUrl)
                        <div class="flex items-center justify-center min-h-[200px]">
                            <video controls class="max-w-full max-h-[60vh] rounded-lg shadow-lg"
                                   onerror="this.outerHTML = '<p class=\'text-sm text-red-400\'>No se pudo cargar el video.</p>'">
                                <source src="{{ $dataUrl }}" type="{{ $mime }}">
                            </video>
                        </div>
                    @else
                        {{-- Fallback: file info card --}}
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Vista previa no disponible para este tipo de archivo
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if($mime)
                                    {{ $mime }}
                                @else
                                    Tipo de archivo desconocido
                                @endif
                            </p>
                            @if($media['size_for_humans'] ?? false)
                                <p class="text-xs text-gray-400 mt-3">
                                    Tamaño: {{ $media['size_for_humans'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 shrink-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        @if($media['size_for_humans'] ?? false)
                            {{ $media['original_name'] ?? '' }} · {{ $media['size_for_humans'] }}
                        @else
                            {{ $media['original_name'] ?? '' }}
                        @endif
                    </span>
                    <a href="{{ route('student.lms.resource.download', $r['id']) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
