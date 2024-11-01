<x-card class="h-full">

    @slot('header')
    <h3 class="text-green-950 bg-primary-100 rounded-t-xl p-2 text-lg md:text-xl font-bold dark:text-neutral-200">
        <div class="h-full flex items-center">
            <x-icon name="document-text" class="flex-none w-10 h-10" />
            <div class="flex-initial">Canales de Pago.</div>
        </div>
    </h3>
    @endslot

    <div class="h-full block bg-white dark:bg-neutral-700">

        {{-- <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex items-center justify-center">
                <div class="w-24 h-24 bg-blue-200 rounded-full">
                    <div class="text-center">
                        <x-icon name="document-text" class="flex-none h-14 w-14" />
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="grid place-items-center h-24 ">
            <p class="text-center bg-gray-200 rounded-full p-4">
                <x-icon name="document-text" class="flex-none h-14 w-14" />
            </p>
        </div>

        <div class="font-bold text-lg md:text-xl border-b-2 border-gray-400">TITULAR: Asociación de terciarios capuchinos de Venezuela C.A </div> 

        <div class="text-xs text-gray-200flex justify-between items-center bg-info-100 p-2 my-2 rounded-lg origin-bottom">
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200">
                <div class="text-sm md:text-sm lg:text-md xl:text-xl capitalize">
                    <strong>Baco del Tesoro - Bs</strong>
                    <br>
                    Cuenta Corriente<br>
                    RIF: J000570167<br>
                    0163-0246-51-2463000279<br>
                </div>
            </div>
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <div class="text-sm md:text-sm lg:text-md xl:text-xl capitalize">
                    <strong>Bancaribe - Bs</strong>
                    <br>
                    Cuenta Corriente<br>
                    RIF: J000570167<br>
                    0114-0270-40-2700047221<br>
                </div>
            </div>            
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <div class="text-sm md:text-sm lg:text-md xl:text-xl capitalize">
                    <strong>Bancaribe - Divisas</strong>
                    <br>
                    Cuenta Corriente<br>
                    RIF: J000570167<br>
                    0114-0270-40-2704003083<br>
                </div>
            </div>            
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <div class="text-sm md:text-sm lg:text-md xl:text-xl capitalize">
                    <strong>Pago Móvil</strong>
                    <br>
                    Banco del Tesoro (0163)<br>
                    RIF: J000570167<br>
                    04245891682<br>
                </div>
            </div>            
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <div class="text-sm md:text-sm lg:text-md xl:text-xl capitalize">
                    <strong>Efectivo</strong>
                    <br>
                    En las oficinas administrativas de la institución. 
                </div>
            </div>  
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <div class="text-sm md:text-sm lg:text-md xl:text-xl capitalize">
                    Punto de Venta <br>
                    En la taquilla de Administración. 
                </div>
            </div>           
        </div>
    </div>

</x-card>

{{--  --}}