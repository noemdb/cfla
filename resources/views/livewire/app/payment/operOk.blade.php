<x-modal-card title="Resultado de la operación" blur wire:model="modalOperOk" max-width="xl" align="center">
    <div class="relative">
        <!-- Background Effects -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-gray-900/50 rounded-xl pointer-events-none">
        </div>

        <x-card class="border border-emerald-500/30 bg-gray-900/90 backdrop-blur-xl shadow-2xl relative overflow-hidden">

            <div class="max-w-sm mx-auto p-2">
                <!-- Icono de éxito -->
                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto bg-emerald-900/50 text-emerald-400 rounded-full border border-emerald-500/30 shadow-[0_0_20px_rgba(16,185,129,0.3)] animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 10-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>

                <!-- Título y mensaje -->
                <div class="text-center mt-6">
                    <h2 class="text-2xl font-bold text-white mb-2">¡Transacción exitosa!</h2>
                    <p class="text-sm text-gray-300 leading-relaxed">
                        Tu transacción se completó correctamente. Gracias por confiar en nuestro servicio.
                    </p>
                </div>

                <!-- Badge -->
                <div class="flex justify-center mt-6">
                    <span
                        class="inline-flex items-center px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-300 bg-emerald-900/30 border border-emerald-500/30 rounded-full shadow-sm">
                        Confirmado
                    </span>
                </div>

                <!-- Detalles de la transacción -->
                <div class="mt-6 border-t border-emerald-500/20 pt-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">ID de registro:</span>
                        <span class="font-mono text-emerald-200">{{ $payment->id ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Referencia:</span>
                        <span class="font-mono text-emerald-200">{{ $payment->number_i_pay_1 ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Fecha:</span>
                        <span class="text-gray-200">{{ Carbon\Carbon::now()->format('d/m/Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold pt-2 border-t border-emerald-500/10">
                        <span class="text-gray-200">Monto Total:</span>
                        <span class="text-emerald-400">Bs.
                            {{ number_format($payment->ammount_1 ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Botón de acción -->
                <div class="mt-8">
                    <button wire:click="close()"
                        class="w-full px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 rounded-xl shadow-lg shadow-emerald-500/20 transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                        Cerrar Ventana
                    </button>
                </div>
            </div>

        </x-card>
    </div>
</x-modal-card>
