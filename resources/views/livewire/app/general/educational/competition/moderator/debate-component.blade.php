<div>

    @if ($debates->isNotEmpty())

        <div class="md:flex my-2">

            <ul class="flex-column space-y space-y-4 text-sm font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0">
                <li class=" bg-green-600 py-2 rounded">{{$grado->name}}</li>
                @foreach ($debates as $item)        
                    <li>
                        <button type="button" wire:click="active({{$item->id}})" class="inline-flex items-center px-4 py-3 text-white {{ ($item->id==$active_id) ? 'bg-blue-400 dark:bg-blue-400' : 'bg-blue-700 dark:bg-blue-600'}}  rounded-lg active w-full " aria-current="page">
                            <div class="block text-start">

                                <div class="flex">
                                    <div class="px-2">
                                        <div class="block">{{$item->name}}</div>
                                        <div class="text-xs text-gray-800 font-light">{{$item->full_grado}}</div>
                                    </div>

                                    @if ($item->status_active)
                                        <x-badge.circle negative icon="pause" class="ms-2 px-2 animate-pulse"/>
                                    @endif
                                </div>
                                
                            </div>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-full">
                @if ($active_id)
                    @php $key = "competition-moderator-question-component-".$active_id; @endphp
                    <livewire:app.general.educational.competition.moderator.question-component :debate_id="$active_id"/>
                @else
                    <div>Seleccione debate</div>
                @endif
            </div>

        </div>
    @else

        @if ($grado_id)            
            <div>No hay debates registrados</div>
        @else
            <div>Seleccione Grado/Año</div>            
        @endif
    @endif
</div>
