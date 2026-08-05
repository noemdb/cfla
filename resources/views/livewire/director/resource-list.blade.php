{{-- resources/views/livewire/director/resource-list.blade.php --}}
<div class="fade-in">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Recursos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de recursos educativos del LMS · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o tema…"
                class="w-full sm:w-64 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
        </div>
    </div>

    <div class="space-y-3">
        @forelse($resources as $resource)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 flex flex-col md:flex-row md:items-center justify-between gap-3 dark:border-white/5 dark:bg-gray-900">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate dark:text-white">{{ $resource->display_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            @if($resource->activity?->topic){{ $resource->activity->topic }} @endif
                            @if($resource->activity?->pevaluacion?->pensum?->asignatura?->name)· {{ $resource->activity->pevaluacion->pensum->asignatura->name }} @endif
                            @if($resource->activity?->pevaluacion?->profesor?->lastname)· {{ $resource->activity->pevaluacion->profesor->lastname }}, {{ $resource->activity->pevaluacion->profesor->name }} @endif
                        </p>
                        @if($resource->media?->original_name)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ $resource->media->original_name }} @if($resource->media->mime_type)· {{ $resource->media->mime_type }} @endif</p>
                        @endif
                    </div>
                </div>
                <div class="md:text-right shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Recurso
                    </span>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                Sin recursos para los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $resources->links() }}</div>

</div>
