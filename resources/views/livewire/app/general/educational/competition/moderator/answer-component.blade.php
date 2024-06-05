<div> 

    <x-card class="border border-gray-600 rounded" id="{{$question->id}}">

        <div class="text-2xl font-bold">Gestión de Respuestas</div>

        @if ($timeRemaining > 0)

            <div class="flex justify-center text-xl mb-2 underline border-t-2 border-gray-600">Cronómetro</div>

            <div class="flex justify-between items-center" >

                <div class="text-2xl border rounded bg-blue-900 p-2">
                    @if ($timerActive)
                        <span wire:poll.{{ $pollingInterval }}ms="decrementCount">{{ gmdate('i:s', $timeRemaining) }} <span class="text-sm font-light">[min:seg]</span></span>
                    @else
                        <span>{{ gmdate('i:s', $timeRemaining) }} <span class="text-sm font-light">[min:seg]</span></span>
                    @endif
                </div>        

                <x-button positive lg spinner label="Iniciar" wire:click="start"/>

            </div>

        @else

            <div class="text-sm  border-t-2 border-gray-600">Haz clic en la sección que ha respondido correctamente para registrar</div>

            <div class="flex justify-between bg-blue-900 rounded p-2">

                <div class="text-lg">{{$grado->name}}</div>

                <div class="flex justify-around">

                    @forelse ($seccions as $item)
                        <div class="pr-2 mr-2">
                            <x-button positive label="{{$item->name}}"/>        
                        </div>
                    @empty
                        <div>No hay secciones</div>
                    @endforelse

                </div>

            </div>
            
        @endif 
    
    </x-card>    

</div>
