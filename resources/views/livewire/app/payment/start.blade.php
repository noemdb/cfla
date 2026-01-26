<div class="h-full flex flex-col group cursor-pointer" wire:click="setStart">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-4">
        <div class="p-2 bg-emerald-900/50 rounded-lg border border-emerald-500/30">
            <x-icon name="document-text" class="w-8 h-8 text-emerald-400" />
        </div>
        <h3 class="text-lg md:text-xl font-bold text-emerald-100 uppercase tracking-wide">
            Reporta tus pagos aquí
        </h3>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col">
        <div class="relative overflow-hidden rounded-xl mb-4">
            <div
                class="flex justify-center bg-gray-800/50 p-6 rounded-xl border border-emerald-500/20 group-hover:border-emerald-500/40 transition-colors">
                <img class="w-24 h-24 filter drop-shadow-[0_0_10px_rgba(16,185,129,0.3)]"
                    src="{{ asset('image/logo/report-payment.png') }}" alt="" />

                <div
                    class="absolute inset-0 bg-emerald-500/0 group-hover:bg-emerald-500/5 transition-colors duration-300">
                </div>
            </div>

            <div class="text-center mt-4 border-b border-emerald-500/20 pb-4 mb-4">
                <div class="text-xl font-semibold text-gray-200 mb-1">Reporte de pago</div>
                <div class="text-sm text-emerald-400 uppercase tracking-wider font-bold mb-2">Asistente</div>
                <div class="text-xs text-gray-400">Reporta tus transferencias, pago móvil y/o depósitos.</div>
            </div>
        </div>

        <div class="bg-emerald-900/20 border border-emerald-500/20 p-4 rounded-xl mb-6">
            <div class="text-sm text-gray-300 leading-relaxed">
                <span class="text-emerald-400 font-bold block mb-1">Un proceso sencillo y rápido:</span>
                Usando esta opción es necesaria la verificación, conciliación y registro de los datos ingresados. Estas
                actividades cumplen con un lapso de tiempo <span class="text-emerald-200">(1 o 2 días)</span> para ser
                procesados en el <span class="text-emerald-400 font-bold">SAEFL</span>.
            </div>
        </div>

        <div class="mt-auto">
            <x-button label="Comenzar" wire:click="setStart" positive
                class="w-full bg-emerald-600 hover:bg-emerald-500 border-none shadow-lg shadow-emerald-500/20" />
        </div>
    </div>
</div>
