<div>
    <div class="m-1 w-full" wire:poll.1s="updateDebate({{ $competition->id }})">
        {{-- <div class="m-1 w-full"> --}}

        @if ($debate)
            <div class="flex justify-between items-center bg-transparent border-b border-emerald-500/10 h-full p-2">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <span
                            class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter">DEBATE:
                            {{ $debate->id }}</span>
                        <span
                            class="text-[10px] bg-gray-800 text-gray-400 px-2 py-0.5 rounded-full font-bold tracking-tighter uppercase">{{ $debate->grado->name ?? 'Grado general' }}</span>
                    </div>

                    <h5 class="text-2xl font-black tracking-tight text-white uppercase group">
                        {{ $debate->name }}
                    </h5>

                    <div x-data="{ open: false }" class="mt-2">
                        <button
                            class="text-xs font-bold text-emerald-400/60 hover:text-emerald-400 transition-colors uppercase tracking-widest"
                            @click="open = ! open">Ver detalles <span x-text="open ? '-' : '+'"></span></button>
                        <div x-show="open" x-collapse @click.outside="open = false"
                            class="mt-2 text-sm text-gray-400 italic">
                            {{ $debate->description }}
                        </div>
                    </div>
                </div>

                <div class="flex-none">
                    <x-dropdown icon="adjustments-vertical" class="text-emerald-400">
                        <x-dropdown.item label="Estadísticas de Debate" icon="chart-bar" />
                    </x-dropdown>
                </div>
            </div>
        @else
            <div>Espere... No hay un debate activo</div>
        @endif

    </div>
</div>
