<div>
    <div class="border rounded p-2 border-gray-600">

        <div class="text-start">

            <div class="flex justify-between">
                <div>Pregunta:</div>
                <div class="text-sm font-normal">Categoría: <span class=" font-bold">{{$question->category}}</span></div>
            </div>            
            
            <div id="alert-border-1" class="flex items-center justify-center p-4 mb-4 text-blue-800 border-t-4 border-blue-300 bg-blue-50 dark:text-blue-400 dark:bg-gray-800 dark:border-blue-800" role="alert">
                <div class="ms-3 text-sm font-medium">
                    {{$question->text}} 
                </div>
            </div>

        </div>
        <div id="alert-border-3" class="text-start flex items-center justify-center p-2 mb-2 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-400 dark:bg-gray-800 dark:border-yellow-800" role="alert">
            <div class="ms-3 text-sm font-medium">
                Opciones:
            </div>
        </div>
        <div class="grid mb-2 border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 md:mb-4 md:grid-cols-2 bg-white dark:bg-gray-800">
            @forelse ($options as $item)
                <figure class="flex flex-col items-center justify-start p-8 text-center bg-white border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-ss-lg md:border-e dark:bg-gray-800 dark:border-gray-700">                    
                    <blockquote class="max-w-2xl mx-auto mb-2 text-gray-500 lg:mb-2 dark:text-gray-400">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            @php $color=$colors[$loop->index]; @endphp
                            <x-badge.circle :$color lg label="{{$literal[$loop->index]}}" class=""/>
                        </h3>
                    </blockquote>
                    <figcaption class="flex items-center justify-center ">
                        <div class="space-y-0.5 font-medium dark:text-white text-left rtl:text-right ms-3">
                            <div class="text-sm text-gray-500 dark:text-gray-400 ">
                                {{$item->text}}
                            </div>
                        </div>
                    </figcaption>
                </figure>
            @empty
                <li class="me-2">No hay preguntas</li>
            @endforelse            
        </div>

        respuesta

        <livewire:app.general.educational.competition.dashboard.answer-component :question_id="$question->id"/>

    </div>
</div>
