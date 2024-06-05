<div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
        <div class="col-span-1 text-start">
            Preguntas:
            <ul class="">
                {{-- @php $items =  @endphp --}}
                @forelse ($questions->sortBy('category') as $item)
                    @php $active = ($item->id == $active_id) ? true : false; @endphp
                    <li class="me-2 my-4 mx-2 p-2 border-b-2 border-sky-600 font-normal {{($active) ? 'font-bold bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-600 dark:hover:bg-gray-400' : null}}">
                        @php $bg = 'bg-'.$item->color.'-200'; @endphp
                        <button class="text-start" wire:click="active({{$item->id}})">
                            {{$loop->iteration}}. {{$item->text}}
                        </button>
                    </li>
                @empty
                    <li class="me-2">
                        No hay preguntas
                    </li>        
                @endforelse
            </ul> 
        </div>
        <div class="col-span-2 ">

            @if ($active_id)                
                <livewire:app.general.educational.competition.dashboard.option-component :question_id="$active_id"/>
            @else
            <div>Seleccione pregunta</div>
            @endif


        </div>
    </div>

</div>