<div>
    <section>
        <div class="px-2 mx-auto text-center">

            @if ($competition)

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-1">

                    {{-- Panel principal --}}
                    <div class="col-span-10">

                        {{-- Contenedor con estado Alpine para colapsar cabecera --}}
                        <div x-data="{ showHeader: true }">
                            
                            {{-- Botón de alternancia --}}
                            <div class="flex justify-end mb-2">
                                <button 
                                    @click="showHeader = !showHeader" 
                                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-300"
                                    title="Mostrar/Ocultar Título">
                                    <span x-text="showHeader ? 'Ocultar Título' : 'Mostrar Título'"></span>
                                    <svg x-show="showHeader" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg x-show="!showHeader" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Grid colapsable: Competition + Debate --}}
                            <div x-show="showHeader" x-transition class="grid grid-cols-1 sm:grid-cols-4 gap-1">
                                <div class="col-span-2">
                                    <livewire:app.general.educational.competition.scoreboard.competition-component
                                        :id="$competition->id" />
                                </div>
                                <div class="col-span-2">
                                    <livewire:app.general.educational.competition.scoreboard.debate-component
                                        :id="$competition->id" />
                                </div>
                            </div>

                        </div>

                        {{-- Pregunta y opciones --}}
                        <div class="border-t-2 border-emerald-600/40 mt-2 rounded-2xl p-4 bg-white shadow-sm">
                            <livewire:app.general.educational.competition.scoreboard.question-component
                                :id="$competition->id" />
                            <livewire:app.general.educational.competition.scoreboard.option-component
                                :id="$competition->id" />
                        </div>

                    </div>

                    {{-- Sidebar: Resultados Preliminares --}}
                    <div class="col-span-2 border-l-2 border-emerald-500/30 bg-gray-50">

                        <div class="bg-emerald-600 p-3"
                            wire:poll.5s="updateScoreBoard({{ $competition->id }})">
                            <span class="text-lg font-bold text-white uppercase tracking-widest">
                                Resultados Preliminares
                            </span>
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