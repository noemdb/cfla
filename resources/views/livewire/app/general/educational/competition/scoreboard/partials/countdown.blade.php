<div class="diagnostic-card border border-emerald-500/20 rounded-2xl p-4 mt-2 bg-gray-900/40 backdrop-blur-sm">

    <div class="flex justify-center m-2 w-full">

        <div class="text-center w-full">
            @if ($question)
                <div>

                    <div class="text-gray-100">

                        <div class="font-bold text-xl mb-2 text-emerald-400 uppercase tracking-widest">
                            Cronómetro
                        </div>

                        <div>
                            @if ($timeRemaining >= 0)
                                <h1 class="text-8xl font-black text-white tabular-nums">{{ $timeRemaining }}</h1>
                            @else
                                <div x-show="showMessage" class="message">
                                    <div class="text-sm font-light text-orange-400">Tiempo finalizado... actualizando la
                                        información</div>
                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="text-sm font-bold text-emerald-500/60 uppercase tracking-widest mt-2">[Segundos]</div>

                </div>
            @else
                <div class="text-gray-500 italic">Espere a que se establezca la pregunta activa</div>
            @endif
        </div>

    </div>

</div>
