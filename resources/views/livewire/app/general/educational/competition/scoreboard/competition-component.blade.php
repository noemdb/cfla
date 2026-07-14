<div>
    <div class="m-1 w-full">

        @if ($competition)

            <div class="flex justify-between items-center bg-white border-b border-emerald-200 h-full p-2">

                <div class="flex-1">

                    {{-- Badge ID + indicador activo --}}
                    <div class="flex items-center space-x-2 mb-1">
                        <span
                            class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter border border-emerald-200">
                            ID: {{ $competition->id }}
                        </span>

                        @if ($competition->status_active)
                            <span class="flex h-2 w-2 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                            </span>
                        @endif
                    </div>

                    {{-- Nombre de la competición --}}
                    <h5 class="text-xl font-black tracking-tight text-gray-900 uppercase">
                        {{ $competition->name }}
                    </h5>

                    {{-- Descripción colapsable --}}
                    <div x-data="{ open: false }" class="mt-2">
                        <button
                            class="text-xs font-bold text-emerald-600/70 hover:text-emerald-700 transition-colors uppercase tracking-widest"
                            @click="open = ! open">
                            Ver descripción <span x-text="open ? '-' : '+'"></span>
                        </button>
                        <div x-show="open" x-collapse @click.outside="open = false"
                            class="mt-2 text-sm text-gray-500 italic">
                            {{ $competition->description }}
                        </div>
                    </div>

                </div>

                {{-- Dropdown acciones --}}
                <div class="flex-none">
                    <x-dropdown icon="ellipsis-vertical" class="text-emerald-600">
                        <x-dropdown.item label="Planilla de Resultados" icon="chart-bar" />
                    </x-dropdown>
                </div>

            </div>

        @else

            <div class="text-gray-500 italic text-sm p-2">
                Espere a que se establezca una competición activa
            </div>

        @endif

    </div>
</div>