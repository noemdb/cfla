<div class="space-y-6" id="{{ $question->id }}">
    @if ($question->status_active)
        @if (!$question->status_over_time)
            <div class="diagnostic-card border border-emerald-500/20 rounded-2xl p-6 bg-gray-950/40">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-xs font-black uppercase tracking-widest text-emerald-500">Cronómetro en
                            Vivo</span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div
                        class="grow flex items-center justify-center bg-gray-900/60 border border-emerald-500/10 rounded-2xl py-6 px-10 min-w-[200px]">
                        <div class="text-5xl font-black text-white tabular-nums tracking-tighter">
                            @if ($timerActive)
                                <span wire:poll.{{ $pollingInterval }}ms="decrementCount">
                                    {{ gmdate('i:s', $timeRemaining) }}
                                </span>
                            @else
                                <span>{{ gmdate('i:s', $timeRemaining) }}</span>
                            @endif
                        </div>
                        <div class="ml-3 flex flex-col items-start border-l border-emerald-500/20 pl-3">
                            <span
                                class="text-[10px] text-emerald-500/60 font-bold uppercase tracking-widest">MIN:SEG</span>
                        </div>
                    </div>

                    <div class="flex gap-3 w-full md:w-auto">
                        @if ($timerActive)
                            <button wire:click="pause"
                                class="flex-1 md:flex-none px-8 py-4 bg-orange-500/20 border border-orange-500/30 text-orange-400 rounded-xl font-bold uppercase tracking-widest hover:bg-orange-500/30 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Pausar
                            </button>
                        @else
                            <button wire:click="start"
                                class="flex-1 md:flex-none px-8 py-4 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 rounded-xl font-bold uppercase tracking-widest hover:bg-emerald-500/30 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                        clip-rule="evenodd" />
                                </svg>
                                Iniciar
                            </button>
                        @endif
                        <button wire:click="finished"
                            class="flex-1 md:flex-none px-8 py-4 bg-red-500/20 border border-red-500/30 text-red-400 rounded-xl font-bold uppercase tracking-widest hover:bg-red-500/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z"
                                    clip-rule="evenodd" />
                            </svg>
                            Terminar
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div
                class="diagnostic-card border border-emerald-500/20 rounded-2xl p-6 bg-gray-950/40 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                    <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div
                        class="bg-emerald-600/20 border border-emerald-400/30 text-emerald-400 py-3 px-6 rounded-xl font-black uppercase tracking-[0.2em] text-lg shadow-inner">
                        {{ $grado->name }}
                    </div>

                    <div class="flex-1 text-center md:text-start">
                        @if ($question->status_answer)
                            <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-1">Resultado
                                Registrado</p>
                            @php $seccion_name = ($answer->seccion) ? $answer->seccion->name : 'N/A';@endphp
                            <h4 class="text-3xl font-black text-white uppercase">Sección {{ $seccion_name }}</h4>
                        @else
                            <p class="text-xs font-bold text-orange-400 uppercase tracking-widest mb-1">Tiempo Agotado
                            </p>
                            <h4 class="text-2xl font-bold text-gray-300">Asignar puntuación a la sección:</h4>
                        @endif
                    </div>

                    <div class="shrink-0">
                        @if ($question->status_answer)
                            <div class="flex items-center gap-3">
                                @php $score = ($answer->score) ? $answer->score : 0 ; @endphp
                                <div class="text-right">
                                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Puntos
                                        Ganados</div>
                                    <div class="text-4xl font-black text-emerald-400">{{ $score }} <span
                                            class="text-xs">PTS</span></div>
                                </div>
                                <button
                                    wire:click="setPoin({{ $answer->id }}, {{ $score ? 0 : $question->weighting }})"
                                    class="p-4 rounded-xl transition-all duration-300 border {{ $score ? 'bg-orange-500/20 border-orange-500/30 text-orange-400' : 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400' }}">
                                    @if ($score)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        @else
                            @if ($question->exist_option_correct)
                                <div class="flex flex-wrap justify-center md:justify-end gap-2">
                                    @php $seccions = $grado->activeSeccions() @endphp
                                    @foreach ($seccions as $item)
                                        <button wire:click="saveAnswerSeccion({{ $item->id }},true)"
                                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-600/20">
                                            Sección {{ $item->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="px-4 py-2 bg-red-950/40 border border-red-500/30 text-red-500 rounded-lg text-xs font-black uppercase tracking-widest">
                                    Error: Sin respuesta correcta
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="py-10 text-center opacity-30 border border-dashed border-emerald-500/10 rounded-2xl">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-500">Módulo de Respuesta Inactivo</p>
        </div>
    @endif
</div>
