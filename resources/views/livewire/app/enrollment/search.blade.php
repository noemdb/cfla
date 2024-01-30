<x-modal.card blur wire:model="modalSearch" max-width="2xl">
    <x-slot name="header">
        <h3 class=" rounded-t-lg text-green-950 bg-primary-200 text-xl font-bold dark:text-neutral-200">
            <div class="h-full flex items-center">
                <x-icon name="document-text" class="flex-none w-10 h-10" />
                <div class="flex-initial">Actualización de Matrícula.</div>
            </div>
        </h3>
    </x-slot>

    <x-card class="border-0 shadow-none">

        <x-slot name="header">
            {{-- <div class="flex justify-center bg-gray-200 rounded-3xl">
                <img class="rounded-t-lg w-16 h-16" src="{{asset('image/highlighted/matricula.png')}}" alt="" />
            </div> --}}
            <div class="text-start">
                <h3 class=" text-green-950 opacity-50 text-sm dark:text-neutral-200">
                    Ingrese número de cédula.
                </h3>
            </div>
        </x-slot>        

        <div class="flex justify-between items-end">
            <div class="grow 0 pr-2"><x-input class="h-8" label="Cédula del estudiante" placeholder="Sólo números" corner-hint="Ej: 12345678" right-icon="calculator" wire:model="ci" /></div>
            <div class="grow-0 pl-2 items-end"><x-button class="h-8 p-0.5 border rounded-md shadow" icon="search" primary flat squared wire:click="search" /></div>
        </div>

        <hr class="my-2">

        <div class="text-xs text-gray-400 dark:text-neutral-200 md:p-4 bg-gray-100 rounded">                
            <span class="font-bold">Ingresar la cédula de identidad del estudiante. </span>
            En el primer paso, se debe ingresar la cédula de identidad del estudiante, este paso es necesario para identificarlo.
        </div>        

    </x-card>
</x-modal.card>