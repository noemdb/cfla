{{-- filepath: /home/nuser/code/cfla/resources/views/livewire/app/general/educational/competition/scoreboard/partials/scores.blade.php --}}
@php $peducativos = $competition->peducativos; @endphp
<div class="flex flex-wrap gap-3">
    @forelse ($peducativos as $peducativo)
        <div class="bg-lime-50 border-t-4 border-lime-500 p-3 rounded-md shadow-sm w-full">
            <div class="text-lg font-semibold text-end mb-1">{{$peducativo->name}}</div>

            @php $grados = $peducativo->grados; @endphp
            <ul class="space-y-1">
                @forelse ($grados as $grado)
                    <li class="border-b py-1">
                        <div class="flex justify-between items-center text-sm">
                            <div>{{$grado->name}}</div>
                            <div class="font-bold text-lime-700">{{$competition->getTotalScoreForGrado($grado->id)}} <small class="text-gray-400">Pts</small></div>
                        </div>

                        {{-- Mostrar las secciones activas del grado --}}
                        @php $secciones = $grado->activeSeccions(); @endphp
                        <div class="flex flex-wrap gap-2 ml-2 mt-1">
                            @forelse ($secciones as $seccion)
                                <div class="bg-white border border-gray-300 rounded px-2 py-1 text-xs font-medium text-gray-700 shadow-sm">
                                    {{$seccion->name}}: {{$competition->getTotalScoreForSection($seccion->id)}} Pts
                                </div>
                            @empty
                                <div class="text-gray-400 text-xs italic">No hay secciones activas</div>
                            @endforelse
                        </div>
                    </li>
                @empty
                    <li class="text-gray-400 text-sm italic">No hay grados/años</li>
                @endforelse
            </ul>
        </div>
    @empty
        <div class="text-gray-500 text-sm italic">No hay planes de estudio</div>
    @endforelse
</div>