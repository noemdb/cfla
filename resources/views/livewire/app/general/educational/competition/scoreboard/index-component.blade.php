<div>
    <section>
        <div class="px-2 mx-auto text-center">
            @if ($competition)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-1">
                    <div class="col-span-10">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-1">
                            <div class="col-span-2">
                                <livewire:app.general.educational.competition.scoreboard.competition-component :id="$competition->id"/>
                            </div>
                            <div class="col-span-2">
                                <livewire:app.general.educational.competition.scoreboard.debate-component :id="$competition->id"/>
                            </div>
                        </div>
                        <div class="border-t-4 border-cyan-600 mt-1 bg-slate-100">
                            <livewire:app.general.educational.competition.scoreboard.question-component :id="$competition->id"/>
                            <livewire:app.general.educational.competition.scoreboard.option-component :id="$competition->id"/>
                        </div>
                    </div>
                    <div class="col-span-2 border-l-2">
                        <div class="bg-lime-200 border-t-4 border-green-600 p-1" wire:poll.5s="updateScoreBoard({{$competition->id}})">
                            <span class="text-lg font-bold">Resultados Preliminares:</span>
                        </div>
                        @include('livewire.app.general.educational.competition.scoreboard.partials.scores')
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center mt-5">
                    @include('livewire.app.general.educational.competition.scoreboard.default.notfound')
                </div>
            @endif
        </div>
    </section>
</div>