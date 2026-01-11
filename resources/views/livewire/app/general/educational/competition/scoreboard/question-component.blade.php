<div>

    <div class="mt-4 p-4 w-full border border-emerald-500/20 bg-gray-900/40 rounded-2xl backdrop-blur-sm"
        wire:poll.1s="updateQuestion({{ $competition->id }})">

        @if ($question)
            <div class="diagnostic-card border border-emerald-500/10 rounded-xl p-6">

                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <div class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2">Pregunta Activa
                        </div>
                        <div class="flex items-start gap-4">
                            <span
                                class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded-full font-bold">#{{ $question->id }}</span>
                            <h5 class="text-2xl font-bold text-white leading-tight flex-1">{!! $question->text !!}</h5>
                        </div>
                    </div>

                    <div class="shrink-0 ml-6">
                        <div
                            class="rounded-full w-24 h-24 bg-gradient-to-br from-emerald-600 to-emerald-400 shadow-lg flex flex-col justify-center items-center">
                            <div class="text-white text-center text-4xl font-black">{{ $question->weighting }}</div>
                            <div class="text-emerald-100 text-xs font-bold uppercase tracking-widest">Pts</div>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="text-center py-8 text-gray-500 italic">Espere a que se establezca la pregunta activa</div>
        @endif

    </div>

</div>
