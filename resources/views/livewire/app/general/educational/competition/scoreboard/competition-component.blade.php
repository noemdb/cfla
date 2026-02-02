<div>

    <div class="m-1 w-full" wire:poll.1s="updateCompetition({{ $competition->id }})">

        @if ($competition)
            <div class="flex justify-between items-center bg-transparent border-b border-emerald-500/10 h-full p-2">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <span
                            class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter">ID:
                            {{ $competition->id }}</span>
                        @if ($competition->status_active)
                            <span class="flex h-2 w-2 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                        @endif
                    </div>
                    <h5 class="text-3xl font-black tracking-tight text-white uppercase group">
                        {{ $competition->name }}
                    </h5>

                    <div x-data="{ open: false }" class="mt-2">
                        <button
                            class="text-xs font-bold text-emerald-400/60 hover:text-emerald-400 transition-colors uppercase tracking-widest"
                            @click="open = ! open">Ver descripción <span x-text="open ? '-' : '+'"></span></button>
                        <div x-show="open" x-collapse @click.outside="open = false"
                            class="mt-2 text-sm text-gray-400 italic">
                            {{ $competition->description }}
                        </div>
                    </div>
                </div>

                <div class="flex-none">
                    <x-dropdown icon="ellipsis-vertical" class="text-emerald-400">
                        <x-dropdown.item label="Planilla de Resultados" icon="chart-bar" />
                    </x-dropdown>
                </div>
            </div>
        @else
            <div>Espere a que se establezca una competición activa</div>
        @endif

    </div>

</div>
