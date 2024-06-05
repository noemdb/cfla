<div>
    <div class="m-1 w-full" wire:poll.1s="updateDebate({{$competition->id}})">
    {{-- <div class="m-1 w-full"> --}}

        @if ($debate)

            <div class="flex justify-between border-b-2 border-l-2 rounded border-gray-200" >

                <div class="flex-1">

                    <h5 class="mb-2 px-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">                
                        <div class="text-green-950">
                            <small class="block text-sm font-bold text-green-900">Área de Formación activo: </small>
                            <small class="text-gray-200">{{$debate->id}}.</small> {{$debate->name}} 
                        </div> 
                    </h5>
                    <div class="text-8xl text-green-200 font-bold">{{$debate->grado->name}}</div> 

                    <div class="text-sm font-light">
                        <div class="text-right">        
                            <div x-data="{ open: false }">
                                <button  class="text-sm font-light" @click="open = ! open">Leer más</button>         
                                <div x-show="open" @click.outside="open = false">
                                    <p class="text-start mb-1 font-normal text-gray-700 dark:text-gray-400 ">
                                        {{$debate->description}}
                                        
                                    </p>
                                </div>
                            </div> 
                        </div>
                    </div>

                </div>

                <div class="flex-none">
                    <x-dropdown>
                        <x-dropdown.item label="Planilla de Resultados" />
                    </x-dropdown>
                </div>

            </div>

        @else
            <div>Espere... No hay un debate activo</div>
        @endif       

    </div>
</div>
