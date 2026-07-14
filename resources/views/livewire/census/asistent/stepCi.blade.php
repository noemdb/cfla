<div class="max-w-sm mx-auto">
    <h2 class="mb-2 text-xl font-bold text-white">Consultar</h2>

    <div class="grid md:grid-cols-1">
        @if ($is_regular)
            <div class="p-4 mb-4 text-sm text-red-100 bg-red-900 border border-red-700 rounded-xl shadow-lg animate-pulse"
                role="alert">
                <div class="flex items-center mb-2">
                    <x-icon name="exclamation-circle" class="w-5 h-5 mr-2 text-red-200" />
                    <span class="text-lg font-black uppercase">Aviso Importante</span>
                </div>
                <p class="font-bold leading-relaxed">
                    Este proceso de proceso es exclusivo para estudiantes de nuevo ingreso. Los estudiantes regulares no
                    requieren realizar este registro.
                </p>
            </div>
        @endif

        <p class="mb-4 text-gray-400">Por favor, ingresa tu cédula de identidad para consultar el estatus del censo de
            tus representados, o registrar uno nuevo</p>

        <div class="mb-4 space-y-2">
            <x-input wire:model="representant_ci" label="Cédula de Identidad" placeholder="Ej: 12345678"
                right-icon="identification" />
        </div>

        <div class="mb-2 space-y-2">

            <x-button wire:click="searchByCi" xl positive label="Consultar"
                class="w-full !bg-green-800 hover:!bg-green-900 border-2 !border-green-900 shadow-lg" type="button" />
        </div>
    </div>
</div>
