<div class="space-y-6 fade-in">
    <!-- Filters Bar -->
    <div class="bg-gray-900 p-6 rounded-xl border border-white/10 shadow-2xl mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 items-end">
            <!-- Search -->
            <div class="lg:col-span-3">
                <x-input label="Buscar por Texto" placeholder="Pregunta..."
                    wire:model.live.debounce.300ms="search"
                    class="bg-gray-800 border-gray-700 text-white placeholder-gray-500 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                    <x-slot name="prepend">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </x-slot>
                </x-input>
            </div>

            <!-- Debate -->
            <div class="lg:col-span-2">
                <x-select label="Encuentro" placeholder="Todos" wire:model.live="debate_id"
                    :options="$list_debates" option-label="name" option-value="id"
                    class="bg-gray-800 border-gray-700 text-white shadow-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500" />
            </div>

            <!-- Grado -->
            <div class="lg:col-span-2">
                <x-select label="Grado / Año" placeholder="Todos" wire:model.live="grado_id"
                    :options="$list_grados" option-label="name" option-value="id"
                    class="bg-gray-800 border-gray-700 text-white shadow-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500" />
            </div>

            <!-- Ponderacion -->
            <div class="lg:col-span-1">
                <x-select label="Puntos" placeholder="Pts" wire:model.live="weighting" :options="$list_weightings"
                    class="bg-gray-800 border-gray-700 text-white shadow-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500" />
            </div>

            <!-- Category -->
            <div class="lg:col-span-2">
                <x-select label="Asignatura" placeholder="Todas" wire:model.live="category" :options="$list_categories"
                    class="bg-gray-800 border-gray-700 text-white shadow-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500" />
            </div>

            <!-- Status -->
            <div class="lg:col-span-2">
                <x-select label="Estado Especial" placeholder="Cualquier estado" wire:model.live="status" :options="[
                    ['label' => '⚠️ En Revisión', 'value' => 'under_review'],
                ]"
                    option-label="label" option-value="value"
                    class="bg-gray-800 border-gray-700 text-white shadow-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500" />
            </div>

            <!-- Toggles Section (Span 2) -->
            <div class="lg:col-span-2 flex flex-col justify-center gap-2 pb-1">
                <x-toggle left-label="Respondidas" wire:model.live="filterAnswered" emerald />
                <x-toggle left-label="Sin Responder" wire:model.live="filterUnanswered" emerald />
            </div>

            <!-- Reset -->
            <div class="lg:col-span-1 flex justify-end">
                <button wire:click="resetFilters"
                    class="w-full flex items-center justify-center p-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white rounded-xl border border-gray-700 transition-all shadow-lg">
                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @if ($questions->isEmpty())
        <div class="py-20 bg-gray-900/20 border border-white/5 rounded-xl text-center">
            <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <p class="text-gray-500 font-medium">No se han registrado preguntas en esta competición aún.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($questions as $question)
                @php
                    $isVoided = $question->status_under_review ?? false;
                    $hasAnswer = $question->answers->isNotEmpty();
                    $firstAnswer = $question->answers->first();
                @endphp
                <div
                    class="bg-gray-900/40 backdrop-blur-md rounded-xl p-6 border transition-all duration-300 relative {{ $isVoided ? 'border-red-500/30 shadow-lg shadow-red-500/5' : ($hasAnswer ? 'border-emerald-500/10 hover:border-emerald-500/30' : 'border-white/5 opacity-70') }}">

                    @if ($isVoided)
                        <div
                            class="absolute top-0 right-0 p-1.5 px-3 bg-red-500 text-white text-[9px] font-black uppercase tracking-widest rounded-bl-2xl rounded-tr-xl shadow-lg">
                            Anulada en revisión
                        </div>
                    @elseif(!$hasAnswer)
                        <div
                            class="absolute top-0 right-0 p-1.5 px-3 bg-gray-700 text-gray-300 text-[9px] font-black uppercase tracking-widest rounded-bl-2xl rounded-tr-xl shadow-lg">
                            Sin Responder
                        </div>
                    @endif

                    <!-- Contexto del Debate -->
                    <div class="mb-4 pb-4 border-b border-white/5">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Debate &
                            Contexto</div>
                        <h4 class="text-sm font-bold text-gray-300 leading-tight">{{ $question->debate->name ?? 'N/A' }}
                        </h4>
                        @if ($hasAnswer)
                            <div class="mt-2 text-xs font-bold text-emerald-400">
                                {{ $firstAnswer->grado->name ?? 'Grado general' }} |
                                {{ $firstAnswer->seccion->name ?? 'Dió Rpta' }}
                            </div>
                        @endif
                    </div>

                    <!-- Pregunta -->
                    <div class="mb-6">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Interrogante
                            Evaluada</div>
                        <p class="text-sm font-medium text-white italic">"{!! $question->text ?? '...' !!}"</p>
                    </div>

                    @if ($hasAnswer)
                        <!-- Estadísticas Rápidas -->
                        <div
                            class="flex items-center justify-between bg-black/20 p-3 rounded-xl border border-white/5 mb-6">
                            <div class="text-center w-1/2 border-r border-white/5">
                                <div class="text-[9px] text-gray-500 font-black uppercase tracking-widest">Puntaje
                                    Recibido</div>
                                <div class="text-lg font-black {{ $isVoided ? 'text-red-400' : 'text-emerald-400' }}">
                                    {{ $firstAnswer->score ?? '0' }} <span class="text-[9px] opacity-50">PTS</span>
                                </div>
                            </div>
                            <div class="text-center w-1/2 pl-3 line-clamp-1">
                                <div class="text-[9px] text-gray-500 font-black uppercase tracking-widest">Opción
                                    Marcada</div>
                                <div class="text-xs font-black text-blue-400 mt-1"
                                    title="{{ $firstAnswer->option_text ?? 'Respuesta Correcta' }}">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($firstAnswer->option_text ?? 'Aprobada'), 20) }}
                                </div>
                            </div>
                        </div>

                        <!-- Acción -->
                        <div class="flex justify-end mt-auto">
                            @if ($isVoided)
                                <button type="button" wire:click.prevent="toggleNullifyStatus({{ $question->id }})"
                                    wire:confirm="¿Desea restaurar/desanular esta respuesta? Los puntos serán restablecidos a su valor original de ponderación."
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500/10 text-emerald-500 border border-emerald-500/30 hover:bg-emerald-500 hover:text-white rounded-xl transition-all duration-300 font-bold text-xs uppercase tracking-widest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Desanular
                                </button>
                            @else
                                <button type="button" wire:click.prevent="toggleNullifyStatus({{ $question->id }})"
                                    wire:confirm="¿Desea anular esta respuesta? Los puntos asignados pasarán directamente a 0 y la pregunta irá a revisión."
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-500/10 text-red-500 border border-red-500/30 hover:bg-red-500 hover:text-white rounded-xl transition-all duration-300 font-bold text-xs uppercase tracking-widest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                        </path>
                                    </svg>
                                    Anular
                                </button>
                            @endif
                        </div>
                    @else
                        <div
                            class="flex items-center justify-center mt-auto p-3 border border-dashed border-gray-700 rounded-xl">
                            <span class="text-xs text-gray-500 font-medium">A la espera de los participantes...</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-12 pt-8 border-t border-white/5">
            {{ $questions->links() }}
        </div>
    @endif
</div>
