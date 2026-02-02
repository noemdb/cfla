<div>

    <x-card class="p-2">

        <x-slot name="header">
            <h3 class=" text-indigo-950 bg-indigo-100 rounded-t-xl p-2 text-lg md:text-xl font-bold dark:text-neutral-200">
                <div class="h-full flex items-center gap-2">
                    <x-icon name="bars-3" class="flex-none w-8 h-8" />
                    <div class="flex-initial">Proceso Matriculación Escolar 2024 2025</div>
                </div>
            </h3>
        </x-slot>
    
        <div class="block rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700">
            <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
                <div class="flex justify-center">
                    {{-- <img class="rounded-t-lg w-24 h-24" src="{{asset('image/logo/report-payment.png')}}" alt="" /> --}}
                    <x-icon name="bars-3" class="flex-none max-w-14 max-h-14" />
                    <a href="#!">
                        <div class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100"></div>
                    </a>
                </div>
                <div class="text-center dark:text-neutral-200">
                    <div class="text-lg md:text-xl text-gray-900">Censo Escolar</div>
                    <div class="text-md text-gray-600">Asistente</div>
                    <div class="text-sm text-gray-400">Registra tu interes por pertenecer a nuestra institución educativa.</div>
                </div>
            </div>
    
            <div class="text-xs text-gray-200flex justify-between items-center bg-indigo-100 p-4 my-4 rounded-lg origin-bottom rotate-2">
    
                <div class="mt-2 text-xs text-gray-600 flex justify-between items-center">
                    <span class="font-bold">Un proceso adaptado para ti.</span>
                </div>
    
                <div class="mt-1">
                    <p class="text-xs text-neutral-600 dark:text-neutral-200">
                        <b>Fase 1. Registro Inicial:</b> Manifiesta tu interés para optar a este proceso de matriculación escolar. <br>
                        <div>
                            Sean bienvenidos a nuestro proceso de Matriculación Escolar 2024-2025. Nos llena de alegría y compromiso recibirles en nuestra institución, donde la educación trasciende las aulas y se convierte en un camino de formación integral.
                        </div>
                        {{-- Sean bienvenidos a nuestro proceso de Matriculación Escolar 2024-2025. Nos llena de alegría y compromiso recibirles en nuestra institución, donde la educación trasciende las aulas y se convierte en un camino de formación integral. --}}
                    </p>
                </div>            
    
            </div>
    
            <div class="flex justify-center">
                <x-button href="{{env('APP_URL_SAEFL').'/general/catchments/index'}}" target="_blank" label="Comenzar" positive class="w-full bg-indigo-900 text-white"/>
                {{-- <x-button label="Comenzar" url="{{env('APP_URL_SAEFL').'/general/catchment/index'}}" target="_blank" positive class="w-full bg-indigo-900 text-white"/> --}}
            </div>
    
        </div>
    
    </x-card>

</div>