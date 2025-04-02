<div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">

    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
        <div class="col-span-5 text-start">
            Preguntas:..
            <ul class="">
                @forelse ($questions->sortBy('category') as $item)
                    @php $active = ($item->id == $active_id) ? true : false; @endphp
                    <li class="me-2 my-4 mx-2 p-2 border-b-2 border-primary-600 font-normal {{($active) ? 'font-bold bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-600 dark:hover:bg-gray-400' : null}}">
                        
                        <div class="flex items-center">
                            <div class="grow px-2">
                                <div class="w-full">
                                    <div>
                                        <button class="text-start w-full" wire:click="active({{$item->id}})">
                                            {{$loop->iteration}}. {!!$item->text!!}. 
                                            <div class="flex justify-end border-t-2 border-gray-400 dark:border-gray-700">
                                                <small>{{$item->category}}</small> || <small>[<b>{{$item->time}}</b>seg]</small> <small>[<b>{{$item->weighting}}</b>pts]</small>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="flex-none px-2 text-sm">
                                <div class="flex">
                                    @if ($item->status_active) <x-badge.circle negative icon="pause" class="ms-2 px-2 animate-pulse"/> @endif
                                    @if ($item->status_over_time) <x-badge.circle outline icon="check"  class="px-1" /> @endif
                                    @if ($item->status_answer) <x-badge.circle flat icon="check"  class="px-1" /> @endif
                                </div>                                
                            </div>

                        </div>

                        
                    </li>
                @empty
                    <li class="me-2">
                        No hay preguntas
                    </li>        
                @endforelse
            </ul> 
        </div>
        <div class="col-span-7 ">

            @if ($active_id)   
                @php $key = "competition-moderator-option-component-".$active_id; @endphp             
                <livewire:app.general.educational.competition.moderator.option-component :question_id="$active_id"/>
            @else
                <div>Seleccione pregunta</div>
            @endif


        </div>
    </div>

</div>