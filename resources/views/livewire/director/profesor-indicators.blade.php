{{-- resources/views/livewire/director/profesor-indicators.blade.php --}}
<div class="fade-in">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Profesores</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento de docentes y su carga por lapso · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por apellido o nombre…"
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
        @forelse($profesores as $profesor)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 flex flex-col md:flex-row md:items-center justify-between gap-3 dark:border-white/5 dark:bg-gray-900">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate dark:text-white">{{ $profesor->lastname }}, {{ $profesor->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Docente activo</p>
                    </div>
                </div>
                <div class="md:text-right shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                        {{ $profesor->peva_count }} evaluaciones
                    </span>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                Sin profesores para los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $profesores->links() }}</div>

</div>
