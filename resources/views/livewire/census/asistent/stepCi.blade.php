<div class="max-w-sm mx-auto">
    <h2 class="mb-2 text-3xl font-bold text-white">Consultar</h2>

    <div class="grid md:grid-cols-1">
        <p class="mb-4 text-gray-400">Por favor, ingresa tu cédula de identidad para consultar el estatus del censo de
            tus representados, o registrar uno nuevo.</p>

        <div class="mb-4 space-y-2">
            <x-input wire:model="representant_ci" label="Cédula de Identidad" placeholder="Ej: 12345678"
                right-icon="identification" />
        </div>

        <div class="mb-2 space-y-2">
{{--  

<x-button wire:click="searchByCi" xl positive label="Consultar" class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" type="button" />

--}}
        </div>
    </div>
</div>
