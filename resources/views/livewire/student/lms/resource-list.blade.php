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
                    <a href="{{ route('student.lms.resource.download', $resource) }}"
                       class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-[10px] font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </a>
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
</div>
