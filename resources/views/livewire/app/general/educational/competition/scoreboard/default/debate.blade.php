<div class="m-1 w-full border-t-2 border-gray-200">

    <x-card>

        <x-slot name="title">
            <h5 class="mb-2 px-2 text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{$debate->name}}
                {{-- <small class="text-sm font-light">Debate activo:</small> --}}
            </h5>
        </x-slot>

        <x-slot name="action">
            <x-dropdown>
                <x-dropdown.item label="Acerca de ..." />
            </x-dropdown>
        </x-slot> 
        
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

    </x-card>

</div>