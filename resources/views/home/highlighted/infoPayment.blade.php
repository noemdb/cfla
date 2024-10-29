<x-card class="h-full">

    @slot('header')
    <h3 class="text-green-950 bg-primary-100 rounded-t-xl p-2 text-lg md:text-xl font-bold dark:text-neutral-200">
        <div class="h-full flex items-center">
            <x-icon name="credit-card" class="flex-none w-10 h-10" />
            <div class="flex-initial">Canales de Pago.</div>
        </div>
    </h3>
    @endslot

    <div
        class="h-full block bg-white dark:bg-neutral-700">
        <div class="relative overflow-hidden bg-cover bg-no-repeat" data-te-ripple-init data-te-ripple-color="light">
            <div class="flex justify-center">
                {{-- <img class="rounded-t-lg w-24 h-24" src="{{asset('image/logo/btnpayment.png')}}" alt="" /> --}}
                <a href="#!">
                    <div
                        class="absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-[hsla(0,0%,98%,0.15)] bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100">
                    </div>
                </a>
            </div>
            {{-- <div class="text-center  dark:text-neutral-200"> --}}
                {{-- <div class="text-lg md:text-xl text-gray-900 dark:text-neutral-200">Canales de pagos activos</div> --}}
                {{-- <div class="text-md text-red-600 font-bold dark:text-neutral-200"> TITULAR: Asociación de terciarios capuchinos de Venezuela C.A  </div> --}}
                {{-- <div class="text-md text-gray-600 dark:text-neutral-200">Asistente</div> --}}
                {{-- <div class="text-sm text-gray-900 dark:text-neutral-200">Verificación, concialición y registro automático.</div> --}}
                {{-- <div class="text-sm text-orange-400 font-bold dark:text-neutral-200">Procesado por el Consorcio Credicard.</div> --}}
            {{-- </div> --}}
        </div>
        <div class="text-xs text-gray-200flex justify-between items-center bg-info-100 p-4 my-4 rounded-lg origin-bottom">
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200">
                <span class="font-bold">TITULAR: Asociación de terciarios capuchinos de Venezuela C.A </span>                
                <span>
                    BANCO DEL TESORO - Bs<br>
                    CUENTA CORRIENTE<br>
                    RIF: J000570167<br>
                    0163-0246-51-2463000279<br>
                </span>
            </div>
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <span>
                    BANCARIBE - Bs<br>
                    CUENTA CORRIENTE<br>
                    RIF: J000570167<br>
                    0114-0270-40-2700047221<br>
                </span>
            </div>            
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <span>
                    BANCARIBE - Divisas<br>
                    CUENTA CORRIENTE<br>
                    RIF: J000570167<br>
                    0114-0270-40-2704003083<br>
                </span>
            </div>            
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <span>
                    PAGO MÓVIL<br>
                    Banco del Tesoro (0163)<br>
                    RIF: J000570167<br>
                    04245891682<br>
                </span>
            </div>            
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <span>
                    EFECTIVO<br>
                    En las oficinas administrativas de la institución. 
                </span>
            </div>  
            <div class="text-xs mb-4 text-neutral-600 dark:text-neutral-200 border-t">
                <span>
                    PUNTO DE VENTA <br>
                    En la taquilla de Administración. 
                </span>
            </div>           
        </div>
    </div>

</x-card>

{{--  --}}