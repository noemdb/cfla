<div class="space-y-6">
    @if ($question)
        <div class="diagnostic-card border {{ $question->status_answer ? 'border-orange-500/30' : 'border-emerald-500/10' }} rounded-2xl p-6 bg-gray-900/40 backdrop-blur-md relative overflow-hidden">
            @if ($question->status_answer)
                <div class="absolute top-0 left-0 w-1 h-full bg-orange-500/50"></div>
            @endif
            <div class="flex items-center justify-between mb-6 border-b border-emerald-500/10 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 {{ $question->status_answer ? 'bg-orange-500/20' : 'bg-emerald-500/20' }} rounded-lg">
                        <svg class="w-5 h-5 {{ $question->status_answer ? 'text-orange-400' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-sm font-bold text-gray-300 italic">"{{ $question->text }}"</h5>
                        @if ($question->status_under_review)
                            <span class="text-[9px] font-black uppercase tracking-widest text-red-500 flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Atención: Respuesta Anulada (En revisión)
                            </span>
                        @elseif ($question->status_answer)
                            <span class="text-[9px] font-black uppercase tracking-widest text-orange-400 flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Atención: Ya respondida
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    @if ($question->status_active)
                        <button wire:click="setOffline({{ $question->id }})"
                            class="p-2 hover:bg-red-500/20 rounded-lg text-red-400 transition-colors"
                            title="Desactivar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    @else
                        <button wire:click="setOnline({{ $question->id }})"
                            class="p-2 hover:bg-emerald-500/20 rounded-lg text-emerald-400 transition-colors"
                            title="Activar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-950/40 border border-emerald-500/10 rounded-xl p-3 text-center">
                    <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-1">Ponderación</div>
                    <div class="text-xl font-black text-emerald-400">{{ $question->weighting }} <span
                            class="text-[10px] opacity-50">PTS</span></div>
                </div>
                <div class="bg-gray-950/40 border border-emerald-500/10 rounded-xl p-3 text-center">
                    <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-1">Tiempo Límite</div>
                    <div class="text-xl font-black text-emerald-400">{{ $question->time }} <span
                            class="text-[10px] opacity-50">SEG</span></div>
                </div>
                <div
                    class="bg-gray-950/40 border border-emerald-500/10 rounded-xl p-3 text-center relative overflow-hidden group">
                    <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-1">Tiempo Transcurrido
                    </div>
                    <div class="text-xl font-black text-white group-hover:text-emerald-400 transition-colors">
                        {{ $question->time_elapsed ?? '0' }} <span class="text-[10px] opacity-50">SEG</span></div>
                    @if ($question->status_active)
                        <div class="absolute bottom-0 left-0 h-0.5 bg-emerald-500 animate-pulse w-full"></div>
                    @endif
                </div>
            </div>

            {{-- Botón de anulación --}}
            @if ($question->status_answer)
                <div class="mt-5 flex justify-end">
                    <button type="button" 
                        {{ $question->status_under_review ? 'disabled' : '' }}
                        wire:click.prevent="nullifyQuestion"
                        wire:confirm="¿Anular Puntuación? Esta acción no se puede revertir. Se resetearán los puntos otorgados y la interrogante pasará a estado de revisión de los moderadores."
                        title="{{ $question->status_under_review ? 'Esta respuesta ya fue anulada' : 'Anular Respuesta' }}"
                        class="flex items-center gap-2.5 px-5 py-2.5 bg-red-500/10 text-red-500 rounded-xl transition-all duration-300 border border-red-500/30 group 
                               {{ $question->status_under_review 
                                  ? 'opacity-40 cursor-not-allowed grayscale' 
                                  : 'hover:bg-red-500 hover:text-white shadow-lg shadow-red-500/20' }}">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <span class="text-[11px] font-black uppercase tracking-widest">Anular Respuesta Puntuada</span>
                    </button>
                </div>
            @endif
        </div>

        @include('livewire.app.general.educational.competition.moderator.partials.answer')

        <div class="flex items-center space-x-3 mb-4">
            <span class="text-xs font-black uppercase tracking-[0.2em] text-emerald-500/60">Opciones de Respuesta</span>
            <div class="h-px bg-gradient-to-r from-emerald-500/20 to-transparent flex-1"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($options as $item)
                @php
                    $isCorrect = $item->status_option_correct && $question->status_over_time;
                    $isWrong = $item->status_wrong_answer && $question->status_over_time;
                @endphp
                <div
                    class="relative diagnostic-card rounded-2xl p-5 border transition-all duration-300
                    {{ $isCorrect
                        ? 'bg-emerald-600/20 border-emerald-400 shadow-emerald-500/20 shadow-lg'
                        : ($isWrong
                            ? 'bg-red-500/20 border-red-500/30'
                            : 'bg-gray-900/40 border-emerald-500/10 hover:border-emerald-500/30') }}">

                    @if (!$question->status_answer && $question->status_over_time && $question->status_active)
                        @php $seccions = $question->seccions @endphp
                        <div class="absolute top-3 right-3 z-10">
                            <x-dropdown icon="ellipsis-vertical" class="text-emerald-400">
                                <x-dropdown.header label="Asignar Puntaje">
                                    @foreach ($seccions as $seccion)
                                        <x-dropdown.item :label="$seccion->name"
                                            wire:click="answerScoreSeccion({{ $seccion->id }},{{ $item->id }})"
                                            icon="plus-circle" />
                                    @endforeach
                                </x-dropdown.header>
                                <x-dropdown.header label="Anulación" separator>
                                    @foreach ($seccions as $seccion)
                                        <x-dropdown.item :label="$seccion->name"
                                            wire:click="answerNullSeccion({{ $seccion->id }},{{ $item->id }})"
                                            icon="minus-circle" />
                                    @endforeach
                                </x-dropdown.header>
                            </x-dropdown>
                        </div>
                    @endif

                    <div class="flex items-start gap-4">
                        <div class="shrink-0">
                            @php $color = $colors[$loop->index]; @endphp
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center font-black text-lg bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 shadow-inner">
                                {{ $literal[$loop->index] }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <p
                                class="text-sm font-medium leading-relaxed {{ $isCorrect ? 'text-white' : 'text-gray-400' }}">
                                {{ $item->text }}
                            </p>
                            @if ($isCorrect)
                                <span
                                    class="text-[10px] font-black uppercase tracking-tighter text-emerald-400 mt-2 block">Respuesta
                                    Correcta</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-2 py-10 text-center text-gray-600 italic border border-dashed border-emerald-500/10 rounded-xl">
                    No se han definido opciones para esta pregunta.
                </div>
            @endforelse
        </div>
    @else
        <div class="h-full flex flex-col items-center justify-center py-20 opacity-30">
            <svg class="w-16 h-16 text-emerald-500/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-bold uppercase tracking-widest text-emerald-500/40">Esperando selección de pregunta
            </p>
        </div>
    @endif
</div>
