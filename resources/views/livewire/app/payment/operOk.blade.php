<x-modal.card title="Resultado de la operación" blur wire:model="modalOperOk" max-width="xl">

    <x-card>
        @slot('title')
        {{-- <h1 class="text-center text-green-800 text-9xl">Operación realizada éxitosamente</h1> --}}
        @endslot
        <div class="max-w-sm mx-auto bg-white border border-gray-200 rounded-lg shadow-lg p-6">
            <!-- Icono de éxito -->
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 text-green-600 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 10-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Título y mensaje -->
            <div class="text-center mt-4">
                <h2 class="text-xl font-bold text-gray-800">¡Transacción exitosa!</h2>
                <p class="mt-2 text-sm text-gray-500">
                    Tu transacción se completó correctamente. Gracias por confiar en nuestro servicio.
                </p>
            </div>

            <!-- Badge -->
            <div class="flex justify-center mt-4">
                <span
                    class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                    Confirmado
                </span>
            </div>

            <!-- Detalles de la transacción -->
            <div class="mt-4 border-t pt-4 text-sm text-gray-600">
                <p><strong>ID de transacción:</strong> {{$payment->id ?? null}}</p>
                {{-- <p><strong>Fecha:</strong> {{$payment->created_at->format('d-m-Y h:i A') ?? null}}</p> --}}
                <p><strong>Monto:</strong>Bs. {{ number_format($payment->ammount_1,2,',','.') ?? null}}</p>
            </div>

            <!-- Botón de acción -->
            <div class="mt-6">
                <button wire:click="close()"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-300">
                    Cerrar
                </button>
            </div>
        </div>

    </x-card>

</x-modal.card>