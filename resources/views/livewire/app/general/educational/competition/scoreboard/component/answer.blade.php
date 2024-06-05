<div class="h-full">
    
    <div class="flex justify-center m-2 w-full">
        
        <div class="text-center w-full h-full" >
            @if ($question) 
                {{-- {{$question->text}} --}}
                @include('livewire.app.general.educational.competition.scoreboard.partials.countdown')
            @else
                <div class="text-gray-400">Espere a que se establezca la pregunta activa</div>
            @endif
        </div>
        
    </div>
    
</div>