<div class="text-start font-normal text-gray-500 px-2">
    

    <span class="font-light">Pregunta activa:</span>  
    <div class="text-xl ms-3">
        <span class="font-bolt">{{$question->text}}</span>
    </div>
    <div>
        Ponderación: <span class="font-bold">{{$question->weighting}}</span> || Tiempo: <span class="font-bold">{{$question->time}}</span>
    </div>

    {{-- <div class="text-end">
        Ponderación: <span class=" font-bold">{{$question->weighting}}</span> || Tiempo: <span class=" font-bold">{{$question->time}}</span>
    </div> --}}

    <div class="p-2">
        <div class="text-center" >
            @if ($question) 
                @include('livewire.app.general.educational.competition.scoreboard.partials.timer')
            @else
                <div>No hay pregunta activa</div>
            @endif
        </div>
    </div>

<div id="alert-border-3" class="text-start flex items-center justify-center px-2 my-2 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-400 dark:bg-gray-800 dark:border-yellow-800" role="alert">
    <div class="ms-3 text-sm font-medium">
        Opciones:
    </div>
</div>
<div class="grid mb-2 border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 md:mb-4 md:grid-cols-2 bg-white dark:bg-gray-800">
    @forelse ($options as $item)
        <figure  {{ ($item->status_option_correct && $question->status_over_time) ? "style=background-color:#ccc" : null}} class="flex flex-col items-center justify-start p-8 text-center bg-white border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-ss-lg md:border-e dark:bg-gray-800 dark:border-gray-700">                    
            <blockquote class="max-w-2xl mx-auto mb-2 text-gray-500 lg:mb-2 dark:text-gray-400">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    @php $color=$colors[$loop->index]; @endphp
                    <x-mini-badge :$color lg label="{{$literal[$loop->index]}}"/>
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
        <li class="me-2">No hay opciones</li>
    @endforelse            
</div>