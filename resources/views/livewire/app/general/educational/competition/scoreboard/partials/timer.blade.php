<div class="py-2 border-t-2 border-blue-800">

    <div class="py-2">Tiempo/Puntaje</div>
    @if (! $question->status_over_time)

        <div wire:poll.{{ $timeRemaining}}s="updateTimetimeRemaining">
            <div class=" font-bold text-xl mb-2 border-t-2 border-gray-200">Cronómetro <span class="text-sm font-bold text-gray-400">[Seg.]</span></div>

            <div x-data="{ 
                seconds: $wire.entangle('timeRemaining'),
                interval: null,
                running: false,
                showMessage: false,
                start() {
                this.interval = setInterval(() => {
                    this.seconds--;
                    if (this.seconds === 0) {
                    this.stop();
                    this.showMessage = true;
                    }
                }, 1000);
                    this.running = true;
                },
                stop() {
                    clearInterval(this.interval);
                    this.running = false;
                    this.seconds = 0;
                    this.showMessage = false;
                },
                init() {
                    this.start();
                }
            }">
                <h1 class="text-8xl font-extrabold" x-text="seconds"></h1>
                {{-- <button x-on:click="stop" x-show="running">Stop</button> --}}
                <div x-show="showMessage" class="message">
                    <div class="text-sm font-light">Tiempo finalizado... actualizando la información</div>
                    {{-- <p>The countdown has reached zero.</p> --}}
                </div>
            </div>

        </div>        

    @else
        <div class="p-2 m-2">
            {{$question ?? 'fail'}}
            <span class="border border-gray-300 rounded p-2">Tiempo finalizado</span>
            @if ($question->status_answer) <span class="border border-gray-300 rounded p-2">Puntaje adjudicado</span> @endif
        </div>
    @endif

</div>