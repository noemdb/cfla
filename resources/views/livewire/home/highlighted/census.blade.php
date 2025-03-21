
<x-card class="bg-white rounded m-2 h-full">

    @slot('header')
    <h3 class="text-green-950 bg-blue-100 mt-2 p-2 text-xl font-bold dark:text-neutral-200">
        <div class="h-full flex items-center">
            <x-icon name="menu" class="flex-none w-10 h-10 mb-4" />
            <div class="flex-initial">Censo Escolar 25-26 - Asistente</div>                    
        </div>
    </h3>
    @endslot

    <div
        class="h-full border-t-2 block rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700">
        <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex justify-center">
                {{-- <img class="rounded-t-lg w-24 h-24" src="{{asset('image/logo/report-payment.png')}}" alt="" /> --}}

                

                <div class="grid place-items-center h-24 ">
                    <p class="text-center bg-gray-200 rounded-full p-4">
                        <x-icon name="document-report" class="w-24 h-24" />
                    </p>
                </div>

                
                <a href="#!">
                    <div
                        class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100">
                    </div>
                </a>                
            </div>
            <div class="text-center  dark:text-neutral-200 border-t-2 mt-6 pt-6">
                <div class="text-3xl text-gray-900">El primer paso hacia una educación de excelencia.</div>
                <div class="text-md text-gray-600">1ra Jornada desde 1 hasta el 10 de abril 2025, hora 2pm.</div>
                {{-- <div class="text-sm text-gray-400">Reporta tus transferencias, pago movìl y/o depósitos siguiendo este asistente.</div> --}}
            </div>
        </div>
        <div class="p-6">
            <p class="text-md mb-4 text-neutral-600 dark:text-neutral-200">
                Nos complace poder ofrecerles a sus hijos la oportunidad de formar parte de nuestra comunidad educativa, que está comprometida con la excelencia académica y el desarrollo integral de los estudiantes.🙌🏻
            </p>
            <div class="flex justify-end">
                <x-button positive label="Comenzar" class="w-full" :href="route('census')"/>
            </div>
        </div>
    </div>

</x-card>