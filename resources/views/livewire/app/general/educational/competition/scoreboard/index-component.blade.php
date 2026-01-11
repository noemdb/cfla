<div>
    <section>
        <div class="px-2 mx-auto text-center">
            @if ($competition)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-1">
                    <div class="col-span-10">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-1">
                            <div class="col-span-2">
                                <livewire:app.general.educational.competition.scoreboard.competition-component
                                    :id="$competition->id" />
                            </div>
                            <div class="col-span-2">
                                <livewire:app.general.educational.competition.scoreboard.debate-component
                                    :id="$competition->id" />
                            </div>
                        </div>
                        <div
                            class="border-t-2 border-emerald-500/30 mt-2 diagnostic-card rounded-2xl p-4 bg-gray-900/40">
                            <livewire:app.general.educational.competition.scoreboard.question-component
                                :id="$competition->id" />
                            <livewire:app.general.educational.competition.scoreboard.option-component
                                :id="$competition->id" />
                        </div>
                    </div>
                    <div class="col-span-2 border-l-2 border-emerald-500/20">
                        <div class="bg-emerald-600/20 border-t-2 border-emerald-500/30 p-3 backdrop-blur-sm"
                            wire:poll.5s="updateScoreBoard({{ $competition->id }})">
                            <span class="text-lg font-bold text-emerald-300 uppercase tracking-widest">Resultados
                                Preliminares</span>
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
