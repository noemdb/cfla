@php $peducativos = $competition->peducativos; @endphp

<div class="flex flex-col gap-2 p-2">
    @forelse ($peducativos as $peducativo)

        {{-- Tarjeta con estado Alpine independiente (para colapsar una sin afectar a las demás) --}}
        <div x-data="{ showGrades: true }" class="border border-emerald-200 rounded-lg shadow-sm bg-emerald-100 overflow-hidden">

            {{-- Cabecera Clickeable (Botón de colapsado) --}}
            <div @click="showGrades = !showGrades" 
                 class="flex items-center justify-between p-3 cursor-pointer hover:bg-emerald-200/50 transition-colors select-none">
                
                <div class="text-sm font-bold text-emerald-900 uppercase tracking-wide">
                    {{ $peducativo->name }}
                </div>

                {{-- Icono Chevron (Gira al colapsar) --}}
                <svg class="w-5 h-5 text-emerald-700 transform transition-transform duration-300"
                     :class="{ '-rotate-90': !showGrades }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Contenido Colapsable --}}
            <div x-show="showGrades" x-collapse>
                <div class="px-3 pb-3 pt-0">
                    @php $grados = $peducativo->grados; @endphp
                    <ul class="space-y-1.5">
                        @forelse ($grados as $grado)
                            <li class="border-b border-emerald-200/50 py-1.5 last:border-b-0">
                                
                                {{-- Nombre del grado --}}
                                <div class="flex justify-between items-center text-sm mb-1">
                                    <div class="font-semibold text-gray-900">{{ $grado->name }}</div>
                                </div>

                                {{-- Secciones activas --}}
                                @php $secciones = $grado->activeSeccions(); @endphp
                                <div class="flex flex-wrap gap-1.5 ml-0">
                                    @forelse ($secciones as $seccion)
                                        <div class="bg-white border border-emerald-300 rounded-lg px-2.5 py-1 text-xs font-bold text-emerald-800 shadow-sm hover:bg-emerald-50 transition-colors cursor-default">
                                            {{ $seccion->name }}: 
                                            <span class="text-gray-900 font-black">{{ $competition->getTotalScoreForSection($seccion->id) }}</span>
                                            Pts
                                        </div>
                                    @empty
                                        <div class="text-gray-500 text-xs italic bg-emerald-50/60 px-2 py-1 rounded border border-emerald-100">
                                            No hay secciones activas
                                        </div>
                                    @endforelse
                                </div>
                            </li>
                        @empty
                            <li class="text-gray-500 text-sm italic bg-emerald-50/60 p-2 rounded-lg border border-emerald-100">
                                No hay grados/años
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

    @empty
        <div class="text-gray-500 text-sm italic text-center py-2 bg-emerald-50/60 rounded-lg border border-emerald-100">
            No hay planes de estudio
        </div>
    @endforelse
</div>