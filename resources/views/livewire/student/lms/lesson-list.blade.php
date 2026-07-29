<div class="max-w-6xl mx-auto py-8 px-4 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Lecciones</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Actividades publicadas por tus profesores
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Buscar lección…"
               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm"/>
        <select wire:model.live="lapsoId"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm">
            <option value="">Todos los lapsos</option>
            @foreach($lapsos as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <select wire:model.live="asignaturaId"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm">
            <option value="">Todas las asignaturas</option>
            {{-- Las asignaturas se filtran desde el backend con whereHas --}}
        </select>
    </div>

    {{-- Grid de actividades --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($activities as $activity)
            <a href="{{ route('student.lms.activity', $activity) }}"
               class="group block bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-3 hover:border-emerald-500/30 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-400 transition-colors truncate">
                            {{ $activity->topic }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                        </p>
                    </div>
                </div>
                @if($activity->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ Str::limit(strip_tags($activity->description), 120) }}
                    </p>
                @endif
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-[10px] text-gray-400">
                        {{ $activity->pevaluacion?->lapso?->name ?? '' }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium text-emerald-400 bg-emerald-500/10">
                        Ver lección
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-16">
                <p class="text-gray-500 font-medium">No hay lecciones disponibles</p>
                <p class="text-xs text-gray-400 mt-1">Las lecciones aparecerán cuando los profesores las publiquen.</p>
            </div>
        @endforelse
    </div>

    @if($activities->hasPages())
        <div class="pt-4">{{ $activities->links('vendor.livewire.custom-tailwind') }}</div>
    @endif
</div>
