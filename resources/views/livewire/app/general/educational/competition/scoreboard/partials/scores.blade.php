{{-- filepath: /home/nuser/code/cfla/resources/views/livewire/app/general/educational/competition/scoreboard/partials/scores.blade.php --}}
@php $peducativos = $competition->peducativos; @endphp
<div class="flex flex-col gap-3 p-2">
    @forelse ($peducativos as $peducativo)
        <div
            class="diagnostic-card border border-emerald-500/20 p-4 rounded-2xl shadow-lg bg-gray-900/40 backdrop-blur-sm">
            <div class="text-lg font-bold text-emerald-300 text-end mb-3 uppercase tracking-wide">{{ $peducativo->name }}
            </div>

            @php $grados = $peducativo->grados; @endphp
            <ul class="space-y-2">
                @forelse ($grados as $grado)
                    <li class="border-b border-emerald-500/10 py-2">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <div class="font-bold text-white">{{ $grado->name }}</div>
                        </div>

                        {{-- Mostrar las secciones activas del grado --}}
                        @php $secciones = $grado->activeSeccions(); @endphp
                        <div class="flex flex-wrap gap-2 ml-2">
                            @forelse ($secciones as $seccion)
                                <div
                                    class="bg-emerald-600/20 border border-emerald-500/30 rounded-lg px-3 py-1.5 text-xs font-bold text-emerald-300 shadow-sm hover:bg-emerald-600/30 transition-colors">
                                    {{ $seccion->name }}: <span
                                        class="text-white">{{ $competition->getTotalScoreForSection($seccion->id) }}</span>
                                    Pts
                                </div>
                            @empty
                                <div class="text-gray-500 text-xs italic">No hay secciones activas</div>
                            @endforelse
                        </div>
                    </li>
                @empty
                    <li class="text-gray-500 text-sm italic">No hay grados/años</li>
                @endforelse
            </ul>
        </div>
    @empty
        <div class="text-gray-500 text-sm italic text-center py-4">No hay planes de estudio</div>
    @endforelse
</div>
