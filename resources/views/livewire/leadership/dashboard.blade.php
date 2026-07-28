<div class="fade-in">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Panel de Seguimiento</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium">
                {{ Auth::user()->username }} · {{ $metrics['total_areas'] }} área(s) asignada(s)
            </p>
        </div>
    </div>

    {{-- KPI Cards grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Áreas</div>
            <div class="text-3xl font-black text-white">{{ $metrics['total_areas'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Asignaturas</div>
            <div class="text-3xl font-black text-white">{{ $metrics['total_asignaturas'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Profesores</div>
            <div class="text-3xl font-black text-white">{{ $metrics['total_profesores'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">En Revisión</div>
            <div class="text-3xl font-black {{ $metrics['activities_in_review'] > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                {{ $metrics['activities_in_review'] }}
            </div>
        </div>
    </div>

    {{-- Áreas expandidas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($metrics['areas'] as $area)
            <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-amber-500/30 transition-all duration-200 p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="text-sm font-bold text-white">{{ $area['name'] }}</h3>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20">
                        {{ $area['total_asignaturas'] ?? 0 }}
                    </span>
                </div>
                @if($area['description'] ?? false)
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $area['description'] }}</p>
                @endif
                <div class="flex items-center gap-2 pt-2 border-t border-white/5">
                    <a href="{{ route('app.leadership.activities', ['area_id' => $area['id'] ?? 0]) }}"
                        class="text-[10px] font-bold uppercase tracking-widest text-amber-400 hover:text-amber-300 transition-colors">
                        Ver actividades →
                    </a>
                </div>
            </div>
        @endforeach
        @if(empty($metrics['areas']))
            <div class="col-span-full py-16 text-center">
                <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 font-medium mb-2">No tienes áreas asignadas</p>
                <p class="text-gray-600 text-sm">Contacta al administrador para asignarte como líder de áreas de conocimiento.</p>
            </div>
        @endif
    </div>
</div>
