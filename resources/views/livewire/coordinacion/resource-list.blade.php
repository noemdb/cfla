<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Recursos Compartidos</h1>
            <p class="text-sm text-gray-400 mt-1">Archivos y materiales didácticos disponibles</p>
        </div>
    </div>

    {{-- Search --}}
    <div>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre del recurso o actividad..."
            class="w-full rounded-lg border border-white/10 bg-gray-800 text-gray-200 text-sm px-3 py-2 placeholder-gray-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
    </div>

    {{-- Resources Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($resources as $resource)
            <div class="bg-gray-800/30 border border-white/5 rounded-xl p-4 hover:border-emerald-500/30 transition-colors">
                {{-- Icon + Name --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ $resource->display_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">
                            {{ $resource->activity?->topic ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-500">
                    <span class="px-2 py-0.5 bg-gray-700/50 rounded">
                        {{ $resource->activity?->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                    </span>
                    <span>
                        {{ $resource->activity?->pevaluacion?->profesor?->lastname ?? '' }}
                    </span>
                </div>

                {{-- Download button --}}
                @if($resource->media && ($resource->media->public_url || $resource->media->external_url))
                    <a href="{{ $resource->media->external_url ?: $resource->media->public_url }}"
                        target="_blank"
                        class="mt-3 flex items-center justify-center gap-1.5 w-full px-3 py-1.5 text-xs bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descargar
                    </a>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                No se encontraron recursos disponibles.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $resources->links() }}
    </div>
</div>
