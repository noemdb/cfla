<div>

    <div class="m-1 w-full">

        <div class="flex justify-between border-b-2 border-r-2 border-gray-200 rounded h-full">
            <div class="flex-1">

                <h5 class="mb-2 px-2 text-4xl font-bold tracking-tight text-gray-900 dark:text-white">                
                    <div class="text-green-950">
                        <small class="block text-sm font-bold text-green-900">Competición activa: </small>
                        {{$competition->name}} 
                    </div> 
                </h5>

                <div class="text-sm font-light">
                    <div class="text-right">        
                        <div x-data="{ open: false }">
                            <button  class="text-sm font-light" @click="open = ! open">Leer más</button>         
                            <div x-show="open" @click.outside="open = false">
                                <p class="text-start mb-1 font-normal text-gray-700 dark:text-gray-400 ">
                                    {{$competition->description}}
                                    
                                </p>
                                <p class="text-start mb-1 font-normal text-gray-700 dark:text-gray-400">
                                    {{$competition->motive}}
                                </p>
                                <span class="font-semibold text-gray-700 dark:text-gray-400 text-sm">[Fecha: {{$competition->date}}]</span>
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

    </div>

</div>