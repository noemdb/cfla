<x-modal.card blur wire:model="modalSearch" max-width="2xl">
    <x-slot name="header">
        <h3 class=" rounded-t-lg text-green-950 bg-green-100 text-xl font-bold dark:text-neutral-200">
            <div class="h-full flex items-center">
                <x-icon name="document-text" class="flex-none w-10 h-10" />
                <div class="flex-initial">Reporte de Pago</div>
            </div>
        </h3>
    </x-slot>

    <x-card class="border-0 shadow-none">

        <x-slot name="header" class=" border-b-2">
            <div class="text-center">
                <div class="font-bold">Asistente</div>
                <div class="py-0 my-0 text-green-950 opacity-50 text-sm dark:text-neutral-200">
                    Período Escolar 2024 - 2025
                </div>
            </div>            
        </x-slot>
        

        <div class="flex justify-between items-end">
            <div class="grow 0 pr-2"><x-input class="h-8" label="CI. Representante" placeholder="Sólo números" corner-hint="Ej: 12345678" wire:model="ci" /></div>
            <div class="grow-0 pl-2"><br><x-button class="h-8 p-0.5 border rounded-md shadow" icon="search" primary flat squared wire:click="search" /></div>
        </div>

        <hr class="my-2">

        <div class="text-xs text-gray-400 dark:text-neutral-200 md:p-4 bg-gray-100 rounded">
            El número de cédula de identidad se solicita para verificar la identidad del representante y asociar su reporte de pago.
            EL <span class="text-green-900 font-bold">SAEFL</span> buscará en su base de datos, y si encuentra una coincidencia, ejecutará los siguientes pasos del asistente.
        </div>        

    </x-card>
</x-modal.card>