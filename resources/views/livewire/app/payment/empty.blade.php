<x-modal.card title="No se encontró ningún registro" blur wire:model="modalEmpty" max-width="lg" align="center">
    <div class="bg-gray-900/90 text-center p-6 rounded-lg border border-emerald-500/30">
        <div class="mb-4">
            <div
                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium leading-6 text-gray-200">No hay resultados</h3>
            <p class="text-sm text-gray-400 mt-2">No se encontró ningún registro asociado a la CI ingresada:</p>
        </div>
        <div
            class="flex justify-center text-3xl font-mono font-bold text-emerald-400 my-6 tracking-wider bg-gray-800 rounded-lg py-2 border border-dashed border-gray-700">
            {{ $ci ?? '---' }}</div>
        <div class="mt-4">
            <button type="button" wire:click="$set('modalEmpty', false)"
                class="inline-flex w-full justify-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-600 hover:bg-gray-700 sm:mt-0 sm:w-auto">Cerrar</button>
        </div>
    </div>
</x-modal.card>
