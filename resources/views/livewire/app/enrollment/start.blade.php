<div class="p-2 border rounded shadow">
    <x-card class="p-4">

        <x-slot name="header">
            <h3 class=" text-green-950 bg-green-100 mt-2 p-2 text-xl font-bold dark:text-neutral-200">
                <div class="h-full flex items-center">
                    <x-icon name="document-text" class="flex-none w-10 h-10" />
                    <div class="flex-initial">Sólicitud actualización de matrícula.</div>
                </div>
            </h3>
            <span class="text-xs text-gray-400 text-right font-bold dark:text-neutral-200">Período Escolar 2023 -
                2024</span>
        </x-slot>

        <x-input label="Cédula del Representante" placeholder="Sólo números" corner-hint="Ej: 12345678" wire:model="ci">
            <x-slot name="append">
                <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                    <x-button class="h-full rounded-r-md" icon="search" primary flat squared wire:click="search" />
                </div>
            </x-slot>
        </x-input>

        <hr class="my-2">

        <div class="text-xs text-gray-200flex justify-between items-center">
            La solicitud de actualización de matrícula es un documento que se utiliza para informar de los cambios que
            se han producido en los datos de un estudiante. Estos cambios pueden referirse a la dirección, el teléfono,
            la situación familiar o cualquier otra información relevante.
        </div>

    </x-card>
</div>