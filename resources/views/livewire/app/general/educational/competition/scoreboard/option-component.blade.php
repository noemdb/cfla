{{-- Altura restringida al espacio visible hasta el footer --}}
<div class="h-[calc(100vh-12rem)] flex flex-col overflow-hidden">
    <div class="h-full flex flex-col">
        @if ($question)
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 h-full flex-1">
                
                {{-- Columna Izquierda: Etiqueta "Opciones" --}}
                <div class="col-span-1 flex items-center justify-center">
                    <div class="rotate-[-90deg] whitespace-nowrap text-xl font-bold text-emerald-700 uppercase tracking-wider">
                        Opciones
                    </div>
                </div>

                {{-- Columna Central: Opciones de respuesta --}}
                <div class="col-span-8 flex flex-col h-full">
                    {{-- Línea separadora --}}
                    @if ($question->status_answer)
                        <div class="my-2 border-t-2 border-emerald-400 flex-shrink-0"></div>
                    @else
                        <div class="my-2 border-t-2 border-emerald-300 flex-shrink-0"></div>
                    @endif

                    {{-- Grid de opciones: se expande al espacio restante --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-emerald-100 rounded-2xl bg-white shadow-sm flex-1 content-stretch overflow-hidden">
                        @forelse ($options as $item)
                            @php
                                $wrong = $item->status_wrong_answer && $question->status_over_time;
                                $status_answer = $item->status_option_correct && $question->status_over_time;
                                $status_noanswer = $question->status_over_time && !$item->status_option_correct;
                            @endphp

                            {{-- Tarjeta de Opción: ocupa espacio uniforme --}}
                            <div class="h-full flex flex-col items-center justify-center px-4 py-6 text-center rounded-xl border transition-all duration-300 ease-in-out relative
                                {{ $status_answer
                                    ? 'bg-emerald-100 border-emerald-400 shadow-md ring-1 ring-emerald-200'
                                    : ($wrong
                                        ? 'bg-red-50 border-red-200 shadow-sm opacity-80'
                                        : ($status_noanswer
                                            ? 'bg-gray-100 border-gray-200 shadow-sm opacity-60'
                                            : 'bg-emerald-50 border-emerald-200 shadow-sm hover:shadow-md hover:border-emerald-400 hover:-translate-y-0.5')
                                        )
                                }}">

                                {{-- Icono Check --}}
                                @if ($status_answer)
                                    <div class="absolute top-3 right-3 bg-emerald-600 rounded-full p-1.5 shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif

                                {{-- Contenido centrado --}}
                                <div class="flex flex-col items-center justify-center flex-1 w-full gap-4">
                                    
                                    {{-- Círculo con literal --}}
                                    <div class="rounded-full flex justify-center items-center transition-all duration-300
                                        {{ $status_answer
                                            ? 'h-24 w-24 bg-emerald-200 border-2 border-emerald-500 text-emerald-900 shadow-sm'
                                            : ($wrong
                                                ? 'h-20 w-20 bg-red-100 border-2 border-red-300 text-red-800'
                                                : ($status_noanswer
                                                    ? 'h-20 w-20 bg-gray-200 border-2 border-gray-300 text-gray-500'
                                                    : 'h-20 w-20 bg-white border-2 border-emerald-300 text-gray-800 shadow-sm')
                                            )
                                        }}">
                                        <span class="font-black text-center {{ $status_answer ? 'text-5xl' : 'text-4xl' }}">
                                            {{ $literal[$loop->index] }}
                                        </span>
                                    </div>

                                    {{-- Texto de la opción --}}
                                    <div class="w-full px-2 z-10">
                                        <div class="text-center transition-all duration-300 text-xl leading-tight
                                            {{ $status_noanswer
                                                ? 'text-gray-400 font-light line-through decoration-gray-400'
                                                : ($status_answer
                                                    ? 'text-emerald-900 font-bold'
                                                    : ($wrong
                                                        ? 'text-red-800 font-medium'
                                                        : 'text-gray-800 font-semibold')
                                                )
                                            }}">
                                            <span>{!! $item->text !!}</span>
                                            
                                        </div>
                                        <small class="block text-xs text-emerald-700/60 mt-2">#{{ $item->id }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 flex items-center justify-center py-8 text-gray-600 italic bg-emerald-50/30 rounded-xl border border-emerald-100 h-full">
                                Espere a que se establezca la pregunta activa
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Columna Derecha: Cronómetro y Resultados --}}
                <div class="col-span-3 flex flex-col gap-3 h-full">
                    @if ($competition)
                        <div class="flex-shrink-0">
                            @include('livewire.app.general.educational.competition.scoreboard.partials.countdown')
                        </div>
                        <div class="flex-1 min-h-0 overflow-hidden">
                            @include('livewire.app.general.educational.competition.scoreboard.partials.results')
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-600 italic text-sm">
                            Espere a que se establezca una competición
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="flex items-center justify-center py-6 text-gray-600 italic bg-emerald-50 rounded-xl border border-emerald-200 h-full">
                Espere... No hay una pregunta activa
            </div>
        @endif
    </div>
</div>