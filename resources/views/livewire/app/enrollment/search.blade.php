<div class="space-y-6 text-white antialiased">
    <div class="flex flex-col items-center">
        <div
            class="w-20 h-20 bg-emerald-900/40 rounded-2xl flex items-center justify-center mb-4 border border-emerald-500/30">
            <x-icon name="identification" class="w-12 h-12 text-emerald-400" />
        </div>
        <h2 class="text-2xl font-bold">Identificación</h2>
        <p class="text-gray-400 text-sm">Ingrese los datos para continuar</p>
    </div>

    <div class="space-y-4">
        <div class="space-y-2">
            <label class="text-xs font-semibold text-emerald-400 uppercase tracking-widest ml-1">Cédula del
                Representante</label>
            <div class="flex gap-2">
                <div class="flex-1">
                    <x-input placeholder="Solo números" wire:model="ci"
                        class="bg-white/5 border-white/10 text-white placeholder-gray-500 rounded-xl focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <x-button icon="magnifying-glass" wire:click="search"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl aspect-square p-3 border-none shadow-lg shadow-emerald-900/20 transition-all active:scale-95" />
            </div>
            @error('ci')
                <span class="text-xs text-red-400 ml-1">{{ $message }}</span>
            @enderror
            @if ($error_message)
                <span class="text-xs text-red-400 ml-1 block">{{ $error_message }}</span>
            @endif
        </div>

        @if ($representant)
            <div
                class="bg-emerald-900/20 border border-emerald-500/30 p-6 rounded-2xl transition-all duration-500 animate-in fade-in slide-in-from-bottom-4">
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white font-bold text-xl uppercase">
                        {{ substr($representant->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-emerald-400">{{ $representant->name }}</h3>
                        <p class="text-xs text-gray-400">CI: {{ $representant->ci_representant }}</p>
                    </div>
                </div>

                <x-button label="Continuar al Sistema" wire:click="goToSaefl" primary
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white border-none rounded-xl font-bold shadow-lg" />
            </div>
        @else
            <div class="bg-white/2 border-l-4 border-emerald-600 p-4 rounded-r-xl">
                <p class="text-xs text-gray-400 leading-relaxed">
                    Este paso es necesario para identificar al representante en nuestro sistema y cargar la información
                    correspondiente a sus representados.
                </p>
            </div>
        @endif
    </div>

    <div class="pt-4">
        <button wire:click="restart"
            class="text-gray-500 hover:text-emerald-400 text-xs font-medium uppercase tracking-tighter transition-colors flex items-center gap-2 mx-auto">
            <x-icon name="arrow-left" class="w-3 h-3" />
            Volver a las instrucciones
        </button>
    </div>
</div>
