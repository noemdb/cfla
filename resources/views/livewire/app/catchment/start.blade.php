<x-card class="p-4 cursor-pointer">

    <x-slot name="header">
        <h3 class=" text-indigo-950 bg-indigo-100 rounded-t-xl p-2 text-xl font-bold dark:text-neutral-200">
            <div class="h-full flex items-center">
                <x-icon name="menu" class="flex-none w-10 h-10" />
                <div class="flex-initial">Proceso Matriculación Escolar<br>2024 2025</div>
            </div>
        </h3>
    </x-slot>

    <div class="block rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700">
        <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex justify-center">
                {{-- <img class="rounded-t-lg w-24 h-24" src="{{asset('image/logo/report-payment.png')}}" alt="" /> --}}
                <x-icon name="menu" class="flex-none w-24 h-24" />
                <a href="#!">
                    <div class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100"></div>
                </a>
            </div>
            <div class="text-center  dark:text-neutral-200">
                <div class="text-3xl text-gray-900">Censo Escolar</div>
                <div class="text-md text-gray-600">Asistente</div>
                <div class="text-sm text-gray-400">Registra tu interes por pertenecer a nuestra institución educativa.</div>
            </div>
        </div>

        <div class="text-xs text-gray-200flex justify-between items-center bg-indigo-100 p-4 my-4 rounded-lg origin-bottom rotate-2">

            <div class="mt-2 text-xs text-gray-200 flex justify-between items-center">
                <span class="font-bold">Un proceso sencillo y rápido, adaptado para ti:</span>
                cfla.
            </div>

            <div class="mt-2">
                <p class="text-xs mb-4 text-neutral-600 dark:text-neutral-200">
                    Fase 1. Registro Inicial: Manifiesta tu interés para optar a este proceso de matriculación escolar.
                </p>
            </div>            

        </div>

        <div class="flex justify-center">
            <x-button label="Comenzar" url="{{env('APP_URL_SAEFL').'/general/catchment/index'}}" target="_blank" positive class="w-full bg-indigo-900 text-white"/>
        </div>

    </div>

</x-card>