<div class="bg-green-100 border-t-4 border-cyan-600 p-2 mt-2">
    
    <div class="flex justify-center m-2 w-full">
        
        <div class="text-center w-full" >
            @if ($question) 
                <div class="">

                    <div class="text-gray-800">
                
                        <div class=" font-bold text-xl mb-2 ">
                            Cronómetro
                        </div>
                        {{-- <small class="text-gray-400">[Cuenta regresiva]</small> --}}
                    
                        <div class="">
                            @if ($timeRemaining >= 0)
                                <h1 class="text-8xl font-extrabold">{{$timeRemaining}}</h1>
                            @else
                                <div x-show="showMessage" class="message">
                                    <div class="text-sm font-light">Tiempo finalizado... actualizando la información</div>
                                </div>
                            @endif
                        </div>
                
                    </div>
                    
                    <div class="text-sm font-bold text-gray-400">[Seg]</div>                    
                
                </div>
            @else
                <div class="text-gray-400">Espere a que se establezca la pregunta activa</div>
            @endif
        </div>
        
    </div>
    
</div>