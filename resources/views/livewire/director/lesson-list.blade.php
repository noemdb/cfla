{{-- resources/views/livewire/director/lesson-list.blade.php --}}
<div class="fade-in">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Lecciones</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de lecciones publicadas en el LMS · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por tema…"
                class="w-full sm:w-64 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
            <select wire:model.live="lapsoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Todos los lapsos</option>
                @foreach($lapsos as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($lessons as $lesson)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 flex flex-col md:flex-row md:items-center justify-between gap-3 dark:border-white/5 dark:bg-gray-900">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate dark:text-white">{{ $lesson->topic }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            @if($lesson->pevaluacion?->pensum?->asignatura?->name){{ $lesson->pevaluacion->pensum->asignatura->name }} · @endif
                            @if($lesson->pevaluacion?->pensum?->pestudio?->peducativo?->name){{ $lesson->pevaluacion->pensum->pestudio->peducativo->name }} @endif
                            @if($lesson->pevaluacion?->profesor?->lastname)· {{ $lesson->pevaluacion->profesor->lastname }}, {{ $lesson->pevaluacion->profesor->name }} @endif
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                            {{ $lesson->lmsSections->count() }} secciones
                            @if($lesson->lmsPublication?->published_at)· Publicada {{ $lesson->lmsPublication->published_at->format('d/m/Y') }} @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $lesson->pevaluacion?->lapso?->name }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                        {{ ucfirst($lesson->lmsPublication?->status ?? 'Sin estado') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                Sin lecciones publicadas para los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $lessons->links() }}</div>

</div>
