<div class="space-y-6" id="{{ $question->id }}">
    @if ($timeRemaining > 0)
        {{-- Card de Gestión de Respuestas con Estilo Premium --}}
        <div class="diagnostic-card border border-emerald-500/20 rounded-lg p-6 bg-gray-950/40 relative overflow-hidden backdrop-blur-md">
            <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div class="flex items-center justify-between mb-6 border-b border-emerald-500/10 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-emerald-500/10 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-white uppercase tracking-tight">Gestión de Respuestas</h4>
                        <p class="text-[10px] text-emerald-500 font-black tracking-widest uppercase">Cronómetro en Tiempo Real</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-6">
                {{-- Contenedor del Timer Alpine.js --}}
                <div class="grow flex items-center justify-center bg-gray-900/60 border border-emerald-500/10 rounded-lg py-6 px-10 min-w-[200px]">
                    <div wire:key="ans-timer-{{ $question->id }}" class="text-5xl font-black text-white tabular-nums tracking-tighter"
                        x-data="{
                            s: @js($timeRemaining ?? 0),
                            active: @js($timerActive ?? false),
                            iv: null,
                            competition_id: @js($question->debate->competition_id),
                            init() {
                                this.sync();
                                if (window.Echo) {
                                    window.Echo.channel('competition.' + this.competition_id)
                                        .listen('.timer.sync', (e) => {
                                            this.s = e.time_remaining;
                                            this.active = e.timer_active;
                                            this.sync();
                                        });
                                }
                            },
                            sync() {
                                if (this.iv) clearInterval(this.iv);
                                if (this.active && this.s > 0) {
                                    this.iv = setInterval(() => { 
                                        if (this.s > 0) { 
                                            this.s--; 
                                        } 
                                        if (this.s <= 0) { 
                                            clearInterval(this.iv); 
                                            this.active = false;
                                            $wire.finished();
                                        }
                                    }, 1000);
                                }
                            },
                            pauseTimer() {
                                $wire.pause(this.s);
                            },
                            fmt(v) { 
                                let mins = Math.floor(v / 60);
                                let secs = v % 60;
                                return String(mins).padStart(2,'0') + ':' + String(secs).padStart(2,'0');
                            }
                        }" x-init="init()">
                        <span x-text="fmt(s)"></span>
                    </div>
                    <div class="ml-3 flex flex-col items-start border-l border-emerald-500/20 pl-3">
                        <span class="text-[10px] text-emerald-500/60 font-bold uppercase tracking-widest">MIN:SEG</span>
                    </div>
                </div>

                {{-- Controles --}}
                <div class="flex gap-3 w-full md:w-auto">
                    @if ($timerActive)
                        <button x-on:click="pauseTimer()"
                            class="flex-1 md:flex-none px-8 py-2 bg-orange-500/20 border border-orange-500/30 text-orange-400 rounded-lg font-bold uppercase tracking-widest hover:bg-orange-500/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Pausar
                        </button>
                    @else
                        <button wire:click="start"
                            class="flex-1 md:flex-none px-8 py-2 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 rounded-lg font-bold uppercase tracking-widest hover:bg-emerald-500/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                            Iniciar
                        </button>
                    @endif
                    <button wire:click="finished"
                        class="flex-1 md:flex-none px-8 py-2 bg-red-500/20 border border-red-500/30 text-red-400 rounded-lg font-bold uppercase tracking-widest hover:bg-red-500/30 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
                        </svg>
                        Terminar
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="diagnostic-card border border-emerald-500/20 rounded-lg p-6 bg-gray-950/40 relative overflow-hidden backdrop-blur-md">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="bg-emerald-600/20 border border-emerald-400/30 text-emerald-400 py-2 px-6 rounded-lg font-black uppercase tracking-[0.2em] text-lg shadow-inner">
                    {{ $grado->name ?? 'Sin Grado' }}
                </div>

                <div class="flex-1 text-center md:text-start">
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-1">Registro de Puntuación</p>
                    <h4 class="text-lg font-bold text-gray-300">Haz clic en la sección ganadora:</h4>
                </div>

                <div class="flex flex-wrap justify-center md:justify-end gap-2">
                    @forelse ($seccions as $item)
                        <button wire:click="saveAnswerSeccion({{ $item->id }}, true)"
                            class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-bold text-sm uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-600/20 border border-emerald-400/30">
                            {{ $item->name }}
                        </button>
                    @empty
                        <div class="px-4 py-2 bg-red-950/40 border border-red-500/30 text-red-500 rounded-lg text-xs font-black uppercase tracking-widest">
                            No hay secciones activas
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
