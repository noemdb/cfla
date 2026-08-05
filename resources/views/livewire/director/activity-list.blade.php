{{-- resources/views/livewire/director/activity-list.blade.php --}}
<div class="fade-in">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Actividades</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de actividades académicas · solo lectura</p>
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
        @forelse($activities as $activity)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 flex flex-col md:flex-row md:items-center justify-between gap-3 dark:border-white/5 dark:bg-gray-900">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-cyan-500 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate dark:text-white">{{ $activity->topic }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            @if($activity->pevaluacion?->pensum?->asignatura?->name){{ $activity->pevaluacion->pensum->asignatura->name }} · @endif
                            @if($activity->pevaluacion?->seccion?->name){{ $activity->pevaluacion->seccion->name }} @endif
                            @if($activity->pevaluacion?->seccion?->grado?->name)· {{ $activity->pevaluacion->seccion->grado->name }} @endif
                            @if($activity->pevaluacion?->profesor?->lastname)· {{ $activity->pevaluacion->profesor->lastname }}, {{ $activity->pevaluacion->profesor->name }} @endif
                        </p>
                        @if($activity->thematic)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">{{ $activity->thematic }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>{{ $activity->pevaluacion?->lapso?->name }}
                    </span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('app.director.activities.format', $activity->pevaluacion_id) }}" target="_blank" rel="noopener"
                            class="text-[11px] font-bold uppercase tracking-widest text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300">
                            Formato
                        </a>
                        <a href="{{ route('app.director.activities.resume', $activity->pevaluacion_id) }}" target="_blank" rel="noopener"
                            class="text-[11px] font-bold uppercase tracking-widest text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300">
                            Resumen
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                Sin actividades para los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $activities->links() }}</div>

</div>
