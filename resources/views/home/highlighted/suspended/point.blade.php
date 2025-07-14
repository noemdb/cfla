<x-card class="h-full">

    @slot('header')
        <h3 class="text-green-950 bg-warning-100 rounded-t-xl p-2 text-lg md:text-xl font-bold dark:text-neutral-200">
            <div class="h-full flex items-center">
                <x-icon name="credit-card" class="flex-none w-10 h-10" />
                <div class="flex-initial">Punto de Venta Virtual.</div>
            </div>
        </h3>
    @endslot

    <div class="container mx-auto px-4 h-full">
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">⚠️ ¡Importante!</strong>
            <span class="block sm:inline">El servicio de <strong>Botón de Pago Virtual (TPV)</strong> se encuentra
                temporalmente suspendido.</span>
            <p class="text-sm mt-2">Agradecemos su comprensión y le pedimos disculpas por cualquier inconveniente.</p>
        </div>
    </div>

</x-card>

{{--  --}}
