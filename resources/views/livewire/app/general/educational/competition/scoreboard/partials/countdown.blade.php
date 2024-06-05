<div class="">

    <div class=" font-bold text-xl mb-2 border-t-2 border-gray-200">
        Cronómetro
    </div>
    <small class="text-gray-400">[Cuenta regresiva]</small>

    <div class="">
        @if ($timeRemaining >= 0)
            <h1 class="text-8xl font-extrabold">{{$timeRemaining}}</h1>
        @else
            <div x-show="showMessage" class="message">
                <div class="text-sm font-light">Tiempo finalizado... actualizando la información</div>
            </div>
        @endif
    </div>
    
    <div class="text-sm font-bold text-gray-400">[Seg]</div>

    @include('livewire.app.general.educational.competition.scoreboard.partials.results')

    

</div>