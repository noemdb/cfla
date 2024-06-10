<div>
    
    <div class="mt-4 p-2 w-full border border-gray-300 bg-green-200 rounded-lg" wire:poll.1s="updateQuestion({{$competition->id}})">
    {{-- <div class="mt-4 pt-4 w-full border-t-2 border-gray-200"> --}}

        @if ($question)
            <x-card>
        
                <x-slot name="title">
                    <h5 class="mb-2 px-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">

                        <div class="text-sm font-bold text-green-900">Pregunta:</div>
                        <div class="text-green-950 flex justify-between items-center">
                            <div><small class="text-gray-200">{{$question->id}}.</small> <span class="text-4xl text-green-950 font-bold">{!!$question->text!!}</span> </div>
                        </div>
                        
                    </h5>
                    {{-- <div class="text-6xl bg-green-400 p-4 rounded">{{$question->weighting}}Pts</div>   --}}
                    
                    <div class="rounded-full w-24 h-24 bg-green-400 shadow-md flex justify-center items-center">
                        <div class="font-bold">
                            <div class="text-white text-center text-4xl">{{$question->weighting}}</div>
                            <div>Pts</div>
                        </div>
                    </div>
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
