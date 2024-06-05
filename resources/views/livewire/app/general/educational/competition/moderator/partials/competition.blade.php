<div class="m-1 w-full">

    <x-card>

        <x-slot name="title">
            <div class="flex justify-center items-center mb-2 px-2">            
                <h5 class="grow ms-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{$competition->name}}
                </h5>

                @if ($competition->status_active)
                    <x-badge.circle negative icon="pause" class="ms-2 px-2 animate-pulse"/>
                @endif
            </div>
        </x-slot>

        <x-slot name="action">
            <x-dropdown>
                @if ($competition->status_active)
                    <x-dropdown.item label="Desactivar competición" wire:click="setOffline({{$competition->id}})"/>
                @else
                    <x-dropdown.item label="Activar competición" wire:click="setOnline({{$competition->id}})"/>
                @endif                
                <x-dropdown.item label="Planilla de Resultados" />
            </x-dropdown>
        </x-slot> 
        
        <div class="text-right">        
            <div x-data="{ open: false }">
                <button  class=" font-bold border-gray-200" @click="open = ! open">Leer más</button>         
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

    </x-card>

</div>