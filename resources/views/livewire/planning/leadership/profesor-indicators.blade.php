<div class="fade-in space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Indicadores de Profesores</h1>
        <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">
            {{ count($profesores) }} profesor(es) en tus áreas asignadas
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Lista de profesores --}}
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-4 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60 px-1 pb-2 border-b border-white/5">
                Profesores
            </div>
            <div class="space-y-1 max-h-[70vh] overflow-y-auto">
                @forelse($profesores as $prof)
                    <button wire:click="selectProfesor({{ $prof->id }})"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors {{ $selectedProfesorId == $prof->id ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-gray-300 hover:text-amber-300 hover:bg-white/5' }}">
                        <span class="font-medium">{{ $prof->lastname }}, {{ $prof->name }}</span>
                    </button>
                @empty
                    <p class="text-gray-500 text-sm px-3 py-4 text-center">No hay profesores asignados a tus áreas.</p>
                @endforelse
            </div>

            {{-- Selector de lapso --}}
            <div class="pt-3 border-t border-white/5">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-1.5">Filtrar por lapso</label>
                <select wire:model.live="selectedLapsoId"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                    <option value="">Todos los lapsos</option>
                    @foreach($lapsos as $l)
                        <option value="{{ $l->id }}">{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Detalle del profesor seleccionado --}}
        <div class="lg:col-span-2 space-y-4">
            @if($profesor && $kpi)
                <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-4">
                    <h3 class="text-sm font-bold text-white">{{ $profesor->lastname }}, {{ $profesor->name }}</h3>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-1">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">IEE</div>
                            <div class="text-lg font-black {{ ($kpi['iee'] ?? 0) < 70 ? 'text-red-400' : 'text-emerald-400' }}">
                                {{ $kpi['iee'] ?? '—' }}%
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">IRE</div>
                            <div class="text-lg font-black {{ ($kpi['ire'] ?? 0) < 70 ? 'text-red-400' : 'text-emerald-400' }}">
                                {{ $kpi['ire'] ?? '—' }}%
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Notas (Real)</div>
                            <div class="text-lg font-black text-white">{{ $kpi['real_notas'] ?? 0 }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Carga (Pevas)</div>
                            <div class="text-lg font-black text-white">{{ $kpi['total_pevas'] ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-white/5">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-gray-500">Meta de notas:</span>
                            <span class="font-bold text-white">{{ $kpi['goal_notas'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-white/5 bg-gray-900 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">Selecciona un profesor para ver sus indicadores</p>
                </div>
            @endif
        </div>
    </div>
</div>
