<div x-data="{ zoomLevel: 100, showHeader: true }">
    {{-- zoom solo a la ventada de preguntas --}}
    <div :style="'zoom: ' + zoomLevel + '%'">
        <section>
            <div class="px-2 mx-auto text-center">

                @if ($competition)
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-1">

                        {{-- Panel principal --}}
                        <div class="col-span-10">

                            {{-- Contenedor de Controles --}}
                            <div class="flex justify-end items-center mb-2 gap-3">

                                {{-- Controles de Zoom --}}
                                <div
                                    class="flex items-center bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                    <button @click="zoomLevel = Math.max(50, zoomLevel - 10)"
                                        class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 font-bold text-sm focus:outline-none transition-colors"
                                        title="Reducir Zoom">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>

                                    <div
                                        class="px-2 py-1.5 bg-gray-50 border-x border-gray-200 min-w-[3rem] text-center">
                                        <span class="text-xs font-bold text-gray-700" x-text="zoomLevel + '%'"></span>
                                    </div>

                                    <button @click="zoomLevel = Math.min(250, zoomLevel + 10)"
                                        class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 font-bold text-sm focus:outline-none transition-colors"
                                        title="Ampliar Zoom">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>

                                    <button @click="zoomLevel = 100"
                                        class="px-3 py-1.5 border-l border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-bold focus:outline-none transition-colors flex items-center"
                                        title="Restaurar Original">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        100%
                                    </button>
                                </div>

                                {{-- Botón de alternancia de Título --}}
                                <button @click="showHeader = !showHeader"
                                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-300"
                                    title="Mostrar/Ocultar Título">
                                    <span x-text="showHeader ? 'Ocultar Título' : 'Mostrar Título'"></span>
                                    <svg x-show="showHeader" class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg x-show="!showHeader" class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
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

                            {{-- Pregunta y opciones --}}
                            <div class="border-t-2 border-emerald-600/40 mt-2 rounded-lg p-4 bg-white shadow-sm">
                                <livewire:app.general.educational.competition.scoreboard.question-component
                                    :id="$competition->id" />
                                <livewire:app.general.educational.competition.scoreboard.option-component
                                    :id="$competition->id" />
                            </div>

                        </div>

                        {{-- Sidebar: Resultados Preliminares --}}
                        <div class="col-span-2 border-l-2 border-emerald-500/30 bg-gray-50">

                            <div class="bg-emerald-600 p-3">
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
</div>
