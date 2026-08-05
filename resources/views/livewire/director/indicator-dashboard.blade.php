{{-- resources/views/livewire/director/indicator-dashboard.blade.php --}}
<div class="fade-in">

    {{-- Cabecera --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Panel de Dirección</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento institucional · modo solo lectura</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($lapsoActive)
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                    Lapso activo: {{ $lapsoActive->name }}
                </span>
            @endif
            <select wire:model.live="selectedLapsoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                @foreach($lapsos ?? [] as $lapso)
                    <option value="{{ $lapso->id }}">{{ $lapso->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPIs globales (toda la institución) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Peducativos</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPeducativos }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Pensums</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPensums }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Evaluaciones</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPevaluacions }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Actividades</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalActivities }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Recursos</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalResources }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Profesores</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalProfesoresActivos }}</p>
        </div>
    </div>

    {{-- Indicadores por Peducativo --}}
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-white/5 dark:bg-gray-900">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-white/5">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Indicadores por Peducativo</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Carga, actividades y profesores por unidad educativa</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-white/5">
                        <th class="px-5 py-3">Peducativo</th>
                        <th class="px-5 py-3">Pensums</th>
                        <th class="px-5 py-3">Actividades</th>
                        <th class="px-5 py-3">Profesores</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peducativoIndicators as $item)
                        <tr class="border-b border-gray-100 last:border-0 dark:border-white/5">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $item->peducativo->name }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->pensums_count }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->activities_count }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->profesores_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin datos para el lapso seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
