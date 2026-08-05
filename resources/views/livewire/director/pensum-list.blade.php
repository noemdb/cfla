{{-- resources/views/livewire/director/pensum-list.blade.php --}}
<div class="fade-in">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Pensums</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Consulta de pensums por estudio · solo lectura</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por asignatura, grado o estudio…"
                class="w-full sm:w-64 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
            <select wire:model.live="peducativoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Todos los peducativos</option>
                @foreach($peducativos as $peducativo)
                    <option value="{{ $peducativo->id }}">{{ $peducativo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($pensums as $pensum)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 flex flex-col md:flex-row md:items-center justify-between gap-3 dark:border-white/5 dark:bg-gray-900">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-sky-500 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate dark:text-white">{{ $pensum->asignatura?->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $pensum->grado?->name }} · {{ $pensum->pestudio?->name }} · {{ $pensum->pestudio?->peducativo?->name }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 dark:border-white/5 dark:bg-gray-900 dark:text-gray-500">
                Sin pensums para los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $pensums->links() }}</div>

</div>
