{{-- Partial: Código de Vestimenta --}}
{{-- Controlado por $modalDressCode en CatchmentWizard --}}
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
    wire:click.self="closeDressCode">

    <div class="relative w-full max-w-lg rounded-lg bg-white dark:bg-gray-900 shadow-2xl overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                Código de Vestimenta
            </h3>
            <button wire:click="closeDressCode"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition rounded-lg p-1 hover:bg-gray-100 dark:hover:bg-gray-800">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-4 text-gray-700 dark:text-gray-200">

            {{-- Aviso principal --}}
            <div
                class="flex items-center gap-3 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 px-4 py-3">
                <x-icon name="exclamation-triangle" class="w-6 h-6 shrink-0 text-red-600 dark:text-red-400" />
                <p class="font-bold uppercase tracking-wide text-red-700 dark:text-red-400 text-sm">
                    Debe presentarse de manera sobria
                </p>
            </div>

            {{-- Lista de prohibiciones --}}
            <div
                class="rounded-lg border border-red-100 dark:border-red-900 bg-gray-50 dark:bg-gray-800/50 p-4">
                <h4
                    class="mb-2 flex items-center gap-2 font-bold text-red-700 dark:text-red-400 uppercase text-sm tracking-wide">
                    <x-icon name="x-circle" class="w-5 h-5 shrink-0" />
                    No está permitido:
                </h4>
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-start gap-2">
                        <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                        Licras, mono deportivo, leggings, strapless.
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                        Bermudas, shorts, pantalones rotos.
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                        Blusas y franelas sin mangas.
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                        Exponer el ombligo.
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="x-mark" class="mt-0.5 w-4 h-4 shrink-0 text-red-500" />
                        Vestidos o faldas cortas
                        <span class="text-gray-500 dark:text-gray-400">(debe estar por debajo de la rodilla).</span>
                    </li>
                </ul>
            </div>

            {{-- Nota al pie --}}
            <p class="text-[10px] italic text-gray-500 dark:text-gray-400 text-center">
                * Esta indicación aplica para el día de la cita presencial en el colegio.
            </p>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <x-button primary label="Entendido" wire:click="closeDressCode" />
        </div>
    </div>
</div>
