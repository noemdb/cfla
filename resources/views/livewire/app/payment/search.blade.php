<x-modal-card blur wire:model="modalSearch" align="center">

    <div class="h-full flex flex-col justify-center max-w-2xl mx-auto shadow-xl">
        <x-card class="border border-emerald-500/30 bg-gray-900/90 backdrop-blur-xl shadow-2xl relative overflow-hidden">

            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-green-600"></div>

            <div class="p-6">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div
                        class="inline-flex justify-center items-center p-3 bg-emerald-900/30 rounded-full border border-emerald-500/20 mb-4 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                        <x-icon name="document-text" class="w-10 h-10 text-emerald-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1">Reporte de Pago</h2>
                    <div class="text-emerald-400 font-medium tracking-wide">Asistente</div>
                    <div
                        class="text-xs font-semibold text-emerald-200/70 uppercase tracking-widest mt-2 bg-emerald-900/30 py-1 px-3 rounded-full inline-block border border-emerald-500/10">
                        Período Escolar 2024 - 2025</div>
                </div>

                <!-- Input Section -->
                <div class="bg-gray-800/50 p-6 rounded-xl border border-emerald-500/10 mb-6">
                    <div class="flex items-end gap-3">
                        <div class="flex-grow">
                            <x-input
                                class="bg-gray-900 border-gray-700 text-white placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500/20 transition-colors"
                                label="CI. Representante" placeholder="Ej: 12345678" corner-hint="Sólo números"
                                wire:model="ci" />
                        </div>
                        <div class="mb-[2px]">
                            <x-button
                                class="h-[42px] px-6 bg-emerald-600 hover:bg-emerald-500 text-white border-0 shadow-lg shadow-emerald-500/20 transition-all rounded-lg"
                                icon="magnifying-glass" wire:click="search" />
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-gray-800/80 rounded-lg p-4 border-l-4 border-emerald-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <x-icon name="information-circle" class="w-5 h-5 text-emerald-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-100 leading-relaxed font-light tracking-wide">
                                El número de cédula de identidad se solicita para verificar la identidad del
                                representante y asociar su reporte de pago.
                                El <span class="text-emerald-400 font-bold">SAEFL</span> buscará en su base de datos
                                para iniciar el asistente.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </x-card>
    </div>

</x-modal-card>
