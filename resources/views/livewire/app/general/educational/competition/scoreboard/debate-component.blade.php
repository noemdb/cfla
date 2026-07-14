<div>
    <div class="m-1 w-full">

        @if ($debate)

            <div class="flex justify-between items-center bg-white border-b border-emerald-200 h-full p-2">

                <div class="flex-1">

                    {{-- Badges: ID debate + grado --}}
                    <div class="flex items-center space-x-2 mb-1">
                        <span
                            class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter border border-emerald-200">
                            DEBATE: {{ $debate->id }}
                        </span>
                        <span
                            class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-bold tracking-tighter uppercase border border-gray-200">
                            {{ $debate->grado->name ?? 'Grado general' }}
                        </span>
                    </div>

                    {{-- Nombre del debate --}}
                    <h5 class="text-lg font-black tracking-tight text-gray-900 uppercase">
                        {{ $debate->name }}
                    </h5>

                    {{-- Descripción colapsable --}}
                    <div x-data="{ open: false }" class="mt-2">
                        <button
                            class="text-xs font-bold text-emerald-600/70 hover:text-emerald-700 transition-colors uppercase tracking-widest"
                            @click="open = ! open">
                            Ver detalles <span x-text="open ? '-' : '+'"></span>
                        </button>
                        <div x-show="open" x-collapse @click.outside="open = false"
                            class="mt-2 text-sm text-gray-500 italic">
                            {{ $debate->description }}
                        </div>
                    </div>

                </div>

                {{-- Dropdown acciones --}}
                <div class="flex-none">
                    <x-dropdown icon="adjustments-vertical" class="text-emerald-600">
                        <x-dropdown.item label="Estadísticas de Debate" icon="chart-bar" />
                    </x-dropdown>
                </div>

            </div>

        @else

            <div class="text-gray-500 italic text-sm p-2">
                Espere... No hay un debate activo
            </div>

        @endif

    </div>
</div>