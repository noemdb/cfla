<div class="min-h-screen bg-gray-900 text-white p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-center mb-2">Diagnóstico Completado</h1>
            <p class="text-gray-400 text-center">{{ $selectedPensum->asignatura->full_name ?? 'Área Académica' }}</p>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2">
                    {{ $sessionStats['total_answers'] ?? 0 }}
                </div>
                <div class="text-gray-400">Preguntas Respondidas</div>
            </div>

            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2">
                    {{ number_format($sessionStats['average_progress'] ?? 0, 1) }}%
                </div>
                <div class="text-gray-400">Progreso Promedio</div>
            </div>

            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-purple-400 mb-2">
                    {{ $sessionStats['completed_sessions'] ?? 0 }}
                </div>
                <div class="text-gray-400">Sesiones Completadas</div>
            </div>
        </div>

        <!-- Completion Message -->
        <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-6 mb-8">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-green-400">¡Felicitaciones!</h2>
            </div>
            <p class="text-gray-300 mb-4">
                Has completado exitosamente el diagnóstico para
                <strong>{{ $selectedPensum->asignatura->full_name ?? 'esta área' }}</strong>.
                Tus respuestas han sido registradas y serán utilizadas para mejorar las estrategias de aprendizaje.
            </p>
            <div class="flex flex-wrap gap-4">
                <button wire:click="openAnsweredQuestionsModal"
                    class="text-gray-400 hover:text-white underline transition-colors duration-200">
                    Ver Respuestas Detalladas
                </button>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button wire:click="backToDashboard"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition-colors duration-200">
                Volver al Dashboard
            </button>

            <button wire:click="restartIdentification"
                class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg font-medium transition-colors duration-200">
                Nuevo Diagnóstico
            </button>
        </div>
    </div>

    <!-- Modal for Detailed Answers -->
    @if ($showAnsweredModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-700">
                    <h3 class="text-xl font-semibold">Respuestas Detalladas</h3>
                    <button wire:click="closeAnsweredQuestionsModal"
                        class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @php
                        $answeredQuestions = $this->getAnsweredQuestionsWithAnswers();
                    @endphp

                    @if ($answeredQuestions->count() > 0)
                        <div class="space-y-6">
                            @foreach ($answeredQuestions as $index => $item)
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <!-- Question Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                                    Pregunta {{ $index + 1 }}
                                                </span>
                                                @if ($item['question']->difficulty)
                                                    @php
                                                        $difficultyClass = match ($item['question']->difficulty) {
                                                            'easy' => 'bg-green-600 text-white',
                                                            'medium' => 'bg-yellow-600 text-white',
                                                            'hard' => 'bg-red-600 text-white',
                                                            default => 'bg-gray-600 text-white',
                                                        };
                                                    @endphp
                                                    <span class="text-xs px-2 py-1 rounded {{ $difficultyClass }}">
                                                        {{ ucfirst($item['question']->difficulty) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="font-medium text-white mb-2">{{ $item['question']->pregunta }}
                                            </h4>
                                        </div>
                                        <span class="text-xs text-gray-400 ml-4">
                                            {{ \Carbon\Carbon::parse($item['completed_at'])->format('d/m/Y H:i') }}
                                        </span>
                                    </div>

                                    <!-- Answer Section -->
                                    <div class="bg-gray-600 rounded p-3">
                                        <div class="text-sm text-gray-300 mb-1">Tu respuesta:</div>

                                        @if ($item['question']->tipo_pregunta === 'multiple')
                                            <div class="text-white font-medium">{{ $item['answer'] }}</div>

                                            @if ($item['question']->options->count() > 0)
                                                <div class="mt-3">
                                                    <div class="text-xs text-gray-400 mb-2">Opciones disponibles:</div>
                                                    <div class="space-y-1">
                                                        @foreach ($item['question']->options as $option)
                                                            <div
                                                                class="flex items-center text-sm
                                                            {{ $option->opcion === $item['answer'] ? 'text-green-400 font-medium' : 'text-gray-400' }}">
                                                                <span
                                                                    class="w-2 h-2 rounded-full mr-2
                                                                {{ $option->opcion === $item['answer'] ? 'bg-green-400' : 'bg-gray-500' }}">
                                                                </span>
                                                                {{ $option->opcion }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @elseif($item['question']->tipo_pregunta === 'scale')
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-white font-medium text-lg">{{ $item['answer'] }}</span>
                                                <span class="text-gray-400">/ 10</span>
                                                <div class="flex-1 bg-gray-700 rounded-full h-2 ml-3">
                                                    <div class="bg-blue-500 h-2 rounded-full"
                                                        style="width: {{ ($item['answer'] / 10) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        @elseif($item['question']->tipo_pregunta === 'open')
                                            <div class="text-white bg-gray-700 rounded p-2 italic">
                                                "{{ $item['answer'] }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-gray-400">No se encontraron respuestas para mostrar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
