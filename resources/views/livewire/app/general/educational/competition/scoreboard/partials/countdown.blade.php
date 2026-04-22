<div class="border border-emerald-200 rounded-2xl p-4 mt-2 bg-emerald-100 shadow-sm">
    <div class="flex justify-center m-2 w-full">
        <div class="text-center w-full">
            @if ($question)
                <div>
                    <div class="text-gray-800">
                        <div class="font-bold text-xl mb-2 text-emerald-800 uppercase tracking-widest">
                            Cronómetro
                        </div>
                        <div wire:key="score-timer-{{ $question->id ?? 'none' }}" x-data="{
                            s: @js($timeRemaining ?? 0),
                            active: false,
                            iv: null,
                            competition_id: @js($question->debate->competition_id),
                            init() {
                                // Sincronización inicial
                                this.sync();
                                
                                // Escuchar WebSocket del Moderador
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
                                        } else {
                                            clearInterval(this.iv); 
                                            this.active = false;
                                        }
                                    }, 1000);
                                }
                            },
                            fmt(v) { 
                                return String(v).padStart(2,'0');
                            }
                        }" x-init="init()">
                            <template x-if="s > 0">
                                <h1 class="text-8xl font-black text-emerald-900 tabular-nums" x-text="fmt(s)"></h1>
                            </template>
                            <template x-if="s <= 0">
                                <div class="message">
                                    <div class="text-sm font-light text-orange-600 animate-pulse">Tiempo finalizado...</div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="text-sm font-bold text-emerald-700 uppercase tracking-widest mt-2">[Segundos]</div>
                </div>
            @else
                <div class="text-gray-600 italic">Espere a que se establezca la pregunta activa</div>
            @endif
        </div>
    </div>
</div>