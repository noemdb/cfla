<div class="mt-1 w-full border-t-2 border-emerald-300">

    <div class="py-4 bg-emerald-100 border border-emerald-200 rounded-lg shadow-sm">

        <h4 class="text-lg font-bold text-emerald-800 uppercase tracking-widest mb-4">Puntuación</h4>

        @if (!empty($grado->name))
            <div class="font-bold text-lg text-gray-800 mb-4">{{ $grado->name }}</div>
            @php $seccions = $grado->activeSeccions() @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($seccions as $item)
                    <div
                        class="flex flex-col items-center justify-center p-6 text-center bg-white border border-emerald-200 rounded-lg hover:border-emerald-400 transition-all duration-300 shadow-sm">
                        <h4 class="mb-2 text-lg font-bold tracking-tight text-emerald-800 uppercase">
                            Sección {{ $item->name }}
                        </h4>
                        <p class="font-black text-5xl text-emerald-900">
                            {{ $competition->getTotalScoreForSection($item->id) }}
                        </p>
                        <span class="text-xs text-emerald-700 font-bold uppercase tracking-widest mt-1">PTS</span>
                    </div>
                @empty
                    <div class="col-span-2 text-gray-600 italic py-4">No hay secciones activas</div>
                @endforelse
            </div>
        @else
            <div class="text-gray-600 italic">No hay Grado/Año</div>
        @endif

    </div>

</div>