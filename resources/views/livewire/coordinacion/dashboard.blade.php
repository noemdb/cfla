<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Panel de Coordinación</h1>
            <p class="text-sm text-gray-400 mt-1">Indicadores y seguimiento de tus programas educativos</p>
        </div>
        <div class="w-full sm:w-48">
            <select wire:model.live="selectedLapsoId"
                class="w-full rounded-lg border border-white/10 bg-gray-800 text-gray-200 text-sm px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                <option value="">Seleccionar lapso</option>
                @foreach($lapsos as $lapso)
                    <option value="{{ $lapso->id }}">{{ $lapso->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-800/50 border border-white/5 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Actividades</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">{{ number_format($totalActivities) }}</p>
        </div>
        <div class="bg-gray-800/50 border border-white/5 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Profesores Activos</p>
            <p class="text-2xl font-bold text-blue-400 mt-1">{{ number_format($totalProfesoresActivos) }}</p>
        </div>
        <div class="bg-gray-800/50 border border-white/5 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Evaluaciones</p>
            <p class="text-2xl font-bold text-purple-400 mt-1">{{ number_format($totalPevaluacions) }}</p>
        </div>
        <div class="bg-gray-800/50 border border-white/5 rounded-xl p-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Recursos</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($totalResources) }}</p>
        </div>
    </div>

    {{-- Indicadores por Programa Educativo --}}
    @forelse($peducativoIndicators as $item)
        <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">{{ $item->peducativo->name ?? 'Programa' }}</h3>
                    <p class="text-xs text-gray-500">{{ $item->pestudios->count() }} plan(es) de estudio</p>
                </div>
                <span class="text-xs text-gray-500 bg-gray-800 px-2.5 py-1 rounded-full">
                    {{ $lapsoActive?->name ?? $selectedLapsoId ? "Lapso #{$selectedLapsoId}" : '' }}
                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-emerald-400">{{ number_format($item->activities_count) }}</p>
                    <p class="text-xs text-gray-500">Actividades</p>
                </div>
                <div class="bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-blue-400">{{ number_format($item->profesores_count) }}</p>
                    <p class="text-xs text-gray-500">Profesores</p>
                </div>
                <div class="bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-purple-400">{{ number_format($item->lessons_count) }}</p>
                    <p class="text-xs text-gray-500">Lecciones</p>
                </div>
                <div class="bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-cyan-400">{{ number_format($item->grados_count) }}</p>
                    <p class="text-xs text-gray-500">Grados</p>
                </div>
                <div class="bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-amber-400">{{ number_format($item->pensums_count) }}</p>
                    <p class="text-xs text-gray-500">Pensums</p>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12 text-gray-500">
            <p>No tienes programas educativos asignados.</p>
            <p class="text-sm mt-1">Contacta al administrador para asignarte como coordinador.</p>
        </div>
    @endforelse

    {{-- Loading indicator --}}
    <div wire:loading class="fixed bottom-4 right-4 bg-emerald-600 text-white text-xs px-3 py-2 rounded-lg shadow-lg">
        Cargando...
    </div>
</div>
