<div>
    <div class="md:flex my-2">
        <ul class="flex-column space-y space-y-4 text-sm font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0">
    
            @forelse ($debates as $item)
    
                <li>
                    <button type="button" wire:click="active({{$item->id}})" class="inline-flex items-center px-4 py-3 text-white {{ ($item->id==$active_id) ? 'bg-blue-400 dark:bg-blue-400' : 'bg-blue-700 dark:bg-blue-600'}}  rounded-lg active w-full " aria-current="page">
                        <div class="block text-start">
                            <div class="block">{{$item->name}}</div>
                            <div class="text-xs text-gray-800 font-light">{{$item->full_grado}}</div>
                        </div>
                    </button>
                </li>
    
            @empty
                <div>No hay debates registrados</div>
            @endforelse
        </ul>
        <div class="p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-full">

            @if ($active_id)
                <livewire:app.general.educational.competition.dashboard.question-component :debate_id="$active_id"/>
            @else
                <div>Seleccione debate</div>
            @endif            
            
        </div>
    </div>
</div>
