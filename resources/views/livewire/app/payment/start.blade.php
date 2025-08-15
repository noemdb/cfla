<x-card class="p-4 cursor-pointer" wire:click="setStart">

    <x-slot name="header">
        <h3 class=" text-green-950 bg-green-100 rounded-t-xl p-2 text-lg md:text-xl font-bold dark:text-neutral-200">
            <div class="h-full flex items-center">
                <x-icon name="document-text" class="flex-none w-10 h-10" />
                <div class="flex-initial">Reporta tus pagos aquí.</div>

            </div>
        </h3>
    </x-slot>

    <div
        class="block rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700">
        <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex justify-center">
                <img class="rounded-t-lg w-24 h-24" src="{{ asset('image/logo/report-payment.png') }}" alt="" />
                <a href="#!" wire:click="setStart">
                    <div
                        class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100">
                    </div>
                </a>
            </div>
            <div class="text-center  dark:text-neutral-200">
                <div class="text-lg md:text-2xl leading-tight text-gray-900 border-b-2 border-b-gray-400">Reporte de
                    pago </div>
                <div class="text-md text-gray-600">Asistente</div>
                <div class="text-sm text-gray-400">Reporta tus transferencias, pago movìl y/o depósitos siguiendo este asistente.</div>
            </div>
        </div>

        <div
            class="text-xs text-gray-200flex justify-between items-center bg-green-100 p-4 my-4 rounded-lg origin-bottom">

            <div class="text-sm md:text-md lg:text-lg xl:text-xl">
                <span class="font-bold">Un proceso sencillo y rápido:</span>
                Usando esta opción es necesaria la verificación, concialición y registro de los datos ingresados,
                estas
                actividades cumplen con un lapso de tiempo (uno (1) o dos (2) días) para ser correctamente
                procesados en
                el <span class=" text-green-950 font-bold">SAEFL</span>.
            </div>

            {{-- <div class="mt-2 text-xs text-gray-200flex justify-between items-center">
                <span class="font-bold">Un proceso sencillo y rápido:</span>
                cfla.
            </div>

            <div class="mt-2">
                <p class="text-xs mb-4 text-neutral-600 dark:text-neutral-200">
                    Usando esta opción es necesaria la verificación, concialición y registro de los datos ingresados,
                    estas
                    actividades cumplen con un lapso de tiempo (uno (1) o dos (2) días) para ser correctamente
                    procesados en
                    el <span class=" text-green-950 font-bold">SAEFL</span>.
                </p>
            </div>             --}}

        </div>

        <div class="flex justify-center">
            <x-button label="Comenzar" wire:click="setStart" positive class="w-full bg-green-900 text-white" />
        </div>

    </div>

</x-card>
