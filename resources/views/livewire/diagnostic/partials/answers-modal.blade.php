@if ($showAnsweredModal && $selectedPensum)
    @php
        $answeredQuestions = $this->getAnsweredQuestionsWithAnswers();
        $totalAnswered = $answeredQuestions->count();
    @endphp

    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4" x-data
        @keydown.escape.window="$wire.closeAnsweredQuestionsModal()" role="dialog" aria-modal="true"
        aria-label="Respuestas del área">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeAnsweredQuestionsModal"></div>

        <!-- Panel -->
        <div
            class="relative w-full max-w-6xl max-h-[92vh] sm:max-h-[88vh] flex flex-col bg-gray-900 border border-white/10 rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div
                class="flex items-start justify-between gap-4 px-5 py-4 border-b border-white/10 bg-gray-900/95 backdrop-blur">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5">
                        <span
                            class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-500/15 border border-emerald-500/20">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-white truncate">
                                {{ $selectedPensum->asignatura->full_name ?? 'Área' }}
                            </h2>
                            <p class="text-xs text-gray-400">
                                {{ $totalAnswered }}
                                {{ $totalAnswered === 1 ? 'pregunta respondida' : 'preguntas respondidas' }}
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" wire:click="closeAnsweredQuestionsModal" aria-label="Cerrar"
                    class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-5 space-y-4">
                @forelse($answeredQuestions as $index => $item)
                    @php
                        $question = $item['question'];
                        $difficultyClasses = match ($question->difficulty) {
                            'easy' => 'bg-green-500/15 text-green-300 border-green-500/30',
                            'medium' => 'bg-yellow-500/15 text-yellow-300 border-yellow-500/30',
                            'hard' => 'bg-red-500/15 text-red-300 border-red-500/30',
                            default => 'bg-gray-500/15 text-gray-300 border-gray-500/30',
                        };
                        $difficultyLabel = match ($question->difficulty) {
                            'easy' => 'Fácil',
                            'medium' => 'Medio',
                            'hard' => 'Difícil',
                            default => ucfirst((string) $question->difficulty),
                        };
                        $typeLabel = match ($question->tipo_pregunta) {
                            'multiple' => 'Selección',
                            'scale' => 'Escala',
                            'open' => 'Abierta',
                            default => ucfirst((string) $question->tipo_pregunta),
                        };
                    @endphp

                    <article wire:key="answered-{{ $question->id }}"
                        class="rounded-xl border border-white/5 bg-gray-800/60 overflow-hidden">
                        <!-- Question header -->
                        <div class="flex items-start gap-3 p-4 border-b border-white/5">
                            <span
                                class="shrink-0 w-7 h-7 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium leading-relaxed">{{ $question->pregunta }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    @if ($question->difficulty)
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $difficultyClasses }}">
                                            {{ $difficultyLabel }}
                                        </span>
                                    @endif
                                    <span
                                        class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white/5 text-gray-400 border border-white/10">
                                        {{ $typeLabel }}
                                    </span>
                                    @if ($item['completed_at'])
                                        <span class="text-[11px] text-gray-500">
                                            {{ \Carbon\Carbon::parse($item['completed_at'])->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Answer -->
                        <div class="p-4 bg-gray-900/40">
                            @if ($question->tipo_pregunta === 'multiple')
                                <div class="space-y-1.5">
                                    @foreach ($question->options as $option)
                                        @php $isSelected = $option->opcion === $item['answer']; @endphp
                                        <div
                                            class="flex items-center gap-3 px-3 py-2 rounded-lg border text-sm
                                            {{ $isSelected
                                                ? 'bg-emerald-500/15 border-emerald-500/40 text-white'
                                                : 'bg-white/5 border-white/5 text-gray-400' }}">
                                            <span
                                                class="shrink-0 w-4 h-4 rounded-full border-2 flex items-center justify-center
                                                {{ $isSelected ? 'border-emerald-400 bg-emerald-500' : 'border-gray-500' }}">
                                                @if ($isSelected)
                                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                            <span>{{ $option->opcion }}</span>
                                            @if ($isSelected)
                                                <span
                                                    class="ml-auto text-[10px] font-bold uppercase tracking-wide text-emerald-300">
                                                    Tu respuesta
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->tipo_pregunta === 'scale')
                                @php
                                    $value = (int) $item['answer'];
                                    $pct = max(0, min(100, ($value / 10) * 100));
                                @endphp
                                <div class="flex items-center gap-4">
                                    <div class="text-2xl font-bold text-emerald-400 tabular-nums">
                                        {{ $value }}<span class="text-sm text-gray-500">/10</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-2 rounded-full bg-gray-700 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400"
                                                style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <blockquote
                                    class="text-gray-200 bg-white/5 border-l-4 border-emerald-500/60 rounded-r-lg px-4 py-3 italic">
                                    {{ $item['answer'] }}
                                </blockquote>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="text-center py-16">
                        <svg class="w-14 h-14 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="text-gray-400">No hay respuestas guardadas para esta área.</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer -->
            <div class="px-5 py-3 border-t border-white/10 bg-gray-900/95 flex justify-end">
                <button type="button" wire:click="closeAnsweredQuestionsModal"
                    class="px-5 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
@endif
