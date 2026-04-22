<div>
    <div class="mt-4 p-4 w-full border border-emerald-200 bg-emerald-100 rounded-2xl shadow-sm">
        
        @if ($question)
            {{-- Contenedor interno con fondo blanco para generar profundidad y contraste --}}
            <div class="border border-emerald-200/60 rounded-xl p-6 bg-white shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        {{-- Label: Pregunta Activa --}}
                        <div class="text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                            Pregunta Activa
                        </div>
                        
                        {{-- ID + Texto de la pregunta --}}
                        <div class="flex items-start gap-4">
                            <span
                                class="text-xs bg-emerald-200 text-emerald-900 px-2 py-1 rounded-full font-bold border border-emerald-300">
                                #{{ $question->id }}
                            </span>
                            <h5 class="text-2xl font-bold text-gray-900 leading-tight flex-1">
                                {!! $question->text !!}
                            </h5>
                        </div>
                    </div>
                    
                    {{-- Badge de puntaje: armonizado con el tema verde claro --}}
                    <div class="shrink-0 ml-6">
                        <div
                            class="rounded-full w-24 h-24 bg-emerald-100 border-2 border-emerald-400 shadow-sm flex flex-col justify-center items-center">
                            <div class="text-emerald-900 text-center text-4xl font-black">
                                {{ $question->weighting }}
                            </div>
                            <div class="text-emerald-800 text-xs font-bold uppercase tracking-widest">Pts</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Estado: sin pregunta activa --}}
            <div class="text-center py-8 text-gray-500 italic bg-emerald-50/60 rounded-xl border border-emerald-200">
                Espere a que se establezca la pregunta activa
            </div>
        @endif
    </div>
</div>