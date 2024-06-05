<div>
    
    <div class="flex justify-center m-2 w-full border-t-4 border-blue-800">
        
        <div class="text-center w-full" >
            @if ($question) 
                {{$question->text}}
                {{-- @include('livewire.app.general.educational.competition.scoreboard.partials.countdown') --}}
            @else
                <div>No hay pregunta activa</div>
            @endif
        </div>
        
    </div>
    
</div>
