<div class="p-2">

    <x-card class="border border-gray-600 rounded shadow" id="{{ $question->id }}">

        <div class="text-2xl font-bold">Gestión de Respuestas</div>

        @if ($question->status_active)
            @if (!$question->status_over_time)

                <div class="font-bold text-xl mt-2 border-t-2 border-gray-600">Cronómetro</div>
                <div class="grid grid-cols-6 sm:grid-cols-6 gap-2 py-2 mb-4">

                    <div class="col-span-3 sm:col-span-2">
                        <div class="text-sm font-normal border rounded border-gray-600 p-2">
                            <div class="text-2xl font-extrabold">
                                @if ($timerActive)
                                    <span
                                        wire:poll.{{ $pollingInterval }}ms="decrementCount">{{ gmdate('i:s', $timeRemaining) }}
                                        <span class="text-sm font-light">[min:seg]</span></span>
                                @else
                                    <span>{{ gmdate('i:s', $timeRemaining) }} <br> <span
                                            class="text-sm font-light">[min:seg]</span></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-span-3 sm:col-span-4">
                        <div class="text-sm font-normal flex">
                            @if ($question->status_active)
                                @if ($timerActive)
                                    <x-button warning lg label="Pausar" wire:click="pause" class="w-1/2 ml-1 h-full" />
                                @else
                                    <x-button positive lg label="Iniciar" wire:click="start"
                                        class="w-1/2 ml-1 h-full" />
                                @endif
                                <x-button negative lg label="Finalizar" wire:click="finished"
                                    class="w-1/2 ml-1 h-full" />
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
                        <div> Tiempo finalizado: <span class="font-normal">Haz clic en la sección que ha respondido
                                correctamente para registrar y adjudicar puntaje</span> </div>
                    @endif
                </div>

                <div class="bg-blue-900 rounded py-2 my-2">

                    <div class="text-lg">

                        <x-badge lg gray label="{{ $grado->name }}" class="p-4 text-2xl font-bold" />

                        {{-- modificacion de la Mecanica Asignacion de punto por seccion --}}
                        @if ($answer)
                            @php $seccion_name = ($answer->seccion) ? $answer->seccion->name : null;@endphp
                            <x-badge lg positive label="{{ $seccion_name }}" class="p-4 text-3xl font-bold" />
                        @endif
                    </div>

                    <div class="">

                        @if ($question->status_answer)
                            <div class="flex justify-center items-center">
                                @php $score = ($answer->score) ? $answer->score : 0 ; @endphp
                                Puntaje adjudicado sección: <x-badge outline lg positive label="{{ $score }} pts"
                                    class="mx-2 text-xl" />

                                @if ($score)
                                    <span class="p-2 bg-yellow-600 border border-gray-400 rounded cursor-pointer"
                                        wire:click="setPoin({{ $answer->id }},0)">Anular</span>
                                @else
                                    <span class="p-2 bg-green-600 border border-gray-400 rounded cursor-pointer"
                                        wire:click="setPoin({{ $answer->id }},{{ $question->weighting }})">Puntuar</span>
                                @endif

                            </div>
                        @else
                            @if ($question->exist_option_correct)

                                {{-- Mecanica Asignacion de punto por grado --}}
                                {{-- <x-button lg positive label="Respuesta Correcta" wire:click="saveAnswer({{$grado->id}},true)"/> --}}
                                {{-- <x-button lg negative label="Respuesta Incorrecta" wire:click="saveAnswer({{$grado->id}},false)"/> --}}

                                {{-- modificacion de la Mecanica Asignacion de punto por seccion --}}
                                <div class="pb-2 border-gray-400 dark:border-gray-700">Seleccione la sección que
                                    respondió correctamente</div>
                                @php $seccions = $grado->activeSeccions() @endphp
                                <div class="flex justify-around">
                                    @foreach ($seccions as $item)
                                        <x-button lg positive label="Sección {{ $item->name }}"
                                            wire:click="saveAnswerSeccion({{ $item->id }},true)" class="p-1" />
                                    @endforeach
                                </div>
                            @else
                                <div class="text-red-800 font-semibold text-xl">No hay una respuesta correcta registrada</div>
                            @endif

                        @endif

                    </div>

                </div>

            @endif
        @else

            <div>No hay pregunta activa</div>

        @endif



    </x-card>

</div>
