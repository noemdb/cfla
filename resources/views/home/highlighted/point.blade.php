<x-card class=" bg-gray-200 rounded shadow m-2">

    @slot('header')
    <h3 class="text-green-950 bg-green-100 mt-2 p-2 text-xl font-bold dark:text-neutral-200">
        <div class="h-full flex items-center">
            <x-icon name="credit-card" class="flex-none w-10 h-10" />
            <div class="flex-initial">Punto de Venta Virtual</div>
        </div>
    </h3>
    @endslot

    <div
        class="block rounded-lg bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700">
        <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex justify-center">
                <img class="rounded-t-lg w-24 h-24" src="{{asset('image/logo/btnpayment.png')}}" alt="" />
                <a href="#!">
                    <div
                        class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100">
                    </div>
                </a>
            </div>
            <div class="text-center  dark:text-neutral-200">
                <div class="text-3xl text-gray-900 dark:text-neutral-200">TPV Botón de Pago CFLA</div>
                <div class="text-md text-red-600 font-bold dark:text-neutral-200">PAGO DIRECTO</div>
                <div class="text-md text-gray-600 dark:text-neutral-200">Asistente</div>
                <div class="text-sm text-gray-900 dark:text-neutral-200">Verificación, concialición y registro automático.</div>
                <div class="text-sm text-orange-400 font-bold dark:text-neutral-200">Procesado por el Consorcio Credicard
                    .</div>
            </div>
        </div>
        <div class="p-6">
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200">
                Seguimos trabajando en la mejora de nuestros servicios, hemos agregado una nueva opción de pago aún más rápida a través de esta conexión.
            </div>
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200">
                <span class="font-bold">Tarjetas de débito aceptadas: </span>                
                <span>Bancaribe, Banco del Tesoro, Mi Banco, Bancamiga, Bancrecer y BANFANB.</span>
            </div>
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200">
                <span class="font-bold">Verificación, concialición y registro automático: </span>                
                <span>Tus pagos son registrados automaticamente en el <span class=" text-green-950 font-bold">SAEFL</span>.</span>
            </div>
            <div class="flex justify-end">
                <x-button positive label="Comenzar" />
            </div>
        </div>
    </div>

</x-card>