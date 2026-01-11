<div class="mt-1 w-full border-t-2 border-emerald-500/30">

    <div class="py-4 diagnostic-card bg-gray-900/40 border border-emerald-500/20 rounded-2xl backdrop-blur-sm">

        <h4 class="text-2xl font-bold text-emerald-400 uppercase tracking-widest mb-4">Puntuación</h4>

        @if (!empty($grado->name))
            <div class="font-bold text-xl text-gray-300 mb-4">{{ $grado->name }}</div>
            @php $seccions = $grado->activeSeccions() @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($seccions as $item)
                    <div
                        class="diagnostic-card flex flex-col items-center justify-center p-6 text-center bg-gray-800/40 border border-emerald-500/10 rounded-xl hover:border-emerald-500/30 transition-all duration-300">
                        <h4 class="mb-2 text-lg font-bold tracking-tight text-emerald-300 uppercase">
                            Sección {{ $item->name }}
                        </h4>
                        <p class="font-black text-5xl text-white">
                            {{ $competition->getTotalScoreForSection($item->id) }}
                        </p>
                        <span class="text-xs text-emerald-500 font-bold uppercase tracking-widest mt-1">PTS</span>
                    </div>
                @empty
                    <div class="col-span-2 text-gray-500 italic py-4">No hay secciones activas</div>
                @endforelse
            </div>
        @else
            <div class="text-gray-500 italic">No hay Grado/Año</div>
        @endif

    </div>

</div>
