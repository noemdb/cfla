<div>
    <div class="border rounded p-2 border-gray-600">

        <div class="text-start">

            <div class="m-1">

                <x-card>
            
                    <x-slot name="title">
                        <div class="flex">
                            <div class="font-normal text-gray-500 px-2">Pregunta:</span> <span class="text-md ms-3 font-medium">{{$question->text}} </div> 
                            @if ($question->status_active)
                                <x-badge.circle negative icon="pause" class="ms-2 px-2 animate-pulse"/>
                            @endif
                        </div>
                    </x-slot>
            
                    <x-slot name="action">
                        <x-dropdown>
                            @if ($question->status_active)
                                <x-dropdown.item label="Desactivar pregunta" wire:click="setOffline({{$question->id}})"/>
                            @else
                                <x-dropdown.item label="Activar pregunta" wire:click="setOnline({{$question->id}})"/>
                            @endif                
                        </x-dropdown>
                    </x-slot> 
                    
                    <div class="text-right">        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-2 mb-4">
                            <div class="col-span-1">
                                <div class="text-sm font-normal border rounded border-gray-600 p-2">Ponderación: <span class=" font-bold">{{$question->weighting}}</span></div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-sm font-normal border rounded border-gray-600 p-2">Tiempo: <span class=" font-bold">{{$question->time}}</span></div>
                            </div>
                            <div class="col-span-1">
                                <div class="text-sm font-normal border rounded border-gray-600 p-2">T.Transcurrido: <span class=" font-bold">{{$question->time_elapsed ?? '0'}}</span></div>
                            </div>
                        </div> 
                    </div>
            
                </x-card>
            
            </div>            

        </div>

        @if ($question)
            @include('livewire.app.general.educational.competition.moderator.partials.answer')
        @endif

        <div id="alert-border-3" class="text-start flex items-center justify-center px-2 my-2 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-400 dark:bg-gray-800 dark:border-yellow-800" role="alert">
            <div class="ms-3 text-sm font-medium">
                Opciones:
            </div>
        </div>
        <div class="grid mb-2 border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 md:mb-4 md:grid-cols-2 bg-white dark:bg-gray-800">
            @forelse ($options as $item)
                @php 
                    $correct = ($item->status_option_correct && $question->status_over_time) ? "background-color:darkolivegreen;" : null;
                    $wrong = ($item->status_wrong_answer && $question->status_over_time) ? "background-color:red;" : null;
                @endphp
                <figure style="{{$correct}} {{$wrong}}" class="relative flex flex-col items-center justify-start p-8 text-center bg-white border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-ss-lg md:border-e dark:bg-gray-800 dark:border-gray-700">

                    @if (! $question->status_answer && $question->status_over_time && $question->status_active)

                        @php $seccions = $question->seccions @endphp
                        <div class="absolute top-2 right-2">

                            <x-dropdown>
                                <x-dropdown.header label="Puntuación">
                                    @foreach ($seccions as $seccion)
                                    <x-dropdown.item :label="$seccion->name" wire:click="answerScoreSeccion({{$seccion->id}},{{$item->id}})"/>
                                    @endforeach
                                </x-dropdown.header>
                                <x-dropdown.item separator/>

                                <x-dropdown.header label="Anulación">
                                    @foreach ($seccions as $seccion)
                                    <x-dropdown.item :label="$seccion->name" wire:click="answerNullSeccion({{$seccion->id}},{{$item->id}})"/>
                                    @endforeach
                                </x-dropdown.header>

                            </x-dropdown>
                            
                        </div>
                        
                    @endif                    
                
                    <blockquote class="max-w-2xl mx-auto mb-2 text-gray-500 lg:mb-2 dark:text-gray-400">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            @php $color = $colors[$loop->index]; @endphp
                            <x-badge.circle :$color lg label="{{$literal[$loop->index]}}"/>
                        </h3>
                    </blockquote>
                    <figcaption class="flex items-center justify-center">
                        <div class="space-y-0.5 font-medium dark:text-white text-left rtl:text-right ms-3">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{$item->text}}
                            </div>
                        </div>
                    </figcaption>
                </figure>
            @empty
                <li class="me-2">No hay opciones</li>
            @endforelse            
        </div>

        

    </div>
</div>
