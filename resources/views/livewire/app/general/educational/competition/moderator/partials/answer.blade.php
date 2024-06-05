<div class="p-2">

    <x-card class="border border-gray-600 rounded shadow" id="{{$question->id}}">

        <div class="text-2xl font-bold">Gestión de Respuestas</div>

        @if (! $question->status_over_time)

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-2 mb-4">
                <div class="col-span-4 text-center">
                    <div class=" font-bold text-xl mb-2 border-t-2 border-gray-600">Cronómetro</div>
                </div>
                <div class="col-span-2">
                    <div class="text-sm font-normal border rounded border-gray-600 p-2">
                        <div class="text-2xl font-extrabold">
                            @if ($timerActive)
                                <span wire:poll.{{ $pollingInterval }}ms="decrementCount">{{ gmdate('i:s', $timeRemaining) }} <span class="text-sm font-light">[min:seg]</span></span>
                            @else
                                <span>{{ gmdate('i:s', $timeRemaining) }} <span class="text-sm font-light">[min:seg]</span></span>
                            @endif
                        </div> 
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="text-sm font-normal">
                        @if ($question->status_active)
                            @if ($timerActive)
                                <x-button negative lg label="Pausar" wire:click="pause" class="w-full"/>                    
                            @else
                                <x-button positive lg label="Iniciar" wire:click="start" class="w-full"/>
                            @endif
                        @else
                            <div class="flex justify-center items-center">
                                La pregunta no está activa                               
                            </div>
                        @endif                        
                    </div>
                </div>
            </div>

        @else

            <div class="text-start text-sm border-t-2 py-2 my-2 border-gray-600">
                @if ($question->status_answer)
                    <div> Repuesta registrada. Puntos adjudicados </div> 
                @else
                    <div> Tiempo finalizado: <span class="font-normal">Haz clic en la sección que ha respondido correctamente para registrar y adjudicar puntaje</span> </div>
                @endif
            </div>

            <div class="bg-blue-900 rounded py-2 my-2">

                <div class="text-lg">{{$grado->name}}</div>

                <div class="">

                    @if ($question->status_answer)
                        <div class="flex justify-center items-center">
                            @php $label = ($answer) ? $answer->seccion->name : null; @endphp
                            Puntaje adjudicado[{{$question->weighting}}] a la sección: <x-badge outline lg positive label="{{$label}}" class="mx-2" /> 
                        </div>
                    @else

                        <div class="flex justify-center items-center">
                            @forelse ($seccions as $item)
                                <div class="pr-2 mr-2">
                                    <x-button positive label="{{$item->name}}" wire:click="save({{$item->id}})"/>
                                </div>
                            @empty
                                <div>No hay secciones</div>
                            @endforelse
                        </div>

                        @if (! $seccions->empty())
                            @php $seccion = $seccions->first(); @endphp
                            <div> <x-button positive label="Correcto" wire:click="save({{$seccion->id}})"/></div>
                        @else
                            <div>No hay secciones</div>
                        @endif
                        
                    @endif

                </div>

            </div>
            
        @endif 
    
    </x-card>   

</div>