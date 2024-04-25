<div>

    <div style="text-transform: uppercase;">
    
        <x-card>

            @slot('header')
            <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
                Nuestro cuerpo docente
            </h3>
            @endslot

            <div class="container-fluid mx-auto text-sm">
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-1">
                    @forelse ($profesors as $item)
                        <div class="flex content-between border-b-2 border-gray-200">
                            <div class="cursor-pointer" wire:click="showProfesor({{$item->id}})">{{$loop->iteration}}. {{$item->name.' '.$item->lastname}}</div>                        
                        </div>                    
                    @empty
                        <div>No hay docentes con carga académica</div>
                    @endforelse
                </div>            
            </div>    
        
        </x-card>

        <x-card>

            @slot('header')
            <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
                Nuestro Personal Administrativo
            </h3>
            @endslot

            <div class="container-fluid mx-auto text-sm">
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-1">
                    @forelse ($workers as $item)
                        <div class="flex content-between border-b-2 border-gray-200">
                            <div>{{$loop->iteration}}. {{$item->fullname}}</div>                        
                        </div>                    
                    @empty
                        <div>No hay personal administrativo</div>
                    @endforelse
                </div>            
            </div>    
        
        </x-card>

        @includeWhen($modalShow,'livewire.home.workers.modal.profesor')

    </div>

</div>
