<div>
    
    <div class="mt-4 pt-4 w-full border-t-2 border-gray-200" wire:poll.1s="updateQuestion({{$competition->id}})">
    {{-- <div class="mt-4 pt-4 w-full border-t-2 border-gray-200"> --}}

        @if ($question)
            <x-card>
        
                <x-slot name="title">
                    <h5 class="mb-2 px-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">

                        <div class="text-sm font-bold text-green-900">Pregunta:</div>
                        <div class="text-green-950 flex justify-between items-center">
                            <div><small class="text-gray-200">{{$question->id}}.</small> <span class="text-7xl">{{$question->text}}</span> </div>
                        </div>
                        
                    </h5>
                    <div class="text-6xl bg-green-200 p-4 rounded">{{$question->weighting}}Pts</div>                            
                </x-slot>
        
                <x-slot name="action">
                    <x-dropdown>
                        <x-dropdown.item label="Acerca de ..." />
                    </x-dropdown>
                </x-slot>                 
        
            </x-card>
        @else
            <div>Espere a que se establezca la pregunta activa</div>
        @endif                

    </div>

</div>
